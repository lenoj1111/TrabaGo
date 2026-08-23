<?php

namespace App\Http\Controllers\Jobseeker;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Jobseeker;
use App\Models\JobseekerDetail;
use App\Models\JobseekerSkill;
use App\Models\Notification;
use App\Models\TrainingEnrollment;
use App\Models\TrainingProgram;
use App\Models\TrainingTopic;
use App\Services\SkillMatchingService;
use App\Services\TrainingQuizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        $jobseeker = $user->jobseeker;

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

        // Fetch all active/approved jobs
        $allJobs = JobPosting::with('employer')
            ->where('status', 'approved')
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

        $query = JobPosting::with('employer')->where('status', 'approved');

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
            'pending' => JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->where('status', 'pending')->count(),
            'reviewed' => JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->where('status', 'reviewed')->count(),
            'interview' => JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->where('status', 'interview')->count(),
            'hired' => JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->where('status', 'hired')->count(),
            'rejected' => JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->where('status', 'rejected')->count(),
        ];

        return view('jobseeker.applications.index', compact('applications', 'counts', 'filter', 'jobseeker'));
    }

    public function withdrawApplication($id)
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $app = JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->findOrFail($id);

        $app->delete();

        return redirect()->back()->with('info', 'Your application has been withdrawn.');
    }

    // =========================================================================
    // 4. TRAINING PROGRAMS & QUIZZES
    // =========================================================================

    public function trainingIndex()
    {
        $jobseeker = $this->getOrCreateJobseeker();
        $userSkills = $this->matchingService->getJobseekerSkills($jobseeker);

        $trainings = TrainingProgram::with(['topics', 'enrollments' => function ($q) use ($jobseeker) {
            $q->where('jobseeker_id', $jobseeker->jobseeker_id);
        }])->get();

        // Identify earned vs available skills
        $allTrainingSkills = [];
        foreach ($trainings as $t) {
            // Extract keywords or predefined skills
            $words = array_filter(explode(' ', $t->title), fn($w) => strlen($w) > 3);
            $allTrainingSkills[$t->training_id] = array_values($words);
        }

        return view('jobseeker.training.index', compact('trainings', 'userSkills', 'allTrainingSkills', 'jobseeker'));
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
        $training = TrainingProgram::findOrFail($id);

        $score = (int) $request->input('score', 0);
        $passed = $score >= 80;

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

        $enrollment->status = $passed ? 'completed' : 'in_progress';
        $enrollment->answers = ['score' => $score, 'passed' => $passed, 'submitted_at' => now()->toIso8601String()];
        if ($passed) {
            $enrollment->end_date = now()->toDateString();

            // Automatically grant the training skill to the jobseeker's profile!
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

            // Create achievement notification
            Notification::create([
                'user_id' => Auth::id(),
                'title' => 'Skill Certified!',
                'message' => "Congratulations! You passed the '{$training->title}' assessment with {$score}%. The skill '{$skillName}' has been added to your profile, raising your AI match score!",
                'type' => 'training',
                'is_read' => false,
                'related_id' => $training->training_id,
            ]);
        }
        $enrollment->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'passed' => $passed,
                'score' => $score,
                'message' => $passed ? "Skill certified! Added '{$training->title}' to your profile." : "Quiz completed. Keep practicing!",
            ]);
        }

        return redirect()->route('jobseeker.training.show', $id)
            ->with($passed ? 'success' : 'info', $passed ? "Congratulations! You scored {$score}% and earned the skill certification." : "You scored {$score}%. Review the lessons and try again to earn your certificate!");
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
            $query->where('type', $filter);
        }

        $notifications = $query->orderByDesc('created_at')->paginate(15);
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
        $details = $jobseeker->details;
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

        return view('jobseeker.profile', compact('user', 'jobseeker', 'skills', 'details', 'profileStrength', 'certificates'));
    }

    public function updateProfile(Request $request)
    {
        $jobseeker = $this->getOrCreateJobseeker();

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'mobile_number' => 'nullable|string|max:30',
            'civil_status' => 'nullable|string|max:50',
            'sex' => 'nullable|string|max:20',
            'citizenship' => 'nullable|string|max:50',
            'employment_status' => 'nullable|string|max:100',
        ]);

        $jobseeker->update($request->only([
            'first_name', 'last_name', 'middle_name', 'mobile_number',
            'civil_status', 'sex', 'citizenship', 'employment_status', 'birth_date'
        ]));

        // Update details
        $details = $jobseeker->details ?: new JobseekerDetail(['jobseeker_id' => $jobseeker->jobseeker_id]);
        if ($request->has('address')) {
            $addr = $request->input('address');
            $details->address = is_array($addr) ? $addr : ['city' => $addr, 'full' => $addr];
        }
        if ($request->has('education')) {
            $details->education = $request->input('education');
        }
        if ($request->has('work_experience')) {
            $details->work_experience = $request->input('work_experience');
        }
        $details->save();

        return redirect()->route('jobseeker.profile')->with('success', 'Profile updated successfully.');
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
}
