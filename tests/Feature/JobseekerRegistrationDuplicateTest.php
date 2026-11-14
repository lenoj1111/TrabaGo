<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class JobseekerRegistrationDuplicateTest extends TestCase
{
    use RefreshDatabase;

    public function test_duplicate_mobile_number_shows_existing_account_advice(): void
    {
        DB::table('users')->insert([
            'email' => 'existing@example.com',
            'password' => 'hashed-password',
            'role' => 'jobseeker',
            'status' => 'active',
            'is_approved' => false,
            'phone_number' => '9602077432',
            'is_contact_verified' => true,
            'account_status' => 'unverified',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->from('/jobseeker/register')->post('/jobseeker/register', [
            'email' => 'new@example.com',
            'mobile_number' => '9602077432',
        ]);

        $response->assertRedirect('/jobseeker/register');
        $response->assertSessionHasErrors([
            'mobile_number' => 'An account with this mobile number already exists. Please contact your JPO or an administrator to update your email or reset your password. For your security, never share your password.',
        ]);
    }

    public function test_matching_name_and_birth_date_shows_existing_account_advice(): void
    {
        $userId = DB::table('users')->insertGetId([
            'email' => 'existing@example.com',
            'password' => 'hashed-password',
            'role' => 'jobseeker',
            'status' => 'active',
            'is_approved' => false,
            'phone_number' => '9602077432',
            'is_contact_verified' => true,
            'account_status' => 'unverified',
            'created_at' => now(),
            'updated_at' => now(),
        ], 'user_id');

        DB::table('jobseekers')->insert([
            'user_id' => $userId,
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'birth_date' => '1995-04-12',
        ]);

        $response = $this->from('/jobseeker/register')->post('/jobseeker/register', [
            'email' => 'new@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'role' => 'jobseeker',
            'first_name' => 'maria',
            'last_name' => 'SANTOS',
            'birth_date' => '1995-04-12',
            'sex' => 'Female',
            'civil_status' => 'Single',
            'citizenship' => 'Filipino',
            'mobile_number' => '9602077433',
            'employment_status' => 'Unemployed',
        ]);

        $response->assertRedirect('/jobseeker/register');
        $response->assertSessionHas('error', 'An existing jobseeker account matches this name and date of birth. Please contact your JPO or an administrator to confirm the account and update your email or reset your password. For your security, never share your password.');
    }
}
