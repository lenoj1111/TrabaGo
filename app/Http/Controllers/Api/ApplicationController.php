<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Jobseeker;
use App\Models\Notification;
use App\Services\SkillMatchingService;
use App\Traits\ResolvesJobseeker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    use ResolvesJobseeker;

    protected SkillMatchingService $matchingService;

    public function __construct(SkillMatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    /**
     * Format a JobApplication model for API responses.
     */
    private function formatApplication(JobApplication $app, ?array $userSkills = null): array
    {
        $job = $app->jobPosting;
        $companyName = $job && $job->employer ? $job->employer->company_name : 'DMDP Employer';
        $jobTitle = $job ? $job->title : 'Job Position';

        $match = null;
        if ($job && $userSkills !== null) {
            $jobSkills = $this->matchingService->getJobSkills($job);
            $match = $this->matchingService->calculateMatch($userSkills, $jobSkills);
        }

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
            'match_details' => $match,
            'match_percentage' => $match['score'] ?? null,
            'hired_date' => $app->hired_date,
            'resignation_status' => $app->resignation_status,
            'resignation_reason' => $app->resignation_reason,
        ];
    }

    /**
     * Get all applications for the authenticated jobseeker with status counts.
     */
    public function getAll(Request $request)
    {
        $jobseeker = $this->resolveJobseeker($request, false);

        if (!$jobseeker) {
            return response()->json([
                'applications' => [],
                'counts' => [
                    'all' => 0, 'offered' => 0, 'pending' => 0, 'reviewed' => 0, 'interview' => 0, 'hired' => 0, 'rejected' => 0
                ]
            ]);
        }

        $userSkills = $this->matchingService->getJobseekerSkills($jobseeker);
        $filter = $request->input('status', 'all');

        $query = JobApplication::with(['jobPosting.employer'])
            ->where('jobseeker_id', $jobseeker->jobseeker_id);

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        $applications = $query->orderByDesc('application_id')->get()->map(function ($app) use ($userSkills) {
            return $this->formatApplication($app, $userSkills);
        });

        // Counts by status exactly like web
        $counts = [
            'all' => JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->count(),
            'offered' => JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->where('status', 'offered')->count(),
            'pending' => JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->where('status', 'pending')->count(),
            'reviewed' => JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->where('status', 'reviewed')->count(),
            'interview' => JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->where('status', 'interview')->count(),
            'hired' => JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->where('status', 'hired')->count(),
            'rejected' => JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)->where('status', 'rejected')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $applications,
            'applications' => $applications,
            'counts' => $counts,
            'is_employed' => $jobseeker->isEmployed(),
            'hired_company' => $jobseeker->hired_company,
        ]);
    }

    /**
     * Submit a new job application.
     */
    public function submit(Request $request)
    {
        $user = $this->resolveUser($request);
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

        $job = JobPosting::availableForJobseekers()->find($jobId);
        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job posting is no longer available.',
            ], 404);
        }

        if (!$user->is_approved) {
            return response()->json([
                'success' => false,
                'message' => 'Your jobseeker account is pending approval by DMDP. You cannot apply for a job posting until your account is approved.',
            ], 403);
        }

        $jobseeker = $this->resolveJobseeker($request, true);

        // Employment Flexibility: Jobseekers remain "Open to all" for opportunities even if currently employed.

        // Profile and Documents Completeness Check (Enforced on Mobile & Web)
        $missingRequirements = $jobseeker->getMissingRequirements();
        if (!empty($missingRequirements)) {
            $missingList = implode(', ', $missingRequirements);
            return response()->json([
                'success' => false,
                'message' => "Application blocked: You cannot apply for a job posting until your profile details and required documents are completed. Missing: {$missingList}. Please complete your profile and upload required documents in the Document Vault.",
                'missing_requirements' => array_values($missingRequirements),
            ], 422);
        }

        // Check if already applied
        $existing = JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)
            ->where('job_id', $jobId)
            ->first();

        if ($existing) {
            $formatted = $this->formatApplication($existing->load(['jobPosting.employer']));
            return response()->json([
                'success' => true,
                'message' => 'You have already submitted an application for this position.',
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
            'message' => "You successfully applied for {$job->title} at " . ($job->employer->company_name ?? 'DMDP Employer') . ".",
            'type' => 'approval',
            'is_read' => false,
            'related_id' => $app->application_id,
        ]);

        $formatted = $this->formatApplication($app->load(['jobPosting.employer']));

        return response()->json([
            'success' => true,
            'message' => "Your application for '{$job->title}' has been submitted successfully!",
            'application' => $formatted,
            'data' => $formatted,
        ], 201);
    }

    /**
     * Accept a job offer.
     */
    public function acceptOffer(Request $request, $id)
    {
        $user = $this->resolveUser($request);
        $jobseeker = $this->resolveJobseeker($request, false);
        if (!$jobseeker || !$user) {
            return response()->json(['success' => false, 'message' => 'Jobseeker not found'], 404);
        }

        $application = JobApplication::with(['jobPosting.employer.user'])
            ->where('jobseeker_id', $jobseeker->jobseeker_id)
            ->find($id);

        if (!$application || $application->status !== 'offered') {
            return response()->json(['success' => false, 'message' => 'This application does not have an active job offer to accept.'], 400);
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
                'message' => "Candidate {$jobseeker->first_name} {$jobseeker->last_name} has accepted your job offer for '{$jobTitle}'.",
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
            $otherApp->update(['status' => 'withdrawn']);
        }

        // 5. Notify the jobseeker
        Notification::create([
            'user_id' => $user->user_id,
            'title' => '🎉 Congratulations on Your New Job!',
            'message' => "You have officially accepted the job offer for '{$jobTitle}' at {$companyName}. Your employment status is now active!",
            'type' => 'approval',
            'is_read' => false,
            'related_id' => $application->application_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Congratulations! You have accepted the job offer from {$companyName}. You are now officially hired!",
            'hired_company' => $companyName,
        ]);
    }

    /**
     * Decline a job offer.
     */
    public function declineOffer(Request $request, $id)
    {
        $jobseeker = $this->resolveJobseeker($request, false);
        if (!$jobseeker) {
            return response()->json(['success' => false, 'message' => 'Jobseeker not found'], 404);
        }

        $application = JobApplication::with(['jobPosting.employer.user'])
            ->where('jobseeker_id', $jobseeker->jobseeker_id)
            ->find($id);

        if (!$application || $application->status !== 'offered') {
            return response()->json(['success' => false, 'message' => 'This application does not have an active job offer to decline.'], 400);
        }

        $reason = $request->input('reason', $request->input('decline_reason', 'Candidate declined the offer.'));
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

        return response()->json([
            'success' => true,
            'message' => "You have declined the job offer for '{$jobTitle}'. Your other applications remain active.",
        ]);
    }

    /**
     * Request resignation for currently hired jobseeker.
     */
    public function requestResignation(Request $request)
    {
        $jobseeker = $this->resolveJobseeker($request, false);
        if (!$jobseeker) {
            return response()->json(['success' => false, 'message' => 'Jobseeker not found'], 404);
        }

        $hiredApplication = JobApplication::where('jobseeker_id', $jobseeker->jobseeker_id)
            ->where('status', 'hired')
            ->latest('application_id')
            ->first();

        if (!$hiredApplication) {
            return response()->json(['success' => false, 'message' => 'You do not have an active employment record to resign from.'], 400);
        }

        $reason = $request->input('reason', '');
        if (empty($reason)) {
            return response()->json(['success' => false, 'message' => 'Please provide a reason for requesting resignation.'], 422);
        }

        $hiredApplication->update([
            'resignation_status' => 'requested',
            'resignation_reason' => $reason,
            'resignation_requested_at' => now(),
        ]);

        $employerUser = $hiredApplication->jobPosting?->employer?->user;
        if ($employerUser) {
            Notification::create([
                'user_id' => $employerUser->user_id,
                'title' => 'Resignation Request Submitted',
                'message' => "Jobseeker {$jobseeker->first_name} {$jobseeker->last_name} has requested resignation from '{$hiredApplication->jobPosting?->title}'. Reason: {$reason}",
                'type' => 'manual_review',
                'is_read' => false,
                'related_id' => $hiredApplication->application_id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Your resignation request has been sent to your employer for approval.',
        ]);
    }

    /**
     * Withdraw an application.
     */
    public function withdraw(Request $request, $id)
    {
        $jobseeker = $this->resolveJobseeker($request, false);

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
