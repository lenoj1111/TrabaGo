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
        $programQuestions = array_map(static fn($question) => [
            'id' => $question['id'],
            'question' => $question['question'],
            'image_url' => $question['image_url'],
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
                $certificateIssued = (bool)$enrollment->certificate_issued;
                $isCompleted = $enrollment->status === 'completed' && $certificateIssued;
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
            'training_start_date' => $program->training_start_date,
            'training_end_date' => $program->training_end_date,
            'trainee_capacity' => $program->trainee_capacity,
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

        if (!$jobseeker->isProfileAndDocumentsComplete() || !$user || !$user->isAccountVerified()) {
            return response()->json([
                'success' => false,
                'message' => 'Complete your jobseeker profile and verify your account with a valid Government ID before applying for training.',
            ], 403);
        }

        $eligibilityIssues = $training->demographicEligibilityIssues($jobseeker);
        if ($eligibilityIssues) {
            return response()->json([
                'success' => false,
                'message' => implode(' ', $eligibilityIssues),
            ], 422);
        }

        if ($training->trainee_capacity !== null && TrainingEnrollment::where('training_id', $id)->count() >= $training->trainee_capacity) {
            return response()->json(['success' => false, 'message' => 'This training program has reached its trainee capacity.'], 409);
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

        $hasEssay = collect($gradedSubmission['responses'])
            ->contains(fn($answer) => $answer['question_type'] === 'essay');
        $objectiveScore = $gradedSubmission['objective_score']['percentage'];
        $passingScore = (int) ($training->passing_score ?: 70);
        $passed = !$hasEssay && $objectiveScore !== null && $objectiveScore >= $passingScore;

        $result = DB::transaction(function () use ($training, $jobseeker, $gradedSubmission, $hasEssay, $objectiveScore, $passed) {
            $enrollment = TrainingEnrollment::where('jobseeker_id', $jobseeker->jobseeker_id)
                ->where('training_id', $training->training_id)
                ->lockForUpdate()
                ->first();

            if (!$enrollment) {
                return ['status' => 'not_enrolled'];
            }

            $existingAnswers = is_array($enrollment->answers)
                ? $enrollment->answers
                : (json_decode($enrollment->answers ?? '[]', true) ?: []);
            if ($enrollment->certificate_issued || $enrollment->passed) {
                return ['status' => 'already_passed'];
            }
            if (($existingAnswers['review_status'] ?? null) === 'pending') {
                return ['status' => 'pending_review'];
            }

            $attempts = (int) $enrollment->assessment_attempts + 1;
            $reviewStatus = $hasEssay ? 'pending' : ($passed ? 'passed' : 'needs_retake');
            $enrollment->update([
                'training_type' => $training->training_type ?: 'online',
                'start_date' => $enrollment->start_date ?: now()->toDateString(),
                'score' => $objectiveScore,
                'passed' => $passed,
                'status' => 'in_progress',
                'answers' => array_merge($gradedSubmission, [
                    'review_status' => $reviewStatus,
                    'attempt_number' => $attempts,
                    'submitted_at' => now()->toIso8601String(),
                ]),
                'assessment_attempts' => $attempts,
                'trainer_feedback' => null,
            ]);

            $tryLabel = $attempts === 1 ? '1 try' : "{$attempts} tries";
            if ($hasEssay) {
                $title = 'Assessment Submitted for Trainer Review';
                $message = "Your assessment for '{$training->title}' (Attempt #{$attempts}) is pending trainer review. If passed, return to the DMDP for your practical demo.";
            } elseif ($passed) {
                $title = 'Assessment Passed — Return for Demo (DMDP)';
                $message = "Congratulations! You passed '{$training->title}' with {$objectiveScore}% in {$tryLabel}. Please return to the DMDP for your practical demo before your trainer issues your certificate.";
            } else {
                $title = 'Assessment Not Passed';
                $message = "You scored {$objectiveScore}% on '{$training->title}' (passing score: {$passingScore}%, attempt #{$attempts}). Review the course materials and retake the assessment.";
            }

            Notification::create([
                'user_id' => $jobseeker->user_id,
                'title' => $title,
                'message' => $message,
                'type' => 'training',
                'is_read' => false,
                'related_id' => $enrollment->enrollment_id,
            ]);

            if ($training->trainer?->user_id && ($hasEssay || $passed)) {
                Notification::create([
                    'user_id' => $training->trainer->user_id,
                    'title' => $hasEssay ? 'Trainee Assessment Awaiting Review' : 'Trainee Ready for DMDP Demo',
                    'message' => $hasEssay
                        ? "{$jobseeker->first_name} {$jobseeker->last_name} submitted an assessment for '{$training->title}' (Attempt #{$attempts}) that requires review."
                        : "{$jobseeker->first_name} {$jobseeker->last_name} passed '{$training->title}' with {$objectiveScore}% in {$tryLabel} and is due to return for the practical demo.",
                    'type' => 'training',
                    'is_read' => false,
                    'related_id' => $enrollment->enrollment_id,
                ]);
            }

            return [
                'status' => $reviewStatus,
                'score' => $objectiveScore,
                'attempts' => $attempts,
                'enrollment_id' => $enrollment->enrollment_id,
            ];
        });

        if ($result['status'] === 'not_enrolled') {
            return response()->json(['success' => false, 'message' => 'Enroll in this training course before submitting its assessment.'], 409);
        }
        if ($result['status'] === 'already_passed') {
            return response()->json(['success' => false, 'message' => 'You already passed this assessment. Return to the DMDP for your practical demo.'], 409);
        }
        if ($result['status'] === 'pending_review') {
            return response()->json(['success' => false, 'message' => 'Your previous assessment submission is still awaiting trainer review.'], 409);
        }

        return response()->json([
            'success' => true,
            'review_status' => $result['status'],
            'objective_score' => $gradedSubmission['objective_score'],
            'assessment_attempts' => $result['attempts'],
            'retakes' => max($result['attempts'] - 1, 0),
            'passed' => $passed,
            'certificate_issued' => false,
            'message' => match ($result['status']) {
                'pending' => 'Assessment submitted. Essay and mixed responses are awaiting trainer review.',
                'passed' => 'You passed. Return to the DMDP for your practical demo before certificate issuance.',
                default => 'Assessment not passed. Review the course materials and retake the assessment.',
            },
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
