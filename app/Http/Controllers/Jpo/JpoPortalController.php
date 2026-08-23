<?php

namespace App\Http\Controllers\Jpo;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Jobseeker;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JpoPortalController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Metrics
        $pendingJobseekers = JobApplication::where('referred_by_jpo', 0)
            ->whereIn('status', ['pending', 'reviewed'])
            ->count();

        $pendingAccreditations = DB::table('employer_accreditation')
            ->where('status', 'submitted_to_jpo')
            ->count();

        $pendingPlacementReports = DB::table('placement_reports')
            ->where('status', 'submitted_to_jpo')
            ->count();

        $totalReferredJobseekers = JobApplication::where('referred_by_jpo', 1)->count();
        $totalHiredReferred = JobApplication::where('referred_by_jpo', 1)->where('status', 'hired')->count();

        // Recent applications awaiting JPO evaluation
        $recentApplicants = JobApplication::with(['jobseeker.skills', 'jobPosting.employer'])
            ->where('referred_by_jpo', 0)
            ->whereIn('status', ['pending', 'reviewed'])
            ->latest()
            ->take(5)
            ->get();

        // Recent accreditation papers awaiting JPO review
        $recentAccreditations = DB::table('employer_accreditation')
            ->join('employers', 'employer_accreditation.employer_id', '=', 'employers.employer_id')
            ->select('employer_accreditation.*', 'employers.company_name')
            ->where('employer_accreditation.status', 'submitted_to_jpo')
            ->latest('submitted_at')
            ->take(5)
            ->get();

        return view('jpo.dashboard', compact(
            'user', 'pendingJobseekers', 'pendingAccreditations', 
            'pendingPlacementReports', 'totalReferredJobseekers', 
            'totalHiredReferred', 'recentApplicants', 'recentAccreditations'
        ));
    }

    // =========================================================================
    // 1. EVALUATE JOBSEEKER (REFER TO EMPLOYER)
    // =========================================================================

    public function evaluateJobseekers(Request $request)
    {
        $query = JobApplication::with(['jobseeker.skills', 'jobseeker.details', 'jobseeker.socialStatus', 'jobPosting.employer'])
            ->latest();

        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'pending') {
                $query->where('referred_by_jpo', 0);
            } elseif ($request->status === 'referred') {
                $query->where('referred_by_jpo', 1);
            }
        }

        if ($request->has('search') && $request->search !== '') {
            $s = $request->search;
            $query->whereHas('jobseeker', function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $applications = $query->paginate(15)->withQueryString();

        return view('jpo.evaluations.jobseekers', compact('applications'));
    }

    public function referJobseeker(Request $request, $id)
    {
        $application = JobApplication::with(['jobseeker', 'jobPosting.employer.user'])->findOrFail($id);

        $request->validate([
            'recommendation' => 'required|in:refer,training,reject',
            'remarks' => 'nullable|string',
        ]);

        $recommendation = $request->input('recommendation');
        $remarks = $request->input('remarks', 'Evaluated and officially endorsed by DMDP Job Placement Officer (JPO).');

        if ($recommendation === 'refer') {
            $application->update([
                'referred_by_jpo' => 1,
                'status' => 'reviewed',
                'jpo_notes' => $remarks,
                'jpo_evaluated_at' => now(),
            ]);

            // Create or update jpo_assessments
            $jpoProfile = DB::table('user_profiles')->where('user_id', Auth::id())->first();
            $jpoProfileId = $jpoProfile ? $jpoProfile->profile_id : 1;

            DB::table('jpo_assessments')->updateOrInsert(
                ['application_id' => $application->application_id],
                [
                    'jpo_id' => $jpoProfileId,
                    'recommendation' => 'refer',
                    'remarks' => $remarks,
                    'referral_date' => now()->toDateString(),
                    'referral_notes' => $remarks,
                ]
            );

            // Notify Employer that a qualified jobseeker was referred
            $employerUser = $application->jobPosting->employer->user ?? null;
            if ($employerUser) {
                Notification::create([
                    'user_id' => $employerUser->user_id,
                    'title' => 'Qualified Jobseeker Referred by JPO',
                    'message' => "Job Placement Officer referred applicant '{$application->jobseeker->first_name} {$application->jobseeker->last_name}' for position '{$application->jobPosting->title}'.",
                    'type' => 'manual_review',
                    'is_read' => false,
                    'related_id' => $application->application_id,
                ]);
            }

            // Notify Jobseeker
            $jobseekerUser = $application->jobseeker->user ?? null;
            if ($jobseekerUser) {
                Notification::create([
                    'user_id' => $jobseekerUser->user_id,
                    'title' => 'Application Endorsed by JPO!',
                    'message' => "Great news! DMDP Job Placement Officer has reviewed and officially endorsed your application for '{$application->jobPosting->title}' to the employer.",
                    'type' => 'approval',
                    'is_read' => false,
                    'related_id' => $application->application_id,
                ]);
            }

            return redirect()->back()->with('success', "Applicant '{$application->jobseeker->first_name} {$application->jobseeker->last_name}' successfully evaluated and referred to Employer.");
        } elseif ($recommendation === 'training') {
            $application->update([
                'jpo_notes' => 'Recommended for upskilling/training modules: ' . $remarks,
                'jpo_evaluated_at' => now(),
            ]);

            return redirect()->back()->with('info', 'Applicant recommended for training modules.');
        } else {
            $application->update([
                'status' => 'rejected',
                'jpo_notes' => $remarks,
                'jpo_evaluated_at' => now(),
            ]);

            return redirect()->back()->with('info', 'Application marked as not qualified for referral.');
        }
    }

    // =========================================================================
    // 2. EVALUATE ACCREDITATION PAPERS (SEND TO PESD SUPERVISOR)
    // =========================================================================

    public function evaluateAccreditations()
    {
        $accreditations = DB::table('employer_accreditation')
            ->join('employers', 'employer_accreditation.employer_id', '=', 'employers.employer_id')
            ->select('employer_accreditation.*', 'employers.company_name')
            ->latest('submitted_at')
            ->paginate(15);

        return view('jpo.evaluations.accreditations', compact('accreditations'));
    }

    public function recommendAccreditation(Request $request, $id)
    {
        $accreditation = DB::table('employer_accreditation')->where('accreditation_id', $id)->first();
        if (!$accreditation) {
            return redirect()->back()->withErrors(['error' => 'Accreditation record not found.']);
        }

        $request->validate([
            'action' => 'required|in:recommend,reject',
            'remarks' => 'nullable|string',
        ]);

        $action = $request->input('action');
        $remarks = $request->input('remarks', 'Documents verified and recommended by Job Placement Officer.');

        if ($action === 'recommend') {
            DB::table('employer_accreditation')->where('accreditation_id', $id)->update([
                'status' => 'jpo_approved',
                'jpo_reviewed' => 1,
                'jpo_reviewed_at' => now(),
                'jpo_remarks' => $remarks,
                'jpo_id' => Auth::id(),
            ]);

            // Notify all PESD Supervisors
            $supervisors = User::whereIn('role', ['supervisor', 'pesd_supervisor', 'lmo'])->get();
            foreach ($supervisors as $sup) {
                Notification::create([
                    'user_id' => $sup->user_id,
                    'title' => 'Employer Accreditation Ready for Supervisor Review',
                    'message' => "JPO evaluated and recommended accreditation papers for employer ID #{$accreditation->employer_id}. Please review and endorse for Admin.",
                    'type' => 'manual_review',
                    'is_read' => false,
                    'related_id' => $id,
                ]);
            }

            return redirect()->back()->with('success', 'Accreditation papers evaluated and forwarded to PESD Supervisor for endorsement.');
        } else {
            DB::table('employer_accreditation')->where('accreditation_id', $id)->update([
                'status' => 'rejected',
                'jpo_reviewed' => 1,
                'jpo_reviewed_at' => now(),
                'jpo_remarks' => $remarks,
            ]);

            return redirect()->back()->with('info', 'Accreditation papers marked as rejected.');
        }
    }

    // =========================================================================
    // 3. EVALUATE PLACEMENT REPORT (SEND TO ADMIN)
    // =========================================================================

    public function evaluatePlacementReports()
    {
        $reports = DB::table('placement_reports')
            ->join('employers', 'placement_reports.employer_id', '=', 'employers.employer_id')
            ->select('placement_reports.*', 'employers.company_name')
            ->latest('report_month')
            ->paginate(15);

        return view('jpo.evaluations.placement-reports', compact('reports'));
    }

    public function forwardPlacementReport(Request $request, $id)
    {
        $report = DB::table('placement_reports')->where('report_id', $id)->first();
        if (!$report) {
            return redirect()->back()->withErrors(['error' => 'Placement report not found.']);
        }

        $remarks = $request->input('remarks', 'Verified by Job Placement Officer (JPO).');

        DB::table('placement_reports')->where('report_id', $id)->update([
            'status' => 'jpo_evaluated',
            'jpo_evaluated' => 1,
            'jpo_evaluated_at' => now(),
            'jpo_remarks' => $remarks,
        ]);

        // Notify Admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->user_id,
                'title' => 'Placement Report Evaluated by JPO',
                'message' => "JPO evaluated monthly placement report for employer ID #{$report->employer_id}. Pending your final approval.",
                'type' => 'manual_review',
                'is_read' => false,
                'related_id' => $id,
            ]);
        }

        return redirect()->back()->with('success', 'Placement report verified and forwarded to Admin for final authorization.');
    }

    // =========================================================================
    // 4. PROFILE & SETTINGS
    // =========================================================================

    public function profile()
    {
        $user = Auth::user();
        $profile = DB::table('user_profiles')->where('user_id', $user->user_id)->first();

        return view('jpo.profile', compact('user', 'profile'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'full_name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:50',
            'office' => 'nullable|string|max:150',
        ]);

        DB::table('user_profiles')->updateOrInsert(
            ['user_id' => $user->user_id],
            [
                'full_name' => $request->input('full_name'),
                'phone' => $request->input('phone'),
                'office' => $request->input('office', 'Cebu City DMDP - Job Placement Division'),
            ]
        );

        return redirect()->route('jpo.profile')->with('success', 'Profile updated successfully.');
    }

    // =========================================================================
    // 5. NOTIFICATION CENTER
    // =========================================================================

    public function notifications()
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->user_id)
            ->latest('created_at')
            ->paginate(15);

        $unreadCount = Notification::where('user_id', $user->user_id)
            ->where('is_read', false)
            ->count();

        return view('jpo.notifications', compact('notifications', 'user', 'unreadCount'));
    }

    public function markNotificationRead($id)
    {
        Notification::where('notification_id', $id)
            ->where('user_id', Auth::id())
            ->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    public function markAllNotificationsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
