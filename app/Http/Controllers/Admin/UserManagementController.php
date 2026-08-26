<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class UserManagementController extends Controller
{
    /**
     * Display a listing of all users
     */
    public function index(Request $request)
    {
        $query = DB::table('users')
            ->leftJoin('user_profiles', 'users.user_id', '=', 'user_profiles.user_id')
            ->leftJoin('employers', 'users.user_id', '=', 'employers.user_id')
            ->leftJoin('jobseekers', 'users.user_id', '=', 'jobseekers.user_id')
            ->select(
                'users.*',
                'user_profiles.full_name',
                'user_profiles.position',
                'user_profiles.department',
                'employers.company_name',
                'jobseekers.first_name',
                'jobseekers.last_name'
            );

        // Filter by role
        if ($request->has('role') && $request->role != '') {
            $query->where('users.role', $request->role);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('users.status', $request->status);
        }

        // Search by name or email
        if ($request->has('search') && $request->search != '') {
            $search = '%' . $request->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('users.email', 'like', $search)
                  ->orWhere('user_profiles.full_name', 'like', $search)
                  ->orWhere('employers.company_name', 'like', $search)
                  ->orWhere('jobseekers.first_name', 'like', $search)
                  ->orWhere('jobseekers.last_name', 'like', $search);
            });
        }

        $users = $query->orderBy('users.created_at', 'desc')->paginate(15);

        // Get counts for dashboard
        $counts = [
            'total' => DB::table('users')->count(),
            'active' => DB::table('users')->where('status', 'active')->count(),
            'inactive' => DB::table('users')->where('status', 'inactive')->count(),
            'pending' => DB::table('users')->where('is_approved', 0)->count(),
            'admins' => DB::table('users')->where('role', 'admin')->count(),
            'jpos' => DB::table('users')->where('role', 'jpo')->count(),
            'trainers' => DB::table('users')->where('role', 'trainer')->count(),
            'lmos' => DB::table('users')->where('role', 'lmo')->count(),
            'employers' => DB::table('users')->where('role', 'employer')->count(),
            'jobseekers' => DB::table('users')->where('role', 'jobseeker')->count(),
        ];

        $roles = ['admin', 'jpo', 'trainer', 'lmo', 'employer', 'jobseeker'];
        $statuses = ['active', 'inactive'];

        return view('admin.users.index', compact('users', 'counts', 'roles', 'statuses'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = ['admin', 'jpo', 'trainer', 'lmo'];
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:admin,jpo,trainer,lmo',
            'full_name' => 'required|string|max:150',
            'position' => 'required|string|max:100',
            'department' => 'nullable|string|max:150',
            'office' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:50',
            'specialization' => 'nullable|string|max:150',
            'trainer_type' => 'nullable|in:dmdp,partner',
            'partner_institution' => 'nullable|string|max:255',
            'is_approved' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // 1. Create user account
            $user = DB::table('users')->insertGetId([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'status' => 'active',
                'is_approved' => $request->is_approved ?? 1,
                'created_at' => Carbon::now(),
            ]);

            // 2. Create user profile
            DB::table('user_profiles')->insert([
                'user_id' => $user,
                'full_name' => $request->full_name,
                'position' => $request->position,
                'department' => $request->department,
                'office' => $request->office,
                'phone' => $request->phone,
                'specialization' => $request->specialization,
                'trainer_type' => $request->trainer_type ?? 'dmdp',
                'partner_institution' => $request->partner_institution,
                'is_trainer_approved' => $request->role === 'trainer' ? ($request->is_approved ?? 1) : 0,
                'created_at' => Carbon::now(),
            ]);

            // 3. Create notification
            $this->createNotification($user, 'New Employee Account Created', 
                "An employee account has been created for {$request->full_name} ({$request->role}).");

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', "Employee account created successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Creation failed: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show the form for editing a user
     */
    public function edit(int $id)
    {
        $user = DB::table('users')
            ->leftJoin('user_profiles', 'users.user_id', '=', 'user_profiles.user_id')
            ->where('users.user_id', $id)
            ->first();

        if (!$user) {
            return redirect()->route('admin.users.index')->with('error', 'User not found.');
        }

        $roles = ['admin', 'jpo', 'trainer', 'lmo', 'employer', 'jobseeker'];
        $statuses = ['active', 'inactive'];

        return view('admin.users.edit', compact('user', 'roles', 'statuses'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:admin,jpo,trainer,lmo,employer,jobseeker',
            'status' => 'required|in:active,inactive',
            'is_approved' => 'nullable|boolean',
            'full_name' => 'nullable|string|max:150',
            'position' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:150',
            'office' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // 1. Update user
            DB::table('users')
                ->where('user_id', $id)
                ->update([
                    'role' => $request->role,
                    'status' => $request->status,
                    'is_approved' => $request->is_approved ?? 0,
                    'updated_at' => Carbon::now(),
                ]);

            // 2. Update user profile
            DB::table('user_profiles')
                ->where('user_id', $id)
                ->update([
                    'full_name' => $request->full_name,
                    'position' => $request->position,
                    'department' => $request->department,
                    'office' => $request->office,
                    'phone' => $request->phone,
                    'updated_at' => Carbon::now(),
                ]);

            // 3. Create notification for status change
            if ($request->status === 'inactive') {
                $this->createNotification($id, 'Account Deactivated', 
                    "Your account has been deactivated. Please contact admin for assistance.");
            } elseif ($request->status === 'active' && $request->is_approved) {
                $this->createNotification($id, 'Account Approved', 
                    "Your account has been approved and activated. You can now access the system.");
            }

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'User updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Update failed: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Toggle user status (Activate/Deactivate)
     */
    public function toggleStatus(int $id)
    {
        try {
            $user = DB::table('users')->where('user_id', $id)->first();
            
            if (!$user) {
                return redirect()->back()->with('error', 'User not found.');
            }

            $newStatus = $user->status === 'active' ? 'inactive' : 'active';
            
            DB::table('users')
                ->where('user_id', $id)
                ->update([
                    'status' => $newStatus,
                    'updated_at' => Carbon::now(),
                ]);

            $message = $newStatus === 'active' ? 'activated' : 'deactivated';
            $this->createNotification($id, "Account {$message}", 
                "Your account has been {$message} by an administrator.");

            return redirect()->back()
                ->with('success', "User has been {$message} successfully!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Operation failed: ' . $e->getMessage());
        }
    }

    /**
     * Approve a user
     */
    public function approve(Request $request, int $id)
    {
        try {
            $user = DB::table('users')->where('user_id', $id)->first();
            
            if (!$user) {
                return redirect()->back()->with('error', 'User not found.');
            }

            DB::table('users')
                ->where('user_id', $id)
                ->update([
                    'is_approved' => 1,
                    'status' => 'active',
                ]);

            $this->createNotification($id, 'Account Approved', 
                "Your account has been approved. You can now access the system.");

            if ($request->expectsJson()) {
                return response()->json(['success' => 'User approved successfully!']);
            }

            return redirect()->back()->with('success', 'User approved successfully!');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Approval failed: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Approval failed: ' . $e->getMessage());
        }
    }

    /**
     * Display an employer's submitted accreditation documents.
     */
    public function showEmployerDocuments(int $id)
    {
        $employer = DB::table('employers')
            ->join('users', 'employers.user_id', '=', 'users.user_id')
            ->leftJoin('employer_accreditation', 'employers.employer_id', '=', 'employer_accreditation.employer_id')
            ->where('employers.employer_id', $id)
            ->select(
                'employers.employer_id',
                'employers.company_name',
                'employers.is_accredited',
                'employers.accredited_at',
                'users.email',
                'employer_accreditation.documents',
                'employer_accreditation.submitted_at',
                'employer_accreditation.approved_at'
            )
            ->first();

        if (!$employer) {
            abort(404, 'Employer not found.');
        }

        $documents = json_decode($employer->documents ?? '{}', true) ?: [];

        return view('admin.employer-documents', compact('employer', 'documents'));
    }

    /**
     * Accredit an employer and mark its submitted review as approved.
     */
    public function accreditEmployer(Request $request, int $id)
    {
        try {
            $employer = DB::table('employers')
                ->where('employer_id', $id)
                ->first();

            if (!$employer) {
                return $this->approvalResponse($request, 'Employer not found.', 404);
            }

            DB::beginTransaction();

            DB::table('employers')
                ->where('employer_id', $id)
                ->update([
                    'is_accredited' => 1,
                    'accredited_at' => Carbon::now()->toDateString(),
                ]);

            DB::table('users')
                ->where('user_id', $employer->user_id)
                ->update([
                    'is_approved' => 1,
                    'status' => 'active',
                ]);

            DB::table('employer_accreditation')
                ->where('employer_id', $id)
                ->update([
                    'admin_approved' => 1,
                    'admin_approved_at' => Carbon::now()->toDateString(),
                    'approved_at' => Carbon::now()->toDateString(),
                ]);

            $this->createNotification(
                $employer->user_id,
                'Employer Accreditation Approved',
                'Your employer account and accreditation have been approved.'
            );

            DB::commit();

            return $this->approvalResponse($request, 'Employer accredited successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->approvalResponse($request, 'Accreditation failed: ' . $e->getMessage(), 500);
        }
    }

    private function approvalResponse(Request $request, string $message, int $status = 200)
    {
        if ($request->expectsJson()) {
            $key = $status >= 400 ? 'error' : 'success';
            return response()->json([$key => $message], $status);
        }

        return redirect()->back()->with($status >= 400 ? 'error' : 'success', $message);
    }

    /**
     * Delete a user
     */
    public function destroy(int $id)
    {
        try {
            // Check if user exists
            $user = DB::table('users')->where('user_id', $id)->first();
            if (!$user) {
                return redirect()->back()->with('error', 'User not found.');
            }

            // Prevent deleting yourself - use session user_id instead of auth()->id()
            if ($id === session('user_id')) {
                return redirect()->back()->with('error', 'You cannot delete your own account.');
            }

            DB::beginTransaction();

            // Delete related records first
            DB::table('user_profiles')->where('user_id', $id)->delete();
            DB::table('employers')->where('user_id', $id)->delete();
            DB::table('jobseekers')->where('user_id', $id)->delete();
            DB::table('notifications')->where('user_id', $id)->delete();
            DB::table('audit_logs')->where('user_id', $id)->delete();

            // Delete the user
            DB::table('users')->where('user_id', $id)->delete();

            DB::commit();

            return redirect()->back()->with('success', 'User deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Deletion failed: ' . $e->getMessage());
        }
    }

    /**
     * Helper function to create notifications
     */
    private function createNotification(int $userId, string $title, string $message): void
    {
        DB::table('notifications')->insert([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => 'approval',
            'is_read' => 0,
            'created_at' => Carbon::now(),
        ]);
    }
}