<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Services\SkillMatchingService;
use Illuminate\Http\Request;
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

        $jobs = $query->paginate(12)->withQueryString();

        // Process skill tags for each job
        $jobs->getCollection()->transform(function ($job) {
            $job->skill_tags = $this->matchingService->getJobSkills($job);
            return $job;
        });

        $user = Auth::user();
        $isJobseeker = $user && $user->role === 'jobseeker';
        $selectedJobId = $request->input('job_id');

        return view('jobs.explore', compact('jobs', 'user', 'isJobseeker', 'selectedJobId'));
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
