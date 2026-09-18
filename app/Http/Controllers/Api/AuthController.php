<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobPreference;
use App\Models\Jobseeker;
use App\Models\JobseekerDetail;
use App\Models\JobseekerSkill;
use App\Models\SocialStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Format a user and their comprehensive jobseeker profile data for API responses.
     */
    private function formatUserProfile(User $user): array
    {
        $jobseeker = $user->jobseeker;
        $details = $jobseeker ? $jobseeker->details : null;
        $preferences = $jobseeker ? $jobseeker->preferences : null;
        $socialStatus = $jobseeker ? $jobseeker->socialStatus : null;
        $skills = $jobseeker ? $jobseeker->skills->pluck('skill_name')->filter(function ($s) {
            return !empty($s) && $s !== '[object Object]' && !preg_match('/\.(pdf|docx?|jpe?g|png)$/i', $s);
        })->values()->toArray() : [];

        // Address object
        $address = is_array($details?->address) ? $details->address : (json_decode($details?->address ?? '', true) ?: [
            'street' => '', 'barangay' => '', 'city' => 'Cebu City', 'province' => 'Cebu', 'zip' => '', 'full' => 'Cebu City, Cebu'
        ]);

        // Education object
        $educationObj = is_array($details?->education) ? $details->education : (json_decode($details?->education ?? '', true) ?: [
            'level' => 'College Graduate', 'school' => '', 'course' => '', 'year_graduated' => ''
        ]);
        $educationStr = is_array($educationObj)
            ? trim(($educationObj['course'] ?? '') . ' ' . ($educationObj['school'] ?? ''))
            : (string)($details->education ?? '');

        // Work Experience
        $workExpObj = is_array($details?->work_experience) ? $details->work_experience : (json_decode($details?->work_experience ?? '', true) ?: [
            'company' => '', 'position' => '', 'duration' => '', 'description' => '', 'summary' => ''
        ]);

        // Eligibility & Licenses
        $eligibilityObj = is_array($details?->eligibility) ? $details->eligibility : (json_decode($details?->eligibility ?? '', true) ?: [
            'civil_service' => '', 'prc_license' => '', 'tesda_nc' => '', 'driver_license' => ''
        ]);

        // Language proficiencies
        $languages = is_array($details?->language_proficiency) ? $details->language_proficiency : (json_decode($details?->language_proficiency ?? '', true) ?: ['English', 'Filipino', 'Cebuano']);

        // Calculate Profile Strength exactly as web
        $profileStrength = 30;
        if ($jobseeker) {
            if (!empty($jobseeker->first_name) && !empty($jobseeker->last_name)) $profileStrength += 15;
            if (!empty($jobseeker->mobile_number)) $profileStrength += 15;
            if (count($skills) >= 3) $profileStrength += 20;
            elseif (count($skills) >= 1) $profileStrength += 10;
            if ($details && (!empty($details->education) || !empty($details->work_experience))) $profileStrength += 20;
        }
        $profileStrength = min(100, $profileStrength);

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
            'education_details' => $educationObj,
            'address' => $address,
            'work_experience' => $workExpObj,
            'eligibility' => $eligibilityObj,
            'languages' => $languages,
            'bio' => $workExpObj['summary'] ?? '',
            'skills' => $skills,
            'sex' => $jobseeker->sex ?? null,
            'civil_status' => $jobseeker->civil_status ?? null,
            'civilStatus' => $jobseeker->civil_status ?? null,
            'citizenship' => $jobseeker->citizenship ?? 'Filipino',
            'birth_date' => $jobseeker->birth_date ? $jobseeker->birth_date->format('Y-m-d') : null,
            'birthDate' => $jobseeker->birth_date ? $jobseeker->birth_date->format('Y-m-d') : null,
            'employment_status' => $jobseeker->employment_status ?? 'Looking for job',
            'employmentStatus' => $jobseeker->employment_status ?? 'Looking for job',
            'hired_company' => $jobseeker->hired_company ?? null,
            'is_employed' => $jobseeker ? $jobseeker->isEmployed() : false,
            'profile_strength' => $profileStrength,
            'profileStrength' => $profileStrength,
            'preferences' => [
                'occupation1' => $preferences->occupation1 ?? '',
                'occupation2' => $preferences->occupation2 ?? '',
                'industry1' => $preferences->industry1 ?? '',
                'preferred_location' => $preferences->preferred_location ?? 'Cebu City',
                'salary_expectation' => $preferences->salary_expectation ?? '',
            ],
            'social_status' => [
                'is_pwd' => (bool)($socialStatus->is_pwd ?? false),
                'pwd_type' => $socialStatus->pwd_type ?? '',
                'is_4ps' => (bool)($socialStatus->is_4ps ?? false),
                'household_id' => $socialStatus->household_id ?? '',
                'is_ofw' => (bool)($socialStatus->is_ofw ?? false),
            ],
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

        $user = User::with(['jobseeker.details', 'jobseeker.skills', 'jobseeker.preferences', 'jobseeker.socialStatus', 'profile'])
            ->where('email', $request->email)
            ->first();

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
            'middleName' => 'nullable|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'birthDate' => 'nullable|date',
            'birth_date' => 'nullable|date',
            'sex' => 'nullable|string|max:20',
            'civilStatus' => 'nullable|string|max:50',
            'civil_status' => 'nullable|string|max:50',
            'citizenship' => 'nullable|string|max:100',
            'education' => 'nullable',
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

        try {
            DB::beginTransaction();

            $firstName = $request->input('firstName', $request->input('first_name', 'Jobseeker'));
            $lastName = $request->input('lastName', $request->input('last_name', ''));
            $fullName = trim("{$firstName} {$lastName}");

            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'jobseeker',
                'status' => 'active',
                'is_approved' => true,
            ]);

            $jobseeker = Jobseeker::create([
                'user_id' => $user->user_id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'middle_name' => $request->input('middleName', $request->input('middle_name')),
                'birth_date' => $request->input('birthDate', $request->input('birth_date')),
                'sex' => $request->input('sex', 'Male'),
                'civil_status' => $request->input('civilStatus', $request->input('civil_status', 'Single')),
                'citizenship' => $request->input('citizenship', 'Filipino'),
                'mobile_number' => $request->input('phone', $request->input('mobile_number')),
                'employment_status' => 'Looking for job',
                'email' => $user->email,
            ]);

            // Save details
            $details = JobseekerDetail::create([
                'jobseeker_id' => $jobseeker->jobseeker_id,
                'education' => $request->has('education') 
                    ? (is_array($request->education) ? $request->education : ['level' => (string)$request->education]) 
                    : ['level' => 'College Graduate'],
                'address' => $request->input('address', [
                    'street' => '', 'barangay' => '', 'city' => 'Cebu City', 'province' => 'Cebu', 'zip' => '', 'full' => 'Cebu City, Cebu'
                ]),
                'training_certificates' => [],
            ]);

            // Save skills
            $skillsInput = $request->input('skills', []);
            if (is_string($skillsInput)) {
                $skillsInput = array_map('trim', explode(',', $skillsInput));
            }
            if (is_array($skillsInput)) {
                foreach (array_unique(array_filter($skillsInput)) as $skill) {
                    JobseekerSkill::create([
                        'jobseeker_id' => $jobseeker->jobseeker_id,
                        'skill_name' => $skill,
                        'skill_type' => 'technical',
                    ]);
                }
            }

            // Save preferences
            JobPreference::create([
                'jobseeker_id' => $jobseeker->jobseeker_id,
                'preferred_location' => 'Cebu City',
            ]);

            // Save social status
            SocialStatus::create([
                'jobseeker_id' => $jobseeker->jobseeker_id,
                'is_pwd' => false,
                'is_4ps' => false,
                'is_ofw' => false,
            ]);

            DB::commit();

            $token = $user->createToken('trabago-mobile-app')->plainTextToken;
            $formattedUser = $this->formatUserProfile($user->fresh(['jobseeker.details', 'jobseeker.skills', 'jobseeker.preferences', 'jobseeker.socialStatus', 'profile']));

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
        $user = $request->user()->load(['jobseeker.details', 'jobseeker.skills', 'jobseeker.preferences', 'jobseeker.socialStatus', 'profile']);
        $formattedUser = $this->formatUserProfile($user);

        return response()->json([
            'success' => true,
            'data' => $formattedUser,
            'user' => $formattedUser,
            ...$formattedUser,
        ]);
    }

    /**
     * Update the authenticated user's comprehensive profile.
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

        // 1. Core info
        if ($request->has('firstName') || $request->has('first_name')) {
            $jobseeker->first_name = $request->input('firstName', $request->input('first_name'));
        }
        if ($request->has('lastName') || $request->has('last_name')) {
            $jobseeker->last_name = $request->input('lastName', $request->input('last_name'));
        }
        if ($request->has('middleName') || $request->has('middle_name')) {
            $jobseeker->middle_name = $request->input('middleName', $request->input('middle_name'));
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
        if ($request->has('citizenship')) {
            $jobseeker->citizenship = $request->citizenship;
        }
        if ($request->has('employmentStatus') || $request->has('employment_status')) {
            $jobseeker->employment_status = $request->input('employmentStatus', $request->input('employment_status'));
        }
        if ($request->has('hired_company')) {
            $jobseeker->hired_company = $request->hired_company;
        }
        $jobseeker->save();

        // 2. Details (Address, Education, Work Experience, Eligibility, Language, Bio)
        $details = $jobseeker->details ?: new JobseekerDetail(['jobseeker_id' => $jobseeker->jobseeker_id]);
        
        if ($request->has('address')) {
            $details->address = is_array($request->address) ? $request->address : $details->address;
        }
        if ($request->has('education')) {
            $details->education = is_array($request->education) ? $request->education : ['level' => (string)$request->education];
        }
        if ($request->has('work_experience')) {
            $details->work_experience = is_array($request->work_experience) ? $request->work_experience : $details->work_experience;
        }
        if ($request->has('bio') && is_array($details->work_experience)) {
            $w = $details->work_experience;
            $w['summary'] = $request->input('bio');
            $details->work_experience = $w;
        }
        if ($request->has('eligibility')) {
            $details->eligibility = is_array($request->eligibility) ? $request->eligibility : $details->eligibility;
        }
        if ($request->has('languages')) {
            $langs = $request->languages;
            if (is_string($langs)) $langs = array_map('trim', explode(',', $langs));
            $details->language_proficiency = is_array($langs) ? array_values(array_filter($langs)) : [];
        }
        $details->save();

        // 3. Preferences
        if ($request->has('preferences')) {
            $prefData = $request->preferences;
            $pref = $jobseeker->preferences ?: new JobPreference(['jobseeker_id' => $jobseeker->jobseeker_id]);
            if (isset($prefData['occupation1'])) $pref->occupation1 = $prefData['occupation1'];
            if (isset($prefData['occupation2'])) $pref->occupation2 = $prefData['occupation2'];
            if (isset($prefData['industry1'])) $pref->industry1 = $prefData['industry1'];
            if (isset($prefData['preferred_location'])) $pref->preferred_location = $prefData['preferred_location'];
            if (isset($prefData['salary_expectation'])) $pref->salary_expectation = $prefData['salary_expectation'];
            $pref->save();
        }

        // 4. Social Status
        if ($request->has('social_status')) {
            $socData = $request->social_status;
            $soc = $jobseeker->socialStatus ?: new SocialStatus(['jobseeker_id' => $jobseeker->jobseeker_id]);
            if (isset($socData['is_pwd'])) $soc->is_pwd = (bool)$socData['is_pwd'];
            if (isset($socData['pwd_type'])) $soc->pwd_type = $socData['pwd_type'];
            if (isset($socData['is_4ps'])) $soc->is_4ps = (bool)$socData['is_4ps'];
            if (isset($socData['household_id'])) $soc->household_id = $socData['household_id'];
            if (isset($socData['is_ofw'])) $soc->is_ofw = (bool)$socData['is_ofw'];
            $soc->save();
        }

        // 5. Skills sync if passed
        if ($request->has('skills') && is_array($request->skills)) {
            JobseekerSkill::where('jobseeker_id', $jobseeker->jobseeker_id)->delete();
            foreach (array_unique(array_filter($request->skills)) as $skill) {
                JobseekerSkill::create([
                    'jobseeker_id' => $jobseeker->jobseeker_id,
                    'skill_name' => $skill,
                    'skill_type' => 'technical',
                ]);
            }
        }

        $freshUser = $user->fresh(['jobseeker.details', 'jobseeker.skills', 'jobseeker.preferences', 'jobseeker.socialStatus', 'profile']);
        $formattedUser = $this->formatUserProfile($freshUser);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $formattedUser,
            'user' => $formattedUser,
            ...$formattedUser,
        ]);
    }

    /**
     * Add single skill to jobseeker's profile.
     */
    public function addSkill(Request $request)
    {
        $user = $request->user();
        $jobseeker = $user ? $user->jobseeker : null;
        if (!$jobseeker) {
            return response()->json(['success' => false, 'message' => 'Jobseeker not found'], 404);
        }

        $skillName = trim($request->input('skill_name', $request->input('skill', '')));
        if (empty($skillName)) {
            return response()->json(['success' => false, 'message' => 'Skill name cannot be empty'], 422);
        }

        $exists = JobseekerSkill::where('jobseeker_id', $jobseeker->jobseeker_id)
            ->whereRaw('LOWER(skill_name) = ?', [strtolower($skillName)])
            ->first();

        if (!$exists) {
            JobseekerSkill::create([
                'jobseeker_id' => $jobseeker->jobseeker_id,
                'skill_name' => $skillName,
                'skill_type' => 'technical',
            ]);
        }

        $skills = $jobseeker->fresh('skills')->skills->pluck('skill_name')->toArray();

        return response()->json([
            'success' => true,
            'message' => "Skill '{$skillName}' added",
            'skills' => $skills,
        ]);
    }

    /**
     * Remove a skill by name or id.
     */
    public function removeSkill(Request $request, $id)
    {
        $user = $request->user();
        $jobseeker = $user ? $user->jobseeker : null;
        if (!$jobseeker) {
            return response()->json(['success' => false, 'message' => 'Jobseeker not found'], 404);
        }

        // Check if $id is numeric (skill_id) or string (skill_name)
        if (is_numeric($id)) {
            JobseekerSkill::where('jobseeker_id', $jobseeker->jobseeker_id)->where('skill_id', $id)->delete();
        } else {
            JobseekerSkill::where('jobseeker_id', $jobseeker->jobseeker_id)->where('skill_name', $id)->delete();
        }

        $skills = $jobseeker->fresh('skills')->skills->pluck('skill_name')->toArray();

        return response()->json([
            'success' => true,
            'message' => 'Skill removed',
            'skills' => $skills,
        ]);
    }

    /**
     * Change user password.
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = $request->user();
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password does not match your account password.',
            ], 400);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        return response()->json([
            'success' => true,
            'message' => 'Your password has been changed successfully.',
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
