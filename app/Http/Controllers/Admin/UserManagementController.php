<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
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
                'user_profiles.phone as profile_phone',
                'employers.employer_id',
                'employers.company_name',
                DB::raw("NULL as trade_name"),
                DB::raw("NULL as contact_person"),
                DB::raw("NULL as employer_contact"),
                DB::raw("NULL as business_nature"),
                'jobseekers.jobseeker_id',
                'jobseekers.first_name',
                'jobseekers.last_name',
                'jobseekers.mobile_number as jobseeker_mobile',
                DB::raw("NULL as current_city"),
                DB::raw("NULL as current_barangay"),
                'jobseekers.employment_status'
            );

        if ($request->has('role') && $request->role != '') {
            $query->where('users.role', $request->role);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('users.status', $request->status);
        }

        if ($request->has('search') && $request->search != '') {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('users.email', 'like', $search)
                  ->orWhere('user_profiles.full_name', 'like', $search)
                  ->orWhere('employers.company_name', 'like', $search)
                  ->orWhere('jobseekers.first_name', 'like', $search)
                  ->orWhere('jobseekers.last_name', 'like', $search);
            });
        }

        $users = $query->orderBy('users.created_at', 'desc')->paginate(15)->withQueryString();

        $counts = [
            'total'      => DB::table('users')->count(),
            'active'     => DB::table('users')->where('status', 'active')->count(),
            'inactive'   => DB::table('users')->where('status', 'inactive')->count(),
            'pending'    => DB::table('users')->where('is_approved', 0)->count(),
            'admins'     => DB::table('users')->where('role', 'admin')->count(),
            'jpos'       => DB::table('users')->where('role', 'jpo')->count(),
            'trainers'   => DB::table('users')->where('role', 'trainer')->count(),
            'employers'  => DB::table('users')->where('role', 'employer')->count(),
            'jobseekers' => DB::table('users')->where('role', 'jobseeker')->count(),
        ];

        $roles    = ['admin', 'supervisor', 'jpo', 'trainer', 'lmo', 'employer', 'jobseeker'];
        $statuses = ['active', 'inactive'];

        return view('admin.users.index', compact('users', 'counts', 'roles', 'statuses'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = ['admin', 'jpo', 'trainer', 'employer', 'jobseeker', 'supervisor', 'lmo'];
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user (Admin or JPO)
     */
    public function store(Request $request, bool $isJpo = false)
    {
        $allowedRoles = $isJpo ? ['employer', 'jobseeker'] : ['admin', 'jpo', 'trainer', 'employer', 'jobseeker', 'supervisor', 'lmo'];

        $roleRules = [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:' . implode(',', $allowedRoles),
        ];

        $role = $request->input('role');

        if ($role === 'employer') {
            $rules = array_merge($roleRules, [
                'company_name' => 'required|string|max:255',
                'contact_person' => 'required|string|max:150',
                'primary_email' => 'nullable|email|max:150',
                'secondary_email' => 'nullable|email|max:150',
                'phone' => 'nullable|string|max:50',
                'contact_phone' => 'nullable|string|max:50',
                'company_address' => 'required|string|max:255',
                'business_nature' => 'nullable|string|max:150',
                'is_accredited' => 'nullable',
            ]);
        } elseif ($role === 'jobseeker') {
            $rules = array_merge($roleRules, [
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'middle_name' => 'nullable|string|max:100',
                'phone' => 'required|string|max:50',
                'employment_status' => 'required|string|max:100',
                'is_lgbtq' => 'nullable|boolean',
                'is_senior_citizen' => 'nullable|boolean',
                'is_immigrant' => 'nullable|boolean',
                'immigrant_origin' => 'nullable|string|max:150',
            ]);
        } else {
            $rules = array_merge($roleRules, [
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
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            if ($role === 'employer') {
                $isAccredited = $request->boolean('is_accredited') || $request->input('auto_accredit') == '1';
                $primaryEmail = $request->primary_email ?: $request->email;
                $secondaryEmail = $request->secondary_email;
                $contactPhone = $request->phone ?: ($request->contact_phone ?: null);
                $companyAddress = $request->company_address;

                $userId = DB::table('users')->insertGetId([
                    'email' => $primaryEmail,
                    'password' => Hash::make($request->password),
                    'role' => 'employer',
                    'status' => 'active',
                    'is_approved' => $isAccredited ? 1 : 0,
                    'phone_number' => $contactPhone,
                    'is_contact_verified' => 1,
                    'account_status' => $isAccredited ? 'verified' : 'unverified',
                    'created_at' => Carbon::now(),
                ]);

                $employerData = [
                    'user_id' => $userId,
                    'company_name' => $request->company_name,
                    'contact_person' => $request->contact_person,
                    'primary_email' => $primaryEmail,
                    'secondary_email' => $secondaryEmail,
                    'contact_phone' => $contactPhone,
                    'company_address' => $companyAddress,
                    'is_accredited' => $isAccredited ? 1 : 0,
                    'accredited_at' => $isAccredited ? Carbon::now()->toDateString() : null,
                ];

                if (\Illuminate\Support\Facades\Schema::hasColumn('employers', 'business_nature')) {
                    $employerData['business_nature'] = $request->business_nature;
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('employers', 'region')) {
                    $employerData['region'] = 'Region VII - Central Visayas';
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('employers', 'created_at')) {
                    $employerData['created_at'] = Carbon::now();
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('employers', 'updated_at')) {
                    $employerData['updated_at'] = Carbon::now();
                }

                $employerId = DB::table('employers')->insertGetId($employerData);

                DB::table('user_profiles')->insert([
                    'user_id' => $userId,
                    'full_name' => $request->contact_person,
                    'position' => 'Authorized Contact Person',
                    'phone' => $contactPhone,
                    'office' => $companyAddress,
                    'created_at' => Carbon::now(),
                ]);

                $certNo = $isAccredited ? ('DMDP-EMP-' . date('Y') . '-' . str_pad($employerId, 5, '0', STR_PAD_LEFT)) : null;

                DB::table('employer_accreditation')->insert([
                    'employer_id' => $employerId,
                    'status' => $isAccredited ? 'admin_approved' : 'submitted_to_jpo',
                    'admin_approved' => $isAccredited ? 1 : 0,
                    'admin_approved_at' => $isAccredited ? Carbon::now() : null,
                    'approved_at' => $isAccredited ? Carbon::now()->toDateString() : null,
                    'certificate_no' => $certNo,
                    'certificate_issued_at' => $isAccredited ? Carbon::now() : null,
                    'certificate_issued_by' => $isAccredited ? \Illuminate\Support\Facades\Auth::id() : null,
                    'documents' => json_encode([]),
                    'submitted_at' => Carbon::now(),
                ]);

                $this->createNotification($userId, 'Employer Account Created', 
                    "Employer account for {$request->company_name} has been provisioned.");

                DB::commit();

                $redirect = $isJpo ? redirect()->route('jpo.evaluations.accreditations') : redirect()->route('admin.employers');
                return $redirect->with('success', "Employer account for '{$request->company_name}' created successfully!" . ($isAccredited ? " (Certificate {$certNo} issued)" : ""));

            } elseif ($role === 'jobseeker') {
                $fullName = trim("{$request->first_name} {$request->last_name}");

                $userId = DB::table('users')->insertGetId([
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => 'jobseeker',
                    'status' => 'active',
                    'is_approved' => 1,
                    'phone_number' => $request->phone,
                    'is_contact_verified' => 1,
                    'account_status' => 'verified',
                    'created_at' => Carbon::now(),
                ]);

                $jobseekerId = DB::table('jobseekers')->insertGetId([
                    'user_id' => $userId,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'middle_name' => $request->middle_name,
                    'mobile_number' => $request->phone,
                    'email' => $request->email,
                    'employment_status' => $request->employment_status,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                DB::table('social_status')->insert([
                    'jobseeker_id' => $jobseekerId,
                    'is_lgbtq' => $request->boolean('is_lgbtq') ? 1 : 0,
                    'is_senior_citizen' => $request->boolean('is_senior_citizen') ? 1 : 0,
                    'is_immigrant' => $request->boolean('is_immigrant') ? 1 : 0,
                    'immigrant_origin' => $request->immigrant_origin,
                    'region' => 'Region VII',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                DB::table('user_profiles')->insert([
                    'user_id' => $userId,
                    'full_name' => $fullName,
                    'phone' => $request->phone,
                    'created_at' => Carbon::now(),
                ]);

                DB::table('jobseeker_details')->insert([
                    'jobseeker_id' => $jobseekerId,
                    'address' => json_encode([]),
                    'education' => json_encode([]),
                    'work_experience' => json_encode([]),
                    'eligibility' => json_encode([]),
                    'language_proficiency' => json_encode([]),
                    'training_certificates' => json_encode([]),
                    'created_at' => Carbon::now(),
                ]);

                $this->createNotification($userId, 'Jobseeker Account Created', 
                    "Jobseeker account for {$fullName} has been provisioned.");

                DB::commit();

                $redirect = $isJpo ? redirect()->route('jpo.evaluations.jobseekers') : redirect()->route('admin.jobseekers.index');
                return $redirect->with('success', "Jobseeker account for '{$fullName}' created successfully!");

            } else {
                // Admin, JPO, Trainer, Supervisor, LMO
                $isApproved = ($request->role === 'trainer') ? 1 : ($request->is_approved ?? 1);
                $isTrainerApproved = ($request->role === 'trainer') ? 1 : ($request->is_approved ?? 0);

                // 1. Create user account
                $user = DB::table('users')->insertGetId([
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => $request->role,
                    'status' => 'active',
                    'is_approved' => $isApproved,
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
                    'is_trainer_approved' => $isTrainerApproved,
                    'trainer_approved_by' => $request->role === 'trainer' ? \Illuminate\Support\Facades\Auth::id() : null,
                    'trainer_approved_at' => $request->role === 'trainer' ? Carbon::now() : null,
                    'created_at' => Carbon::now(),
                ]);

                // 3. Create notification
                $this->createNotification($user, 'New Employee Account Created', 
                    "An employee account has been created for {$request->full_name} ({$request->role}).");

                DB::commit();

                return redirect()->route('admin.users.index')
                    ->with('success', "Staff account created successfully!");
            }

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
            ->leftJoin('employers', 'users.user_id', '=', 'employers.user_id')
            ->leftJoin('jobseekers', 'users.user_id', '=', 'jobseekers.user_id')
            ->where('users.user_id', $id)
            ->select(
                'users.user_id',
                'users.email',
                'users.role',
                'users.status',
                'users.is_approved',
                'users.created_at',
                'users.updated_at',
                'user_profiles.profile_id',
                'user_profiles.full_name',
                'user_profiles.position',
                'user_profiles.department',
                'user_profiles.office',
                'user_profiles.phone',
                'user_profiles.specialization',
                'user_profiles.trainer_type',
                'user_profiles.partner_institution',
                'employers.company_name',
                'employers.is_accredited',
                'jobseekers.first_name',
                'jobseekers.last_name',
                'jobseekers.mobile_number as jobseeker_phone'
            )
            ->first();

        if (!$user) {
            return redirect()->route('admin.users.index')->with('error', 'User not found.');
        }

        if (empty($user->full_name)) {
            if (!empty($user->first_name) || !empty($user->last_name)) {
                $user->full_name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
            } elseif (!empty($user->company_name)) {
                $user->full_name = $user->company_name;
            }
        }
        if (empty($user->phone)) {
            $user->phone = $user->jobseeker_phone ?? null;
        }

        $roles    = ['admin', 'supervisor', 'jpo', 'trainer', 'lmo', 'employer', 'jobseeker'];
        $statuses = ['active', 'inactive'];

        return view('admin.users.edit', compact('user', 'roles', 'statuses'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'role'        => 'required|in:admin,supervisor,jpo,trainer,lmo,employer,jobseeker',
            'status'      => 'required|in:active,inactive',
            'is_approved' => 'nullable|boolean',
            'full_name'   => 'nullable|string|max:150',
            'position'    => 'nullable|string|max:100',
            'department'  => 'nullable|string|max:150',
            'office'      => 'nullable|string|max:150',
            'phone'       => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $existingUser = DB::table('users')->where('user_id', $id)->first();
            $isApproved = in_array($request->role, ['employer', 'jobseeker'])
                ? ($existingUser->is_approved ?? 0)
                : ($request->is_approved ?? 0);

            // 1. Update user (Approvals for employer/jobseeker handled strictly in dedicated dashboards)
            DB::table('users')
                ->where('user_id', $id)
                ->update([
                    'role'        => $request->role,
                    'status'      => $request->status,
                    'is_approved' => $isApproved,
                    'updated_at'  => Carbon::now(),
                ]);

            // 2. Update user profile if exists or insert if profile data provided
            $profileExists = DB::table('user_profiles')->where('user_id', $id)->exists();

            if ($profileExists) {
                DB::table('user_profiles')
                    ->where('user_id', $id)
                    ->update([
                        'full_name'  => $request->full_name,
                        'position'   => $request->position,
                        'department' => $request->department,
                        'office'     => $request->office,
                        'phone'      => $request->phone,
                        'updated_at' => Carbon::now(),
                    ]);
            } elseif ($request->filled('full_name') || $request->filled('position') || $request->filled('department') || $request->filled('office') || $request->filled('phone')) {
                DB::table('user_profiles')->insert([
                    'user_id'    => $id,
                    'full_name'  => $request->full_name,
                    'position'   => $request->position,
                    'department' => $request->department,
                    'office'     => $request->office,
                    'phone'      => $request->phone,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            // 3. Create notification for status change
            if ($request->status === 'inactive') {
                $this->createNotification(
                    $id,
                    'Account Deactivated',
                    'Your account has been deactivated. Please contact admin for assistance.'
                );
            } elseif ($request->status === 'active' && $request->is_approved) {
                $this->createNotification(
                    $id,
                    'Account Approved',
                    'Your account has been approved and activated. You can now access the system.'
                );
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
     * Toggle user status (Activate/Deactivate) — returns JSON
     */
    public function toggleStatus(Request $request, int $id)
    {
        try {
            $user = DB::table('users')->where('user_id', $id)->first();

            if (!$user) {
                return response()->json(['error' => 'User not found.'], 404);
            }

            $newStatus = $user->status === 'active' ? 'inactive' : 'active';

            DB::table('users')
                ->where('user_id', $id)
                ->update([
                    'status'     => $newStatus,
                    'updated_at' => Carbon::now(),
                ]);

            $message = $newStatus === 'active' ? 'activated' : 'deactivated';

            $this->createNotification(
                $id,
                "Account {$message}",
                "Your account has been {$message} by an administrator."
            );

            return response()->json([
                'success' => "User has been {$message} successfully!",
                'status'  => $newStatus,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Operation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Approve a user — returns JSON
     */
    public function approve(Request $request, int $id)
    {
        try {
            $user = DB::table('users')->where('user_id', $id)->first();

            if (!$user) {
                return response()->json(['error' => 'User not found.'], 404);
            }

            DB::table('users')
                ->where('user_id', $id)
                ->update([
                    'is_approved' => 1,
                    'status'      => 'active',
                    'updated_at'  => Carbon::now(),
                ]);

            $this->createNotification(
                $id,
                'Account Approved',
                'Your account has been approved. You can now access the system.'
            );

            return response()->json([
                'success' => 'User approved successfully!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Approval failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a user
     */
    public function destroy(int $id)
    {
        try {
            $user = DB::table('users')->where('user_id', $id)->first();

            if (!$user) {
                return redirect()->back()->with('error', 'User not found.');
            }

            // Prevent deleting yourself
            if ($id === session('user_id') || $id === Auth::id()) {
                return redirect()->back()->with('error', 'You cannot delete your own account.');
            }

            DB::beginTransaction();

            DB::table('user_profiles')->where('user_id', $id)->delete();
            DB::table('employers')->where('user_id', $id)->delete();
            DB::table('jobseekers')->where('user_id', $id)->delete();
            DB::table('notifications')->where('user_id', $id)->delete();
            DB::table('audit_logs')->where('user_id', $id)->delete();
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
            'user_id'    => $userId,
            'title'      => $title,
            'message'    => $message,
            'type'       => 'approval',
            'is_read'    => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}