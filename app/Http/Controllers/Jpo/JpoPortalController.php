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

class JpoPortalController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Metrics
        $pendingJobseekers = JobApplication::where('referred_by_jpo', 0)
            ->whereIn('status', ['pending', 'reviewed'])
            ->count();

        $pendingAccreditations = DB::table('employer_accreditation')
            ->where('status', 'submitted_to_jpo')
            ->count();

        $pendingPlacementReports = DB::table('placement_reports')
            ->where('status', 'submitted_to_jpo')
            ->count();

        $totalReferredJobseekers = JobApplication::where('referred_by_jpo', 1)->count();
        $totalHiredReferred = JobApplication::where('referred_by_jpo', 1)->where('status', 'hired')->count();

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

        // Monthly referral trends (last 6 months)
        $monthlyReferralTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            $monthLabel = $monthStart->format('M Y');
            
            $referredMonthCount = JobApplication::where('referred_by_jpo', 1)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            $hiredMonthCount = JobApplication::where('referred_by_jpo', 1)
                ->where('status', 'hired')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            $monthlyReferralTrends[] = [
                'month' => $monthLabel,
                'referred' => $referredMonthCount,
                'hired' => $hiredMonthCount,
            ];
        }

        $conversionRate = $totalReferredJobseekers > 0 
            ? round(($totalHiredReferred / $totalReferredJobseekers) * 100, 1) 
            : 0;

        return view('jpo.dashboard', compact(
            'user', 'pendingJobseekers', 'pendingAccreditations', 
            'pendingPlacementReports', 'totalReferredJobseekers', 
            'totalHiredReferred', 'conversionRate', 'monthlyReferralTrends',
            'recentApplicants', 'recentAccreditations'
        ));
    }

    // =========================================================================
    // 1. EVALUATE JOBSEEKER (REFER TO EMPLOYER)
    // =========================================================================

    public function evaluateJobseekers(Request $request)
    {
        $query = JobApplication::with(['jobseeker.skills', 'jobseeker.details', 'jobseeker.socialStatus', 'jobPosting.employer'])
            ->latest();

        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'pending') {
                $query->where('referred_by_jpo', 0);
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

    public function referJobseeker(Request $request, $id)
    {
        $application = JobApplication::with(['jobseeker', 'jobPosting.employer.user'])->findOrFail($id);

        $request->validate([
            'recommendation' => 'required|in:refer,training,reject',
            'remarks' => 'nullable|string',
        ]);

        $recommendation = $request->input('recommendation');
        $remarks = $request->input('remarks', 'Evaluated and officially endorsed by DMDP Job Placement Officer (JPO).');

        if ($recommendation === 'refer') {
            $application->update([
                'referred_by_jpo' => 1,
                'status' => 'reviewed',
                'jpo_notes' => $remarks,
                'jpo_evaluated_at' => now(),
            ]);

            // Create or update jpo_assessments
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

            // Notify Employer that a qualified jobseeker was referred
            $employerUser = $application->jobPosting->employer->user ?? null;
            if ($employerUser) {
                Notification::create([
                    'user_id' => $employerUser->user_id,
                    'title' => 'Qualified Jobseeker Referred by JPO',
                    'message' => "Job Placement Officer referred applicant '{$application->jobseeker->first_name} {$application->jobseeker->last_name}' for position '{$application->jobPosting->title}'.",
                    'type' => 'manual_review',
                    'is_read' => false,
                    'related_id' => $application->application_id,
                ]);
            }

            // Notify Jobseeker
            $jobseekerUser = $application->jobseeker->user ?? null;
            if ($jobseekerUser) {
                Notification::create([
                    'user_id' => $jobseekerUser->user_id,
                    'title' => 'Application Endorsed by JPO!',
                    'message' => "Great news! DMDP Job Placement Officer has reviewed and officially endorsed your application for '{$application->jobPosting->title}' to the employer.",
                    'type' => 'approval',
                    'is_read' => false,
                    'related_id' => $application->application_id,
                ]);
            }

            return redirect()->back()->with('success', "Applicant '{$application->jobseeker->first_name} {$application->jobseeker->last_name}' successfully evaluated and referred to Employer.");
        } elseif ($recommendation === 'training') {
            $application->update([
                'jpo_notes' => 'Recommended for upskilling/training modules: ' . $remarks,
                'jpo_evaluated_at' => now(),
            ]);

            return redirect()->back()->with('info', 'Applicant recommended for training modules.');
        } else {
            $rejectionReason = $request->filled('remarks') 
                ? $request->input('remarks') 
                : 'Does not meet the minimum qualification requirements specified for this position based on JPO assessment.';

            $application->update([
                'status' => 'rejected',
                'jpo_notes' => $rejectionReason,
                'jpo_evaluated_at' => now(),
            ]);

            // Notify Jobseeker
            $jobseekerUser = $application->jobseeker->user ?? null;
            if ($jobseekerUser) {
                Notification::create([
                    'user_id' => $jobseekerUser->user_id,
                    'title' => 'Application Status: Not Qualified',
                    'message' => "Your application for '{$application->jobPosting->title}' was evaluated by JPO and marked as Not Qualified. Reason: {$rejectionReason}",
                    'type' => 'manual_review',
                    'is_read' => false,
                    'related_id' => $application->application_id,
                ]);
            }

            return redirect()->back()->with('info', 'Application marked as not qualified for referral.');
        }
    }

    public function showNsrpForm($id)
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

    public function updateDocumentStatus(Request $request, $id)
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

        $message = match($docStatus) {
            'complete' => "Documents verified and marked as Complete. You may now recommend this employer to Admin.",
            'incomplete' => "Documents marked as Incomplete with reason recorded. Employer has been notified to rectify.",
            default => "Document status set to Pending Review."
        };

        return redirect()->back()->with('success', $message);
    }

    public function recommendAccreditation(Request $request, $id)
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

    public function printAccreditation($id)
    {
        $accreditation = EmployerAccreditation::with(['employer.user', 'employer.jobPostings'])->findOrFail($id);
        $employer = $accreditation->employer;
        $jobPostings = $employer->jobPostings ?? collect();

        return view('reports.establishment-registration-printable', compact('accreditation', 'employer', 'jobPostings'));
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

    public function forwardPlacementReport(Request $request, $id)
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

    public function showPlacementReport($id)
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
            'user', 'profile', 'totalReferrals', 'totalAccreditationsReviewed', 'totalPlacementReportsAudited'
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

    public function markNotificationRead($id)
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
}
