<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Jobseeker;
use App\Services\SkillMatchingService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class PublicJobController extends Controller
{
    protected SkillMatchingService $matchingService;

    public function __construct(SkillMatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    /**
     * Display a public listing of all available jobs.
     */
    public function index(Request $request)
    {
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

        // Sorting
        $sort = $request->input('sort', 'latest');
        if ($sort === 'deadline') {
            $query->orderBy('valid_until', 'asc');
        } else {
            $query->orderByDesc('job_id');
        }

        $user = Auth::user();
        $isJobseeker = $user?->role === 'jobseeker';
        $jobseeker = $isJobseeker
            ? Jobseeker::with(['skills', 'details'])->where('user_id', $user->user_id)->first()
            : null;
        $jobs = $query->get();
        $rankedJobs = $this->matchingService->rankJobsForJobseeker($jobs, $jobseeker);

        if ($request->input('sort', 'match') === 'latest') {
            usort($rankedJobs, fn($a, $b) => $b['job']->job_id <=> $a['job']->job_id);
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

        $appliedJobIds = $jobseeker
            ? JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)
            ->get()
            ->reject(function ($application) {
                if (in_array($application->status, ['withdrawn', 'rejected'], true)) {
                    return true;
                }

                return $application->status === 'hired'
                    && $application->resignation_status === 'approved';
            })
            ->pluck('job_id')
            ->all()
            : [];
        $userSkills = $this->matchingService->getJobseekerSkills($jobseeker);
        $selectedJobId = $request->input('selected_job', $request->input('job_id'));
        $selectedItem = null;

        foreach ($rankedJobs as $item) {
            if ($selectedJobId && (string) $item['job_id'] === (string) $selectedJobId) {
                $selectedItem = $item;
                break;
            }
        }

        $selectedItem ??= $rankedJobs[0] ?? null;
        $isApproved = $isJobseeker && (bool) $user->is_approved;
        $jobsRoute = $isJobseeker ? 'jobseeker.jobs' : 'jobs.index';
        $jobShowRoute = $isJobseeker ? 'jobseeker.jobs.show' : 'jobs.show';

        return view('jobs.explore', compact(
            'rankedJobs',
            'selectedItem',
            'userSkills',
            'appliedJobIds',
            'jobseeker',
            'user',
            'isJobseeker',
            'isApproved',
            'jobsRoute',
            'jobShowRoute'
        ));
    }

    /**
     * Return details for a single job posting.
     */
    public function show($id)
    {
        $job = JobPosting::with('employer')->availableForJobseekers()->findOrFail($id);
        $job->skill_tags = $this->matchingService->getJobSkills($job);

        if (request()->wantsJson()) {
            return response()->json($job);
        }

        return redirect()->route('jobs.index', ['job_id' => $id]);
    }
}
