<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Jobseeker;
use App\Models\Notification;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    /**
     * Format a JobApplication model for API responses.
     */
    private function formatApplication(JobApplication $app): array
    {
        $job = $app->jobPosting;
        $companyName = $job && $job->employer ? $job->employer->company_name : 'DMDP Employer';
        $jobTitle = $job ? $job->title : 'Job Position';

        return [
            'id' => (string)$app->application_id,
            'application_id' => $app->application_id,
            'jobId' => $app->job_id,
            'job_id' => $app->job_id,
            'jobTitle' => $jobTitle,
            'job_title' => $jobTitle,
            'company' => $companyName,
            'company_name' => $companyName,
            'status' => $app->status ?? 'pending',
            'appliedAt' => $app->created_at ? $app->created_at->toIso8601String() : now()->toIso8601String(),
            'applied_at' => $app->created_at ? $app->created_at->toIso8601String() : now()->toIso8601String(),
            'interviewSchedule' => $app->interview_schedule,
            'interview_schedule' => $app->interview_schedule,
            'interviewMode' => $app->interview_mode,
            'interview_mode' => $app->interview_mode,
            'interviewLocation' => $app->interview_location,
            'interview_location' => $app->interview_location,
            'interviewStatus' => $app->interview_status,
            'interview_status' => $app->interview_status,
            'jobseekerResponse' => $app->jobseeker_response,
            'jobseeker_response' => $app->jobseeker_response,
        ];
    }

    /**
     * Get all applications for the authenticated jobseeker.
     */
    public function getAll(Request $request)
    {
        $user = $request->user();
        $jobseeker = $user ? $user->jobseeker : null;

        if (!$jobseeker) {
            return response()->json([]);
        }

        $applications = JobApplication::with(['jobPosting.employer'])
            ->where('jobseeker_id', $jobseeker->jobseeker_id)
            ->orderByDesc('application_id')
            ->get()
            ->map(function ($app) {
                return $this->formatApplication($app);
            });

        return response()->json($applications);
    }

    /**
     * Submit a new job application.
     */
    public function submit(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        $jobId = $request->input('jobId', $request->input('job_id'));
        if (!$jobId) {
            return response()->json([
                'success' => false,
                'message' => 'jobId is required.',
            ], 422);
        }

        $job = JobPosting::find($jobId);
        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job posting not found.',
            ], 404);
        }

        $jobseeker = $user->jobseeker;
        if (!$jobseeker) {
            $jobseeker = Jobseeker::create([
                'user_id' => $user->user_id,
                'first_name' => explode('@', $user->email)[0] ?? 'Jobseeker',
                'email' => $user->email,
            ]);
        }

        // Check if already applied
        $existing = JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)
            ->where('job_id', $jobId)
            ->first();

        if ($existing) {
            $formatted = $this->formatApplication($existing->load(['jobPosting.employer']));
            return response()->json([
                'success' => true,
                'message' => 'You have already applied for this job.',
                'application' => $formatted,
                'data' => $formatted,
            ]);
        }

        $app = JobApplication::create([
            'job_id' => $jobId,
            'jobseeker_id' => $jobseeker->jobseeker_id,
            'status' => 'pending',
            'referred_by_jpo' => false,
        ]);

        // Create notification for user
        Notification::create([
            'user_id' => $user->user_id,
            'title' => 'Application Submitted',
            'message' => "You successfully applied for {$job->title}.",
            'type' => 'approval',
            'is_read' => 0,
            'related_id' => $app->application_id,
        ]);

        $formatted = $this->formatApplication($app->load(['jobPosting.employer']));

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully',
            'application' => $formatted,
            'data' => $formatted,
        ], 201);
    }

    /**
     * Withdraw an application.
     */
    public function withdraw(Request $request, $id)
    {
        $user = $request->user();
        $jobseeker = $user ? $user->jobseeker : null;

        $query = JobApplication::where('application_id', $id);
        if ($jobseeker) {
            $query->where('jobseeker_id', $jobseeker->jobseeker_id);
        }

        $app = $query->first();
        if ($app) {
            $app->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Application withdrawn successfully',
        ]);
    }
}
