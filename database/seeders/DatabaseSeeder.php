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
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin Account
        $admin = User::where('email', 'admin@trabago.com')->first();
        if (!$admin) {
            $adminId = DB::table('users')->insertGetId([
                'email' => 'admin@trabago.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status' => 'active',
                'is_approved' => 1,
                'created_at' => now(),
            ]);

            DB::table('user_profiles')->insert([
                'user_id' => $adminId,
                'full_name' => 'System Administrator',
                'position' => 'Administrator',
                'department' => 'IT & Systems',
                'office' => 'Main Office',
            ]);
        }

        // 2. Demo Trainer Account
        $trainer = User::where('email', 'trainer@trabago.com')->first();
        $trainerProfileId = null;
        if (!$trainer) {
            $trainerUserId = DB::table('users')->insertGetId([
                'email' => 'trainer@trabago.com',
                'password' => Hash::make('password123'),
                'role' => 'trainer',
                'status' => 'active',
                'is_approved' => 1,
                'created_at' => now(),
            ]);

            $trainerProfileId = DB::table('user_profiles')->insertGetId([
                'user_id' => $trainerUserId,
                'full_name' => 'Prof. Maria Santos',
                'position' => 'Senior Vocational Trainer',
                'department' => 'DMDP Skills Training Division',
                'office' => 'Cebu City DMDP Center',
                'specialization' => 'Vocational & Digital Skills',
                'trainer_type' => 'dmdp',
                'is_trainer_approved' => 1,
            ]);
        } else {
            $profile = DB::table('user_profiles')->where('user_id', $trainer->user_id)->first();
            $trainerProfileId = $profile ? $profile->profile_id : 1;
        }

        // 3. Demo JPO Account
        $jpo = User::where('email', 'jpo@trabago.com')->first();
        if (!$jpo) {
            $jpoUserId = DB::table('users')->insertGetId([
                'email' => 'jpo@trabago.com',
                'password' => Hash::make('password123'),
                'role' => 'jpo',
                'status' => 'active',
                'is_approved' => 1,
                'created_at' => now(),
            ]);

            DB::table('user_profiles')->insert([
                'user_id' => $jpoUserId,
                'full_name' => 'Atty. Roberto Tan',
                'position' => 'Job Placement Officer',
                'department' => 'Employment Facilitation Unit',
                'office' => 'PESD Cebu',
            ]);
        }

        // 4. Demo Supervisor Account
        $supervisor = User::where('email', 'supervisor@trabago.com')->first();
        if (!$supervisor) {
            $supervisorUserId = DB::table('users')->insertGetId([
                'email' => 'supervisor@trabago.com',
                'password' => Hash::make('password123'),
                'role' => 'supervisor',
                'status' => 'active',
                'is_approved' => 1,
                'created_at' => now(),
            ]);

            DB::table('user_profiles')->insert([
                'user_id' => $supervisorUserId,
                'full_name' => 'Elena Ramos',
                'position' => 'PESD Supervisor',
                'department' => 'Public Employment Service Division',
                'office' => 'PESD Cebu',
            ]);
        }

        // 5. Demo LMO Account
        $lmo = User::where('email', 'lmo@trabago.com')->first();
        if (!$lmo) {
            $lmoUserId = DB::table('users')->insertGetId([
                'email' => 'lmo@trabago.com',
                'password' => Hash::make('password123'),
                'role' => 'lmo',
                'status' => 'active',
                'is_approved' => 1,
                'created_at' => now(),
            ]);

            DB::table('user_profiles')->insert([
                'user_id' => $lmoUserId,
                'full_name' => 'David Lim',
                'position' => 'Labor Market Officer',
                'department' => 'Labor Market Intelligence Unit',
                'office' => 'PESD Cebu',
            ]);
        }

        // 3. Demo Employer Accounts
        $employer1 = User::where('email', 'employer@techcorp.com')->first();
        $employer1Id = null;
        if (!$employer1) {
            $empUserId = DB::table('users')->insertGetId([
                'email' => 'employer@techcorp.com',
                'password' => Hash::make('password123'),
                'role' => 'employer',
                'status' => 'active',
                'is_approved' => 1,
                'created_at' => now(),
            ]);

            $employer1Id = DB::table('employers')->insertGetId([
                'user_id' => $empUserId,
                'company_name' => 'TechCorp Solutions Inc.',
                'is_accredited' => 1,
                'accredited_at' => now()->toDateString(),
            ]);

            DB::table('employer_accreditation')->insert([
                'employer_id' => $employer1Id,
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
                'supervisor_remarks' => 'Official DMDP accreditation paperwork validated and endorsed by PESD Supervisor.',
                'admin_approved' => 1,
                'admin_approved_at' => now()->subDays(8)->toDateString(),
                'submitted_at' => now()->subDays(12)->toDateString(),
                'approved_at' => now()->subDays(8)->toDateString(),
            ]);
        } else {
            $emp = DB::table('employers')->where('user_id', $employer1->user_id)->first();
            $employer1Id = $emp ? $emp->employer_id : 1;
        }

        $employer2 = User::where('email', 'hr@cebubpo.com')->first();
        $employer2Id = null;
        if (!$employer2) {
            $empUserId2 = DB::table('users')->insertGetId([
                'email' => 'hr@cebubpo.com',
                'password' => Hash::make('password123'),
                'role' => 'employer',
                'status' => 'active',
                'is_approved' => 1,
                'created_at' => now(),
            ]);

            $employer2Id = DB::table('employers')->insertGetId([
                'user_id' => $empUserId2,
                'company_name' => 'Cebu Global BPO Services',
                'is_accredited' => 1,
                'accredited_at' => now()->toDateString(),
            ]);

            DB::table('employer_accreditation')->insert([
                'employer_id' => $employer2Id,
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
                'supervisor_remarks' => 'Official DMDP accreditation paperwork validated and endorsed by PESD Supervisor.',
                'admin_approved' => 1,
                'admin_approved_at' => now()->subDays(8)->toDateString(),
                'submitted_at' => now()->subDays(12)->toDateString(),
                'approved_at' => now()->subDays(8)->toDateString(),
            ]);
        } else {
            $emp2 = DB::table('employers')->where('user_id', $employer2->user_id)->first();
            $employer2Id = $emp2 ? $emp2->employer_id : 2;
        }

        // 4. Demo Jobseeker Account
        $jobseeker = User::where('email', 'jobseeker@trabago.com')->first();
        $jobseekerId = null;
        if (!$jobseeker) {
            $jobseekerUserId = DB::table('users')->insertGetId([
                'email' => 'jobseeker@trabago.com',
                'password' => Hash::make('password123'),
                'role' => 'jobseeker',
                'status' => 'active',
                'is_approved' => 1,
                'created_at' => now(),
            ]);

            $jobseekerId = DB::table('jobseekers')->insertGetId([
                'user_id' => $jobseekerUserId,
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
            ]);

            DB::table('jobseeker_details')->insert([
                'jobseeker_id' => $jobseekerId,
                'address' => json_encode(['city' => 'Cebu City', 'province' => 'Cebu', 'barangay' => 'Lahug']),
                'education' => json_encode(['BS Information Technology', 'University of Cebu']),
                'work_experience' => json_encode([]),
                'eligibility' => json_encode([]),
                'language_proficiency' => json_encode(['English', 'Filipino', 'Cebuano']),
                'training_certificates' => json_encode([]),
            ]);

            // Welcome Notification
            DB::table('notifications')->insert([
                'user_id' => $jobseekerUserId,
                'title' => 'Welcome to TrabaGo!',
                'message' => 'Your jobseeker profile is ready. Browse job matches and start training programs today.',
                'type' => 'approval',
                'is_read' => 0,
                'created_at' => now(),
            ]);
        }

        // 5. Seed Sample Job Postings
        if (DB::table('job_postings')->count() === 0) {
            DB::table('job_postings')->insert([
                [
                    'employer_id' => $employer1Id,
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
                ],
                [
                    'employer_id' => $employer2Id,
                    'title' => 'Customer Support Representative',
                    'description' => 'Deliver world-class customer service to international clients via voice and chat channels.',
                    'qualifications' => "• High school graduate or college undergraduate\n• Fluent in English and Filipino\n• Amenable to shifting schedules\n• Basic computer proficiency",
                    'vacancy_count' => 10,
                    'valid_until' => now()->addMonths(2)->toDateString(),
                    'accepts_disability' => 0,
                    'disability_type' => null,
                    'status' => 'approved',
                    'created_by' => 'employer',
                    'created_at' => now()->toDateString(),
                    'approved_at' => now()->toDateString(),
                ],
                [
                    'employer_id' => $employer1Id,
                    'title' => 'IT Support Technician',
                    'description' => 'Provide frontline hardware, networking, and software troubleshooting for enterprise office equipment and workstations.',
                    'qualifications' => "• Vocational or Associate Degree in Computer Technology\n• Knowledge of Windows OS, LAN cabling, and peripheral setup\n• Willing to work on-site in Cebu IT Park",
                    'vacancy_count' => 2,
                    'valid_until' => now()->addMonths(1)->toDateString(),
                    'accepts_disability' => 1,
                    'disability_type' => 'Orthopedic disability',
                    'status' => 'approved',
                    'created_by' => 'employer',
                    'created_at' => now()->toDateString(),
                    'approved_at' => now()->toDateString(),
                ],
                [
                    'employer_id' => $employer2Id,
                    'title' => 'Data Entry Associate',
                    'description' => 'Accurately transcribe, verify, and encode business records and customer transactions into cloud database systems.',
                    'qualifications' => "• Minimum typing speed of 40 WPM with 95% accuracy\n• Detail-oriented and reliable\n• Experience with MS Excel / Google Sheets",
                    'vacancy_count' => 5,
                    'valid_until' => now()->addMonths(4)->toDateString(),
                    'accepts_disability' => 1,
                    'disability_type' => 'All eligible persons with disabilities',
                    'status' => 'approved',
                    'created_by' => 'employer',
                    'created_at' => now()->toDateString(),
                    'approved_at' => now()->toDateString(),
                ],
            ]);
        }

        // 6. Seed Sample Training Programs & Topics
        if (DB::table('training_programs')->count() === 0 && $trainerProfileId) {
            $prog1Id = DB::table('training_programs')->insertGetId([
                'trainer_id' => $trainerProfileId,
                'title' => 'Workplace Readiness & Soft Skills',
                'training_type' => 'online',
                'duration_months' => 1,
                'description' => 'Master foundational workplace ethics, professional communication, and interview skills to excel in any industry.',
            ]);

            DB::table('training_topics')->insert([
                [
                    'training_id' => $prog1Id,
                    'title' => 'Effective Communication in the Workplace',
                    'video_url' => 'https://www.youtube.com/watch?v=sample1',
                    'topic_order' => 1,
                    'questions' => json_encode([
                        [
                            'question' => 'What is the most effective approach when communicating with a team member?',
                            'options' => ['Active listening and clarity', 'Speaking loudly', 'Ignoring feedback', 'Using technical jargon only'],
                            'answer' => 0,
                        ],
                    ]),
                ],
                [
                    'training_id' => $prog1Id,
                    'title' => 'Professional Ethics and Punctuality',
                    'video_url' => 'https://www.youtube.com/watch?v=sample2',
                    'topic_order' => 2,
                    'questions' => json_encode([
                        [
                            'question' => 'Why is punctuality important in the workplace?',
                            'options' => ['It demonstrates reliability and respect', 'It is not important', 'Only for managers', 'To avoid taking breaks'],
                            'answer' => 0,
                        ],
                    ]),
                ],
            ]);

            $prog2Id = DB::table('training_programs')->insertGetId([
                'trainer_id' => $trainerProfileId,
                'title' => 'Digital Literacy & Computer Fundamentals',
                'training_type' => 'online',
                'duration_months' => 2,
                'description' => 'Hands-on training covering internet productivity tools, spreadsheet analysis, document processing, and cloud collaboration.',
            ]);

            DB::table('training_topics')->insert([
                [
                    'training_id' => $prog2Id,
                    'title' => 'Spreadsheet Fundamentals',
                    'video_url' => 'https://www.youtube.com/watch?v=sample3',
                    'topic_order' => 1,
                    'questions' => json_encode([
                        [
                            'question' => 'Which formula is used to calculate the total sum of cells A1 through A10 in Excel?',
                            'options' => ['=SUM(A1:A10)', '=TOTAL(A1:A10)', '=ADD(A1..A10)', '=COUNT(A1:A10)'],
                            'answer' => 0,
                        ],
                    ]),
                ],
            ]);
        }

        // Update user if present
        DB::table('users')->where('email', 'espejonjimmyjr@gmail.com')->update([
            'is_approved' => 1,
            'status' => 'active',
            'password' => Hash::make('password123'),
        ]);
    }
}
