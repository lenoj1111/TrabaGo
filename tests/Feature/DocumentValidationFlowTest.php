<?php

namespace Tests\Feature;

use App\Models\Jobseeker;
use App\Models\JobseekerDetail;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DocumentValidationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_document_upload_requires_file()
    {
        $user = User::factory()->create(['role' => 'jobseeker', 'is_approved' => 1]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/documents/upload', [
            'category' => 'resume',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_document_upload_enforces_allowed_extensions()
    {
        $user = User::factory()->create(['role' => 'jobseeker', 'is_approved' => 1]);
        Sanctum::actingAs($user);

        Storage::fake('public');

        // Attempt to upload an executable / unsupported script as resume
        $fakeFile = UploadedFile::fake()->create('malicious.sh', 50, 'application/x-sh');

        $response = $this->postJson('/api/documents/upload', [
            'category' => 'resume',
            'file' => $fakeFile,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_document_upload_enforces_5mb_size_limit()
    {
        $user = User::factory()->create(['role' => 'jobseeker', 'is_approved' => 1]);
        Sanctum::actingAs($user);

        Storage::fake('public');

        // Create an oversized file > 5MB (e.g. 6000 KB)
        $oversizedFile = UploadedFile::fake()->create('huge_resume.pdf', 6000, 'application/pdf');

        $response = $this->postJson('/api/documents/upload', [
            'category' => 'resume',
            'file' => $oversizedFile,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_successful_document_upload_creates_rich_metadata()
    {
        $user = User::factory()->create(['role' => 'jobseeker', 'is_approved' => 1]);
        $jobseeker = Jobseeker::create([
            'user_id' => $user->user_id,
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => $user->email,
        ]);

        Sanctum::actingAs($user);
        Storage::fake('public');

        $validFile = UploadedFile::fake()->create('my_resume.pdf', 500, 'application/pdf');

        $response = $this->postJson('/api/documents/upload', [
            'category' => 'resume',
            'file' => $validFile,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'category' => 'resume',
            ]);

        $this->assertTrue($jobseeker->fresh()->hasDocument('resume'));
    }

    public function test_rejected_document_does_not_fulfill_jobseeker_requirements()
    {
        $user = User::factory()->create(['role' => 'jobseeker', 'is_approved' => 1]);
        $jobseeker = Jobseeker::create([
            'user_id' => $user->user_id,
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'email' => $user->email,
        ]);

        JobseekerDetail::create([
            'jobseeker_id' => $jobseeker->jobseeker_id,
            'training_certificates' => [
                [
                    'id' => 'doc_123',
                    'category' => 'valid_id',
                    'name' => 'blurry_id.jpg',
                    'status' => 'rejected',
                    'rejection_reason' => 'Image was blurry and unreadable.',
                ]
            ],
        ]);

        // hasDocument should return false because document was rejected
        $this->assertFalse($jobseeker->fresh()->hasDocument('valid_id'));
    }

    public function test_admin_can_review_and_reject_document_with_reason()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => 1]);
        $jobseekerUser = User::factory()->create(['role' => 'jobseeker', 'is_approved' => 1]);
        $jobseeker = Jobseeker::create([
            'user_id' => $jobseekerUser->user_id,
            'first_name' => 'Pedro',
            'last_name' => 'Penduko',
            'email' => $jobseekerUser->email,
        ]);

        JobseekerDetail::create([
            'jobseeker_id' => $jobseeker->jobseeker_id,
            'training_certificates' => [
                [
                    'id' => 'doc_target_999',
                    'category' => 'valid_id',
                    'name' => 'fake_id.png',
                    'status' => 'under_review',
                ]
            ],
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/documents/doc_target_999/review', [
            'status' => 'rejected',
            'rejection_reason' => 'ID photo does not match applicant name.',
            'jobseeker_id' => $jobseeker->jobseeker_id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Document has been marked as rejected.',
            ]);

        $detail = JobseekerDetail::where('jobseeker_id', $jobseeker->jobseeker_id)->first();
        $docs = $detail->training_certificates;
        $this->assertEquals('rejected', $docs[0]['status']);
        $this->assertEquals('ID photo does not match applicant name.', $docs[0]['rejection_reason']);

        // Check that notification was sent to the jobseeker
        $this->assertDatabaseHas('notifications', [
            'user_id' => $jobseekerUser->user_id,
            'title' => 'Document Rejected - Action Required',
        ]);
    }
}
