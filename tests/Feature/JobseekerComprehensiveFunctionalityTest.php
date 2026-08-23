<?php

namespace Tests\Feature;

use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Jobseeker;
use App\Models\JobseekerDetail;
use App\Models\JobseekerSkill;
use App\Models\Notification;
use App\Models\TrainingProgram;
use App\Models\TrainingTopic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JobseekerComprehensiveFunctionalityTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Jobseeker $jobseeker;
    private int $jobId;
    private int $trainingId;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        // Create user
        $userId = DB::table('users')->insertGetId([
            'email' => 'juan.delacruz@example.com',
            'password' => Hash::make('password123'),
            'role' => 'jobseeker',
            'status' => 'active',
            'is_approved' => 1,
            'created_at' => now(),
        ]);

        $this->user = User::find($userId);

        // Create jobseeker
        $jobseekerId = DB::table('jobseekers')->insertGetId([
            'user_id' => $userId,
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => 'juan.delacruz@example.com',
            'mobile_number' => '09123456789',
            'employment_status' => 'Looking for job',
            'citizenship' => 'Filipino',
        ]);

        $this->jobseeker = Jobseeker::find($jobseekerId);

        // Create jobseeker details
        DB::table('jobseeker_details')->insert([
            'jobseeker_id' => $jobseekerId,
            'address' => json_encode(['city' => 'Cebu City', 'full' => 'Cebu City']),
            'education' => json_encode(['Bachelor of Science in Information Technology']),
            'work_experience' => json_encode(['Junior Web Developer']),
            'training_certificates' => json_encode([]),
        ]);

        // Add initial skills
        DB::table('jobseeker_skills')->insert([
            ['jobseeker_id' => $jobseekerId, 'skill_name' => 'PHP', 'skill_type' => 'technical'],
            ['jobseeker_id' => $jobseekerId, 'skill_name' => 'Laravel', 'skill_type' => 'technical'],
            ['jobseeker_id' => $jobseekerId, 'skill_name' => 'MySQL', 'skill_type' => 'technical'],
        ]);

        // Create employer
        $employerUserId = DB::table('users')->insertGetId([
            'email' => 'employer@techcebu.com',
            'password' => Hash::make('password123'),
            'role' => 'employer',
            'status' => 'active',
            'is_approved' => 1,
            'created_at' => now(),
        ]);

        $employerId = DB::table('employers')->insertGetId([
            'user_id' => $employerUserId,
            'company_name' => 'Cebu Tech Solutions Inc.',
            'is_accredited' => 1,
            'accredited_at' => now(),
        ]);

        // Create job posting
        $this->jobId = DB::table('job_postings')->insertGetId([
            'employer_id' => $employerId,
            'title' => 'Full-Stack PHP/Laravel Developer',
            'description' => 'We are seeking an experienced developer proficient in PHP, Laravel, and MySQL.',
            'qualifications' => "Requirements:\n- PHP\n- Laravel\n- MySQL\n- Git",
            'status' => 'approved',
            'accepts_disability' => 1,
            'disability_type' => 'Hearing impairment friendly',
        ]);

        // Create trainer user & profile
        $trainerUserId = DB::table('users')->insertGetId([
            'email' => 'trainer@dmdp.gov.ph',
            'password' => Hash::make('password123'),
            'role' => 'trainer',
            'status' => 'active',
            'is_approved' => 1,
            'created_at' => now(),
        ]);

        $trainerProfileId = DB::table('user_profiles')->insertGetId([
            'user_id' => $trainerUserId,
            'full_name' => 'Prof. Alex Trainer',
            'position' => 'Senior Instructor',
            'is_trainer_approved' => 1,
        ]);

        // Create training program with topics
        $this->trainingId = DB::table('training_programs')->insertGetId([
            'trainer_id' => $trainerProfileId,
            'title' => 'Web Development with Laravel & SQL',
            'description' => 'Master backend development with modern Laravel practices.',
            'training_type' => 'online',
            'duration_months' => 1,
        ]);

        DB::table('training_topics')->insert([
            [
                'training_id' => $this->trainingId,
                'title' => 'Introduction to MVC Architecture',
                'video_url' => 'https://example.com/video1.mp4',
                'topic_order' => 1,
                'questions' => json_encode([
                    [
                        'question' => 'What is the role of Controller in MVC?',
                        'choices' => ['Handles HTTP requests and orchestrates logic', 'Database table', 'HTML view', 'CSS styling'],
                        'answer' => 0,
                    ]
                ]),
            ],
            [
                'training_id' => $this->trainingId,
                'title' => 'Database Migrations & Eloquent ORM',
                'video_url' => 'https://example.com/video2.mp4',
                'topic_order' => 2,
                'questions' => json_encode([
                    [
                        'question' => 'What command runs database migrations in Laravel?',
                        'choices' => ['php artisan migrate', 'php run migrate', 'composer update', 'npm run build'],
                        'answer' => 0,
                    ]
                ]),
            ],
        ]);
    }

    public function test_dashboard_renders_with_ai_best_match(): void
    {
        $response = $this->actingAs($this->user)->get('/jobseeker/home');
        $response->assertOk();
        $response->assertSee('Full-Stack PHP/Laravel Developer');
        $response->assertSee('Cebu Tech Solutions Inc.');
    }

    public function test_job_explorer_and_search_filtering(): void
    {
        $response = $this->actingAs($this->user)->get('/jobseeker/jobs');
        $response->assertOk();
        $response->assertSee('Full-Stack PHP/Laravel Developer');

        // Search by keyword
        $searchResponse = $this->actingAs($this->user)->get('/jobseeker/jobs?q=Laravel');
        $searchResponse->assertOk();
        $searchResponse->assertSee('Full-Stack PHP/Laravel Developer');

        // PWD filter
        $pwdResponse = $this->actingAs($this->user)->get('/jobseeker/jobs?pwd_only=1');
        $pwdResponse->assertOk();
        $pwdResponse->assertSee('Full-Stack PHP/Laravel Developer');
    }

    public function test_job_details_view(): void
    {
        $response = $this->actingAs($this->user)->get("/jobseeker/jobs/{$this->jobId}");
        $response->assertOk();
        $response->assertSee('Full-Stack PHP/Laravel Developer');
        $response->assertSee('Skill Compatibility Breakdown');
    }

    public function test_job_application_flow_and_duplicate_prevention(): void
    {
        // Apply for job
        $applyResponse = $this->actingAs($this->user)->post("/jobseeker/jobs/{$this->jobId}/apply", [
            'resume' => UploadedFile::fake()->create('resume.pdf', 500, 'application/pdf'),
        ]);

        $applyResponse->assertRedirect('/jobseeker/applications');
        $this->assertDatabaseHas('job_applications', [
            'job_id' => $this->jobId,
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'status' => 'pending',
        ]);

        // Attempt duplicate application
        $duplicateResponse = $this->actingAs($this->user)->post("/jobseeker/jobs/{$this->jobId}/apply");
        $duplicateResponse->assertSessionHas('warning');
    }

    public function test_applications_pipeline_view_and_withdrawal(): void
    {
        // Submit an application
        $app = JobApplication::create([
            'job_id' => $this->jobId,
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'status' => 'pending',
            'referred_by_jpo' => false,
        ]);

        $response = $this->actingAs($this->user)->get('/jobseeker/applications');
        $response->assertOk();
        $response->assertSee('Full-Stack PHP/Laravel Developer');

        // Withdraw application
        $withdrawResponse = $this->actingAs($this->user)->delete("/jobseeker/applications/{$app->application_id}/withdraw");
        $withdrawResponse->assertSessionHas('info');
        $this->assertDatabaseMissing('job_applications', ['application_id' => $app->application_id]);
    }

    public function test_training_catalog_lesson_and_quiz_certification(): void
    {
        // View Catalog
        $catResponse = $this->actingAs($this->user)->get('/jobseeker/training');
        $catResponse->assertOk();
        $catResponse->assertSee('Web Development with Laravel');

        // View Lesson Topics
        $lessonResponse = $this->actingAs($this->user)->get("/jobseeker/training/{$this->trainingId}");
        $lessonResponse->assertOk();
        $lessonResponse->assertSee('Introduction to MVC Architecture');

        // View Quiz Page
        $quizResponse = $this->actingAs($this->user)->get("/jobseeker/training/{$this->trainingId}/quiz");
        $quizResponse->assertOk();
        $quizResponse->assertSee('Skill Certification Assessment');

        // Submit Quiz with Passing Score (>= 80%)
        $submitResponse = $this->actingAs($this->user)->post("/jobseeker/training/{$this->trainingId}/quiz", [
            'score' => 90,
            'passed' => 1,
        ]);
        $submitResponse->assertRedirect("/jobseeker/training/{$this->trainingId}");

        // Verify Skill was Automatically Granted to Jobseeker Profile
        $this->assertDatabaseHas('jobseeker_skills', [
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'skill_name' => 'Web Development with Laravel & SQL',
        ]);
    }

    public function test_document_hub_upload_and_delete(): void
    {
        $response = $this->actingAs($this->user)->get('/jobseeker/documents');
        $response->assertOk();
        $response->assertSee('Document Hub');

        // Upload a document
        $uploadResponse = $this->actingAs($this->user)->post('/jobseeker/documents/upload', [
            'category' => 'resume',
            'document_file' => UploadedFile::fake()->create('juan_resume.pdf', 300, 'application/pdf'),
        ]);
        $uploadResponse->assertRedirect('/jobseeker/documents');

        // Delete document
        $deleteResponse = $this->actingAs($this->user)->delete('/jobseeker/documents/resume');
        $deleteResponse->assertRedirect('/jobseeker/documents');
    }

    public function test_notifications_center_and_mark_read(): void
    {
        $notif = Notification::create([
            'user_id' => $this->user->user_id,
            'title' => 'Interview Scheduled',
            'message' => 'Your interview has been scheduled with Cebu Tech Solutions.',
            'type' => 'interview',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->user)->get('/jobseeker/notifications');
        $response->assertOk();
        $response->assertSee('Interview Scheduled');

        // Mark single notification read
        $readResponse = $this->actingAs($this->user)->post("/jobseeker/notifications/{$notif->notification_id}/read");
        $readResponse->assertSessionHas('success');
        $this->assertTrue((bool) Notification::find($notif->notification_id)->is_read);

        // Mark all read
        $readAllResponse = $this->actingAs($this->user)->post('/jobseeker/notifications/read-all');
        $readAllResponse->assertSessionHas('success');
    }

    public function test_profile_and_skills_matrix_sync(): void
    {
        $response = $this->actingAs($this->user)->get('/jobseeker/profile');
        $response->assertOk();
        $response->assertSee('Skills Profile');

        // Sync Skills Matrix
        $syncResponse = $this->actingAs($this->user)->post('/jobseeker/profile/skills/sync', [
            'skills' => ['PHP', 'Laravel', 'React', 'Docker', 'AWS'],
        ]);
        $syncResponse->assertRedirect('/jobseeker/profile');

        $this->assertDatabaseHas('jobseeker_skills', [
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'skill_name' => 'Docker',
        ]);
        $this->assertDatabaseHas('jobseeker_skills', [
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'skill_name' => 'React',
        ]);

        // Add single skill
        $addResponse = $this->actingAs($this->user)->post('/jobseeker/profile/skills', [
            'skill_name' => 'Python',
        ]);
        $addResponse->assertRedirect('/jobseeker/profile');
        $this->assertDatabaseHas('jobseeker_skills', [
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'skill_name' => 'Python',
        ]);

        // Update Personal Info
        $updateInfoResponse = $this->actingAs($this->user)->post('/jobseeker/profile/update', [
            'first_name' => 'Juan Carlos',
            'last_name' => 'Dela Cruz',
            'mobile_number' => '09998887766',
            'address' => 'Banilad, Cebu City',
            'bio' => 'Passionate software engineer.',
        ]);
        $updateInfoResponse->assertRedirect('/jobseeker/profile');

        $this->assertDatabaseHas('jobseekers', [
            'jobseeker_id' => $this->jobseeker->jobseeker_id,
            'first_name' => 'Juan Carlos',
            'mobile_number' => '09998887766',
        ]);
    }

    public function test_training_quiz_guarantees_minimum_five_questions_and_proper_options(): void
    {
        $quizResponse = $this->actingAs($this->user)->get("/jobseeker/training/{$this->trainingId}/quiz");
        $quizResponse->assertOk();

        // Verify view data contains at least 5 questions
        $questions = $quizResponse->viewData('questions');
        $this->assertIsArray($questions);
        $this->assertGreaterThanOrEqual(5, count($questions));

        // Verify every question has question text, choices, and numeric answer
        foreach ($questions as $q) {
            $this->assertArrayHasKey('question', $q);
            $this->assertNotEmpty($q['question']);
            $this->assertArrayHasKey('choices', $q);
            $this->assertGreaterThanOrEqual(2, count($q['choices']));
            $this->assertArrayHasKey('answer', $q);
        }
    }
}
