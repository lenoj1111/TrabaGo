<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\TrainingEnrollment;
use App\Models\TrainingProgram;
use App\Services\TrainingQuizService;
use App\Traits\ResolvesJobseeker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainingController extends Controller
{
    use ResolvesJobseeker;

    protected TrainingQuizService $quizService;

    public function __construct(TrainingQuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    /**
     * Format a training program model for API responses.
     */
    private function formatProgram(TrainingProgram $program, ?int $jobseekerId = null): array
    {
        $programQuestions = array_map(static fn ($question) => [
            'id' => $question['id'],
            'question' => $question['question'],
            'question_type' => $question['type'],
            'choices' => $question['choices'],
            'options' => $question['choices'],
            'points' => $question['points'],
        ], $this->quizService->getQuestionsForTraining($program));

        $topics = $program->topics->map(function ($topic) {
            $questions = is_array($topic->questions) ? $topic->questions : json_decode($topic->questions ?? '[]', true);
            $formattedTopicQuestions = [];
            if (!empty($questions)) {
                foreach ($questions as $q) {
                    $choices = $q['choices'] ?? ($q['options'] ?? []);
                    $formattedTopicQuestions[] = [
                        'question' => $q['question'] ?? '',
                        'choices' => array_values($choices),
                        'options' => array_values($choices),
                        'answer' => (int)($q['answer'] ?? 0),
                    ];
                }
            }

            return [
                'id' => $topic->topic_id,
                'topic_id' => $topic->topic_id,
                'title' => $topic->title,
                'videoUrl' => $topic->video_url,
                'video_url' => $topic->video_url,
                'order' => $topic->topic_order,
                'questions' => $formattedTopicQuestions,
            ];
        })->toArray();

        $enrollment = null;
        $isEnrolled = false;
        $isCompleted = false;
        $certificateIssued = false;
        $certificateNo = null;
        $score = null;
        $reviewStatus = null;

        if ($jobseekerId) {
            $enrollment = TrainingEnrollment::where('jobseeker_id', $jobseekerId)
                ->where('training_id', $program->training_id)
                ->first();

            if ($enrollment) {
                $isEnrolled = true;
                $isCompleted = in_array($enrollment->status, ['completed', 'passed']) || (bool)$enrollment->passed || (bool)$enrollment->certificate_issued;
                $certificateIssued = (bool)$enrollment->certificate_issued;
                $certificateNo = $enrollment->certificate_no;
                $score = $enrollment->score;
                $answers = is_array($enrollment->answers)
                    ? $enrollment->answers
                    : (json_decode($enrollment->answers ?? '[]', true) ?: []);
                $reviewStatus = $answers['review_status'] ?? null;
            }
        }

        return [
            'id' => $program->training_id,
            'training_id' => $program->training_id,
            'title' => $program->title,
            'trainingType' => $program->training_type ?? 'online',
            'training_type' => $program->training_type ?? 'online',
            'durationMonths' => $program->duration_months ?? 1,
            'duration_months' => $program->duration_months ?? 1,
            'duration' => ($program->duration_months ?? 1) . ' month(s)',
            'description' => $program->description ?? '',
            'topics' => $topics,
            'modulesCount' => count($topics),
            'questions' => $programQuestions,
            'questionsCount' => count($programQuestions),
            'passing_score' => $program->passing_score ?: 80,
            'is_enrolled' => $isEnrolled,
            'is_completed' => $isCompleted,
            'certificate_issued' => $certificateIssued,
            'certificate_no' => $certificateNo,
            'score' => $score,
            'enrollment_id' => $enrollment ? $enrollment->enrollment_id : null,
            'status' => $enrollment ? $enrollment->status : 'not_enrolled',
            'review_status' => $reviewStatus,
            'passed' => $enrollment ? (bool)$enrollment->passed : false,
            'enrolled_skills' => $enrollment ? $enrollment->enrolled_skills : null,
            'skills' => !empty($program->skills) 
                ? array_values(array_filter(array_map('trim', explode(',', $program->skills))))
                : [trim($program->title)],
        ];
    }

    /**
     * Get all training programs with enrollment status.
     */
    public function getAll(Request $request)
    {
        $jobseeker = $this->resolveJobseeker($request);
        $jobseekerId = $jobseeker ? $jobseeker->jobseeker_id : null;

        $programs = TrainingProgram::with('topics')->get()->map(function ($p) use ($jobseekerId) {
            return $this->formatProgram($p, $jobseekerId);
        });

        $filter = $request->input('filter', 'all');
        if ($filter === 'enrolled') {
            $programs = $programs->filter(fn($p) => $p['is_enrolled'] && !$p['is_completed'])->values();
        } elseif ($filter === 'completed') {
            $programs = $programs->filter(fn($p) => $p['is_completed'])->values();
        }

        return response()->json($programs);
    }

    /**
     * Get single training program by ID.
     */
    public function getById(Request $request, $id)
    {
        $program = TrainingProgram::with('topics')->find($id);

        if (!$program) {
            return response()->json([
                'success' => false,
                'message' => 'Training program not found',
            ], 404);
        }

        $jobseeker = $this->resolveJobseeker($request);
        $jobseekerId = $jobseeker ? $jobseeker->jobseeker_id : null;

        $formatted = $this->formatProgram($program, $jobseekerId);

        return response()->json([
            'success' => true,
            'data' => $formatted,
        ]);
    }

    /**
     * Enroll in a training program.
     */
    public function enroll(Request $request, $id)
    {
        $jobseeker = $this->resolveJobseeker($request);
        if (!$jobseeker) {
            return response()->json([
                'success' => false, 
                'message' => 'Please sign in to your jobseeker account to enroll in courses.',
                'requires_auth' => true
            ], 401);
        }

        $user = $jobseeker->user ?? \App\Models\User::find($jobseeker->user_id);

        $training = TrainingProgram::find($id);
        if (!$training) {
            return response()->json(['success' => false, 'message' => 'Training not found'], 404);
        }

        $enrollment = TrainingEnrollment::where('jobseeker_id', $jobseeker->jobseeker_id)
            ->where('training_id', $id)
            ->first();

        if ($enrollment) {
            return response()->json([
                'success' => true,
                'message' => "You are already enrolled in '{$training->title}'.",
                'enrollment_id' => $enrollment->enrollment_id,
            ]);
        }

        $newEnrollment = TrainingEnrollment::create([
            'jobseeker_id' => $jobseeker->jobseeker_id,
            'training_id' => $id,
            'training_type' => $training->training_type ?: 'online',
            'status' => 'enrolled',
            'start_date' => now()->toDateString(),
        ]);

        Notification::create([
            'user_id' => $user->user_id,
            'title' => 'Enrolled in Training Course',
            'message' => "You have successfully enrolled in '{$training->title}'.",
            'type' => 'training',
            'is_read' => false,
            'related_id' => $training->training_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Successfully enrolled in '{$training->title}'!",
            'enrollment_id' => $newEnrollment->enrollment_id,
        ]);
    }

    /**
     * Submit a training assessment for trainer review.
     */
    public function submitQuiz(Request $request, $id)
    {
        $training = TrainingProgram::with('trainer')->find($id);
        if (!$training) {
            return response()->json(['success' => false, 'message' => 'Training program not found'], 404);
        }

        if (empty($this->quizService->getQuestionsForTraining($training))) {
            return response()->json([
                'success' => false,
                'message' => 'This course does not have assessment questions yet. Please wait for the trainer to add them.',
            ], 422);
        }

        $validated = $request->validate(['responses' => 'required|array']);
        $jobseeker = $this->resolveJobseeker($request);
        if (!$jobseeker) {
            return response()->json(['success' => false, 'message' => 'Sign in to your jobseeker account before submitting the assessment.'], 401);
        }

        try {
            $gradedSubmission = $this->quizService->gradeSubmission($training, $validated['responses']);
        } catch (\InvalidArgumentException $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
        }

        $enrollment = TrainingEnrollment::firstOrNew([
            'jobseeker_id' => $jobseeker->jobseeker_id,
            'training_id' => $training->training_id,
        ]);
        $existingAnswers = is_array($enrollment->answers)
            ? $enrollment->answers
            : (json_decode($enrollment->answers ?? '[]', true) ?: []);
        if (in_array($existingAnswers['review_status'] ?? null, ['pending', 'passed'], true)) {
            return response()->json(['success' => false, 'message' => 'This assessment is already pending review or has been approved.'], 409);
        }

        $enrollment->training_type = $training->training_type ?: 'online';
        $enrollment->start_date = $enrollment->start_date ?: now()->toDateString();
        $enrollment->score = $gradedSubmission['objective_score']['percentage'];
        $enrollment->passed = null;
        $enrollment->status = 'in_progress';
        $enrollment->end_date = null;
        $enrollment->certificate_no = null;
        $enrollment->certificate_issued = false;
        $enrollment->certificate_issued_at = null;
        $enrollment->answers = [
            'review_status' => 'pending',
            'submitted_at' => now()->toIso8601String(),
            'objective_score' => $gradedSubmission['objective_score'],
            'responses' => $gradedSubmission['responses'],
        ];
        $enrollment->save();

        Notification::create([
            'user_id' => $jobseeker->user_id,
            'title' => 'Assessment Submitted for Trainer Review',
            'message' => "Your assessment for '{$training->title}' is awaiting trainer review. Your certificate is pending.",
            'type' => 'training',
            'is_read' => false,
            'related_id' => $enrollment->enrollment_id,
        ]);
        if ($training->trainer?->user_id) {
            Notification::create([
                'user_id' => $training->trainer->user_id,
                'title' => 'Trainee Assessment Awaiting Review',
                'message' => "{$jobseeker->first_name} {$jobseeker->last_name} submitted the assessment for '{$training->title}'. Review objective answers and essay responses.",
                'type' => 'training',
                'is_read' => false,
                'related_id' => $enrollment->enrollment_id,
            ]);
        }

        return response()->json([
            'success' => true,
            'review_status' => 'pending',
            'objective_score' => $gradedSubmission['objective_score'],
            'certificate_issued' => false,
            'message' => 'Assessment submitted. The trainer must review your answers before deciding the result.',
        ]);
    }

    /**
     * Get all issued certificates for the jobseeker.
     */
    public function getCertificates(Request $request)
    {
        $jobseeker = $this->resolveJobseeker($request);
        if (!$jobseeker) {
            return response()->json([]);
        }

        $certificates = DB::table('training_enrollments')
            ->join('training_programs', 'training_enrollments.training_id', '=', 'training_programs.training_id')
            ->where('training_enrollments.jobseeker_id', $jobseeker->jobseeker_id)
            ->where('training_enrollments.certificate_issued', 1)
            ->select(
                'training_enrollments.enrollment_id',
                'training_enrollments.certificate_no',
                'training_enrollments.certificate_issued_at',
                'training_enrollments.score',
                'training_programs.title as course_title',
                'training_programs.training_type',
                'training_programs.duration_months'
            )
            ->orderBy('training_enrollments.certificate_issued_at', 'desc')
            ->get();

        return response()->json($certificates);
    }
}
