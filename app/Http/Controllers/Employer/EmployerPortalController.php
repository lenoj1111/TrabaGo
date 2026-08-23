<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmployerPortalController extends Controller
{
    private function getOrCreateEmployer(): Employer
    {
        $user = Auth::user();
        $employer = Employer::where('user_id', $user->user_id)->first();

        if (!$employer) {
            $companyName = $user->full_name ?? (explode('@', $user->email)[0] . ' Corp');
            $employer = Employer::create([
                'user_id' => $user->user_id,
                'company_name' => $companyName,
                'is_accredited' => 0,
            ]);
        }

        return $employer;
    }

    public function homepage()
    {
        $employer = $this->getOrCreateEmployer();
        $user = Auth::user();

        // Metrics
        $totalJobs = JobPosting::where('employer_id', $employer->employer_id)->count();
        $approvedJobs = JobPosting::where('employer_id', $employer->employer_id)->where('status', 'approved')->count();
        $pendingJobs = JobPosting::where('employer_id', $employer->employer_id)->where('status', 'pending')->count();
        
        // Job applications & referred applicants from JPO
        $jobIds = JobPosting::where('employer_id', $employer->employer_id)->pluck('job_id');
        $totalApplicants = JobApplication::whereIn('job_id', $jobIds)->count();
        $referredCount = JobApplication::whereIn('job_id', $jobIds)->where('referred_by_jpo', 1)->count();
        $hiredCount = JobApplication::whereIn('job_id', $jobIds)->where('status', 'hired')->count();

        // Accreditation status
        $accreditation = DB::table('employer_accreditation')->where('employer_id', $employer->employer_id)->first();

        // Placement reports
        $placementReports = DB::table('placement_reports')->where('employer_id', $employer->employer_id)->get();

        // Recent referred jobseekers
        $recentReferred = JobApplication::with(['jobseeker', 'jobPosting'])
            ->whereIn('job_id', $jobIds)
            ->where('referred_by_jpo', 1)
            ->latest()
            ->take(5)
            ->get();

        return view('employer.homepage', compact(
            'employer', 'user', 'totalJobs', 'approvedJobs', 'pendingJobs', 
            'totalApplicants', 'referredCount', 'hiredCount', 'accreditation', 
            'placementReports', 'recentReferred'
        ));
    }

    // =========================================================================
    // 1. CREATE JOB POSTING (SEND TO ADMIN)
    // =========================================================================

    public function jobPostings()
    {
        $employer = $this->getOrCreateEmployer();
        $jobs = JobPosting::where('employer_id', $employer->employer_id)->latest()->paginate(10);

        return view('employer.job-postings', compact('employer', 'jobs'));
    }

    public function storeJobPosting(Request $request)
    {
        $employer = $this->getOrCreateEmployer();

        $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'required|string',
            'qualifications' => 'nullable|string',
            'vacancy_count' => 'required|integer|min:1',
            'valid_until' => 'nullable|date',
            'accepts_disability' => 'nullable|boolean',
            'disability_type' => 'nullable|string|max:100',
        ]);

        $job = JobPosting::create([
            'employer_id' => $employer->employer_id,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'qualifications' => $request->input('qualifications'),
            'vacancy_count' => $request->input('vacancy_count', 1),
            'valid_until' => $request->input('valid_until', now()->addMonths(2)->toDateString()),
            'accepts_disability' => $request->boolean('accepts_disability'),
            'disability_type' => $request->input('disability_type'),
            'status' => 'pending', // Sent to Admin for review & approval
            'created_by' => 'employer',
            'created_at' => now()->toDateString(),
        ]);

        // Notify Admins of new job posting requiring approval
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->user_id,
                'title' => 'New Job Posting Pending Approval',
                'message' => "Employer {$employer->company_name} submitted a new job opening '{$job->title}' for review.",
                'type' => 'manual_review',
                'is_read' => false,
                'related_id' => $job->job_id,
            ]);
        }

        return redirect()->route('employer.job-postings')->with('success', 'Job posting created successfully and forwarded to the Admin for approval.');
    }

    // =========================================================================
    // 2. PASS ACCREDITATION PAPERS (SEND TO JPO)
    // =========================================================================

    public function accreditation()
    {
        $employer = $this->getOrCreateEmployer();
        $accreditation = DB::table('employer_accreditation')->where('employer_id', $employer->employer_id)->first();

        return view('employer.accreditation', compact('employer', 'accreditation'));
    }

    public function submitAccreditation(Request $request)
    {
        $employer = $this->getOrCreateEmployer();

        $request->validate([
            'business_permit' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'sec_dti' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'bir_2303' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'company_profile' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $docs = [];
        $files = ['business_permit', 'sec_dti', 'bir_2303', 'company_profile'];
        foreach ($files as $fileKey) {
            if ($request->hasFile($fileKey)) {
                $path = $request->file($fileKey)->store('accreditation_docs', 'public');
                $docs[$fileKey] = [
                    'original_name' => $request->file($fileKey)->getClientOriginalName(),
                    'path' => $path,
                    'uploaded_at' => now()->toIso8601String(),
                ];
            }
        }

        $existing = DB::table('employer_accreditation')->where('employer_id', $employer->employer_id)->first();
        if ($existing) {
            $prevDocs = is_array($existing->documents) ? $existing->documents : json_decode($existing->documents ?? '[]', true);
            $mergedDocs = array_merge($prevDocs ?: [], $docs);

            DB::table('employer_accreditation')->where('employer_id', $employer->employer_id)->update([
                'documents' => json_encode($mergedDocs),
                'status' => 'submitted_to_jpo',
                'jpo_reviewed' => 0,
                'supervisor_approved' => 0,
                'admin_approved' => 0,
                'submitted_at' => now()->toDateString(),
            ]);
        } else {
            DB::table('employer_accreditation')->insert([
                'employer_id' => $employer->employer_id,
                'documents' => json_encode($docs),
                'status' => 'submitted_to_jpo',
                'jpo_reviewed' => 0,
                'supervisor_approved' => 0,
                'admin_approved' => 0,
                'submitted_at' => now()->toDateString(),
            ]);
        }

        // Notify all Job Placement Officers (JPO)
        $jpos = User::where('role', 'jpo')->get();
        foreach ($jpos as $jpo) {
            Notification::create([
                'user_id' => $jpo->user_id,
                'title' => 'Employer Accreditation Submitted',
                'message' => "Employer '{$employer->company_name}' submitted accreditation documents for your evaluation.",
                'type' => 'manual_review',
                'is_read' => false,
                'related_id' => $employer->employer_id,
            ]);
        }

        return redirect()->route('employer.accreditation')->with('success', 'Accreditation papers submitted successfully to the Job Placement Officer (JPO) for evaluation.');
    }

    // =========================================================================
    // 3. REVIEW REFERRED JOBSEEKERS (FROM JPO)
    // =========================================================================

    public function referredJobseekers()
    {
        $employer = $this->getOrCreateEmployer();
        $jobIds = JobPosting::where('employer_id', $employer->employer_id)->pluck('job_id');

        $referredApplicants = JobApplication::with(['jobseeker.skills', 'jobseeker.details', 'jobPosting'])
            ->whereIn('job_id', $jobIds)
            ->where('referred_by_jpo', 1)
            ->latest()
            ->paginate(15);

        return view('employer.referred-jobseekers', compact('employer', 'referredApplicants'));
    }

    public function updateApplicantStatus(Request $request, $id)
    {
        $employer = $this->getOrCreateEmployer();
        $jobIds = JobPosting::where('employer_id', $employer->employer_id)->pluck('job_id');

        $application = JobApplication::whereIn('job_id', $jobIds)->findOrFail($id);

        $action = $request->input('action');
        if ($action === 'interview') {
            $request->validate([
                'interview_schedule' => 'required|date',
                'interview_mode' => 'required|in:online,onsite',
                'interview_location' => 'required|string|max:255',
            ]);

            $application->update([
                'status' => 'interview',
                'interview_schedule' => $request->input('interview_schedule'),
                'interview_mode' => $request->input('interview_mode'),
                'interview_location' => $request->input('interview_location'),
                'interview_status' => 'scheduled',
                'jobseeker_response' => 'pending',
            ]);

            // Notify jobseeker
            $jobseekerUser = $application->jobseeker->user;
            if ($jobseekerUser) {
                Notification::create([
                    'user_id' => $jobseekerUser->user_id,
                    'title' => 'Interview Scheduled!',
                    'message' => "{$employer->company_name} scheduled an interview for '{$application->jobPosting->title}' on " . date('M d, Y h:i A', strtotime($request->input('interview_schedule'))),
                    'type' => 'interview',
                    'is_read' => false,
                    'related_id' => $application->application_id,
                ]);
            }

            return redirect()->back()->with('success', 'Interview scheduled and invitation sent to applicant.');
        } elseif ($action === 'hire') {
            $application->update([
                'status' => 'hired',
                'hired_date' => now()->toDateString(),
            ]);

            $jobseekerUser = $application->jobseeker->user;
            if ($jobseekerUser) {
                Notification::create([
                    'user_id' => $jobseekerUser->user_id,
                    'title' => 'Congratulations! You Have Been Hired!',
                    'message' => "Congratulations! {$employer->company_name} has officially hired you for the '{$application->jobPosting->title}' position.",
                    'type' => 'approval',
                    'is_read' => false,
                    'related_id' => $application->application_id,
                ]);
            }

            return redirect()->back()->with('success', 'Jobseeker status updated to Hired!');
        } elseif ($action === 'reject') {
            $application->update(['status' => 'rejected']);
            return redirect()->back()->with('info', 'Applicant marked as rejected.');
        }

        return redirect()->back();
    }

    // =========================================================================
    // 4. GENERATE PLACEMENT REPORT (SEND TO JPO)
    // =========================================================================

    public function placementReports()
    {
        $employer = $this->getOrCreateEmployer();
        $jobIds = JobPosting::where('employer_id', $employer->employer_id)->pluck('job_id');

        $reports = DB::table('placement_reports')->where('employer_id', $employer->employer_id)->latest('report_id')->paginate(10);
        $hiredApplicants = JobApplication::with(['jobseeker', 'jobPosting'])
            ->whereIn('job_id', $jobIds)
            ->where('status', 'hired')
            ->get();

        return view('employer.placement-reports', compact('employer', 'reports', 'hiredApplicants'));
    }

    public function generatePlacementReport(Request $request)
    {
        $employer = $this->getOrCreateEmployer();
        $jobIds = JobPosting::where('employer_id', $employer->employer_id)->pluck('job_id');

        $month = $request->input('report_month', now()->format('Y-m'));
        $hiredApplicants = JobApplication::with(['jobseeker', 'jobPosting'])
            ->whereIn('job_id', $jobIds)
            ->where('status', 'hired')
            ->get();

        $reportData = [
            'month' => $month,
            'company_name' => $employer->company_name,
            'total_hired' => $hiredApplicants->count(),
            'hired_list' => $hiredApplicants->map(function ($app) {
                return [
                    'jobseeker_name' => $app->jobseeker->full_name ?? ($app->jobseeker->first_name . ' ' . $app->jobseeker->last_name),
                    'position' => $app->jobPosting->title ?? 'N/A',
                    'hired_date' => $app->hired_date ?: now()->toDateString(),
                    'referred_by_jpo' => $app->referred_by_jpo ? 'Yes' : 'Direct',
                ];
            })->toArray(),
            'notes' => $request->input('notes', 'Monthly placement report for Cebu City DMDP.'),
            'submitted_at' => now()->toIso8601String(),
        ];

        // Find or assign first JPO
        $jpo = DB::table('user_profiles')->join('users', 'user_profiles.user_id', '=', 'users.user_id')->where('users.role', 'jpo')->first();
        $jpoProfileId = $jpo ? $jpo->profile_id : 1;

        try {
            $reportId = DB::table('placement_reports')->insertGetId([
                'employer_id' => $employer->employer_id,
                'jpo_id' => $jpoProfileId,
                'report_type' => 'employer_monthly',
                'report_month' => $month . '-01',
                'report_data' => json_encode($reportData),
                'status' => 'submitted_to_jpo',
                'jpo_evaluated' => 0,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if (DB::getDriverName() === 'sqlsrv' && str_contains($e->getMessage(), 'CHECK constraint')) {
                $constraints = DB::select("SELECT name FROM sys.check_constraints WHERE parent_object_id = OBJECT_ID('placement_reports') AND (definition LIKE '%status%' OR name LIKE '%placement%statu%')");
                foreach ($constraints as $c) {
                    DB::statement("ALTER TABLE [placement_reports] DROP CONSTRAINT [{$c->name}]");
                }
                DB::statement("ALTER TABLE [placement_reports] ADD CONSTRAINT [CK_placement_reports_status] CHECK ([status] IN ('pending', 'submitted_to_jpo', 'jpo_evaluated', 'approved', 'rejected'))");

                $reportId = DB::table('placement_reports')->insertGetId([
                    'employer_id' => $employer->employer_id,
                    'jpo_id' => $jpoProfileId,
                    'report_type' => 'employer_monthly',
                    'report_month' => $month . '-01',
                    'report_data' => json_encode($reportData),
                    'status' => 'submitted_to_jpo',
                    'jpo_evaluated' => 0,
                ]);
            } else {
                throw $e;
            }
        }

        // Notify JPOs
        $jpos = User::where('role', 'jpo')->get();
        foreach ($jpos as $jUser) {
            Notification::create([
                'user_id' => $jUser->user_id,
                'title' => 'New Placement Report Received',
                'message' => "Employer '{$employer->company_name}' submitted monthly placement report for {$month}.",
                'type' => 'manual_review',
                'is_read' => false,
                'related_id' => $reportId,
            ]);
        }

        return redirect()->route('employer.placement-reports')->with('success', 'Monthly placement report generated and sent to the Job Placement Officer (JPO) for evaluation.');
    }

    // =========================================================================
    // 5. PROFILE & SETTINGS
    // =========================================================================

    public function profile()
    {
        $employer = $this->getOrCreateEmployer();
        $user = Auth::user();

        return view('employer.profile', compact('employer', 'user'));
    }

    public function updateProfile(Request $request)
    {
        $employer = $this->getOrCreateEmployer();
        $user = Auth::user();

        $request->validate([
            'company_name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:50',
            'office_address' => 'nullable|string|max:255',
        ]);

        $employer->update(['company_name' => $request->input('company_name')]);

        return redirect()->route('employer.profile')->with('success', 'Company profile updated successfully.');
    }

    // =========================================================================
    // 6. NOTIFICATION CENTER
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

        return view('employer.notifications', compact('notifications', 'user', 'unreadCount'));
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
