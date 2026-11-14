<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Jobseeker;
use App\Models\JobseekerSkill;
use App\Models\Notification;
use App\Models\TrainingAssessment;
use App\Models\TrainingEnrollment;
use App\Models\TrainingProgram;
use App\Models\TrainingTopic;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TrainerPortalController extends Controller
{
    private function hasTrainerAuthoredAssessments(int $trainingId): bool
    {
        return TrainingAssessment::where('training_id', $trainingId)->exists();
    }

    private function validateCourseRequest(Request $request, bool $includeTopics = false): array
    {
        $rules = [
            'title' => 'required|string|max:150',
            'training_type' => 'required|in:online,laboratory_onsite',
            'training_start_date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'training_end_date' => 'required|date|after_or_equal:training_start_date',
            'trainee_capacity' => 'required|integer|min:1|max:10000',
            'description' => 'required|string|max:2000',
            'skills' => 'nullable|string|max:500',
            'passing_score' => 'nullable|integer|min:50|max:100',
            'demographics' => 'nullable|array',
            'demographics.gender' => 'nullable|in:Male,Female',
            'demographics.min_age' => 'nullable|integer|min:15|max:100',
            'demographics.max_age' => 'nullable|integer|min:15|max:100',
            'demographics.education_levels' => 'nullable|array',
            'demographics.education_levels.*' => 'string|in:Elementary,High School,Senior High School,Vocational,College,Post Graduate',
        ];

        if ($includeTopics) {
            $rules += [
                'topics' => 'nullable|array',
                'topics.*.title' => 'nullable|string|max:150',
                'topics.*.video_url' => 'nullable|string|max:255',
            ];
        }

        $validated = $request->validate($rules);
        $minimumAge = data_get($validated, 'demographics.min_age');
        $maximumAge = data_get($validated, 'demographics.max_age');

        if ($minimumAge !== null && $maximumAge !== null && $maximumAge < $minimumAge) {
            throw ValidationException::withMessages([
                'demographics.max_age' => 'The maximum age must be greater than or equal to the minimum age.',
            ]);
        }

        return $validated;
    }

    private function demographicRequirementsFromRequest(Request $request): ?array
    {
        $demographics = $request->input('demographics', []);
        $requirements = [
            'gender' => $demographics['gender'] ?? null,
            'min_age' => isset($demographics['min_age']) ? (int) $demographics['min_age'] : null,
            'max_age' => isset($demographics['max_age']) ? (int) $demographics['max_age'] : null,
            'education_levels' => array_values(array_unique($demographics['education_levels'] ?? [])),
        ];

        return $requirements['gender'] !== null
            || $requirements['min_age'] !== null
            || $requirements['max_age'] !== null
            || $requirements['education_levels'] !== []
            ? $requirements
            : null;
    }

    /**
     * Get or create trainer user profile ID.
     */
    private function getProfileId(): ?int
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        $profile = DB::table('user_profiles')->where('user_id', $user->user_id)->first();
        if (!$profile) {
            return DB::table('user_profiles')->insertGetId([
                'user_id' => $user->user_id,
                'full_name' => 'Skills Trainer',
                'office' => 'DMDP Manpower Skills Training Center',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $profile->profile_id;
    }

    /**
     * Figure 12: Trainer Dashboard.
     */
    public function dashboard()
    {
        $profileId = $this->getProfileId();

        // Enrollment Metrics (Aggregated single query)
        $enrollmentStats = DB::table('training_enrollments')
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN certificate_issued = 1 THEN 1 ELSE 0 END) as certificates_issued
            ")->first();
        $totalEnrollments = (int) ($enrollmentStats->total ?? 0);
        $inProgressEnrollments = (int) ($enrollmentStats->in_progress ?? 0);
        $completedEnrollments = (int) ($enrollmentStats->completed ?? 0);
        $certificatesIssued = (int) ($enrollmentStats->certificates_issued ?? 0);
        $coursesCount = DB::table('training_programs')->count();

        $completionRate = $totalEnrollments > 0
            ? round(($completedEnrollments / $totalEnrollments) * 100, 1)
            : 0;

        // Top Courses by Enrollment
        $topCourses = DB::table('training_programs')
            ->leftJoin('training_enrollments', 'training_programs.training_id', '=', 'training_enrollments.training_id')
            ->select('training_programs.training_id', 'training_programs.title', 'training_programs.training_type', DB::raw('COUNT(training_enrollments.enrollment_id) as enrollments_count'))
            ->groupBy('training_programs.training_id', 'training_programs.title', 'training_programs.training_type')
            ->orderByDesc('enrollments_count')
            ->limit(5)
            ->get();

        // Monthly enrollment trends (cached for 30s)
        $monthlyTrainingTrends = \Illuminate\Support\Facades\Cache::remember('trainer_monthly_training_trends', 30, function () {
            $trends = [];
            for ($i = 5; $i >= 0; $i--) {
                $monthStart = now()->subMonths($i)->startOfMonth();
                $monthEnd = now()->subMonths($i)->endOfMonth();
                $monthLabel = $monthStart->format('M Y');

                $enrolledCount = DB::table('training_enrollments')
                    ->where(function ($q) use ($monthStart, $monthEnd, $i) {
                        $q->whereBetween('start_date', [$monthStart->toDateString(), $monthEnd->toDateString()]);
                        if ($i === 0) {
                            $q->orWhereNull('start_date');
                        }
                    })
                    ->count();

                $completedCount = DB::table('training_enrollments')
                    ->where('status', 'completed')
                    ->where(function ($q) use ($monthStart, $monthEnd, $i) {
                        $q->whereBetween('end_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                            ->orWhereBetween('certificate_issued_at', [$monthStart, $monthEnd]);
                        if ($i === 0) {
                            $q->orWhere(function ($sub) {
                                $sub->whereNull('end_date')->whereNull('certificate_issued_at');
                            });
                        }
                    })
                    ->count();

                $trends[] = [
                    'month' => $monthLabel,
                    'enrolled' => $enrolledCount,
                    'completed' => $completedCount,
                ];
            }
            return $trends;
        });

        // Recent enrollments requiring trainer attention
        $recentEnrollments = DB::table('training_enrollments')
            ->join('jobseekers', 'training_enrollments.jobseeker_id', '=', 'jobseekers.jobseeker_id')
            ->join('training_programs', 'training_enrollments.training_id', '=', 'training_programs.training_id')
            ->select(
                'training_enrollments.*',
                'jobseekers.first_name',
                'jobseekers.last_name',
                'jobseekers.email as jobseeker_email',
                'training_programs.title as course_title',
                'training_programs.training_type as course_type'
            )
            ->orderBy('training_enrollments.enrollment_id', 'desc')
            ->limit(8)
            ->get();

        return view('trainer.dashboard', compact(
            'totalEnrollments',
            'inProgressEnrollments',
            'completedEnrollments',
            'certificatesIssued',
            'completionRate',
            'coursesCount',
            'topCourses',
            'monthlyTrainingTrends',
            'recentEnrollments'
        ));
    }

    /**
     * Figure 12: Manage Enrollments List.
     */
    public function enrollments(Request $request)
    {
        $query = DB::table('training_enrollments')
            ->join('jobseekers', 'training_enrollments.jobseeker_id', '=', 'jobseekers.jobseeker_id')
            ->join('training_programs', 'training_enrollments.training_id', '=', 'training_programs.training_id')
            ->select(
                'training_enrollments.*',
                'jobseekers.first_name',
                'jobseekers.last_name',
                'jobseekers.email as jobseeker_email',
                'jobseekers.mobile_number',
                'training_programs.title as course_title',
                'training_programs.training_type as course_type'
            );

        if ($request->filled('status')) {
            $request->validate(['status' => 'in:enrolled,in_progress,completed,needs_retake,pending_review']);
            if ($request->status === 'needs_retake') {
                $query->where(function ($subQuery) {
                    $subQuery->where('training_enrollments.status', 'failed')
                        ->orWhere('training_enrollments.answers', 'LIKE', '%"review_status":"needs_retake"%');
                });
            } elseif ($request->status === 'pending_review') {
                $query->where('training_enrollments.status', 'in_progress')
                    ->where('training_enrollments.answers', 'LIKE', '%"review_status":"pending"%');
            } else {
                $query->where('training_enrollments.status', $request->status);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('jobseekers.first_name', 'LIKE', "%{$search}%")
                    ->orWhere('jobseekers.last_name', 'LIKE', "%{$search}%")
                    ->orWhere('training_programs.title', 'LIKE', "%{$search}%")
                    ->orWhere('training_enrollments.certificate_no', 'LIKE', "%{$search}%");
            });
        }

        $enrollments = $query->orderBy('training_enrollments.enrollment_id', 'desc')->paginate(6)->withQueryString();

        return view('trainer.enrollments.index', compact('enrollments'));
    }

    /**
     * Figure 12: Update Enrollment Status.
     */
    public function updateEnrollmentStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:enrolled,in_progress,completed,failed,no_show,multiple_absences',
            'lab_remarks' => 'nullable|string|max:1000',
        ]);

        $enrollment = DB::table('training_enrollments')
            ->join('training_programs', 'training_enrollments.training_id', '=', 'training_programs.training_id')
            ->where('training_enrollments.enrollment_id', $id)
            ->select(
                'training_enrollments.*',
                'training_programs.title as course_title'
            )
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'Enrollment record not found.');
        }

        $updateData = [
            'status' => $request->status,
            'lab_remarks' => $request->lab_remarks,
        ];

        // If the trainer manually resets the status, clear the old assessment data
        if (in_array($request->status, ['enrolled', 'in_progress'])) {
            $updateData['score'] = null;
            $updateData['passed'] = null;
            $updateData['end_date'] = null;
            $updateData['certificate_issued'] = 0;
            $updateData['certificate_no'] = null;
            $updateData['certificate_issued_at'] = null;
            $updateData['answers'] = null;
        }

        if ($request->status === 'completed' && empty($enrollment->end_date)) {
            $updateData['end_date'] = now()->toDateString();
        }

        DB::table('training_enrollments')->where('enrollment_id', $id)->update($updateData);

        // Notify jobseeker
        $jobseeker = DB::table('jobseekers')->where('jobseeker_id', $enrollment->jobseeker_id)->first();
        if ($jobseeker && $jobseeker->user_id) {
            DB::table('notifications')->insert([
                'user_id' => $jobseeker->user_id,
                'title' => 'Enrollment Status Updated',
                'message' => "Your training status for '{$enrollment->course_title}' has been updated to: " . strtoupper(str_replace('_', ' ', $request->status)) . ($request->lab_remarks ? ". Remarks: " . $request->lab_remarks : ""),
                'type' => 'training',
                'is_read' => 0,
                'related_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // THIS WAS MISSING: Redirect back with a success message
        return redirect()->route('trainer.enrollments.index')
            ->with('success', 'Enrollment status updated successfully.');
    }

    /**
     * Figure 12: Evaluate Training Course Answer (Review answers and grade).
     */
    public function evaluateAnswer(Request $request, int $id)
    {
        $enrollment = DB::table('training_enrollments')
            ->join('jobseekers', 'training_enrollments.jobseeker_id', '=', 'jobseekers.jobseeker_id')
            ->join('training_programs', 'training_enrollments.training_id', '=', 'training_programs.training_id')
            ->where('training_enrollments.enrollment_id', $id)
            ->select(
                'training_enrollments.*',
                'jobseekers.first_name',
                'jobseekers.last_name',
                'jobseekers.email as jobseeker_email',
                'training_programs.title as course_title',
                'training_programs.description as course_desc',
                'training_programs.passing_score',
                'training_programs.auto_generate_certificate'
            )
            ->first();

        if (!$enrollment) {
            return redirect()->route('trainer.enrollments.index')->with('error', 'Enrollment not found.');
        }

        $answers = is_array($enrollment->answers)
            ? $enrollment->answers
            : (json_decode($enrollment->answers ?? '[]', true) ?: []);
        if ($request->isMethod('post')) {
            $request->validate([
                'outcome' => 'required|in:passed,needs_retake',
                'trainer_feedback' => 'nullable|string|max:1000',
            ]);
            $passed = $request->outcome === 'passed';
            $status = $passed ? 'completed' : 'in_progress';

            if (($answers['review_status'] ?? null) !== 'pending') {
                return back()->with('error', 'Only assessments awaiting trainer review can be evaluated.');
            }
            $answers['review_status'] = $passed ? 'passed' : 'needs_retake';
            $answers['reviewed_at'] = now()->toIso8601String();
            $answers['reviewed_by'] = Auth::id();

            DB::table('training_enrollments')->where('enrollment_id', $id)->update([
                'score' => data_get($answers, 'objective_score.percentage', $enrollment->score),
                'passed' => $passed ? 1 : 0,
                'status' => $status,
                'trainer_feedback' => $request->trainer_feedback,
                'end_date' => $passed ? now()->toDateString() : null,
                'answers' => json_encode($answers),
            ]);

            // Notify jobseeker
            $jobseeker = DB::table('jobseekers')->where('jobseeker_id', $enrollment->jobseeker_id)->first();
            if ($jobseeker && $jobseeker->user_id) {
                DB::table('notifications')->insert([
                    'user_id' => $jobseeker->user_id,
                    'title' => 'Training Course Evaluated',
                    'message' => "Your assessment for '{$enrollment->course_title}' has been reviewed. Outcome: " . ($passed ? 'PASSED' : 'NEEDS RETAKE') . ($request->trainer_feedback ? ". Trainer feedback: {$request->trainer_feedback}" : ''),
                    'type' => 'training',
                    'is_read' => 0,
                    'related_id' => $id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if ($passed && $enrollment->auto_generate_certificate) {
                return $this->generateCertificate($request, $id);
            }

            return redirect()->route('trainer.enrollments.index')->with(
                'success',
                $passed
                    ? 'Assessment passed. The certificate is ready for trainer issuance.'
                    : 'Assessment reviewed. The learner needs to retake the quiz.'
            );
        }

        if (($answers['review_status'] ?? null) !== 'pending') {
            return redirect()->route('trainer.enrollments.index')
                ->with('info', 'This assessment is not awaiting trainer review.');
        }

        return view('trainer.enrollments.evaluate', compact('enrollment', 'answers'));
    }

    /**
     * Conduct Assessment Interface (Theory & Practical Demonstration).
     */
    public function conductAssessment(Request $request, int $id)
    {
        $enrollment = DB::table('training_enrollments')
            ->join('jobseekers', 'training_enrollments.jobseeker_id', '=', 'jobseekers.jobseeker_id')
            ->join('training_programs', 'training_enrollments.training_id', '=', 'training_programs.training_id')
            ->where('training_enrollments.enrollment_id', $id)
            ->select(
                'training_enrollments.*',
                'jobseekers.first_name',
                'jobseekers.last_name',
                'jobseekers.email as jobseeker_email',
                'jobseekers.mobile_number as jobseeker_phone',
                'training_programs.title as course_title',
                'training_programs.description as course_desc',
                'training_programs.passing_score'
            )
            ->first();

        if (!$enrollment) {
            return redirect()->route('trainer.enrollments.index')->with('error', 'Enrollment not found.');
        }

        $enrollmentAnswers = json_decode($enrollment->answers ?? '[]', true) ?: [];
        if (($enrollmentAnswers['review_status'] ?? null) === 'pending') {
            return redirect()->route('trainer.enrollments.evaluate', $id);
        }
        if ($this->hasTrainerAuthoredAssessments((int) $enrollment->training_id)) {
            return redirect()->route('trainer.enrollments.index')
                ->with('info', 'The learner must submit the course quiz for trainer review before another assessment can be conducted.');
        }
        if (in_array($enrollmentAnswers['review_status'] ?? null, ['passed', 'needs_retake'], true)) {
            return redirect()->route('trainer.enrollments.index')
                ->with('info', 'Use the quiz review decision for this enrollment; a new assessment is available after the learner retakes the quiz.');
        }

        $answers = is_array($enrollment->answers) ? $enrollment->answers : json_decode($enrollment->answers ?? '[]', true);
        $assessments = TrainingAssessment::where('training_id', $enrollment->training_id)->get();

        return view('trainer.enrollments.assessment', compact('enrollment', 'answers', 'assessments'));
    }

    /**
     * Submit Conducted Assessment.
     */
    public function submitAssessment(Request $request, int $id)
    {
        $request->validate([
            'theory_score' => 'nullable|numeric|min:0|max:100',
            'practical_score' => 'nullable|numeric|min:0|max:100',
            'score' => 'required|numeric|min:0|max:100',
            'trainer_feedback' => 'nullable|string|max:1000',
            'lab_remarks' => 'nullable|string|max:1000',
        ]);

        $enrollment = DB::table('training_enrollments')
            ->join('training_programs', 'training_enrollments.training_id', '=', 'training_programs.training_id')
            ->where('training_enrollments.enrollment_id', $id)
            ->select(
                'training_enrollments.*',
                'training_programs.title', // <--- THIS WAS MISSING
                'training_programs.passing_score'
            )
            ->first();

        if (!$enrollment) {
            return redirect()->route('trainer.enrollments.index')->with('error', 'Enrollment not found.');
        }
        if ($this->hasTrainerAuthoredAssessments((int) $enrollment->training_id)) {
            return back()->with('error', 'This course uses trainer-reviewed quiz answers. Review the submitted quiz instead of entering a separate score.');
        }
        $enrollmentAnswers = json_decode($enrollment->answers ?? '[]', true) ?: [];
        if (in_array($enrollmentAnswers['review_status'] ?? null, ['pending', 'passed', 'needs_retake'], true)) {
            return back()->with('error', 'Use the quiz review decision for this enrollment; a new assessment is available after the learner retakes the quiz.');
        }

        $passingThreshold = $enrollment->passing_score ?: 80;
        $score = (float) $request->score;
        $passed = $score >= $passingThreshold;
        $status = $passed ? 'completed' : 'failed';

        $assessmentData = [
            'theory_score' => $request->theory_score,
            'practical_score' => $request->practical_score,
            'final_score' => $score,
            'assessed_by' => Auth::id(),
            'assessed_at' => now()->toIso8601String(),
        ];

        DB::table('training_enrollments')->where('enrollment_id', $id)->update([
            'score' => $score,
            'passed' => $passed ? 1 : 0,
            'status' => $status,
            'trainer_feedback' => $request->trainer_feedback,
            'lab_remarks' => $request->lab_remarks ?: $enrollment->lab_remarks,
            'end_date' => now()->toDateString(),
            'answers' => json_encode($assessmentData),
        ]);

        // If passed, award skills immediately
        if ($passed) {
            $skillName = trim(explode(' - ', $enrollment->title)[0] ?? $enrollment->title);
            $exists = DB::table('jobseeker_skills')
                ->where('jobseeker_id', $enrollment->jobseeker_id)
                ->where('skill_name', $skillName)
                ->exists();

            if (!$exists) {
                DB::table('jobseeker_skills')->insert([
                    'jobseeker_id' => $enrollment->jobseeker_id,
                    'skill_name' => $skillName,
                    'skill_type' => 'technical',
                ]);
            }
        }

        // Notify jobseeker
        $jobseeker = DB::table('jobseekers')->where('jobseeker_id', $enrollment->jobseeker_id)->first();
        if ($jobseeker && $jobseeker->user_id) {
            DB::table('notifications')->insert([
                'user_id' => $jobseeker->user_id,
                'title' => 'Assessment Conducted',
                'message' => "Your assessment for '{$enrollment->title}' has been evaluated. Final Score: {$score}%. Outcome: " . ($passed ? 'PASSED & COMPLETED' : 'NEEDS RETAKE'),
                'type' => 'training',
                'is_read' => 0,
                'related_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // IF PASSED, AUTO-GENERATE THE CERTIFICATE
        if ($passed) {
            // Force refresh the enrollment model to ensure 'passed' and 'completed' are recognized
            $freshEnrollment = DB::table('training_enrollments')->where('enrollment_id', $id)->first();

            // Call generateCertificate and capture the result
            $certificateResult = $this->generateCertificate($request, $id);

            // If generation failed, return the error
            if ($certificateResult instanceof \Illuminate\Http\RedirectResponse && session('error')) {
                return $certificateResult;
            }

            return redirect()->route('trainer.enrollments.index')
                ->with('success', "Assessment submitted! Learner PASSED. Certificate has been generated and issued automatically.");
        }

        return redirect()->route('trainer.enrollments.index')
            ->with('success', "Assessment submitted. Learner needs to RETAKE.");
    }

    /**
     * Mark Enrollment Completion & Award Verified Skills.
     */
    public function markCompletion(Request $request, int $id)
    {
        $enrollment = DB::table('training_enrollments')
            ->join('jobseekers', 'training_enrollments.jobseeker_id', '=', 'jobseekers.jobseeker_id')
            ->join('training_programs', 'training_enrollments.training_id', '=', 'training_programs.training_id')
            ->where('training_enrollments.enrollment_id', $id)
            ->select(
                'training_enrollments.*',
                'jobseekers.first_name',
                'jobseekers.last_name',
                'jobseekers.user_id',
                'training_programs.title as course_title',
                'training_programs.skills as course_skills'
            )
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'Enrollment record not found.');
        }
        // Only require the learner to be marked as passed and completed
        if ((int) $enrollment->passed !== 1 || $enrollment->status !== 'completed') {
            return back()->with('error', 'A certificate can only be issued after the learner is marked as Passed.');
        }
        DB::table('training_enrollments')->where('enrollment_id', $id)->update([
            'status' => 'completed',
            'passed' => 1,
            'end_date' => now()->toDateString(),
            'score' => $enrollment->score ?? 100,
        ]);

        // Award skill to jobseeker profile
        $skillsToAward = [];
        if (!empty($enrollment->enrolled_skills)) {
            $skillsToAward = array_merge($skillsToAward, array_map('trim', explode(',', $enrollment->enrolled_skills)));
        }
        if (!empty($enrollment->course_skills)) {
            $skillsToAward = array_merge($skillsToAward, array_map('trim', explode(',', $enrollment->course_skills)));
        }
        $skillsToAward[] = trim(explode(' - ', $enrollment->course_title)[0] ?? $enrollment->course_title);
        $skillsToAward = array_unique(array_filter($skillsToAward));

        foreach ($skillsToAward as $skill) {
            $exists = DB::table('jobseeker_skills')
                ->where('jobseeker_id', $enrollment->jobseeker_id)
                ->where('skill_name', $skill)
                ->exists();

            if (!$exists) {
                DB::table('jobseeker_skills')->insert([
                    'jobseeker_id' => $enrollment->jobseeker_id,
                    'skill_name' => $skill,
                    'skill_type' => 'technical',
                ]);
            }
        }

        // Notify jobseeker
        if ($enrollment->user_id) {
            DB::table('notifications')->insert([
                'user_id' => $enrollment->user_id,
                'title' => '🎉 Course Completed & Skill Verified!',
                'message' => "Congratulations! You have successfully completed '{$enrollment->course_title}'. Verified skills have been added to your profile.",
                'type' => 'training',
                'is_read' => 0,
                'related_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('trainer.enrollments.index')
            ->with('success', "Learner '{$enrollment->first_name} {$enrollment->last_name}' marked as COMPLETED! Verified skills have been awarded.");
    }
    /**
     * Update the certificate content (competencies, title, etc.) for a course.
     */
    public function updateCertificateContent(Request $request, int $id)
    {
        $course = TrainingProgram::findOrFail($id);

        $validated = $request->validate([
            'certificate_title' => 'required|string|max:255',
            'issue_date' => 'required|string|max:255', // <-- ADD THIS
            'competencies' => 'required|string',
        ]);

        $competenciesArray = array_filter(array_map('trim', explode("\n", $validated['competencies'])));

        $course->update([
            'cert_content' => [
                'title' => $validated['certificate_title'],
                'issue_date' => $validated['issue_date'], // <-- ADD THIS
                'competencies' => array_values($competenciesArray),
            ]
        ]);

        return back()->with('success', 'Certificate content updated successfully.');
    }
    public function showCertificateContent($id)
    {
        $course = TrainingProgram::findOrFail($id);
        return view('trainer.courses.certificate', compact('course'));
    }
    /**
     * Figure 12: Generate Completion Certificate.
     */
    public function generateCertificate(Request $request, int $id)
    {
        $enrollment = DB::table('training_enrollments')
            ->join('jobseekers', 'training_enrollments.jobseeker_id', '=', 'jobseekers.jobseeker_id')
            ->join('training_programs', 'training_enrollments.training_id', '=', 'training_programs.training_id')
            ->where('training_enrollments.enrollment_id', $id)
            ->select(
                'training_enrollments.*',
                'jobseekers.first_name',
                'jobseekers.last_name',
                'jobseekers.user_id',
                'training_programs.title as course_title'
            )
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'Enrollment record not found.');
        }

        // The submitAssessment function already validated the outcome. 
        // We just need to ensure it's marked as completed and passed.
        if ((int) $enrollment->passed !== 1 || $enrollment->status !== 'completed') {
            return back()->with('error', 'A certificate can only be issued after the learner is marked as Passed and Completed.');
        }

        // Generate unique certificate ID
        $certNo = 'DMDP-CERT-' . date('Y') . '-' . strtoupper(Str::random(6));

        DB::table('training_enrollments')->where('enrollment_id', $id)->update([
            'certificate_no' => $certNo,
            'certificate_issued' => 1,
            'certificate_issued_at' => now(),
            'status' => 'completed',
            'passed' => 1,
        ]);

        // Automatically award skill tag to jobseeker
        $skillName = trim(explode(' - ', $enrollment->course_title)[0] ?? $enrollment->course_title);
        $skillExists = DB::table('jobseeker_skills')
            ->where('jobseeker_id', $enrollment->jobseeker_id)
            ->where('skill_name', $skillName)
            ->exists();

        if (!$skillExists) {
            DB::table('jobseeker_skills')->insert([
                'jobseeker_id' => $enrollment->jobseeker_id,
                'skill_name' => $skillName,
                'skill_type' => 'technical',
            ]);
        }

        // Store certificate in jobseeker Document Hub vault (training_certificates)
        $jobseekerDetail = DB::table('jobseeker_details')->where('jobseeker_id', $enrollment->jobseeker_id)->first();
        $existingCerts = [];
        if ($jobseekerDetail && !empty($jobseekerDetail->training_certificates)) {
            $existingCerts = is_array($jobseekerDetail->training_certificates)
                ? $jobseekerDetail->training_certificates
                : (json_decode($jobseekerDetail->training_certificates, true) ?: []);
        }

        // Filter out if this enrollment was previously added
        $existingCerts = array_values(array_filter($existingCerts, fn($c) => ($c['enrollment_id'] ?? null) != $id));

        $certUrl = route('jobseeker.certificates.preview', $id);

        $newCertDoc = [
            'id' => 'cert_' . $id,
            'enrollment_id' => $id,
            'category' => 'certificate',
            'name' => "Certificate of Completion - {$enrollment->course_title} (#{$certNo})",
            'file_url' => $certUrl,
            'status' => 'verified',
            'certificate_no' => $certNo,
            'course_title' => $enrollment->course_title,
            'uploaded_at' => now()->toIso8601String(),
        ];
        $existingCerts[] = $newCertDoc;

        DB::table('jobseeker_details')->updateOrInsert(
            ['jobseeker_id' => $enrollment->jobseeker_id],
            [
                'training_certificates' => json_encode($existingCerts),
            ]
        );

        // Send celebratory notification with download details
        if ($enrollment->user_id) {
            DB::table('notifications')->insert([
                'user_id' => $enrollment->user_id,
                'title' => '🎓 Official Certificate Issued!',
                'message' => "Congratulations! Trainer has issued your official Certificate of Completion (#{$certNo}) for {$enrollment->course_title}. '{$skillName}' has been added to your profile skills, and your certificate is ready to download in your Document Hub.",
                'type' => 'training',
                'is_read' => 0,
                'related_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', "Certificate #{$certNo} generated and awarded to {$enrollment->first_name} {$enrollment->last_name}.");
    }

    /**
     * Preview / Print Certificate.
     */
    public function previewCertificate(int $id)
    {
        $enrollment = DB::table('training_enrollments')
            ->join('jobseekers', 'training_enrollments.jobseeker_id', '=', 'jobseekers.jobseeker_id')
            ->join('training_programs', 'training_enrollments.training_id', '=', 'training_programs.training_id')
            ->where('training_enrollments.enrollment_id', $id)
            ->select(
                'training_enrollments.*',
                'jobseekers.first_name',
                'jobseekers.last_name',
                'training_programs.title as course_title',
                'training_programs.training_type',
                'training_programs.cert_content'

            )
            ->first();

        if (!$enrollment || !$enrollment->certificate_issued) {
            return redirect()->route('trainer.enrollments.index')->with('error', 'Certificate has not been issued for this enrollment.');
        }

        return view('trainer.certificates.preview', compact('enrollment'));
    }

    /**
     * View Training Programs Catalog.
     */
    public function courses(Request $request)
    {
        $query = DB::table('training_programs')
            ->leftJoin('training_enrollments', 'training_programs.training_id', '=', 'training_enrollments.training_id')
            ->leftJoin('training_assessments', 'training_programs.training_id', '=', 'training_assessments.training_id')
            ->select(
                'training_programs.*',
                DB::raw('COUNT(DISTINCT training_enrollments.enrollment_id) as enrolled_count'),
                DB::raw('SUM(CASE WHEN training_enrollments.certificate_issued = 1 THEN 1 ELSE 0 END) as certs_count'),
                DB::raw('COUNT(DISTINCT training_assessments.assessment_id) as assessments_count')
            );

        // Toggle between Active and Archived based on the checkbox
        if ($request->boolean('show_archived')) {
            $query->where('training_programs.is_archived', true);
        } else {
            $query->where('training_programs.is_archived', false);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('training_programs.title', 'LIKE', "%{$search}%")
                    ->orWhere('training_programs.description', 'LIKE', "%{$search}%")
                    ->orWhere('training_programs.skills', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('training_programs.training_type', $request->type);
        }

        $courses = $query->groupBy(
            'training_programs.training_id',
            'training_programs.trainer_id',
            'training_programs.title',
            'training_programs.location',
            'training_programs.is_archived',
            'training_programs.training_type',
            'training_programs.duration_months',
            'training_programs.training_start_date',
            'training_programs.training_end_date',
            'training_programs.trainee_capacity',
            'training_programs.description',
            'training_programs.auto_generate_certificate',
            'training_programs.skills',
            'training_programs.cert_content',
            'training_programs.passing_score',
            'training_programs.demographic_requirements',
            'training_programs.created_at',
            'training_programs.updated_at'
        )
            ->orderBy('training_programs.training_id', 'desc')
            ->paginate(6)
            ->withQueryString();

        return view('trainer.courses.index', compact('courses'));
    }
    /**
     * Show enrolled jobseekers for a course with month and progress filters.
     */
    public function courseEnrollments(Request $request, int $id)
    {
        $validated = $request->validate([
            'month' => ['nullable', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'status' => 'nullable|in:ongoing,completed,pending_review,needs_retake',
        ]);
        $course = TrainingProgram::findOrFail($id);

        $query = DB::table('training_enrollments')
            ->join('jobseekers', 'training_enrollments.jobseeker_id', '=', 'jobseekers.jobseeker_id')
            ->where('training_enrollments.training_id', $course->training_id)
            ->select(
                'training_enrollments.enrollment_id',
                'training_enrollments.status',
                'training_enrollments.start_date',
                'training_enrollments.end_date',
                'training_enrollments.score',
                'training_enrollments.certificate_issued',
                'training_enrollments.answers',
                'jobseekers.first_name',
                'jobseekers.last_name',
                'jobseekers.email as jobseeker_email',
                'jobseekers.mobile_number'
            );

        if (!empty($validated['month'])) {
            $monthStart = \Carbon\Carbon::createFromFormat('!Y-m', $validated['month']);
            $query->whereBetween('training_enrollments.start_date', [
                $monthStart->toDateString(),
                $monthStart->copy()->endOfMonth()->toDateString(),
            ]);
        }

        if (($validated['status'] ?? null) === 'ongoing') {
            $query->whereIn('training_enrollments.status', ['enrolled', 'in_progress'])
                ->where(function ($subQuery) {
                    $subQuery->whereNull('training_enrollments.answers')
                        ->orWhere('training_enrollments.answers', 'NOT LIKE', '%"review_status":"pending"%');
                })
                ->where(function ($subQuery) {
                    $subQuery->whereNull('training_enrollments.answers')
                        ->orWhere('training_enrollments.answers', 'NOT LIKE', '%"review_status":"needs_retake"%');
                });
        } elseif (($validated['status'] ?? null) === 'completed') {
            $query->where('training_enrollments.status', 'completed');
        } elseif (($validated['status'] ?? null) === 'needs_retake') {
            $query->where(function ($subQuery) {
                $subQuery->where('training_enrollments.status', 'failed')
                    ->orWhere('training_enrollments.answers', 'LIKE', '%"review_status":"needs_retake"%');
            });
        } elseif (($validated['status'] ?? null) === 'pending_review') {
            $query->where('training_enrollments.status', 'in_progress')
                ->where('training_enrollments.answers', 'LIKE', '%"review_status":"pending"%');
        }

        $enrollments = $query
            ->orderByDesc('training_enrollments.enrollment_id')
            ->paginate(6)
            ->withQueryString();

        return view('trainer.courses.enrollments', compact('course', 'enrollments'));
    }

    /**
     * Show detailed view of a Course, its Curriculum, Enrollees, and Assessments.
     */
    public function showCourse(int $id)
    {
        $course = TrainingProgram::with('trainer')->findOrFail($id);
        $topics = TrainingTopic::where('training_id', $id)->orderBy('topic_order')->get();
        $assessments = TrainingAssessment::where('training_id', $id)->orderBy('assessment_id')->get();

        $enrollments = DB::table('training_enrollments')
            ->join('jobseekers', 'training_enrollments.jobseeker_id', '=', 'jobseekers.jobseeker_id')
            ->where('training_enrollments.training_id', $id)
            ->select(
                'training_enrollments.*',
                'jobseekers.first_name',
                'jobseekers.last_name',
                'jobseekers.email as jobseeker_email'
            )
            ->orderBy('training_enrollments.enrollment_id', 'desc')
            ->get();

        return view('trainer.courses.show', compact('course', 'topics', 'assessments', 'enrollments'));
    }

    /**
     * Store a newly created Training Course.
     * Note: Assessments are NOT built-in or automatically generated; the trainer creates them separately.
     */
    public function storeCourse(Request $request)
    {
        $this->validateCourseRequest($request, true);

        $trainerProfileId = $this->getProfileId();

        $course = TrainingProgram::create([
            'trainer_id' => $trainerProfileId,
            'title' => trim($request->title),
            'training_type' => $request->training_type,
            'training_start_date' => $request->training_start_date,
            'training_end_date' => $request->training_end_date,
            'location' => $request->location,
            'trainee_capacity' => (int) $request->trainee_capacity,
            'description' => trim($request->description),
            'skills' => $request->skills ? trim($request->skills) : null,
            'passing_score' => $request->filled('passing_score') ? (int) $request->passing_score : 80,
            'demographic_requirements' => $this->demographicRequirementsFromRequest($request),
            'auto_generate_certificate' => false,
        ]);

        // Insert initial topics if provided (NO dummy questions generated)
        if ($request->has('topics') && is_array($request->topics)) {
            $order = 1;
            foreach ($request->topics as $t) {
                if (!empty($t['title'])) {
                    DB::table('training_topics')->insert([
                        'training_id' => $course->training_id,
                        'title' => trim($t['title']),
                        'video_url' => !empty($t['video_url']) ? trim($t['video_url']) : null,
                        'topic_order' => $order++,
                        'questions' => null, // Assessments must be created explicitly by trainer
                    ]);
                }
            }
        }

        return redirect()->route('trainer.courses.show', $course->training_id)
            ->with('warning', "Training course '{$course->title}' was created. Please manage its assessments before learners begin the course.");
    }

    /**
     * Update an existing Training Course.
     */
    public function updateCourse(Request $request, int $id)
    {
        $course = TrainingProgram::findOrFail($id);

        $this->validateCourseRequest($request);

        $course->update([
            'title' => trim($request->title),
            'training_type' => $request->training_type,
            'training_start_date' => $request->training_start_date,
            'training_end_date' => $request->training_end_date,
            'location' => $request->location,
            'trainee_capacity' => (int) $request->trainee_capacity,
            'description' => trim($request->description),
            'skills' => $request->skills ? trim($request->skills) : null,
            'passing_score' => $request->filled('passing_score') ? (int) $request->passing_score : 80,
            'demographic_requirements' => $this->demographicRequirementsFromRequest($request),
            'auto_generate_certificate' => false,
        ]);

        return back()->with('success', "Training course '{$course->title}' updated successfully.");
    }

    /**
     * Delete a Training Course.
     */
    public function destroyCourse(int $id)
    {
        $course = TrainingProgram::findOrFail($id);

        // Check active trainees
        $activeCount = TrainingEnrollment::where('training_id', $id)
            ->whereIn('status', ['enrolled', 'in_progress'])
            ->count();

        if ($activeCount > 0) {
            return back()->with('error', "Cannot delete course '{$course->title}' because it has {$activeCount} active trainee(s). Complete or transfer enrollments first.");
        }

        // Check if any learner has already received a certificate
        $certifiedCount = TrainingEnrollment::where('training_id', $id)
            ->where('certificate_issued', 1)
            ->count();

        if ($certifiedCount > 0) {
            return back()->with('error', "Cannot delete course '{$course->title}' because {$certifiedCount} learner(s) have already received their official certificates. You can close the course instead of deleting it to preserve their records.");
        }
    }
    public function archiveCourse(int $id)
    {
        $course = TrainingProgram::findOrFail($id);
        $course->update(['is_archived' => true]);

        return redirect()->route('trainer.courses')
            ->with('success', "Course '{$course->title}' has been archived. It is now hidden from the public catalog, but learner certificates remain valid.");
    }
    // =========================================================================
    // 2. ASSESSMENT CREATION & MANAGEMENT (Strictly Trainer-Authored)
    // =========================================================================

    /**
     * Store a trainer-authored Assessment Question.
     */
    public function storeAssessment(Request $request, int $id)
    {
        $course = TrainingProgram::findOrFail($id);

        $rules = [
            'question' => 'required|string|max:1000',
            'question_type' => 'required|in:multiple_choice,true_false,essay',
            'correct_answer' => 'required_if:question_type,multiple_choice,true_false|integer|min:0',
            'explanation' => 'nullable|string|max:1000',
            'points' => 'nullable|integer|min:1|max:10',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ];

        // Only require the options array if it's a Multiple Choice or True/False question
        if ($request->input('question_type') !== 'essay') {
            $rules['options'] = 'required|array|min:2|max:6';
            $rules['options.*'] = 'required|string|max:300';
        }

        $validated = $request->validate($rules);

        $options = $validated['question_type'] === 'essay' ? [] : array_values($validated['options']);
        if ($validated['question_type'] !== 'essay' && $validated['correct_answer'] >= count($options)) {
            return back()->withErrors(['correct_answer' => 'Choose one of the listed answer choices.'])->withInput();
        }

        TrainingAssessment::create([
            'training_id' => $course->training_id,
            'question' => trim($validated['question']),
            'image_path' => $request->file('image')?->store('training_assessments', 'public'),
            'question_type' => $validated['question_type'],
            'options' => $options,
            'correct_answer' => $validated['question_type'] === 'essay' ? 0 : (int) $validated['correct_answer'],
            'explanation' => !empty($validated['explanation']) ? trim($validated['explanation']) : null,
            'points' => $request->filled('points') ? (int) $request->points : 1,
        ]);

        return back()->with('success', 'Assessment question added successfully.');
    }

    /**
     * Update an existing Assessment Question.
     */
    public function updateAssessment(Request $request, int $id, int $assessmentId)
    {
        $assessment = TrainingAssessment::where('training_id', $id)->where('assessment_id', $assessmentId)->firstOrFail();

        $validated = $request->validate([
            'question' => 'required|string|max:1000',
            'question_type' => 'required|in:multiple_choice,true_false,essay',
            'options' => 'required_if:question_type,multiple_choice,true_false|array|min:2|max:6',
            'options.*' => 'required|string|max:300',
            'correct_answer' => 'required_if:question_type,multiple_choice,true_false|integer|min:0',
            'explanation' => 'nullable|string|max:1000',
            'points' => 'nullable|integer|min:1|max:10',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'remove_image' => 'nullable|boolean',
        ]);

        $options = $validated['question_type'] === 'essay' ? [] : array_values($validated['options']);
        if ($validated['question_type'] !== 'essay' && $validated['correct_answer'] >= count($options)) {
            return back()->withErrors(['correct_answer' => 'Choose one of the listed answer choices.'])->withInput();
        }

        $imagePath = $assessment->image_path;
        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('training_assessments', 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = null;
        }

        $assessment->update([
            'question' => trim($validated['question']),
            'image_path' => $imagePath,
            'question_type' => $validated['question_type'],
            'options' => $options,
            'correct_answer' => $validated['question_type'] === 'essay' ? 0 : (int) $validated['correct_answer'],
            'explanation' => !empty($validated['explanation']) ? trim($validated['explanation']) : null,
            'points' => $request->filled('points') ? (int) $request->points : 1,
        ]);

        return back()->with('success', 'Assessment question updated successfully.');
    }

    /**
     * Delete an Assessment Question.
     */
    public function destroyAssessment(int $id, int $assessmentId)
    {
        $assessment = TrainingAssessment::where('training_id', $id)->where('assessment_id', $assessmentId)->firstOrFail();
        if ($assessment->image_path) {
            Storage::disk('public')->delete($assessment->image_path);
        }
        $assessment->delete();

        return back()->with('success', 'Assessment question deleted successfully.');
    }

    // =========================================================================
    // 3. COLLABORATOR TRAINER ACCOUNTS (Requires Admin Approval)
    // =========================================================================

    /**
     * List Collaborator Trainer accounts.
     */
    public function collaborators(Request $request)
    {
        $collaborators = DB::table('users')
            ->join('user_profiles', 'users.user_id', '=', 'user_profiles.user_id')
            ->where('users.role', 'trainer')
            ->select(
                'users.user_id',
                'users.email',
                'users.status',
                'users.is_approved',
                'users.created_at',
                'user_profiles.full_name',
                'user_profiles.phone',
                'user_profiles.office',
                'user_profiles.specialization',
                'user_profiles.partner_institution',
                'user_profiles.is_trainer_approved',
                'user_profiles.trainer_type'
            )
            ->orderBy('users.user_id', 'desc')
            ->paginate(6);

        return view('trainer.collaborators.index', compact('collaborators'));
    }

    /**
     * Create a Trainer Account for a Collaborator.
     * Note: Collaborator accounts created by trainers require Admin approval before access is granted.
     */
    public function storeCollaborator(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email|max:150',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:50',
            'office' => 'nullable|string|max:150',
            'specialization' => 'nullable|string|max:150',
            'partner_institution' => 'nullable|string|max:255',
        ]);

        $currentTrainer = Auth::user();
        $currentTrainerProfile = DB::table('user_profiles')->where('user_id', $currentTrainer->user_id)->first();
        $trainerName = $currentTrainerProfile->full_name ?? $currentTrainer->email;

        DB::beginTransaction();
        try {
            // 1. Create User account with status=inactive and is_approved=0 (Awaiting Admin Approval)
            $newUserId = DB::table('users')->insertGetId([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'trainer',
                'status' => 'inactive',
                'is_approved' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. Create User Profile
            DB::table('user_profiles')->insert([
                'user_id' => $newUserId,
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'position' => 'Collaborator Skills Trainer',
                'office' => $request->office ?: 'DMDP Partner Training Provider',
                'specialization' => $request->specialization,
                'trainer_type' => 'partner',
                'partner_institution' => $request->partner_institution ?: 'Accredited Training Partner',
                'is_trainer_approved' => 0,
            ]);

            // 3. Notify Admin users of pending collaborator approval
            $admins = DB::table('users')->where('role', 'admin')->get();
            foreach ($admins as $admin) {
                DB::table('notifications')->insert([
                    'user_id' => $admin->user_id,
                    'title' => 'Collaborator Trainer Account Requires Approval',
                    'message' => "Trainer {$trainerName} has created a collaborator trainer account for '{$request->full_name}' ({$request->email}). Please review and approve this account in User Approvals.",
                    'type' => 'approval',
                    'is_read' => 0,
                    'related_id' => $newUserId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('trainer.collaborators')
                ->with('success', "Collaborator account for '{$request->full_name}' has been created. It is currently PENDING and requires Administrator approval before they can log in.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to register collaborator account: ' . $e->getMessage())->withInput();
        }
    }

    // =========================================================================
    // 4. TRAINING SKILLS ENROLLMENT
    // =========================================================================

    /**
     * View Training Skills Enrollment Console.
     */
    public function skillsEnrollment(Request $request)
    {
        $courses = TrainingProgram::orderBy('title')->get();

        $query = DB::table('jobseekers')
            ->leftJoin('users', 'jobseekers.user_id', '=', 'users.user_id')
            ->select('jobseekers.*', 'users.email as user_email');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('jobseekers.first_name', 'LIKE', "%{$s}%")
                    ->orWhere('jobseekers.last_name', 'LIKE', "%{$s}%")
                    ->orWhere('users.email', 'LIKE', "%{$s}%");
            });
        }

        $jobseekers = $query->orderBy('jobseekers.first_name')->paginate(6)->withQueryString();

        // Load existing enrollments with skills
        $recentEnrollments = DB::table('training_enrollments')
            ->join('jobseekers', 'training_enrollments.jobseeker_id', '=', 'jobseekers.jobseeker_id')
            ->join('training_programs', 'training_enrollments.training_id', '=', 'training_programs.training_id')
            ->select(
                'training_enrollments.*',
                'jobseekers.first_name',
                'jobseekers.last_name',
                'training_programs.title as course_title',
                'training_programs.skills as course_skills'
            )
            ->orderBy('training_enrollments.enrollment_id', 'desc')
            ->limit(10)
            ->get();

        return view('trainer.skills.enrollment', compact('courses', 'jobseekers', 'recentEnrollments'));
    }

    /**
     * Store a Skills Enrollment for a candidate.
     */
    public function storeSkillsEnrollment(Request $request)
    {
        $request->validate([
            'jobseeker_id' => 'required|integer',
            'training_id' => 'required|integer',
            'enrolled_skills' => 'required|string|max:500',
        ]);

        $jobseeker = Jobseeker::findOrFail($request->jobseeker_id);
        $course = TrainingProgram::findOrFail($request->training_id);

        $existing = TrainingEnrollment::where('jobseeker_id', $jobseeker->jobseeker_id)
            ->where('training_id', $course->training_id)
            ->first();

        if ($existing) {
            $existing->enrolled_skills = trim($request->enrolled_skills);
            if ($existing->status === 'failed') {
                $existing->status = 'enrolled';
            }
            $existing->save();
        } else {
            $existing = TrainingEnrollment::create([
                'jobseeker_id' => $jobseeker->jobseeker_id,
                'training_id' => $course->training_id,
                'training_type' => $course->training_type ?: 'online',
                'status' => 'enrolled',
                'start_date' => now()->toDateString(),
                'enrolled_skills' => trim($request->enrolled_skills),
            ]);
        }

        // Notify Jobseeker
        if ($jobseeker->user_id) {
            Notification::create([
                'user_id' => $jobseeker->user_id,
                'title' => 'Enrolled in Skills Training Program',
                'message' => "Your trainer has enrolled you in '{$course->title}' to develop key competencies: {$request->enrolled_skills}.",
                'type' => 'training',
                'is_read' => false,
                'related_id' => $course->training_id,
            ]);
        }

        return back()->with('success', "Candidate {$jobseeker->first_name} {$jobseeker->last_name} successfully enrolled in '{$course->title}' for skill development.");
    }

    // =========================================================================
    // 5. TRAINER PROFILE, UPDATE PROFILE & RESET PASSWORD
    // =========================================================================

    /**
     * Trainer Profile.
     */
    public function profile()
    {
        $user = Auth::user();
        $profile = DB::table('user_profiles')->where('user_id', $user->user_id)->first();

        $coursesCreatedCount = TrainingProgram::where('trainer_id', $profile->profile_id ?? 0)->count();
        $totalTraineesCount = DB::table('training_enrollments')->count();
        $certsAwardedCount = DB::table('training_enrollments')->where('certificate_issued', 1)->count();

        return view('trainer.profile', compact('user', 'profile', 'coursesCreatedCount', 'totalTraineesCount', 'certsAwardedCount'));
    }

    /**
     * Update Trainer Profile.
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:50',
            'office' => 'nullable|string|max:200',
            'specialization' => 'nullable|string|max:150',
            'bio' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        DB::table('user_profiles')->updateOrInsert(
            ['user_id' => $user->user_id],
            [
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'office' => $request->office,
                'specialization' => $request->specialization,
                'bio' => $request->bio,
                'updated_at' => now(),
            ]
        );

        return back()->with('success', 'Trainer profile details updated successfully.');
    }

    /**
     * Reset Password for Trainer.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The provided current password does not match your account password.']);
        }

        DB::table('users')->where('user_id', $user->user_id)->update([
            'password' => Hash::make($request->password),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Your password has been successfully reset! Please use your new password next time you sign in.');
    }

    // =========================================================================
    // 6. NOTIFICATIONS
    // =========================================================================

    /**
     * Notifications.
     */
    public function notifications()
    {
        $user = Auth::user();
        $notifications = DB::table('notifications')
            ->where('user_id', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->paginate(6);

        $unreadCount = DB::table('notifications')
            ->where('user_id', $user->user_id)
            ->where('is_read', 0)
            ->count();

        return view('trainer.notifications', compact('notifications', 'unreadCount'));
    }

    public function markAllNotificationsRead()
    {
        DB::table('notifications')
            ->where('user_id', Auth::id())
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function markNotificationRead(int $id)
    {
        DB::table('notifications')
            ->where('notification_id', $id)
            ->where('user_id', Auth::id())
            ->update(['is_read' => 1]);

        return back()->with('success', 'Notification marked as read.');
    }
}
