<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupervisorPortalController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Metrics
        $pendingSupervisorReviews = DB::table('employer_accreditation')
            ->where('status', 'jpo_approved')
            ->count();

        $endorsedBySupervisor = DB::table('employer_accreditation')
            ->where('supervisor_approved', 1)
            ->count();

        $totalAccreditedEmployers = DB::table('employers')
            ->where('is_accredited', 1)
            ->count();

        // Pending queue from JPO
        $pendingQueue = DB::table('employer_accreditation')
            ->join('employers', 'employer_accreditation.employer_id', '=', 'employers.employer_id')
            ->select('employer_accreditation.*', 'employers.company_name')
            ->where('employer_accreditation.status', 'jpo_approved')
            ->latest('jpo_reviewed_at')
            ->take(5)
            ->get();

        return view('supervisor.dashboard', compact(
            'user', 'pendingSupervisorReviews', 'endorsedBySupervisor', 
            'totalAccreditedEmployers', 'pendingQueue'
        ));
    }

    // =========================================================================
    // ACCREDITATION PAPERS EVALUATION (SEND TO ADMIN)
    // =========================================================================

    public function accreditations(Request $request)
    {
        $query = DB::table('employer_accreditation')
            ->join('employers', 'employer_accreditation.employer_id', '=', 'employers.employer_id')
            ->select('employer_accreditation.*', 'employers.company_name', 'employers.is_accredited as employer_accredited_flag')
            ->latest('submitted_at');

        if ($request->has('filter') && $request->filter !== '') {
            if ($request->filter === 'pending') {
                $query->where('employer_accreditation.status', 'jpo_approved');
            } elseif ($request->filter === 'endorsed') {
                $query->where('employer_accreditation.supervisor_approved', 1);
            }
        }

        $accreditations = $query->paginate(15)->withQueryString();

        return view('supervisor.accreditations.index', compact('accreditations'));
    }

    public function approveAccreditation(Request $request, $id)
    {
        $accreditation = DB::table('employer_accreditation')->where('accreditation_id', $id)->first();
        if (!$accreditation) {
            return redirect()->back()->withErrors(['error' => 'Accreditation record not found.']);
        }

        $action = $request->input('action', 'approve');
        $remarks = $request->input('remarks', 'Endorsed by PESD Supervisor for official Admin accreditation.');

        if ($action === 'approve') {
            DB::table('employer_accreditation')->where('accreditation_id', $id)->update([
                'status' => 'supervisor_approved',
                'supervisor_approved' => 1,
                'supervisor_approved_at' => now(),
                'supervisor_remarks' => $remarks,
                'supervisor_id' => Auth::id(),
            ]);

            // Notify Admins of supervisor endorsement ready for final authorization
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->user_id,
                    'title' => 'Employer Accreditation Endorsed by PESD Supervisor',
                    'message' => "PESD Supervisor endorsed accreditation papers for employer ID #{$accreditation->employer_id}. Pending your final official accreditation.",
                    'type' => 'manual_review',
                    'is_read' => false,
                    'related_id' => $id,
                ]);
            }

            return redirect()->back()->with('success', 'Accreditation papers endorsed successfully and forwarded to the Admin for final processing.');
        } else {
            DB::table('employer_accreditation')->where('accreditation_id', $id)->update([
                'status' => 'rejected',
                'supervisor_approved' => 0,
                'supervisor_remarks' => $remarks,
            ]);

            return redirect()->back()->with('info', 'Accreditation papers returned / rejected.');
        }
    }

    // =========================================================================
    // PROFILE & SETTINGS
    // =========================================================================

    public function profile()
    {
        $user = Auth::user();
        $profile = DB::table('user_profiles')->where('user_id', $user->user_id)->first();

        return view('supervisor.profile', compact('user', 'profile'));
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
                'office' => $request->input('office', 'Public Employment Service Division (PESD) - Cebu City'),
            ]
        );

        return redirect()->route('supervisor.profile')->with('success', 'Supervisor profile updated successfully.');
    }

    // =========================================================================
    // NOTIFICATION CENTER
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

        return view('supervisor.notifications', compact('notifications', 'user', 'unreadCount'));
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
