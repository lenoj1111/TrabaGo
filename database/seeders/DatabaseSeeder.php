<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with 1 clean dataset per user/role.
     */
    public function run(): void
    {
        $password = Hash::make('password123');

        // 1. Admin
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@trabago.com'],
            [
                'password' => $password,
                'role' => 'admin',
                'status' => 'active',
                'is_approved' => 1,
                'created_at' => now(),
            ]
        );

        DB::table('user_profiles')->updateOrInsert(
            ['user_id' => $adminUser->user_id],
            [
                'full_name' => 'System Administrator',
                'position' => 'Administrator',
                'department' => 'IT & Systems',
                'office' => 'Main Office',
            ]
        );

        if (DB::table('notifications')->where('user_id', $adminUser->user_id)->count() === 0) {
            DB::table('notifications')->insert([
                'user_id' => $adminUser->user_id,
                'title' => 'System Initialized',
                'message' => 'TrabaGo platform initialized. All systems and core modules operating normally.',
                'type' => 'approval',
                'is_read' => 0,
                'created_at' => now(),
            ]);
        }

        // 2. Trainer
        $trainerUser = User::firstOrCreate(
            ['email' => 'trainer@trabago.com'],
            [
                'password' => $password,
                'role' => 'trainer',
                'status' => 'active',
                'is_approved' => 1,
                'created_at' => now(),
            ]
        );

        $trainerProfile = DB::table('user_profiles')->where('user_id', $trainerUser->user_id)->first();
        if (!$trainerProfile) {
            $trainerProfileId = DB::table('user_profiles')->insertGetId([
                'user_id' => $trainerUser->user_id,
                'full_name' => 'Prof. Maria Santos',
                'position' => 'Senior Vocational Trainer',
                'department' => 'DMDP Skills Training Division',
                'office' => 'Cebu City DMDP Center',
                'specialization' => 'Vocational & Digital Skills',
                'trainer_type' => 'dmdp',
                'is_trainer_approved' => 1,
            ]);
        } else {
            $trainerProfileId = $trainerProfile->profile_id;
        }

        $prog = DB::table('training_programs')->where('trainer_id', $trainerProfileId)->first();
        if (!$prog) {
            $progId = DB::table('training_programs')->insertGetId([
                'trainer_id' => $trainerProfileId,
                'title' => 'Workplace Readiness & Soft Skills',
                'training_type' => 'online',
                'duration_months' => 1,
                'description' => 'Master foundational workplace ethics, professional communication, and interview skills to excel in any industry.',
            ]);

            DB::table('training_topics')->insert([
                'training_id' => $progId,
                'title' => 'Effective Communication in the Workplace',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'topic_order' => 1,
                'questions' => json_encode([
                    [
                        'question' => 'What is the most effective approach when communicating with a team member?',
                        'options' => ['Active listening and clarity', 'Speaking loudly', 'Ignoring feedback', 'Using technical jargon only'],
                        'answer' => 0,
                    ],
                ]),
            ]);
        } else {
            $progId = $prog->training_id;
        }

        if (DB::table('notifications')->where('user_id', $trainerUser->user_id)->count() === 0) {
            DB::table('notifications')->insert([
                'user_id' => $trainerUser->user_id,
                'title' => 'Trainer Portal Active',
                'message' => 'Your vocational training program has been published and is accepting enrollments.',
                'type' => 'approval',
                'is_read' => 0,
                'created_at' => now(),
            ]);
        }

        // 3. JPO
        $jpoUser = User::firstOrCreate(
            ['email' => 'jpo@trabago.com'],
            [
                'password' => $password,
                'role' => 'jpo',
                'status' => 'active',
                'is_approved' => 1,
                'created_at' => now(),
            ]
        );

        $jpoProfile = DB::table('user_profiles')->where('user_id', $jpoUser->user_id)->first();
        if (!$jpoProfile) {
            $jpoProfileId = DB::table('user_profiles')->insertGetId([
                'user_id' => $jpoUser->user_id,
                'full_name' => 'Atty. Roberto Tan',
                'position' => 'Job Placement Officer',
                'department' => 'Employment Facilitation Unit',
                'office' => 'PESD Cebu',
            ]);
        } else {
            $jpoProfileId = $jpoProfile->profile_id;
        }

        if (DB::table('notifications')->where('user_id', $jpoUser->user_id)->count() === 0) {
            DB::table('notifications')->insert([
                'user_id' => $jpoUser->user_id,
                'title' => 'JPO Evaluation Desk Ready',
                'message' => 'Your placement officer workspace is ready to review candidate referrals and accreditations.',
                'type' => 'approval',
                'is_read' => 0,
                'created_at' => now(),
            ]);
        }

        // 4. Employer
        $employerUser = User::firstOrCreate(
            ['email' => 'employer@techcorp.com'],
            [
                'password' => $password,
                'role' => 'employer',
                'status' => 'active',
                'is_approved' => 1,
                'created_at' => now(),
            ]
        );

        $employer = DB::table('employers')->where('user_id', $employerUser->user_id)->first();
        if (!$employer) {
            $employerId = DB::table('employers')->insertGetId([
                'user_id' => $employerUser->user_id,
                'company_name' => 'TechCorp Solutions Inc.',
                'is_accredited' => 1,
                'accredited_at' => now()->toDateString(),
            ]);
        } else {
            $employerId = $employer->employer_id;
        }

        if (DB::table('employer_accreditation')->where('employer_id', $employerId)->count() === 0) {
            DB::table('employer_accreditation')->insert([
                'employer_id' => $employerId,
                'documents' => json_encode([
                    'sec_dti_registration' => 'documents/demo_sec_dti.pdf',
                    'business_permit' => 'documents/demo_permit.pdf',
                    'bir_2303' => 'documents/demo_bir.pdf',
                    'dole_certificate' => 'documents/demo_dole.pdf',
                ]),
                'ocr_classified_document_type' => 'Business Permit & SEC Registration',
                'ocr_validation_status' => 'auto_approved',
                'status' => 'admin_approved',
                'jpo_reviewed' => 1,
                'jpo_reviewed_at' => now()->subDays(10),
                'jpo_remarks' => 'Verified corporate documents and BIR 2303 registration.',
                'supervisor_approved' => 1,
                'supervisor_approved_at' => now()->subDays(9),
                'supervisor_remarks' => 'Accreditation paperwork validated.',
                'admin_approved' => 1,
                'admin_approved_at' => now()->subDays(8)->toDateString(),
                'submitted_at' => now()->subDays(12)->toDateString(),
                'approved_at' => now()->subDays(8)->toDateString(),
            ]);
        }

        $job = DB::table('job_postings')->where('employer_id', $employerId)->first();
        if (!$job) {
            $jobId = DB::table('job_postings')->insertGetId([
                'employer_id' => $employerId,
                'title' => 'Junior Web Developer',
                'description' => 'We are seeking an enthusiastic Junior Web Developer to assist in building modern web and mobile applications using modern frameworks.',
                'qualifications' => "• Bachelor's degree in IT/CS or vocational certification\n• Basic knowledge of JavaScript, PHP, and SQL\n• Good problem-solving and communication skills",
                'vacancy_count' => 3,
                'valid_until' => now()->addMonths(3)->toDateString(),
                'accepts_disability' => 1,
                'disability_type' => 'Visual/Hearing impaired with accommodations',
                'status' => 'approved',
                'created_by' => 'employer',
                'created_at' => now()->toDateString(),
                'approved_at' => now()->toDateString(),
            ]);
        } else {
            $jobId = $job->job_id;
        }

        if (DB::table('placement_reports')->where('employer_id', $employerId)->count() === 0) {
            DB::table('placement_reports')->insert([
                'employer_id' => $employerId,
                'jpo_id' => $jpoProfileId,
                'report_type' => 'employer_monthly',
                'report_month' => now()->startOfMonth()->toDateString(),
                'report_data' => json_encode(['hires' => 1, 'positions' => ['Junior Web Developer']]),
                'status' => 'approved',
                'admin_remarks' => 'Monthly report verified and archived.',
                'approved_at' => now()->toDateString(),
                'jpo_evaluated' => 1,
                'jpo_evaluated_at' => now(),
                'jpo_remarks' => 'All placement records verified with payroll.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (DB::table('notifications')->where('user_id', $employerUser->user_id)->count() === 0) {
            DB::table('notifications')->insert([
                'user_id' => $employerUser->user_id,
                'title' => 'Company Profile Verified',
                'message' => 'TechCorp Solutions Inc. is accredited. Your job opening for Junior Web Developer is published.',
                'type' => 'approval',
                'is_read' => 0,
                'created_at' => now(),
            ]);
        }

        // 5. Jobseeker
        $jobseekerUser = User::firstOrCreate(
            ['email' => 'jobseeker@trabago.com'],
            [
                'password' => $password,
                'role' => 'jobseeker',
                'status' => 'active',
                'is_approved' => 1,
                'created_at' => now(),
            ]
        );

        $jobseeker = DB::table('jobseekers')->where('user_id', $jobseekerUser->user_id)->first();
        if (!$jobseeker) {
            $jobseekerId = DB::table('jobseekers')->insertGetId([
                'user_id' => $jobseekerUser->user_id,
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'middle_name' => 'Santos',
                'birth_date' => '1998-05-15',
                'sex' => 'Male',
                'civil_status' => 'Single',
                'citizenship' => 'Filipino',
                'mobile_number' => '09123456789',
                'email' => 'jobseeker@trabago.com',
                'employment_status' => 'Unemployed',
                'hired_company' => null,
            ]);
        } else {
            $jobseekerId = $jobseeker->jobseeker_id;
        }

        DB::table('jobseeker_details')->updateOrInsert(
            ['jobseeker_id' => $jobseekerId],
            [
                'address' => json_encode(['city' => 'Cebu City', 'province' => 'Cebu', 'barangay' => 'Lahug']),
                'education' => json_encode(['BS Information Technology', 'University of Cebu']),
                'work_experience' => json_encode([]),
                'eligibility' => json_encode([]),
                'language_proficiency' => json_encode(['English', 'Filipino', 'Cebuano']),
                'training_certificates' => json_encode([]),
            ]
        );

        if (DB::table('jobseeker_skills')->where('jobseeker_id', $jobseekerId)->count() === 0) {
            $skills = ['PHP', 'Laravel', 'JavaScript', 'HTML/CSS', 'MySQL'];
            foreach ($skills as $s) {
                DB::table('jobseeker_skills')->insert([
                    'jobseeker_id' => $jobseekerId,
                    'skill_name' => $s,
                    'skill_type' => 'technical',
                ]);
            }
        }

        DB::table('job_preferences')->updateOrInsert(
            ['jobseeker_id' => $jobseekerId],
            [
                'occupation1' => 'Junior Web Developer',
                'industry1' => 'Information Technology',
                'preferred_location' => 'Cebu City',
                'salary_expectation' => '25000',
            ]
        );

        DB::table('social_status')->updateOrInsert(
            ['jobseeker_id' => $jobseekerId],
            [
                'is_4ps' => 0,
                'is_ofw' => 0,
                'is_pwd' => 0,
            ]
        );

        if (DB::table('job_applications')->where('jobseeker_id', $jobseekerId)->count() === 0) {
            DB::table('job_applications')->insert([
                'job_id' => $jobId,
                'jobseeker_id' => $jobseekerId,
                'status' => 'pending',
                'referred_by_jpo' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (DB::table('training_enrollments')->where('jobseeker_id', $jobseekerId)->count() === 0) {
            DB::table('training_enrollments')->insert([
                'jobseeker_id' => $jobseekerId,
                'training_id' => $progId,
                'training_type' => 'online',
                'status' => 'enrolled',
                'start_date' => now()->toDateString(),
            ]);
        }

        if (DB::table('notifications')->where('user_id', $jobseekerUser->user_id)->count() === 0) {
            DB::table('notifications')->insert([
                'user_id' => $jobseekerUser->user_id,
                'title' => 'Welcome to TrabaGo!',
                'message' => 'Your jobseeker profile is ready. You have applied to Junior Web Developer at TechCorp Solutions Inc.',
                'type' => 'approval',
                'is_read' => 0,
                'created_at' => now(),
            ]);
        }
    }
}
