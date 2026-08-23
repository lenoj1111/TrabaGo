<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jobseeker;
use App\Models\JobseekerDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Format a user and their jobseeker/profile data for API responses.
     */
    private function formatUserProfile(User $user): array
    {
        $jobseeker = $user->jobseeker;
        $details = $jobseeker ? $jobseeker->details : null;
        $skills = $jobseeker ? $jobseeker->skills->pluck('skill_name')->toArray() : [];

        $educationStr = '';
        if ($details && !empty($details->education)) {
            if (is_array($details->education)) {
                $educationStr = implode(', ', array_map(function ($item) {
                    return is_array($item) ? ($item['school'] ?? $item['course'] ?? json_encode($item)) : (string)$item;
                }, $details->education));
            } else {
                $educationStr = (string)$details->education;
            }
        }

        $firstName = $jobseeker->first_name ?? ($user->profile->full_name ?? explode('@', $user->email)[0] ?? 'User');
        $lastName = $jobseeker->last_name ?? '';
        $phone = $jobseeker->mobile_number ?? ($user->profile->phone ?? '');

        return [
            'id' => $user->user_id,
            'user_id' => $user->user_id,
            'email' => $user->email,
            'firstName' => $firstName,
            'first_name' => $firstName,
            'lastName' => $lastName,
            'last_name' => $lastName,
            'middleName' => $jobseeker->middle_name ?? '',
            'middle_name' => $jobseeker->middle_name ?? '',
            'fullName' => $user->full_name,
            'full_name' => $user->full_name,
            'phone' => $phone,
            'mobile_number' => $phone,
            'education' => $educationStr,
            'skills' => $skills,
            'sex' => $jobseeker->sex ?? null,
            'civil_status' => $jobseeker->civil_status ?? null,
            'civilStatus' => $jobseeker->civil_status ?? null,
            'citizenship' => $jobseeker->citizenship ?? 'Filipino',
            'birth_date' => $jobseeker->birth_date ? $jobseeker->birth_date->format('Y-m-d') : null,
            'birthDate' => $jobseeker->birth_date ? $jobseeker->birth_date->format('Y-m-d') : null,
            'employment_status' => $jobseeker->employment_status ?? 'Jobseeker',
            'employmentStatus' => $jobseeker->employment_status ?? 'Jobseeker',
            'role' => $user->role,
            'status' => $user->status,
            'isApproved' => (bool)$user->is_approved,
            'is_approved' => (bool)$user->is_approved,
            'avatar' => null,
        ];
    }

    /**
     * Login user and create API token.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::with(['jobseeker.details', 'jobseeker.skills', 'profile'])->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.',
            ], 401);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is inactive. Please contact support.',
            ], 403);
        }

        // Generate Sanctum plain text token
        $token = $user->createToken('trabago-mobile-app')->plainTextToken;
        $formattedUser = $this->formatUserProfile($user);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $formattedUser,
            'data' => [
                'token' => $token,
                'user' => $formattedUser,
            ],
        ]);
    }

    /**
     * Register a new jobseeker user and generate token.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'firstName' => 'nullable|string|max:100',
            'first_name' => 'nullable|string|max:100',
            'lastName' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'education' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'mobile_number' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $firstName = $request->input('firstName', $request->input('first_name', 'Jobseeker'));
        $lastName = $request->input('lastName', $request->input('last_name', ''));
        $phone = $request->input('phone', $request->input('mobile_number', ''));
        $education = $request->input('education', '');

        try {
            DB::beginTransaction();

            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'jobseeker',
                'status' => 'active',
                'is_approved' => 1,
                'created_at' => now(),
            ]);

            $jobseeker = Jobseeker::create([
                'user_id' => $user->user_id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $request->email,
                'mobile_number' => $phone,
                'citizenship' => 'Filipino',
                'employment_status' => 'Jobseeker',
            ]);

            JobseekerDetail::create([
                'jobseeker_id' => $jobseeker->jobseeker_id,
                'education' => $education ? [$education] : [],
                'address' => [],
                'work_experience' => [],
                'eligibility' => [],
                'language_proficiency' => [],
                'training_certificates' => [],
            ]);

            DB::commit();

            $token = $user->createToken('trabago-mobile-app')->plainTextToken;
            $formattedUser = $this->formatUserProfile($user->load(['jobseeker.details', 'jobseeker.skills']));

            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'token' => $token,
                'user' => $formattedUser,
                'data' => [
                    'token' => $token,
                    'user' => $formattedUser,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get the authenticated user's profile.
     */
    public function getProfile(Request $request)
    {
        $user = $request->user()->load(['jobseeker.details', 'jobseeker.skills', 'profile']);
        $formattedUser = $this->formatUserProfile($user);

        return response()->json([
            'success' => true,
            'data' => $formattedUser,
            ...$formattedUser,
        ]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $jobseeker = $user->jobseeker;
        if (!$jobseeker) {
            $jobseeker = Jobseeker::create([
                'user_id' => $user->user_id,
                'email' => $user->email,
            ]);
        }

        if ($request->has('firstName') || $request->has('first_name')) {
            $jobseeker->first_name = $request->input('firstName', $request->input('first_name'));
        }
        if ($request->has('lastName') || $request->has('last_name')) {
            $jobseeker->last_name = $request->input('lastName', $request->input('last_name'));
        }
        if ($request->has('phone') || $request->has('mobile_number')) {
            $jobseeker->mobile_number = $request->input('phone', $request->input('mobile_number'));
        }
        if ($request->has('birthDate') || $request->has('birth_date')) {
            $jobseeker->birth_date = $request->input('birthDate', $request->input('birth_date'));
        }
        if ($request->has('sex')) {
            $jobseeker->sex = $request->sex;
        }
        if ($request->has('civilStatus') || $request->has('civil_status')) {
            $jobseeker->civil_status = $request->input('civilStatus', $request->input('civil_status'));
        }
        if ($request->has('employmentStatus') || $request->has('employment_status')) {
            $jobseeker->employment_status = $request->input('employmentStatus', $request->input('employment_status'));
        }
        $jobseeker->save();

        if ($request->has('education')) {
            $details = $jobseeker->details;
            if (!$details) {
                $details = new JobseekerDetail(['jobseeker_id' => $jobseeker->jobseeker_id]);
            }
            $details->education = is_array($request->education) ? $request->education : [$request->education];
            $details->save();
        }

        $formattedUser = $this->formatUserProfile($user->fresh(['jobseeker.details', 'jobseeker.skills', 'profile']));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $formattedUser,
            ...$formattedUser,
        ]);
    }

    /**
     * Logout and revoke tokens.
     */
    public function logout(Request $request)
    {
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}
