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
    }
}
