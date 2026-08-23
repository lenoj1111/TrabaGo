<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrainingEnrollment;
use App\Models\TrainingProgram;
use App\Services\TrainingQuizService;
use Illuminate\Http\Request;

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
    private function formatProgram(TrainingProgram $program): array
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
        ];
    }

    /**
     * Get all training programs.
     */
    public function getAll()
    {
        $programs = TrainingProgram::with('topics')->get()->map(function ($p) {
            return $this->formatProgram($p);
        });

        return response()->json($programs);
    }

    /**
     * Get single training program by ID.
     */
    public function getById($id)
    {
        $program = TrainingProgram::with('topics')->find($id);

        if (!$program) {
            return response()->json([
                'success' => false,
                'message' => 'Training program not found',
            ], 404);
        }

        $formatted = $this->formatProgram($program);

        return response()->json($formatted);
    }

    /**
     * Submit quiz results for a training program.
     */
    public function submitQuiz(Request $request, $id)
    {
        $score = $request->input('score', 0);
        $passed = (bool)$request->input('passed', false);

        $user = $request->user();
        if ($user && $user->jobseeker) {
            $jobseekerId = $user->jobseeker->jobseeker_id;
            
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

            $enrollment->status = $passed ? 'completed' : 'in_progress';
            $enrollment->answers = ['score' => $score, 'passed' => $passed, 'submitted_at' => now()->toIso8601String()];
            if ($passed) {
                $enrollment->end_date = now()->toDateString();

                $training = TrainingProgram::find($id);
                if ($training) {
                    $skillName = trim($training->title);
                    $exists = \App\Models\JobseekerSkill::where('jobseeker_id', $jobseekerId)
                        ->where('skill_name', $skillName)
                        ->first();

                    if (!$exists) {
                        \App\Models\JobseekerSkill::create([
                            'jobseeker_id' => $jobseekerId,
                            'skill_name' => $skillName,
                            'skill_type' => 'technical',
                        ]);
                    }

                    \App\Models\Notification::create([
                        'user_id' => $user->user_id,
                        'title' => 'Skill Certified!',
                        'message' => "Congratulations! You passed the '{$training->title}' assessment with {$score}%. The skill '{$skillName}' has been added to your profile, raising your AI match score!",
                        'type' => 'training',
                        'is_read' => false,
                        'related_id' => $training->training_id,
                    ]);
                }
            }
            $enrollment->save();
        }

        return response()->json([
            'success' => true,
            'score' => $score,
            'passed' => $passed,
            'message' => $passed ? 'Congratulations! You passed the quiz and earned a verified skill.' : 'Quiz completed.',
        ]);
    }
}
