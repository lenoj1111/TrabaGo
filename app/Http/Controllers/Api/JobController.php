<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use Illuminate\Http\Request;

class JobController extends Controller
{
    /**
     * Format a job posting model for API responses.
     */
    private function formatJob(JobPosting $job): array
    {
        $companyName = $job->employer->company_name ?? 'DMDP Partner Employer';
        $location = 'Cebu City, Philippines';
        
        $reqs = [];
        if (!empty($job->qualifications)) {
            $lines = preg_split('/[\r\n]+/', $job->qualifications);
            $reqs = array_values(array_filter(array_map('trim', $lines)));
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
            'skills' => $reqs,
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
        ];
    }

    /**
     * Get all job postings with optional search & filters.
     */
    public function getAll(Request $request)
    {
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

        $jobs = $query->orderByDesc('job_id')->get()->map(function ($job) {
            return $this->formatJob($job);
        });

        return response()->json($jobs);
    }

    /**
     * Get single job posting by ID.
     */
    public function getById($id)
    {
        $job = JobPosting::with('employer')->find($id);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found',
            ], 404);
        }

        $formatted = $this->formatJob($job);

        return response()->json($formatted);
    }
}
