<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileJobseekerProfileSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_registration_persists_the_full_jobseeker_profile_for_shared_web_and_app_use(): void
    {
        $registration = $this->postJson('/api/auth/register', [
            'email' => 'mobile.jobseeker@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'role' => 'jobseeker',
            'first_name' => 'Maria',
            'middle_name' => 'Santos',
            'last_name' => 'Dela Cruz',
            'birth_date' => '1998-05-20',
            'sex' => 'Female',
            'civil_status' => 'Single',
            'citizenship' => 'Filipino',
            'mobile_number' => '09123456789',
            'employment_status' => 'Fresh Graduate',
            'address' => [
                'street' => '12 Main Street',
                'barangay' => 'Lahug',
                'city' => 'Cebu City',
                'province' => 'Cebu',
                'postal_code' => '6000',
                'country' => 'Philippines',
            ],
            'education' => [
                'level' => 'College Graduate',
                'course' => 'Information Technology',
                'school' => 'Cebu City University',
                'year_graduated' => '2024',
            ],
            'skills' => [
                ['name' => 'Customer Service', 'type' => 'technical'],
            ],
            'skills_century' => ['Communication'],
            'skills_informal_tech' => ['Basic Computer Repair'],
            'occupation1' => 'IT Support',
            'occupation2' => 'Service Desk Analyst',
            'occupation3' => 'Computer Technician',
            'industry1' => 'Information Technology',
            'industry2' => 'Business Process Outsourcing',
            'industry3' => 'Electronics',
            'preferred_location' => 'Cebu City',
            'salary_min' => '15000',
            'salary_max' => '25000',
            'is_4ps' => true,
            'household_id' => '0722-12345678',
            'is_ofw' => true,
            'is_pwd' => true,
            'pwd_type' => 'Visual',
            'is_lgbtq' => true,
            'is_senior_citizen' => false,
            'is_immigrant' => true,
            'immigrant_origin' => 'Bohol',
        ]);

        $registration->assertCreated()
            ->assertJsonPath('user.address.postal_code', '6000')
            ->assertJsonPath('user.education_details.level', 'College Graduate')
            ->assertJsonPath('user.preferences.occupation3', 'Computer Technician')
            ->assertJsonPath('user.preferences.industry3', 'Electronics')
            ->assertJsonPath('user.preferences.salary_expectation', '₱15,000 - ₱25,000 / month')
            ->assertJsonPath('user.social_status.is_immigrant', true)
            ->assertJsonPath('user.social_status.immigrant_origin', 'Bohol');

        $token = $registration->json('token');

        $this->withToken($token)->putJson('/api/auth/profile', [
            'preferences' => [
                'occupation1' => 'Updated IT Support',
                'occupation3' => 'Network Technician',
                'industry2' => 'Telecommunications',
            ],
            'social_status' => [
                'is_lgbtq' => false,
                'is_immigrant' => true,
                'immigrant_origin' => 'Cebu',
            ],
        ])->assertOk()
            ->assertJsonPath('user.preferences.occupation1', 'Updated IT Support')
            ->assertJsonPath('user.preferences.occupation3', 'Network Technician')
            ->assertJsonPath('user.preferences.industry2', 'Telecommunications')
            ->assertJsonPath('user.social_status.is_lgbtq', false)
            ->assertJsonPath('user.social_status.immigrant_origin', 'Cebu');

        $this->withToken($token)->getJson('/api/auth/profile')
            ->assertOk()
            ->assertJsonPath('user.preferences.occupation3', 'Network Technician')
            ->assertJsonPath('user.social_status.immigrant_origin', 'Cebu');
    }
}
