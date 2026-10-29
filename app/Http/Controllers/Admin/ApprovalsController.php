<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\EmployerAccreditation;
use App\Models\JobPosting;
use App\Models\Jobseeker;
use App\Models\JobseekerDetail;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ApprovalsController extends Controller
{
    public function accreditationDocument(int $accreditationId, string $documentKey, string $action)
    {
        abort_unless(in_array($action, ['preview', 'download'], true), 404);

        $accreditation = DB::table('employer_accreditation')
            ->where('accreditation_id', $accreditationId)
            ->first();
        abort_unless($accreditation, 404);

        if (Auth::user()?->role === 'employer') {
            $employer = Employer::where('user_id', Auth::id())->first();
            abort_unless($employer && (int) $accreditation->employer_id === (int) $employer->employer_id, 404);
        }

        $documents = json_decode($accreditation->documents ?? '[]', true);
        abort_unless(is_array($documents) && array_key_exists($documentKey, $documents), 404);

        $document = $documents[$documentKey];
        $disk = Storage::disk('public');
        $candidates = is_array($document)
            ? array_map(fn ($key) => $document[$key] ?? null, ['file_path', 'path', 'file_url', 'url'])
            : [$document];
        $path = null;

        foreach ($candidates as $candidate) {
            if (!is_string($candidate) || trim($candidate) === '') {
                continue;
            }

            $urlPath = parse_url($candidate, PHP_URL_PATH);
            $candidatePath = is_string($urlPath) && $urlPath !== '' ? $urlPath : $candidate;
            $candidatePath = rawurldecode(str_replace('\\', '/', $candidatePath));
            if (($storagePosition = strrpos($candidatePath, '/storage/')) !== false) {
                $candidatePath = substr($candidatePath, $storagePosition + strlen('/storage/'));
            }
            $candidatePath = ltrim($candidatePath, '/');
            foreach (['storage/app/public/', 'app/public/', 'storage/', 'public/'] as $prefix) {
                if (str_starts_with($candidatePath, $prefix)) {
                    $candidatePath = substr($candidatePath, strlen($prefix));
                    break;
                }
            }

            $segments = explode('/', $candidatePath);
            $isAccreditationDocument = str_starts_with($candidatePath, 'accreditation_docs/')
                || str_starts_with($candidatePath, 'employer_documents/');
            if (
                $isAccreditationDocument
                && !in_array('..', $segments, true)
                && !in_array('.', $segments, true)
                && $disk->exists($candidatePath)
            ) {
                $path = $candidatePath;
                break;
            }
        }
        abort_unless(is_string($path), 404);

        $filename = is_array($document) && !empty($document['original_name'])
            ? $document['original_name']
            : basename($path);

        return $disk->response(
            $path,
            $filename,
            [],
            $action === 'download' ? 'attachment' : 'inline'
        );
    }

    public function index()
    {
        // 1. Pending Job Postings with employer & accreditation details
        $pendingJobs = JobPosting::with(['employer.user', 'employer.accreditation'])
            ->withCount('applications')
            ->where('status', 'pending')
            ->latest()
            ->get();

        foreach ($pendingJobs as $job) {
            if (!$job->employer) {
                $job->employer_accreditation_badge = 'DMDP Official';
                $job->employer_accreditation_state = 'accredited';
            } elseif ($job->employer->is_accredited || ($job->employer->accreditation && $job->employer->accreditation->status === 'admin_approved')) {
                $job->employer_accreditation_badge = 'Accredited';
                $job->employer_accreditation_state = 'accredited';
            } elseif ($job->employer->accreditation && in_array($job->employer->accreditation->status, ['submitted_to_jpo', 'jpo_approved', 'supervisor_approved', 'manual_review', 'pending'])) {
                $job->employer_accreditation_badge = 'Pending';
                $job->employer_accreditation_state = 'pending';
            } else {
                $job->employer_accreditation_badge = 'Not Accredited';
                $job->employer_accreditation_state = 'not_accredited';
            }
        }

        // 2. Pending Employer Accreditations (recommended by JPO or direct)
        $pendingAccreditations = DB::table('employer_accreditation')
            ->join('employers', 'employer_accreditation.employer_id', '=', 'employers.employer_id')
            ->select('employer_accreditation.*', 'employers.company_name')
            ->whereIn('employer_accreditation.status', ['jpo_approved', 'supervisor_approved', 'submitted_to_jpo'])
            ->latest('submitted_at')
            ->get();

        // 3. Pending Placement Reports (evaluated by JPO)
        $pendingPlacementReports = DB::table('placement_reports')
            ->join('employers', 'placement_reports.employer_id', '=', 'employers.employer_id')
            ->select('placement_reports.*', 'employers.company_name')
            ->whereIn('placement_reports.status', ['jpo_evaluated', 'submitted_to_jpo', 'pending'])
            ->latest('report_month')
            ->get();

        // 4. Pending Collaborator Trainer Accounts
        $pendingTrainers = DB::table('users')
            ->join('user_profiles', 'users.user_id', '=', 'user_profiles.user_id')
            ->select(
                'users.*',
                'user_profiles.full_name',
                'user_profiles.position',
                'user_profiles.office',
                'user_profiles.specialization',
                'user_profiles.partner_institution'
            )
            ->where('users.role', 'trainer')
            ->where(function ($q) {
                $q->where('users.is_approved', 0)
                  ->orWhere('users.status', 'pending');
            })
            ->latest('users.created_at')
            ->get();

        // 5. Pending Jobseekers (Awaiting Account & Government ID Verification)
        $hasAccountStatusColumn = Schema::hasColumn('users', 'account_status');

        $pendingJobseekers = DB::table('jobseekers')
            ->join('users', 'jobseekers.user_id', '=', 'users.user_id')
            ->leftJoin('jobseeker_details', 'jobseekers.jobseeker_id', '=', 'jobseeker_details.jobseeker_id')
            ->leftJoin('social_status', 'jobseekers.jobseeker_id', '=', 'social_status.jobseeker_id')
            ->select(
                'jobseekers.*',
                'users.email as user_email',
                $hasAccountStatusColumn ? 'users.account_status' : 'users.status as account_status',
                'users.is_approved as user_is_approved',
                'users.status as user_status',
                'users.created_at as registered_at',
                'jobseeker_details.training_certificates',
                'social_status.is_pwd',
                'social_status.pwd_type'
            )
            ->where('users.role', 'jobseeker')
            ->where(function ($q) use ($hasAccountStatusColumn) {
                $q->where('users.is_approved', 0);

                if ($hasAccountStatusColumn) {
                    $q->orWhere('users.account_status', 'pending_review')
                      ->orWhere('users.account_status', 'unverified')
                      ->orWhereNull('users.account_status');
                } else {
                    $q->orWhere('users.status', 'pending')
                      ->orWhereNull('users.status');
                }
            })
            ->latest('users.created_at')
            ->get();

        foreach ($pendingJobseekers as $js) {
            $docs = [];
            if (!empty($js->training_certificates)) {
                $raw = is_array($js->training_certificates) 
                    ? $js->training_certificates 
                    : json_decode($js->training_certificates, true);
                if (is_array($raw)) {
                    foreach ($raw as $d) {
                        $cat = $d['category'] ?? '';
                        $docs[$cat] = $d;
                    }
                }
            }
            $js->parsed_docs = $docs;
            $js->has_valid_id = !empty($docs['valid_id']);
            $js->valid_id_doc = $docs['valid_id'] ?? null;
            $js->has_resume = !empty($docs['resume']);
            $js->resume_doc = $docs['resume'] ?? null;
        }

        return view('admin.approvals.index', compact(
            'pendingJobs', 
            'pendingAccreditations', 
            'pendingPlacementReports', 
            'pendingTrainers',
            'pendingJobseekers'
        ));
    }

    // =========================================================================
    // PILLAR 1: JOB POSTING APPROVAL
    // =========================================================================

    public function approveJobPosting(int $id)
    {
        $job = JobPosting::with('employer.user')->findOrFail($id);
        $job->update([
            'status' => 'approved',
            'approved_at' => now()->toDateString(),
        ]);

        // Notify employer
        if ($job->employer && $job->employer->user) {
            Notification::create([
                'user_id' => $job->employer->user->user_id,
                'title' => 'Job Posting Approved!',
                'message' => "Your job posting '{$job->title}' has been officially approved by the Admin and is now live for all jobseekers.",
                'type' => 'approval',
                'is_read' => false,
                'related_id' => $job->job_id,
            ]);
        }

        return redirect()->back()->with('success', "Job posting '{$job->title}' approved successfully.");
    }

    public function rejectJobPosting(Request $request, int $id)
    {
        $job = JobPosting::with('employer.user')->findOrFail($id);
        $job->update(['status' => 'rejected']);

        if ($job->employer && $job->employer->user) {
            Notification::create([
                'user_id' => $job->employer->user->user_id,
                'title' => 'Job Posting Rejected',
                'message' => "Your job posting '{$job->title}' was not approved. Remarks: " . $request->input('remarks', 'Please revise and resubmit.'),
                'type' => 'rejection',
                'is_read' => false,
                'related_id' => $job->job_id,
            ]);
        }

        return redirect()->back()->with('info', "Job posting '{$job->title}' rejected.");
    }

    // =========================================================================
    // PILLAR 2: EMPLOYER ACCREDITATION FINAL APPROVAL
    // =========================================================================

    public function approveAccreditation(Request $request, int $id)
    {
        $accreditation = DB::table('employer_accreditation')->where('accreditation_id', $id)->first();
        if (!$accreditation) {
            return redirect()->back()->withErrors(['error' => 'Accreditation record not found.']);
        }

        // Enforce: Admin cannot accredit an employer without recommending approval of JPO unless direct_override is requested
        if (!$request->boolean('direct_override')) {
            if (!$accreditation->jpo_reviewed || !in_array($accreditation->status, ['jpo_approved', 'supervisor_approved'])) {
                return redirect()->back()
                    ->withErrors([
                        'error' => 'Cannot accredit employer without the recommending approval of the JPO. JPO evaluation is required first.'
                    ])
                    ->with('error', 'Cannot accredit employer without the recommending approval of the JPO. JPO evaluation is required first.');
            }

            if (($accreditation->document_status ?? 'pending') !== 'complete') {
                $reason = !empty($accreditation->document_incomplete_reason) 
                    ? " (Reason: {$accreditation->document_incomplete_reason})" 
                    : "";
                return redirect()->back()
                    ->withErrors([
                        'error' => "Cannot accredit employer: Required legal documents are marked as " . ucfirst($accreditation->document_status ?? 'pending') . "{$reason}. Employer documents must be verified as Complete first."
                    ])
                    ->with('error', "Cannot accredit employer: Required legal documents are incomplete or unverified.");
            }
        }

        // Direct accreditation allowed with warning logging
        $isDirectOverride = (!$accreditation->jpo_reviewed || !in_array($accreditation->status, ['jpo_approved', 'supervisor_approved']));
        $certNo = $accreditation->certificate_no ?: ('DMDP-EMP-' . date('Y') . '-' . str_pad($accreditation->employer_id, 5, '0', STR_PAD_LEFT));

        // Update accreditation table & generate official Certificate
        DB::table('employer_accreditation')->where('accreditation_id', $id)->update([
            'status' => 'admin_approved',
            'admin_approved' => 1,
            'admin_approved_at' => now(),
            'approved_at' => now()->toDateString(),
            'certificate_no' => $certNo,
            'certificate_issued_at' => now(),
            'certificate_issued_by' => Auth::id(),
            'admin_rejection_remarks' => null,
        ]);

        // Update employer table to mark accredited
        DB::table('employers')->where('employer_id', $accreditation->employer_id)->update([
            'is_accredited' => 1,
            'accredited_at' => now()->toDateString(),
        ]);

        // Notify employer and verify account
        $employer = Employer::with('user')->find($accreditation->employer_id);
        if ($employer && $employer->user) {
            DB::table('users')->where('user_id', $employer->user->user_id)->update([
                'account_status' => 'verified',
                'is_approved' => 1,
                'updated_at' => now(),
            ]);

            Notification::create([
                'user_id' => $employer->user->user_id,
                'title' => 'Official Accreditation Granted & Certificate Issued!',
                'message' => "Congratulations! {$employer->company_name} is now officially accredited with DMDP Cebu City (Certificate No: {$certNo}). You can view your Certificate of Accreditation in your dashboard.",
                'type' => 'approval',
                'is_read' => false,
                'related_id' => $employer->employer_id,
            ]);
        }

        $msg = $isDirectOverride 
            ? "Employer '{$employer->company_name}' directly accredited by Administrator (Direct Override). Certificate {$certNo} generated."
            : "Employer '{$employer->company_name}' successfully accredited. Certificate {$certNo} issued.";

        return redirect()->back()->with('success', $msg);
    }

    public function rejectAccreditation(Request $request, int $id)
    {
        $request->validate([
            'remarks' => 'required|string|min:5',
        ], [
            'remarks.required' => 'Mandatory administrative remarks are required to explain why this accreditation application is rejected/returned to JPO.',
        ]);

        $accreditation = DB::table('employer_accreditation')->where('accreditation_id', $id)->first();
        if (!$accreditation) {
            return redirect()->back()->withErrors(['error' => 'Accreditation record not found.']);
        }

        $remarks = $request->input('remarks');

        // Loop back to JPO with mandatory administrative remarks
        DB::table('employer_accreditation')->where('accreditation_id', $id)->update([
            'status' => 'submitted_to_jpo',
            'admin_approved' => 0,
            'jpo_reviewed' => 0,
            'admin_rejection_remarks' => $remarks,
        ]);

        DB::table('employers')->where('employer_id', $accreditation->employer_id)->update([
            'is_accredited' => 0,
        ]);

        $employer = Employer::with('user')->find($accreditation->employer_id);
        
        // Notify JPO about the rejection loop with mandatory remarks
        $jpos = User::where('role', 'jpo')->get();
        foreach ($jpos as $jpo) {
            Notification::create([
                'user_id' => $jpo->user_id,
                'title' => 'Accreditation Returned by Admin for Re-evaluation',
                'message' => "Employer '{$employer->company_name}' was rejected by DMDP Admin and returned to JPO queue. Mandatory Remarks: \"{$remarks}\"",
                'type' => 'rejection',
                'is_read' => false,
                'related_id' => $id,
            ]);
        }

        // Notify employer
        if ($employer && $employer->user) {
            Notification::create([
                'user_id' => $employer->user->user_id,
                'title' => 'Accreditation Returned for Revision',
                'message' => "Your accreditation application was reviewed by DMDP Admin and returned to JPO for re-evaluation. Admin Remarks: {$remarks}",
                'type' => 'rejection',
                'is_read' => false,
                'related_id' => $employer->employer_id,
            ]);
        }

        return redirect()->back()->with('info', "Accreditation application for '{$employer->company_name}' has been rejected and returned to JPO queue with administrative remarks.");
    }

    public function disaccreditEmployer(Request $request, $id)
    {
        $employer = Employer::with('user')->findOrFail($id);
        
        $employer->update([
            'is_accredited' => 0,
        ]);

        DB::table('employer_accreditation')->where('employer_id', $id)->update([
            'status' => 'disaccredited',
            'admin_approved' => 0,
        ]);

        if ($employer->user) {
            Notification::create([
                'user_id' => $employer->user->user_id,
                'title' => 'Employer Accreditation Revoked',
                'message' => 'Your official accreditation has been revoked by DMDP Administration. Please contact the DMDP office for further details.',
                'type' => 'rejection',
                'is_read' => false,
                'related_id' => $employer->employer_id,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => "Employer '{$employer->company_name}' has been disaccredited."]);
        }

        return redirect()->back()->with('info', "Employer '{$employer->company_name}' has been disaccredited.");
    }

    public function employerCertificate($id)
    {
        $employer = DB::table('employers')->where('employer_id', $id)->first();
        if (!$employer) abort(404, 'Employer not found');
        $accreditation = DB::table('employer_accreditation')->where('employer_id', $id)->first();
        return view('reports.employer-certificate', compact('employer', 'accreditation'));
    }

    /**
     * Update Employer Login Credentials & HR Information
     * Designed for cases where assigned HR resigns without providing password/access.
     */
    public function updateEmployerCredentials(Request $request, $id)
    {
        $employer = DB::table('employers')->where('employer_id', $id)->first();
        if (!$employer) {
            $employer = DB::table('employers')->where('user_id', $id)->first();
        }

        if (!$employer) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Employer record not found.'], 404);
            }
            return redirect()->back()->with('error', 'Employer record not found.');
        }

        $userId = $employer->user_id;

        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                'max:150',
                $userId ? Rule::unique('users', 'email')->ignore($userId, 'user_id') : 'nullable',
            ],
            'secondary_email' => 'nullable|email|max:150',
            'contact_person' => 'nullable|string|max:150',
            'contact_phone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'email.required' => 'The login email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already in use by another user account.',
            'password.min' => 'The new password must be at least 8 characters long.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        // 1. Update User Credentials
        if ($userId) {
            $userUpdate = [
                'email' => $validated['email'],
                'updated_at' => now(),
            ];
            if (!empty($validated['password'])) {
                $userUpdate['password'] = Hash::make($validated['password']);
            }
            DB::table('users')->where('user_id', $userId)->update($userUpdate);

            // Update user_profiles if present
            if (!empty($validated['contact_person']) || !empty($validated['contact_phone'])) {
                $userProfile = DB::table('user_profiles')->where('user_id', $userId)->first();
                if ($userProfile) {
                    $profileUpdate = ['updated_at' => now()];
                    if (!empty($validated['contact_person'])) {
                        $profileUpdate['full_name'] = $validated['contact_person'];
                    }
                    if (!empty($validated['contact_phone'])) {
                        $profileUpdate['phone'] = $validated['contact_phone'];
                    }
                    DB::table('user_profiles')->where('user_id', $userId)->update($profileUpdate);
                } else if (!empty($validated['contact_person'])) {
                    DB::table('user_profiles')->insert([
                        'user_id' => $userId,
                        'full_name' => $validated['contact_person'],
                        'phone' => $validated['contact_phone'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 2. Update Employer Record
        $employerUpdate = [
            'primary_email' => $validated['email'],
            'updated_at' => now(),
        ];
        if (array_key_exists('secondary_email', $validated)) {
            $employerUpdate['secondary_email'] = $validated['secondary_email'];
        }
        if (array_key_exists('contact_person', $validated)) {
            $employerUpdate['contact_person'] = $validated['contact_person'];
        }
        if (array_key_exists('contact_phone', $validated)) {
            $employerUpdate['contact_phone'] = $validated['contact_phone'];
        }

        DB::table('employers')->where('employer_id', $employer->employer_id)->update($employerUpdate);

        // 3. Security Audit Notification
        if ($userId) {
            Notification::create([
                'user_id' => $userId,
                'title' => 'Employer Login Credentials Updated',
                'message' => "The login credentials for {$employer->company_name} were updated by DMDP Administration. Login email: {$validated['email']}.",
                'type' => 'security',
                'is_read' => false,
                'related_id' => $employer->employer_id,
            ]);
        }

        $successMessage = "Login credentials for '{$employer->company_name}' have been updated successfully. The new HR can now log in using '{$validated['email']}'.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'email' => $validated['email'],
            ]);
        }

        return redirect()->back()->with('success', $successMessage);
    }

    // =========================================================================
    // PILLAR 3: PLACEMENT REPORT APPROVAL
    // =========================================================================

    public function approvePlacementReport(Request $request, int $id)
    {
        $report = DB::table('placement_reports')->where('report_id', $id)->first();
        if (!$report) {
            return redirect()->back()->withErrors(['error' => 'Placement report not found.']);
        }

        $remarks = $request->input('remarks', 'Approved and archived for Cebu City official PESO/DMDP records.');

        DB::table('placement_reports')->where('report_id', $id)->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now()->toDateString(),
            'admin_remarks' => $remarks,
        ]);

        // Notify employer
        $employer = Employer::with('user')->find($report->employer_id);
        if ($employer && $employer->user) {
            Notification::create([
                'user_id' => $employer->user->user_id,
                'title' => 'Placement Report Approved',
                'message' => "Your monthly placement report for {$report->report_month} has been approved and logged into City Hall records.",
                'type' => 'approval',
                'is_read' => false,
                'related_id' => $report->report_id,
            ]);
        }

        return redirect()->back()->with('success', "Placement report approved and archived successfully.");
    }

    public function rejectPlacementReport(Request $request, int $id)
    {
        DB::table('placement_reports')->where('report_id', $id)->update([
            'status' => 'rejected',
            'admin_remarks' => $request->input('remarks', 'Discrepancies found in placement records.'),
        ]);

        return redirect()->back()->with('info', "Placement report rejected.");
    }

    public function placementReports(Request $request)
    {
        $statusFilter = $request->input('status', 'all');
        $searchQuery = $request->input('search', '');

        $query = DB::table('placement_reports')
            ->join('employers', 'placement_reports.employer_id', '=', 'employers.employer_id')
            ->select('placement_reports.*', 'employers.company_name');

        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('placement_reports.status', $statusFilter);
        }

        if ($searchQuery) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('employers.company_name', 'LIKE', '%' . $searchQuery . '%')
                  ->orWhere('placement_reports.report_id', 'LIKE', '%' . $searchQuery . '%');
            });
        }

        $reports = $query->latest('placement_reports.report_id')->paginate(15)->withQueryString();

        $stats = [
            'total' => DB::table('placement_reports')->count(),
            'submitted_to_jpo' => DB::table('placement_reports')->where('status', 'submitted_to_jpo')->count(),
            'pending_admin' => DB::table('placement_reports')->where('status', 'jpo_evaluated')->count(),
            'approved' => DB::table('placement_reports')->where('status', 'approved')->count(),
            'rejected' => DB::table('placement_reports')->where('status', 'rejected')->count(),
        ];

        return view('admin.placement-reports.index', compact('reports', 'stats', 'statusFilter', 'searchQuery'));
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

    public function printAccreditation(int $id)
    {
        $accreditation = EmployerAccreditation::with(['employer.user', 'employer.jobPostings'])->findOrFail($id);
        $employer = $accreditation->employer;
        $jobPostings = $employer->jobPostings ?? collect();

        return view('reports.establishment-registration-printable', compact('accreditation', 'employer', 'jobPostings'));
    }

    // =========================================================================
    // VIEW JOBSEEKER STATUS DIRECTORY
    // =========================================================================

    public function jobseekers(Request $request)
    {
        $query = DB::table('jobseekers')
            ->leftJoin('users', 'jobseekers.user_id', '=', 'users.user_id')
            ->leftJoin('social_status', 'jobseekers.jobseeker_id', '=', 'social_status.jobseeker_id')
            ->select(
                'jobseekers.*', 
                'users.user_id',
                'users.email as user_email', 
                'users.status as account_status',
                'users.is_approved as user_is_approved',
                'users.created_at as registered_at',
                'social_status.is_pwd',
                'social_status.pwd_type',
                'social_status.is_4ps'
            )
            ->selectRaw('(SELECT COUNT(*) FROM jobseeker_skills WHERE jobseeker_id = jobseekers.jobseeker_id) as skills_count')
            ->selectRaw("NULL as skills_text")
            ->selectRaw('(SELECT COUNT(*) FROM job_applications WHERE jobseeker_id = jobseekers.jobseeker_id) as applications_count')
            ->selectRaw('(SELECT COUNT(*) FROM training_enrollments WHERE jobseeker_id = jobseekers.jobseeker_id) as trainings_count')
            ->selectRaw('(SELECT COUNT(*) FROM training_enrollments WHERE jobseeker_id = jobseekers.jobseeker_id AND certificate_issued = 1) as certs_count')
            ->selectRaw('(SELECT COUNT(*) FROM job_applications WHERE jobseeker_id = jobseekers.jobseeker_id AND referred_by_jpo = 1) as jpo_referrals_count')
            ->selectRaw("(SELECT COUNT(*) FROM job_applications WHERE jobseeker_id = jobseekers.jobseeker_id AND status = 'hired') as hired_count");

        if ($request->has('search') && $request->search !== '') {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('jobseekers.first_name', 'like', "%{$s}%")
                  ->orWhere('jobseekers.last_name', 'like', "%{$s}%")
                  ->orWhere('users.email', 'like', "%{$s}%");
            });
        }

        if ($request->has('employment_status') && $request->employment_status !== '') {
            $query->where('jobseekers.employment_status', $request->employment_status);
        }

        if ($request->filled('stage')) {
            $stage = $request->stage;
            if ($stage === 'in_training') {
                $query->whereRaw("(SELECT COUNT(*) FROM training_enrollments WHERE jobseeker_id = jobseekers.jobseeker_id AND status = 'in_progress') > 0");
            } elseif ($stage === 'certified') {
                $query->whereRaw('(SELECT COUNT(*) FROM training_enrollments WHERE jobseeker_id = jobseekers.jobseeker_id AND certificate_issued = 1) > 0');
            } elseif ($stage === 'applied') {
                $query->whereRaw('(SELECT COUNT(*) FROM job_applications WHERE jobseeker_id = jobseekers.jobseeker_id) > 0');
            } elseif ($stage === 'jpo_referred') {
                $query->whereRaw('(SELECT COUNT(*) FROM job_applications WHERE jobseeker_id = jobseekers.jobseeker_id AND referred_by_jpo = 1) > 0');
            } elseif ($stage === 'hired') {
                $query->whereRaw("(SELECT COUNT(*) FROM job_applications WHERE jobseeker_id = jobseekers.jobseeker_id AND status = 'hired') > 0");
            }
        }

        if ($request->has('pwd_only') && $request->pwd_only == 1) {
            $query->where('social_status.is_pwd', 1);
        }

        $jobseekers = $query->orderBy('jobseekers.jobseeker_id', 'desc')->paginate(15)->withQueryString();

        // Attach is_verified, missing_requirements, skills_text, location_text, education_text
        $jobseekerIds = collect($jobseekers->items())->pluck('jobseeker_id');
        $models = \App\Models\Jobseeker::with(['user', 'details', 'socialStatus', 'skills'])
            ->whereIn('jobseeker_id', $jobseekerIds)
            ->get()
            ->keyBy('jobseeker_id');

        foreach ($jobseekers->items() as $item) {
            $model = $models->get($item->jobseeker_id);
            $item->is_verified = $model ? $model->isVerified() : false;
            $item->missing_requirements = $model ? array_values($model->getMissingRequirements()) : [];
            $item->skills_text = $model && $model->skills ? $model->skills->pluck('skill_name')->implode(', ') : '';

            $documents = $model && $model->details ? $model->details->training_certificates : [];
            if (is_string($documents)) {
                $documents = json_decode($documents, true) ?: [];
            }
            $validIdDocument = collect(is_array($documents) ? $documents : [])->first(
                fn ($document) => is_array($document) && ($document['category'] ?? null) === 'valid_id'
            );
            $item->valid_id_url = $validIdDocument
                ? route('admin.jobseekers.valid-id', $item->jobseeker_id)
                : null;
            $item->valid_id_name = $validIdDocument['name'] ?? null;
            $validIdSource = (string) ($validIdDocument['file_path'] ?? $validIdDocument['path'] ?? $validIdDocument['name'] ?? '');
            $validIdPath = parse_url($validIdSource, PHP_URL_PATH);
            $item->valid_id_is_image = in_array(
                strtolower(pathinfo(is_string($validIdPath) ? $validIdPath : $validIdSource, PATHINFO_EXTENSION)),
                ['jpg', 'jpeg', 'png', 'webp', 'gif'],
                true
            );

            $address = $model && $model->details ? ($model->details->address ?? []) : [];
            $education = $model && $model->details ? ($model->details->education ?? []) : [];

            $barangay = is_array($address) ? ($address['barangay'] ?? '') : '';
            $city = is_array($address) ? ($address['city'] ?? '') : '';
            $province = is_array($address) ? ($address['province'] ?? 'Cebu') : 'Cebu';
            $loc = trim("{$barangay}, {$city}, {$province}", " ,");
            $item->location_text = !empty($loc) ? $loc : 'Cebu City';

            $eduLevel = is_array($education) ? ($education['level'] ?? ($education['educational_level'] ?? '')) : '';
            $eduCourse = is_array($education) ? ($education['course'] ?? ($education['course_degree'] ?? '')) : '';
            $item->education_text = !empty($eduLevel) ? ($eduLevel . (!empty($eduCourse) ? " ({$eduCourse})" : '')) : 'N/A';
        }

        $totalJobseekers = DB::table('jobseekers')->count();
        $pwdJobseekers = DB::table('social_status')->where('is_pwd', 1)->count();
        $employedJobseekers = DB::table('job_applications')->where('status', 'hired')->distinct('jobseeker_id')->count();
        $inTrainingJobseekers = DB::table('training_enrollments')->where('status', 'in_progress')->distinct('jobseeker_id')->count();
        $certifiedJobseekers = DB::table('training_enrollments')->where('certificate_issued', 1)->distinct('jobseeker_id')->count();

        return view('admin.jobseekers.index', compact(
            'jobseekers', 
            'totalJobseekers', 
            'pwdJobseekers', 
            'employedJobseekers',
            'inTrainingJobseekers',
            'certifiedJobseekers'
        ));
    }

    public function viewJobseekerValidId(int $id)
    {
        $jobseeker = Jobseeker::with('details')->findOrFail($id);
        $documents = $jobseeker->details?->training_certificates ?? [];
        if (is_string($documents)) {
            $documents = json_decode($documents, true) ?: [];
        }

        abort_unless(is_array($documents), 404);
        $document = collect($documents)->first(
            fn ($item) => is_array($item) && ($item['category'] ?? null) === 'valid_id'
        );
        abort_unless($document, 404);

        $disk = Storage::disk('public');
        foreach (['file_path', 'path', 'file_url', 'url'] as $key) {
            $candidate = $document[$key] ?? null;
            if (!is_string($candidate) || trim($candidate) === '') {
                continue;
            }

            $urlPath = parse_url($candidate, PHP_URL_PATH);
            $path = rawurldecode(str_replace('\\', '/', is_string($urlPath) && $urlPath !== '' ? $urlPath : $candidate));
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
            $isJobseekerDocument = str_starts_with($path, "documents/{$jobseeker->jobseeker_id}/")
                || str_starts_with($path, 'ids/')
                || str_starts_with($path, 'valid_ids/');
            if (
                $isJobseekerDocument
                && !in_array('..', $segments, true)
                && !in_array('.', $segments, true)
                && $disk->exists($path)
            ) {
                return $disk->response($path, $document['name'] ?? basename($path), [], 'inline');
            }
        }

        abort(404, 'Valid ID file not found on storage.');
    }

    public function disapproveJobseeker(Request $request, int $id)
    {
        $validated = $request->validate([
            'remarks' => 'required|string|max:1000',
        ]);

        $jobseeker = Jobseeker::with('user', 'details')->findOrFail($id);

        $user = $jobseeker->user;
        abort_unless($user, 404);
        $remarks = trim($validated['remarks']);

        DB::transaction(function () use ($jobseeker, $user, $remarks) {
            $user->update([
                'is_approved' => 0,
                'account_status' => 'unverified',
            ]);

            $details = $jobseeker->details;
            if ($details && !empty($details->training_certificates)) {
                $documents = is_array($details->training_certificates)
                    ? $details->training_certificates
                    : (json_decode($details->training_certificates, true) ?: []);

                foreach ($documents as &$document) {
                    if (is_array($document) && ($document['category'] ?? null) === 'valid_id') {
                        $document['status'] = 'rejected';
                        $document['rejection_reason'] = $remarks;
                        $document['reviewed_by'] = Auth::id();
                        $document['reviewed_at'] = now()->toIso8601String();
                    }
                }
                unset($document);

                $details->training_certificates = array_values($documents);
                $details->save();
            }

            Notification::create([
                'user_id' => $user->user_id,
                'title' => 'Jobseeker Account Requires Revision',
                'message' => "Your account approval was revoked. Admin remarks: {$remarks}",
                'type' => 'rejection',
                'is_read' => false,
                'related_id' => $jobseeker->jobseeker_id,
            ]);
        });

        $message = "Jobseeker '{$jobseeker->first_name} {$jobseeker->last_name}' has been disapproved. The remarks were sent to the jobseeker.";

        if ($request->expectsJson()) {
            return response()->json(['success' => $message]);
        }

        return redirect()->back()->with('success', $message);
    }

    // =========================================================================
    // PILLAR 4: COLLABORATOR TRAINER ACCOUNT APPROVAL
    // =========================================================================

    public function approveTrainer(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'status' => 'active',
            'is_approved' => 1,
        ]);

        DB::table('user_profiles')->where('user_id', $id)->update([
            'is_trainer_approved' => 1,
            'trainer_approved_by' => Auth::id(),
            'trainer_approved_at' => now(),
        ]);

        Notification::create([
            'user_id' => $user->user_id,
            'title' => 'Trainer Account Approved!',
            'message' => "Congratulations! Your trainer collaborator account has been officially approved by the Administrator. You may now log in to the Skills Trainer portal.",
            'type' => 'approval',
            'is_read' => false,
            'related_id' => $user->user_id,
        ]);

        return redirect()->back()->with('success', "Collaborator trainer '{$user->email}' approved successfully.");
    }

    public function rejectTrainer(Request $request, int$id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'status' => 'inactive',
            'is_approved' => 0,
        ]);

        DB::table('user_profiles')->where('user_id', $id)->update([
            'is_trainer_approved' => 0,
        ]);

        return redirect()->back()->with('info', "Collaborator trainer account for '{$user->email}' was rejected.");
    }

    // =========================================================================
    // PILLAR 5: JOBSEEKER IDENTITY & VERIFICATION APPROVAL
    // =========================================================================

    public function approveJobseeker(Request $request, int $id)
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

        // Update document status in details if present
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
                'title' => 'Account & Government ID Approved!',
                'message' => 'Congratulations! Your jobseeker account and government ID have been verified and approved by the DMDP Admin. You are now authorized to submit job applications.',
                'type' => 'approval',
                'is_read' => false,
                'related_id' => $jobseeker->jobseeker_id,
            ]);
        }

        $message = "Jobseeker '{$jobseeker->first_name} {$jobseeker->last_name}' has been approved and verified successfully.";

        if ($request->expectsJson()) {
            return response()->json(['success' => $message]);
        }

        return redirect()->back()->with('success', $message);
    }

    public function rejectJobseeker(Request $request, int $id)
    {
        $jobseeker = Jobseeker::with('user', 'details')->find($id);
        if (!$jobseeker) {
            $jobseeker = Jobseeker::with('user', 'details')->where('user_id', $id)->firstOrFail();
        }

        $reason = $request->input('remarks') ?: 'Your submitted government ID or documents did not meet verification criteria. Please upload a clear valid government ID in your Document Hub.';

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
                'title' => 'Verification Document Requires Revision',
                'message' => "Your government ID submission requires revision: {$reason}",
                'type' => 'rejection',
                'is_read' => false,
                'related_id' => $jobseeker->jobseeker_id,
            ]);
        }

        return redirect()->back()->with('info', "Jobseeker '{$jobseeker->first_name} {$jobseeker->last_name}' returned for document revision.");
    }
}
