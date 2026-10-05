<?php

namespace Tests\Feature;

use App\Models\Jobseeker;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class JobseekerLoginRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_jobseeker_login_route_exists(): void
    {
        $response = $this->get('/jobseeker/login');
        $response->assertOk();
    }

    public function test_jobseeker_can_login_and_access_home_and_profile(): void
    {
        $userId = DB::table('users')->insertGetId([
            'email' => 'jobseeker@trabago.com',
            'password' => Hash::make('password123'),
            'role' => 'jobseeker',
            'status' => 'active',
            'is_approved' => 1,
            'created_at' => now(),
        ]);

        DB::table('jobseekers')->insert([
            'user_id' => $userId,
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => 'jobseeker@trabago.com',
            'employment_status' => 'Unemployed',
        ]);

        $user = User::find($userId);

        // Test login redirect
        $loginResponse = $this->post('/login', [
            'email' => 'jobseeker@trabago.com',
            'password' => 'password123',
        ]);
        $loginResponse->assertRedirect('/jobseeker/home');

        // Test accessing jobseeker home
        $homeResponse = $this->actingAs($user)->get('/jobseeker/home');
        $homeResponse->assertOk();

        // Test accessing jobseeker profile
        $profileResponse = $this->actingAs($user)->get('/jobseeker/profile');
        $profileResponse->assertOk();
    }

    public function test_pending_jobseeker_can_login_but_cannot_apply_until_approved(): void
    {
        $userId = DB::table('users')->insertGetId([
            'email' => 'pending.jobseeker@trabago.com',
            'password' => Hash::make('password123'),
            'role' => 'jobseeker',
            'status' => 'active',
            'is_approved' => 0,
            'created_at' => now(),
        ]);

        $jobseekerId = DB::table('jobseekers')->insertGetId([
            'user_id' => $userId,
            'first_name' => 'Pending',
            'last_name' => 'Seeker',
            'email' => 'pending.jobseeker@trabago.com',
            'employment_status' => 'Unemployed',
        ]);

        $employerUserId = DB::table('users')->insertGetId([
            'email' => 'pending-employer@trabago.com',
            'password' => Hash::make('password123'),
            'role' => 'employer',
            'status' => 'active',
            'is_approved' => 1,
            'created_at' => now(),
        ]);

        $employerId = DB::table('employers')->insertGetId([
            'user_id' => $employerUserId,
            'company_name' => 'Approved Employer',
            'is_accredited' => 1,
            'accredited_at' => now(),
        ]);

        $jobId = DB::table('job_postings')->insertGetId([
            'employer_id' => $employerId,
            'title' => 'Open Job For Pending User',
            'description' => 'A test job posting.',
            'qualifications' => 'None',
            'vacancy_count' => 1,
            'valid_until' => now()->addMonth()->toDateString(),
            'status' => 'approved',
            'created_by' => 'employer',
        ]);

        $loginResponse = $this->post('/login', [
            'email' => 'pending.jobseeker@trabago.com',
            'password' => 'password123',
        ]);
        $loginResponse->assertRedirect('/jobseeker/home');

        $jobseeker = Jobseeker::find($jobseekerId);
        $this->assertFalse($jobseeker->isEmployed());

        $applyResponse = $this->actingAs(User::find($userId))->post("/jobseeker/jobs/{$jobId}/apply");
        $applyResponse->assertRedirect('/jobseeker/jobs');
        $applyResponse->assertSessionHas('error', 'Your account is pending approval. You must be approved before you can apply for a job.');

        $this->assertDatabaseMissing('job_applications', [
            'jobseeker_id' => $jobseekerId,
            'job_id' => $jobId,
        ]);
    }
}

