<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class JobseekerRegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('jobseeker.register');
    }

    public function register(Request $request)
    {
        foreach (['salary_min', 'salary_max'] as $salaryField) {
            $salaryValue = $request->input($salaryField);
            if (is_string($salaryValue)) {
                $request->merge([$salaryField => str_replace(',', '', trim($salaryValue))]);
            }
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            // User table fields
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:jobseeker',

            // Jobseeker fields
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'birth_date' => 'required|date',
            'sex' => 'required|string|max:20',
            'civil_status' => 'required|string|max:50',
            'citizenship' => 'required|string|max:100',
            'mobile_number' => 'required|string|max:50|unique:users,phone_number',
            'employment_status' => 'required|string|max:50',

            // Jobseeker Details (JSON fields)
            'address' => 'nullable|array',
            'education' => 'nullable|array',
            'work_experience' => 'nullable|array',
            'eligibility' => 'nullable|array',
            'language_proficiency' => 'nullable|array',
            'training_certificates' => 'nullable|array',

            // Skills
            'skills' => 'nullable|array',
            'skills_century' => 'nullable|array',
            'skills_informal_tech' => 'nullable|array',

            // Job Preferences
            'occupation1' => 'nullable|string|max:150',
            'occupation2' => 'nullable|string|max:150',
            'occupation3' => 'nullable|string|max:150',
            'industry1' => 'nullable|string|max:150',
            'industry2' => 'nullable|string|max:150',
            'industry3' => 'nullable|string|max:150',
            'preferred_location' => 'nullable|string|max:255',
            'salary_expectation' => 'nullable|string|max:100',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',

            // Social Status
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
        ], [
            'email.unique' => 'An account with this email already exists. Please contact your JPO or an administrator to update your email or reset your password. For your security, never share your password.',
            'mobile_number.unique' => 'An account with this mobile number already exists. Please contact your JPO or an administrator to update your email or reset your password. For your security, never share your password.',
        ]);

        if (
            $request->filled('salary_min')
            && $request->filled('salary_max')
            && (float) $request->input('salary_max') < (float) $request->input('salary_min')
        ) {
            $validator->errors()->add('salary_max', 'Maximum salary must be greater than or equal to minimum salary.');
        }

        // 1. Check standard validation first
        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Please review the registration details and try again.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        // 2. DUPLICATE CHECK (Name + DOB)
        if ($request->input('role', 'jobseeker') === 'jobseeker') {
            $firstName = $request->input('first_name');
            $lastName = $request->input('last_name');
            $birthDate = $request->input('birth_date');

            if ($firstName && $lastName && $birthDate) {
                $dateStr = \Carbon\Carbon::parse($birthDate)->format('Y-m-d');

                $duplicate = DB::table('jobseekers')
                    ->whereRaw('LOWER(LTRIM(RTRIM(first_name))) = ?', [\Illuminate\Support\Str::lower(trim($firstName))])
                    ->whereRaw('LOWER(LTRIM(RTRIM(last_name))) = ?', [\Illuminate\Support\Str::lower(trim($lastName))])
                    ->whereRaw("CONVERT(varchar(10), birth_date, 120) = ?", [$dateStr])
                    ->exists();

                if ($duplicate && !$request->has('confirm_duplicate')) {
                    // If the request expects JSON, return the popup message
                    if ($request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'error' => 'POSSIBLE_DUPLICATE',
                            'message' => 'A jobseeker with the same name and birthdate already exists. If this is you, please log in. If you are a different person, click Continue to proceed with registration.',
                        ], 409);
                    }

                    // If the form submits normally (JS failed), show this fallback error
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'A jobseeker with the same name and birthdate already exists. If this is you, please log in. If you are a different person, confirm the warning to continue registration.');
                }
            }
        }
        try {
            // Begin transaction
            DB::beginTransaction();

            // 1. Create User
            $user = DB::table('users')->insertGetId([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'jobseeker',
                'status' => 'active',
                'is_approved' => 0,
                'phone_number' => $request->mobile_number ?? null,
                'is_contact_verified' => 1,
                'account_status' => 'unverified',
                'created_at' => now(),
            ]);

            // 2. Create Jobseeker Profile
            $jobseekerId = DB::table('jobseekers')->insertGetId([
                'user_id' => $user,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
                'birth_date' => $request->birth_date,
                'sex' => $request->sex,
                'civil_status' => $request->civil_status,
                'citizenship' => $request->citizenship,
                'mobile_number' => $request->mobile_number,
                'email' => $request->email,
                'employment_status' => $request->employment_status,
            ]);

            // 3. Create Jobseeker Details (JSON)
            DB::table('jobseeker_details')->insert([
                'jobseeker_id' => $jobseekerId,
                'address' => json_encode($request->address ?? []),
                'education' => json_encode($request->education ?? []),
                'work_experience' => json_encode($request->work_experience ?? []),
                'eligibility' => json_encode($request->eligibility ?? []),
                'language_proficiency' => json_encode($request->language_proficiency ?? []),
                'training_certificates' => json_encode($request->training_certificates ?? []),
            ]);

            // 4. Create Skills
            if ($request->has('skills')) {
                foreach ($request->skills as $skill) {
                    // Check if skill is an array or string
                    if (is_array($skill)) {
                        $skillName = $skill['name'] ?? null;
                        $skillType = $skill['type'] ?? 'technical';
                    } else {
                        $skillName = $skill;
                        $skillType = 'technical';
                    }

                    // Only insert if skill name is not empty
                    if (!empty($skillName)) {
                        DB::table('jobseeker_skills')->insert([
                            'jobseeker_id' => $jobseekerId,
                            'skill_name' => $skillName,
                            'skill_type' => $skillType,
                        ]);
                    }
                }
            }

            // 4b. 21st Century Skills (DOLE NSRP Section VII)
            if ($request->has('skills_century')) {
                foreach ((array) $request->skills_century as $cSkill) {
                    if (!empty($cSkill)) {
                        DB::table('jobseeker_skills')->insert([
                            'jobseeker_id' => $jobseekerId,
                            'skill_name' => $cSkill,
                            'skill_type' => '21st_century',
                        ]);
                    }
                }
            }

            // 4c. Technical Skills Acquired Without Formal Training (DOLE NSRP Section IX)
            if ($request->has('skills_informal_tech')) {
                foreach ((array) $request->skills_informal_tech as $iSkill) {
                    if (!empty($iSkill)) {
                        DB::table('jobseeker_skills')->insert([
                            'jobseeker_id' => $jobseekerId,
                            'skill_name' => $iSkill,
                            'skill_type' => 'technical',
                        ]);
                    }
                }
            }

            // 5. Create Job Preferences
            $salaryExpectation = $request->input('salary_expectation');
            $salaryMin = $request->input('salary_min');
            $salaryMax = $request->input('salary_max');
            if ($salaryMin !== null || $salaryMax !== null) {
                if ($salaryMin !== null && $salaryMax !== null) {
                    $salaryExpectation = '₱' . number_format((float) $salaryMin) . ' - ₱' . number_format((float) $salaryMax) . ' / month';
                } elseif ($salaryMin !== null) {
                    $salaryExpectation = '₱' . number_format((float) $salaryMin) . '+ / month';
                } else {
                    $salaryExpectation = 'Up to ₱' . number_format((float) $salaryMax) . ' / month';
                }
            }
            DB::table('job_preferences')->insert([
                'jobseeker_id' => $jobseekerId,
                'occupation1' => $request->occupation1,
                'occupation2' => $request->occupation2,
                'occupation3' => $request->occupation3,
                'industry1' => $request->industry1,
                'industry2' => $request->industry2,
                'industry3' => $request->industry3,
                'preferred_location' => $request->preferred_location,
                'salary_expectation' => $salaryExpectation,
            ]);

            // 6. Create Social Status
            DB::table('social_status')->insert([
                'jobseeker_id' => $jobseekerId,
                'is_4ps' => $request->has('is_4ps'),
                'household_id' => $request->household_id,
                'is_ofw' => $request->has('is_ofw'),
                'is_pwd' => $request->has('is_pwd'),
                'pwd_type' => $request->pwd_type,
                'is_lgbtq' => $request->has('is_lgbtq'),
                'is_senior_citizen' => $request->has('is_senior_citizen'),
                'is_immigrant' => $request->has('is_immigrant'),
                'immigrant_origin' => $request->immigrant_origin,
                'region' => $request->input('region', 'Region VII - Central Visayas'),
            ]);

            // 7. Create notification for admin (only if admin exists)
            $admin = DB::table('users')->where('role', 'admin')->first();
            if ($admin) {
                DB::table('notifications')->insert([
                    'user_id' => $admin->user_id,
                    'title' => 'New Jobseeker Registration',
                    'message' => "A new jobseeker has registered: {$request->first_name} {$request->last_name}",
                    'type' => 'approval',
                    'is_read' => 0,
                    'created_at' => now(),
                ]);
            }
            // Commit transaction
            DB::commit();

            // Redirect to success page
            return redirect()->route('jobseeker.register.success')
                ->with('success', 'Jobseeker registration successful! You can now log in to set up your profile and explore job openings.');
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            $isDuplicatePhone = $e instanceof QueryException
                && str_contains(strtolower($e->getMessage()), 'users_phone_number_unique');

            $errorMessage = $isDuplicatePhone
                ? 'An account with this mobile number already exists. Please contact your JPO or an administrator to update your email or reset your password. For your security, never share your password.'
                : 'Registration could not be completed. Please try again or contact your JPO or an administrator for assistance.';

            if ($request->wantsJson()) {
                return response()->json(['message' => $errorMessage], 422);
            }

            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    public function success()
    {
        return view('jobseeker.success');
    }
}
