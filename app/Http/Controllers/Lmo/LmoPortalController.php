<?php

namespace App\Http\Controllers\Lmo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LmoPortalController extends Controller
{
    /**
     * Figure 13: Labor Market Information Officer Dashboard.
     */
    public function dashboard()
    {
        // 1. Overall Jobseeker Workflow Supervision Funnel (Figure 13)
        $totalJobseekers = DB::table('jobseekers')->count();
        $inTraining = DB::table('training_enrollments')->where('status', 'in_progress')->distinct('jobseeker_id')->count('jobseeker_id');
        $certifiedSkillsCount = DB::table('training_enrollments')->where('certificate_issued', 1)->distinct('jobseeker_id')->count('jobseeker_id');
        $totalApplications = DB::table('job_applications')->count();
        $jpoEvaluated = DB::table('job_applications')->where('referred_by_jpo', 1)->count();
        $interviewsScheduled = DB::table('job_applications')->where('status', 'interview')->count();
        $hiredPlacements = DB::table('job_applications')->where('status', 'hired')->count();

        // 2. Labor Market Demographics
        $pwdJobseekers = DB::table('social_status')->where('is_pwd', 1)->count();
        $fourPsJobseekers = DB::table('social_status')->where('is_4ps', 1)->count();

        // 3. Labor Market Skills Supply (From jobseeker_skills)
        $topSupplySkills = DB::table('jobseeker_skills')
            ->select('skill_name', DB::raw('COUNT(*) as total_holders'))
            ->groupBy('skill_name')
            ->orderBy('total_holders', 'desc')
            ->limit(6)
            ->get();

        // 4. Labor Market Skills Demand (From approved job_postings)
        $totalActiveJobs = DB::table('job_postings')->where('status', 'approved')->count();
        $totalEmployers = DB::table('employers')->count();

        // Recent jobseeker workflow activities
        $recentWorkflows = DB::table('jobseekers')
            ->leftJoin('users', 'jobseekers.user_id', '=', 'users.user_id')
            ->select(
                'jobseekers.*',
                'users.email as user_email',
                DB::raw('(SELECT COUNT(*) FROM job_applications WHERE jobseeker_id = jobseekers.jobseeker_id) as apps_count'),
                DB::raw('(SELECT COUNT(*) FROM training_enrollments WHERE jobseeker_id = jobseekers.jobseeker_id) as trainings_count'),
                DB::raw('(SELECT COUNT(*) FROM jobseeker_skills WHERE jobseeker_id = jobseekers.jobseeker_id) as skills_count'),
                DB::raw("(SELECT COUNT(*) FROM job_applications WHERE jobseeker_id = jobseekers.jobseeker_id AND status = 'hired') as hired_count")
            )
            ->orderBy('jobseekers.jobseeker_id', 'desc')
            ->limit(8)
            ->get();

        return view('lmo.dashboard', compact(
            'totalJobseekers',
            'inTraining',
            'certifiedSkillsCount',
            'totalApplications',
            'jpoEvaluated',
            'interviewsScheduled',
            'hiredPlacements',
            'pwdJobseekers',
            'fourPsJobseekers',
            'topSupplySkills',
            'totalActiveJobs',
            'totalEmployers',
            'recentWorkflows'
        ));
    }

