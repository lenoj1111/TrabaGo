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
            'street' => '',
            'barangay' => '',
            'city' => 'Cebu City',
            'province' => 'Cebu',
            'zip' => '',
            'full' => 'Cebu City, Cebu'
        ]);
        $address['zip'] = $address['zip'] ?? $address['postal_code'] ?? '';
        $address['postal_code'] = $address['postal_code'] ?? $address['zip'];
        $address['country'] = $address['country'] ?? 'Philippines';
        $address['full'] = $address['full'] ?? trim(implode(', ', array_filter([
            $address['street'] ?? '',
            $address['barangay'] ?? '',
            $address['city'] ?? '',
            $address['province'] ?? '',
        ])));

        // Education object
        $educationObj = is_array($details?->education) ? $details->education : (json_decode($details?->education ?? '', true) ?: [
            'level' => 'College Graduate',
            'school' => '',
            'course' => '',
            'year_graduated' => ''
        ]);
        $educationStr = is_array($educationObj)
            ? trim(($educationObj['course'] ?? '') . ' ' . ($educationObj['school'] ?? ''))
            : (string)($details->education ?? '');

        // Work Experience
        $workExpObj = is_array($details?->work_experience) ? $details->work_experience : (json_decode($details?->work_experience ?? '', true) ?: [
            'company' => '',
            'position' => '',
            'duration' => '',
            'description' => '',
            'summary' => ''
        ]);
        $workEntries = $workExpObj['entries'] ?? [];
        if (is_array($workEntries) && isset($workEntries[0]) && is_array($workEntries[0])) {
            $latestWork = $workEntries[0];
            $workExpObj = array_merge($latestWork, $workExpObj, [
                'company' => $latestWork['employer'] ?? $latestWork['company'] ?? $workExpObj['company'] ?? '',
                'position' => $latestWork['position'] ?? $latestWork['job_title'] ?? $workExpObj['position'] ?? '',
                'duration' => $latestWork['duration'] ?? $workExpObj['duration'] ?? '',
                'description' => $latestWork['responsibilities'] ?? $latestWork['description'] ?? $workExpObj['description'] ?? '',
                'entries' => $workEntries,
            ]);
        }

        // Eligibility & Licenses
        $eligibilityObj = is_array($details?->eligibility) ? $details->eligibility : (json_decode($details?->eligibility ?? '', true) ?: [
            'civil_service' => '',
            'prc_license' => '',
            'tesda_nc' => '',
            'driver_license' => ''
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

        $missingRequirements = $jobseeker ? $jobseeker->getMissingRequirements() : [];
        $isProfileComplete = empty($missingRequirements);
        $isVerified = (bool) ($user->is_approved && $isProfileComplete);

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
                'occupation3' => $preferences->occupation3 ?? '',
                'industry1' => $preferences->industry1 ?? '',
                'industry2' => $preferences->industry2 ?? '',
                'industry3' => $preferences->industry3 ?? '',
                'preferred_location' => $preferences->preferred_location ?? 'Cebu City',
                'salary_expectation' => $preferences->salary_expectation ?? '',
            ],
            'social_status' => [
                'is_pwd' => (bool)($socialStatus->is_pwd ?? false),
                'pwd_type' => $socialStatus->pwd_type ?? '',
                'is_4ps' => (bool)($socialStatus->is_4ps ?? false),
                'household_id' => $socialStatus->household_id ?? '',
                'is_ofw' => (bool)($socialStatus->is_ofw ?? false),
                'is_lgbtq' => (bool)($socialStatus->is_lgbtq ?? false),
                'is_senior_citizen' => (bool)($socialStatus->is_senior_citizen ?? false),
                'is_immigrant' => (bool)($socialStatus->is_immigrant ?? false),
                'immigrant_origin' => $socialStatus->immigrant_origin ?? '',
                'region' => $socialStatus->region ?? 'Region VII - Central Visayas',
            ],
            'role' => $user->role,
            'status' => $user->status,
            'isApproved' => (bool)$user->is_approved,
            'is_approved' => (bool)$user->is_approved,
            'isVerified' => $isVerified,
            'is_verified' => $isVerified,
            'is_contact_verified' => (bool)$user->is_contact_verified,
            'isContactVerified' => (bool)$user->is_contact_verified,
            'account_status' => $user->account_status ?? ($isVerified ? 'verified' : ($user->is_approved ? 'pending_review' : 'unverified')),
            'accountStatus' => $user->account_status ?? ($isVerified ? 'verified' : ($user->is_approved ? 'pending_review' : 'unverified')),
            'is_profile_complete' => $isProfileComplete,
            'missing_requirements' => array_values($missingRequirements),
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

        if ($user->status === 'inactive') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is inactive. Please contact support.',
            ], 403);
        }

        // Restrict mobile app exclusively to Jobseekers
        if ($user->role !== 'jobseeker') {
            return response()->json([
                'success' => false,
                'message' => 'Access restricted. The TrabaGo mobile application is exclusively for Jobseekers. Admins, Trainers, and JPOs must log in via the Web Portal.',
            ], 403);
        }

        if ($user->role === 'jobseeker' && !$user->jobseeker) {
            $nameParts = explode(' ', $user->full_name ?? explode('@', $user->email)[0], 2);
            Jobseeker::create([
                'user_id' => $user->user_id,
                'first_name' => $nameParts[0] ?? 'Jobseeker',
                'last_name' => $nameParts[1] ?? '',
                'email' => $user->email,
                'employment_status' => 'Looking for job',
                'citizenship' => 'Filipino',
            ]);
            $user->load(['jobseeker.details', 'jobseeker.skills', 'jobseeker.preferences', 'jobseeker.socialStatus', 'profile']);
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
     * Send OTP verification code to email or phone number.
     * Enforces duplicate account prevention for registration.
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'nullable|string|max:191',
            'email' => 'nullable|email|max:191',
            'phone_number' => 'nullable|string|max:50',
            'mobile_number' => 'nullable|string|max:50',
            'type' => 'nullable|string|in:registration,recovery,login',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->input('email', filter_var($request->input('identifier'), FILTER_VALIDATE_EMAIL) ? $request->input('identifier') : null);
        $phone = $request->input('phone_number', $request->input('mobile_number', !filter_var($request->input('identifier'), FILTER_VALIDATE_EMAIL) ? $request->input('identifier') : null));
        $identifier = $email ?: $phone;

        if (!$identifier) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a valid email address or phone number.',
            ], 422);
        }

        $type = $request->input('type', 'registration');

        // B. Duplicate Check & Account Retrieval Flow (auth-implementation-patterns + error-handling-patterns)
        if ($type === 'registration') {
            $existingUser = User::where(function ($q) use ($email, $phone) {
                if ($email) $q->where('email', $email);
                if ($phone) $q->orWhere('phone_number', $phone);
            })->orWhereHas('jobseeker', function ($q) use ($email, $phone) {
                if ($email) $q->where('email', $email);
                if ($phone) $q->orWhere('mobile_number', $phone);
            })->first();

            if ($existingUser) {
                return response()->json([
                    'success' => false,
                    'error' => 'ACCOUNT_ALREADY_EXISTS',
                    'message' => 'This email or phone number is already registered. Would you like to retrieve your existing account?',
                    'identifier' => $existingUser->email ?: $identifier,
                ], 409);
            }
        } elseif ($type === 'recovery') {
            $existingUser = User::where('email', $email)
                ->when($phone, fn($q) => $q->orWhere('phone_number', $phone))
                ->orWhereHas('jobseeker', function ($q) use ($email, $phone) {
                    if ($email) $q->where('email', $email);
                    if ($phone) $q->orWhere('mobile_number', $phone);
                })->first();

            if (!$existingUser) {
                return response()->json([
                    'success' => false,
                    'error' => 'ACCOUNT_NOT_FOUND',
                    'message' => 'No account found matching this email or phone number.',
                ], 404);
            }
        }

        // Generate 6-digit cryptographic random OTP code
        $otpCode = (string) random_int(100000, 999999);

        // Invalidate previous unverified OTPs for this identifier
        \App\Models\OtpVerification::where('identifier', $identifier)
            ->whereNull('verified_at')
            ->delete();

        // Create new verification record (10 minute expiry)
        \App\Models\OtpVerification::create([
            'identifier' => $identifier,
            'otp_code' => $otpCode,
            'type' => $type,
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
        ]);

        // In production dispatch SMS / Email. For dev/demo, log and return preview
        \Illuminate\Support\Facades\Log::info("TrabaGo OTP for {$identifier} ({$type}): {$otpCode}");

        return response()->json([
            'success' => true,
            'message' => "A 6-digit verification code has been sent to {$identifier}.",
            'identifier' => $identifier,
            'type' => $type,
            'expires_in_minutes' => 10,
            'otp_preview' => app()->environment('local', 'testing') ? $otpCode : null,
        ]);
    }

    /**
     * Verify submitted OTP code.
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string',
            'otp_code' => 'required|string|min:4|max:10',
            'type' => 'nullable|string|in:registration,recovery,login',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $type = $request->input('type', 'registration');
        $otp = \App\Models\OtpVerification::where('identifier', $request->identifier)
            ->where('type', $type)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (!$otp) {
            return response()->json([
                'success' => false,
                'error' => 'OTP_EXPIRED_OR_NOT_FOUND',
                'message' => 'No pending OTP verification code found. Please request a new code.',
            ], 422);
        }

        if ($otp->expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'error' => 'OTP_EXPIRED',
                'message' => 'The verification code has expired. Please request a new code.',
            ], 422);
        }

        if ($otp->attempts >= 5) {
            return response()->json([
                'success' => false,
                'error' => 'TOO_MANY_ATTEMPTS',
                'message' => 'Too many failed verification attempts. Please request a new code.',
            ], 429);
        }

        if (trim($otp->otp_code) !== trim($request->otp_code)) {
            $otp->increment('attempts');
            $remaining = max(0, 5 - $otp->attempts);
            return response()->json([
                'success' => false,
                'error' => 'INVALID_OTP',
                'message' => "Invalid verification code. {$remaining} attempts remaining.",
            ], 422);
        }

        $otp->update(['verified_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Verification code verified successfully.',
            'verified' => true,
            'verification_token' => base64_encode("{$otp->id}:{$otp->identifier}:{$otp->otp_code}"),
        ]);
    }

    /**
     * Account recovery via OTP.
     */
    public function recoverAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string',
            'otp_code' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $otp = \App\Models\OtpVerification::where('identifier', $request->identifier)
            ->where('type', 'recovery')
            ->where('otp_code', $request->otp_code)
            ->latest()
            ->first();

        if (!$otp || $otp->expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'error' => 'INVALID_OTP',
                'message' => 'Invalid or expired recovery code.',
            ], 422);
        }

        $identifier = $request->identifier;
        $user = User::where('email', $identifier)
            ->orWhere('phone_number', $identifier)
            ->orWhereHas('jobseeker', function ($q) use ($identifier) {
                $q->where('email', $identifier)->orWhere('mobile_number', $identifier);
            })->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'ACCOUNT_NOT_FOUND',
                'message' => 'No account found matching this contact info.',
            ], 404);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'is_contact_verified' => true,
        ]);
        $otp->update(['verified_at' => now()]);

        $token = $user->createToken('trabago-mobile-app')->plainTextToken;
        $formattedUser = $this->formatUserProfile($user->fresh(['jobseeker.details', 'jobseeker.skills', 'jobseeker.preferences', 'jobseeker.socialStatus', 'profile']));

        return response()->json([
            'success' => true,
            'message' => 'Password reset and account retrieved successfully.',
            'token' => $token,
            'user' => $formattedUser,
        ]);
    }

    /**
     * Register a new user and generate token.
     */
    public function register(Request $request)
    {
        $phone = $request->input('phone', $request->input('mobile_number', $request->input('phone_number')));
        $email = $request->input('email');

        // Duplicate account prevention check (backend-security-coder + auth-implementation-patterns)
        $existing = User::where('email', $email)
            ->when($phone, fn($q) => $q->orWhere('phone_number', $phone))
            ->orWhereHas('jobseeker', function ($q) use ($email, $phone) {
                if ($email) $q->where('email', $email);
                if ($phone) $q->orWhere('mobile_number', $phone);
            })
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'error' => 'ACCOUNT_ALREADY_EXISTS',
                'message' => 'This email or phone number is already registered. Would you like to retrieve your existing account?',
                'identifier' => $existing->email ?: ($email ?: $phone),
            ], 409);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'nullable|string|in:jobseeker,employer',
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
            'employment_status' => 'nullable|string|max:100',
            'address' => 'nullable|array',
            'work_experience' => 'nullable|array',
            'eligibility' => 'nullable|array',
            'language_proficiency' => 'nullable|array',
            'training_certificates' => 'nullable|array',
            'skills' => 'nullable|array',
            'skills_century' => 'nullable|array',
            'skills_informal_tech' => 'nullable|array',
            'occupation1' => 'nullable|string|max:150',
            'occupation2' => 'nullable|string|max:150',
            'occupation3' => 'nullable|string|max:150',
            'industry1' => 'nullable|string|max:150',
            'industry2' => 'nullable|string|max:150',
            'industry3' => 'nullable|string|max:150',
            'preferred_location' => 'nullable|string|max:255',
            'salary_expectation' => 'nullable|string|max:100',
            'salary_min' => 'nullable|string|max:30',
            'salary_max' => 'nullable|string|max:30',
            'is_4ps' => 'nullable|boolean',
            'household_id' => 'nullable|string|max:100',
            'is_ofw' => 'nullable|boolean',
            'is_pwd' => 'nullable|boolean',
            'pwd_type' => 'nullable|string|max:255',
            'is_lgbtq' => 'nullable|boolean',
            'is_senior_citizen' => 'nullable|boolean',
            'is_immigrant' => 'nullable|boolean',
            'immigrant_origin' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:100',
            'education' => 'nullable',
            'phone' => 'nullable|string|max:50',
            'mobile_number' => 'nullable|string|max:50',
            'phone_number' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $salaryMin = $this->normalizeSalary($request->input('salary_min'));
        $salaryMax = $this->normalizeSalary($request->input('salary_max'));
        if ($salaryMin !== null && $salaryMax !== null && $salaryMax < $salaryMin) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum salary must be greater than or equal to minimum salary.',
                'errors' => ['salary_max' => ['Maximum salary must be greater than or equal to minimum salary.']],
            ], 422);
        }

        // 👇 DUPLICATE ACCOUNT CHECK 👇
        if ($request->input('role', 'jobseeker') === 'jobseeker') {
            $firstName = $request->input('firstName', $request->input('first_name'));
            $lastName = $request->input('lastName', $request->input('last_name'));
            $birthDate = $request->input('birthDate', $request->input('birth_date'));

            if ($firstName && $lastName && $birthDate) {
                // Convert the input date to a Y-m-d string to compare against the DB
                $dateStr = \Carbon\Carbon::parse($birthDate)->format('Y-m-d');

                $duplicate = Jobseeker::where('first_name', $firstName)
                    ->where('last_name', $lastName)
                    ->whereDate('birth_date', $dateStr)
                    ->exists();

                if ($duplicate && !$request->has('confirm_duplicate')) {
                    // If the frontend expects JSON (our JS interceptor)
                    if ($request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'error' => 'POSSIBLE_DUPLICATE',
                            'message' => 'A profile with this name and birthdate already exists. If this is you, please log in. If you are a different person with the same name, click OK to proceed.',
                        ], 409);
                    }

                    // Fallback just in case
                    return back()->withInput()->with('duplicate_warning', 'A profile with this name and birthdate already exists.');
                }
            }
        }
        // 👆 END OF DUPLICATE CHECK 👆
        try {
            DB::beginTransaction();

            $firstName = $request->input('firstName', $request->input('first_name', 'Jobseeker'));
            $lastName = $request->input('lastName', $request->input('last_name', ''));
            $fullName = trim("{$firstName} {$lastName}");
            $role = $request->input('role', 'jobseeker');

            $user = User::create([
                'email' => $request->email,
                'phone_number' => $phone ?: null,
                'password' => Hash::make($request->password),
                'role' => $role,
                'status' => 'active',
                'is_approved' => true,
                'is_contact_verified' => true,
                'account_status' => 'unverified',
            ]);

            if ($role === 'employer') {
                \App\Models\Employer::create([
                    'user_id' => $user->user_id,
                    'company_name' => $request->input('company_name', $request->input('company', $fullName ?: 'Partner Employer')),
                    'is_accredited' => false,
                ]);
            } else {
                $jobseeker = Jobseeker::create([
                    'user_id' => $user->user_id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'middle_name' => $request->input('middleName', $request->input('middle_name')),
                    'birth_date' => $request->input('birthDate', $request->input('birth_date')),
                    'sex' => $request->input('sex', 'Male'),
                    'civil_status' => $request->input('civilStatus', $request->input('civil_status', 'Single')),
                    'citizenship' => $request->input('citizenship', 'Filipino'),
                    'mobile_number' => $phone,
                    'employment_status' => $request->input('employment_status', 'Looking for job'),
                    'email' => $user->email,
                ]);

                // Save details
                $details = JobseekerDetail::create([
                    'jobseeker_id' => $jobseeker->jobseeker_id,
                    'education' => $request->has('education')
                        ? (is_array($request->education) ? $request->education : ['level' => (string)$request->education])
                        : ['level' => 'College Graduate'],
                    'address' => $request->input('address', [
                        'street' => '',
                        'barangay' => '',
                        'city' => 'Cebu City',
                        'province' => 'Cebu',
                        'zip' => '',
                        'postal_code' => '',
                        'country' => 'Philippines',
                        'full' => 'Cebu City, Cebu'
                    ]),
                    'work_experience' => $request->input('work_experience', []),
                    'eligibility' => $request->input('eligibility', []),
                    'language_proficiency' => $request->input('language_proficiency', ['English', 'Filipino', 'Cebuano']),
                    'training_certificates' => [],
                ]);

                // Save skills
                $skillsInput = $request->input('skills', []);
                if (is_string($skillsInput)) {
                    $skillsInput = array_map('trim', explode(',', $skillsInput));
                }
                if (is_array($skillsInput)) {
                    foreach ($skillsInput as $skill) {
                        $skillName = is_array($skill) ? ($skill['name'] ?? $skill['skill_name'] ?? '') : $skill;
                        $skillType = is_array($skill) ? ($skill['type'] ?? $skill['skill_type'] ?? 'technical') : 'technical';
                        if (!is_string($skillName) || trim($skillName) === '') {
                            continue;
                        }
                        JobseekerSkill::create([
                            'jobseeker_id' => $jobseeker->jobseeker_id,
                            'skill_name' => trim($skillName),
                            'skill_type' => $skillType,
                        ]);
                    }
                }
                foreach ([
                    'skills_century' => '21st_century',
                    'skills_informal_tech' => 'technical',
                ] as $field => $skillType) {
                    foreach ((array) $request->input($field, []) as $skillName) {
                        if (is_string($skillName) && trim($skillName) !== '') {
                            JobseekerSkill::create([
                                'jobseeker_id' => $jobseeker->jobseeker_id,
                                'skill_name' => trim($skillName),
                                'skill_type' => $skillType,
                            ]);
                        }
                    }
                }

                // Save preferences
                $salaryExpectation = $request->input('salary_expectation');
                $salaryMin = $this->normalizeSalary($request->input('salary_min'));
                $salaryMax = $this->normalizeSalary($request->input('salary_max'));
                if ($salaryMin !== null || $salaryMax !== null) {
                    $salaryExpectation = $this->formatSalaryRange($salaryMin, $salaryMax);
                }
                JobPreference::create([
                    'jobseeker_id' => $jobseeker->jobseeker_id,
                    'occupation1' => $request->input('occupation1'),
                    'occupation2' => $request->input('occupation2'),
                    'occupation3' => $request->input('occupation3'),
                    'industry1' => $request->input('industry1'),
                    'industry2' => $request->input('industry2'),
                    'industry3' => $request->input('industry3'),
                    'preferred_location' => $request->input('preferred_location', 'Cebu City'),
                    'salary_expectation' => $salaryExpectation,
                ]);

                // Save social status
                SocialStatus::create([
                    'jobseeker_id' => $jobseeker->jobseeker_id,
                    'is_pwd' => $request->boolean('is_pwd'),
                    'pwd_type' => $request->boolean('is_pwd') ? $request->input('pwd_type') : null,
                    'is_4ps' => $request->boolean('is_4ps'),
                    'household_id' => $request->boolean('is_4ps') ? $request->input('household_id') : null,
                    'is_ofw' => $request->boolean('is_ofw'),
                    'is_lgbtq' => $request->boolean('is_lgbtq'),
                    'is_senior_citizen' => $request->boolean('is_senior_citizen'),
                    'is_immigrant' => $request->boolean('is_immigrant'),
                    'immigrant_origin' => $request->boolean('is_immigrant') ? $request->input('immigrant_origin') : null,
                    'region' => $request->input('region', 'Region VII - Central Visayas'),
                ]);
            }

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
        if ($user->role !== 'jobseeker') {
            return response()->json([
                'success' => false,
                'message' => 'Access restricted. The mobile profile is exclusively for Jobseekers.',
            ], 403);
        }
        if ($user->role === 'jobseeker' && !$user->jobseeker) {
            $nameParts = explode(' ', $user->full_name ?? explode('@', $user->email)[0], 2);
            Jobseeker::create([
                'user_id' => $user->user_id,
                'first_name' => $nameParts[0] ?? 'Jobseeker',
                'last_name' => $nameParts[1] ?? '',
                'email' => $user->email,
                'employment_status' => 'Looking for job',
                'citizenship' => 'Filipino',
            ]);
            $user->load(['jobseeker.details', 'jobseeker.skills', 'jobseeker.preferences', 'jobseeker.socialStatus', 'profile']);
        }
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
        if ($user->role !== 'jobseeker') {
            return response()->json([
                'success' => false,
                'message' => 'Access restricted. Only Jobseekers can update profile details.',
            ], 403);
        }

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
        if ($request->has('education_details') && is_array($request->education_details)) {
            $details->education = $request->education_details;
        } elseif ($request->has('education')) {
            $details->education = is_array($request->education) ? $request->education : ['level' => (string)$request->education];
        }
        if ($request->has('work_experience')) {
            $details->work_experience = is_array($request->work_experience) ? $request->work_experience : $details->work_experience;
        }
        if ($request->has('bio')) {
            $w = is_array($details->work_experience) ? $details->work_experience : [];
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
            if (isset($prefData['occupation3'])) $pref->occupation3 = $prefData['occupation3'];
            if (isset($prefData['industry1'])) $pref->industry1 = $prefData['industry1'];
            if (isset($prefData['industry2'])) $pref->industry2 = $prefData['industry2'];
            if (isset($prefData['industry3'])) $pref->industry3 = $prefData['industry3'];
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
            if (isset($socData['is_lgbtq'])) $soc->is_lgbtq = (bool)$socData['is_lgbtq'];
            if (isset($socData['is_senior_citizen'])) $soc->is_senior_citizen = (bool)$socData['is_senior_citizen'];
            if (isset($socData['is_immigrant'])) $soc->is_immigrant = (bool)$socData['is_immigrant'];
            if (isset($socData['immigrant_origin'])) $soc->immigrant_origin = $socData['immigrant_origin'];
            if (isset($socData['region'])) $soc->region = $socData['region'];
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

    private function normalizeSalary(mixed $value): ?int
    {
        $digits = preg_replace('/[^0-9]/', '', (string) $value);
        return $digits === '' ? null : (int) $digits;
    }

    private function formatSalaryRange(?int $minimum, ?int $maximum): string
    {
        if ($minimum !== null && $maximum !== null) {
            return '₱' . number_format($minimum) . ' - ₱' . number_format($maximum) . ' / month';
        }
        if ($minimum !== null) {
            return '₱' . number_format($minimum) . '+ / month';
        }

        return $maximum !== null ? 'Up to ₱' . number_format($maximum) . ' / month' : '';
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
