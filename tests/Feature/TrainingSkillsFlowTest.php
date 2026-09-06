<?php

namespace Tests\Feature;

use App\Models\Jobseeker;
use App\Models\TrainingEnrollment;
use App\Models\TrainingProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TrainingSkillsFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $jobseekerUser;
    protected Jobseeker $jobseeker;
    protected User $trainerUser;
    protected TrainingProgram $trainingCourse;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Jobseeker user and profile
        $this->jobseekerUser = User::create([
            'email' => 'skills_jobseeker_' . uniqid() . '@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'jobseeker',
            'status' => 'active',
            'is_approved' => 1,
        ]);

        $this->jobseeker = Jobseeker::create([
            'user_id' => $this->jobseekerUser->user_id,
            'first_name' => 'Carlos',
            'last_name' => 'Mendoza',
            'email' => $this->jobseekerUser->email,
            'mobile_number' => '09123456789',
        ]);

        DB::table('jobseeker_details')->insert([
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'education' => json_encode(['level' => 'Vocational Diploma']),
            'training_certificates' => json_encode([]),
        ]);

        DB::table('jobseeker_skills')->insert([
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'skill_name' => 'Basic Computer Literacy',
            'skill_type' => 'technical',
        ]);

        // 2. Create Trainer user and profile
        $this->trainerUser = User::create([
            'email' => 'lead_trainer_' . uniqid() . '@dmdp.gov.ph',
            'password' => Hash::make('TrainerPass123!'),
            'role' => 'trainer',
            'status' => 'active',
            'is_approved' => 1,
        ]);

        $trainerProfileId = DB::table('user_profiles')->insertGetId([
            'user_id' => $this->trainerUser->user_id,
            'full_name' => 'Master Instructor Ronald Cruz',
            'phone' => '09223344556',
            'position' => 'Senior Technical Instructor',
            'office' => 'DMDP Skills Training Center',
            'specialization' => 'Automotive & Mechatronics',
            'is_trainer_approved' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Create Training Program with explicit skills & assessment passing score
        $this->trainingCourse = TrainingProgram::create([
            'trainer_id' => $trainerProfileId,
            'title' => 'Mechatronics Automation & PLC Programming',
            'training_type' => 'laboratory_onsite',
            'duration_months' => 4,
            'description' => 'Industrial automation, programmable logic controllers, and sensor interfacing.',
            'skills' => 'PLC Programming, Sensor Calibration, Pneumatics Control',
            'passing_score' => 80,
            'auto_generate_certificate' => 0,
        ]);

        // Add curriculum topics
        DB::table('training_topics')->insert([
            [
                'training_id' => $this->trainingCourse->training_id,
                'title' => 'Introduction to PLC Ladder Logic',
                'video_url' => 'https://example.com/plc1',
                'topic_order' => 1,
            ],
            [
                'training_id' => $this->trainingCourse->training_id,
                'title' => 'Pneumatic Actuators and Solenoids',
                'video_url' => 'https://example.com/plc2',
                'topic_order' => 2,
            ],
        ]);

        // Add trainer-authored assessment question
        DB::table('training_assessments')->insert([
            'training_id' => $this->trainingCourse->training_id,
            'question' => 'Which PLC instruction represents a normally open contact?',
            'question_type' => 'multiple_choice',
            'options' => json_encode([
                'XIC (Examine If Closed)',
                'XIO (Examine If Open)',
                'OTE (Output Energize)',
                'TON (Timer On Delay)',
            ]),
            'correct_answer' => 0,
            'points' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Job Seeker Flow - Step 1: View Training Skills
     */
    public function test_jobseeker_views_training_skills_catalog(): void
    {
        $response = $this->actingAs($this->jobseekerUser)->get(route('jobseeker.training.skills'));

        $response->assertStatus(200);
        $response->assertSee('Vocational Training Skills');
        $response->assertSee('PLC Programming');
        $response->assertSee('Mechatronics Automation & PLC Programming');
        $response->assertSee('Enroll in Training');
    }

    /**
     * Job Seeker Flow - Step 2: Enroll in Training
     */
    public function test_jobseeker_enrolls_in_training_from_skills_catalog(): void
    {
        $response = $this->actingAs($this->jobseekerUser)
            ->post(route('jobseeker.training.enroll', $this->trainingCourse->training_id));

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('jobseeker.training.show', $this->trainingCourse->training_id));

        // Verify enrollment record in database
        $this->assertDatabaseHas('training_enrollments', [
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'training_id' => $this->trainingCourse->training_id,
            'status' => 'enrolled',
        ]);
    }

    /**
     * Job Seeker Flow - Step 3: Training Enrollment Dashboard
     */
    public function test_jobseeker_views_training_enrollment_dashboard(): void
    {
        // Create an enrollment
        $enrollment = TrainingEnrollment::create([
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'training_id' => $this->trainingCourse->training_id,
            'training_type' => 'laboratory_onsite',
            'status' => 'enrolled',
            'start_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->jobseekerUser)->get(route('jobseeker.training.enrollments'));

        $response->assertStatus(200);
        $response->assertSee('My Training Enrollments');
        $response->assertSee('Mechatronics Automation & PLC Programming');
        $response->assertSee('1. Enrolled');
    }

    /**
     * Trainer Flow - Step 1: View Enrolled Job Seekers
     */
    public function test_trainer_views_enrolled_jobseekers(): void
    {
        TrainingEnrollment::create([
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'training_id' => $this->trainingCourse->training_id,
            'training_type' => 'laboratory_onsite',
            'status' => 'enrolled',
            'start_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->trainerUser)->get(route('trainer.enrollments.index'));

        $response->assertStatus(200);
        $response->assertSee('Enrolled Job Seekers');
        $response->assertSee('Carlos Mendoza');
        $response->assertSee('Mechatronics Automation & PLC Programming');
        $response->assertSee('Assess');
    }

    /**
     * Trainer Flow - Step 2: Update Enrollment Status
     */
    public function test_trainer_updates_enrollment_status(): void
    {
        $enrollment = TrainingEnrollment::create([
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'training_id' => $this->trainingCourse->training_id,
            'training_type' => 'laboratory_onsite',
            'status' => 'enrolled',
            'start_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->trainerUser)
            ->post(route('trainer.enrollments.status', $enrollment->enrollment_id), [
                'status' => 'in_progress',
                'lab_remarks' => 'Completed Module 1 wiring and pneumatic bench tests successfully.',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $enrollment->refresh();
        $this->assertEquals('in_progress', $enrollment->status);
        $this->assertEquals('Completed Module 1 wiring and pneumatic bench tests successfully.', $enrollment->lab_remarks);
    }

    /**
     * Trainer Flow - Step 3: Conduct Assessment
     */
    public function test_trainer_conducts_assessment(): void
    {
        $enrollment = TrainingEnrollment::create([
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'training_id' => $this->trainingCourse->training_id,
            'training_type' => 'laboratory_onsite',
            'status' => 'in_progress',
            'start_date' => now()->toDateString(),
        ]);

        // 1. Trainer views conduct assessment view
        $viewResponse = $this->actingAs($this->trainerUser)
            ->get(route('trainer.enrollments.assessment', $enrollment->enrollment_id));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Conduct Learner Assessment');
        $viewResponse->assertSee('Carlos Mendoza');
        $viewResponse->assertSee('Which PLC instruction represents a normally open contact?');

        // 2. Trainer submits conducted assessment
        $submitResponse = $this->actingAs($this->trainerUser)
            ->post(route('trainer.enrollments.assessment.submit', $enrollment->enrollment_id), [
                'theory_score' => 90,
                'practical_score' => 95,
                'score' => 93,
                'trainer_feedback' => 'Demonstrated high competence in PLC ladder wiring and safety precautions.',
                'lab_remarks' => 'Full workshop attendance verified.',
            ]);

        $submitResponse->assertSessionHasNoErrors();
        $submitResponse->assertRedirect(route('trainer.enrollments.index'));

        $enrollment->refresh();
        $this->assertEquals(93, (int) $enrollment->score);
        $this->assertEquals(1, $enrollment->passed);
        $this->assertEquals('completed', $enrollment->status);
        $this->assertNotNull($enrollment->end_date);
    }

    /**
     * Trainer Flow - Step 4: Mark Completion & Award Skills
     */
    public function test_trainer_marks_completion_and_awards_skill(): void
    {
        $enrollment = TrainingEnrollment::create([
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'training_id' => $this->trainingCourse->training_id,
            'training_type' => 'laboratory_onsite',
            'status' => 'in_progress',
            'start_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->trainerUser)
            ->post(route('trainer.enrollments.complete', $enrollment->enrollment_id));

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('trainer.enrollments.index'));

        $enrollment->refresh();
        $this->assertEquals('completed', $enrollment->status);
        $this->assertEquals(1, $enrollment->passed);

        // Verify verified skill was awarded to jobseeker
        $this->assertDatabaseHas('jobseeker_skills', [
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'skill_name' => 'Mechatronics Automation & PLC Programming',
        ]);
    }

    /**
     * Trainer Flow - Step 5: Generate Skills Certificate
     */
    public function test_trainer_generates_skills_certificate(): void
    {
        $enrollment = TrainingEnrollment::create([
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'training_id' => $this->trainingCourse->training_id,
            'training_type' => 'laboratory_onsite',
            'status' => 'completed',
            'passed' => 1,
            'score' => 95,
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->trainerUser)
            ->post(route('trainer.enrollments.certificate', $enrollment->enrollment_id));

        $response->assertSessionHasNoErrors();

        $enrollment->refresh();
        $this->assertEquals(1, $enrollment->certificate_issued);
        $this->assertNotNull($enrollment->certificate_no);
        $this->assertStringStartsWith('DMDP-CERT-', $enrollment->certificate_no);

        // Verify certificate is registered in jobseeker Document Hub
        $details = DB::table('jobseeker_details')->where('jobseeker_id', $this->jobseeker->jobseeker_id)->first();
        $this->assertNotNull($details);
        $certs = json_decode($details->training_certificates, true);
        $this->assertNotEmpty($certs);
        $this->assertEquals($enrollment->certificate_no, $certs[0]['certificate_no']);
    }

    /**
     * End-to-End: Jobseeker previews generated certificate
     */
    public function test_jobseeker_views_generated_skills_certificate(): void
    {
        $certNo = 'DMDP-CERT-2026-TESTPLC';
        $enrollment = TrainingEnrollment::create([
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'training_id' => $this->trainingCourse->training_id,
            'training_type' => 'laboratory_onsite',
            'status' => 'completed',
            'passed' => 1,
            'score' => 95,
            'certificate_issued' => 1,
            'certificate_no' => $certNo,
            'certificate_issued_at' => now(),
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->jobseekerUser)
            ->get(route('jobseeker.certificates.preview', $enrollment->enrollment_id));

        $response->assertStatus(200);
        $response->assertSee('Certificate of Completion');
        $response->assertSee('Carlos Mendoza');
        $response->assertSee($certNo);
    }
}
