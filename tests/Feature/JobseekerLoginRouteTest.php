<?php

namespace Tests\Feature;

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
}

