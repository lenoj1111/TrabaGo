<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobseekerDetail;
use App\Models\JobseekerSkill;
use App\Models\Notification;
use App\Models\TrainingEnrollment;
use App\Models\TrainingProgram;
use App\Services\TrainingQuizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TrainingController extends Controller
{
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
        $programQuestions = $this->quizService->getQuestionsForTraining($program, 5);

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
     * Resolve the jobseeker model from authenticated user or fallback to first available jobseeker.
     */
    private function resolveJobseeker(?Request $request): ?\App\Models\Jobseeker
    {
        $user = auth('sanctum')->user() ?? ($request ? ($request->user() ?? Auth::user()) : Auth::user());
        if ($user) {
            $jobseeker = $user->jobseeker ?? \App\Models\Jobseeker::where('user_id', $user->user_id)->first();
            if (!$jobseeker) {
                $nameParts = explode(' ', $user->full_name ?? '', 2);
                $jobseeker = \App\Models\Jobseeker::firstOrCreate(
                    ['user_id' => $user->user_id],
                    [
                        'first_name' => $nameParts[0] ?? explode('@', $user->email)[0] ?? 'Jobseeker',
                        'last_name' => $nameParts[1] ?? 'User',
                        'email' => $user->email,
                        'employment_status' => 'Looking for job',
                        'citizenship' => 'Filipino',
                    ]
                );
            }
            return $jobseeker;
        }

        // Fallback for dev / unauthenticated demo requests
        return \App\Models\Jobseeker::first();
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
     * Submit quiz results for a training program and generate official certificate.
     */
    public function submitQuiz(Request $request, $id)
    {
        $score = (int)$request->input('score', 0);
        $total = (int)$request->input('total', 5);
        $training = TrainingProgram::find($id);

        if (!$training) {
            return response()->json(['success' => false, 'message' => 'Training program not found'], 404);
        }

        $passingThreshold = $training->passing_score ?: 80;
        // Percentage calculation
        $percentScore = $total > 0 ? (int)(($score / $total) * 100) : $score;
        $passed = $percentScore >= $passingThreshold;

        $jobseeker = $this->resolveJobseeker($request);
        $certNo = null;

        if ($jobseeker) {
            $jobseekerId = $jobseeker->jobseeker_id;
            $user = $jobseeker->user ?? \App\Models\User::find($jobseeker->user_id);
            
            $enrollment = TrainingEnrollment::where('jobseeker_id', $jobseekerId)
                ->where('training_id', $id)
                ->first();

            if (!$enrollment) {
                $enrollment = new TrainingEnrollment([
                    'jobseeker_id' => $jobseekerId,
                    'training_id' => $id,
                    'training_type' => 'online',
                    'start_date' => now()->toDateString(),
                ]);
            }

            $enrollment->score = $percentScore;
            $enrollment->passed = $passed ? 1 : 0;
            $enrollment->answers = ['score' => $percentScore, 'passed' => $passed, 'submitted_at' => now()->toIso8601String()];
            
            if ($passed) {
                $enrollment->end_date = now()->toDateString();
                $enrollment->status = 'completed';
                
                $certNo = 'DMDP-CERT-' . date('Y') . '-' . strtoupper(Str::random(6));
                $enrollment->certificate_no = $certNo;
                $enrollment->certificate_issued = 1;
                $enrollment->certificate_issued_at = now();

                // Save to document vault
                $detail = JobseekerDetail::where('jobseeker_id', $jobseekerId)->first();
                if (!$detail) {
                    $detail = JobseekerDetail::create(['jobseeker_id' => $jobseekerId, 'training_certificates' => []]);
                }
                $existingDocs = is_array($detail->training_certificates)
                    ? $detail->training_certificates
                    : (json_decode($detail->training_certificates ?? '[]', true) ?: []);

                $existingDocs[] = [
                    'id' => 'cert_' . ($enrollment->enrollment_id ?: $id),
                    'enrollment_id' => $enrollment->enrollment_id ?: $id,
                    'category' => 'certificate',
                    'name' => "Certificate of Completion - {$training->title} (#{$certNo})",
                    'status' => 'verified',
                    'certificate_no' => $certNo,
                    'course_title' => $training->title,
                    'uploaded_at' => now()->toIso8601String(),
                ];
                $detail->training_certificates = array_values($existingDocs);
                $detail->save();

                // Automatically grant skill tag to jobseeker's profile
                $skillName = trim($training->title);
                $exists = JobseekerSkill::where('jobseeker_id', $jobseekerId)
                    ->where('skill_name', $skillName)
                    ->first();

                if (!$exists) {
                    JobseekerSkill::create([
                        'jobseeker_id' => $jobseekerId,
                        'skill_name' => $skillName,
                        'skill_type' => 'technical',
                    ]);
                }

                // Notification if user object available
                if ($user) {
                    Notification::create([
                        'user_id' => $user->user_id,
                        'title' => '🎓 Official Certificate Issued!',
                        'message' => "Congratulations! You passed '{$training->title}' with {$percentScore}%. Your official Certificate of Completion (#{$certNo}) has been generated and added to your profile skills!",
                        'type' => 'training',
                        'is_read' => false,
                        'related_id' => $training->training_id,
                    ]);
                }
            } else {
                $enrollment->status = 'in_progress';
            }

            $enrollment->save();
        }

        return response()->json([
            'success' => true,
            'score' => $percentScore,
            'passed' => $passed,
            'certificate_no' => $certNo,
            'message' => $passed
                ? "Congratulations! You scored {$percentScore}% and earned your official Certificate of Completion (#{$certNo})!"
                : "Assessment completed with score {$percentScore}%. Passing score is {$passingThreshold}%. Review course material and try again!",
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
