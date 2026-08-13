<?php

namespace App\Http\Controllers;

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
            'mobile_number' => 'required|string|max:50',
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
            
            // Job Preferences
            'occupation1' => 'nullable|string|max:150',
            'occupation2' => 'nullable|string|max:150',
            'occupation3' => 'nullable|string|max:150',
            'industry1' => 'nullable|string|max:150',
            'industry2' => 'nullable|string|max:150',
            'industry3' => 'nullable|string|max:150',
            'preferred_location' => 'nullable|string|max:255',
            'salary_expectation' => 'nullable|string|max:100',
            
            // Social Status
            'is_4ps' => 'nullable|boolean',
            'household_id' => 'nullable|string|max:100',
            'is_ofw' => 'nullable|boolean',
            'is_pwd' => 'nullable|boolean',
            'pwd_type' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
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
                'is_approved' => 0, // Pending admin approval
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

            // 5. Create Job Preferences
            DB::table('job_preferences')->insert([
                'jobseeker_id' => $jobseekerId,
                'occupation1' => $request->occupation1,
                'occupation2' => $request->occupation2,
                'occupation3' => $request->occupation3,
                'industry1' => $request->industry1,
                'industry2' => $request->industry2,
                'industry3' => $request->industry3,
                'preferred_location' => $request->preferred_location,
                'salary_expectation' => $request->salary_expectation,
            ]);

            // 6. Create Social Status
            DB::table('social_status')->insert([
                'jobseeker_id' => $jobseekerId,
                'is_4ps' => $request->has('is_4ps'),
                'household_id' => $request->household_id,
                'is_ofw' => $request->has('is_ofw'),
                'is_pwd' => $request->has('is_pwd'),
                'pwd_type' => $request->pwd_type,
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
                ->with('success', 'Registration successful! Please wait for admin approval.');

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Registration failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function success()
    {
        return view('jobseeker.success');
    }
}