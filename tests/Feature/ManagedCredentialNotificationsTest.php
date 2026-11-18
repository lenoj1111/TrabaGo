<?php

namespace Tests\Feature;

use App\Models\Jobseeker;
use App\Models\User;
use App\Notifications\AccountCredentialsUpdatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ManagedCredentialNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_jpo_updated_jobseeker_credentials_are_emailed(): void
    {
        Notification::fake();
        config(['mail.default' => 'smtp']);

        $jpo = User::factory()->create(['role' => 'jpo']);
        $jobseekerUser = User::factory()->create(['role' => 'jobseeker']);
        $jobseeker = Jobseeker::create([
            'user_id' => $jobseekerUser->user_id,
            'first_name' => 'Job',
            'last_name' => 'Seeker',
            'email' => $jobseekerUser->email,
        ]);

        $this->actingAs($jpo)
            ->from('/jpo/jobseekers')
            ->put('/jpo/jobseekers/' . $jobseeker->jobseeker_id . '/credentials', [
                'email' => 'updated-jobseeker@example.com',
                'password' => 'UpdatedPass123!',
                'password_confirmation' => 'UpdatedPass123!',
            ])
            ->assertRedirect('/jpo/jobseekers');

        $this->assertSame('updated-jobseeker@example.com', $jobseekerUser->fresh()->email);
        $this->assertSame('updated-jobseeker@example.com', $jobseeker->fresh()->email);
        $this->assertTrue(Hash::check('UpdatedPass123!', $jobseekerUser->fresh()->password));

        Notification::assertSentTo($jobseekerUser, AccountCredentialsUpdatedNotification::class, function ($notification) {
            return $notification->loginEmail === 'updated-jobseeker@example.com'
                && $notification->updatedPassword === 'UpdatedPass123!';
        });
    }

    public function test_admin_updated_jobseeker_credentials_are_emailed(): void
    {
        Notification::fake();
        config(['mail.default' => 'smtp']);

        $admin = User::factory()->create(['role' => 'admin']);
        $jobseekerUser = User::factory()->create(['role' => 'jobseeker']);
        $jobseeker = Jobseeker::create([
            'user_id' => $jobseekerUser->user_id,
            'first_name' => 'Job',
            'last_name' => 'Seeker',
            'email' => $jobseekerUser->email,
        ]);

        $this->actingAs($admin)
            ->from('/admin/jobseekers')
            ->put('/admin/jobseekers/' . $jobseeker->jobseeker_id . '/credentials', [
                'email' => 'admin-updated-jobseeker@example.com',
                'password' => 'UpdatedPass123!',
                'password_confirmation' => 'UpdatedPass123!',
            ])
            ->assertRedirect('/admin/jobseekers');

        Notification::assertSentTo($jobseekerUser, AccountCredentialsUpdatedNotification::class, function ($notification) {
            return $notification->loginEmail === 'admin-updated-jobseeker@example.com'
                && $notification->updatedPassword === 'UpdatedPass123!';
        });
    }

    public function test_admin_updated_employer_credentials_are_emailed(): void
    {
        Notification::fake();
        config(['mail.default' => 'smtp']);

        $admin = User::factory()->create(['role' => 'admin']);
        $employerUser = User::factory()->create(['role' => 'employer']);
        $employerId = DB::table('employers')->insertGetId([
            'user_id' => $employerUser->user_id,
            'company_name' => 'Example Employer',
        ]);

        $this->actingAs($admin)
            ->from('/admin/employers')
            ->post('/admin/employers/' . $employerId . '/update-credentials', [
                'email' => 'updated-employer@example.com',
                'password' => 'UpdatedPass123!',
                'password_confirmation' => 'UpdatedPass123!',
            ])
            ->assertRedirect('/admin/employers');

        $this->assertSame('updated-employer@example.com', $employerUser->fresh()->email);
        $this->assertTrue(Hash::check('UpdatedPass123!', $employerUser->fresh()->password));

        Notification::assertSentTo($employerUser, AccountCredentialsUpdatedNotification::class, function ($notification) {
            return $notification->loginEmail === 'updated-employer@example.com'
                && $notification->updatedPassword === 'UpdatedPass123!';
        });
    }
}
