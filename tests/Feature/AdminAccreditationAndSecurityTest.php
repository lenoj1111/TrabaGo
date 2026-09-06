<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccreditationAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $jpoUser;
    protected User $employerUser;
    protected Employer $employer;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Admin User
        $this->adminUser = User::create([
            'email' => 'admin_test@trabago.gov.ph',
            'password' => Hash::make('AdminPass123!'),
            'role' => 'admin',
            'status' => 'active',
            'is_approved' => 1,
        ]);
        DB::table('user_profiles')->insert([
            'user_id' => $this->adminUser->user_id,
            'full_name' => 'Super Administrator',
            'office' => 'DMDP City Hall Admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. JPO User
        $this->jpoUser = User::create([
            'email' => 'jpo_eval@trabago.gov.ph',
            'password' => Hash::make('password'),
            'role' => 'jpo',
            'status' => 'active',
            'is_approved' => 1,
        ]);
        DB::table('user_profiles')->insert([
            'user_id' => $this->jpoUser->user_id,
            'full_name' => 'Jonathan JPO',
            'office' => 'Job Placement Office',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Employer User & Profile (Inactive until approved)
        $this->employerUser = User::create([
            'email' => 'techcorp@example.com',
            'password' => Hash::make('password'),
            'role' => 'employer',
            'status' => 'inactive',
            'is_approved' => 0,
        ]);

        $this->employer = Employer::create([
            'user_id' => $this->employerUser->user_id,
            'company_name' => 'TechCorp Solutions Cebu',
            'industry' => 'Information Technology',
            'company_address' => 'IT Park, Cebu City',
            'contact_person' => 'Jane Tech HR',
            'contact_phone' => '09171112222',
            'is_accredited' => 0,
        ]);
    }

    public function test_admin_can_approve_employer_account_without_accreditation(): void
    {
        // Employer submits accreditation papers
        $accId = DB::table('employer_accreditation')->insertGetId([
            'employer_id' => $this->employer->employer_id,
            'status' => 'submitted_to_jpo',
            'jpo_reviewed' => 0,
        ]);

        // Admin approves the employer user account
        $response = $this->actingAs($this->adminUser)->post("/admin/users/{$this->employerUser->user_id}/approve");
        $response->assertRedirect();

        // Employer user account is now active and approved
        $this->assertDatabaseHas('users', [
            'user_id' => $this->employerUser->user_id,
            'is_approved' => 1,
            'status' => 'active',
        ]);

        // Employer is STILL NOT accredited
        $this->assertDatabaseHas('employers', [
            'employer_id' => $this->employer->employer_id,
            'is_accredited' => 0,
        ]);
    }

    public function test_admin_cannot_accredit_employer_without_jpo_recommending_approval(): void
    {
        // Accreditation record without JPO recommendation
        $accId = DB::table('employer_accreditation')->insertGetId([
            'employer_id' => $this->employer->employer_id,
            'status' => 'submitted_to_jpo',
            'jpo_reviewed' => 0,
        ]);

        // 1. Via ApprovalsController
        $response = $this->actingAs($this->adminUser)->post("/admin/approvals/accreditations/{$accId}/approve", [
            'remarks' => 'Attempting premature accreditation.',
        ]);
        $response->assertSessionHas('error');
        $response->assertSessionHasErrors(['error']);

        // Employer is NOT accredited
        $this->assertDatabaseHas('employers', [
            'employer_id' => $this->employer->employer_id,
            'is_accredited' => 0,
        ]);

        // 2. Via Employer Management Direct Route
        $jsonResponse = $this->actingAs($this->adminUser)->postJson("/admin/employers/{$this->employer->employer_id}/accredit");
        $jsonResponse->assertStatus(422);
        $jsonResponse->assertJsonFragment([
            'success' => false,
        ]);
    }

    public function test_admin_can_accredit_employer_after_jpo_recommending_approval(): void
    {
        // Accreditation record evaluated & recommended by JPO
        $accId = DB::table('employer_accreditation')->insertGetId([
            'employer_id' => $this->employer->employer_id,
            'status' => 'jpo_approved',
            'document_status' => 'complete',
            'jpo_reviewed' => 1,
            'jpo_reviewed_at' => now(),
            'jpo_remarks' => 'Valid Mayor permit and SEC registration.',
        ]);

        // Admin approves accreditation
        $response = $this->actingAs($this->adminUser)->post("/admin/approvals/accreditations/{$accId}/approve", [
            'remarks' => 'Official DMDP accreditation granted following JPO recommendation.',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Employer is now accredited
        $this->assertDatabaseHas('employers', [
            'employer_id' => $this->employer->employer_id,
            'is_accredited' => 1,
        ]);

        $this->assertDatabaseHas('employer_accreditation', [
            'accreditation_id' => $accId,
            'status' => 'admin_approved',
            'admin_approved' => 1,
        ]);
    }

    public function test_admin_can_view_placement_reports_and_details(): void
    {
        // Create sample placement report matching database schema
        $reportId = DB::table('placement_reports')->insertGetId([
            'employer_id' => $this->employer->employer_id,
            'report_type' => 'employer_monthly',
            'report_month' => '2026-09-01',
            'report_data' => json_encode([
                'title' => 'Monthly Placement Report September 2026',
                'placed_count' => 8,
                'candidates' => ['Maria Santos', 'Juan Dela Cruz'],
            ]),
            'status' => 'pending',
        ]);

        // Admin can access Placement Reports Directory
        $indexResponse = $this->actingAs($this->adminUser)->get('/admin/placement-reports');
        $indexResponse->assertOk();
        $indexResponse->assertSee('Placement Reports Directory');
        $indexResponse->assertSee('TechCorp Solutions Cebu');

        // Admin can view specific report detail and print view
        $showResponse = $this->actingAs($this->adminUser)->get("/admin/placement-reports/{$reportId}");
        $showResponse->assertOk();
        $showResponse->assertSee('TechCorp Solutions Cebu');

        $printResponse = $this->actingAs($this->adminUser)->get("/admin/placement-reports/{$reportId}/print");
        $printResponse->assertOk();
    }

    public function test_admin_profile_update_and_password_reset(): void
    {
        // 1. Admin Profile View
        $profileResponse = $this->actingAs($this->adminUser)->get('/admin/profile');
        $profileResponse->assertOk();
        $profileResponse->assertSee('Reset Administrator Password');
        $profileResponse->assertSee('Super Administrator');

        // 2. Admin Update Profile
        $updateResponse = $this->actingAs($this->adminUser)->post('/admin/profile/update', [
            'full_name' => 'Updated Super Admin Name',
            'email' => 'new_admin_email@trabago.gov.ph',
            'office' => 'City Hall 5th Floor Executive Office',
            'phone' => '09998887777',
        ]);
        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $this->adminUser->user_id,
            'full_name' => 'Updated Super Admin Name',
            'office' => 'City Hall 5th Floor Executive Office',
        ]);
        $this->assertDatabaseHas('users', [
            'user_id' => $this->adminUser->user_id,
            'email' => 'new_admin_email@trabago.gov.ph',
        ]);

        // 3. Admin Reset Password
        $resetPasswordResponse = $this->actingAs($this->adminUser)->post('/admin/profile/reset-password', [
            'current_password' => 'AdminPass123!',
            'password' => 'NewSecurePassword2026!',
            'password_confirmation' => 'NewSecurePassword2026!',
        ]);
        $resetPasswordResponse->assertRedirect();
        $resetPasswordResponse->assertSessionHas('success');

        // Verify password was changed and hashed
        $this->adminUser->refresh();
        $this->assertTrue(Hash::check('NewSecurePassword2026!', $this->adminUser->password));
    }

    public function test_job_posting_approvals_show_employer_accreditation_state(): void
    {
        // 1. Employer 1: Accredited
        $this->employer->update(['is_accredited' => 1]);
        DB::table('employer_accreditation')->insert([
            'employer_id' => $this->employer->employer_id,
            'status' => 'admin_approved',
            'jpo_reviewed' => 1,
            'admin_approved' => 1,
        ]);
        $jobAccredited = JobPosting::create([
            'employer_id' => $this->employer->employer_id,
            'title' => 'Senior Laravel Architect',
            'vacancy_count' => 2,
            'status' => 'pending',
        ]);

        // 2. Employer 2: Pending Accreditation
        $employer2User = User::create([
            'email' => 'pending_co@example.com',
            'password' => Hash::make('password'),
            'role' => 'employer',
            'status' => 'active',
            'is_approved' => 1,
        ]);
        $employerPending = Employer::create([
            'user_id' => $employer2User->user_id,
            'company_name' => 'Pending Tech Inc',
            'industry' => 'Tech',
            'company_address' => 'Mandaue City',
            'contact_person' => 'Mark Pending',
            'contact_phone' => '09182223333',
            'is_accredited' => 0,
        ]);
        DB::table('employer_accreditation')->insert([
            'employer_id' => $employerPending->employer_id,
            'status' => 'jpo_approved',
            'jpo_reviewed' => 1,
        ]);
        $jobPending = JobPosting::create([
            'employer_id' => $employerPending->employer_id,
            'title' => 'Junior React Developer',
            'vacancy_count' => 1,
            'status' => 'pending',
        ]);

        // 3. Employer 3: Not Accredited
        $employer3User = User::create([
            'email' => 'unaccredited@example.com',
            'password' => Hash::make('password'),
            'role' => 'employer',
            'status' => 'active',
            'is_approved' => 1,
        ]);
        $employerUnaccredited = Employer::create([
            'user_id' => $employer3User->user_id,
            'company_name' => 'Unaccredited Retail',
            'industry' => 'Retail',
            'company_address' => 'Colon St, Cebu City',
            'contact_person' => 'Sam Retail',
            'contact_phone' => '09193334444',
            'is_accredited' => 0,
        ]);
        $jobUnaccredited = JobPosting::create([
            'employer_id' => $employerUnaccredited->employer_id,
            'title' => 'Retail Assistant',
            'vacancy_count' => 3,
            'status' => 'pending',
        ]);

        // Access Approvals Center
        $response = $this->actingAs($this->adminUser)->get('/admin/approvals');
        $response->assertOk();
        $response->assertSee('Employer Accreditation');
        $response->assertSee('Accredited');
        $response->assertSee('Pending');
        $response->assertSee('Not Accredited');
    }

    public function test_supervisor_and_lmo_roles_cannot_be_provisioned(): void
    {
        // Attempt to create user with supervisor role
        $responseSup = $this->actingAs($this->adminUser)->post('/admin/users', [
            'email' => 'new_supervisor@trabago.gov.ph',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'supervisor',
            'full_name' => 'Forbidden Supervisor',
            'position' => 'Supervisor',
        ]);
        $responseSup->assertSessionHasErrors(['role']);

        // Attempt to create user with lmo role
        $responseLmo = $this->actingAs($this->adminUser)->post('/admin/users', [
            'email' => 'new_lmo@trabago.gov.ph',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'lmo',
            'full_name' => 'Forbidden LMO',
            'position' => 'LMO Officer',
        ]);
        $responseLmo->assertSessionHasErrors(['role']);

        // Permitted roles: admin, jpo, trainer
        $responseJpo = $this->actingAs($this->adminUser)->post('/admin/users', [
            'email' => 'valid_jpo@trabago.gov.ph',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'jpo',
            'full_name' => 'Valid JPO Staff',
            'position' => 'JPO Officer',
        ]);
        $responseJpo->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', [
            'email' => 'valid_jpo@trabago.gov.ph',
            'role' => 'jpo',
        ]);
    }
}
