<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $totalJobs = DB::table('job_postings')->count();
        $approvedJobs = DB::table('job_postings')->where('status', 'approved')->count();
        $pendingJobs = DB::table('job_postings')->where('status', 'pending')->count();
        $closedJobs = DB::table('job_postings')->where('status', 'closed')->count();

        $totalEmployers = DB::table('employers')->count();
        $accreditedEmployers = DB::table('employers')->where('is_accredited', 1)->count();

        $totalJobseekers = DB::table('jobseekers')->count();
        $employedJobseekers = DB::table('jobseekers')->where('employment_status', 'employed')->count();

        $totalApplications = DB::table('job_applications')->count();
        $hiredApplications = DB::table('job_applications')->where('status', 'hired')->count();
        $interviewApplications = DB::table('job_applications')->where('status', 'interview')->count();
        $pendingApplications = DB::table('job_applications')->where('status', 'pending')->count();

        $placementRate = $totalJobseekers > 0 ? round(($employedJobseekers / $totalJobseekers) * 100, 1) : 0;
        $hireRate = $totalApplications > 0 ? round(($hiredApplications / $totalApplications) * 100, 1) : 0;

        // Monthly placement trends (last 6 months)
        $monthlyTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            $monthLabel = $monthStart->format('M Y');
            
            $appsCount = DB::table('job_applications')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            $hiresCount = DB::table('job_applications')
                ->where('status', 'hired')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            $monthlyTrends[] = [
                'month' => $monthLabel,
                'applications' => $appsCount,
                'hires' => $hiresCount,
            ];
        }

        // Pending Approval Items for Quick Action
        $pendingApprovalsList = [];
        $pendingJobPostings = DB::table('job_postings')
            ->join('employers', 'job_postings.employer_id', '=', 'employers.employer_id')
            ->select('job_postings.job_id', 'job_postings.title', 'employers.company_name', 'job_postings.created_at', DB::raw("'Job Posting' as item_type"))
            ->where('job_postings.status', 'pending')
            ->latest('job_postings.created_at')
            ->limit(4)
            ->get();

        foreach ($pendingJobPostings as $job) {
            $pendingApprovalsList[] = [
                'type' => 'Job Posting',
                'title' => $job->title,
                'entity' => $job->company_name,
                'date' => $job->created_at,
                'link' => route('admin.job-postings.show', $job->job_id),
            ];
        }

        if (Schema::hasTable('employer_accreditation')) {
            $pendingAccreds = DB::table('employer_accreditation')
                ->join('employers', 'employer_accreditation.employer_id', '=', 'employers.employer_id')
                ->select('employer_accreditation.accreditation_id', 'employers.company_name', 'employer_accreditation.submitted_at', 'employer_accreditation.status')
                ->whereIn('employer_accreditation.status', ['submitted_to_jpo', 'supervisor_approved', 'jpo_approved'])
                ->latest('employer_accreditation.submitted_at')
                ->limit(4)
                ->get();

            foreach ($pendingAccreds as $acc) {
                $pendingApprovalsList[] = [
                    'type' => 'Accreditation',
                    'title' => 'Accreditation Review',
                    'entity' => $acc->company_name,
                    'date' => $acc->submitted_at ?? now(),
                    'link' => route('admin.approvals.index'),
                ];
            }
        }

        // Recent System Hires / Activity
        $recentHires = DB::table('job_applications')
            ->join('job_postings', 'job_applications.job_id', '=', 'job_postings.job_id')
            ->join('employers', 'job_postings.employer_id', '=', 'employers.employer_id')
            ->join('jobseekers', 'job_applications.jobseeker_id', '=', 'jobseekers.jobseeker_id')
            ->select(
                'jobseekers.first_name', 'jobseekers.last_name',
                'job_postings.title as job_title',
                'employers.company_name',
                'job_applications.created_at as hire_time'
            )
            ->where('job_applications.status', 'hired')
            ->latest('job_applications.created_at')
            ->limit(5)
            ->get();

        $stats = [
            'total_jobs' => $totalJobs,
            'approved_jobs' => $approvedJobs,
            'pending_jobs' => $pendingJobs,
            'closed_jobs' => $closedJobs,
            'total_employers' => $totalEmployers,
            'accredited_employers' => $accreditedEmployers,
            'total_jobseekers' => $totalJobseekers,
            'employed_jobseekers' => $employedJobseekers,
            'total_applications' => $totalApplications,
            'hired_applications' => $hiredApplications,
            'interview_applications' => $interviewApplications,
            'pending_applications' => $pendingApplications,
            'placement_rate' => $placementRate,
            'hire_rate' => $hireRate,
        ];

        return view('admin.dashboard', compact(
            'stats',
            'monthlyTrends',
            'pendingApprovalsList',
            'recentHires'
        ));
    }

    /**
     * Show Admin Profile.
     */
    public function profile()
    {
        $user = Auth::user();
        $profile = DB::table('user_profiles')->where('user_id', $user->user_id)->first();
        return view('admin.profile', compact('user', 'profile'));
    }

    /**
     * Update Admin Profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'full_name' => 'required|string|max:150',
            'email' => 'nullable|email|unique:users,email,' . $user->user_id . ',user_id',
            'phone' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:150',
            'office' => 'nullable|string|max:150',
        ]);

        if ($request->filled('email') && $request->email !== $user->email) {
            DB::table('users')->where('user_id', $user->user_id)->update([
                'email' => $request->email,
                'updated_at' => now(),
            ]);
        }

        DB::table('user_profiles')->updateOrInsert(
            ['user_id' => $user->user_id],
            [
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'position' => $request->position,
                'department' => $request->department,
                'office' => $request->office,
                'updated_at' => now(),
            ]
        );

        return back()->with('success', 'Admin profile updated successfully.');
    }

    /**
     * Reset / Change Admin Password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'The provided current password does not match your current credentials.',
            'password.confirmed' => 'The new password confirmation does not match.',
            'password.min' => 'The new password must be at least 8 characters.',
        ]);

        $user = Auth::user();
        DB::table('users')->where('user_id', $user->user_id)->update([
            'password' => Hash::make($request->password),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Admin password has been reset successfully.');
    }

    /**
     * Labor Market Insights & Analytics (Admin handling LMO responsibilities).
     */
    public function analytics()
    {
        $skillDistribution = DB::table('jobseeker_skills')
            ->select('skill_name', DB::raw('COUNT(*) as total'))
            ->groupBy('skill_name')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $employmentByStatus = DB::table('jobseekers')
            ->select(DB::raw("COALESCE(employment_status, 'Looking for job') as status_name"), DB::raw('COUNT(*) as total'))
            ->groupBy('employment_status')
            ->get();

        $driver = DB::getDriverName();
        $dateExpr = match ($driver) {
            'sqlsrv' => "SUBSTRING(CAST(hired_date AS VARCHAR(10)), 1, 7)",
            'sqlite' => "SUBSTR(hired_date, 1, 7)",
            'pgsql'  => "TO_CHAR(hired_date, 'YYYY-MM')",
            default  => "SUBSTRING(CAST(hired_date AS CHAR(10)), 1, 7)",
        };

        $monthlyHiredTrends = DB::table('job_applications')
            ->where('status', 'hired')
            ->whereNotNull('hired_date')
            ->select(DB::raw("{$dateExpr} as hire_month"), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw($dateExpr))
            ->orderBy('hire_month', 'desc')
            ->limit(6)
            ->get();

        return view('admin.analytics', compact('skillDistribution', 'employmentByStatus', 'monthlyHiredTrends'));
    }

    /**
     * Admin Notification Center.
     */
    public function notifications()
    {
        $user = Auth::user();

        // Ensure system approval alerts exist for the admin
        $pendingJobs = Schema::hasTable('job_postings') ? DB::table('job_postings')->where('status', 'pending')->count() : 0;
        
        $pendingAccreditations = 0;
        if (Schema::hasTable('employer_accreditation')) {
            $pendingAccreditations = DB::table('employer_accreditation')
                ->whereIn('status', ['supervisor_approved', 'jpo_approved', 'submitted_to_jpo'])
                ->count();
        }

        $pendingReports = 0;
        if (Schema::hasTable('placement_reports')) {
            $pendingReports = DB::table('placement_reports')
                ->whereIn('status', ['jpo_evaluated', 'submitted_to_jpo', 'pending'])
                ->count();
        }

        // If there are pending approvals but no unread notifications for them, seed helpful system alerts
        if ($pendingJobs > 0) {
            $hasJobAlert = DB::table('notifications')
                ->where('user_id', $user->user_id)
                ->where('type', 'job_approval')
                ->where('is_read', 0)
                ->exists();

            if (!$hasJobAlert) {
                DB::table('notifications')->insert([
                    'user_id' => $user->user_id,
                    'title' => 'Job Postings Awaiting Authorization',
                    'message' => "There are {$pendingJobs} employer job vacancy postings waiting for administrative review and approval.",
                    'type' => 'job_approval',
                    'is_read' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        if ($pendingAccreditations > 0) {
            $hasAccAlert = DB::table('notifications')
                ->where('user_id', $user->user_id)
                ->where('type', 'accreditation')
                ->where('is_read', 0)
                ->exists();

            if (!$hasAccAlert) {
                DB::table('notifications')->insert([
                    'user_id' => $user->user_id,
                    'title' => 'Accreditation Endorsement from PESD Supervisor',
                    'message' => "PESD Supervisor has endorsed {$pendingAccreditations} employer accreditation file(s) for final administrative grant.",
                    'type' => 'accreditation',
                    'is_read' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        if ($pendingReports > 0) {
            $hasReportAlert = DB::table('notifications')
                ->where('user_id', $user->user_id)
                ->where('type', 'placement_report')
                ->where('is_read', 0)
                ->exists();

            if (!$hasReportAlert) {
                DB::table('notifications')->insert([
                    'user_id' => $user->user_id,
                    'title' => 'Monthly Placement Report Verified by JPO',
                    'message' => "{$pendingReports} monthly employer placement report(s) evaluated and forwarded by JPO for official archival.",
                    'type' => 'placement_report',
                    'is_read' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $notifications = DB::table('notifications')
            ->where('user_id', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $unreadCount = DB::table('notifications')
            ->where('user_id', $user->user_id)
            ->where('is_read', 0)
            ->count();

        return view('admin.notifications', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark single notification as read.
     */
    public function markNotificationRead($id)
    {
        DB::table('notifications')
            ->where('notification_id', $id)
            ->where('user_id', Auth::id())
            ->update([
                'is_read' => 1,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsRead()
    {
        DB::table('notifications')
            ->where('user_id', Auth::id())
            ->update([
                'is_read' => 1,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'All notifications marked as read.');
    }
}