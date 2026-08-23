<?php

namespace App\Services;

use App\Models\TrainingProgram;

class TrainingQuizService
{
    /**
     * Get at least 5 structured quiz questions for a training program.
     *
     * @param TrainingProgram $training
     * @param int $minCount Minimum required questions (defaults to 5)
     * @return array
     */
    public function getQuestionsForTraining(TrainingProgram $training, int $minCount = 5): array
    {
        $questions = [];

        // 1. Extract existing questions from topic JSONs
        if ($training->relationLoaded('topics') || $training->topics) {
            foreach ($training->topics as $topic) {
                $qData = is_array($topic->questions) ? $topic->questions : json_decode($topic->questions ?? '[]', true);
                if (is_array($qData) && !empty($qData)) {
                    foreach ($qData as $q) {
                        if (is_array($q) && !empty($q['question'])) {
                            $choices = $q['choices'] ?? ($q['options'] ?? []);
                            if (is_array($choices) && count($choices) >= 2) {
                                $questions[] = [
                                    'question' => $q['question'],
                                    'choices' => array_values($choices),
                                    'options' => array_values($choices),
                                    'answer' => is_numeric($q['answer'] ?? 0) ? (int)$q['answer'] : 0,
                                    'explanation' => $q['explanation'] ?? 'Correct standard procedure for this competency.',
                                ];
                            }
                        }
                    }
                }
            }
        }

        // 2. If questions count < 5, fill from course-tailored question bank
        if (count($questions) < $minCount) {
            $bank = $this->getQuestionBankForTitle($training->title, $training->description ?? '');
            
            foreach ($bank as $item) {
                // Avoid exact duplicate questions
                $exists = false;
                foreach ($questions as $existing) {
                    if (strcasecmp($existing['question'], $item['question']) === 0) {
                        $exists = true;
                        break;
                    }
                }

                if (!$exists) {
                    $questions[] = $item;
                    if (count($questions) >= $minCount) {
                        break;
                    }
                }
            }
        }

        return array_values($questions);
    }

