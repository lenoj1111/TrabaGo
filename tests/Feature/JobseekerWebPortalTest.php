<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\JobseekerDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JobseekerWebPortalTest extends TestCase
{
    use RefreshDatabase;

    private function createJobseekerUser(): User
    {
        $userId = DB::table('users')->insertGetId([
            'email' => 'juan@trabago.com',
            'password' => Hash::make('secret123'),
            'role' => 'jobseeker',
            'status' => 'active',
            'is_approved' => 1,
            'created_at' => now(),
        ]);

        DB::table('jobseekers')->insert([
            'user_id' => $userId,
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => 'juan@trabago.com',
            'employment_status' => 'Looking for job',
        ]);

        return User::find($userId);
    }

    public function test_guest_is_redirected_from_jobseeker_home(): void
    {
        $response = $this->get('/jobseeker/home');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_jobseeker_can_access_home(): void
    {
        $user = $this->createJobseekerUser();

        $response = $this->actingAs($user)->get('/jobseeker/home');
        $response->assertOk();
        $response->assertSee('DMDP TrabaGo');
    }

    public function test_authenticated_jobseeker_can_access_jobs(): void
    {
        $user = $this->createJobseekerUser();

        $response = $this->actingAs($user)->get('/jobseeker/jobs');
        $response->assertOk();
        $response->assertSee('AI Cosine-Similarity Job Explorer');
    }

    public function test_authenticated_jobseeker_can_access_applications(): void
    {
        $user = $this->createJobseekerUser();

        $response = $this->actingAs($user)->get('/jobseeker/applications');
        $response->assertOk();
        $response->assertSee('Application Lifecycle Tracker');
    }

    public function test_authenticated_jobseeker_can_access_training(): void
    {
        $user = $this->createJobseekerUser();

        $response = $this->actingAs($user)->get('/jobseeker/training');
        $response->assertOk();
        $response->assertSee('DMDP Skill Enhancement');
    }

    public function test_authenticated_jobseeker_can_access_documents(): void
    {
        $user = $this->createJobseekerUser();

        $response = $this->actingAs($user)->get('/jobseeker/documents');
        $response->assertOk();
        $response->assertSee('Document Hub & Verification');
    }

    public function test_jobseeker_can_preview_their_uploaded_document(): void
    {
        $user = $this->createJobseekerUser();
        $jobseeker = $user->jobseeker;
        Storage::fake('public');
        Storage::disk('public')->put("documents/{$jobseeker->jobseeker_id}/resume.pdf", '%PDF-1.4 preview');

        JobseekerDetail::create([
            'jobseeker_id' => $jobseeker->jobseeker_id,
            'training_certificates' => [
                [
                    'category' => 'resume',
                    'name' => 'resume.pdf',
                    'file_path' => "documents/{$jobseeker->jobseeker_id}/resume.pdf",
                ],
            ],
        ]);

        $response = $this->actingAs($user)->get('/jobseeker/documents/resume/preview');

        $response->assertOk();
        $response->assertHeader('content-disposition', 'inline; filename=resume.pdf');
    }

    public function test_authenticated_jobseeker_can_access_profile(): void
    {
        $user = $this->createJobseekerUser();

        $response = $this->actingAs($user)->get('/jobseeker/profile');
        $response->assertOk();
        $response->assertSee('Skills Matrix');
    }
}
