<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Jobseeker;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MultiRoleProgramFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $supervisorUser;
    protected User $jpoUser;
    protected User $employerUser;
    protected User $jobseekerUser;

    protected Employer $employer;
    protected Jobseeker $jobseeker;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Admin
        $this->adminUser = User::create([
            'email' => 'admin@trabago.gov.ph',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'is_approved' => 1,
        ]);

        // 2. PESD Supervisor
        $this->supervisorUser = User::create([
            'email' => 'supervisor@trabago.gov.ph',
            'password' => Hash::make('password'),
            'role' => 'supervisor',
            'status' => 'active',
            'is_approved' => 1,
        ]);
        DB::table('user_profiles')->insert([
            'user_id' => $this->supervisorUser->user_id,
            'full_name' => 'Maria Supervisor',
            'office' => 'PESD Cebu City',
        ]);

        // 3. Job Placement Officer (JPO)
        $this->jpoUser = User::create([
            'email' => 'jpo@trabago.gov.ph',
            'password' => Hash::make('password'),
            'role' => 'jpo',
            'status' => 'active',
            'is_approved' => 1,
        ]);
        DB::table('user_profiles')->insert([
            'user_id' => $this->jpoUser->user_id,
            'full_name' => 'Pedro JPO',
            'office' => 'DMDP Placement Office',
        ]);

        // 4. Employer
        $this->employerUser = User::create([
            'email' => 'techcorp@cebu.com',
            'password' => Hash::make('password'),
            'role' => 'employer',
            'status' => 'active',
            'is_approved' => 1,
        ]);
        $this->employer = Employer::create([
            'user_id' => $this->employerUser->user_id,
            'company_name' => 'Cebu Tech Solutions Inc.',
            'is_accredited' => 0,
        ]);

        // 5. Jobseeker
        $this->jobseekerUser = User::create([
            'email' => 'juan@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'jobseeker',
            'status' => 'active',
            'is_approved' => 1,
        ]);
        $this->jobseeker = Jobseeker::create([
            'user_id' => $this->jobseekerUser->user_id,
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => 'juan@gmail.com',
            'employment_status' => 'Looking for job',
        ]);
    }

    public function test_employer_creates_job_posting_sent_to_admin_and_admin_approves(): void
    {
        // 1. Employer creates job posting (Figure 9)
        $createResponse = $this->actingAs($this->employerUser)->post('/employer/job-postings', [
            'title' => 'Software Quality Analyst',
            'description' => 'Perform regression and functional testing on web portals.',
            'qualifications' => 'IT Graduate, Attention to detail',
            'vacancy_count' => 2,
            'accepts_disability' => 1,
            'disability_type' => 'Hearing Impaired Accommodated',
        ]);
        $createResponse->assertRedirect('/employer/job-postings');

        $this->assertDatabaseHas('job_postings', [
            'title' => 'Software Quality Analyst',
            'employer_id' => $this->employer->employer_id,
            'status' => 'pending', // Sent to Admin
        ]);

        $job = JobPosting::where('title', 'Software Quality Analyst')->first();

        // 2. Admin approves job posting (Figure 10)
        $approveResponse = $this->actingAs($this->adminUser)->post("/admin/approvals/job-postings/{$job->job_id}/approve");
        $approveResponse->assertRedirect();

        $this->assertDatabaseHas('job_postings', [
            'job_id' => $job->job_id,
            'status' => 'approved',
        ]);
    }

    public function test_full_accreditation_pipeline_employer_to_jpo_to_supervisor_to_admin(): void
    {
        // Stage 1: Employer passes accreditation papers to JPO (Figure 9)
        $uploadResponse = $this->actingAs($this->employerUser)->post('/employer/accreditation/upload', [
            'business_permit' => UploadedFile::fake()->create('mayors_permit.pdf', 300, 'application/pdf'),
            'sec_dti' => UploadedFile::fake()->create('dti_cert.pdf', 300, 'application/pdf'),
        ]);
        $uploadResponse->assertRedirect('/employer/accreditation');

        $this->assertDatabaseHas('employer_accreditation', [
            'employer_id' => $this->employer->employer_id,
            'status' => 'submitted_to_jpo',
        ]);

        $acc = DB::table('employer_accreditation')->where('employer_id', $this->employer->employer_id)->first();

        // Stage 2: JPO evaluates & recommends to PESD Supervisor (Figure 8)
        $jpoResponse = $this->actingAs($this->jpoUser)->post("/jpo/evaluations/accreditations/{$acc->accreditation_id}/recommend", [
            'action' => 'recommend',
            'remarks' => 'Mayor permit and DTI verified authentic.',
        ]);
        $jpoResponse->assertRedirect();

        $this->assertDatabaseHas('employer_accreditation', [
            'accreditation_id' => $acc->accreditation_id,
            'status' => 'jpo_approved',
            'jpo_reviewed' => 1,
        ]);

        // Stage 3: PESD Supervisor approves & sends to Admin (Figure 11)
        $supResponse = $this->actingAs($this->supervisorUser)->post("/supervisor/accreditations/{$acc->accreditation_id}/approve", [
            'action' => 'approve',
            'remarks' => 'Endorsed for official accreditation by PESD Supervisor.',
        ]);
        $supResponse->assertRedirect();

        $this->assertDatabaseHas('employer_accreditation', [
            'accreditation_id' => $acc->accreditation_id,
            'status' => 'supervisor_approved',
            'supervisor_approved' => 1,
        ]);

        // Stage 4: Admin officially accredits the Employer (Figure 10)
        $adminResponse = $this->actingAs($this->adminUser)->post("/admin/approvals/accreditations/{$acc->accreditation_id}/approve", [
            'remarks' => 'Official DMDP accreditation granted.',
        ]);
        $adminResponse->assertRedirect();

        $this->assertDatabaseHas('employer_accreditation', [
            'accreditation_id' => $acc->accreditation_id,
            'status' => 'admin_approved',
            'admin_approved' => 1,
        ]);

        $this->assertDatabaseHas('employers', [
            'employer_id' => $this->employer->employer_id,
            'is_accredited' => 1,
        ]);
    }

    public function test_jpo_evaluates_jobseeker_refers_to_employer_and_employer_interviews_and_hires(): void
    {
        // Job posting
        $job = JobPosting::create([
            'employer_id' => $this->employer->employer_id,
            'title' => 'Frontend Developer',
            'vacancy_count' => 1,
            'status' => 'approved',
        ]);

        // Job application
        $app = JobApplication::create([
            'job_id' => $job->job_id,
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'status' => 'pending',
            'referred_by_jpo' => 0,
        ]);

        // 1. JPO evaluates applicant and refers to Employer (Figure 8)
        $referResponse = $this->actingAs($this->jpoUser)->post("/jpo/evaluations/jobseekers/{$app->application_id}/refer", [
            'recommendation' => 'refer',
            'remarks' => 'Applicant demonstrates strong UI skills and high AI compatibility.',
        ]);
        $referResponse->assertRedirect();

        $this->assertDatabaseHas('job_applications', [
            'application_id' => $app->application_id,
            'referred_by_jpo' => 1,
            'status' => 'reviewed',
        ]);

        // 2. Employer views referred candidate and schedules interview (Figure 9)
        $interviewResponse = $this->actingAs($this->employerUser)->post("/employer/applicants/{$app->application_id}/status", [
            'action' => 'interview',
            'interview_schedule' => date('Y-m-d H:i:s', strtotime('+3 days')),
            'interview_mode' => 'online',
            'interview_location' => 'https://meet.google.com/xyz-test',
        ]);
        $interviewResponse->assertRedirect();

        $this->assertDatabaseHas('job_applications', [
            'application_id' => $app->application_id,
            'status' => 'interview',
            'interview_mode' => 'online',
        ]);

        // 3. Employer marks candidate as Hired (Figure 9)
        $hireResponse = $this->actingAs($this->employerUser)->post("/employer/applicants/{$app->application_id}/status", [
            'action' => 'hire',
        ]);
        $hireResponse->assertRedirect();

        $this->assertDatabaseHas('job_applications', [
            'application_id' => $app->application_id,
            'status' => 'hired',
        ]);
    }

    public function test_placement_report_pipeline_employer_to_jpo_to_admin(): void
    {
        // 1. Employer generates monthly placement report (Figure 9)
        $generateResponse = $this->actingAs($this->employerUser)->post('/employer/placement-reports/generate', [
            'report_month' => date('Y-m'),
            'notes' => 'Monthly placement report for DMDP.',
        ]);
        $generateResponse->assertRedirect('/employer/placement-reports');

        $this->assertDatabaseHas('placement_reports', [
            'employer_id' => $this->employer->employer_id,
            'status' => 'submitted_to_jpo',
        ]);

        $rep = DB::table('placement_reports')->where('employer_id', $this->employer->employer_id)->first();

        // 2. JPO evaluates and forwards report to Admin (Figure 8)
        $jpoResponse = $this->actingAs($this->jpoUser)->post("/jpo/evaluations/placement-reports/{$rep->report_id}/forward", [
            'remarks' => 'Verified hired candidate records against system placements.',
        ]);
        $jpoResponse->assertRedirect();

        $this->assertDatabaseHas('placement_reports', [
            'report_id' => $rep->report_id,
            'status' => 'jpo_evaluated',
            'jpo_evaluated' => 1,
        ]);

        // 3. Admin authorizes & archives placement report (Figure 10)
        $adminResponse = $this->actingAs($this->adminUser)->post("/admin/approvals/placement-reports/{$rep->report_id}/approve", [
            'remarks' => 'Approved and logged in Cebu City official archives.',
        ]);
        $adminResponse->assertRedirect();

        $this->assertDatabaseHas('placement_reports', [
            'report_id' => $rep->report_id,
            'status' => 'approved',
        ]);
    }

    public function test_admin_jobseeker_status_monitoring_directory(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/jobseekers');
        $response->assertOk();
        $response->assertSee('Juan');
        $response->assertSee('Dela Cruz');
        $response->assertSee('Jobseeker Status Directory');
    }
}