    /**
     * Provide comprehensive course-relevant questions based on training title and category.
     */
    private function getQuestionBankForTitle(string $title, string $description): array
    {
        $lower = strtolower($title . ' ' . $description);

        if (str_contains($lower, 'laravel') || str_contains($lower, 'web') || str_contains($lower, 'php') || str_contains($lower, 'sql') || str_contains($lower, 'programming') || str_contains($lower, 'developer')) {
            return [
                [
                    'question' => 'In the Laravel MVC framework, what is the primary role of a Controller?',
                    'choices' => [
                        'Handles HTTP requests and coordinates model data and views',
                        'Defines physical database table schemas',
                        'Compiles CSS stylesheets and JavaScript assets',
                        'Acts as the web server reverse proxy'
                    ],
                    'options' => [
                        'Handles HTTP requests and coordinates model data and views',
                        'Defines physical database table schemas',
                        'Compiles CSS stylesheets and JavaScript assets',
                        'Acts as the web server reverse proxy'
                    ],
                    'answer' => 0,
                    'explanation' => 'Controllers orchestrate incoming HTTP requests, retrieve or persist data using Eloquent Models, and return views or JSON responses.',
                ],
                [
                    'question' => 'Which Artisan command is used to execute database migration scripts in Laravel?',
                    'choices' => [
                        'php artisan migrate',
                        'php artisan make:table',
                        'composer update --database',
                        'npm run build'
                    ],
                    'options' => [
                        'php artisan migrate',
                        'php artisan make:table',
                        'composer update --database',
                        'npm run build'
                    ],
                    'answer' => 0,
                    'explanation' => 'php artisan migrate executes all pending migration files against the configured SQL database.',
                ],
                [
                    'question' => 'Which HTTP method should be used for securely creating a new record via a REST API or web form?',
                    'choices' => [
                        'POST',
                        'GET',
                        'OPTIONS',
                        'HEAD'
                    ],
                    'options' => [
                        'POST',
                        'GET',
                        'OPTIONS',
                        'HEAD'
                    ],
                    'answer' => 0,
                    'explanation' => 'HTTP POST is standard for creating new resources and submitting secure form payloads.',
                ],
                [
                    'question' => 'What is the primary purpose of Eloquent ORM relationships in backend development?',
                    'choices' => [
                        'To define and query connections between database entities using object-oriented syntax',
                        'To speed up CPU clock cycles on the host server',
                        'To eliminate the need for primary keys and foreign keys',
                        'To automatically render frontend HTML templates'
                    ],
                    'options' => [
                        'To define and query connections between database entities using object-oriented syntax',
                        'To speed up CPU clock cycles on the host server',
                        'To eliminate the need for primary keys and foreign keys',
                        'To automatically render frontend HTML templates'
                    ],
                    'answer' => 0,
                    'explanation' => 'Eloquent relationships (hasMany, belongsTo, etc.) let developers query relational database tables without writing raw SQL strings.',
                ],
                [
                    'question' => 'How does parameterized SQL / Prepared Statements protect a web application?',
                    'choices' => [
                        'Prevents SQL Injection attacks by separating query code from user-supplied parameters',
                        'Compresses images before uploading to storage',
                        'Caches client-side browser cookies',
                        'Encrypts SSL certificates'
                    ],
                    'options' => [
                        'Prevents SQL Injection attacks by separating query code from user-supplied parameters',
                        'Compresses images before uploading to storage',
                        'Caches client-side browser cookies',
                        'Encrypts SSL certificates'
                    ],
                    'answer' => 0,
                    'explanation' => 'Parameterized queries ensure user input is treated strictly as data parameters rather than executable SQL commands, stopping SQL injection.',
                ],
            ];
        }

        if (str_contains($lower, 'digital') || str_contains($lower, 'computer') || str_contains($lower, 'excel') || str_contains($lower, 'spreadsheet') || str_contains($lower, 'office')) {
            return [
                [
                    'question' => 'Which spreadsheet formula correctly calculates the sum of all values in cell range A1 through A20 in Microsoft Excel and Google Sheets?',
                    'choices' => [
                        '=SUM(A1:A20)',
                        '=TOTAL(A1..A20)',
                        '=ADD(A1:A20)',
                        '=COUNT(A1:A20)'
                    ],
                    'options' => [
                        '=SUM(A1:A20)',
                        '=TOTAL(A1..A20)',
                        '=ADD(A1:A20)',
                        '=COUNT(A1:A20)'
                    ],
                    'answer' => 0,
                    'explanation' => '=SUM(A1:A20) is the standard arithmetic formula to add cell values across a specified range.',
                ],
                [
                    'question' => 'What is the most secure practice when managing login passwords for workplace computer accounts?',
                    'choices' => [
                        'Use strong, unique passwords with multi-factor authentication (MFA)',
                        'Write passwords on sticky notes attached to the monitor',
                        'Share the password via unencrypted chat with coworkers',
                        'Use the same basic password for all personal and business accounts'
                    ],
                    'options' => [
                        'Use strong, unique passwords with multi-factor authentication (MFA)',
                        'Write passwords on sticky notes attached to the monitor',
                        'Share the password via unencrypted chat with coworkers',
                        'Use the same basic password for all personal and business accounts'
                    ],
                    'answer' => 0,
                    'explanation' => 'Complex passwords combined with 2FA/MFA provide robust protection against unauthorized data access.',
                ],
                [
                    'question' => 'Which file format is universally recommended for preserving document layout and typography across all operating systems?',
                    'choices' => [
                        'PDF (.pdf)',
                        'Plain Text (.txt)',
                        'Bitmap Image (.bmp)',
                        'Temporary Cache (.tmp)'
                    ],
                    'options' => [
                        'PDF (.pdf)',
                        'Plain Text (.txt)',
                        'Bitmap Image (.bmp)',
                        'Temporary Cache (.tmp)'
                    ],
                    'answer' => 0,
                    'explanation' => 'PDF (Portable Document Format) preserves formatting, fonts, and layouts across all devices and printers.',
                ],
                [
                    'question' => 'What is the function of keyboard shortcut Ctrl + Z (or Cmd + Z) in standard document and data applications?',
                    'choices' => [
                        'Undo the most recent action or edit',
                        'Permanently delete the current file',
                        'Print the entire document',
                        'Close the application window'
                    ],
                    'options' => [
                        'Undo the most recent action or edit',
                        'Permanently delete the current file',
                        'Print the entire document',
                        'Close the application window'
                    ],
                    'answer' => 0,
                    'explanation' => 'Ctrl + Z reverses the last change made in almost all productivity and text software.',
                ],
                [
                    'question' => 'When creating cloud backups of company documents, what is the key advantage?',
                    'choices' => [
                        'Ensures data recovery in case of hardware failure or localized data loss',
                        'Removes all file size limits permanently',
                        'Allows anyone on the internet to view confidential files without permission',
                        'Eliminates the need for an internet connection'
                    ],
                    'options' => [
                        'Ensures data recovery in case of hardware failure or localized data loss',
                        'Removes all file size limits permanently',
                        'Allows anyone on the internet to view confidential files without permission',
                        'Eliminates the need for an internet connection'
                    ],
                    'answer' => 0,
                    'explanation' => 'Cloud backups protect against hardware failure, theft, and data corruption by storing synchronized copies off-site.',
                ],
            ];
        }

        if (str_contains($lower, 'customer') || str_contains($lower, 'support') || str_contains($lower, 'communication') || str_contains($lower, 'call center')) {
            return [
                [
                    'question' => 'What is the primary step in de-escalating an upset or dissatisfied customer?',
                    'choices' => [
                        'Listen actively without interrupting, acknowledge their frustration, and express empathy',
                        'Interrupt immediately and explain why the customer is mistaken',
                        'Transfer the call immediately without informing the customer',
                        'Tell the customer to submit an email and disconnect the line'
                    ],
                    'options' => [
                        'Listen actively without interrupting, acknowledge their frustration, and express empathy',
                        'Interrupt immediately and explain why the customer is mistaken',
                        'Transfer the call immediately without informing the customer',
                        'Tell the customer to submit an email and disconnect the line'
                    ],
                    'answer' => 0,
                    'explanation' => 'Empathetic active listening validates the customer’s concerns and sets the stage for collaborative problem resolution.',
                ],
                [
                    'question' => 'When resolving a customer inquiry, why is documentation in the CRM or ticketing system critical?',
                    'choices' => [
                        'Provides an audit trail and enables team members to follow up with full context',
                        'To keep score of customer complaints',
                        'To automatically deduct salary from coworkers',
                        'It is not necessary if the customer verbally agrees'
                    ],
                    'options' => [
                        'Provides an audit trail and enables team members to follow up with full context',
                        'To keep score of customer complaints',
                        'To automatically deduct salary from coworkers',
                        'It is not necessary if the customer verbally agrees'
                    ],
                    'answer' => 0,
                    'explanation' => 'Accurate ticket logs ensure seamless collaboration across shifts and maintain company service level standards.',
                ],
                [
                    'question' => 'What does "First Contact Resolution" (FCR) measure in customer support operations?',
                    'choices' => [
                        'The percentage of customer issues resolved satisfactorily on the initial interaction',
                        'How fast the agent answers the phone',
                        'The number of calls transferred to supervisors',
                        'The duration of the onboarding training period'
                    ],
                    'options' => [
                        'The percentage of customer issues resolved satisfactorily on the initial interaction',
                        'How fast the agent answers the phone',
                        'The number of calls transferred to supervisors',
                        'The duration of the onboarding training period'
                    ],
                    'answer' => 0,
                    'explanation' => 'FCR measures efficiency and customer satisfaction by resolving requests without requiring repeat contacts.',
                ],
                [
                    'question' => 'Which tone of voice is most effective for professional customer interactions?',
                    'choices' => [
                        'Polite, clear, confident, and empathetic',
                        'Aggressive and authoritative',
                        'Monotone and disinterested',
                        'Sarcastic and humorous'
                    ],
                    'options' => [
                        'Polite, clear, confident, and empathetic',
                        'Aggressive and authoritative',
                        'Monotone and disinterested',
                        'Sarcastic and humorous'
                    ],
                    'answer' => 0,
                    'explanation' => 'A calm, polite, and confident tone builds trust and demonstrates workplace professionalism.',
                ],
                [
                    'question' => 'Before concluding a support interaction with a client, what is the best closing procedure?',
                    'choices' => [
                        'Confirm all concerns have been addressed and summarize any follow-up commitments',
                        'Hang up immediately when the technical task is done',
                        'Ask the customer for their personal social media handles',
                        'Leave the ticket open indefinitely'
                    ],
                    'options' => [
                        'Confirm all concerns have been addressed and summarize any follow-up commitments',
                        'Hang up immediately when the technical task is done',
                        'Ask the customer for their personal social media handles',
                        'Leave the ticket open indefinitely'
                    ],
                    'answer' => 0,
                    'explanation' => 'Checking for additional questions and confirming next steps ensures complete satisfaction and prevents repeat contacts.',
                ],
            ];
        }

        // Generic Workplace Readiness & Technical Competency Question Bank (5+ questions)
        return [
            [
                'question' => "What is the primary best practice when applying {$title} in modern enterprise workflows?",
                'choices' => [
                    "Maintaining consistent industry standards and following verified documentation guidelines",
                    "Skipping preliminary quality checks to complete tasks faster",
                    "Ignoring constructive peer feedback during project execution",
                    "Relying solely on unverified third-party tools without validation"
                ],
                'options' => [
                    "Maintaining consistent industry standards and following verified documentation guidelines",
                    "Skipping preliminary quality checks to complete tasks faster",
                    "Ignoring constructive peer feedback during project execution",
                    "Relying solely on unverified third-party tools without validation"
                ],
                'answer' => 0,
                'explanation' => "Following verified standards and documentation ensures dependable, high-quality deliverables in {$title}.",
            ],
            [
                'question' => "How do you ensure data accuracy, safety, and reliability when performing tasks in {$title}?",
                'choices' => [
                    "Perform systematic routine validation, testing, and double-checking outputs before submission",
                    "Assuming all inputs are correct without verification",
                    "Only checking for errors when reported by clients or supervisors",
                    "Deleting unexpected records without creating audit logs"
                ],
                'options' => [
                    "Perform systematic routine validation, testing, and double-checking outputs before submission",
                    "Assuming all inputs are correct without verification",
                    "Only checking for errors when reported by clients or supervisors",
                    "Deleting unexpected records without creating audit logs"
                ],
                'answer' => 0,
                'explanation' => "Proactive quality assurance and output verification guarantee consistent performance.",
            ],
            [
                'question' => "Which of the following demonstrates professional problem-solving and critical thinking in {$title}?",
                'choices' => [
                    "Identifying root causes systematically and implementing structured long-term solutions",
                    "Hiding issues until the end of the project cycle",
                    "Assigning blame to other team members when hurdles arise",
                    "Restarting systems without investigating error logs and causes"
                ],
                'options' => [
                    "Identifying root causes systematically and implementing structured long-term solutions",
                    "Hiding issues until the end of the project cycle",
                    "Assigning blame to other team members when hurdles arise",
                    "Restarting systems without investigating error logs and causes"
                ],
                'answer' => 0,
                'explanation' => "Systematic root-cause analysis resolves issues permanently and prevents recurrent disruptions.",
            ],
            [
                'question' => "When communicating status updates to managers, employers, or clients regarding {$title}, you should:",
                'choices' => [
                    "Provide clear, concise, and structured progress updates with milestones and blockers",
                    "Only reply when a critical crisis happens",
                    "Use vague responses to avoid commitments",
                    "Communicate exclusively through informal, untracked channels"
                ],
                'options' => [
                    "Provide clear, concise, and structured progress updates with milestones and blockers",
                    "Only reply when a critical crisis happens",
                    "Use vague responses to avoid commitments",
                    "Communicate exclusively through informal, untracked channels"
                ],
                'answer' => 0,
                'explanation' => "Transparent and structured communication keeps all stakeholders aligned on project milestones.",
            ],
            [
                'question' => "What is required to continuously improve professional competency in {$title}?",
                'choices' => [
                    "Staying updated with industry developments and participating in ongoing skills training",
                    "Stopping all learning once initially employed",
                    "Refusing to learn new tools or optimized workflows",
                    "Relying only on basic knowledge without practical application"
                ],
                'options' => [
                    "Staying updated with industry developments and participating in ongoing skills training",
                    "Stopping all learning once initially employed",
                    "Refusing to learn new tools or optimized workflows",
                    "Relying only on basic knowledge without practical application"
                ],
                'answer' => 0,
                'explanation' => "Lifelong learning and active upskilling are essential for long-term career growth and productivity.",
            ],
        ];
    }
}
