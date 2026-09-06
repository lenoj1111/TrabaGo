<?php

namespace Tests\Feature;

use App\Models\Jobseeker;
use App\Models\JobseekerDetail;
use App\Models\TrainingAssessment;
use App\Models\TrainingEnrollment;
use App\Models\TrainingProgram;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\TrainingQuizService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TrainerPortalComprehensiveTest extends TestCase
{
    use RefreshDatabase;

    protected User $trainerUser;
    protected UserProfile $trainerProfile;
    protected User $adminUser;
    protected User $jobseekerUser;
    protected Jobseeker $jobseeker;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Trainer User
        $this->trainerUser = User::create([
            'email' => 'trainer_test_' . uniqid() . '@example.com',
            'password' => Hash::make('TrainerPass123!'),
            'role' => 'trainer',
            'status' => 'active',
            'is_approved' => 1,
        ]);

        $this->trainerProfile = UserProfile::create([
            'user_id' => $this->trainerUser->user_id,
            'full_name' => 'Engr. Test Trainer',
            'position' => 'Senior Technical Trainer',
            'office' => 'DMDP Central Testing Center',
            'specialization' => 'Electrical Installation & Maintenance',
            'trainer_type' => 'dmdp',
            'is_trainer_approved' => 1,
        ]);

        // 2. Create Admin User
        $this->adminUser = User::create([
            'email' => 'admin_test_' . uniqid() . '@example.com',
            'password' => Hash::make('AdminPass123!'),
            'role' => 'admin',
            'status' => 'active',
            'is_approved' => 1,
        ]);

        // 3. Create Jobseeker User
        $this->jobseekerUser = User::create([
            'email' => 'jobseeker_test_' . uniqid() . '@example.com',
            'password' => Hash::make('SeekerPass123!'),
            'role' => 'jobseeker',
            'status' => 'active',
            'is_approved' => 1,
        ]);

        $this->jobseeker = Jobseeker::create([
            'user_id' => $this->jobseekerUser->user_id,
            'first_name' => 'Juan',
            'last_name' => 'Trainee',
            'email' => $this->jobseekerUser->email,
            'mobile_number' => '09171234567',
            'employment_status' => 'wage_employed',
        ]);
    }

    /**
     * Test 1: Full CRUD for training courses.
     */
    public function test_trainer_can_perform_full_crud_on_training_courses()
    {
        $this->actingAs($this->trainerUser);

        // 1. Create Course
        $createResponse = $this->post(route('trainer.courses.store'), [
            'title' => 'Automotive Servicing NC-II',
            'training_type' => 'laboratory_onsite',
            'duration_months' => 3,
            'description' => 'Comprehensive automotive diagnostics, maintenance, and workshop safety protocols.',
            'skills' => 'Engine Overhaul, Brake Servicing, Electrical Troubleshooting',
            'passing_score' => 85,
            'auto_generate_certificate' => 1,
            'topics' => [
                ['title' => 'Module 1: Engine Diagnostics', 'video_url' => 'https://example.com/engine'],
                ['title' => 'Module 2: Brake Systems', 'video_url' => 'https://example.com/brake'],
            ],
        ]);

        $createResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('training_programs', [
            'title' => 'Automotive Servicing NC-II',
            'training_type' => 'laboratory_onsite',
            'duration_months' => 3,
            'passing_score' => 85,
            'auto_generate_certificate' => 1,
        ]);

        $course = TrainingProgram::where('title', 'Automotive Servicing NC-II')->first();
        $this->assertNotNull($course);
        $createResponse->assertRedirect(route('trainer.courses'));

        // Verify topics created without fake auto questions
        $topics = DB::table('training_topics')->where('training_id', $course->training_id)->get();
        $this->assertCount(2, $topics);
        $this->assertNull($topics->first()->questions);

        // 2. Read Course List & Details
        $indexResponse = $this->get(route('trainer.courses'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Automotive Servicing NC-II');
        $indexResponse->assertSee('Engine Overhaul');

        $showResponse = $this->get(route('trainer.courses.show', $course->training_id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Module 1: Engine Diagnostics');
        $showResponse->assertSee('85%');

        // 3. Update Course
        $updateResponse = $this->put(route('trainer.courses.update', $course->training_id), [
            'title' => 'Automotive Servicing & Diagnostics NC-II',
            'training_type' => 'laboratory_onsite',
            'duration_months' => 4,
            'description' => 'Updated syllabus including electronic fuel injection systems.',
            'skills' => 'Engine Overhaul, EFI Diagnostics, Brake Servicing',
            'passing_score' => 80,
            'auto_generate_certificate' => 0,
        ]);

        $updateResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('training_programs', [
            'training_id' => $course->training_id,
            'title' => 'Automotive Servicing & Diagnostics NC-II',
            'duration_months' => 4,
            'auto_generate_certificate' => 0,
            'passing_score' => 80,
        ]);

        // 4. Delete Course
        $deleteResponse = $this->delete(route('trainer.courses.destroy', $course->training_id));
        $deleteResponse->assertRedirect(route('trainer.courses'));
        $this->assertDatabaseMissing('training_programs', [
            'training_id' => $course->training_id,
        ]);
    }

    /**
     * Test 2: Trainer creates assessments manually; NOT built-in or automatically generated.
     */
    public function test_trainer_creates_assessment_manually_and_no_built_in_fallback_exists()
    {
        $this->actingAs($this->trainerUser);

        // Create Course
        $course = TrainingProgram::create([
            'trainer_id' => $this->trainerProfile->profile_id,
            'title' => 'Solar PV Installation NC-II',
            'training_type' => 'online',
            'duration_months' => 2,
            'description' => 'Renewable energy solar panel installation standards.',
            'passing_score' => 80,
            'auto_generate_certificate' => 1,
        ]);

        $quizService = new TrainingQuizService();

        // Initially: No questions should exist; should NOT return built-in questions
        $initialQuestions = $quizService->getQuestionsForTraining($course);
        $this->assertEmpty($initialQuestions, 'Assessments should not be built-in or automatically generated.');

        // Trainer manually creates Question 1
        $q1Response = $this->post(route('trainer.courses.assessments.store', $course->training_id), [
            'question' => 'What safety instrument must be verified before connecting solar array DC wiring?',
            'question_type' => 'multiple_choice',
            'options' => [
                'True-RMS Digital Multimeter and Lockout/Tagout verification',
                'Visual check without metering',
                'Bare-hand wire gauge touch test',
                'No verification needed for DC circuits'
            ],
            'correct_answer' => 0,
            'explanation' => 'Lockout/tagout and True-RMS metering prevent electric shock and short-circuit arc flash.',
            'points' => 2,
        ]);

        $q1Response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('training_assessments', [
            'training_id' => $course->training_id,
            'question' => 'What safety instrument must be verified before connecting solar array DC wiring?',
            'correct_answer' => 0,
            'points' => 2,
        ]);

        // Trainer manually creates Question 2
        $this->post(route('trainer.courses.assessments.store', $course->training_id), [
            'question' => 'Which angle of solar panel tilt is optimal for Cebu City latitude (approx 10° North)?',
            'question_type' => 'multiple_choice',
            'options' => [
                'Approximately 10° to 15° facing South',
                'Vertical 90° facing West',
                'Flat 0° horizontal',
                '45° facing North'
            ],
            'correct_answer' => 0,
            'explanation' => 'Tilt matching local latitude maximizes annual solar irradiance capture in equatorial regions.',
            'points' => 1,
        ]);

        // Verify quizService strictly returns the 2 trainer-created questions
        $retrievedQuestions = $quizService->getQuestionsForTraining($course);
        $this->assertCount(2, $retrievedQuestions);
        $this->assertEquals('What safety instrument must be verified before connecting solar array DC wiring?', $retrievedQuestions[0]['question']);
        $this->assertEquals(0, $retrievedQuestions[0]['answer']);
        $this->assertEquals('Which angle of solar panel tilt is optimal for Cebu City latitude (approx 10° North)?', $retrievedQuestions[1]['question']);

        // Trainer updates Question 2
        $q2 = TrainingAssessment::where('training_id', $course->training_id)->orderBy('assessment_id', 'desc')->first();
        $updateQResponse = $this->put(route('trainer.courses.assessments.update', [$course->training_id, $q2->assessment_id]), [
            'question' => 'Which angle of solar panel tilt is optimal for Cebu City latitude (approx 10° North)? (Updated)',
            'options' => [
                'Approximately 10° to 15° facing South (Correct)',
                'Vertical 90° facing West',
                'Flat 0° horizontal',
                '45° facing North'
            ],
            'correct_answer' => 0,
            'explanation' => 'Updated rationale for solar latitude angles.',
            'points' => 3,
        ]);

        $updateQResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('training_assessments', [
            'assessment_id' => $q2->assessment_id,
            'question' => 'Which angle of solar panel tilt is optimal for Cebu City latitude (approx 10° North)? (Updated)',
            'points' => 3,
        ]);

        // Trainer deletes Question 1
        $q1 = TrainingAssessment::where('training_id', $course->training_id)->orderBy('assessment_id', 'asc')->first();
        $this->delete(route('trainer.courses.assessments.destroy', [$course->training_id, $q1->assessment_id]));
        $this->assertDatabaseMissing('training_assessments', ['assessment_id' => $q1->assessment_id]);

        $finalQuestions = $quizService->getQuestionsForTraining($course);
        $this->assertCount(1, $finalQuestions);
    }

    /**
     * Test 3: If job seeker completes training course, certificate is automatically generated when auto_generate_certificate = true.
     */
    public function test_certificate_is_automatically_generated_when_course_option_is_enabled()
    {
        $this->actingAs($this->jobseekerUser);

        // Create course with auto_generate_certificate = 1
        $course = TrainingProgram::create([
            'trainer_id' => $this->trainerProfile->profile_id,
            'title' => 'Web Development with Laravel NC-III',
            'training_type' => 'online',
            'duration_months' => 2,
            'description' => 'Modern PHP and Laravel full-stack web applications.',
            'passing_score' => 80,
            'auto_generate_certificate' => 1,
        ]);

        // Submit quiz with passing score
        $response = $this->post(route('jobseeker.training.quiz.submit', $course->training_id), [
            'score' => 95,
        ]);

        $response->assertSessionHasNoErrors();

        // Verify enrollment is marked completed and certificate is auto-generated
        $enrollment = TrainingEnrollment::where('jobseeker_id', $this->jobseeker->jobseeker_id)
            ->where('training_id', $course->training_id)
            ->first();

        $this->assertNotNull($enrollment);
        $this->assertEquals('completed', $enrollment->status);
        $this->assertEquals(1, $enrollment->passed);
        $this->assertEquals(1, $enrollment->certificate_issued);
        $this->assertNotNull($enrollment->certificate_no);
        $this->assertStringStartsWith('DMDP-CERT-', $enrollment->certificate_no);
        $this->assertNotNull($enrollment->certificate_issued_at);

        // Verify certificate is stored in jobseeker Document Hub
        $detail = JobseekerDetail::where('jobseeker_id', $this->jobseeker->jobseeker_id)->first();
        $this->assertNotNull($detail);
        $this->assertStringContainsString($enrollment->certificate_no, $detail->training_certificates);
    }

    /**
     * Test 4: Certificate is NOT automatically generated when trainer chooses auto_generate_certificate = false.
     */
    public function test_certificate_is_not_automatically_generated_when_trainer_chooses_manual_assessment()
    {
        $this->actingAs($this->jobseekerUser);

        // Create course with auto_generate_certificate = 0 (Requires trainer manual assessment)
        $course = TrainingProgram::create([
            'trainer_id' => $this->trainerProfile->profile_id,
            'title' => 'Heavy Equipment Operation (Excavator NC-II)',
            'training_type' => 'laboratory_onsite',
            'duration_months' => 3,
            'description' => 'Rigorous hydraulic excavator operation requiring trainer practical grade.',
            'passing_score' => 80,
            'auto_generate_certificate' => 0, // Disabled!
        ]);

        // Jobseeker submits assessment score
        $response = $this->post(route('jobseeker.training.quiz.submit', $course->training_id), [
            'score' => 90,
        ]);

        $response->assertSessionHasNoErrors();

        $enrollment = TrainingEnrollment::where('jobseeker_id', $this->jobseeker->jobseeker_id)
            ->where('training_id', $course->training_id)
            ->first();

        $this->assertNotNull($enrollment);
        $this->assertEquals('completed', $enrollment->status);
        $this->assertEquals(0, $enrollment->certificate_issued, 'Certificate should NOT be issued automatically.');
        $this->assertNull($enrollment->certificate_no);

        // Now Trainer reviews and manually issues the certificate
        $this->actingAs($this->trainerUser);

        $certResponse = $this->post(route('trainer.enrollments.certificate', $enrollment->enrollment_id));
        $certResponse->assertSessionHasNoErrors();

        $enrollment->refresh();
        $this->assertEquals(1, $enrollment->certificate_issued);
        $this->assertNotNull($enrollment->certificate_no);
        $this->assertStringStartsWith('DMDP-CERT-', $enrollment->certificate_no);
    }

    /**
     * Test 5: Collaborator trainer account created by trainer requires admin approval and cannot log in until approved.
     */
    public function test_collaborator_created_by_trainer_requires_admin_approval_and_admin_approves()
    {
        $this->actingAs($this->trainerUser);

        $collaboratorEmail = 'collab_trainer_' . uniqid() . '@partner-inst.edu.ph';

        // Trainer registers collaborator
        $response = $this->post(route('trainer.collaborators.store'), [
            'full_name' => 'Prof. Alex Rivera',
            'email' => $collaboratorEmail,
            'password' => 'CollabPass123!',
            'password_confirmation' => 'CollabPass123!',
            'phone' => '09187654321',
            'office' => 'Mechatronics Center',
            'specialization' => 'PLC Robotics',
            'partner_institution' => 'Cebu Institute of Technology',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('trainer.collaborators'));

        // Verify collaborator user is created with status = inactive and is_approved = 0 (pending admin approval)
        $collabUser = User::where('email', $collaboratorEmail)->first();
        $this->assertNotNull($collabUser);
        $this->assertEquals('trainer', $collabUser->role);
        $this->assertEquals('inactive', $collabUser->status);
        $this->assertEquals(0, $collabUser->is_approved);

        // Collaborator attempts to log in before admin approval -> should fail
        $this->post('/logout');
        $loginResponse = $this->post('/login', [
            'email' => $collaboratorEmail,
            'password' => 'CollabPass123!',
        ]);

        $loginResponse->assertSessionHasErrors('email');
        $this->assertGuest();

        // Admin approves the collaborator trainer account
        $this->actingAs($this->adminUser);

        $approveResponse = $this->post(route('admin.approvals.trainers.approve', $collabUser->user_id));
        $approveResponse->assertSessionHasNoErrors();

        $collabUser->refresh();
        $this->assertEquals('active', $collabUser->status);
        $this->assertEquals(1, $collabUser->is_approved);

        // Collaborator logs in after approval -> should succeed
        $this->post('/logout');
        $loginSuccessResponse = $this->post('/login', [
            'email' => $collaboratorEmail,
            'password' => 'CollabPass123!',
        ]);

        $loginSuccessResponse->assertRedirect('/trainer/dashboard');
        $this->assertAuthenticatedAs($collabUser);
    }

    /**
     * Test 6: Trainer account created by Admin is automatically approved.
     */
    public function test_trainer_account_created_by_admin_is_automatically_approved()
    {
        $this->actingAs($this->adminUser);

        $adminCreatedEmail = 'admin_trainer_' . uniqid() . '@dmdp.gov.ph';

        $response = $this->post(route('admin.users.store'), [
            'email' => $adminCreatedEmail,
            'password' => 'DirectPass123!',
            'password_confirmation' => 'DirectPass123!',
            'role' => 'trainer',
            'full_name' => 'Engr. Direct Approved Trainer',
            'position' => 'Senior Technical Instructor',
            'department' => 'Skills Training Division',
            'office' => 'DMDP City Center',
            'phone' => '09228889999',
            'specialization' => 'Electronics NC-II',
            'trainer_type' => 'dmdp',
        ]);

        $response->assertSessionHasNoErrors();

        // Verify account is automatically approved
        $newUser = User::where('email', $adminCreatedEmail)->first();
        $this->assertNotNull($newUser);
        $this->assertEquals('trainer', $newUser->role);
        $this->assertEquals('active', $newUser->status);
        $this->assertEquals(1, $newUser->is_approved);

        // Can log in directly without any further approvals
        $this->post('/logout');
        $loginResponse = $this->post('/login', [
            'email' => $adminCreatedEmail,
            'password' => 'DirectPass123!',
        ]);

        $loginResponse->assertRedirect('/trainer/dashboard');
        $this->assertAuthenticatedAs($newUser);
    }

    /**
     * Test 7: Profile, Update Profile, and Reset Password.
     */
    public function test_trainer_profile_update_and_password_reset()
    {
        $this->actingAs($this->trainerUser);

        // 1. View Profile
        $profileResponse = $this->get(route('trainer.profile'));
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee($this->trainerProfile->full_name);

        // 2. Update Profile
        $updateResponse = $this->post(route('trainer.profile.update'), [
            'full_name' => 'Engr. Updated Trainer Name',
            'phone' => '09179998877',
            'office' => 'DMDP Modern Tech Training Complex',
            'specialization' => 'Advanced Robotics and IoT',
            'bio' => '15 years industry experience in mechatronics and certified TESDA NTTC instructor.',
        ]);

        $updateResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $this->trainerUser->user_id,
            'full_name' => 'Engr. Updated Trainer Name',
            'phone' => '09179998877',
            'specialization' => 'Advanced Robotics and IoT',
        ]);

        // 3. Reset Password - Wrong current password fails
        $wrongPassResponse = $this->post(route('trainer.password.update'), [
            'current_password' => 'WrongPassword123',
            'password' => 'NewTrainerPass123!',
            'password_confirmation' => 'NewTrainerPass123!',
        ]);
        $wrongPassResponse->assertSessionHasErrors('current_password');

        // 4. Reset Password - Correct current password succeeds
        $resetResponse = $this->post(route('trainer.password.update'), [
            'current_password' => 'TrainerPass123!',
            'password' => 'NewTrainerPass123!',
            'password_confirmation' => 'NewTrainerPass123!',
        ]);

        $resetResponse->assertSessionHasNoErrors();

        // Verify password hash updated
        $this->trainerUser->refresh();
        $this->assertTrue(Hash::check('NewTrainerPass123!', $this->trainerUser->password));
    }

    /**
     * Test 8: Training Skills Enrollment.
     */
    public function test_training_skills_enrollment()
    {
        $this->actingAs($this->trainerUser);

        $course = TrainingProgram::create([
            'trainer_id' => $this->trainerProfile->profile_id,
            'title' => 'Machining NC-II (Lathe & Milling)',
            'training_type' => 'laboratory_onsite',
            'duration_months' => 3,
            'description' => 'Precision metal fabrication and lathe turning.',
            'skills' => 'Lathe Operation, Milling Machine Setup, Precision Micrometer Measurement',
            'passing_score' => 80,
            'auto_generate_certificate' => 1,
        ]);

        // 1. View skills enrollment page
        $pageResponse = $this->get(route('trainer.skills.enrollment'));
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Training Skills Enrollment');
        $pageResponse->assertSee($this->jobseeker->first_name);

        // 2. Enroll jobseeker targeting specific skills
        $enrollResponse = $this->post(route('trainer.skills.enrollment.store'), [
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'training_id' => $course->training_id,
            'enrolled_skills' => 'Lathe Operation, Precision Measurement',
        ]);

        $enrollResponse->assertSessionHasNoErrors();

        $this->assertDatabaseHas('training_enrollments', [
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'training_id' => $course->training_id,
            'enrolled_skills' => 'Lathe Operation, Precision Measurement',
            'status' => 'enrolled',
        ]);
    }
}
