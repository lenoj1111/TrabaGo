<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics from your database
        $stats = [
            'total_jobs' => DB::table('job_postings')->count(),
            'pending_jobs' => DB::table('job_postings')->where('status', 'pending')->count(),
            'total_employers' => DB::table('employers')->count(),
            'total_jobseekers' => DB::table('jobseekers')->count(),
            'total_applications' => DB::table('job_applications')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
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
        $request->validate([
            'full_name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:150',
            'office' => 'nullable|string|max:150',
        ]);

        $user = Auth::user();
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