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
use App\Services\SkillMatchingService;
use App\Services\TrainingQuizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JobseekerPortalController extends Controller
{
    protected SkillMatchingService $matchingService;
    protected TrainingQuizService $quizService;

    public function __construct(SkillMatchingService $matchingService, TrainingQuizService $quizService)
    {
        $this->matchingService = $matchingService;
        $this->quizService = $quizService;
    }

    /**
     * Ensure the authenticated user has an associated Jobseeker profile.
     */
    private function getOrCreateJobseeker(): Jobseeker
    {
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
    // 1. DASHBOARD / HOME
    // =========================================================================

    public function index()
    {
        $user = Auth::user();
        $jobseeker = $this->getOrCreateJobseeker();
        $userSkills = $this->matchingService->getJobseekerSkills($jobseeker);

        // Fetch all active/approved and non-expired jobs
        $allJobs = JobPosting::with('employer')
            ->availableForJobseekers()
            ->orderByDesc('job_id')
            ->get();

        // Calculate AI Cosine Similarity rankings
        $rankedJobs = $this->matchingService->rankJobsForJobseeker($allJobs, $jobseeker);

        // Best match job (Hero banner)
        $bestMatch = !empty($rankedJobs) ? $rankedJobs[0] : null;

        // Statistics
        $availableJobsCount = $allJobs->count();
        $activeApplicationsCount = JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)
            ->whereIn('status', ['pending', 'reviewed', 'interview'])
            ->count();
        $availableTrainingsCount = TrainingProgram::count();

        // Calculate Profile Strength percentage
        $profileStrength = 30; // base score for account creation
        if (!empty($jobseeker->first_name) && !empty($jobseeker->last_name)) $profileStrength += 15;
        if (!empty($jobseeker->mobile_number)) $profileStrength += 15;
        if (count($userSkills) >= 3) $profileStrength += 20;
        elseif (count($userSkills) >= 1) $profileStrength += 10;
        if ($jobseeker->details && (!empty($jobseeker->details->education) || !empty($jobseeker->details->work_experience))) $profileStrength += 20;
        $profileStrength = min(100, $profileStrength);

        // Recommended trainings
        $trainings = TrainingProgram::with('topics')->take(3)->get();

        // Recent applications
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
            'recentApplications'
        ));
    }

    // =========================================================================
    // 2. JOB SEARCH & EXPLORER
    // =========================================================================

    public function jobs(Request $request)
    {
        $user = Auth::user();
        $jobseeker = $this->getOrCreateJobseeker();
        $userSkills = $this->matchingService->getJobseekerSkills($jobseeker);

        $query = JobPosting::with('employer')->availableForJobseekers();

        // Search by keyword
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

        // Filter by location
        if ($location = $request->input('location')) {
            $query->where(function ($q) use ($location) {
                $q->where('description', 'LIKE', "%{$location}%")
                  ->orWhere('qualifications', 'LIKE', "%{$location}%");
            });
        }

        // Filter: PWD Inclusive
        if ($request->boolean('pwd_only') || $request->input('filter') === 'pwd') {
            $query->where('accepts_disability', true);
        }

        $allJobs = $query->get();
        $rankedJobs = $this->matchingService->rankJobsForJobseeker($allJobs, $jobseeker);

        // Sorting
        $sort = $request->input('sort', 'match');
        if ($sort === 'latest') {
            usort($rankedJobs, fn($a, $b) => $b['job']->job_id <=> $a['job']->job_id);
        }

        // Get applied job IDs for quick badge indication
        $appliedJobIds = JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)
            ->pluck('job_id')
            ->toArray();

        // Selected job for right-hand preview panel
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

        // Check if user has uploaded resumes for application modal
        $savedDocuments = $jobseeker->details && $jobseeker->details->training_certificates 
            ? (is_array($jobseeker->details->training_certificates) ? $jobseeker->details->training_certificates : json_decode($jobseeker->details->training_certificates, true))
            : [];

        return view('jobseeker.jobs.index', compact(
            'rankedJobs',
            'selectedItem',
            'userSkills',
            'appliedJobIds',
            'savedDocuments',
            'jobseeker'
        ));
    }

    public function jobShow($id)
    {
        $job = JobPosting::with('employer')->findOrFail($id);

        // Expired or unapproved jobs cannot be viewed by jobseekers
        if ($job->status !== 'approved' || $job->isExpired()) {
            return redirect()->route('jobseeker.jobs')
                ->with('error', 'This job posting has expired or is no longer available.');
        }

        $jobseeker = $this->getOrCreateJobseeker();
        $userSkills = $this->matchingService->getJobseekerSkills($jobseeker);
        $jobSkills = $this->matchingService->getJobSkills($job);
        $match = $this->matchingService->calculateMatch($userSkills, $jobSkills);

        $hasApplied = JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)
            ->where('job_id', $job->job_id)
            ->first();

        // Suggested training for missing skills
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
            'jobseeker'
        ));
    }

    // =========================================================================
    // 3. APPLICATION LIFECYCLE MANAGEMENT
    // =========================================================================

    public function apply(Request $request, $jobId)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $job = JobPosting::findOrFail($jobId);

        // Expired or unapproved jobs cannot be applied to
        if ($job->status !== 'approved' || $job->isExpired()) {
            return redirect()->route('jobseeker.jobs')
                ->with('error', 'This job posting has expired or is no longer accepting applications.');
        }

        // Employed check: cannot apply for another job while currently hired/employed
        if ($jobseeker->isEmployed()) {
            $company = $jobseeker->hired_company ?: 'your current employer';
            return redirect()->route('jobseeker.jobs')
                ->with('error', "You cannot apply for another job while currently employed at {$company}. You must request a resignation from your employer and have it approved before applying for other positions.");
        }

        // Duplicate check
        $existing = JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)
            ->where('job_id', $jobId)
            ->first();

        if ($existing) {
            return redirect()->back()->with('warning', 'You have already submitted an application for this position.');
        }

        // Handle resume file upload if provided
        $resumeUrl = null;
        if ($request->hasFile('resume')) {
            $path = $request->file('resume')->store('resumes', 'public');
            $resumeUrl = Storage::url($path);

            // Sync resume to jobseeker document vault (training_certificates)
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
        }

        // Create application
        $application = JobApplication::create([
            'job_id' => $jobId,
            'jobseeker_id' => $jobseeker->jobseeker_id,
            'status' => 'pending',
            'referred_by_jpo' => false,
        ]);

        // Create notification
        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'Application Submitted',
            'message' => "Your application for {$job->title} at " . ($job->employer->company_name ?? 'Partner Employer') . " was successfully submitted.",
            'type' => 'approval',
            'is_read' => false,
            'related_id' => $application->application_id,
        ]);

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

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        $applications = $query->orderByDesc('application_id')->get()->map(function ($app) use ($userSkills) {
            $jobSkills = $app->jobPosting ? $this->matchingService->getJobSkills($app->jobPosting) : [];
            $match = $this->matchingService->calculateMatch($userSkills, $jobSkills);
            $app->match_details = $match;
            return $app;
        });

        // Counts by status
        $counts = [
            'all' => JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->count(),
            'offered' => JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->where('status', 'offered')->count(),
            'pending' => JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->where('status', 'pending')->count(),
            'reviewed' => JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->where('status', 'reviewed')->count(),
            'interview' => JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->where('status', 'interview')->count(),
            'hired' => JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->where('status', 'hired')->count(),
            'rejected' => JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->where('status', 'rejected')->count(),
        ];

        return view('jobseeker.applications.index', compact('applications', 'counts', 'filter', 'jobseeker'));
    }

    public function acceptOffer($id)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $application = JobApplication::with(['jobPosting.employer.user', 'jobseeker'])
            ->where('jobseeker_id', $jobseeker->jobseeker_id)
            ->findOrFail($id);

        if ($application->status !== 'offered') {
            return redirect()->back()->with('error', 'This application does not have an active job offer to accept.');
        }

        $jobTitle = $application->jobPosting?->title ?? 'Position';
        $employer = $application->jobPosting?->employer;
        $companyName = $employer?->company_name ?? 'Company';

        // 1. Mark this application as hired
        $application->update([
            'status' => 'hired',
            'hired_date' => now()->toDateString(),
        ]);

        // 2. Automatically update jobseeker profile to Employed
        $jobseeker->update([
            'employment_status' => 'Employed',
            'hired_company' => $companyName,
        ]);

        // 3. Notify the hiring employer
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

        // 4. Auto-withdraw all other active applications
        $otherActiveApps = JobApplication::with(['jobPosting.employer.user'])
            ->where('jobseeker_id', $jobseeker->jobseeker_id)
            ->where('application_id', '!=', $application->application_id)
            ->whereIn('status', ['pending', 'reviewed', 'interview', 'offered'])
            ->get();

        foreach ($otherActiveApps as $otherApp) {
            $otherApp->update([
                'status' => 'withdrawn',
            ]);

            // Notify other employers
            $otherEmployerUser = $otherApp->jobPosting?->employer?->user;
            if ($otherEmployerUser) {
                Notification::create([
                    'user_id' => $otherEmployerUser->user_id,
                    'title' => 'Application Withdrawn (Candidate Accepted Another Offer)',
                    'message' => "Candidate {$jobseeker->first_name} {$jobseeker->last_name} has accepted an offer from another company and their application for '{$otherApp->jobPosting->title}' has been automatically withdrawn.",
                    'type' => 'info',
                    'is_read' => false,
                    'related_id' => $otherApp->application_id,
                ]);
            }
        }

        // 5. Notify the jobseeker
        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'Congratulations on Your New Job!',
            'message' => "You have officially accepted the job offer for '{$jobTitle}' at {$companyName}. Your employment status is now active!",
            'type' => 'approval',
            'is_read' => false,
            'related_id' => $application->application_id,
        ]);

        $withdrawnCount = $otherActiveApps->count();
        $msg = "Congratulations! You have accepted the job offer from {$companyName}. You are now officially hired!";
        if ($withdrawnCount > 0) {
            $msg .= " Your other {$withdrawnCount} active application(s) were automatically withdrawn.";
        }

        return redirect()->route('jobseeker.applications')->with('success', $msg);
    }

    public function declineOffer(Request $request, $id)
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

        // Notify employer
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

    public function withdrawApplication($id)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $app = JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->findOrFail($id);

        $app->delete();

        return redirect()->back()->with('info', 'Your application has been withdrawn.');
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

        // Notify employer
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
    // 4. TRAINING PROGRAMS & QUIZZES
    // =========================================================================

    public function trainingIndex(Request $request)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $userSkills = $this->matchingService->getJobseekerSkills($jobseeker);
        $filter = $request->input('filter', 'all');

        $allTrainings = TrainingProgram::with(['topics', 'enrollments' => function ($q) use ($jobseeker) {
            $q->where('jobseeker_id', $jobseeker->jobseeker_id);
        }])->get();

        $enrolledCount = $allTrainings->filter(function ($t) {
            $enr = $t->enrollments->first();
            return $enr && in_array($enr->status, ['enrolled', 'in_progress', 'completed']);
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
                return $enr && in_array($enr->status, ['enrolled', 'in_progress', 'completed']);
            });
        } elseif ($filter === 'completed') {
            $trainings = $allTrainings->filter(function ($t) {
                $enr = $t->enrollments->first();
                return $enr && $enr->status === 'completed';
            });
        } else {
            $trainings = $allTrainings;
        }

        // Identify earned vs available skills
        $allTrainingSkills = [];
        foreach ($allTrainings as $t) {
            // Extract keywords or predefined skills
            $words = array_filter(explode(' ', $t->title), fn($w) => strlen($w) > 3);
            $allTrainingSkills[$t->training_id] = array_values($words);
        }

        return view('jobseeker.training.index', compact('trainings', 'userSkills', 'allTrainingSkills', 'jobseeker', 'filter', 'counts'));
    }

    /**
     * View Training Skills catalog and pathways.
     */
    public function trainingSkills(Request $request)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $userSkills = $this->matchingService->getJobseekerSkills($jobseeker);
        $userSkillNames = array_map('strtolower', $userSkills);

        $trainings = TrainingProgram::with(['trainer', 'topics', 'enrollments' => function ($q) use ($jobseeker) {
            $q->where('jobseeker_id', $jobseeker->jobseeker_id);
        }])->get();

        $skillsCatalog = [];
        foreach ($trainings as $training) {
            $enrollment = $training->enrollments->first();
            $status = $enrollment ? $enrollment->status : 'unregistered';
            $certIssued = $enrollment ? (bool)$enrollment->certificate_issued : false;

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

        return view('jobseeker.training.skills', compact('skillsCatalog', 'userSkills', 'jobseeker'));
    }

    /**
     * View Training Enrollments dashboard (My Enrollments).
     */
    public function trainingEnrollments(Request $request)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $userSkills = $this->matchingService->getJobseekerSkills($jobseeker);

        $enrollments = TrainingEnrollment::with(['trainingProgram.topics', 'trainingProgram.trainer'])
            ->where('jobseeker_id', $jobseeker->jobseeker_id)
            ->orderBy('enrollment_id', 'desc')
            ->get();

        $stats = [
            'total' => $enrollments->count(),
            'in_progress' => $enrollments->whereIn('status', ['enrolled', 'in_progress'])->count(),
            'completed' => $enrollments->where('status', 'completed')->count(),
            'certificates' => $enrollments->where('certificate_issued', 1)->count(),
        ];

        return view('jobseeker.training.enrollments', compact('enrollments', 'jobseeker', 'userSkills', 'stats'));
    }

    public function enrollTraining($id)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $training = TrainingProgram::findOrFail($id);

        $enrollment = TrainingEnrollment::where('jobseeker_id', $jobseeker->jobseeker_id)
            ->where('training_id', $id)
            ->first();

        if ($enrollment) {
            return redirect()->route('jobseeker.training.show', $id)
                ->with('info', "You are already enrolled in '{$training->title}'.");
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

    public function trainingShow($id)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $training = TrainingProgram::with('topics')->findOrFail($id);
        $enrollment = TrainingEnrollment::where('jobseeker_id', $jobseeker->jobseeker_id)
            ->where('training_id', $id)
            ->first();

        return view('jobseeker.training.show', compact('training', 'enrollment', 'jobseeker'));
    }

    public function trainingQuiz($id)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $training = TrainingProgram::with('topics')->findOrFail($id);
        $questions = $this->quizService->getQuestionsForTraining($training, 5);

        return view('jobseeker.training.quiz', compact('training', 'jobseeker', 'questions'));
    }

    public function submitQuiz(Request $request, $id)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $training = TrainingProgram::with('trainer')->findOrFail($id);

        $score = (int) $request->input('score', 0);
        $passingThreshold = $training->passing_score ?: 80;
        $passed = $score >= $passingThreshold;

        $enrollment = TrainingEnrollment::where('jobseeker_id', $jobseeker->jobseeker_id)
            ->where('training_id', $id)
            ->first();

        if (!$enrollment) {
            $enrollment = new TrainingEnrollment([
                'jobseeker_id' => $jobseeker->jobseeker_id,
                'training_id' => $id,
                'training_type' => 'online',
                'start_date' => now()->toDateString(),
            ]);
        }

        $enrollment->score = $score;
        $enrollment->passed = $passed ? 1 : 0;
        $enrollment->answers = ['score' => $score, 'passed' => $passed, 'submitted_at' => now()->toIso8601String()];

        if ($passed) {
            $enrollment->end_date = now()->toDateString();
            $enrollment->status = 'completed';

            // Check if trainer enabled automatic certificate generation
            $autoGenCert = (bool) ($training->auto_generate_certificate ?? true);

            if ($autoGenCert) {
                // Automatically generate certificate
                $certNo = 'DMDP-CERT-' . date('Y') . '-' . strtoupper(Str::random(6));
                $enrollment->certificate_no = $certNo;
                $enrollment->certificate_issued = 1;
                $enrollment->certificate_issued_at = now();

                // Store certificate in jobseeker Document Hub vault (training_certificates)
                $jobseekerDetail = JobseekerDetail::where('jobseeker_id', $jobseeker->jobseeker_id)->first();
                $existingCerts = [];
                if ($jobseekerDetail && !empty($jobseekerDetail->training_certificates)) {
                    $existingCerts = is_array($jobseekerDetail->training_certificates)
                        ? $jobseekerDetail->training_certificates
                        : (json_decode($jobseekerDetail->training_certificates, true) ?: []);
                }

                // Filter out previous entry if any
                $existingCerts = array_values(array_filter($existingCerts, fn($c) => ($c['enrollment_id'] ?? null) != $enrollment->enrollment_id));
                $certUrl = route('jobseeker.certificates.preview', $enrollment->enrollment_id ?: $id);

                $existingCerts[] = [
                    'id' => 'cert_' . ($enrollment->enrollment_id ?: $id),
                    'enrollment_id' => $enrollment->enrollment_id ?: $id,
                    'category' => 'certificate',
                    'name' => "Certificate of Completion - {$training->title} (#{$certNo})",
                    'file_url' => $certUrl,
                    'status' => 'verified',
                    'certificate_no' => $certNo,
                    'course_title' => $training->title,
                    'uploaded_at' => now()->toIso8601String(),
                ];

                JobseekerDetail::updateOrCreate(
                    ['jobseeker_id' => $jobseeker->jobseeker_id],
                    ['training_certificates' => json_encode($existingCerts)]
                );

                // Automatically grant skill tag to jobseeker's profile
                $skillName = trim($training->title);
                $exists = JobseekerSkill::where('jobseeker_id', $jobseeker->jobseeker_id)
                    ->where('skill_name', $skillName)
                    ->first();

                if (!$exists) {
                    JobseekerSkill::create([
                        'jobseeker_id' => $jobseeker->jobseeker_id,
                        'skill_name' => $skillName,
                        'skill_type' => 'technical',
                    ]);
                }

                // Celebratory achievement notification
                Notification::create([
                    'user_id' => Auth::id(),
                    'title' => '🎓 Official Certificate Issued!',
                    'message' => "Congratulations! You passed '{$training->title}' with {$score}%. Your official Certificate of Completion (#{$certNo}) has been automatically generated and is ready in your Document Hub.",
                    'type' => 'training',
                    'is_read' => false,
                    'related_id' => $training->training_id,
                ]);
            } else {
                // Trainer chose manual certificate review/assessment
                $enrollment->certificate_issued = 0;

                Notification::create([
                    'user_id' => Auth::id(),
                    'title' => 'Assessment Completed (Pending Trainer Review)',
                    'message' => "You scored {$score}% on '{$training->title}'. Your assessment will be reviewed by your trainer before your official certificate is released.",
                    'type' => 'training',
                    'is_read' => false,
                    'related_id' => $training->training_id,
                ]);

                if ($training->trainer && $training->trainer->user_id) {
                    Notification::create([
                        'user_id' => $training->trainer->user_id,
                        'title' => 'Trainee Assessment Completed',
                        'message' => "{$jobseeker->first_name} {$jobseeker->last_name} completed the assessment for '{$training->title}' (Score: {$score}%). Review to issue their certificate.",
                        'type' => 'training',
                        'is_read' => false,
                        'related_id' => $enrollment->enrollment_id ?: $id,
                    ]);
                }
            }
        } else {
            $enrollment->status = 'in_progress';
        }

        $enrollment->save();

        // If enrollment_id was newly generated, sync with certificate if auto-generated
        if ($passed && ($training->auto_generate_certificate ?? true) && !empty($enrollment->certificate_no)) {
            $jobseekerDetail = JobseekerDetail::where('jobseeker_id', $jobseeker->jobseeker_id)->first();
            if ($jobseekerDetail && !empty($jobseekerDetail->training_certificates)) {
                $docs = json_decode($jobseekerDetail->training_certificates, true) ?: [];
                foreach ($docs as &$doc) {
                    if (($doc['certificate_no'] ?? '') === $enrollment->certificate_no) {
                        $doc['enrollment_id'] = $enrollment->enrollment_id;
                        $doc['id'] = 'cert_' . $enrollment->enrollment_id;
                        $doc['file_url'] = route('jobseeker.certificates.preview', $enrollment->enrollment_id);
                    }
                }
                $jobseekerDetail->training_certificates = json_encode($docs);
                $jobseekerDetail->save();
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'passed' => $passed,
                'score' => $score,
                'certificate_issued' => $enrollment->certificate_issued ?? 0,
                'message' => $passed 
                    ? (($training->auto_generate_certificate ?? true) 
                        ? "Congratulations! Certificate generated and awarded for '{$training->title}'." 
                        : "Assessment submitted! Score recorded. Trainer will review before issuing certificate.")
                    : "Quiz completed with score {$score}%. Review the course materials and try again!",
            ]);
        }

        return redirect()->route('jobseeker.training.show', $id)
            ->with($passed ? 'success' : 'info', $passed 
                ? (($training->auto_generate_certificate ?? true) 
                    ? "Congratulations! You scored {$score}% and your official certificate has been issued." 
                    : "You scored {$score}%. Your submission has been sent to your trainer for evaluation.")
                : "You scored {$score}%. Review the lessons and try again to earn your certificate!");
    }

    /**
     * Preview and print/download Certificate of Completion for Jobseeker.
     */
    public function previewCertificate($id)
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
                'training_programs.description as course_desc'
            )
            ->first();

        if (!$enrollment) {
            abort(404, 'Certificate record not found or not yet generated.');
        }

        return view('jobseeker.certificates.preview', compact('enrollment'));
    }

    // =========================================================================
    // 5. DOCUMENT HUB & VERIFICATION
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

    public function uploadDocument(Request $request)
    {
        $request->validate([
            'category' => 'required|in:resume,valid_id,certificate,certs,pwd_id',
            'document_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $jobseeker = $this->getOrCreateJobseeker();
        $details = $jobseeker->details;

        if (!$details) {
            $details = JobseekerDetail::create([
                'jobseeker_id' => $jobseeker->jobseeker_id,
                'training_certificates' => [],
            ]);
        }

        $file = $request->file('document_file');
        $cleanName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());
        $fileName = time() . '_' . $cleanName;
        $filePath = $file->storeAs('documents/' . $jobseeker->jobseeker_id, $fileName, 'public');
        $fileUrl = Storage::url($filePath);

        $category = $request->input('category');
        $canonicalCategory = ($category === 'certs') ? 'certificate' : $category;

        $existingDocs = is_array($details->training_certificates) 
            ? $details->training_certificates 
            : (json_decode($details->training_certificates ?? '[]', true) ?: []);

        // Filter out existing single-item category (unless it's an issued certificate from an enrollment)
        $filtered = array_filter($existingDocs, function ($d) use ($canonicalCategory) {
            $cat = ($d['category'] ?? '') === 'certs' ? 'certificate' : ($d['category'] ?? '');
            if ($cat !== $canonicalCategory) {
                return true;
            }
            if ($canonicalCategory === 'certificate' && !empty($d['enrollment_id'])) {
                return true; // Keep official issued course certificates
            }
            return false;
        });

        $newDoc = [
            'id' => 'doc_' . uniqid(),
            'category' => $canonicalCategory,
            'name' => $file->getClientOriginalName(),
            'file_url' => $fileUrl,
            'status' => 'under_review',
            'uploaded_at' => now()->toIso8601String(),
        ];

        $filtered[] = $newDoc;
        $details->training_certificates = array_values($filtered);
        $details->save();

        // Create Notification
        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'Document Uploaded',
            'message' => "Your " . ucfirst(str_replace('_', ' ', $canonicalCategory)) . " has been uploaded and is under review.",
            'type' => 'approval',
            'is_read' => false,
        ]);

        return redirect()->route('jobseeker.documents')
            ->with('success', ucfirst(str_replace('_', ' ', $canonicalCategory)) . ' uploaded successfully to your vault.');
    }

    public function deleteDocument(Request $request, $category)
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
    // 6. NOTIFICATION CENTER
    // =========================================================================

    public function notifications(Request $request)
    {
        $user = Auth::user();
        $filter = $request->input('category', 'all');

        $query = Notification::where('user_id', $user->user_id);

        if ($filter !== 'all') {
            if ($filter === 'application') {
                $query->whereIn('type', ['application', 'approval', 'rejection', 'interview', 'manual_review', 'referral', 'hired']);
            } elseif ($filter === 'training') {
                $query->whereIn('type', ['training', 'certificate', 'course', 'quiz']);
            } elseif ($filter === 'interview') {
                $query->where('type', 'interview');
            } else {
                $query->where('type', $filter);
            }
        }

        $notifications = $query->orderByDesc('created_at')->paginate(15)->appends(['category' => $filter]);
        $unreadCount = Notification::where('user_id', $user->user_id)->where('is_read', false)->count();

        return view('jobseeker.notifications.index', compact('notifications', 'unreadCount', 'filter'));
    }

    public function markNotificationRead($id)
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
    // 7. PROFILE & SKILLS MATRIX
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
        ]);

        // 1. Update Core Jobseeker Info
        $jobseeker->update($request->only([
            'first_name', 'last_name', 'middle_name', 'mobile_number',
            'civil_status', 'sex', 'citizenship', 'employment_status', 'hired_company', 'birth_date'
        ]));

        // 2. Update Details (Address, Education, Work Experience, Eligibility, Language, Bio)
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
        $currentExp['company'] = $request->input('experience_company', $currentExp['company'] ?? '');
        $currentExp['position'] = $request->input('experience_position', $currentExp['position'] ?? '');
        $currentExp['duration'] = $request->input('experience_duration', $currentExp['duration'] ?? '');
        $currentExp['description'] = $request->input('experience_description', $currentExp['description'] ?? '');
        $currentExp['summary'] = $request->input('bio', $currentExp['summary'] ?? '');
        $details->work_experience = $currentExp;

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

        // 3. Update Preferences
        $preferences = $jobseeker->preferences ?: new JobPreference(['jobseeker_id' => $jobseeker->jobseeker_id]);
        $preferences->occupation1 = $request->input('occupation1', $preferences->occupation1);
        $preferences->occupation2 = $request->input('occupation2', $preferences->occupation2);
        $preferences->industry1 = $request->input('industry1', $preferences->industry1);
        $preferences->preferred_location = $request->input('preferred_location', $preferences->preferred_location);
        $preferences->salary_expectation = $request->input('salary_expectation', $preferences->salary_expectation);
        $preferences->save();

        // 4. Update Social Status (PWD, 4Ps, OFW)
        $social = $jobseeker->socialStatus ?: new SocialStatus(['jobseeker_id' => $jobseeker->jobseeker_id]);
        $social->is_pwd = $request->boolean('is_pwd');
        $social->pwd_type = $request->boolean('is_pwd') ? $request->input('pwd_type', '') : null;
        $social->is_4ps = $request->boolean('is_4ps');
        $social->household_id = $request->boolean('is_4ps') ? $request->input('household_id', '') : null;
        $social->is_ofw = $request->boolean('is_ofw');
        $social->save();

        return redirect()->route('jobseeker.profile')->with('success', 'Comprehensive profile updated successfully.');
    }

    public function syncSkills(Request $request)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $submittedSkills = $request->input('skills', []);

        // Delete existing skills
        JobseekerSkill::where('jobseeker_id', $jobseeker->jobseeker_id)->delete();

        // Insert new unique valid skills
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

    public function removeSkill($id)
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

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('jobseeker.profile', ['tab' => 'security'])
                ->withErrors(['current_password' => 'The provided current password does not match your account password.'])
                ->withInput();
        }

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
