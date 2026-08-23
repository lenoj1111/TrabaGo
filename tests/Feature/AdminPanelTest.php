<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_all_admin_routes(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $userId = DB::table('users')->insertGetId([
                'email' => 'admin@trabago.com',
                'password' => bcrypt('password123'),
                'role' => 'admin',
                'status' => 'active',
                'is_approved' => 1,
                'created_at' => now(),
            ]);
            $admin = User::find($userId);
        }

        // Create a user profile
        DB::table('user_profiles')->insert([
            'user_id' => $admin->user_id,
            'full_name' => 'Admin User',
            'position' => 'Admin',
        ]);

        // Create a test job posting
        $jobId = DB::table('job_postings')->insertGetId([
            'title' => 'Test Job',
            'description' => 'Test Description',
            'vacancy_count' => 1,
            'valid_until' => now()->addDays(10)->toDateString(),
            'status' => 'approved',
            'created_by' => 'admin',
            'created_at' => now(),
        ]);

        $routes = [
            '/admin/dashboard',
            '/admin/job-postings',
            '/admin/job-postings/create',
            "/admin/job-postings/{$jobId}",
            "/admin/job-postings/{$jobId}/edit",
            '/admin/job-postings-list',
            '/admin/users',
            '/admin/users/create',
            "/admin/users/{$admin->user_id}/edit",
            '/admin/employers',
            '/admin/reports',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($admin)->get($route);
            $response->assertStatus(200);
        }
    }

    public function test_admin_login_redirects_to_dashboard(): void
    {
        DB::table('users')->insert([
            'email' => 'admin2@trabago.com',
            'password' => bcrypt('secret123'),
            'role' => 'admin',
            'status' => 'active',
            'is_approved' => 1,
            'created_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin2@trabago.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/admin/dashboard');
    }

    public function test_admin_can_edit_and_update_user_without_initial_profile(): void
    {
        $admin = User::create([
            'email' => 'admin_test@trabago.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'status' => 'active',
            'is_approved' => 1,
        ]);

        $jobseekerUser = DB::table('users')->insertGetId([
            'email' => 'jobseeker_target@trabago.com',
            'password' => bcrypt('password123'),
            'role' => 'jobseeker',
            'status' => 'active',
            'is_approved' => 1,
            'created_at' => now(),
        ]);

        DB::table('jobseekers')->insert([
            'user_id' => $jobseekerUser,
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'mobile_number' => '09123456789',
        ]);

        // 1. Admin views edit page (this had the UrlGenerationException)
        $response = $this->actingAs($admin)->get("/admin/users/{$jobseekerUser}/edit");
        $response->assertStatus(200);
        $response->assertSee('Maria Santos');
        $response->assertSee("action=\"http://localhost/admin/users/{$jobseekerUser}\"", false);

        // 2. Admin submits update
        $updateResponse = $this->actingAs($admin)->put("/admin/users/{$jobseekerUser}", [
            'role' => 'trainer',
            'status' => 'active',
            'is_approved' => 1,
            'full_name' => 'Maria Santos Updated',
            'position' => 'Senior Trainer',
            'department' => 'Tech Dept',
            'office' => 'Main Office',
            'phone' => '09123456789',
        ]);

        $updateResponse->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'user_id' => $jobseekerUser,
            'role' => 'trainer',
        ]);
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $jobseekerUser,
            'full_name' => 'Maria Santos Updated',
            'position' => 'Senior Trainer',
        ]);
    }
}

