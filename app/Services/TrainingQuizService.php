<?php

namespace App\Services;

use App\Models\TrainingProgram;
use Illuminate\Support\Facades\DB;

class TrainingQuizService
{
    /**
     * Get assessment questions authored by the trainer for a training program.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getQuestionsForTraining(TrainingProgram $training): array
    {
        $questions = [];
        $assessments = DB::table('training_assessments')
            ->where('training_id', $training->training_id)
            ->orderBy('assessment_id', 'asc')
            ->get();

        foreach ($assessments as $item) {
            $choices = is_array($item->options) ? $item->options : json_decode($item->options ?? '[]', true);
            $type = $item->question_type ?: 'multiple_choice';
            if ($type === 'essay' || (is_array($choices) && count($choices) >= 2)) {
                $questions[] = [
                    'id' => $item->assessment_id,
                    'question' => $item->question,
                    'type' => $type,
                    'choices' => $type === 'essay' ? [] : array_values($choices),
                    'answer' => $type === 'essay' ? null : (int) $item->correct_answer,
                    'explanation' => $item->explanation ?? '',
                    'points' => (int) ($item->points ?? 1),
                ];
            }
        }

        return $questions;
    }

    /**
     * Score objective answers and retain essay responses for trainer review.
     *
     * @param array<string, mixed> $submittedResponses
     * @return array{responses: array<int, array<string, mixed>>, objective_score: array<string, int|float|null>}
     */
    public function gradeSubmission(TrainingProgram $training, array $submittedResponses): array
    {
        $gradedResponses = [];
        $correctCount = 0;
        $objectiveCount = 0;
        $earnedPoints = 0;
        $totalPoints = 0;

        foreach ($this->getQuestionsForTraining($training) as $question) {
            $id = (string) $question['id'];
            if (!array_key_exists($id, $submittedResponses)) {
                throw new \InvalidArgumentException('Please answer every assessment question before submitting.');
            }

            $response = $submittedResponses[$id];
            $graded = [
                'assessment_id' => $question['id'],
                'question' => $question['question'],
                'question_type' => $question['type'],
                'response' => null,
                'options' => $question['choices'],
                'correct_answer' => null,
                'is_correct' => null,
                'points' => $question['points'],
                'awarded_points' => null,
                'explanation' => $question['explanation'],
            ];

            if ($question['type'] === 'essay') {
                if (!is_string($response) || trim($response) === '' || mb_strlen($response) > 5000) {
                    throw new \InvalidArgumentException('Essay answers are required and must be 5,000 characters or fewer.');
                }
                $graded['response'] = trim($response);
            } else {
                $selectedIndex = filter_var($response, FILTER_VALIDATE_INT);
                if ($selectedIndex === false || !array_key_exists($selectedIndex, $question['choices'])) {
                    throw new \InvalidArgumentException('Please select a valid answer for each multiple-choice question.');
                }

                $isCorrect = $selectedIndex === $question['answer'];
                $graded['response'] = $question['choices'][$selectedIndex];
                $graded['correct_answer'] = $question['choices'][$question['answer']];
                $graded['selected_index'] = $selectedIndex;
                $graded['is_correct'] = $isCorrect;
                $graded['awarded_points'] = $isCorrect ? $question['points'] : 0;
                $objectiveCount++;
                $totalPoints += $question['points'];
                if ($isCorrect) {
                    $correctCount++;
                    $earnedPoints += $question['points'];
                }
            }

            $gradedResponses[] = $graded;
        }

        return [
            'responses' => $gradedResponses,
            'objective_score' => [
                'correct_count' => $correctCount,
                'question_count' => $objectiveCount,
                'earned_points' => $earnedPoints,
                'total_points' => $totalPoints,
                'percentage' => $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 1) : null,
            ],
        ];
    }
}