    /**
     * Figure 13: Supervise Jobseeker Workflow (Monitoring training, applications, evaluations).
     */
    public function superviseJobseekers(Request $request)
    {
        $query = DB::table('jobseekers')
            ->leftJoin('users', 'jobseekers.user_id', '=', 'users.user_id')
            ->leftJoin('social_status', 'jobseekers.jobseeker_id', '=', 'social_status.jobseeker_id')
            ->select(
                'jobseekers.*',
                'users.email as user_email',
                'social_status.is_pwd',
                'social_status.pwd_type',
                'social_status.is_4ps',
                DB::raw('(SELECT COUNT(*) FROM job_applications WHERE jobseeker_id = jobseekers.jobseeker_id) as applications_count'),
                DB::raw('(SELECT COUNT(*) FROM training_enrollments WHERE jobseeker_id = jobseekers.jobseeker_id) as trainings_count'),
                DB::raw('(SELECT COUNT(*) FROM training_enrollments WHERE jobseeker_id = jobseekers.jobseeker_id AND certificate_issued = 1) as certs_count'),
                DB::raw('(SELECT COUNT(*) FROM job_applications WHERE jobseeker_id = jobseekers.jobseeker_id AND referred_by_jpo = 1) as jpo_referrals_count'),
                DB::raw("(SELECT COUNT(*) FROM job_applications WHERE jobseeker_id = jobseekers.jobseeker_id AND status = 'hired') as hired_count")
            );

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('jobseekers.first_name', 'LIKE', "%{$search}%")
                  ->orWhere('jobseekers.last_name', 'LIKE', "%{$search}%")
                  ->orWhere('users.email', 'LIKE', "%{$search}%")
                  ->orWhere('jobseekers.employment_status', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('stage')) {
            $stage = $request->stage;
            if ($stage === 'in_training') {
                $query->whereRaw("(SELECT COUNT(*) FROM training_enrollments WHERE jobseeker_id = jobseekers.jobseeker_id AND status = 'in_progress') > 0");
            } elseif ($stage === 'certified') {
                $query->whereRaw('(SELECT COUNT(*) FROM training_enrollments WHERE jobseeker_id = jobseekers.jobseeker_id AND certificate_issued = 1) > 0');
            } elseif ($stage === 'applied') {
                $query->whereRaw('(SELECT COUNT(*) FROM job_applications WHERE jobseeker_id = jobseekers.jobseeker_id) > 0');
            } elseif ($stage === 'jpo_referred') {
                $query->whereRaw('(SELECT COUNT(*) FROM job_applications WHERE jobseeker_id = jobseekers.jobseeker_id AND referred_by_jpo = 1) > 0');
            } elseif ($stage === 'hired') {
                $query->whereRaw("(SELECT COUNT(*) FROM job_applications WHERE jobseeker_id = jobseekers.jobseeker_id AND status = 'hired') > 0");
            }
        }

        if ($request->filled('pwd_only') && $request->pwd_only == 1) {
            $query->where('social_status.is_pwd', 1);
        }

        $jobseekers = $query->orderBy('jobseekers.jobseeker_id', 'desc')->paginate(15)->withQueryString();

        return view('lmo.jobseekers.supervise', compact('jobseekers'));
    }

    /**
     * Labor Market Insights & Reports.
     */
    public function marketInsights()
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

        return view('lmo.analytics.index', compact('skillDistribution', 'employmentByStatus', 'monthlyHiredTrends'));
    }

    /**
     * LMO Profile.
     */
    public function profile()
    {
        $user = Auth::user();
        $profile = DB::table('user_profiles')->where('user_id', $user->user_id)->first();
        return view('lmo.profile', compact('user', 'profile'));
    }

    /**
     * Update LMO Profile.
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:50',
            'office' => 'nullable|string|max:200',
        ]);

        $user = Auth::user();
        DB::table('user_profiles')->updateOrInsert(
            ['user_id' => $user->user_id],
            [
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'office' => $request->office,
                'updated_at' => now(),
            ]
        );

        return back()->with('success', 'LMO profile updated successfully.');
    }

    /**
     * Notifications.
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

        return view('lmo.notifications', compact('notifications', 'unreadCount', 'user'));
    }

    public function markNotificationRead($id)
    {
        DB::table('notifications')
            ->where('notification_id', $id)
            ->where('user_id', Auth::id())
            ->update(['is_read' => 1]);

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllNotificationsRead()
    {
        DB::table('notifications')
            ->where('user_id', Auth::id())
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
