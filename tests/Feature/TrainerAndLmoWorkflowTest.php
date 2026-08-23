<?php

namespace Tests\Feature;

use App\Models\Jobseeker;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TrainerAndLmoWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $trainerUser;
    protected User $lmoUser;
    protected User $jobseekerUser;
    protected Jobseeker $jobseeker;
    protected int $trainingId;
    protected int $enrollmentId;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Trainer User
        $this->trainerUser = User::create([
            'email' => 'trainer@trabago.gov.ph',
            'password' => Hash::make('password'),
            'role' => 'trainer',
            'status' => 'active',
            'is_approved' => 1,
        ]);
        $trainerProfileId = DB::table('user_profiles')->insertGetId([
            'user_id' => $this->trainerUser->user_id,
            'full_name' => 'Prof. Alex Trainer',
            'office' => 'DMDP Skills Development Center',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. LMO User
        $this->lmoUser = User::create([
            'email' => 'lmo@trabago.gov.ph',
            'password' => Hash::make('password'),
            'role' => 'lmo',
            'status' => 'active',
            'is_approved' => 1,
        ]);
        DB::table('user_profiles')->insert([
            'user_id' => $this->lmoUser->user_id,
            'full_name' => 'Carla LMO Officer',
            'office' => 'Labor Market Information Division',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Jobseeker User
        $this->jobseekerUser = User::create([
            'email' => 'maria@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'jobseeker',
            'status' => 'active',
            'is_approved' => 1,
        ]);
        $this->jobseeker = Jobseeker::create([
            'user_id' => $this->jobseekerUser->user_id,
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'email' => 'maria@gmail.com',
            'employment_status' => 'Looking for job',
        ]);

        // Training Course
        $this->trainingId = DB::table('training_programs')->insertGetId([
            'trainer_id' => $trainerProfileId,
            'title' => 'Web Development & PHP Fundamentals',
            'training_type' => 'online',
            'duration_months' => 2,
            'description' => 'Comprehensive vocational training on full-stack web development.',
        ]);

        // Training Enrollment
        $this->enrollmentId = DB::table('training_enrollments')->insertGetId([
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'training_id' => $this->trainingId,
            'training_type' => 'online',
            'status' => 'enrolled',
            'answers' => json_encode([
                ['question' => 'What is PHP?', 'selected_answer' => 'A server-side scripting language', 'is_correct' => true],
                ['question' => 'What is MVC?', 'selected_answer' => 'Model View Controller architecture', 'is_correct' => true],
            ]),
        ]);
    }

    public function test_trainer_dashboard_access_and_metrics(): void
    {
        $response = $this->actingAs($this->trainerUser)->get('/trainer/dashboard');
        $response->assertOk();
        $response->assertSee('Trainer Command Center');
        $response->assertSee('Maria');
        $response->assertSee('Web Development & PHP Fundamentals');
    }

    public function test_trainer_updates_enrollment_status(): void
    {
        // Figure 12: Update Enrollment Status
        $response = $this->actingAs($this->trainerUser)->post("/trainer/enrollments/{$this->enrollmentId}/status", [
            'status' => 'in_progress',
            'lab_remarks' => 'Student actively attending morning coding lab sessions.',
        ]);
        $response->assertRedirect();

        $this->assertDatabaseHas('training_enrollments', [
            'enrollment_id' => $this->enrollmentId,
            'status' => 'in_progress',
            'lab_remarks' => 'Student actively attending morning coding lab sessions.',
        ]);
    }

    public function test_trainer_evaluates_course_answers_and_grades_submission(): void
    {
        // Figure 12: Evaluate Training Course Answer
        $response = $this->actingAs($this->trainerUser)->post("/trainer/enrollments/{$this->enrollmentId}/evaluate", [
            'score' => 92.5,
            'trainer_feedback' => 'Exceptional comprehension of MVC architectural principles.',
        ]);
        $response->assertRedirect('/trainer/enrollments');

        $this->assertDatabaseHas('training_enrollments', [
            'enrollment_id' => $this->enrollmentId,
            'score' => 92.5,
            'passed' => 1,
            'status' => 'completed',
            'trainer_feedback' => 'Exceptional comprehension of MVC architectural principles.',
        ]);
    }

    public function test_trainer_generates_certificate_and_grants_verified_skill(): void
    {
        // Figure 12: Generate Certificate
        $response = $this->actingAs($this->trainerUser)->post("/trainer/enrollments/{$this->enrollmentId}/certificate");
        $response->assertRedirect();

        $enrollment = DB::table('training_enrollments')->where('enrollment_id', $this->enrollmentId)->first();
        $this->assertEquals(1, $enrollment->certificate_issued);
        $this->assertNotNull($enrollment->certificate_no);
        $this->assertStringContainsString('DMDP-CERT-', $enrollment->certificate_no);

        // Verify that skill was granted to jobseeker
        $this->assertDatabaseHas('jobseeker_skills', [
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'skill_name' => 'Web Development & PHP Fundamentals',
        ]);

        // Preview certificate
        $previewResponse = $this->actingAs($this->trainerUser)->get("/trainer/certificates/{$this->enrollmentId}/preview");
        $previewResponse->assertOk();
        $previewResponse->assertSee('Certificate of Completion');
        $previewResponse->assertSee('Maria Santos');
    }

    public function test_trainer_creates_new_training_course_with_topics(): void
    {
        $response = $this->actingAs($this->trainerUser)->post('/trainer/courses', [
            'title' => 'Automotive Electronic Diagnostics NC II',
            'training_type' => 'laboratory_onsite',
            'duration_months' => 3,
            'description' => 'Comprehensive hands-on training on modern electronic control systems and OBD-II scanning.',
            'topics' => [
                ['title' => 'OBD-II Fault Code Diagnosis', 'video_url' => 'https://example.com/video1'],
                ['title' => 'Sensor Calibration & Testing', 'video_url' => 'https://example.com/video2'],
            ],
        ]);

        $response->assertRedirect('/trainer/courses');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('training_programs', [
            'title' => 'Automotive Electronic Diagnostics NC II',
            'training_type' => 'laboratory_onsite',
            'duration_months' => 3,
        ]);

        $newCourse = DB::table('training_programs')->where('title', 'Automotive Electronic Diagnostics NC II')->first();
        $this->assertNotNull($newCourse);

        $this->assertDatabaseHas('training_topics', [
            'training_id' => $newCourse->training_id,
            'title' => 'OBD-II Fault Code Diagnosis',
        ]);
        $this->assertDatabaseHas('training_topics', [
            'training_id' => $newCourse->training_id,
            'title' => 'Sensor Calibration & Testing',
        ]);
    }

    public function test_lmo_dashboard_access_and_funnel(): void
    {
        // Figure 13: LMO Dashboard
        $response = $this->actingAs($this->lmoUser)->get('/lmo/dashboard');
        $response->assertOk();
        $response->assertSee('Labor Market Oversight');
        $response->assertSee('Jobseeker Workflow Supervision Funnel');
        $response->assertSee('Labor Market Skills Supply');
    }

    public function test_lmo_supervises_jobseeker_workflow_directory(): void
    {
        // Figure 13: Supervise Jobseeker Workflow
        $response = $this->actingAs($this->lmoUser)->get('/lmo/jobseekers/supervise');
        $response->assertOk();
        $response->assertSee('Jobseeker Workflow Supervision Registry');
        $response->assertSee('Maria');
        $response->assertSee('Santos');

        // Test filtering by stage
        $filteredResponse = $this->actingAs($this->lmoUser)->get('/lmo/jobseekers/supervise?stage=in_training');
        $filteredResponse->assertOk();
    }

    public function test_lmo_market_insights_analytics(): void
    {
        // Figure 13: Labor Market Analytics
        $response = $this->actingAs($this->lmoUser)->get('/lmo/analytics');
        $response->assertOk();
        $response->assertSee('Market Intelligence');
        $response->assertSee('Top Verified Skills');
    }
}
