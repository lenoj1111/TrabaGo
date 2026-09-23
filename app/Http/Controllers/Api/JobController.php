<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Jobseeker;
use App\Models\TrainingProgram;
use App\Services\SkillMatchingService;
use Illuminate\Http\Request;

class JobController extends Controller
{
    protected SkillMatchingService $matchingService;

    public function __construct(SkillMatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    /**
     * Format a job posting model for API responses.
     */
    private function formatJob(JobPosting $job, ?array $userSkills = null, ?array $appliedJobIds = null): array
    {
        $companyName = $job->employer->company_name ?? 'DMDP Partner Employer';
        $location = 'Cebu City, Philippines';
        
        $reqs = [];
        if (!empty($job->qualifications)) {
            $lines = preg_split('/[\r\n]+/', $job->qualifications);
            $reqs = array_values(array_filter(array_map('trim', $lines)));
        }

        $jobSkills = $this->matchingService->getJobSkills($job);
        $match = null;
        $matchPercentage = null;
        $matchedSkills = [];
        $missingSkills = [];

        if ($userSkills !== null) {
            $match = $this->matchingService->calculateMatch($userSkills, $jobSkills);
            $matchPercentage = $match['score'] ?? 0;
            $matchedSkills = $match['matchedSkills'] ?? [];
            $missingSkills = $match['missingSkills'] ?? [];
        }

        $hasApplied = false;
        if ($appliedJobIds !== null) {
            $hasApplied = in_array($job->job_id, $appliedJobIds);
        }

        return [
            'id' => $job->job_id,
            'job_id' => $job->job_id,
            'title' => $job->title,
            'company' => $companyName,
            'company_name' => $companyName,
            'employer_id' => $job->employer_id,
            'location' => $location,
            'type' => 'Full-time',
            'employment_type' => 'Full-time',
            'salary' => '₱18,000 - ₱35,000',
            'salary_expectation' => '₱18,000 - ₱35,000',
            'description' => $job->description ?? 'No description provided.',
            'qualifications' => $job->qualifications ?? '',
            'requirements' => $reqs,
            'skills' => $jobSkills,
            'vacancy_count' => $job->vacancy_count ?? 1,
            'vacancyCount' => $job->vacancy_count ?? 1,
            'valid_until' => $job->valid_until ? $job->valid_until->format('Y-m-d') : null,
            'validUntil' => $job->valid_until ? $job->valid_until->format('Y-m-d') : null,
            'accepts_disability' => (bool)$job->accepts_disability,
            'acceptsDisability' => (bool)$job->accepts_disability,
            'disability_type' => $job->disability_type,
            'status' => $job->status,
            'created_at' => $job->created_at ? $job->created_at->format('Y-m-d') : now()->format('Y-m-d'),
            'createdAt' => $job->created_at ? $job->created_at->format('Y-m-d') : now()->format('Y-m-d'),
            'logo' => null,
            'match_percentage' => $matchPercentage,
            'matched_skills' => $matchedSkills,
            'missing_skills' => $missingSkills,
            'has_applied' => $hasApplied,
        ];
    }

    /**
     * Resolve the jobseeker model from authenticated Sanctum user or fallback.
     */
    private function resolveJobseeker(?Request $request): ?\App\Models\Jobseeker
    {
        $user = auth('sanctum')->user() ?? ($request ? ($request->user() ?? \Illuminate\Support\Facades\Auth::user()) : \Illuminate\Support\Facades\Auth::user());
        if ($user) {
            $jobseeker = $user->jobseeker ?? \App\Models\Jobseeker::where('user_id', $user->user_id)->first();
            if (!$jobseeker) {
                $nameParts = explode(' ', $user->full_name ?? '', 2);
                $jobseeker = \App\Models\Jobseeker::firstOrCreate(
                    ['user_id' => $user->user_id],
                    [
                        'first_name' => $nameParts[0] ?? explode('@', $user->email)[0] ?? 'Jobseeker',
                        'last_name' => $nameParts[1] ?? 'User',
                        'email' => $user->email,
                        'employment_status' => 'Looking for job',
                        'citizenship' => 'Filipino',
                    ]
                );
            }
            return $jobseeker;
        }

        return \App\Models\Jobseeker::first();
    }

    /**
     * Get all job postings with optional search & filters and AI skill match ranking.
     */
    public function getAll(Request $request)
    {
        $jobseeker = $this->resolveJobseeker($request);
        $userSkills = $jobseeker ? $this->matchingService->getJobseekerSkills($jobseeker) : null;
        $appliedJobIds = $jobseeker 
            ? JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->pluck('job_id')->toArray() 
            : [];

        $query = JobPosting::with('employer');

        $searchTerm = $request->input('q', $request->input('query'));
        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('qualifications', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('employer', function ($empQuery) use ($searchTerm) {
                      $empQuery->where('company_name', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        if ($request->filled('location')) {
            $loc = $request->input('location');
            $query->where(function ($q) use ($loc) {
                $q->where('description', 'LIKE', "%{$loc}%");
            });
        }

        if ($request->filled('filter')) {
            $filter = strtolower($request->input('filter'));
            if ($filter === 'pwd' || $filter === 'disability') {
                $query->where('accepts_disability', 1);
            }
        }

        $allJobs = $query->orderByDesc('job_id')->get();

        $formatted = $allJobs->map(function ($job) use ($userSkills, $appliedJobIds) {
            return $this->formatJob($job, $userSkills, $appliedJobIds);
        });

        // Sort by match percentage if requested or by default if user has skills
        $sort = $request->input('sort', $userSkills ? 'match' : 'latest');
        if ($sort === 'match' && $userSkills !== null) {
            $formatted = $formatted->sortByDesc('match_percentage')->values();
        }

        return response()->json($formatted);
    }

    /**
     * Get single job posting by ID with full match details and recommended training.
     */
    public function getById(Request $request, $id)
    {
        $job = JobPosting::with('employer')->find($id);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found',
            ], 404);
        }

        $jobseeker = $this->resolveJobseeker($request);
        $userSkills = $jobseeker ? $this->matchingService->getJobseekerSkills($jobseeker) : [];
        $jobSkills = $this->matchingService->getJobSkills($job);
        $match = $this->matchingService->calculateMatch($userSkills, $jobSkills);

        $hasApplied = false;
        if ($jobseeker) {
            $hasApplied = JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)
                ->where('job_id', $job->job_id)
                ->exists();
        }

        // Recommend trainings for missing skills
        $recommendedTrainings = [];
        if (!empty($match['missingSkills'])) {
            $recommendedTrainings = TrainingProgram::where(function ($q) use ($match) {
                foreach ($match['missingSkills'] as $missing) {
                    $q->orWhere('title', 'LIKE', "%{$missing}%")
                      ->orWhere('description', 'LIKE', "%{$missing}%");
                }
            })->take(2)->get()->map(function ($t) {
                return [
                    'id' => $t->training_id,
                    'title' => $t->title,
                    'duration_months' => $t->duration_months ?? 1,
                    'training_type' => $t->training_type ?? 'online',
                ];
            });
        }

        $formatted = $this->formatJob($job, $userSkills, $hasApplied ? [$job->job_id] : []);
        $formatted['match_details'] = $match;
        $formatted['recommended_trainings'] = $recommendedTrainings;
        $formatted['can_apply'] = !($jobseeker && $jobseeker->isEmployed());
        $formatted['is_employed'] = $jobseeker ? $jobseeker->isEmployed() : false;
        $formatted['hired_company'] = $jobseeker ? $jobseeker->hired_company : null;

        return response()->json([
            'success' => true,
            'data' => $formatted,
        ]);
    }

    /**
     * Get complete Jobseeker Dashboard with AI Cosine Similarity ranking, Profile Strength, and stats.
     */
    public function getDashboard(Request $request)
    {
        $jobseeker = $this->resolveJobseeker($request);
        $userSkills = $jobseeker ? $this->matchingService->getJobseekerSkills($jobseeker) : [];

        $allJobs = JobPosting::with('employer')->availableForJobseekers()->orderByDesc('job_id')->get();
        
        $rankedJobs = [];
        if ($jobseeker) {
            $rankedJobs = $this->matchingService->rankJobsForJobseeker($allJobs, $jobseeker);
        }

        $appliedJobIds = $jobseeker 
            ? JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->pluck('job_id')->toArray() 
            : [];

        // Format top ranked jobs
        $formattedRanked = [];
        foreach (array_slice($rankedJobs, 0, 10) as $item) {
            $jobModel = $item['job'];
            $f = $this->formatJob($jobModel, $userSkills, $appliedJobIds);
            $f['match_percentage'] = $item['match_percentage'] ?? 0;
            $f['matched_skills'] = $item['matched_skills'] ?? [];
            $f['missing_skills'] = $item['missing_skills'] ?? [];
            $formattedRanked[] = $f;
        }

        $bestMatch = !empty($formattedRanked) ? $formattedRanked[0] : null;

        // Calculate Profile Strength
        $profileStrength = 30;
        if ($jobseeker) {
            if (!empty($jobseeker->first_name) && !empty($jobseeker->last_name)) $profileStrength += 15;
            if (!empty($jobseeker->mobile_number)) $profileStrength += 15;
            if (count($userSkills) >= 3) $profileStrength += 20;
            elseif (count($userSkills) >= 1) $profileStrength += 10;
            if ($jobseeker->details && (!empty($jobseeker->details->education) || !empty($jobseeker->details->work_experience))) $profileStrength += 20;
        }
        $profileStrength = min(100, $profileStrength);

        $stats = [
            'available_jobs' => $allJobs->count(),
            'active_applications' => $jobseeker ? JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->whereIn('status', ['pending', 'reviewed', 'interview', 'offered'])->count() : 0,
            'available_trainings' => TrainingProgram::count(),
            'profile_strength' => $profileStrength,
        ];

        $recentApplications = [];
        if ($jobseeker) {
            $recentApplications = JobApplication::with(['jobPosting.employer'])
                ->where('jobseeker_id', $jobseeker->jobseeker_id)
                ->orderByDesc('application_id')
                ->take(3)
                ->get()
                ->map(function ($app) {
                    return [
                        'id' => (string)$app->application_id,
                        'jobTitle' => $app->jobPosting?->title ?? 'Position',
                        'company' => $app->jobPosting?->employer?->company_name ?? 'Company',
                        'status' => $app->status ?? 'pending',
                        'appliedAt' => $app->created_at ? $app->created_at->toIso8601String() : now()->toIso8601String(),
                    ];
                });
        }

        $recommendedTrainings = TrainingProgram::take(3)->get()->map(function ($t) {
            return [
                'id' => $t->training_id,
                'title' => $t->title,
                'duration_months' => $t->duration_months ?? 1,
                'training_type' => $t->training_type ?? 'online',
            ];
        });

        return response()->json([
            'success' => true,
            'profile_strength' => $profileStrength,
            'stats' => $stats,
            'best_match' => $bestMatch,
            'ranked_jobs' => $formattedRanked,
            'recent_applications' => $recentApplications,
            'recommended_trainings' => $recommendedTrainings,
            'is_employed' => $jobseeker ? $jobseeker->isEmployed() : false,
            'hired_company' => $jobseeker ? $jobseeker->hired_company : null,
        ]);
    }
}
