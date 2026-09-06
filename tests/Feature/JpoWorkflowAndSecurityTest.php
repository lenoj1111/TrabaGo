<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Jobseeker;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class JpoWorkflowAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $jpoUser;
    protected User $employerUser;
    protected Employer $employer;
    protected User $jobseekerUser;
    protected Jobseeker $jobseeker;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Admin
        $this->adminUser = User::create([
            'email' => 'admin_test@trabago.gov.ph',
            'password' => Hash::make('AdminPass123!'),
            'role' => 'admin',
            'status' => 'active',
            'is_approved' => 1,
        ]);

        // 2. JPO
        $this->jpoUser = User::create([
            'email' => 'jpo_officer@trabago.gov.ph',
            'password' => Hash::make('JpoPass123!'),
            'role' => 'jpo',
            'status' => 'active',
            'is_approved' => 1,
        ]);
        DB::table('user_profiles')->insert([
            'user_id' => $this->jpoUser->user_id,
            'full_name' => 'Officer Mary JPO',
            'office' => 'DMDP Job Placement Division',
            'phone' => '09170001111',
            'position' => 'Job Placement Officer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Employer
        $this->employerUser = User::create([
            'email' => 'cebucorp@example.com',
            'password' => Hash::make('EmployerPass123!'),
            'role' => 'employer',
            'status' => 'active',
            'is_approved' => 1,
        ]);
        $this->employer = Employer::create([
            'user_id' => $this->employerUser->user_id,
            'company_name' => 'Cebu IT Global Corp',
            'industry' => 'Information Technology',
            'company_address' => 'IT Park, Cebu City',
            'contact_person' => 'HR Director',
            'contact_phone' => '09172223333',
            'is_accredited' => 0,
        ]);

        // 4. Jobseeker
        $this->jobseekerUser = User::create([
            'email' => 'applicant_john@example.com',
            'password' => Hash::make('JobseekerPass123!'),
            'role' => 'jobseeker',
            'status' => 'active',
            'is_approved' => 1,
        ]);
        $this->jobseeker = Jobseeker::create([
            'user_id' => $this->jobseekerUser->user_id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'applicant_john@example.com',
            'mobile_number' => '09179998888',
            'birth_date' => '1998-05-15',
            'sex' => 'male',
            'civil_status' => 'Single',
            'citizenship' => 'Filipino',
            'employment_status' => 'Unemployed',
        ]);
    }

    public function test_jpo_can_update_employer_document_status_to_complete(): void
    {
        $accId = DB::table('employer_accreditation')->insertGetId([
            'employer_id' => $this->employer->employer_id,
            'status' => 'submitted_to_jpo',
            'document_status' => 'pending',
            'jpo_reviewed' => 0,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($this->jpoUser)->post("/jpo/evaluations/accreditations/{$accId}/document-status", [
            'document_status' => 'complete',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employer_accreditation', [
            'accreditation_id' => $accId,
            'document_status' => 'complete',
            'document_incomplete_reason' => null,
        ]);
    }

    public function test_jpo_setting_incomplete_requires_a_reason(): void
    {
        $accId = DB::table('employer_accreditation')->insertGetId([
            'employer_id' => $this->employer->employer_id,
            'status' => 'submitted_to_jpo',
            'document_status' => 'pending',
            'jpo_reviewed' => 0,
            'submitted_at' => now(),
        ]);

        // Missing incomplete reason
        $response = $this->actingAs($this->jpoUser)->post("/jpo/evaluations/accreditations/{$accId}/document-status", [
            'document_status' => 'incomplete',
            'document_incomplete_reason' => '',
        ]);

        $response->assertSessionHasErrors('document_incomplete_reason');

        // With valid incomplete reason
        $response2 = $this->actingAs($this->jpoUser)->post("/jpo/evaluations/accreditations/{$accId}/document-status", [
            'document_status' => 'incomplete',
            'document_incomplete_reason' => 'Missing Mayor\'s Business Permit and expired BIR 2303 certificate.',
        ]);

        $response2->assertRedirect();
        $response2->assertSessionHas('success');

        $this->assertDatabaseHas('employer_accreditation', [
            'accreditation_id' => $accId,
            'document_status' => 'incomplete',
            'document_incomplete_reason' => 'Missing Mayor\'s Business Permit and expired BIR 2303 certificate.',
        ]);
    }

    public function test_jpo_cannot_recommend_employer_if_documents_are_incomplete_or_pending(): void
    {
        $accId = DB::table('employer_accreditation')->insertGetId([
            'employer_id' => $this->employer->employer_id,
            'status' => 'submitted_to_jpo',
            'document_status' => 'incomplete',
            'document_incomplete_reason' => 'BIR Form 2303 is missing.',
            'jpo_reviewed' => 0,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($this->jpoUser)->post("/jpo/evaluations/accreditations/{$accId}/recommend", [
            'action' => 'recommend',
            'remarks' => 'Trying to recommend incomplete papers',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('error');

        // Status must remain submitted_to_jpo and not jpo_approved
        $this->assertDatabaseHas('employer_accreditation', [
            'accreditation_id' => $accId,
            'status' => 'submitted_to_jpo',
            'jpo_reviewed' => 0,
        ]);
    }

    public function test_jpo_can_recommend_employer_when_documents_are_complete(): void
    {
        $accId = DB::table('employer_accreditation')->insertGetId([
            'employer_id' => $this->employer->employer_id,
            'status' => 'submitted_to_jpo',
            'document_status' => 'complete',
            'jpo_reviewed' => 0,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($this->jpoUser)->post("/jpo/evaluations/accreditations/{$accId}/recommend", [
            'action' => 'recommend',
            'remarks' => 'All legal credentials verified complete and valid.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employer_accreditation', [
            'accreditation_id' => $accId,
            'status' => 'jpo_approved',
            'jpo_reviewed' => 1,
        ]);
    }

    public function test_admin_cannot_approve_accreditation_if_documents_are_incomplete(): void
    {
        $accId = DB::table('employer_accreditation')->insertGetId([
            'employer_id' => $this->employer->employer_id,
            'status' => 'jpo_approved',
            'document_status' => 'incomplete',
            'document_incomplete_reason' => 'Missing DTI certificate.',
            'jpo_reviewed' => 1,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)->post("/admin/approvals/accreditations/{$accId}/approve");

        $response->assertRedirect();
        $response->assertSessionHasErrors('error');

        // Employer must NOT be accredited
        $this->assertDatabaseHas('employers', [
            'employer_id' => $this->employer->employer_id,
            'is_accredited' => 0,
        ]);
    }

    public function test_admin_can_approve_accreditation_when_documents_are_complete_and_jpo_recommended(): void
    {
        $accId = DB::table('employer_accreditation')->insertGetId([
            'employer_id' => $this->employer->employer_id,
            'status' => 'jpo_approved',
            'document_status' => 'complete',
            'jpo_reviewed' => 1,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)->post("/admin/approvals/accreditations/{$accId}/approve");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employer_accreditation', [
            'accreditation_id' => $accId,
            'status' => 'admin_approved',
            'admin_approved' => 1,
        ]);

        $this->assertDatabaseHas('employers', [
            'employer_id' => $this->employer->employer_id,
            'is_accredited' => 1,
        ]);
    }

    public function test_jpo_can_view_placement_reports(): void
    {
        $repId = DB::table('placement_reports')->insertGetId([
            'employer_id' => $this->employer->employer_id,
            'report_type' => 'employer_monthly',
            'report_month' => '2026-08-01',
            'report_data' => json_encode([
                'total_hired' => 2,
                'hired_list' => [
                    ['name' => 'Candidate 1', 'position' => 'Developer', 'hired_date' => '2026-08-10'],
                    ['name' => 'Candidate 2', 'position' => 'QA Analyst', 'hired_date' => '2026-08-15'],
                ],
            ]),
            'status' => 'submitted_to_jpo',
        ]);

        $indexResponse = $this->actingAs($this->jpoUser)->get('/jpo/evaluations/placement-reports');
        $indexResponse->assertOk();
        $indexResponse->assertSee($this->employer->company_name);

        $printResponse = $this->actingAs($this->jpoUser)->get("/jpo/evaluations/placement-reports/{$repId}/print");
        $printResponse->assertOk();
    }

    public function test_jpo_can_view_nsrp_form_for_candidate(): void
    {
        $jobPosting = JobPosting::create([
            'employer_id' => $this->employer->employer_id,
            'title' => 'Software Engineer',
            'description' => 'Build web applications',
            'vacancy_count' => 3,
            'status' => 'approved',
        ]);

        $application = JobApplication::create([
            'job_id' => $jobPosting->job_id,
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'status' => 'pending',
            'referred_by_jpo' => 0,
        ]);

        $response = $this->actingAs($this->jpoUser)->get("/jpo/evaluations/jobseekers/{$application->application_id}/nsrp");
        $response->assertOk();
        $response->assertSee('NSRP Form 1.REV 3');
        $response->assertSee($this->jobseeker->first_name);
        $response->assertSee($this->jobseeker->last_name);
    }

    public function test_jpo_can_update_profile(): void
    {
        $response = $this->actingAs($this->jpoUser)->post('/jpo/profile/update', [
            'full_name' => 'Officer Mary JPO Updated',
            'phone' => '09178889999',
            'office' => 'DMDP Placement Office - Cebu City',
            'position' => 'Senior Placement Officer',
            'bio' => 'Assigned to IT-BPM Sector & Technical Training Candidates',
        ]);

        $response->assertRedirect('/jpo/profile');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $this->jpoUser->user_id,
            'full_name' => 'Officer Mary JPO Updated',
            'phone' => '09178889999',
            'position' => 'Senior Placement Officer',
        ]);
    }

    public function test_jpo_can_reset_password_with_valid_credentials(): void
    {
        $response = $this->actingAs($this->jpoUser)->post('/jpo/profile/password', [
            'current_password' => 'JpoPass123!',
            'password' => 'NewJpoSecurePass999!',
            'password_confirmation' => 'NewJpoSecurePass999!',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->jpoUser->refresh();
        $this->assertTrue(Hash::check('NewJpoSecurePass999!', $this->jpoUser->password));
    }

    public function test_jpo_cannot_reset_password_with_invalid_current_password(): void
    {
        $response = $this->actingAs($this->jpoUser)->post('/jpo/profile/password', [
            'current_password' => 'WrongPassword!',
            'password' => 'NewJpoSecurePass999!',
            'password_confirmation' => 'NewJpoSecurePass999!',
        ]);

        $response->assertSessionHasErrors('current_password');

        $this->jpoUser->refresh();
        $this->assertTrue(Hash::check('JpoPass123!', $this->jpoUser->password));
    }

    public function test_nsrp_form_renders_21st_century_and_technical_skills(): void
    {
        $jobPosting = JobPosting::create([
            'employer_id' => $this->employer->employer_id,
            'title' => 'Maintenance Technician',
            'description' => 'Facilities maintenance and repair',
            'vacancy_count' => 2,
            'status' => 'approved',
        ]);

        $application = JobApplication::create([
            'job_id' => $jobPosting->job_id,
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'status' => 'pending',
            'referred_by_jpo' => 0,
        ]);

        // Add specific Section VII & Section IX skills
        DB::table('jobseeker_skills')->insert([
            ['jobseeker_id' => $this->jobseeker->jobseeker_id, 'skill_name' => 'Critical Thinking', 'skill_type' => 'technical'],
            ['jobseeker_id' => $this->jobseeker->jobseeker_id, 'skill_name' => 'Team Work', 'skill_type' => 'technical'],
            ['jobseeker_id' => $this->jobseeker->jobseeker_id, 'skill_name' => 'Carpentry', 'skill_type' => 'technical'],
            ['jobseeker_id' => $this->jobseeker->jobseeker_id, 'skill_name' => 'Welding', 'skill_type' => 'technical'],
            ['jobseeker_id' => $this->jobseeker->jobseeker_id, 'skill_name' => 'Graphic Design', 'skill_type' => 'technical'], // custom / others
        ]);

        $response = $this->actingAs($this->jpoUser)->get("/jpo/evaluations/jobseekers/{$application->application_id}/nsrp");

        $response->assertOk();
        $response->assertSee('VII. 21st CENTURY SKILLS');
        $response->assertSee('IX. TECHNICAL SKILLS ACQUIRED WITHOUT FORMAL TRAINING');
        $response->assertSee('Critical Thinking');
        $response->assertSee('Team Work');
        $response->assertSee('Carpentry');
        $response->assertSee('Welding');
        $response->assertSee('Graphic Design');
    }

    public function test_form_8_placement_report_renders_dole_standard_format(): void
    {
        $reportId = DB::table('placement_reports')->insertGetId([
            'employer_id' => $this->employer->employer_id,
            'report_month' => '2026-09-01',
            'report_type' => 'employer_monthly',
            'status' => 'submitted_to_jpo',
            'report_data' => json_encode([
                'hired_list' => [
                    [
                        'name' => 'Juan Dela Cruz',
                        'sex' => 'M',
                        'position' => 'Software Developer',
                        'status' => 'hired',
                        'hired_date' => '2026-09-02',
                        'city' => 'Cebu City'
                    ]
                ]
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->jpoUser)->get("/jpo/evaluations/placement-reports/{$reportId}/print");

        $response->assertOk();
        $response->assertSee('FORM 8');
        $response->assertSee('PLACEMENT REPORT');
        $response->assertSee('Name of Activity:');
        $response->assertSee('Regional Office:');
        $response->assertSee('Status of Application');
        $response->assertSee('Juan Dela Cruz');
        $response->assertSee('Software Developer');
        $response->assertSee('PESO Officer / Staff');
        $response->assertSee('PESO Manager');
    }

    public function test_establishment_registration_and_vacancies_form_renders_official_dmdp_format(): void
    {
        $accId = DB::table('employer_accreditation')->insertGetId([
            'employer_id' => $this->employer->employer_id,
            'status' => 'submitted_to_jpo',
            'document_status' => 'complete',
            'documents' => json_encode([
                'bir_2303' => 'bir.pdf',
                'mayors_permit' => 'permit.pdf',
                'sec_dti' => 'sec.pdf',
            ]),
            'submitted_at' => now(),
        ]);

        JobPosting::create([
            'employer_id' => $this->employer->employer_id,
            'title' => 'Customer Care Specialist',
            'description' => 'Handle customer support tickets',
            'qualifications' => 'Good communication skills in English and Tagalog',
            'vacancy_count' => 5,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->jpoUser)->get("/jpo/evaluations/accreditations/{$accId}/print");

        $response->assertOk();
        $response->assertSee('ESTABLISHMENT REGISTRATION');
        $response->assertSee('Establishment Information');
        $response->assertSee('Establishment Contact Details');
        $response->assertSee('Documentary Requirements');
        $response->assertSee('APPROVAL');
        $response->assertSee('JOB VACANCIES AND QUALIFICATION FORM');
        $response->assertSee('Customer Care Specialist');
        $response->assertSee('ACCEPTS DISABILITY?');
    }
}
