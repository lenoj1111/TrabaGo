<?php

namespace App\Http\Controllers\Jpo;

use App\Http\Controllers\Controller;
use App\Models\EmployerAccreditation;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Jobseeker;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class JpoPortalController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Metrics (Aggregated single query)
        $appStats = JobApplication::selectRaw("
            SUM(CASE WHEN referred_by_jpo = 0 AND status IN ('pending', 'reviewed') THEN 1 ELSE 0 END) as pending_jobseekers,
            SUM(CASE WHEN referred_by_jpo = 1 THEN 1 ELSE 0 END) as total_referred,
            SUM(CASE WHEN referred_by_jpo = 1 AND status = 'hired' THEN 1 ELSE 0 END) as total_hired
        ")->first();

        $pendingJobseekers = (int) ($appStats->pending_jobseekers ?? 0);
        $totalReferredJobseekers = (int) ($appStats->total_referred ?? 0);
        $totalHiredReferred = (int) ($appStats->total_hired ?? 0);

        $pendingAccreditations = DB::table('employer_accreditation')
            ->where('status', 'submitted_to_jpo')
            ->count();

        $pendingPlacementReports = DB::table('placement_reports')
            ->where('status', 'submitted_to_jpo')
            ->count();

        // Recent applications awaiting JPO evaluation
        $recentApplicants = JobApplication::with(['jobseeker.skills', 'jobPosting.employer'])
            ->where('referred_by_jpo', 0)
            ->whereIn('status', ['pending', 'reviewed'])
            ->latest()
            ->take(5)
            ->get();

        // Recent accreditation papers awaiting JPO review
        $recentAccreditations = DB::table('employer_accreditation')
            ->join('employers', 'employer_accreditation.employer_id', '=', 'employers.employer_id')
            ->select('employer_accreditation.*', 'employers.company_name')
            ->where('employer_accreditation.status', 'submitted_to_jpo')
            ->latest('submitted_at')
            ->take(5)
            ->get();

        // Monthly referral trends (single grouped query instead of 12 sequential queries)
        $sixMonthsAgo = now()->subMonths(5)->startOfMonth();
        $trendRows = JobApplication::where('referred_by_jpo', 1)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->selectRaw("
                CONVERT(varchar(7), created_at, 120) as yrmo,
                COUNT(*) as referred_count,
                SUM(CASE WHEN status = 'hired' THEN 1 ELSE 0 END) as hired_count
            ")
            ->groupBy(DB::raw("CONVERT(varchar(7), created_at, 120)"))
            ->get()
            ->keyBy('yrmo');

        $monthlyReferralTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);
            $yrmo = $monthDate->format('Y-m');
            $label = $monthDate->format('M Y');
            $row = $trendRows->get($yrmo);

            $monthlyReferralTrends[] = [
                'month' => $label,
                'referred' => (int) ($row->referred_count ?? 0),
                'hired' => (int) ($row->hired_count ?? 0),
            ];
        }

        $conversionRate = $totalReferredJobseekers > 0
            ? round(($totalHiredReferred / $totalReferredJobseekers) * 100, 1)
            : 0;

        return view('jpo.dashboard', compact(
            'user',
            'pendingJobseekers',
            'pendingAccreditations',
            'pendingPlacementReports',
            'totalReferredJobseekers',
            'totalHiredReferred',
            'conversionRate',
            'monthlyReferralTrends',
            'recentApplicants',
            'recentAccreditations'
        ));
    }

    // =========================================================================
    // 1. EVALUATE JOBSEEKER (REFER TO EMPLOYER)
    // =========================================================================

    public function evaluateJobseekers(Request $request)
    {
        $query = JobApplication::with(['jobseeker.skills', 'jobseeker.details', 'jobseeker.socialStatus', 'jobPosting.employer'])
            ->latest();

        // Default: fetch everything for the JPO
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'pending') {
                $query->whereIn('status', ['jpo_pending', 'pending', 'reviewed']);
            } elseif ($request->status === 'referred') {
                $query->where('referred_by_jpo', 1);
            }
        }

        if ($request->has('search') && $request->search !== '') {
            $s = $request->search;
            $query->whereHas('jobseeker', function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                    ->orWhere('last_name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $applications = $query->paginate(15)->withQueryString();

        return view('jpo.evaluations.jobseekers', compact('applications'));
    }

    public function referJobseeker(Request $request, int $id)
    {
        $application = JobApplication::with(['jobseeker.user', 'jobPosting.employer.user'])->findOrFail($id);

        $request->validate([
            'recommendation' => 'required|in:refer,training,reject',
            'remarks' => 'nullable|string',
        ]);

        $recommendation = $request->input('recommendation');
        $remarks = $request->input('remarks', 'Evaluated by DMDP Job Placement Officer (JPO).');
        $jobseekerUser = $application->jobseeker->user ?? null;
        $jobTitle = $application->jobPosting->title ?? 'the position';

        if ($recommendation === 'refer') {
            $application->update([
                'referred_by_jpo' => 1,
                'status' => 'pending',
                'jpo_notes' => $remarks,
                'jpo_evaluated_at' => now(),
            ]);

            $jpoProfile = DB::table('user_profiles')->where('user_id', Auth::id())->first();
            $jpoProfileId = $jpoProfile ? $jpoProfile->profile_id : 1;

            DB::table('jpo_assessments')->updateOrInsert(
                ['application_id' => $application->application_id],
                [
                    'jpo_id' => $jpoProfileId,
                    'recommendation' => 'refer',
                    'remarks' => $remarks,
                    'referral_date' => now()->toDateString(),
                    'referral_notes' => $remarks,
                ]
            );

            if ($employerUser = $application->jobPosting->employer->user ?? null) {
                Notification::create([
                    'user_id' => $employerUser->user_id,
                    'title' => 'Qualified Jobseeker Referred by JPO',
                    'message' => "Job Placement Officer referred applicant '{$application->jobseeker->first_name} {$application->jobseeker->last_name}' for position '{$jobTitle}'.",
                    'type' => 'manual_review',
                    'is_read' => false,
                    'related_id' => $application->application_id,
                ]);
            }

            if ($jobseekerUser) {
                Notification::create([
                    'user_id' => $jobseekerUser->user_id,
                    'title' => 'Application Endorsed by JPO!',
                    'message' => "Great news! DMDP JPO reviewed your application for '{$jobTitle}' and officially endorsed you to the employer.",
                    'type' => 'approval',
                    'is_read' => false,
                    'related_id' => $application->application_id,
                ]);
            }

            return redirect()->back()->with('success', "Applicant successfully referred to Employer.");
        }

        if ($recommendation === 'training') {
            $application->update([
                'status' => 'training_recommended',
                'referred_by_jpo' => 0,
                'jpo_notes' => $remarks,
                'jpo_evaluated_at' => now(),
            ]);

            if ($jobseekerUser) {
                Notification::create([
                    'user_id' => $jobseekerUser->user_id,
                    'title' => 'Training Recommended by JPO',
                    'message' => "The JPO has reviewed your application for '{$jobTitle}' and recommended you for upskilling/training. Remarks: {$remarks}",
                    'type' => 'training',
                    'is_read' => false,
                    'related_id' => $application->application_id,
                ]);
            }

            return redirect()->back()->with('info', 'Applicant recommended for training. Notification sent to jobseeker.');
        }

        // Reject
        $application->update([
            'status' => 'rejected',
            'referred_by_jpo' => 0,
            'jpo_notes' => $remarks,
            'jpo_evaluated_at' => now(),
        ]);

        if ($jobseekerUser) {
            Notification::create([
                'user_id' => $jobseekerUser->user_id,
                'title' => 'Application Status: Not Qualified',
                'message' => "Your application for '{$jobTitle}' was evaluated by JPO and marked as Not Qualified. Reason: {$remarks}",
                'type' => 'rejection',
                'is_read' => false,
                'related_id' => $application->application_id,
            ]);
        }

        return redirect()->back()->with('info', 'Application marked as Not Qualified.');
    }

    public function showNsrpForm(int $id)
    {
        $application = JobApplication::with([
            'jobseeker.skills',
            'jobseeker.details',
            'jobseeker.socialStatus',
            'jobPosting.employer'
        ])->findOrFail($id);

        $jobseeker = $application->jobseeker;
        $details = $jobseeker->details;
        $socialStatus = $jobseeker->socialStatus;
        $skills = $jobseeker->skills;
        $job = $application->jobPosting;
        $employer = $job->employer ?? null;

        return view('reports.nsrp-printable', compact('application', 'jobseeker', 'details', 'socialStatus', 'skills', 'job', 'employer'));
    }

    public function viewJobseekerDocument(int $id, int $documentIndex)
    {
        $application = JobApplication::with('jobseeker.details')->findOrFail($id);
        $jobseeker = $application->jobseeker;
        abort_unless($jobseeker && $jobseeker->details, 404);

        $documents = $jobseeker->details->training_certificates;
        if (!is_array($documents)) {
            $documents = json_decode($documents ?? '[]', true) ?: [];
        }
        abort_unless(isset($documents[$documentIndex]) && is_array($documents[$documentIndex]), 404);

        $document = $documents[$documentIndex];
        if (($document['category'] ?? null) === 'certificate' && !empty($document['enrollment_id'])) {
            $enrollment = DB::table('training_enrollments')
                ->join('jobseekers', 'training_enrollments.jobseeker_id', '=', 'jobseekers.jobseeker_id')
                ->join('training_programs', 'training_enrollments.training_id', '=', 'training_programs.training_id')
                ->where('training_enrollments.enrollment_id', $document['enrollment_id'])
                ->where('training_enrollments.jobseeker_id', $jobseeker->jobseeker_id)
                ->where('training_enrollments.certificate_issued', 1)
                ->select(
                    'training_enrollments.*',
                    'jobseekers.first_name',
                    'jobseekers.last_name',
                    'training_programs.title as course_title'
                )
                ->first();

            abort_unless($enrollment, 404, 'Certificate not found or not yet issued.');

            return view('trainer.certificates.preview', [
                'enrollment' => $enrollment,
                'backUrl' => route('jpo.evaluations.jobseekers'),
            ]);
        }

        $disk = Storage::disk('public');
        foreach (['file_path', 'path', 'file_url', 'url'] as $pathKey) {
            $candidate = $document[$pathKey] ?? null;
            if (!is_string($candidate) || trim($candidate) === '') {
                continue;
            }

            $urlPath = parse_url($candidate, PHP_URL_PATH);
            $path = is_string($urlPath) && $urlPath !== '' ? $urlPath : $candidate;
            $path = rawurldecode(str_replace('\\', '/', $path));
            if (($storagePosition = strrpos($path, '/storage/')) !== false) {
                $path = substr($path, $storagePosition + strlen('/storage/'));
            }
            $path = ltrim($path, '/');
            foreach (['storage/app/public/', 'app/public/', 'storage/', 'public/'] as $prefix) {
                if (str_starts_with($path, $prefix)) {
                    $path = substr($path, strlen($prefix));
                    break;
                }
            }

            $segments = explode('/', $path);
            $isApplicantDocument = str_starts_with($path, 'documents/');
            $isResume = str_starts_with($path, 'resumes/');
            $isLegacyId = str_starts_with($path, 'ids/') || str_starts_with($path, 'valid_ids/');
            if (
                ($isApplicantDocument || $isResume || $isLegacyId)
                && !in_array('..', $segments, true)
                && !in_array('.', $segments, true)
                && $disk->exists($path)
            ) {
                return $disk->response($path, $document['name'] ?? basename($path), [], 'inline');
            }
        }

        abort(404, 'Document file not found on storage.');
    }

    // =========================================================================
    // 2. EVALUATE ACCREDITATION PAPERS (RECOMMEND TO ADMIN)
    // =========================================================================

    public function evaluateAccreditations(Request $request)
    {
        $query = DB::table('employer_accreditation')
            ->join('employers', 'employer_accreditation.employer_id', '=', 'employers.employer_id')
            ->select('employer_accreditation.*', 'employers.company_name')
            ->latest('submitted_at');

        if ($request->filled('status')) {
            $query->where('employer_accreditation.status', $request->status);
        }

        if ($request->filled('doc_status')) {
            $query->where('employer_accreditation.document_status', $request->doc_status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('employers.company_name', 'like', "%{$s}%");
        }

        $accreditations = $query->paginate(15)->withQueryString();

        return view('jpo.evaluations.accreditations', compact('accreditations'));
    }

    public function updateDocumentStatus(Request $request, int $id)
    {
        $accreditation = DB::table('employer_accreditation')->where('accreditation_id', $id)->first();
        if (!$accreditation) {
            return redirect()->back()->withErrors(['error' => 'Accreditation record not found.']);
        }

        $request->validate([
            'document_status' => 'required|in:pending,complete,incomplete',
            'document_incomplete_reason' => 'nullable|required_if:document_status,incomplete|string|max:1000',
        ], [
            'document_incomplete_reason.required_if' => 'Please provide a clear reason why the employer documents are incomplete.',
        ]);

        $docStatus = $request->input('document_status');
        $incompleteReason = $docStatus === 'incomplete' ? $request->input('document_incomplete_reason') : null;

        DB::table('employer_accreditation')->where('accreditation_id', $id)->update([
            'document_status' => $docStatus,
            'document_incomplete_reason' => $incompleteReason,
            'document_verified_at' => now(),
            'document_verified_by' => Auth::id(),
        ]);

        // Notify employer regarding document evaluation
        $employer = DB::table('employers')->where('employer_id', $accreditation->employer_id)->first();
        if ($employer) {
            $employerUser = DB::table('users')->where('user_id', $employer->user_id)->first();
            if ($employerUser) {
                if ($docStatus === 'incomplete') {
                    Notification::create([
                        'user_id' => $employerUser->user_id,
                        'title' => 'Accreditation Documents Incomplete',
                        'message' => "DMDP Job Placement Officer evaluated your legal accreditation papers and marked them as Incomplete. Reason: {$incompleteReason}. Please update and upload the required valid documents.",
                        'type' => 'manual_review',
                        'is_read' => false,
                        'related_id' => $id,
                    ]);
                } elseif ($docStatus === 'complete') {
                    Notification::create([
                        'user_id' => $employerUser->user_id,
                        'title' => 'Accreditation Documents Verified Complete',
                        'message' => "Great news! DMDP Job Placement Officer verified that your accreditation papers are complete and valid. Your accreditation is now eligible for official recommendation to the Administrator.",
                        'type' => 'manual_review',
                        'is_read' => false,
                        'related_id' => $id,
                    ]);
                }
            }
        }

        $message = match ($docStatus) {
            'complete' => "Documents verified and marked as Complete. You may now recommend this employer to Admin.",
            'incomplete' => "Documents marked as Incomplete with reason recorded. Employer has been notified to rectify.",
            default => "Document status set to Pending Review."
        };

        return redirect()->back()->with('success', $message);
    }

    public function recommendAccreditation(Request $request, int $id)
    {
        $accreditation = DB::table('employer_accreditation')->where('accreditation_id', $id)->first();
        if (!$accreditation) {
            return redirect()->back()->withErrors(['error' => 'Accreditation record not found.']);
        }

        // STRICT GUARD: JPO cannot recommend if documents are marked as incomplete
        if (($accreditation->document_status ?? 'pending') === 'incomplete') {
            $reason = !empty($accreditation->document_incomplete_reason)
                ? " (Reason: {$accreditation->document_incomplete_reason})"
                : "";
            return redirect()->back()
                ->withErrors(['error' => "Cannot recommend employer accreditation to Admin: Required legal documents are marked as Incomplete{$reason}. The employer must rectify documents first."])
                ->with('error', "Cannot recommend employer: Required legal documents are marked as Incomplete.");
        }

        $request->validate([
            'action' => 'required|in:recommend,reject',
            'remarks' => 'nullable|string',
        ]);

        $action = $request->input('action');
        $remarks = $request->input('remarks', 'Documents verified complete and recommended by Job Placement Officer.');

        if ($action === 'recommend') {
            DB::table('employer_accreditation')->where('accreditation_id', $id)->update([
                'status' => 'jpo_approved',
                'jpo_reviewed' => 1,
                'jpo_reviewed_at' => now(),
                'jpo_remarks' => $remarks,
                'document_status' => 'complete',
                'document_verified_at' => now(),
                'document_verified_by' => Auth::id(),
                'jpo_id' => Auth::id(),
            ]);

            // Notify Admins directly (Supervisor role removed, JPO recommends directly to Admin)
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $adm) {
                Notification::create([
                    'user_id' => $adm->user_id,
                    'title' => 'Employer Accreditation Recommended by JPO',
                    'message' => "JPO verified complete legal credentials and recommended employer ID #{$accreditation->employer_id} for official DMDP accreditation.",
                    'type' => 'accreditation',
                    'is_read' => false,
                    'related_id' => $id,
                ]);
            }

            return redirect()->back()->with('success', 'Employer legal documents verified complete and recommended to Admin for official accreditation.');
        } else {
            DB::table('employer_accreditation')->where('accreditation_id', $id)->update([
                'status' => 'rejected',
                'jpo_reviewed' => 1,
                'jpo_reviewed_at' => now(),
                'jpo_remarks' => $remarks,
                'jpo_id' => Auth::id(),
            ]);

            return redirect()->back()->with('info', 'Accreditation papers marked as rejected.');
        }
    }

    public function printAccreditation(int $id)
    {
        $accreditation = EmployerAccreditation::with(['employer.user', 'employer.jobPostings'])->findOrFail($id);
        $employer = $accreditation->employer;
        $jobPostings = $employer->jobPostings ?? collect();

        return view('reports.establishment-registration-printable', compact('accreditation', 'employer', 'jobPostings'));
    }

    public function accreditEmployer(Request $request, $id)
    {
        $accreditation = DB::table('employer_accreditation')->where('accreditation_id', $id)->first();
        if (!$accreditation) {
            $employer = DB::table('employers')->where('employer_id', $id)->first();
            if (!$employer) {
                return redirect()->back()->withErrors(['error' => 'Accreditation or Employer record not found.']);
            }
            $employerId = $employer->employer_id;
        } else {
            $employerId = $accreditation->employer_id;
        }

        $certNo = ($accreditation && !empty($accreditation->certificate_no))
            ? $accreditation->certificate_no
            : ('DMDP-EMP-' . date('Y') . '-' . str_pad($employerId, 5, '0', STR_PAD_LEFT));

        DB::table('employers')->where('employer_id', $employerId)->update([
            'is_accredited' => 1,
            'accredited_at' => now()->toDateString(),
        ]);

        if ($accreditation) {
            DB::table('employer_accreditation')->where('accreditation_id', $accreditation->accreditation_id)->update([
                'status' => 'admin_approved',
                'admin_approved' => 1,
                'jpo_reviewed' => 1,
                'jpo_reviewed_at' => now(),
                'document_status' => 'complete',
                'approved_at' => now()->toDateString(),
                'certificate_no' => $certNo,
                'certificate_issued_at' => now(),
                'certificate_issued_by' => Auth::id(),
                'admin_rejection_remarks' => null,
            ]);
        } else {
            DB::table('employer_accreditation')->insert([
                'employer_id' => $employerId,
                'status' => 'admin_approved',
                'admin_approved' => 1,
                'jpo_reviewed' => 1,
                'document_status' => 'complete',
                'approved_at' => now()->toDateString(),
                'certificate_no' => $certNo,
                'certificate_issued_at' => now(),
                'certificate_issued_by' => Auth::id(),
                'documents' => json_encode([]),
                'submitted_at' => now(),
            ]);
        }

        $employer = Employer::with('user')->find($employerId);
        if ($employer && $employer->user) {
            DB::table('users')->where('user_id', $employer->user->user_id)->update([
                'account_status' => 'verified',
                'is_approved' => 1,
                'updated_at' => now(),
            ]);

            Notification::create([
                'user_id' => $employer->user->user_id,
                'title' => 'Official Accreditation Granted & Certificate Issued!',
                'message' => "Congratulations! {$employer->company_name} is now officially accredited with DMDP Cebu City (Certificate No: {$certNo}).",
                'type' => 'approval',
                'is_read' => false,
                'related_id' => $employerId,
            ]);
        }

        return redirect()->back()->with('success', "Employer '{$employer->company_name}' successfully accredited. Certificate {$certNo} issued.");
    }

    public function disaccreditEmployer(Request $request, $id)
    {
        $accreditation = DB::table('employer_accreditation')->where('accreditation_id', $id)->first();
        $employerId = $accreditation ? $accreditation->employer_id : $id;
        $employer = Employer::with('user')->find($employerId);
        if (!$employer) {
            return redirect()->back()->withErrors(['error' => 'Employer record not found.']);
        }

        $employer->update(['is_accredited' => 0]);

        DB::table('employer_accreditation')->where('employer_id', $employerId)->update([
            'status' => 'disaccredited',
            'admin_approved' => 0,
        ]);

        if ($employer->user) {
            Notification::create([
                'user_id' => $employer->user->user_id,
                'title' => 'Employer Accreditation Revoked',
                'message' => 'Your official accreditation has been revoked by the Job Placement Office (DMDP). Please contact the DMDP office for further details.',
                'type' => 'rejection',
                'is_read' => false,
                'related_id' => $employer->employer_id,
            ]);
        }

        return redirect()->back()->with('info', "Employer '{$employer->company_name}' has been disaccredited.");
    }

    public function certificate($id)
    {
        $accreditation = DB::table('employer_accreditation')->where('accreditation_id', $id)->first();
        $employerId = $accreditation ? $accreditation->employer_id : $id;
        $employer = DB::table('employers')->where('employer_id', $employerId)->first();
        if (!$employer) abort(404, 'Employer not found');
        return view('reports.employer-certificate', compact('employer', 'accreditation'));
    }

    public function createUser()
    {
        $roles = ['employer', 'jobseeker'];
        return view('jpo.users.create', compact('roles'));
    }

    public function storeUser(Request $request)
    {
        $role = $request->input('role');
        if (!in_array($role, ['employer', 'jobseeker'])) {
            return redirect()->back()->withErrors(['role' => 'JPO can only provision Employer or Jobseeker accounts.'])->withInput();
        }

        $userController = new \App\Http\Controllers\Admin\UserManagementController();
        return $userController->store($request, true);
    }

    // =========================================================================
    // 3. EVALUATE PLACEMENT REPORT (SEND TO ADMIN)
    // =========================================================================

    public function evaluatePlacementReports(Request $request)
    {
        $query = DB::table('placement_reports')
            ->join('employers', 'placement_reports.employer_id', '=', 'employers.employer_id')
            ->select('placement_reports.*', 'employers.company_name')
            ->latest('report_month');

        if ($request->filled('status')) {
            $query->where('placement_reports.status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('employers.company_name', 'like', "%{$s}%");
        }

        if ($request->filled('month')) {
            $query->where('placement_reports.report_month', 'like', $request->month . '%');
        }

        $reports = $query->paginate(15)->withQueryString();

        return view('jpo.evaluations.placement-reports', compact('reports'));
    }

    public function forwardPlacementReport(Request $request, int $id)
    {
        $report = DB::table('placement_reports')->where('report_id', $id)->first();
        if (!$report) {
            return redirect()->back()->withErrors(['error' => 'Placement report not found.']);
        }

        $remarks = $request->input('remarks', 'Verified by Job Placement Officer (JPO).');

        DB::table('placement_reports')->where('report_id', $id)->update([
            'status' => 'jpo_evaluated',
            'jpo_evaluated' => 1,
            'jpo_evaluated_at' => now(),
            'jpo_remarks' => $remarks,
        ]);

        // Notify Admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->user_id,
                'title' => 'Placement Report Evaluated by JPO',
                'message' => "JPO evaluated monthly placement report for employer ID #{$report->employer_id}. Pending your final approval.",
                'type' => 'manual_review',
                'is_read' => false,
                'related_id' => $id,
            ]);
        }

        return redirect()->back()->with('success', 'Placement report verified and forwarded to Admin for final authorization.');
    }

    public function showPlacementReport(int $id)
    {
        $report = DB::table('placement_reports')
            ->join('employers', 'placement_reports.employer_id', '=', 'employers.employer_id')
            ->where('placement_reports.report_id', $id)
            ->select('placement_reports.*', 'employers.company_name')
            ->first();

        if (!$report) {
            abort(404, 'Placement report not found.');
        }

        return view('reports.placement-printable', compact('report'));
    }

    // =========================================================================
    // 4. PROFILE, UPDATE PROFILE & RESET PASSWORD
    // =========================================================================

    public function profile()
    {
        $user = Auth::user();
        $profile = DB::table('user_profiles')->where('user_id', $user->user_id)->first();

        // JPO metrics
        $totalReferrals = JobApplication::where('referred_by_jpo', 1)->count();
        $totalAccreditationsReviewed = DB::table('employer_accreditation')->where('jpo_reviewed', 1)->count();
        $totalPlacementReportsAudited = DB::table('placement_reports')->where('jpo_evaluated', 1)->count();

        return view('jpo.profile', compact(
            'user',
            'profile',
            'totalReferrals',
            'totalAccreditationsReviewed',
            'totalPlacementReportsAudited'
        ));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'full_name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:50',
            'office' => 'nullable|string|max:150',
            'position' => 'nullable|string|max:150',
            'bio' => 'nullable|string|max:1000',
        ]);

        DB::table('user_profiles')->updateOrInsert(
            ['user_id' => $user->user_id],
            [
                'full_name' => $request->input('full_name'),
                'phone' => $request->input('phone'),
                'office' => $request->input('office', 'Cebu City DMDP - Job Placement Division'),
                'position' => $request->input('position', 'Job Placement Officer (JPO)'),
                'bio' => $request->input('bio'),
                'updated_at' => now(),
            ]
        );

        return redirect()->route('jpo.profile')->with('success', 'Officer profile details updated successfully.');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The new password must be at least 8 characters in length.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The provided current password does not match your account password.']);
        }

        DB::table('users')->where('user_id', $user->user_id)->update([
            'password' => Hash::make($request->password),
            'updated_at' => now(),
        ]);

        // Security Notification
        Notification::create([
            'user_id' => $user->user_id,
            'title' => 'Password Reset Successfully',
            'message' => 'Your officer account password was recently updated. If you did not initiate this change, please contact the DMDP System Administrator immediately.',
            'type' => 'security',
            'is_read' => false,
        ]);

        return back()->with('success', 'Your password has been successfully reset! Please use your new password next time you sign in.');
    }

    // =========================================================================
    // 5. NOTIFICATION CENTER
    // =========================================================================

    public function notifications()
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->user_id)
            ->latest('created_at')
            ->paginate(15);

        $unreadCount = Notification::where('user_id', $user->user_id)
            ->where('is_read', false)
            ->count();

        return view('jpo.notifications', compact('notifications', 'user', 'unreadCount'));
    }

    public function markNotificationRead(int $id)
    {
        Notification::where('notification_id', $id)
            ->where('user_id', Auth::id())
            ->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    public function markAllNotificationsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    public function approveJobseeker(Request $request, $id)
    {
        $jobseeker = Jobseeker::with('user', 'details')->find($id);
        if (!$jobseeker) {
            $jobseeker = Jobseeker::with('user', 'details')->where('user_id', $id)->firstOrFail();
        }

        $user = $jobseeker->user;
        if ($user) {
            $user->update([
                'status' => 'active',
                'is_approved' => 1,
                'account_status' => 'verified',
            ]);
        }

        $details = $jobseeker->details;
        if ($details && !empty($details->training_certificates)) {
            $docs = is_array($details->training_certificates)
                ? $details->training_certificates
                : (json_decode($details->training_certificates, true) ?: []);

            foreach ($docs as &$doc) {
                if (in_array($doc['category'] ?? '', ['valid_id', 'resume', 'certificate', 'pwd_id'])) {
                    $doc['status'] = 'approved';
                    $doc['reviewed_by'] = Auth::id();
                    $doc['reviewed_at'] = now()->toIso8601String();
                }
            }
            $details->training_certificates = array_values($docs);
            $details->save();
        }

        if ($user) {
            Notification::create([
                'user_id' => $user->user_id,
                'title' => 'Jobseeker Identity Verified by JPO!',
                'message' => 'Your government ID and account have been verified by the Job Placement Officer (JPO). You may now apply for job openings.',
                'type' => 'approval',
                'is_read' => false,
                'related_id' => $jobseeker->jobseeker_id,
            ]);
        }

        return redirect()->back()->with('success', "Jobseeker '{$jobseeker->first_name} {$jobseeker->last_name}' verified and approved successfully.");
    }

    public function rejectJobseeker(Request $request, $id)
    {
        $jobseeker = Jobseeker::with('user', 'details')->find($id);
        if (!$jobseeker) {
            $jobseeker = Jobseeker::with('user', 'details')->where('user_id', $id)->firstOrFail();
        }

        $reason = $request->input('remarks') ?: 'Your submitted government ID does not meet PESO/DMDP criteria. Please upload a clear, legible ID in your Document Hub.';

        $user = $jobseeker->user;
        if ($user) {
            $user->update([
                'account_status' => 'unverified',
                'is_approved' => 0,
            ]);
        }

        $details = $jobseeker->details;
        if ($details && !empty($details->training_certificates)) {
            $docs = is_array($details->training_certificates)
                ? $details->training_certificates
                : (json_decode($details->training_certificates, true) ?: []);

            foreach ($docs as &$doc) {
                if (($doc['category'] ?? '') === 'valid_id') {
                    $doc['status'] = 'rejected';
                    $doc['rejection_reason'] = $reason;
                    $doc['reviewed_by'] = Auth::id();
                    $doc['reviewed_at'] = now()->toIso8601String();
                }
            }
            $details->training_certificates = array_values($docs);
            $details->save();
        }

        if ($user) {
            Notification::create([
                'user_id' => $user->user_id,
                'title' => 'Government ID Requires Revision',
                'message' => "Your government ID submission requires revision: {$reason}",
                'type' => 'rejection',
                'is_read' => false,
                'related_id' => $jobseeker->jobseeker_id,
            ]);
        }

        return redirect()->back()->with('info', "Jobseeker '{$jobseeker->first_name} {$jobseeker->last_name}' returned for ID revision.");
    }
}
