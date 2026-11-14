<?php

namespace App\Http\Controllers\Jobseeker;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\JobPreference;
use App\Models\Jobseeker;
use App\Models\JobseekerDetail;
use App\Models\JobseekerSkill;
use App\Models\Notification;
use App\Models\SocialStatus;
use App\Models\TrainingEnrollment;
use App\Models\TrainingProgram;
use App\Models\TrainingTopic;
use App\Services\IdDocumentVerificationService;
use App\Services\SkillMatchingService;
use App\Services\TrainingQuizService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class JobseekerPortalController extends Controller
{
    protected SkillMatchingService $matchingService;
    protected TrainingQuizService $quizService;
    protected IdDocumentVerificationService $idDocumentVerification;

    public function __construct(
        SkillMatchingService $matchingService,
        TrainingQuizService $quizService,
        IdDocumentVerificationService $idDocumentVerification
    ) {
        $this->matchingService = $matchingService;
        $this->quizService = $quizService;
        $this->idDocumentVerification = $idDocumentVerification;
    }

    private function getOrCreateJobseeker(): Jobseeker
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $jobseeker = $user->jobseeker()->first();

        if (!$jobseeker) {
            $nameParts = explode(' ', $user->full_name ?? '', 2);
            $jobseeker = Jobseeker::create([
                'user_id' => $user->user_id,
                'first_name' => $nameParts[0] ?? explode('@', $user->email)[0] ?? 'Jobseeker',
                'last_name' => $nameParts[1] ?? '',
                'email' => $user->email,
                'employment_status' => 'Looking for job',
                'citizenship' => 'Filipino',
            ]);
        }

        return $jobseeker;
    }

    // =========================================================================
    // 1. DASHBOARD
    // =========================================================================

    public function index()
    {
        $user = Auth::user();
        $jobseeker = $this->getOrCreateJobseeker();
        $userSkills = $this->matchingService->getJobseekerSkills($jobseeker);

        $activeHiredApp = $jobseeker->activeHiredApplication;

        $allJobs = JobPosting::with('employer')
            ->availableForJobseekers()
            ->orderByDesc('job_id')
            ->get();

        $rankedJobs = $this->matchingService->rankJobsForJobseeker($allJobs, $jobseeker);
        $bestMatch = !empty($rankedJobs) ? $rankedJobs[0] : null;

        $availableJobsCount = $allJobs->count();
        $activeApplicationsCount = JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)
            ->whereIn('status', ['pending', 'reviewed', 'interview'])
            ->count();
        $availableTrainingsCount = TrainingProgram::count();

        $profileStrength = 30;
        if (!empty($jobseeker->first_name) && !empty($jobseeker->last_name)) $profileStrength += 15;
        if (!empty($jobseeker->mobile_number)) $profileStrength += 15;
        if (count($userSkills) >= 3) $profileStrength += 20;
        elseif (count($userSkills) >= 1) $profileStrength += 10;
        if ($jobseeker->details && (!empty($jobseeker->details->education) || !empty($jobseeker->details->work_experience))) $profileStrength += 20;
        $profileStrength = min(100, $profileStrength);

        $trainings = TrainingProgram::with('topics')->take(3)->get();

        $recentApplications = JobApplication::with(['jobPosting.employer'])
            ->where('jobseeker_id', $jobseeker->jobseeker_id)
            ->orderByDesc('application_id')
            ->take(3)
            ->get();

        return view('jobseeker.homepage', compact(
            'user',
            'jobseeker',
            'userSkills',
            'bestMatch',
            'rankedJobs',
            'availableJobsCount',
            'activeApplicationsCount',
            'availableTrainingsCount',
            'profileStrength',
            'trainings',
            'recentApplications',
            'activeHiredApp'

        ));
    }

    // =========================================================================
    // 2. JOB SEARCH
    // =========================================================================

    public function jobs(Request $request)
    {
        $user = Auth::user();
        $jobseeker = $this->getOrCreateJobseeker();
        $userSkills = $this->matchingService->getJobseekerSkills($jobseeker);

        $query = JobPosting::with('employer')->availableForJobseekers();

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('qualifications', 'LIKE', "%{$search}%")
                    ->orWhereHas('employer', function ($empQ) use ($search) {
                        $empQ->where('company_name', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($location = $request->input('location')) {
            $query->where(function ($q) use ($location) {
                $q->where('description', 'LIKE', "%{$location}%")
                    ->orWhere('qualifications', 'LIKE', "%{$location}%");
            });
        }

        if ($request->boolean('pwd_only') || $request->input('filter') === 'pwd') {
            $query->where('accepts_disability', true);
        }

        $allJobs = $query->get();
        $rankedJobs = $this->matchingService->rankJobsForJobseeker($allJobs, $jobseeker);

        $sort = $request->input('sort', 'match');
        if ($sort === 'latest') {
            usort($rankedJobs, fn($a, $b) => $b['job']->job_id <=> $a['job']->job_id);
        }

        // 1. Get all applications
        $userApplications = JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->get();

        // 2. Filter using PHP logic (proven to work in Tinker)
        $appliedJobIds = $userApplications->filter(function ($app) {
            if (in_array($app->status, ['withdrawn', 'rejected'])) return false;
            if ($app->status === 'hired' && $app->resignation_status === 'approved') return false;
            return true;
        })->pluck('job_id')->toArray();
        $selectedJobId = $request->input('selected_job', !empty($rankedJobs) ? $rankedJobs[0]['job_id'] : null);
        $selectedItem = null;
        if ($selectedJobId) {
            foreach ($rankedJobs as $item) {
                if ((string)$item['job_id'] === (string)$selectedJobId) {
                    $selectedItem = $item;
                    break;
                }
            }
        }
        if (!$selectedItem && !empty($rankedJobs)) {
            $selectedItem = $rankedJobs[0];
        }

        $perPage = 6;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $rankedJobs = new LengthAwarePaginator(
            array_slice($rankedJobs, ($currentPage - 1) * $perPage, $perPage),
            count($rankedJobs),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $savedDocuments = $jobseeker->details && $jobseeker->details->training_certificates
            ? (is_array($jobseeker->details->training_certificates) ? $jobseeker->details->training_certificates : json_decode($jobseeker->details->training_certificates, true))
            : [];
        $hasSavedResume = $jobseeker->hasDocument('resume');

        return view('jobseeker.jobs.index', compact(
            'rankedJobs',
            'selectedItem',
            'userSkills',
            'appliedJobIds',
            'savedDocuments',
            'hasSavedResume',
            'jobseeker'
        ));
    }

    public function jobShow(int $id)
    {
        $job = JobPosting::with('employer')->findOrFail($id);

        if ($job->status !== 'approved' || $job->isExpired()) {
            return redirect()->route('jobseeker.jobs')
                ->with('error', 'This job posting has expired or is no longer available.');
        }

        $jobseeker = $this->getOrCreateJobseeker();
        $hasSavedResume = $jobseeker->hasDocument('resume');
        $userSkills = $this->matchingService->getJobseekerSkills($jobseeker);
        $jobSkills = $this->matchingService->getJobSkills($job);
        $match = $this->matchingService->calculateMatch($userSkills, $jobSkills);

        // THE FIX: Check the application and ignore if it's a resigned hire
        $app = JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)
            ->where('job_id', $job->job_id)
            ->first();

        $hasApplied = false;
        if ($app) {
            if ($app->status === 'hired') {
                if ($app->resignation_status !== 'approved') {
                    $hasApplied = true;
                }
            } elseif (!in_array($app->status, ['withdrawn', 'rejected'])) {
                $hasApplied = true;
            }
        }

        $recommendedTrainings = [];
        if (!empty($match['missingSkills'])) {
            $recommendedTrainings = TrainingProgram::where(function ($q) use ($match) {
                foreach ($match['missingSkills'] as $missing) {
                    $q->orWhere('title', 'LIKE', "%{$missing}%")
                        ->orWhere('description', 'LIKE', "%{$missing}%");
                }
            })->take(2)->get();
        }

        return view('jobseeker.jobs.show', compact(
            'job',
            'jobSkills',
            'userSkills',
            'match',
            'hasApplied',
            'recommendedTrainings',
            'jobseeker',
            'hasSavedResume'
        ));
    }

    // =========================================================================
    // 3. APPLICATIONS
    // =========================================================================

    /**
     * Submit a job application.
     *
     * NOTE: Employment status is intentionally NOT checked here.
     * Jobseekers may apply to unlimited jobs even while currently employed.
     * The only restriction is that they cannot ACCEPT a new offer while
     * already employed (see acceptOffer()).
     */
    public function apply(Request $request, int $jobId)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $job = JobPosting::findOrFail($jobId);

        // Check if the OCR verification passed (valid_id is present and not rejected)
        if (!$jobseeker->isProfileAndDocumentsComplete()) {
            return redirect()->route('jobseeker.documents')
                ->with('error', 'You must upload a valid, verified Government ID before you can apply for jobs.');
        }

        if ($job->status !== 'approved' || $job->isExpired()) {
            return redirect()->route('jobseeker.jobs')
                ->with('error', 'This job posting has expired or is no longer accepting applications.');
        }

        // Employment Flexibility: Jobseekers remain "Open to all" for opportunities even if currently employed.

        // Read fresh from DB — session-cached user can be stale after OCR auto-verify
        $user = \App\Models\User::find(Auth::id());
        if (!$user || !$user->isAccountVerified()) {
            return redirect()->route('jobseeker.documents')
                ->with('error', 'Your account is unverified. Please upload a valid Government ID in your Document Hub so it can be verified automatically before applying for jobs.');
        }

        // Check if the user has an ACTIVE application for this job
        $existing = JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)
            ->where('job_id', $jobId)
            ->whereNotIn('status', ['withdrawn', 'rejected'])
            ->where(function ($query) {
                $query->where('status', '!=', 'hired')
                    ->orWhere(function ($q) {
                        $q->where('status', 'hired')
                            ->where(function ($sq) {
                                $sq->whereNull('resignation_status')
                                    ->orWhere('resignation_status', '!=', 'approved');
                            });
                    });
            })
            ->first();

        if ($existing) {
            return redirect()->back()->with('warning', 'You already have an active application for this position.');
        }

        $resumeRules = ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'];
        if ($job->resume_required && !$jobseeker->hasDocument('resume')) {
            $resumeRules[0] = 'required';
        }
        $request->validate(['resume' => $resumeRules]);

        // Handle resume file upload if provided
        $resumeUrl = null;
        if ($request->hasFile('resume')) {
            $path = $request->file('resume')->store('resumes', 'public');
            $resumeUrl = Storage::url($path);

            $details = $jobseeker->details;
            if (!$details) {
                $details = JobseekerDetail::create([
                    'jobseeker_id' => $jobseeker->jobseeker_id,
                    'training_certificates' => [],
                ]);
            }

            $existingDocs = is_array($details->training_certificates)
                ? $details->training_certificates
                : (json_decode($details->training_certificates ?? '[]', true) ?: []);

            $filtered = array_filter($existingDocs, fn($d) => ($d['category'] ?? '') !== 'resume');
            $filtered[] = [
                'id' => 'doc_' . uniqid(),
                'category' => 'resume',
                'name' => $request->file('resume')->getClientOriginalName(),
                'file_url' => $resumeUrl,
                'status' => 'under_review',
                'uploaded_at' => now()->toIso8601String(),
            ];
            $details->training_certificates = array_values($filtered);
            $details->save();
            $jobseeker->refresh();
        }

        // Profile and documents must be complete before applying
        $missingRequirements = $jobseeker->getMissingRequirements();
        if (!empty($missingRequirements)) {
            $missingList = implode(', ', $missingRequirements);
            return redirect()->back()
                ->with('error', "Application blocked: You cannot apply for a job posting until your profile details and required documents are completed. Missing: {$missingList}. Please update your profile and upload your documents in the Document Hub.");
        }

        $application = JobApplication::create([
            'job_id' => $jobId,
            'jobseeker_id' => $jobseeker->jobseeker_id,
            'status' => 'jpo_pending',   // ← was 'pending'
            'referred_by_jpo' => false,
        ]);

        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'Application Submitted',
            'message' => "Your application for {$job->title} at " . ($job->employer->company_name ?? 'Partner Employer') . " has been submitted and is pending DMDP evaluation.",
            'type' => 'approval',
            'is_read' => false,
            'related_id' => $application->application_id,
        ]);

        // Notify all JPOs and Admins — either can evaluate
        $reviewers = \App\Models\User::whereIn('role', ['jpo', 'admin'])->get();
        foreach ($reviewers as $reviewer) {
            Notification::create([
                'user_id' => $reviewer->user_id,
                'title' => 'New Application Awaiting Evaluation',
                'message' => "{$jobseeker->first_name} {$jobseeker->last_name} applied for '{$job->title}' at " . ($job->employer->company_name ?? 'Partner Employer') . ". Pending JPO/Admin review.",
                'type' => 'manual_review',
                'is_read' => false,
                'related_id' => $application->application_id,
            ]);
        }

        return redirect()->route('jobseeker.applications')
            ->with('success', "Your application for '{$job->title}' has been submitted successfully!");
    }

    public function applications(Request $request)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $userSkills = $this->matchingService->getJobseekerSkills($jobseeker);

        $filter = $request->input('status', 'all');

        $query = JobApplication::with(['jobPosting.employer', 'jobPosting'])
            ->where('jobseeker_id', $jobseeker->jobseeker_id);

        // Handle the filter logic for the tabs
        if ($filter !== 'all') {
            if ($filter === 'pending') {
                // Group JPO Pending, Training, and Employer Pending
                $query->whereIn('status', ['jpo_pending', 'pending', 'reviewed', 'training_recommended']);
            } elseif ($filter === 'hired') {
                // Only show ACTIVE hires (where resignation is not approved)
                $query->where('status', 'hired')
                    ->where(function ($q) {
                        $q->whereNull('resignation_status')
                            ->orWhere('resignation_status', '!=', 'approved');
                    });
            } elseif ($filter === 'rejected') {
                // Group Rejected
                $query->where('status', 'rejected');
            } else {
                $query->where('status', $filter);
            }
        }

        // Paginate the applications and map the match data
        $applications = $query->orderByDesc('application_id')->paginate(6)->through(function ($app) use ($userSkills) {
            $jobSkills = $app->jobPosting ? $this->matchingService->getJobSkills($app->jobPosting) : [];
            $match = $this->matchingService->calculateMatch($userSkills, $jobSkills);
            $app->match_details = $match;
            return $app;
        });

        // THE FIX: Accurate Counts
        $allApps = JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->get();

        $counts = [
            'all' => $allApps->count(),
            'offered' => $allApps->where('status', 'offered')->count(),

            // Pending DMDP review (JPO hasn't acted yet)
            'jpo_pending' => $allApps->where('status', 'jpo_pending')->count(),

            // Endorsed by JPO — waiting for employer (status pending or reviewed AFTER referral)
            'pending' => $allApps->filter(function ($a) {
                return $a->referred_by_jpo == 1 && in_array($a->status, ['pending', 'reviewed']);
            })->count(),

            'interview' => $allApps->where('status', 'interview')->count(),
            'hired' => $allApps->filter(fn($app) => $app->status === 'hired' && $app->resignation_status !== 'approved')->count(),
            'rejected' => $allApps->where('status', 'rejected')->count(),
        ];

        return view('jobseeker.applications.index', compact('applications', 'counts', 'filter', 'jobseeker'));
    }

    /**
     * Accept a job offer.
     *
     * This is the ONLY place employment status is enforced. A jobseeker
     * cannot accept a new offer while already employed — they must first
     * resign from their current employer and have it approved.
     */
    public function acceptOffer(int $id)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $application = JobApplication::with(['jobPosting.employer.user', 'jobseeker'])
            ->where('jobseeker_id', $jobseeker->jobseeker_id)
            ->findOrFail($id);

        if ($application->status !== 'offered') {
            return redirect()->back()->with('error', 'This application does not have an active job offer to accept.');
        }

        // The only employment restriction in the whole flow.
        if ($jobseeker->isEmployed()) {
            $company = $jobseeker->hired_company ?: 'your current employer';
            return redirect()->back()->with('error', "You are already employed at {$company}. You must resign from your current company before accepting another job offer.");
        }

        $jobTitle = $application->jobPosting?->title ?? 'Position';
        $employer = $application->jobPosting?->employer;
        $companyName = $employer?->company_name ?? 'Company';

        $application->update([
            'status' => 'hired',
            'hired_date' => now()->toDateString(),
        ]);

        $jobseeker->update([
            'employment_status' => 'Employed',
            'hired_company' => $companyName,
        ]);

        if ($employer?->user) {
            Notification::create([
                'user_id' => $employer->user->user_id,
                'title' => 'Job Offer Accepted!',
                'message' => "Candidate {$jobseeker->first_name} {$jobseeker->last_name} has accepted your job offer for the position '{$jobTitle}'.",
                'type' => 'approval',
                'is_read' => false,
                'related_id' => $application->application_id,
            ]);
        }

        // 4. In accordance with Region 7 Employment Flexibility & Status Privacy rules,
        // jobseekers remain "Open to all" for opportunities even if currently employed.
        // We do not broadcast or force-withdraw their other applications.

        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'Congratulations on Your New Job!',
            'message' => "You have officially accepted the job offer for '{$jobTitle}' at {$companyName}. Your employment status is now active!",
            'type' => 'approval',
            'is_read' => false,
            'related_id' => $application->application_id,
        ]);

        return redirect()->route('jobseeker.applications')->with('success', "Congratulations! You have accepted the job offer from {$companyName}. You are now officially hired!");
    }

    public function declineOffer(Request $request, int $id)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $application = JobApplication::with(['jobPosting.employer.user', 'jobseeker'])
            ->where('jobseeker_id', $jobseeker->jobseeker_id)
            ->findOrFail($id);

        if ($application->status !== 'offered') {
            return redirect()->back()->with('error', 'This application does not have an active job offer to decline.');
        }

        $request->validate([
            'decline_reason' => 'nullable|string|max:1000',
        ]);

        $reason = $request->input('decline_reason') ?: 'Candidate declined the offer.';
        $jobTitle = $application->jobPosting?->title ?? 'Position';
        $employer = $application->jobPosting?->employer;

        $application->update([
            'status' => 'declined',
            'declined_at' => now(),
            'decline_reason' => $reason,
        ]);

        if ($employer?->user) {
            Notification::create([
                'user_id' => $employer->user->user_id,
                'title' => 'Job Offer Declined',
                'message' => "Candidate {$jobseeker->first_name} {$jobseeker->last_name} has declined the job offer for '{$jobTitle}'. Reason: {$reason}",
                'type' => 'manual_review',
                'is_read' => false,
                'related_id' => $application->application_id,
            ]);
        }

        return redirect()->route('jobseeker.applications')->with('info', "You have declined the job offer for '{$jobTitle}'. Your other applications remain active.");
    }

    /**
     * Withdraw an application — HARD DELETE.
     *
     * Deletes dependent jpo_assessments rows first to satisfy the FK, then
     * hard-deletes the application so it disappears from the employer's queue.
     * Re-applying to the same company/position is possible afterward.
     */
    public function withdrawApplication(int $id)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $app = JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->findOrFail($id);

        if (in_array($app->status, ['hired', 'declined', 'withdrawn'], true)) {
            return redirect()->back()->with('error', 'This application can no longer be withdrawn.');
        }

        $jobTitle = $app->jobPosting?->title ?? 'this position';

        try {
            DB::transaction(function () use ($app) {
                DB::table('jpo_assessments')->where('application_id', $app->application_id)->delete();
                $app->delete();
            });
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->with(
                'error',
                'This application could not be withdrawn because it has linked records. Please contact DMDP support.'
            );
        }

        return redirect()->back()->with('info', "Your application for '{$jobTitle}' has been withdrawn.");
    }

    public function requestResignation(Request $request)
    {
        $jobseeker = $this->getOrCreateJobseeker();

        $hiredApplication = $jobseeker->activeHiredApplication;
        if (!$hiredApplication) {
            $hiredApplication = $jobseeker->applications()
                ->where('status', 'hired')
                ->latest('application_id')
                ->first();
        }

        if (!$hiredApplication) {
            return redirect()->back()->withErrors(['error' => 'You do not have an active employment record to resign from.']);
        }

        $request->validate([
            'reason' => 'required|string|max:1000',
        ], [
            'reason.required' => 'Please provide a reason for requesting resignation.',
        ]);

        $hiredApplication->update([
            'resignation_status' => 'requested',
            'resignation_reason' => $request->input('reason'),
            'resignation_requested_at' => now(),
        ]);

        $employerUser = $hiredApplication->jobPosting?->employer?->user;
        if ($employerUser) {
            Notification::create([
                'user_id' => $employerUser->user_id,
                'title' => 'Resignation Request Submitted',
                'message' => "Jobseeker {$jobseeker->first_name} {$jobseeker->last_name} has requested resignation from '{$hiredApplication->jobPosting->title}'. Reason: " . $request->input('reason'),
                'type' => 'manual_review',
                'is_read' => false,
                'related_id' => $hiredApplication->application_id,
            ]);
        }

        return redirect()->back()->with('success', 'Your resignation request has been sent to your employer for approval.');
    }

    // =========================================================================
    // 4. TRAINING
    // =========================================================================

    public function trainingIndex(Request $request)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $userSkills = $this->matchingService->getJobseekerSkills($jobseeker);
        $filter = $request->input('filter', 'all');

        $allTrainings = TrainingProgram::with(['topics', 'enrollments' => function ($q) use ($jobseeker) {
            $q->where('jobseeker_id', $jobseeker->jobseeker_id);
        }])
            ->where(function ($q) use ($jobseeker) {
                // Show only non-archived courses, OR archived courses that the jobseeker is already enrolled in
                $q->where('is_archived', false)
                    ->orWhereHas('enrollments', function ($sub) use ($jobseeker) {
                        $sub->where('jobseeker_id', $jobseeker->jobseeker_id);
                    });
            })->get();
        $accountCanEnroll = $jobseeker->isProfileAndDocumentsComplete()
            && Auth::user()
            && Auth::user()->isAccountVerified();
        $allTrainings->each(function ($training) use ($jobseeker, $accountCanEnroll) {
            $training->eligibility_issues = $training->demographicEligibilityIssues($jobseeker);
            $training->account_can_enroll = $accountCanEnroll;
            $training->can_enroll = $accountCanEnroll && empty($training->eligibility_issues);
        });

        $enrolledCount = $allTrainings->filter(function ($t) {
            $enr = $t->enrollments->first();
            return $enr && in_array($enr->status, ['enrolled', 'in_progress', 'completed', 'failed']);
        })->count();

        $completedCount = $allTrainings->filter(function ($t) {
            $enr = $t->enrollments->first();
            return $enr && $enr->status === 'completed';
        })->count();

        $counts = [
            'all' => $allTrainings->count(),
            'enrolled' => $enrolledCount,
            'completed' => $completedCount,
        ];

        if ($filter === 'enrolled') {
            $trainings = $allTrainings->filter(function ($t) {
                $enr = $t->enrollments->first();
                return $enr && in_array($enr->status, ['enrolled', 'in_progress', 'completed', 'failed']);
            });
        } elseif ($filter === 'completed') {
            $trainings = $allTrainings->filter(function ($t) {
                $enr = $t->enrollments->first();
                return $enr && $enr->status === 'completed';
            });
        } else {
            $trainings = $allTrainings;
        }

        $allTrainingSkills = [];
        foreach ($allTrainings as $t) {
            $words = array_filter(explode(' ', $t->title), fn($w) => strlen($w) > 3);
            $allTrainingSkills[$t->training_id] = array_values($words);
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $trainings = new LengthAwarePaginator(
            $trainings->forPage($currentPage, 6)->values(),
            $trainings->count(),
            6,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('jobseeker.training.index', compact('trainings', 'userSkills', 'allTrainingSkills', 'jobseeker', 'filter', 'counts'));
    }

    public function trainingSkills(Request $request)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $userSkills = $this->matchingService->getJobseekerSkills($jobseeker);
        $userSkillNames = array_map('strtolower', $userSkills);
        $accountCanEnroll = $jobseeker->isProfileAndDocumentsComplete()
            && Auth::user()
            && Auth::user()->isAccountVerified();

        $trainings = TrainingProgram::with(['trainer', 'topics', 'enrollments' => function ($q) use ($jobseeker) {
            $q->where('jobseeker_id', $jobseeker->jobseeker_id);
        }])->get();

        $skillsCatalog = [];
        foreach ($trainings as $training) {
            $enrollment = $training->enrollments->first();
            $status = $enrollment ? $enrollment->status : 'unregistered';
            $certIssued = $enrollment ? (bool)$enrollment->certificate_issued : false;
            $eligibilityIssues = $training->demographicEligibilityIssues($jobseeker);

            $skillsList = [];
            if (!empty($training->skills)) {
                $skillsList = array_map('trim', explode(',', $training->skills));
            } else {
                $skillsList = [trim($training->title)];
            }

            foreach ($skillsList as $sName) {
                if (empty($sName)) continue;
                $isEarned = in_array(strtolower($sName), $userSkillNames) || ($status === 'completed');
                $skillsCatalog[] = [
                    'skill_name' => $sName,
                    'course_id' => $training->training_id,
                    'course_title' => $training->title,
                    'course_type' => $training->training_type,
                    'duration_months' => $training->duration_months,
                    'topics_count' => $training->topics->count(),
                    'passing_score' => $training->passing_score ?: 80,
                    'status' => $status,
                    'is_earned' => $isEarned,
                    'certificate_issued' => $certIssued,
                    'enrollment_id' => $enrollment ? $enrollment->enrollment_id : null,
                    'eligibility_issues' => $eligibilityIssues,
                    'account_can_enroll' => $accountCanEnroll,
                    'can_enroll' => $accountCanEnroll && empty($eligibilityIssues),
                ];
            }
        }

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $skillsCatalog = array_filter($skillsCatalog, function ($item) use ($search) {
                return str_contains(strtolower($item['skill_name']), $search)
                    || str_contains(strtolower($item['course_title']), $search);
            });
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $skillsCatalog = new LengthAwarePaginator(
            array_slice($skillsCatalog, ($currentPage - 1) * 6, 6),
            count($skillsCatalog),
            6,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('jobseeker.training.skills', compact('skillsCatalog', 'userSkills', 'jobseeker'));
    }

    public function trainingEnrollments(Request $request)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $userSkills = $this->matchingService->getJobseekerSkills($jobseeker);

        $allEnrollments = TrainingEnrollment::with(['trainingProgram.topics', 'trainingProgram.trainer'])
            ->where('jobseeker_id', $jobseeker->jobseeker_id)
            ->orderBy('enrollment_id', 'desc')
            ->get();

        $stats = [
            'total' => $allEnrollments->count(),
            'in_progress' => $allEnrollments->whereIn('status', ['enrolled', 'in_progress', 'failed'])->count(),
            'completed' => $allEnrollments->where('status', 'completed')->count(),
            'certificates' => $allEnrollments->where('certificate_issued', 1)->count(),
        ];

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $enrollments = new LengthAwarePaginator(
            $allEnrollments->forPage($currentPage, 6)->values(),
            $allEnrollments->count(),
            6,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('jobseeker.training.enrollments', compact('enrollments', 'jobseeker', 'userSkills', 'stats'));
    }

    public function enrollTraining(int $id)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $training = TrainingProgram::findOrFail($id);
        if ($training->is_archived) {
            return redirect()->route('jobseeker.training')
                ->with('error', 'This training program is no longer accepting new enrollments.');
        }

        $enrollment = TrainingEnrollment::where('jobseeker_id', $jobseeker->jobseeker_id)
            ->where('training_id', $id)
            ->first();

        if ($enrollment) {
            return redirect()->route('jobseeker.training.show', $id)
                ->with('info', "You are already enrolled in '{$training->title}'.");
        }

        if (!$jobseeker->isProfileAndDocumentsComplete()) {
            return redirect()->route('jobseeker.documents')
                ->with('error', 'You must complete your profile and upload a valid Government ID before you can apply for training.');
        }

        $user = \App\Models\User::find(Auth::id());
        if (!$user || !$user->isAccountVerified()) {
            return redirect()->route('jobseeker.documents')
                ->with('error', 'Your account is unverified. Please upload a valid Government ID in your Document Hub and complete account verification before applying for training.');
        }

        $eligibilityIssues = $training->demographicEligibilityIssues($jobseeker);
        if ($eligibilityIssues) {
            return redirect()->route('jobseeker.training.show', $id)
                ->with('error', implode(' ', $eligibilityIssues));
        }

        if ($training->trainee_capacity !== null && TrainingEnrollment::where('training_id', $id)->count() >= $training->trainee_capacity) {
            return redirect()->route('jobseeker.training.show', $id)
                ->with('error', 'This training program has reached its trainee capacity.');
        }

        TrainingEnrollment::create([
            'jobseeker_id' => $jobseeker->jobseeker_id,
            'training_id' => $id,
            'training_type' => $training->training_type ?: 'online',
            'status' => 'enrolled',
            'start_date' => now()->toDateString(),
        ]);

        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'Enrolled in Training Course',
            'message' => "You have successfully enrolled in '{$training->title}'. Start learning to complete modules and earn your certification!",
            'type' => 'training',
            'is_read' => false,
            'related_id' => $training->training_id,
        ]);

        return redirect()->route('jobseeker.training.show', $id)
            ->with('success', "Successfully enrolled in '{$training->title}'! You can now start the learning modules and assessment quiz.");
    }

    public function trainingShow(int $id)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $training = TrainingProgram::with('topics')->findOrFail($id);
        $hasAssessmentQuestions = count($this->quizService->getQuestionsForTraining($training)) > 0;
        $enrollment = TrainingEnrollment::where('jobseeker_id', $jobseeker->jobseeker_id)
            ->where('training_id', $id)
            ->first();
        $assessmentData = $enrollment?->answers ?? [];
        if (!is_array($assessmentData)) {
            $assessmentData = json_decode($assessmentData, true) ?: [];
        }
        $assessmentReviewStatus = $assessmentData['review_status'] ?? null;

        return view('jobseeker.training.show', compact(
            'training',
            'enrollment',
            'jobseeker',
            'hasAssessmentQuestions',
            'assessmentReviewStatus'
        ));
    }

    public function trainingQuiz(int $id)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $training = TrainingProgram::with('topics')->findOrFail($id);
        $enrollment = TrainingEnrollment::where('jobseeker_id', $jobseeker->jobseeker_id)
            ->where('training_id', $id)
            ->first();
        $assessmentData = $enrollment?->answers ?? [];
        if (!is_array($assessmentData)) {
            $assessmentData = json_decode($assessmentData, true) ?: [];
        }
        if (($assessmentData['review_status'] ?? null) === 'pending') {
            return redirect()->route('jobseeker.training.show', $id)
                ->with('info', 'Your assessment is pending trainer review.');
        }
        if (($assessmentData['review_status'] ?? null) === 'passed') {
            return redirect()->route('jobseeker.training.show', $id)
                ->with('info', 'Your assessment has been approved.');
        }

        $questions = array_map(function ($question) {
            unset($question['answer'], $question['explanation']);
            return $question;
        }, $this->quizService->getQuestionsForTraining($training));

        return view('jobseeker.training.quiz', compact('training', 'jobseeker', 'questions'));
    }

    public function submitQuiz(Request $request, int $id)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $training = TrainingProgram::with('trainer')->findOrFail($id);
        $questions = $this->quizService->getQuestionsForTraining($training);
        if (empty($questions)) {
            return back()->with('error', 'This course does not have assessment questions yet. Please wait for the trainer to add them.');
        }

        $request->validate(['responses' => 'required|json']);
        $submittedResponses = json_decode($request->input('responses'), true);
        if (!is_array($submittedResponses)) {
            return back()->withErrors(['responses' => 'Your answers could not be read. Please try again.']);
        }

        foreach ($questions as $question) {
            $key = (string) $question['id'];
            if (!array_key_exists($key, $submittedResponses)) {
                return back()->withErrors(['responses' => 'Please answer every assessment question before submitting.']);
            }

            $response = $submittedResponses[$key];
            if ($question['type'] === 'essay') {
                if (!is_string($response) || trim($response) === '' || mb_strlen($response) > 5000) {
                    return back()->withErrors(['responses' => 'Essay answers are required and must be 5,000 characters or fewer.']);
                }
            } elseif (
                filter_var($response, FILTER_VALIDATE_INT) === false
                || !array_key_exists((int) $response, $question['choices'])
            ) {
                return back()->withErrors(['responses' => 'Please select a valid answer for each multiple-choice question.']);
            }
        }

        try {
            $gradedSubmission = $this->quizService->gradeSubmission($training, $submittedResponses);
        } catch (\InvalidArgumentException $exception) {
            return back()->withErrors(['responses' => $exception->getMessage()]);
        }

        $enrollment = TrainingEnrollment::where('jobseeker_id', $jobseeker->jobseeker_id)
            ->where('training_id', $id)
            ->first();

        if (!$enrollment && $training->trainee_capacity !== null && TrainingEnrollment::where('training_id', $id)->count() >= $training->trainee_capacity) {
            return redirect()->route('jobseeker.training.show', $id)
                ->with('error', 'This training program has reached its trainee capacity.');
        }

        $existingAnswers = $enrollment?->answers ?? [];
        if (!is_array($existingAnswers)) {
            $existingAnswers = json_decode($existingAnswers, true) ?: [];
        }
        if (in_array($existingAnswers['review_status'] ?? null, ['pending', 'passed'], true)) {
            return redirect()->route('jobseeker.training.show', $id)
                ->with('info', 'Your submitted assessment is already being reviewed or has been approved.');
        }

        if (!$enrollment) {
            $enrollment = new TrainingEnrollment([
                'jobseeker_id' => $jobseeker->jobseeker_id,
                'training_id' => $id,
                'training_type' => $training->training_type ?: 'online',
                'start_date' => now()->toDateString(),
            ]);
        }

        // 1. Check if this course has ANY essay questions
        $hasEssay = \App\Models\TrainingAssessment::where('training_id', $training->training_id)
            ->where('question_type', 'essay')
            ->exists();

        $score = $gradedSubmission['objective_score']['percentage'];
        $passingScore = $training->passing_score ?? 80;

        $reviewStatus = 'pending';
        $status = 'in_progress';
        $passed = null;
        $endDate = null;

        // 2. If NO essays, auto-grade and auto-complete
        if (!$hasEssay) {
            if ($score >= $passingScore) {
                $reviewStatus = 'passed';
                $status = 'completed';
                $passed = 1;
                $endDate = now()->toDateString();
            } else {
                $reviewStatus = 'needs_retake';
                $status = 'failed';
                $passed = 0;
                $endDate = now()->toDateString();
            }
        }

        $enrollment->score = $score;
        $enrollment->passed = $passed; // Will be null if there's an essay, waiting for trainer
        $enrollment->status = $status;
        $enrollment->end_date = $endDate;
        $enrollment->answers = [
            'review_status' => $reviewStatus,
            'submitted_at' => now()->toIso8601String(),
            'objective_score' => $gradedSubmission['objective_score'],
            'responses' => $gradedSubmission['responses'],
        ];
        $enrollment->save();

        // 3. If they passed and there are no essays, ISSUE THE CERTIFICATE IMMEDIATELY
        if ($status === 'completed' && $reviewStatus === 'passed') {

            // Call the TrainerPortalController's generateCertificate method programmatically
            $trainerController = new \App\Http\Controllers\Trainer\TrainerPortalController();

            // We need to pass the enrollment ID
            $trainerController->generateCertificate($request, $enrollment->enrollment_id);

            // Refresh the enrollment so the new certificate ID is caught
            $enrollment->refresh();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'review_status' => 'passed',
                    'objective_score' => $gradedSubmission['objective_score'],
                    'certificate_issued' => true,
                    'message' => 'Congratulations! You passed and your certificate has been issued.',
                ]);
            }
            return redirect()->route('jobseeker.training.show', $id)
                ->with('success', 'Congratulations! You passed the assessment and your certificate has been generated automatically.');
        }

        // 3. If they passed and there are no essays, ISSUE THE CERTIFICATE IMMEDIATELY
        if ($status === 'completed' && $reviewStatus === 'passed') {
            // We call the TrainerPortalController's generateCertificate method programmatically
            $trainerController = new \App\Http\Controllers\Trainer\TrainerPortalController();

            // We need to pass the enrollment ID
            $trainerController->generateCertificate($request, $enrollment->enrollment_id);

            // After generation, redirect with a special success message
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'review_status' => 'passed',
                    'objective_score' => $gradedSubmission['objective_score'],
                    'certificate_issued' => true,
                    'message' => 'Congratulations! You passed and your certificate has been issued.',
                ]);
            }
            return redirect()->route('jobseeker.training.show', $id)
                ->with('success', 'Congratulations! You passed the assessment and your certificate has been generated automatically.');
        }

        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'Assessment Submitted for Trainer Review',
            'message' => "Your assessment for '{$training->title}' is awaiting trainer review. Your certificate is pending.",
            'type' => 'training',
            'is_read' => false,
            'related_id' => $enrollment->enrollment_id,
        ]);
        if ($training->trainer?->user_id) {
            Notification::create([
                'user_id' => $training->trainer->user_id,
                'title' => 'Trainee Assessment Awaiting Review',
                'message' => "{$jobseeker->first_name} {$jobseeker->last_name} submitted the assessment for '{$training->title}'. Review objective answers and essay responses.",
                'type' => 'training',
                'is_read' => false,
                'related_id' => $enrollment->enrollment_id,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'review_status' => 'pending',
                'objective_score' => $gradedSubmission['objective_score'],
                'certificate_issued' => false,
                'message' => 'Assessment submitted. The trainer must review your answers before deciding the result.',
            ]);
        }

        return redirect()->route('jobseeker.training.show', $id);
    }

    public function previewCertificate(int $id)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $enrollment = DB::table('training_enrollments')
            ->join('jobseekers', 'training_enrollments.jobseeker_id', '=', 'jobseekers.jobseeker_id')
            ->join('training_programs', 'training_enrollments.training_id', '=', 'training_programs.training_id')
            ->where('training_enrollments.enrollment_id', $id)
            ->where('training_enrollments.jobseeker_id', $jobseeker->jobseeker_id)
            ->select(
                'training_enrollments.*',
                'jobseekers.first_name',
                'jobseekers.last_name',
                'training_programs.title as course_title',
                'training_programs.description as course_desc',
                'training_programs.cert_content' // <--- MAKE SURE THIS IS HERE
            )
            ->first();

        if (!$enrollment) {
            abort(404, 'Certificate record not found or not yet generated.');
        }

        return view('jobseeker.certificates.preview', compact('enrollment'));
    }
    // =========================================================================
    // 5. DOCUMENTS
    // =========================================================================

    public function documents()
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $details = $jobseeker->details;

        $documents = [
            'resume' => null,
            'valid_id' => null,
            'certificate' => null,
            'pwd_id' => null,
        ];

        $certificatesList = [];

        if ($details && !empty($details->training_certificates)) {
            $docs = is_array($details->training_certificates)
                ? $details->training_certificates
                : json_decode($details->training_certificates, true);

            if (is_array($docs)) {
                foreach ($docs as $doc) {
                    $rawCat = $doc['category'] ?? '';
                    $cat = ($rawCat === 'certs') ? 'certificate' : $rawCat;

                    if ($cat === 'certificate') {
                        $certificatesList[] = $doc;
                    }

                    if (array_key_exists($cat, $documents)) {
                        $documents[$cat] = $doc;
                    }
                }
            }
        }

        return view('jobseeker.documents.index', compact('documents', 'certificatesList', 'jobseeker', 'details'));
    }

    public function previewDocument(string $category)
    {
        abort_unless(in_array($category, ['resume', 'valid_id', 'certificate', 'certs', 'pwd_id'], true), 404);

        $jobseeker = $this->getOrCreateJobseeker();
        $details = $jobseeker->details;
        abort_unless($details && !empty($details->training_certificates), 404);

        $documents = is_array($details->training_certificates)
            ? $details->training_certificates
            : (json_decode($details->training_certificates, true) ?: []);
        $category = $category === 'certs' ? 'certificate' : $category;
        $document = null;

        foreach ($documents as $candidateDocument) {
            if (!is_array($candidateDocument)) {
                continue;
            }

            $documentCategory = ($candidateDocument['category'] ?? '') === 'certs'
                ? 'certificate'
                : ($candidateDocument['category'] ?? '');
            if ($documentCategory === $category) {
                $document = $candidateDocument;
            }
        }

        abort_unless($document, 404);

        if ($category === 'certificate' && !empty($document['enrollment_id'])) {
            return redirect()->route('jobseeker.certificates.preview', $document['enrollment_id']);
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
    private function runPaddleOcr(string $absolutePath): ?string
    {
        $pythonScript = base_path('app/scripts/ocr_engine.py');

        if (!file_exists($pythonScript) || !file_exists($absolutePath)) {
            Log::error("OCR failed: Script or image missing.", [
                'script_exists' => file_exists($pythonScript),
                'image_exists'  => file_exists($absolutePath),
                'image_path'    => $absolutePath,
            ]);
            return null;
        }

        try {
            // PHP-FPM on Windows does not pass USERPROFILE/HOME to subprocesses.
            // PaddleOCR's ModelScope dependency requires a home directory to
            // locate its cache, so we pass the env vars explicitly.
            $home = getenv('USERPROFILE') ?: ('C:\\Users\\' . getenv('USERNAME'));
            $env = [
                'USERPROFILE' => $home,
                'HOME'        => $home,
                'PATH'        => getenv('PATH'),
                'SYSTEMROOT'  => getenv('SYSTEMROOT') ?: 'C:\\Windows',
                'TEMP'        => getenv('TEMP') ?: 'C:\\Windows\\Temp',
                'TMP'         => getenv('TMP')  ?: 'C:\\Windows\\Temp',
            ];

            $process = new Process(['py', '-3.12', $pythonScript, $absolutePath], null, $env);
            $process->setTimeout(60);
            $process->run();

            if (!$process->isSuccessful()) {
                Log::warning('PaddleOCR exited non-zero: ' . $process->getErrorOutput());
                return null;
            }

            $output = trim($process->getOutput());
            return $output === '' ? null : $output;
        } catch (\Throwable $e) {
            Log::error('PaddleOCR exception: ' . $e->getMessage());
            return null;
        }
    }



    /**
     * Normalizes text for comparison (uppercase, letters only).
     */
    private function normalizeIdText(string $s): string
    {
        $s = strtoupper($s);
        $s = preg_replace('/[^A-Z\s]/', '', $s);
        $s = preg_replace('/\s+/', ' ', $s);
        return trim($s);
    }

    /**
     * Extracts and normalizes a date from raw OCR text.
     */
    /**
     * Extracts and normalizes a date from raw OCR text.
     */
    private function extractOcrDate(string $ocrText): ?string
    {
        $months = [
            'JANUARY' => '01',
            'JANUAR' => '01',
            'ANUARY' => '01',
            'FEBRUARY' => '02',
            'FEBRUAR' => '02',
            'EBRUARY' => '02',
            'MARCH' => '03',
            'MARC' => '03',
            'ARCH' => '03',
            'APRIL' => '04',
            'APRI' => '04',
            'PRIL' => '04',
            'MAY' => '05',
            'JUNE' => '06',
            'JUN' => '06',
            'UNE' => '06',
            'JULY' => '07',
            'JUL' => '07',
            'ULY' => '07',
            'AUGUST' => '08',
            'AUGUS' => '08',
            'UGUST' => '08',
            'SEPTEMBER' => '09',
            'SEPTEMBE' => '09',
            'EPTEMBER' => '09',
            'OCTOBER' => '10',
            'OCTOBE' => '10',
            'CTOBER' => '10',
            'NOVEMBER' => '11',
            'NOVEMBE' => '11',
            'OVEMBER' => '11',
            'DECEMBER' => '12',
            'DECEMBE' => '12',
            'ECEMBER' => '12',
        ];

        $upper = strtoupper($ocrText);

        // 1. Try full/truncated month names (For National IDs): "MARCH 07, 2003"
        $pattern = '/\b(' . implode('|', array_map(fn($m) => preg_quote($m, '/'), array_keys($months))) . ')\s*(\d{1,2})[,\s]*(\d{4})\b/';
        if (preg_match($pattern, $upper, $m)) {
            $mon = $months[$m[1]] ?? null;
            if ($mon) {
                return sprintf('%04d-%02d-%02d', (int)$m[3], (int)$mon, (int)$m[2]);
            }
        }

        // 2. Direct hit for Driver's License format: YYYY/MM/DD (e.g., 2004/09/29)
        // This searches the whole text for the first occurrence of this exact format
        if (preg_match('/(\d{4})[\/\-.](\d{1,2})[\/\-.](\d{1,2})/', $upper, $m)) {
            // Ensure it's a valid date (not a license number)
            if ((int)$m[1] > 1900 && (int)$m[1] < 2100 && (int)$m[2] >= 1 && (int)$m[2] <= 12 && (int)$m[3] >= 1 && (int)$m[3] <= 31) {
                return sprintf('%04d-%02d-%02d', (int)$m[1], (int)$m[2], (int)$m[3]);
            }
        }

        // 3. Look for DD/MM/YYYY or MM/DD/YYYY
        if (preg_match('/\b(\d{1,2})[\/\-.](\d{1,2})[\/\-.](\d{4})\b/', $upper, $m)) {
            // Assume MM/DD/YYYY if it's a standard ID
            if ((int)$m[3] > 1900 && (int)$m[3] < 2100) {
                return sprintf('%04d-%02d-%02d', (int)$m[3], (int)$m[1], (int)$m[2]);
            }
        }

        // 4. Last resort: any 4-digit year near "BIRTH"
        $lines = preg_split('/\r?\n/', $ocrText);
        foreach ($lines as $i => $line) {
            if (stripos($line, 'BIRTH') !== false || stripos($line, 'KAPANGANAKAN') !== false) {
                $combined = strtoupper($line . ' ' . ($lines[$i + 1] ?? ''));
                if (preg_match('/\b(\d{4})\b/', $combined, $m)) {
                    return sprintf('%04d-01-01', (int)$m[1]);
                }
            }
        }

        return null;
    }
    /**
     * Checks if the OCR text matches the jobseeker's registered Name AND Date of Birth.
     */
    private function idMatchesJobseeker(Jobseeker $jobseeker, string $ocrText): array
    {
        $registeredName = trim(implode(' ', array_filter([
            $jobseeker->first_name,
            $jobseeker->middle_name,
            $jobseeker->last_name,
        ])));

        $registeredDob = $jobseeker->birth_date ? \Carbon\Carbon::parse($jobseeker->birth_date)->format('Y-m-d') : null;

        if ($registeredName === '' || trim($ocrText) === '') {
            return ['match' => false, 'reason' => 'Could not read the ID clearly.'];
        }

        // 1. Check Name
        $normRegistered = $this->normalizeIdText($registeredName);
        $normOcr = $this->normalizeIdText($ocrText);

        $nameMatch = true;
        foreach (explode(' ', $normRegistered) as $word) {
            if ($word !== '' && strpos($normOcr, $word) === false) {
                $nameMatch = false;
                break;
            }
        }

        if (!$nameMatch) {
            return ['match' => false, 'reason' => 'The name on this ID does not match your registered profile name.'];
        }

        // 2. Check DOB (if the jobseeker has a DOB set)
        if ($registeredDob) {
            $ocrDob = $this->extractOcrDate($ocrText);

            if (!$ocrDob) {
                return ['match' => false, 'reason' => 'We could not read the date of birth on this ID. Please upload a clearer photo.'];
            }

            if ($ocrDob !== $registeredDob) {
                return ['match' => false, 'reason' => 'The date of birth on this ID does not match your registered profile.'];
            }
        }

        return ['match' => true, 'reason' => null];
    }
    public function uploadDocument(Request $request)
    {
        set_time_limit(120);

        $request->validate([
            'category' => 'required|in:resume,valid_id,certificate,certs,pwd_id',
            'document_file' => 'required|file|max:5120',
        ]);

        $category = $request->input('category');
        $canonicalCategory = ($category === 'certs') ? 'certificate' : $category;
        $file = $request->file('document_file');

        $allowedExtensions = match ($canonicalCategory) {
            'resume' => ['pdf', 'doc', 'docx'],
            'valid_id', 'pwd_id' => ['jpg', 'jpeg', 'png', 'pdf'],
            'certificate' => ['pdf', 'jpg', 'jpeg', 'png'],
            default => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'],
        };

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedExtensions)) {
            $allowedList = strtoupper(implode(', ', $allowedExtensions));
            return redirect()->back()
                ->withErrors(['document_file' => "Invalid file format for " . ucfirst(str_replace('_', ' ', $canonicalCategory)) . ". Allowed formats: {$allowedList}."])
                ->withInput();
        }

        if (in_array($canonicalCategory, ['valid_id', 'pwd_id']) && in_array($extension, ['jpg', 'jpeg', 'png'])) {
            $imageInfo = @getimagesize($file->getRealPath());
            if ($imageInfo) {
                [$width, $height] = $imageInfo;
                if ($width < 300 || $height < 200) {
                    return redirect()->back()
                        ->withErrors(['document_file' => 'The uploaded ID image is too low-resolution. Please upload a clear photo (at least 300x200 pixels) to ensure legibility.'])
                        ->withInput();
                }
            }
        }

        $jobseeker = $this->getOrCreateJobseeker();
        $details = $jobseeker->details;

        if (!$details) {
            $details = JobseekerDetail::create([
                'jobseeker_id' => $jobseeker->jobseeker_id,
                'training_certificates' => [],
            ]);
        }

        $cleanName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $fileName = time() . '_' . $cleanName . '.' . $extension;
        $filePath = $file->storeAs('documents/' . $jobseeker->jobseeker_id, $fileName, 'public');
        $fileUrl = Storage::url($filePath);

        $existingDocs = is_array($details->training_certificates)
            ? $details->training_certificates
            : (json_decode($details->training_certificates ?? '[]', true) ?: []);

        $filtered = array_filter($existingDocs, function ($d) use ($canonicalCategory) {
            $cat = ($d['category'] ?? '') === 'certs' ? 'certificate' : ($d['category'] ?? '');
            if ($cat !== $canonicalCategory) {
                return true;
            }
            if ($canonicalCategory === 'certificate' && !empty($d['enrollment_id'])) {
                return true;
            }
            return false;
        });

        // ---------------------------------------------------------
        // AUTO-VERIFICATION LOGIC (Name + DOB)
        // ---------------------------------------------------------
        $status = 'under_review';
        $rejectionReason = null;

        if ($canonicalCategory === 'valid_id' && in_array($extension, ['jpg', 'jpeg', 'png'])) {
            $absolutePath = Storage::disk('public')->path($filePath);
            $ocrText = $this->runPaddleOcr($absolutePath);

            if ($ocrText === null) {
                $status = 'rejected';
                $rejectionReason = 'We could not read your ID. Please upload a clearer, well-lit photo.';
            } else {
                $verification = $this->idMatchesJobseeker($jobseeker, $ocrText);

                if ($verification['match']) {
                    $status = 'verified'; // 🎉 Auto-verified!
                } else {
                    $status = 'rejected';
                    $rejectionReason = $verification['reason'];
                }
            }
        }

        $newDoc = [
            'id' => 'doc_' . uniqid(),
            'category' => $canonicalCategory,
            'name' => $file->getClientOriginalName(),
            'file_url' => $fileUrl,
            'url' => $fileUrl,
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'status' => $status,
            'rejection_reason' => $rejectionReason,
            'reviewed_by' => $status === 'verified' ? 'auto_ocr' : null,
            'reviewed_at' => $status === 'verified' ? now()->toIso8601String() : null,
            'uploaded_at' => now()->toIso8601String(),
        ];

        $filtered[] = $newDoc;
        $details->training_certificates = array_values($filtered);
        $details->save();

        // ---------------------------------------------------------
        // UPDATE USER ACCOUNT STATUS
        // ---------------------------------------------------------
        $user = Auth::user();
        if ($user && $canonicalCategory === 'valid_id' && $status === 'verified') {
            DB::table('users')->where('user_id', $user->user_id)->update([
                'account_status' => 'verified',
                'is_approved'    => true,
                'updated_at'     => now(),
            ]);

            // ✅ Reload the user into the current session so is_approved = true is seen
            //    on the very next page load (Jobs page, Apply button, etc.)
            Auth::setUser($user->fresh());
        }

        // ---------------------------------------------------------
        // NOTIFICATION & REDIRECT
        // ---------------------------------------------------------
        $notifyTitle = match (true) {
            $status === 'verified' => 'ID Automatically Verified',
            $status === 'rejected' => 'ID Could Not Be Verified',
            default                => 'Document Uploaded',
        };

        $notifyMessage = match (true) {
            $status === 'verified' => 'Your Government ID matched your registered name and date of birth. Your account is now verified!',
            $status === 'rejected' => $rejectionReason,
            default                => ucfirst(str_replace('_', ' ', $canonicalCategory)) . ' uploaded and is under review.',
        };

        Notification::create([
            'user_id' => Auth::id(),
            'title'   => $notifyTitle,
            'message' => $notifyMessage,
            'type'    => $status === 'rejected' ? 'rejection' : 'approval',
            'is_read' => false,
        ]);

        $flashType = $status === 'rejected' ? 'error' : 'success';

        return redirect()->route('jobseeker.documents')->with($flashType, $notifyMessage);
    }

    public function deleteDocument(Request $request, string $category)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $details = $jobseeker->details;

        $canonicalCategory = ($category === 'certs') ? 'certificate' : $category;
        $docId = $request->input('doc_id');

        if ($details && !empty($details->training_certificates)) {
            $existingDocs = is_array($details->training_certificates)
                ? $details->training_certificates
                : (json_decode($details->training_certificates ?? '[]', true) ?: []);

            $filtered = array_filter($existingDocs, function ($d) use ($canonicalCategory, $docId) {
                if ($docId && isset($d['id']) && $d['id'] === $docId) {
                    return false;
                }
                $cat = ($d['category'] ?? '') === 'certs' ? 'certificate' : ($d['category'] ?? '');
                if (!$docId && $cat === $canonicalCategory) {
                    return false;
                }
                return true;
            });

            $details->training_certificates = array_values($filtered);
            $details->save();
        }

        return redirect()->route('jobseeker.documents')->with('info', 'Document removed.');
    }

    // =========================================================================
    // 6. NOTIFICATIONS
    // =========================================================================

    public function notifications(Request $request)
    {
        $user = Auth::user();
        $filter = $request->input('category', 'all');

        $query = Notification::where('user_id', $user->user_id);

        if ($filter !== 'all') {
            if ($filter === 'application') {
                $query->whereIn('type', ['application', 'approval', 'rejection', 'manual_review']);
            } else {
                $query->where('type', $filter);
            }
        }

        $notifications = $query->orderByDesc('created_at')->paginate(6)->appends(['category' => $filter]);
        $unreadCount = Notification::where('user_id', $user->user_id)->where('is_read', false)->count();

        return view('jobseeker.notifications.index', compact('notifications', 'unreadCount', 'filter'));
    }

    public function markNotificationRead(int $id)
    {
        $user = Auth::user();
        Notification::where('user_id', $user->user_id)
            ->where('notification_id', $id)
            ->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Marked as read.');
    }

    public function markAllNotificationsRead()
    {
        $user = Auth::user();
        Notification::where('user_id', $user->user_id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    // =========================================================================
    // 7. PROFILE & SKILLS
    // =========================================================================

    public function profile()
    {
        $user = Auth::user();
        $jobseeker = $this->getOrCreateJobseeker();
        $skills = $jobseeker->skills ? $jobseeker->skills->pluck('skill_name')->filter(function ($s) {
            return !empty($s) && $s !== '[object Object]' && $s !== 'object Object' && !preg_match('/\.(pdf|docx?|jpe?g|png)$/i', $s);
        })->values()->toArray() : [];
        $details = $jobseeker->details ?: new JobseekerDetail(['jobseeker_id' => $jobseeker->jobseeker_id]);
        $preferences = $jobseeker->preferences ?: new JobPreference(['jobseeker_id' => $jobseeker->jobseeker_id]);
        $socialStatus = $jobseeker->socialStatus ?: new SocialStatus(['jobseeker_id' => $jobseeker->jobseeker_id]);
        $profileStrength = $this->calculateProfileStrength($jobseeker);

        $certificates = DB::table('training_enrollments')
            ->join('training_programs', 'training_enrollments.training_id', '=', 'training_programs.training_id')
            ->where('training_enrollments.jobseeker_id', $jobseeker->jobseeker_id)
            ->where('training_enrollments.certificate_issued', 1)
            ->select(
                'training_enrollments.*',
                'training_programs.title as course_title',
                'training_programs.training_type'
            )
            ->orderBy('training_enrollments.certificate_issued_at', 'desc')
            ->get();

        return view('jobseeker.profile', compact(
            'user',
            'jobseeker',
            'skills',
            'details',
            'preferences',
            'socialStatus',
            'profileStrength',
            'certificates'
        ));
    }

    public function updateProfile(Request $request)
    {
        $jobseeker = $this->getOrCreateJobseeker();

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'sex' => 'nullable|string|max:20',
            'civil_status' => 'nullable|string|max:50',
            'citizenship' => 'nullable|string|max:50',
            'mobile_number' => 'nullable|string|max:30',
            'employment_status' => 'nullable|string|max:100',
            'hired_company' => 'nullable|string|max:150',
            'work_experience_submitted' => 'nullable|boolean',
            'work_experience' => 'nullable|array',
            'work_experience.*.employer' => 'nullable|string|max:150',
            'work_experience.*.employer_address' => 'nullable|string|max:500',
            'work_experience.*.employer_reference' => 'nullable|string|max:150',
            'work_experience.*.employer_contact_number' => 'nullable|string|max:30',
            'work_experience.*.employer_contact_email' => 'nullable|email|max:150',
            'work_experience.*.position' => 'nullable|string|max:150',
            'work_experience.*.job_title' => 'nullable|string|max:150',
            'work_experience.*.employment_type' => 'nullable|in:Full-time,Part-time,Contract/Project-based,Internship/OJT,Freelance,Temporary',
            'work_experience.*.start_date' => 'nullable|date',
            'work_experience.*.end_date' => 'nullable|date',
            'work_experience.*.responsibilities' => 'nullable|string|max:3000',
        ]);

        foreach ((array) $request->input('work_experience', []) as $index => $experience) {
            if (
                is_array($experience) && !empty($experience['start_date']) && !empty($experience['end_date'])
                && strtotime($experience['end_date']) < strtotime($experience['start_date'])
            ) {
                return back()->withErrors([
                    "work_experience.{$index}.end_date" => 'The end date must be on or after the start date.',
                ])->withInput();
            }
        }

        $jobseeker->update($request->only([
            'first_name',
            'last_name',
            'middle_name',
            'mobile_number',
            'civil_status',
            'sex',
            'citizenship',
            'employment_status',
            'hired_company',
            'birth_date'
        ]));

        $details = $jobseeker->details ?: new JobseekerDetail(['jobseeker_id' => $jobseeker->jobseeker_id]);

        $currentAddress = is_array($details->address) ? $details->address : (json_decode($details->address ?? '', true) ?: []);
        $currentAddress['street'] = $request->input('address_street', $currentAddress['street'] ?? '');
        $currentAddress['barangay'] = $request->input('address_barangay', $currentAddress['barangay'] ?? '');
        $currentAddress['city'] = $request->input('address_city', $currentAddress['city'] ?? 'Cebu City');
        $currentAddress['province'] = $request->input('address_province', $currentAddress['province'] ?? 'Cebu');
        $currentAddress['zip'] = $request->input('address_zip', $currentAddress['zip'] ?? '');
        $currentAddress['full'] = trim(implode(', ', array_filter([
            $currentAddress['street'],
            $currentAddress['barangay'],
            $currentAddress['city'],
            $currentAddress['province']
        ])));
        $details->address = $currentAddress;

        $currentEdu = is_array($details->education) ? $details->education : (json_decode($details->education ?? '', true) ?: []);
        $currentEdu['level'] = $request->input('education_level', $currentEdu['level'] ?? '');
        $currentEdu['school'] = $request->input('education_school', $currentEdu['school'] ?? '');
        $currentEdu['course'] = $request->input('education_course', $currentEdu['course'] ?? '');
        $currentEdu['year_graduated'] = $request->input('education_year', $currentEdu['year_graduated'] ?? '');
        $details->education = $currentEdu;

        $currentExp = is_array($details->work_experience) ? $details->work_experience : (json_decode($details->work_experience ?? '', true) ?: []);
        if (!is_array($currentExp)) {
            $currentExp = [];
        }
        if ($request->boolean('work_experience_submitted')) {
            $submittedExperiences = $request->input('work_experience', []);
        } elseif ($request->hasAny(['experience_company', 'experience_position', 'experience_duration', 'experience_description'])) {
            $submittedExperiences = [[
                'employer' => $request->input('experience_company'),
                'position' => $request->input('experience_position'),
                'job_title' => $request->input('experience_position'),
                'responsibilities' => $request->input('experience_description'),
            ]];
        } else {
            $submittedExperiences = $currentExp['entries'] ?? [];
            if ($submittedExperiences === [] && !array_key_exists('entries', $currentExp) && !array_is_list($currentExp)) {
                $submittedExperiences = [$currentExp];
            }
        }

        $workExperienceEntries = [];
        foreach ((array) $submittedExperiences as $index => $experience) {
            if (!is_array($experience)) {
                continue;
            }

            $employmentTypeAliases = [
                'full time' => 'Full-time',
                'full-time' => 'Full-time',
                'fulltime' => 'Full-time',
                'regular' => 'Full-time',
                'part time' => 'Part-time',
                'part-time' => 'Part-time',
                'contract' => 'Contract/Project-based',
                'project-based' => 'Contract/Project-based',
                'contract/project-based' => 'Contract/Project-based',
                'internship' => 'Internship/OJT',
                'ojt' => 'Internship/OJT',
                'internship/ojt' => 'Internship/OJT',
                'freelance' => 'Freelance',
                'temporary' => 'Temporary',
                'temp' => 'Temporary',
            ];
            $legacyPosition = trim((string) ($experience['position'] ?? ''));
            $legacyTitle = trim((string) ($experience['job_title'] ?? $experience['title'] ?? ''));
            $employmentType = trim((string) ($experience['employment_type'] ?? ''));
            $position = $legacyPosition;
            if ($employmentType === '' && isset($employmentTypeAliases[strtolower($legacyPosition)])) {
                $employmentType = $employmentTypeAliases[strtolower($legacyPosition)];
                $position = $legacyTitle;
            } elseif ($employmentType === '' && isset($employmentTypeAliases[strtolower($legacyTitle)])) {
                $employmentType = $employmentTypeAliases[strtolower($legacyTitle)];
            }

            $entry = [
                'employer' => trim((string) ($experience['employer'] ?? $experience['company'] ?? '')),
                'employer_address' => trim((string) ($experience['employer_address'] ?? '')),
                'employer_reference' => trim((string) ($experience['employer_reference'] ?? $experience['reference'] ?? '')),
                'employer_contact_number' => trim((string) ($experience['employer_contact_number'] ?? '')),
                'employer_contact_email' => trim((string) ($experience['employer_contact_email'] ?? '')),
                'position' => $position,
                'employment_type' => $employmentType,
                'job_title' => $employmentType,
                'start_date' => $experience['start_date'] ?? null,
                'end_date' => $experience['end_date'] ?? null,
                'responsibilities' => trim((string) ($experience['responsibilities'] ?? $experience['description'] ?? '')),
            ];

            $hasExperienceData = false;
            foreach ($entry as $value) {
                if (trim((string) $value) !== '') {
                    $hasExperienceData = true;
                    break;
                }
            }

            if ($hasExperienceData) {
                $workExperienceEntries[] = $entry;
            }
        }

        $details->work_experience = [
            'entries' => array_values($workExperienceEntries),
            'summary' => $request->input('bio', $currentExp['summary'] ?? ''),
        ];

        $currentElig = is_array($details->eligibility) ? $details->eligibility : (json_decode($details->eligibility ?? '', true) ?: []);
        $currentElig['civil_service'] = $request->input('eligibility_civil_service', $currentElig['civil_service'] ?? '');
        $currentElig['prc_license'] = $request->input('eligibility_prc_license', $currentElig['prc_license'] ?? '');
        $currentElig['tesda_nc'] = $request->input('eligibility_tesda_nc', $currentElig['tesda_nc'] ?? '');
        $currentElig['driver_license'] = $request->input('eligibility_driver_license', $currentElig['driver_license'] ?? '');
        $details->eligibility = $currentElig;

        $languages = $request->input('languages', []);
        if (is_string($languages)) {
            $languages = array_map('trim', explode(',', $languages));
        }
        $details->language_proficiency = is_array($languages) ? array_values(array_filter($languages)) : [];
        $details->save();

        $preferences = $jobseeker->preferences ?: new JobPreference(['jobseeker_id' => $jobseeker->jobseeker_id]);
        $preferences->occupation1 = $request->input('occupation1', $preferences->occupation1);
        $preferences->occupation2 = $request->input('occupation2', $preferences->occupation2);
        $preferences->industry1 = $request->input('industry1', $preferences->industry1);
        $preferences->preferred_location = $request->input('preferred_location', $preferences->preferred_location);
        $preferences->salary_expectation = $request->input('salary_expectation', $preferences->salary_expectation);
        $preferences->save();

        // 4. Update Social Status (PWD, 4Ps, OFW, LGBTQ+, Senior, Immigrant, Region 7)
        $social = $jobseeker->socialStatus ?: new SocialStatus(['jobseeker_id' => $jobseeker->jobseeker_id]);
        $social->is_pwd = $request->boolean('is_pwd');
        $social->pwd_type = $request->boolean('is_pwd') ? $request->input('pwd_type', '') : null;
        $social->is_4ps = $request->boolean('is_4ps');
        $social->household_id = $request->boolean('is_4ps') ? $request->input('household_id', '') : null;
        $social->is_ofw = $request->boolean('is_ofw');
        $social->is_lgbtq = $request->boolean('is_lgbtq');
        $social->is_senior_citizen = $request->boolean('is_senior_citizen');
        $social->is_immigrant = $request->boolean('is_immigrant');
        $social->immigrant_origin = $request->boolean('is_immigrant') ? $request->input('immigrant_origin', '') : null;
        $social->region = $request->input('region', $social->region ?: 'Region VII - Central Visayas');
        $social->save();

        return redirect()->route('jobseeker.profile')->with('success', 'Comprehensive profile updated successfully.');
    }

    public function syncSkills(Request $request)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $submittedSkills = $request->input('skills', []);

        JobseekerSkill::where('jobseeker_id', $jobseeker->jobseeker_id)->delete();

        $uniqueSkills = array_values(array_unique(array_filter(array_map('trim', $submittedSkills), function ($s) {
            return !empty($s) && $s !== '[object Object]' && $s !== 'object Object';
        })));

        foreach ($uniqueSkills as $skill) {
            JobseekerSkill::create([
                'jobseeker_id' => $jobseeker->jobseeker_id,
                'skill_name' => $skill,
                'skill_type' => 'technical',
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'skills' => $uniqueSkills]);
        }

        return redirect()->route('jobseeker.profile')->with('success', 'Skills matrix updated successfully!');
    }

    public function addSkill(Request $request)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $skillName = trim($request->input('skill_name'));

        if (empty($skillName)) {
            return redirect()->back()->with('error', 'Skill name cannot be empty.');
        }

        $exists = JobseekerSkill::where('jobseeker_id', $jobseeker->jobseeker_id)
            ->whereRaw('LOWER(skill_name) = ?', [strtolower($skillName)])
            ->first();

        if (!$exists) {
            JobseekerSkill::create([
                'jobseeker_id' => $jobseeker->jobseeker_id,
                'skill_name' => $skillName,
                'skill_type' => 'technical',
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'skill' => $skillName]);
        }

        return redirect()->route('jobseeker.profile')->with('success', "Added skill: {$skillName}");
    }

    public function removeSkill(int $id)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        JobseekerSkill::where('jobseeker_id', $jobseeker->jobseeker_id)
            ->where('skill_id', $id)
            ->delete();

        return redirect()->route('jobseeker.profile')->with('info', 'Skill removed.');
    }

    private function calculateProfileStrength(?Jobseeker $jobseeker): int
    {
        if (!$jobseeker) {
            return 20;
        }

        $strength = 30;
        if (!empty($jobseeker->first_name) && !empty($jobseeker->last_name)) $strength += 15;
        if (!empty($jobseeker->mobile_number)) $strength += 15;

        $skillsCount = $jobseeker->skills ? $jobseeker->skills->count() : 0;
        if ($skillsCount >= 3) $strength += 20;
        elseif ($skillsCount >= 1) $strength += 10;

        if ($jobseeker->details && (!empty($jobseeker->details->education) || !empty($jobseeker->details->work_experience) || !empty($jobseeker->details->resume_path))) {
            $strength += 20;
        }

        return min(100, $strength);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('jobseeker.profile', ['tab' => 'security'])
                ->withErrors(['current_password' => 'The provided current password does not match your account password.'])
                ->withInput();
        }

        /** @var \App\Models\User $user */
        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        Notification::create([
            'user_id' => $user->user_id,
            'title' => 'Password Reset Successfully',
            'message' => 'Your account password was recently updated. If you did not make this change, please contact DMDP administrator immediately.',
            'type' => 'manual_review',
            'is_read' => false,
        ]);

        return redirect()->route('jobseeker.profile', ['tab' => 'security'])
            ->with('success', 'Your password has been successfully reset and updated.');
    }
}
