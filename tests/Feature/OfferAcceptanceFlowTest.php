<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Jobseeker;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OfferAcceptanceFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $jobseekerUser;
    protected Jobseeker $jobseeker;
    protected User $employerUserA;
    protected Employer $employerA;
    protected JobPosting $jobA;
    protected User $employerUserB;
    protected Employer $employerB;
    protected JobPosting $jobB;

    protected function setUp(): void
    {
        parent::setUp();

        // Jobseeker
        $this->jobseekerUser = User::create([
            'email' => 'juan.delacruz@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'jobseeker',
            'status' => 'active',
            'is_approved' => 1,
        ]);

        $this->jobseeker = Jobseeker::create([
            'user_id' => $this->jobseekerUser->user_id,
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => 'juan.delacruz@example.com',
            'mobile_number' => '09171234567',
            'employment_status' => 'Unemployed',
        ]);

        // Employer A (Acme Corp)
        $this->employerUserA = User::create([
            'email' => 'hr@acmecorp.com',
            'password' => Hash::make('password'),
            'role' => 'employer',
            'status' => 'active',
            'is_approved' => 1,
        ]);

        $this->employerA = Employer::create([
            'user_id' => $this->employerUserA->user_id,
            'company_name' => 'Acme Corporation',
            'business_type' => 'Corporation',
            'contact_person' => 'Alice HR',
            'is_accredited' => true,
        ]);

        $this->jobA = JobPosting::create([
            'employer_id' => $this->employerA->employer_id,
            'title' => 'Software Engineer',
            'description' => 'Build backend services.',
            'qualifications' => 'PHP, Laravel',
            'status' => 'approved',
            'vacancies_count' => 2,
            'valid_until' => now()->addDays(30)->toDateString(),
        ]);

        // Employer B (Beta Tech)
        $this->employerUserB = User::create([
            'email' => 'hr@betatech.com',
            'password' => Hash::make('password'),
            'role' => 'employer',
            'status' => 'active',
            'is_approved' => 1,
        ]);

        $this->employerB = Employer::create([
            'user_id' => $this->employerUserB->user_id,
            'company_name' => 'Beta Technologies',
            'business_type' => 'Corporation',
            'contact_person' => 'Bob HR',
            'is_accredited' => true,
        ]);

        $this->jobB = JobPosting::create([
            'employer_id' => $this->employerB->employer_id,
            'title' => 'Frontend Developer',
            'description' => 'Build UI components.',
            'qualifications' => 'JavaScript, CSS',
            'status' => 'approved',
            'vacancies_count' => 1,
            'valid_until' => now()->addDays(30)->toDateString(),
        ]);
    }

    public function test_employer_can_extend_job_offer_with_terms(): void
    {
        $application = JobApplication::create([
            'job_id' => $this->jobA->job_id,
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'status' => 'interview',
        ]);

        $response = $this->actingAs($this->employerUserA)
            ->post(route('employer.applicants.update_status', $application->application_id), [
                'action' => 'offer',
                'offer_salary' => 35000.00,
                'offer_start_date' => now()->addWeeks(2)->toDateString(),
                'offer_notes' => 'Welcome to Acme Corporation! Day 1 HMO and hybrid work arrangement.',
            ]);

        $response->assertSessionHas('success');

        $application->refresh();
        $this->assertEquals('offered', $application->status);
        $this->assertEquals(35000.00, (float)$application->offer_salary);
        $this->assertNotNull($application->offered_at);
        $this->assertStringContainsString('Day 1 HMO', $application->offer_notes);

        // Notification sent to jobseeker
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->jobseekerUser->user_id,
            'title' => 'Job Offer Received!',
        ]);
    }

    public function test_jobseeker_can_view_pending_job_offer_and_terms(): void
    {
        JobApplication::create([
            'job_id' => $this->jobA->job_id,
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'status' => 'offered',
            'offered_at' => now(),
            'offer_salary' => 45000.00,
            'offer_start_date' => now()->addDays(14)->toDateString(),
            'offer_notes' => 'Full health benefits and performance bonus.',
        ]);

        $response = $this->actingAs($this->jobseekerUser)
            ->get(route('jobseeker.applications'));

        $response->assertOk();
        $response->assertSee('Formal Job Offer from Acme Corporation');
        $response->assertSee('45,000.00');
        $response->assertSee('Accept Job Offer');
        $response->assertSee('Decline Offer');
    }

    public function test_jobseeker_can_decline_job_offer(): void
    {
        $application = JobApplication::create([
            'job_id' => $this->jobA->job_id,
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'status' => 'offered',
            'offered_at' => now(),
            'offer_salary' => 30000.00,
        ]);

        $response = $this->actingAs($this->jobseekerUser)
            ->post(route('jobseeker.applications.decline_offer', $application->application_id), [
                'decline_reason' => 'Pursuing another opportunity with different schedule.',
            ]);

        $response->assertRedirect(route('jobseeker.applications'));
        $response->assertSessionHas('info');

        $application->refresh();
        $this->assertEquals('declined', $application->status);
        $this->assertNotNull($application->declined_at);
        $this->assertEquals('Pursuing another opportunity with different schedule.', $application->decline_reason);

        // Jobseeker profile remains unemployed
        $this->jobseeker->refresh();
        $this->assertEquals('Unemployed', $this->jobseeker->employment_status);

        // Employer received notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->employerUserA->user_id,
            'title' => 'Job Offer Declined',
        ]);
    }

    public function test_jobseeker_accepting_offer_sets_hired_and_auto_withdraws_other_applications(): void
    {
        // Application A (Acme Corp) - Offered
        $appA = JobApplication::create([
            'job_id' => $this->jobA->job_id,
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'status' => 'offered',
            'offered_at' => now(),
            'offer_salary' => 50000.00,
        ]);

        // Application B (Beta Tech) - Under review / interview
        $appB = JobApplication::create([
            'job_id' => $this->jobB->job_id,
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'status' => 'interview',
        ]);

        // Jobseeker accepts offer A
        $response = $this->actingAs($this->jobseekerUser)
            ->post(route('jobseeker.applications.accept_offer', $appA->application_id));

        $response->assertRedirect(route('jobseeker.applications'));
        $response->assertSessionHas('success');

        // Application A is hired
        $appA->refresh();
        $this->assertEquals('hired', $appA->status);
        $this->assertNotNull($appA->hired_date);

        // Jobseeker profile updated to Employed at Acme Corp
        $this->jobseeker->refresh();
        $this->assertEquals('Employed', $this->jobseeker->employment_status);
        $this->assertEquals('Acme Corporation', $this->jobseeker->hired_company);

        // Application B is automatically withdrawn
        $appB->refresh();
        $this->assertEquals('withdrawn', $appB->status);

        // Notifications checked
        // 1. Acme Corporation notified of offer acceptance
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->employerUserA->user_id,
            'title' => 'Job Offer Accepted!',
        ]);

        // 2. Beta Technologies notified that application was withdrawn
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->employerUserB->user_id,
            'title' => 'Application Withdrawn (Candidate Accepted Another Offer)',
        ]);

        // 3. Jobseeker congratulated
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->jobseekerUser->user_id,
            'title' => 'Congratulations on Your New Job!',
        ]);
    }
}
