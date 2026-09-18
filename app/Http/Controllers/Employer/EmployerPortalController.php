<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\EmployerAccreditation;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
        $interviewCount = JobApplication::whereIn('job_id', $jobIds)->where('status', 'interview')->count();
        $hiredCount = JobApplication::whereIn('job_id', $jobIds)->where('status', 'hired')->count();
        $pendingScreeningCount = JobApplication::whereIn('job_id', $jobIds)->whereIn('status', ['pending', 'reviewed'])->count();

        // Top Job Postings with application distribution
        $topJobs = JobPosting::where('employer_id', $employer->employer_id)
            ->withCount('applications')
            ->orderByDesc('applications_count')
            ->take(5)
            ->get();

        // Monthly applicant volume trends (last 6 months)
        $monthlyApplicantTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            $monthLabel = $monthStart->format('M Y');
            
            $appsCount = JobApplication::whereIn('job_id', $jobIds)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            $hCount = JobApplication::whereIn('job_id', $jobIds)
                ->where('status', 'hired')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            $monthlyApplicantTrends[] = [
                'month' => $monthLabel,
                'applications' => $appsCount,
                'hires' => $hCount,
            ];
        }

        // Accreditation status
        $accreditation = DB::table('employer_accreditation')->where('employer_id', $employer->employer_id)->first();

        // Placement reports
        $placementReports = DB::table('placement_reports')->where('employer_id', $employer->employer_id)->get();

        // Recent applicants
        $recentApplicants = JobApplication::with(['jobseeker', 'jobPosting'])
            ->whereIn('job_id', $jobIds)
            ->latest()
            ->take(5)
            ->get();

        return view('employer.homepage', compact(
            'employer', 'user', 'totalJobs', 'approvedJobs', 'pendingJobs', 
            'totalApplicants', 'referredCount', 'interviewCount', 'hiredCount', 'pendingScreeningCount',
            'topJobs', 'monthlyApplicantTrends', 'accreditation', 
            'placementReports', 'recentApplicants'
        ));
    }

    // =========================================================================
    // 1. MANAGE JOB POSTINGS (FULL CRUD & APPROVAL PIPELINE)
    // =========================================================================

    public function jobPostings(Request $request)
    {
        $employer = $this->getOrCreateEmployer();

        $query = JobPosting::where('employer_id', $employer->employer_id);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('qualifications', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        $jobs = $query->withCount('applications')->latest('job_id')->paginate(10)->withQueryString();

        $stats = [
            'total' => JobPosting::where('employer_id', $employer->employer_id)->count(),
            'approved' => JobPosting::where('employer_id', $employer->employer_id)->where('status', 'approved')->count(),
            'pending' => JobPosting::where('employer_id', $employer->employer_id)->where('status', 'pending')->count(),
            'rejected' => JobPosting::where('employer_id', $employer->employer_id)->where('status', 'rejected')->count(),
            'closed' => JobPosting::where('employer_id', $employer->employer_id)->where('status', 'closed')->count(),
        ];

        return view('employer.job-postings', compact('employer', 'jobs', 'stats'));
    }

    public function createJobPosting()
    {
        return redirect()->route('employer.job-postings', ['create' => 1]);
    }

    public function storeJobPosting(Request $request)
    {
        $employer = $this->getOrCreateEmployer();

        $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'required|string',
            'qualifications' => 'nullable|string',
            'vacancy_count' => 'required|integer|min:1',
            'valid_until' => ['nullable', 'date', 'after_or_equal:today'],
            'accepts_disability' => 'nullable|boolean',
            'disability_type' => 'nullable|string|max:100',
        ], [
            'valid_until.after_or_equal' => 'The valid until date cannot be in the past.',
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

    public function showJobPosting($id)
    {
        $employer = $this->getOrCreateEmployer();
        $job = JobPosting::with(['applications.jobseeker', 'applications.jobseeker.skills'])
            ->where('employer_id', $employer->employer_id)
            ->findOrFail($id);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($job);
        }

        return view('employer.job-postings-show', compact('employer', 'job'));
    }

    public function editJobPosting($id)
    {
        $employer = $this->getOrCreateEmployer();
        $job = JobPosting::where('employer_id', $employer->employer_id)->findOrFail($id);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($job);
        }

        return redirect()->route('employer.job-postings', ['edit_id' => $id]);
    }

    public function updateJobPosting(Request $request, $id)
    {
        $employer = $this->getOrCreateEmployer();
        $job = JobPosting::where('employer_id', $employer->employer_id)->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'required|string',
            'qualifications' => 'nullable|string',
            'vacancy_count' => 'required|integer|min:1',
            'valid_until' => ['nullable', 'date', 'after_or_equal:today'],
            'accepts_disability' => 'nullable|boolean',
            'disability_type' => 'nullable|string|max:100',
        ], [
            'valid_until.after_or_equal' => 'The valid until date cannot be in the past.',
        ]);

        $status = $job->status;
        // If the job posting was previously rejected, re-submitting moves it to pending for re-review
        if ($job->status === 'rejected') {
            $status = 'pending';
        }

        $job->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'qualifications' => $request->input('qualifications'),
            'vacancy_count' => $request->input('vacancy_count'),
            'valid_until' => $request->input('valid_until'),
            'accepts_disability' => $request->boolean('accepts_disability'),
            'disability_type' => $request->input('disability_type'),
            'status' => $status,
        ]);

        return redirect()->route('employer.job-postings')->with('success', 'Job posting updated successfully.');
    }

    public function destroyJobPosting($id)
    {
        $employer = $this->getOrCreateEmployer();
        $job = JobPosting::where('employer_id', $employer->employer_id)->findOrFail($id);

        // Check if there are applications
        $applicationsCount = JobApplication::where('job_id', $job->job_id)->count();
        if ($applicationsCount > 0) {
            $job->update(['status' => 'closed']);
            return redirect()->route('employer.job-postings')->with('info', "Job posting '{$job->title}' has active applications, so it has been closed rather than removed.");
        }

        $jobTitle = $job->title;
        $job->delete();

        return redirect()->route('employer.job-postings')->with('success', "Job posting '{$jobTitle}' deleted successfully.");
    }

    public function closeJobPosting($id)
    {
        $employer = $this->getOrCreateEmployer();
        $job = JobPosting::where('employer_id', $employer->employer_id)->findOrFail($id);

        $job->update(['status' => 'closed']);

        return redirect()->route('employer.job-postings')->with('success', "Job posting '{$job->title}' marked as closed.");
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
            'bir_2303' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'sec_dti' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'mayors_permit' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'business_permit' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'philjobnet_proof' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'job_vacancies_form' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'dole_license' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'dmw_license' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'dmw_job_orders' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'letter_of_intent' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'company_profile' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $docs = [];
        $files = [
            'bir_2303',
            'sec_dti',
            'mayors_permit',
            'business_permit',
            'philjobnet_proof',
            'job_vacancies_form',
            'dole_license',
            'dmw_license',
            'dmw_job_orders',
            'letter_of_intent',
            'company_profile',
        ];
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

    public function referredJobseekers(Request $request)
    {
        $employer = $this->getOrCreateEmployer();
        $jobIds = JobPosting::where('employer_id', $employer->employer_id)->pluck('job_id');

        $query = JobApplication::with(['jobseeker.skills', 'jobseeker.details', 'jobPosting'])
            ->whereIn('job_id', $jobIds);

        // Filter by specific job if requested
        if ($request->filled('job_id')) {
            $query->where('job_id', $request->input('job_id'));
        }

        // Filter by status tab
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $status = $request->input('status');
            if ($status === 'not_qualified') {
                $query->where('status', 'rejected');
            } elseif ($status === 'resignation_requested') {
                $query->where('resignation_status', 'requested');
            } else {
                $query->where('status', $status);
            }
        }

        // Search by candidate name or job title
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('jobseeker', function ($jq) use ($search) {
                    $jq->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('jobPosting', function ($jq) use ($search) {
                    $jq->where('title', 'like', "%{$search}%");
                });
            });
        }

        $referredApplicants = $query->latest('application_id')->paginate(15)->withQueryString();

        $stats = [
            'total' => JobApplication::whereIn('job_id', $jobIds)->count(),
            'pending' => JobApplication::whereIn('job_id', $jobIds)->whereIn('status', ['pending', 'reviewed'])->count(),
            'interview' => JobApplication::whereIn('job_id', $jobIds)->where('status', 'interview')->count(),
            'offered' => JobApplication::whereIn('job_id', $jobIds)->where('status', 'offered')->count(),
            'hired' => JobApplication::whereIn('job_id', $jobIds)->where('status', 'hired')->count(),
            'not_qualified' => JobApplication::whereIn('job_id', $jobIds)->where('status', 'rejected')->count(),
            'resignation_requested' => JobApplication::whereIn('job_id', $jobIds)->where('resignation_status', 'requested')->count(),
        ];

        $employerJobs = JobPosting::where('employer_id', $employer->employer_id)->select('job_id', 'title')->get();

        return view('employer.referred-jobseekers', compact('employer', 'referredApplicants', 'stats', 'employerJobs'));
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
            $jobseekerUser = $application->jobseeker->user ?? null;
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
        } elseif ($action === 'offer') {
            $request->validate([
                'offer_salary' => 'nullable|numeric|min:0',
                'offer_start_date' => 'nullable|date',
                'offer_notes' => 'nullable|string|max:1000',
            ]);

            $application->update([
                'status' => 'offered',
                'offered_at' => now(),
                'offer_salary' => $request->input('offer_salary'),
                'offer_start_date' => $request->input('offer_start_date'),
                'offer_notes' => $request->input('offer_notes'),
            ]);

            // Notify jobseeker
            $jobseekerUser = $application->jobseeker->user ?? null;
            if ($jobseekerUser) {
                $salaryTxt = $request->filled('offer_salary') ? ' with an offered salary of ₱' . number_format($request->input('offer_salary'), 2) : '';
                Notification::create([
                    'user_id' => $jobseekerUser->user_id,
                    'title' => 'Job Offer Received!',
                    'message' => "Congratulations! {$employer->company_name} has extended you a formal Job Offer for the '{$application->jobPosting->title}' position{$salaryTxt}. Please review and respond in your Applications dashboard.",
                    'type' => 'approval',
                    'is_read' => false,
                    'related_id' => $application->application_id,
                ]);
            }

            return redirect()->back()->with('success', "Formal job offer sent to {$application->jobseeker->first_name} {$application->jobseeker->last_name}!");
        } elseif ($action === 'hire') {
            $application->update([
                'status' => 'hired',
                'hired_date' => now()->toDateString(),
            ]);

            // Automatically tag jobseeker profile as Employed and record hiring company
            if ($application->jobseeker) {
                $application->jobseeker->update([
                    'employment_status' => 'Employed',
                    'hired_company' => $employer->company_name,
                ]);
            }

            $jobseekerUser = $application->jobseeker->user ?? null;
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

            return redirect()->back()->with('success', "Candidate '{$application->jobseeker->first_name} {$application->jobseeker->last_name}' status updated to Hired!");
        } elseif ($action === 'not_qualified' || $action === 'reject') {
            $remarks = $request->input('remarks') ?: ($request->input('notes') ?: 'Candidate does not meet the specified qualifications for this role.');

            $application->update([
                'status' => 'rejected',
                'jpo_notes' => $remarks,
            ]);

            $jobseekerUser = $application->jobseeker->user ?? null;
            if ($jobseekerUser) {
                Notification::create([
                    'user_id' => $jobseekerUser->user_id,
                    'title' => 'Application Status: Not Qualified',
                    'message' => "{$employer->company_name} reviewed your application for '{$application->jobPosting->title}' and marked it as Not Qualified. Reason: {$remarks}",
                    'type' => 'manual_review',
                    'is_read' => false,
                    'related_id' => $application->application_id,
                ]);
            }

            return redirect()->back()->with('info', "Candidate '{$application->jobseeker->first_name} {$application->jobseeker->last_name}' marked as Not Qualified.");
        }

        return redirect()->back();
    }

    public function respondResignation(Request $request, $id)
    {
        $employer = $this->getOrCreateEmployer();
        $jobIds = JobPosting::where('employer_id', $employer->employer_id)->pluck('job_id');

        $application = JobApplication::whereIn('job_id', $jobIds)
            ->with(['jobseeker', 'jobPosting'])
            ->findOrFail($id);

        $request->validate([
            'action' => 'required|in:approve,reject',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $action = $request->input('action');
        $remarks = $request->input('remarks');

        if ($action === 'approve') {
            $application->update([
                'resignation_status' => 'approved',
                'resignation_approved_at' => now(),
                'resignation_remarks' => $remarks,
            ]);

            // Untag jobseeker profile: set to Unemployed and clear hired_company
            if ($application->jobseeker) {
                $application->jobseeker->update([
                    'employment_status' => 'Unemployed',
                    'hired_company' => null,
                ]);
            }

            // Notify jobseeker
            $jobseekerUser = $application->jobseeker?->user;
            if ($jobseekerUser) {
                Notification::create([
                    'user_id' => $jobseekerUser->user_id,
                    'title' => 'Resignation Approved',
                    'message' => "Your resignation request for '{$application->jobPosting->title}' has been approved by {$employer->company_name}. Your profile has been updated to Unemployed, and you may now apply for other job opportunities.",
                    'type' => 'approval',
                    'is_read' => false,
                    'related_id' => $application->application_id,
                ]);
            }

            return redirect()->back()->with('success', "Resignation request for {$application->jobseeker->first_name} {$application->jobseeker->last_name} has been approved. The candidate is now marked as Unemployed.");
        } else {
            $application->update([
                'resignation_status' => 'rejected',
                'resignation_remarks' => $remarks,
            ]);

            // Notify jobseeker
            $jobseekerUser = $application->jobseeker?->user;
            if ($jobseekerUser) {
                Notification::create([
                    'user_id' => $jobseekerUser->user_id,
                    'title' => 'Resignation Request Declined',
                    'message' => "Your resignation request for '{$application->jobPosting->title}' was declined by {$employer->company_name}." . ($remarks ? " Reason: {$remarks}" : ''),
                    'type' => 'manual_review',
                    'is_read' => false,
                    'related_id' => $application->application_id,
                ]);
            }

            return redirect()->back()->with('info', "Resignation request for {$application->jobseeker->first_name} {$application->jobseeker->last_name} was declined.");
        }
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

    public function showPlacementReport($id)
    {
        $employer = $this->getOrCreateEmployer();
        $report = DB::table('placement_reports')
            ->join('employers', 'placement_reports.employer_id', '=', 'employers.employer_id')
            ->where('placement_reports.report_id', $id)
            ->where('placement_reports.employer_id', $employer->employer_id)
            ->select('placement_reports.*', 'employers.company_name')
            ->first();

        if (!$report) {
            abort(404, 'Placement report not found or unauthorized.');
        }

        return view('reports.placement-printable', compact('report'));
    }

    public function printAccreditation()
    {
        $employer = $this->getOrCreateEmployer();
        $accreditation = EmployerAccreditation::where('employer_id', $employer->employer_id)->first();
        $jobPostings = $employer->jobPostings ?? collect();

        return view('reports.establishment-registration-printable', compact('accreditation', 'employer', 'jobPostings'));
    }

    // =========================================================================
    // 5. PROFILE & SETTINGS
    // =========================================================================

    public function profile()
    {
        $employer = $this->getOrCreateEmployer();
        $user = Auth::user();
        $profile = $user->profile ?: new UserProfile(['user_id' => $user->user_id]);
        $accreditation = DB::table('employer_accreditation')->where('employer_id', $employer->employer_id)->first();

        return view('employer.profile', compact('employer', 'user', 'profile', 'accreditation'));
    }

    public function updateProfile(Request $request)
    {
        $employer = $this->getOrCreateEmployer();
        $user = Auth::user();

        $request->validate([
            'company_name' => 'required|string|max:150',
            'full_name' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:150',
            'office' => 'nullable|string|max:150',
            'specialization' => 'nullable|string|max:150',
        ]);

        $employer->update(['company_name' => $request->input('company_name')]);

        $profile = $user->profile ?: new UserProfile(['user_id' => $user->user_id]);
        $profile->full_name = $request->input('full_name', $profile->full_name);
        $profile->phone = $request->input('phone', $profile->phone);
        $profile->position = $request->input('position', $profile->position);
        $profile->department = $request->input('department', $profile->department);
        $profile->office = $request->input('office', $profile->office);
        $profile->specialization = $request->input('specialization', $profile->specialization);
        $profile->save();

        return redirect()->route('employer.profile')->with('success', 'Company and representative profile updated successfully.');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The new password must be at least 8 characters in length.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('employer.profile', ['tab' => 'security'])
                ->withErrors(['current_password' => 'The provided current password does not match your account password.'])
                ->withInput();
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        Notification::create([
            'user_id' => $user->user_id,
            'title' => 'Password Reset Successfully',
            'message' => 'Your employer account password was recently updated. If you did not initiate this change, please contact DMDP administrator immediately.',
            'type' => 'manual_review',
            'is_read' => false,
        ]);

        return redirect()->route('employer.profile', ['tab' => 'security'])
            ->with('success', 'Your password has been successfully reset and updated.');
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
