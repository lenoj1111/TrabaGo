<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Jobseeker;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmployerPortalComprehensiveTest extends TestCase
{
    use RefreshDatabase;

    protected User $employerUser;
    protected Employer $employer;
    protected User $jobseekerUser;
    protected Jobseeker $jobseeker;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Admin
        $this->adminUser = User::create([
            'email' => 'admin@trabago.gov.ph',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'active',
            'is_approved' => 1,
        ]);

        // 2. Employer
        $this->employerUser = User::create([
            'email' => 'employer@cebutech.com',
            'password' => Hash::make('secret123'),
            'role' => 'employer',
            'status' => 'active',
            'is_approved' => 1,
        ]);

        $this->employer = Employer::create([
            'user_id' => $this->employerUser->user_id,
            'company_name' => 'Cebu Tech Innovation Hub',
            'is_accredited' => 1,
            'accredited_at' => now()->toDateString(),
        ]);

        // 3. Jobseeker
        $this->jobseekerUser = User::create([
            'email' => 'maria.santos@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'jobseeker',
            'status' => 'active',
            'is_approved' => 1,
        ]);

        $this->jobseeker = Jobseeker::create([
            'user_id' => $this->jobseekerUser->user_id,
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'email' => 'maria.santos@gmail.com',
            'mobile_number' => '09123456789',
        ]);
    }

    /**
     * Test creating a job posting rejects a past date.
     */
    public function test_job_posting_creation_fails_when_valid_until_is_in_the_past(): void
    {
        $pastDate = now()->subDays(3)->toDateString();

        $response = $this->actingAs($this->employerUser)->post(route('employer.job-postings.store'), [
            'title' => 'Software Engineer',
            'description' => 'Develop scalable web services in PHP and Laravel.',
            'qualifications' => 'BS Computer Science, 2 years experience',
            'vacancy_count' => 3,
            'valid_until' => $pastDate,
            'accepts_disability' => 1,
            'disability_type' => 'Hearing Impaired',
        ]);

        $response->assertSessionHasErrors(['valid_until']);
        $this->assertDatabaseMissing('job_postings', [
            'title' => 'Software Engineer',
            'employer_id' => $this->employer->employer_id,
        ]);
    }

    /**
     * Test creating a job posting succeeds with a valid future date.
     */
    public function test_job_posting_creation_succeeds_with_future_date(): void
    {
        $futureDate = now()->addMonths(1)->toDateString();

        $response = $this->actingAs($this->employerUser)->post(route('employer.job-postings.store'), [
            'title' => 'Lead Full-Stack Developer',
            'description' => 'Build high-performance web applications using modern stacks.',
            'qualifications' => 'Laravel, Vue/Alpine, MySQL/PostgreSQL',
            'vacancy_count' => 2,
            'valid_until' => $futureDate,
            'accepts_disability' => 1,
            'disability_type' => 'Mobility Accessible',
        ]);

        $response->assertRedirect(route('employer.job-postings'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('job_postings', [
            'title' => 'Lead Full-Stack Developer',
            'employer_id' => $this->employer->employer_id,
            'vacancy_count' => 2,
            'status' => 'pending',
            'created_by' => 'employer',
        ]);
    }

    /**
     * Test updating a job posting succeeds with future date and rejects a past date.
     */
    public function test_job_posting_update_validates_date_and_updates_data(): void
    {
        $job = JobPosting::create([
            'employer_id' => $this->employer->employer_id,
            'title' => 'Original Role',
            'description' => 'Original Description',
            'vacancy_count' => 1,
            'valid_until' => now()->addDays(20)->toDateString(),
            'status' => 'approved',
            'created_by' => 'employer',
        ]);

        // Attempt update with past date
        $badUpdateResponse = $this->actingAs($this->employerUser)
            ->put(route('employer.job-postings.update', $job->job_id), [
                'title' => 'Updated Role',
                'description' => 'Updated Description',
                'vacancy_count' => 5,
                'valid_until' => now()->subDay()->toDateString(),
            ]);

        $badUpdateResponse->assertSessionHasErrors(['valid_until']);

        // Update with valid future date
        $goodDate = now()->addDays(45)->toDateString();
        $goodUpdateResponse = $this->actingAs($this->employerUser)
            ->put(route('employer.job-postings.update', $job->job_id), [
                'title' => 'Senior Cloud Architect',
                'description' => 'Lead enterprise cloud migration architectures.',
                'qualifications' => 'AWS/GCP Certified, Kubernetes',
                'vacancy_count' => 4,
                'valid_until' => $goodDate,
                'accepts_disability' => 0,
            ]);

        $goodUpdateResponse->assertRedirect(route('employer.job-postings'));
        $goodUpdateResponse->assertSessionHas('success');

        $this->assertDatabaseHas('job_postings', [
            'job_id' => $job->job_id,
            'title' => 'Senior Cloud Architect',
            'vacancy_count' => 4,
        ]);
    }

    /**
     * Test job posting show and delete actions.
     */
    public function test_job_posting_show_and_delete(): void
    {
        $job = JobPosting::create([
            'employer_id' => $this->employer->employer_id,
            'title' => 'DevOps Specialist',
            'description' => 'Manage CI/CD pipelines.',
            'vacancy_count' => 1,
            'valid_until' => now()->addDays(30)->toDateString(),
            'status' => 'approved',
            'created_by' => 'employer',
        ]);

        // View job posting show page
        $showResponse = $this->actingAs($this->employerUser)
            ->get(route('employer.job-postings.show', $job->job_id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('DevOps Specialist');

        // Delete job posting
        $deleteResponse = $this->actingAs($this->employerUser)
            ->delete(route('employer.job-postings.destroy', $job->job_id));

        $deleteResponse->assertRedirect(route('employer.job-postings'));
        $this->assertDatabaseMissing('job_postings', ['job_id' => $job->job_id]);
    }

    /**
     * Test employer can mark an applicant as Hired.
     */
    public function test_employer_can_mark_applicant_as_hired(): void
    {
        $job = JobPosting::create([
            'employer_id' => $this->employer->employer_id,
            'title' => 'Database Administrator',
            'description' => 'Maintain SQL databases.',
            'vacancy_count' => 1,
            'status' => 'approved',
            'created_by' => 'employer',
        ]);

        $application = JobApplication::create([
            'job_id' => $job->job_id,
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'status' => 'pending',
            'referred_by_jpo' => true,
        ]);

        $response = $this->actingAs($this->employerUser)
            ->post(route('employer.applicants.update_status', $application->application_id), [
                'action' => 'hire',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('job_applications', [
            'application_id' => $application->application_id,
            'status' => 'hired',
        ]);

        // Verify jobseeker profile is tagged as Employed with the company they applied to
        $this->assertDatabaseHas('jobseekers', [
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'employment_status' => 'Employed',
            'hired_company' => $this->employer->company_name,
        ]);

        // Verify notification sent to jobseeker
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->jobseekerUser->user_id,
            'title' => 'Congratulations! You Have Been Hired!',
        ]);

        // Verify jobseeker profile displays Employed and the company they applied to
        $profileResponse = $this->actingAs($this->jobseekerUser)->get(route('jobseeker.profile'));
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee('Employed');
        $profileResponse->assertSee($this->employer->company_name);

        // Verify jobseeker homepage also displays Employed and the company
        $homeResponse = $this->actingAs($this->jobseekerUser)->get(route('jobseeker.home'));
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Profile Tagged as Employed');
        $homeResponse->assertSee($this->employer->company_name);
    }

    /**
     * Test employer can mark an applicant as Not Qualified.
     */
    public function test_employer_can_mark_applicant_as_not_qualified(): void
    {
        $job = JobPosting::create([
            'employer_id' => $this->employer->employer_id,
            'title' => 'Cybersecurity Analyst',
            'description' => 'Security audits and pentesting.',
            'vacancy_count' => 1,
            'status' => 'approved',
            'created_by' => 'employer',
        ]);

        $application = JobApplication::create([
            'job_id' => $job->job_id,
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'status' => 'pending',
            'referred_by_jpo' => true,
        ]);

        $reason = 'Candidate requires at least 3 years experience with CISSP accreditation.';

        $response = $this->actingAs($this->employerUser)
            ->post(route('employer.applicants.update_status', $application->application_id), [
                'action' => 'not_qualified',
                'remarks' => $reason,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('info');

        // Status must be rejected (Not Qualified)
        $this->assertDatabaseHas('job_applications', [
            'application_id' => $application->application_id,
            'status' => 'rejected',
            'jpo_notes' => $reason,
        ]);

        // Jobseeker must receive notification that status is Not Qualified
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->jobseekerUser->user_id,
            'title' => 'Application Status: Not Qualified',
        ]);
    }

    /**
     * Test employer can view placement reports.
     */
    public function test_employer_can_view_placement_reports(): void
    {
        $jpoUser = User::create([
            'email' => 'jpo_eval@trabago.gov.ph',
            'password' => Hash::make('password123'),
            'role' => 'jpo',
            'status' => 'active',
            'is_approved' => 1,
        ]);
        $jpoProfileId = DB::table('user_profiles')->insertGetId([
            'user_id' => $jpoUser->user_id,
            'full_name' => 'Officer JPO',
        ]);

        $reportData = [
            'month' => '2026-09',
            'company_name' => $this->employer->company_name,
            'total_hired' => 1,
            'hired_list' => [
                [
                    'jobseeker_name' => 'Maria Santos',
                    'position' => 'Software Engineer',
                    'hired_date' => now()->toDateString(),
                    'referred_by_jpo' => 'Yes',
                ]
            ],
            'notes' => 'Official monthly report.',
        ];

        $reportId = DB::table('placement_reports')->insertGetId([
            'employer_id' => $this->employer->employer_id,
            'jpo_id' => $jpoProfileId,
            'report_type' => 'employer_monthly',
            'report_month' => '2026-09-01',
            'report_data' => json_encode($reportData),
            'status' => 'submitted_to_jpo',
            'jpo_evaluated' => 0,
        ]);

        // 1. Placement reports index
        $indexResponse = $this->actingAs($this->employerUser)
            ->get(route('employer.placement-reports'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Placement Submission History');
        $indexResponse->assertSee('September 2026');

        // 2. View specific report print/detail
        $showResponse = $this->actingAs($this->employerUser)
            ->get(route('employer.placement-reports.show', $reportId));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Maria Santos');
        $showResponse->assertSee('Software Engineer');
    }

    /**
     * Test employer can update company and representative profile.
     */
    public function test_employer_can_update_profile(): void
    {
        $response = $this->actingAs($this->employerUser)
            ->post(route('employer.profile.update'), [
                'company_name' => 'Cebu Global Innovation Corp',
                'full_name' => 'Roberto Carlos',
                'phone' => '09998887766',
                'position' => 'Head of Human Capital',
                'department' => 'People & Culture',
                'office' => '12th Floor, Skyrise 4, Cebu IT Park',
                'specialization' => 'Information Technology & BPO',
            ]);

        $response->assertRedirect(route('employer.profile'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employers', [
            'employer_id' => $this->employer->employer_id,
            'company_name' => 'Cebu Global Innovation Corp',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $this->employerUser->user_id,
            'full_name' => 'Roberto Carlos',
            'phone' => '09998887766',
            'position' => 'Head of Human Capital',
            'department' => 'People & Culture',
        ]);
    }

    /**
     * Test employer can reset password and fails with invalid current password.
     */
    public function test_employer_can_reset_password_and_fails_with_invalid_current_password(): void
    {
        // Attempt with wrong current password
        $badResponse = $this->actingAs($this->employerUser)
            ->post(route('employer.password.reset'), [
                'current_password' => 'wrongpassword',
                'password' => 'NewSecurePassword123!',
                'password_confirmation' => 'NewSecurePassword123!',
            ]);

        $badResponse->assertSessionHasErrors(['current_password']);

        // Attempt with correct current password ('secret123')
        $goodResponse = $this->actingAs($this->employerUser)
            ->post(route('employer.password.reset'), [
                'current_password' => 'secret123',
                'password' => 'NewSecurePassword123!',
                'password_confirmation' => 'NewSecurePassword123!',
            ]);

        $goodResponse->assertRedirect(route('employer.profile', ['tab' => 'security']));
        $goodResponse->assertSessionHas('success');

        // Verify password changed
        $this->employerUser->refresh();
        $this->assertTrue(Hash::check('NewSecurePassword123!', $this->employerUser->password));
    }
}
