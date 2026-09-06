<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Jobseeker;
use App\Models\TrainingProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class JobseekerEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected User $jobseekerUser;
    protected Jobseeker $jobseeker;
    protected User $employerUser;
    protected Employer $employer;
    protected JobPosting $jobPosting;
    protected TrainingProgram $training;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Employer & Job Posting
        $this->employerUser = User::create([
            'email' => 'techcebu@example.com',
            'password' => Hash::make('password'),
            'role' => 'employer',
            'status' => 'active',
            'is_approved' => 1,
        ]);

        $this->employer = Employer::create([
            'user_id' => $this->employerUser->user_id,
            'company_name' => 'Tech Solutions Cebu',
            'business_type' => 'Corporation',
            'contact_person' => 'Jane HR',
            'is_accredited' => true,
        ]);

        $this->jobPosting = JobPosting::create([
            'employer_id' => $this->employer->employer_id,
            'title' => 'Senior Laravel Engineer',
            'description' => 'Build high-performance web applications in Cebu.',
            'qualifications' => 'Laravel, PHP, MySQL',
            'status' => 'approved',
            'vacancies_count' => 3,
            'valid_until' => now()->addDays(30)->toDateString(),
            'accepts_disability' => false,
        ]);

        // 2. Create Jobseeker User & Profile
        $this->jobseekerUser = User::create([
            'email' => 'pedro.penduko@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'jobseeker',
            'status' => 'active',
            'is_approved' => 1,
        ]);

        $this->jobseeker = Jobseeker::create([
            'user_id' => $this->jobseekerUser->user_id,
            'first_name' => 'Pedro',
            'last_name' => 'Penduko',
            'middle_name' => 'Ramos',
            'mobile_number' => '09171234567',
            'sex' => 'Male',
            'civil_status' => 'Single',
            'citizenship' => 'Filipino',
            'employment_status' => 'Unemployed',
        ]);

        // 3. Create Trainer Profile & Training Program
        $trainerProfileId = \Illuminate\Support\Facades\DB::table('user_profiles')->insertGetId([
            'user_id' => $this->employerUser->user_id,
            'full_name' => 'Master Trainer',
            'office' => 'DMDP Training Center',
        ]);

        $this->training = TrainingProgram::create([
            'trainer_id' => $trainerProfileId,
            'title' => 'Web Development & API Integration',
            'description' => 'Comprehensive vocational track on modern web services.',
            'training_type' => 'online',
            'duration_months' => 2,
        ]);
    }

    /**
     * Test that job seekers can see until when a job posting is available across views.
     */
    public function test_jobseeker_can_see_job_availability_deadline(): void
    {
        $expectedDate = $this->jobPosting->valid_until->format('M d, Y');

        // 1. Check Job Feed
        $response = $this->actingAs($this->jobseekerUser)->get(route('jobseeker.jobs'));
        $response->assertStatus(200);
        $response->assertSee('Available until');
        $response->assertSee($expectedDate);

        // 2. Check Job Show Page
        $showResponse = $this->actingAs($this->jobseekerUser)->get(route('jobseeker.jobs.show', $this->jobPosting->job_id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Application Deadline');
        $showResponse->assertSee($this->jobPosting->valid_until->format('F d, Y'));

        // 3. Check Homepage
        $homeResponse = $this->actingAs($this->jobseekerUser)->get(route('jobseeker.home'));
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Available until');
        $homeResponse->assertSee($expectedDate);
    }

    /**
     * Test application status displays 'Not Qualified' and shows JPO reason when rejected.
     */
    public function test_application_status_shows_not_qualified_and_jpo_reason(): void
    {
        // Create an application marked as rejected by JPO with notes
        $application = JobApplication::create([
            'job_id' => $this->jobPosting->job_id,
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'status' => 'rejected',
            'referred_by_jpo' => false,
            'jpo_notes' => 'Candidate requires at least 3 years of hands-on production experience in Laravel microservices.',
            'jpo_evaluated_at' => now(),
        ]);

        $response = $this->actingAs($this->jobseekerUser)->get(route('jobseeker.applications'));
        $response->assertStatus(200);

        // Must display 'Not Qualified' badge and tab
        $response->assertSee('Not Qualified');
        $response->assertDontSee('Not Selected');

        // Must display the JPO reason given
        $response->assertSee('JPO Evaluation Feedback');
        $response->assertSee('Candidate requires at least 3 years of hands-on production experience in Laravel microservices.');

        // Filter tab by rejected
        $filterResponse = $this->actingAs($this->jobseekerUser)->get(route('jobseeker.applications', ['status' => 'rejected']));
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('Candidate requires at least 3 years of hands-on production experience in Laravel microservices.');
    }

    /**
     * Test job application modal clarifies that resume attachment is optional.
     */
    public function test_application_modal_clarifies_resume_is_optional(): void
    {
        $response = $this->actingAs($this->jobseekerUser)->get(route('jobseeker.jobs'));
        $response->assertStatus(200);
        $response->assertSee('Attach Resume / CV (Optional)');
        $response->assertSee('If omitted, the employer will evaluate your application using your verified TrabaGo profile details and skills matrix.');
    }

    /**
     * Test Jobseeker profile view, update profile, and reset password.
     */
    public function test_jobseeker_profile_view_update_and_password_reset(): void
    {
        // 1. View Profile
        $viewResponse = $this->actingAs($this->jobseekerUser)->get(route('jobseeker.profile', ['tab' => 'view']));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Pedro Penduko');
        $viewResponse->assertSee('Personal Information');
        $viewResponse->assertSee('Residential Address');

        // 2. Update Profile
        $updateResponse = $this->actingAs($this->jobseekerUser)->post(route('jobseeker.profile.update_info'), [
            'first_name' => 'Pedro',
            'last_name' => 'Penduko Jr.',
            'middle_name' => 'Ramos',
            'mobile_number' => '09189998877',
            'sex' => 'Male',
            'civil_status' => 'Married',
            'citizenship' => 'Filipino',
            'employment_status' => 'Employed',
            'address_barangay' => 'Lahug',
            'address_city' => 'Cebu City',
            'education_level' => 'College Degree',
            'education_course' => 'BS Computer Science',
            'education_school' => 'University of San Carlos',
            'experience_company' => 'Cebu IT Park Innovations',
            'experience_position' => 'Web Developer',
            'experience_duration' => '3 - 5 Years',
        ]);

        $updateResponse->assertRedirect(route('jobseeker.profile'));
        $this->assertDatabaseHas('jobseekers', [
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'last_name' => 'Penduko Jr.',
            'civil_status' => 'Married',
        ]);

        // 3. Reset Password (with invalid current password fails)
        $badPasswordResponse = $this->actingAs($this->jobseekerUser)->post(route('jobseeker.password.change'), [
            'current_password' => 'WrongPassword123!',
            'password' => 'BrandNewPassword2026!',
            'password_confirmation' => 'BrandNewPassword2026!',
        ]);
        $badPasswordResponse->assertSessionHasErrors(['current_password']);

        // 4. Reset Password (valid)
        $goodPasswordResponse = $this->actingAs($this->jobseekerUser)->post(route('jobseeker.password.change'), [
            'current_password' => 'Secret123!',
            'password' => 'BrandNewPassword2026!',
            'password_confirmation' => 'BrandNewPassword2026!',
        ]);
        $goodPasswordResponse->assertRedirect(route('jobseeker.profile', ['tab' => 'security']));
        $goodPasswordResponse->assertSessionHas('success');

        // Verify password hash updated
        $this->jobseekerUser->refresh();
        $this->assertTrue(Hash::check('BrandNewPassword2026!', $this->jobseekerUser->password));
    }

    /**
     * Test job seekers can enroll in available training skills.
     */
    public function test_jobseeker_can_enroll_in_training_skills(): void
    {
        // 1. Check training catalog index has open enrollment
        $indexResponse = $this->actingAs($this->jobseekerUser)->get(route('jobseeker.training'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Web Development & API Integration');
        $indexResponse->assertSee('Enroll Now');

        // 2. Enroll in training program
        $enrollResponse = $this->actingAs($this->jobseekerUser)->post(route('jobseeker.training.enroll', $this->training->training_id));
        $enrollResponse->assertRedirect(route('jobseeker.training.show', $this->training->training_id));
        $enrollResponse->assertSessionHas('success');

        // 3. Verify enrollment record in database
        $this->assertDatabaseHas('training_enrollments', [
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'training_id' => $this->training->training_id,
            'status' => 'enrolled',
        ]);

        // 4. Check course show page reflects enrolled status
        $showResponse = $this->actingAs($this->jobseekerUser)->get(route('jobseeker.training.show', $this->training->training_id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Enrolled');

        // 5. Enrolled filter tab shows the course
        $filterResponse = $this->actingAs($this->jobseekerUser)->get(route('jobseeker.training', ['filter' => 'enrolled']));
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('Web Development & API Integration');
        $filterResponse->assertSee('Continue');
    }

    /**
     * Test jobseekers cannot see or apply to expired job postings.
     */
    public function test_jobseeker_cannot_see_or_apply_to_expired_job(): void
    {
        // 1. Create an expired job posting
        $expiredJob = JobPosting::create([
            'employer_id' => $this->employer->employer_id,
            'title' => 'Expired Frontend Developer Role',
            'description' => 'This job posting deadline has passed.',
            'qualifications' => 'React, JavaScript',
            'status' => 'approved',
            'vacancy_count' => 1,
            'valid_until' => now()->subDays(5)->toDateString(),
            'accepts_disability' => false,
        ]);

        // 2. Create an active non-expired job posting
        $activeJob = JobPosting::create([
            'employer_id' => $this->employer->employer_id,
            'title' => 'Active Mobile Developer Role',
            'description' => 'This job posting is currently open.',
            'qualifications' => 'Flutter, Dart',
            'status' => 'approved',
            'vacancy_count' => 2,
            'valid_until' => now()->addDays(20)->toDateString(),
            'accepts_disability' => false,
        ]);

        // 3. Homepage check: Active job is shown, expired job is NOT shown
        $homeResponse = $this->actingAs($this->jobseekerUser)->get(route('jobseeker.home'));
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Active Mobile Developer Role');
        $homeResponse->assertDontSee('Expired Frontend Developer Role');

        // 4. Explorer check: Active job is in search results, expired job is NOT
        $jobsResponse = $this->actingAs($this->jobseekerUser)->get(route('jobseeker.jobs'));
        $jobsResponse->assertStatus(200);
        $jobsResponse->assertSee('Active Mobile Developer Role');
        $jobsResponse->assertDontSee('Expired Frontend Developer Role');

        // 5. Direct view check: Visiting expired job redirects with error
        $showResponse = $this->actingAs($this->jobseekerUser)->get(route('jobseeker.jobs.show', $expiredJob->job_id));
        $showResponse->assertRedirect(route('jobseeker.jobs'));
        $showResponse->assertSessionHas('error');

        // 6. Direct apply check: Attempting to apply to expired job is blocked and redirects with error
        $applyResponse = $this->actingAs($this->jobseekerUser)->post(route('jobseeker.jobs.apply', $expiredJob->job_id));
        $applyResponse->assertRedirect(route('jobseeker.jobs'));
        $applyResponse->assertSessionHas('error');

        // Verify NO job application was created for the expired job
        $this->assertDatabaseMissing('job_applications', [
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'job_id' => $expiredJob->job_id,
        ]);

        // 7. Active job can be viewed and applied to normally
        $activeShowResponse = $this->actingAs($this->jobseekerUser)->get(route('jobseeker.jobs.show', $activeJob->job_id));
        $activeShowResponse->assertStatus(200);
        $activeShowResponse->assertSee('Active Mobile Developer Role');

        $activeApplyResponse = $this->actingAs($this->jobseekerUser)->post(route('jobseeker.jobs.apply', $activeJob->job_id));
        $activeApplyResponse->assertRedirect(route('jobseeker.applications'));
        $activeApplyResponse->assertSessionHas('success');

        $this->assertDatabaseHas('job_applications', [
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'job_id' => $activeJob->job_id,
            'status' => 'pending',
        ]);
    }

    /**
     * Test when a jobseeker gets hired, profile is tagged as Employed with the company name.
     */
    public function test_hired_jobseeker_profile_tagged_as_employed_with_company_name(): void
    {
        $application = JobApplication::create([
            'job_id' => $this->jobPosting->job_id,
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'status' => 'pending',
            'referred_by_jpo' => false,
        ]);

        // Employer hires the applicant
        $response = $this->actingAs($this->employerUser)->post(
            route('employer.applicants.update_status', $application->application_id),
            ['action' => 'hire']
        );

        $response->assertRedirect();

        // Refresh jobseeker model
        $this->jobseeker->refresh();

        $this->assertEquals('Employed', $this->jobseeker->employment_status);
        $this->assertEquals('Tech Solutions Cebu', $this->jobseeker->hired_company);
        $this->assertTrue($this->jobseeker->isEmployed());

        // View Profile: Verify Employed badge & company name are displayed
        $profileResponse = $this->actingAs($this->jobseekerUser)->get(route('jobseeker.profile'));
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee('Tagged as Employed');
        $profileResponse->assertSee('Tech Solutions Cebu');
    }

    /**
     * Test an already hired/employed jobseeker cannot apply for another job.
     */
    public function test_employed_jobseeker_cannot_apply_to_another_job(): void
    {
        // 1. Tag jobseeker as Employed at Tech Solutions Cebu
        JobApplication::create([
            'job_id' => $this->jobPosting->job_id,
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'status' => 'hired',
            'hired_date' => now()->toDateString(),
            'referred_by_jpo' => false,
        ]);

        $this->jobseeker->update([
            'employment_status' => 'Employed',
            'hired_company' => 'Tech Solutions Cebu',
        ]);

        // 2. Create another employer and job opening
        $anotherEmployerUser = User::create([
            'email' => 'innocorp@example.com',
            'password' => Hash::make('password'),
            'role' => 'employer',
            'status' => 'active',
            'is_approved' => 1,
        ]);

        $anotherEmployer = Employer::create([
            'user_id' => $anotherEmployerUser->user_id,
            'company_name' => 'InnoCorp Cebu',
            'business_type' => 'Corporation',
            'contact_person' => 'Bob HR',
            'is_accredited' => true,
        ]);

        $secondJob = JobPosting::create([
            'employer_id' => $anotherEmployer->employer_id,
            'title' => 'Cloud Systems Architect',
            'description' => 'Manage AWS & Docker infrastructure.',
            'qualifications' => 'AWS, Docker, Linux',
            'status' => 'approved',
            'vacancies_count' => 1,
            'valid_until' => now()->addDays(25)->toDateString(),
            'accepts_disability' => false,
        ]);

        // 3. Visiting the job page displays employed restriction
        $showResponse = $this->actingAs($this->jobseekerUser)->get(route('jobseeker.jobs.show', $secondJob->job_id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Currently Employed at Tech Solutions Cebu');
        $showResponse->assertSee('Employed candidates cannot apply for another job');

        // 4. Attempting to apply via POST is blocked and redirects with error
        $applyResponse = $this->actingAs($this->jobseekerUser)->post(route('jobseeker.jobs.apply', $secondJob->job_id));
        $applyResponse->assertRedirect(route('jobseeker.jobs'));
        $applyResponse->assertSessionHas('error');

        // Verify NO new application was created for the second job
        $this->assertDatabaseMissing('job_applications', [
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'job_id' => $secondJob->job_id,
        ]);
    }

    /**
     * Test full resignation cycle:
     * - Employed jobseeker requests resignation.
     * - Employer approves resignation.
     * - Jobseeker profile resets to 'Unemployed'.
     * - Jobseeker can now apply for other jobs.
     */
    public function test_jobseeker_resignation_request_and_employer_approval_flow(): void
    {
        // 1. Setup hired state
        $hiredApplication = JobApplication::create([
            'job_id' => $this->jobPosting->job_id,
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'status' => 'hired',
            'hired_date' => now()->subMonths(3)->toDateString(),
            'referred_by_jpo' => false,
        ]);

        $this->jobseeker->update([
            'employment_status' => 'Employed',
            'hired_company' => 'Tech Solutions Cebu',
        ]);

        // 2. Setup second job
        $secondJob = JobPosting::create([
            'employer_id' => $this->employer->employer_id,
            'title' => 'Product Designer',
            'description' => 'Design user interfaces.',
            'qualifications' => 'Figma, UI/UX',
            'status' => 'approved',
            'vacancies_count' => 1,
            'valid_until' => now()->addDays(25)->toDateString(),
            'accepts_disability' => false,
        ]);

        // 3. Jobseeker submits resignation request
        $resignRequestResponse = $this->actingAs($this->jobseekerUser)->post(
            route('jobseeker.resignation.request'),
            ['reason' => 'Pursuing higher education and career progression.']
        );

        $resignRequestResponse->assertRedirect();
        $resignRequestResponse->assertSessionHas('success');

        $hiredApplication->refresh();
        $this->assertEquals('requested', $hiredApplication->resignation_status);
        $this->assertEquals('Pursuing higher education and career progression.', $hiredApplication->resignation_reason);
        $this->assertNotNull($hiredApplication->resignation_requested_at);

        // Jobseeker still cannot apply while resignation is only pending approval
        $prematureApplyResponse = $this->actingAs($this->jobseekerUser)->post(route('jobseeker.jobs.apply', $secondJob->job_id));
        $prematureApplyResponse->assertRedirect(route('jobseeker.jobs'));
        $prematureApplyResponse->assertSessionHas('error');

        // 4. Employer views candidates and sees resignation request
        $employerCandidatesResponse = $this->actingAs($this->employerUser)->get(route('employer.referred-jobseekers'));
        $employerCandidatesResponse->assertStatus(200);
        $employerCandidatesResponse->assertSee('Resignation Requested');
        $employerCandidatesResponse->assertSee('Pursuing higher education and career progression.');

        // 5. Employer approves the resignation
        $approveResponse = $this->actingAs($this->employerUser)->post(
            route('employer.applicants.resignation', $hiredApplication->application_id),
            [
                'action' => 'approve',
                'remarks' => 'Employee cleared of all company responsibilities. Best of luck!',
            ]
        );

        $approveResponse->assertRedirect();
        $approveResponse->assertSessionHas('success');

        // Verify application and jobseeker profile status
        $hiredApplication->refresh();
        $this->jobseeker->refresh();

        $this->assertEquals('approved', $hiredApplication->resignation_status);
        $this->assertEquals('Employee cleared of all company responsibilities. Best of luck!', $hiredApplication->resignation_remarks);
        $this->assertNotNull($hiredApplication->resignation_approved_at);

        // Profile must now be tagged as Unemployed and company cleared
        $this->assertEquals('Unemployed', $this->jobseeker->employment_status);
        $this->assertNull($this->jobseeker->hired_company);
        $this->assertFalse($this->jobseeker->isEmployed());

        // 6. Jobseeker applications view shows approved status
        $appViewResponse = $this->actingAs($this->jobseekerUser)->get(route('jobseeker.applications'));
        $appViewResponse->assertStatus(200);
        $appViewResponse->assertSee('Resignation Approved by Tech Solutions Cebu');

        // 7. Jobseeker can now successfully apply to another job!
        $reapplyResponse = $this->actingAs($this->jobseekerUser)->post(route('jobseeker.jobs.apply', $secondJob->job_id));
        $reapplyResponse->assertRedirect(route('jobseeker.applications'));
        $reapplyResponse->assertSessionHas('success');

        $this->assertDatabaseHas('job_applications', [
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'job_id' => $secondJob->job_id,
            'status' => 'pending',
        ]);
    }

    /**
     * Test employer declining resignation keeps candidate employed and restricted.
     */
    public function test_declined_resignation_keeps_candidate_employed(): void
    {
        $hiredApplication = JobApplication::create([
            'job_id' => $this->jobPosting->job_id,
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'status' => 'hired',
            'hired_date' => now()->subMonths(1)->toDateString(),
            'resignation_status' => 'requested',
            'resignation_reason' => 'Want to explore other options.',
            'resignation_requested_at' => now(),
            'referred_by_jpo' => false,
        ]);

        $this->jobseeker->update([
            'employment_status' => 'Employed',
            'hired_company' => 'Tech Solutions Cebu',
        ]);

        // Employer declines resignation
        $rejectResponse = $this->actingAs($this->employerUser)->post(
            route('employer.applicants.resignation', $hiredApplication->application_id),
            [
                'action' => 'reject',
                'remarks' => 'Contractual minimum bond of 6 months not yet reached.',
            ]
        );

        $rejectResponse->assertRedirect();
        $hiredApplication->refresh();
        $this->jobseeker->refresh();

        $this->assertEquals('rejected', $hiredApplication->resignation_status);
        $this->assertEquals('Contractual minimum bond of 6 months not yet reached.', $hiredApplication->resignation_remarks);

        // Jobseeker remains Employed
        $this->assertEquals('Employed', $this->jobseeker->employment_status);
        $this->assertTrue($this->jobseeker->isEmployed());

        // Check Jobseeker applications page displays declined message
        $appViewResponse = $this->actingAs($this->jobseekerUser)->get(route('jobseeker.applications'));
        $appViewResponse->assertStatus(200);
        $appViewResponse->assertSee('Resignation Request Declined');
        $appViewResponse->assertSee('Contractual minimum bond of 6 months not yet reached.');
    }
}
