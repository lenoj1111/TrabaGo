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
        $jobStats = DB::table('job_postings')
            ->selectRaw("
                COUNT(*) as total_jobs,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_jobs,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_jobs,
                SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed_jobs
            ")->first();

        $totalJobs = (int) ($jobStats->total_jobs ?? 0);
        $approvedJobs = (int) ($jobStats->approved_jobs ?? 0);
        $pendingJobs = (int) ($jobStats->pending_jobs ?? 0);
        $closedJobs = (int) ($jobStats->closed_jobs ?? 0);

        $empStats = DB::table('employers')
            ->selectRaw("
                COUNT(*) as total_employers,
                SUM(CASE WHEN is_accredited = 1 THEN 1 ELSE 0 END) as accredited_employers
            ")->first();

        $totalEmployers = (int) ($empStats->total_employers ?? 0);
        $accreditedEmployers = (int) ($empStats->accredited_employers ?? 0);

        $seekerStats = DB::table('jobseekers')
            ->selectRaw("
                COUNT(*) as total_jobseekers,
                SUM(CASE WHEN employment_status = 'employed' THEN 1 ELSE 0 END) as employed_jobseekers
            ")->first();

        $totalJobseekers = (int) ($seekerStats->total_jobseekers ?? 0);
        $employedJobseekers = (int) ($seekerStats->employed_jobseekers ?? 0);

        $appStats = DB::table('job_applications')
            ->selectRaw("
                COUNT(*) as total_applications,
                SUM(CASE WHEN status = 'hired' THEN 1 ELSE 0 END) as hired_applications,
                SUM(CASE WHEN status = 'interview' THEN 1 ELSE 0 END) as interview_applications,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_applications
            ")->first();

        $totalApplications = (int) ($appStats->total_applications ?? 0);
        $hiredApplications = (int) ($appStats->hired_applications ?? 0);
        $interviewApplications = (int) ($appStats->interview_applications ?? 0);
        $pendingApplications = (int) ($appStats->pending_applications ?? 0);

        $placementRate = $totalJobseekers > 0 ? round(($employedJobseekers / $totalJobseekers) * 100, 1) : 0;
        $hireRate = $totalApplications > 0 ? round(($hiredApplications / $totalApplications) * 100, 1) : 0;

        // Monthly placement trends (Single query for 6 months instead of 12 queries)
        $sixMonthsAgo = now()->subMonths(5)->startOfMonth();
        $driver = DB::getDriverName();
        $dateExpr = match ($driver) {
            'sqlsrv' => "SUBSTRING(CAST(created_at AS VARCHAR(10)), 1, 7)",
            'sqlite' => "SUBSTR(created_at, 1, 7)",
            'pgsql'  => "TO_CHAR(created_at, 'YYYY-MM')",
            default  => "SUBSTRING(CAST(created_at AS CHAR(10)), 1, 7)",
        };

        $rawMonthly = DB::table('job_applications')
            ->where('created_at', '>=', $sixMonthsAgo)
            ->selectRaw("
                {$dateExpr} as m_key,
                COUNT(*) as apps_count,
                SUM(CASE WHEN status = 'hired' THEN 1 ELSE 0 END) as hires_count
            ")
            ->groupBy(DB::raw($dateExpr))
            ->get()
            ->keyBy('m_key');

        $monthlyTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $k = $m->format('Y-m');
            $rec = $rawMonthly[$k] ?? null;
            $monthlyTrends[] = [
                'month' => $m->format('M Y'),
                'applications' => (int) ($rec->apps_count ?? 0),
                'hires' => (int) ($rec->hires_count ?? 0),
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

        $pendingJobseekers = DB::table('jobseekers')
            ->join('users', 'jobseekers.user_id', '=', 'users.user_id')
            ->select('jobseekers.first_name', 'jobseekers.last_name', 'users.email', 'users.created_at')
            ->where('users.role', 'jobseeker')
            ->where(function ($q) {
                $q->where('users.is_approved', 0)
                  ->orWhere('users.account_status', 'pending_review')
                  ->orWhere('users.account_status', 'unverified')
                  ->orWhereNull('users.account_status');
            })
            ->latest('users.created_at')
            ->limit(4)
            ->get();

        foreach ($pendingJobseekers as $js) {
            $pendingApprovalsList[] = [
                'type' => 'Jobseeker Identity',
                'title' => 'Government ID Verification',
                'entity' => trim(($js->first_name ?? '') . ' ' . ($js->last_name ?? '')) ?: $js->email,
                'date' => $js->created_at ?? now(),
                'link' => route('admin.approvals.index'),
            ];
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
     * Labor Market Insights & Analytics (Removed / Redirected to Dashboard).
     */
    public function analytics()
    {
        return redirect()->route('admin.dashboard');
    }

    /**
     * Admin Notification Center.
     */
    public function notifications()
    {
        $user = Auth::user();

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