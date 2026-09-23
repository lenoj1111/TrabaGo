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
use Illuminate\Support\Str;

class TrainerPortalController extends Controller
{
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

        $totalEnrollments = DB::table('training_enrollments')->count();
        $inProgressEnrollments = DB::table('training_enrollments')->where('status', 'in_progress')->count();
        $completedEnrollments = DB::table('training_enrollments')->where('status', 'completed')->count();
        $certificatesIssued = DB::table('training_enrollments')->where('certificate_issued', 1)->count();
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

        // Monthly enrollment trends (last 6 months)
        $monthlyTrainingTrends = [];
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

            $monthlyTrainingTrends[] = [
                'month' => $monthLabel,
                'enrolled' => $enrolledCount,
                'completed' => $completedCount,
            ];
        }

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
            $query->where('training_enrollments.status', $request->status);
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

        $enrollments = $query->orderBy('training_enrollments.enrollment_id', 'desc')->paginate(12)->withQueryString();

        return view('trainer.enrollments.index', compact('enrollments'));
    }

    /**
     * Figure 12: Update Enrollment Status.
     */
    public function updateEnrollmentStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:enrolled,in_progress,completed,failed',
            'lab_remarks' => 'nullable|string|max:1000',
        ]);

        $enrollment = DB::table('training_enrollments')->where('enrollment_id', $id)->first();
        if (!$enrollment) {
            return back()->with('error', 'Enrollment record not found.');
        }

        $updateData = [
            'status' => $request->status,
            'lab_remarks' => $request->lab_remarks,
        ];

        if ($request->status === 'completed' && empty($enrollment->end_date)) {
            $updateData['end_date'] = now()->toDateString();
        }

        DB::table('training_enrollments')->where('enrollment_id', $id)->update($updateData);

        // Notify jobseeker
        $jobseeker = DB::table('jobseekers')->where('jobseeker_id', $enrollment->jobseeker_id)->first();
        if ($jobseeker && $jobseeker->user_id) {
            DB::table('notifications')->insert([
                'user_id' => $jobseeker->user_id,
                'title' => 'Training Enrollment Updated',
                'message' => "Your training status has been updated to: " . strtoupper($request->status) . ($request->lab_remarks ? ". Remarks: " . $request->lab_remarks : ""),
                'type' => 'training',
                'is_read' => 0,
                'related_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Enrollment status updated successfully.');
    }

    /**
     * Figure 12: Evaluate Training Course Answer (Review answers and grade).
     */
    public function evaluateAnswer(Request $request, $id)
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
                'training_programs.description as course_desc'
            )
            ->first();

        if (!$enrollment) {
            return redirect()->route('trainer.enrollments.index')->with('error', 'Enrollment not found.');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'score' => 'required|numeric|min:0|max:100',
                'trainer_feedback' => 'nullable|string|max:1000',
            ]);

            $passed = $request->score >= 80;
            $status = $passed ? 'completed' : 'failed';

            DB::table('training_enrollments')->where('enrollment_id', $id)->update([
                'score' => $request->score,
                'passed' => $passed ? 1 : 0,
                'status' => $status,
                'trainer_feedback' => $request->trainer_feedback,
                'end_date' => now()->toDateString(),
            ]);

            // Notify jobseeker
            $jobseeker = DB::table('jobseekers')->where('jobseeker_id', $enrollment->jobseeker_id)->first();
            if ($jobseeker && $jobseeker->user_id) {
                DB::table('notifications')->insert([
                    'user_id' => $jobseeker->user_id,
                    'title' => 'Training Course Evaluated',
                    'message' => "Your assessment for '{$enrollment->course_title}' has been evaluated. Score: {$request->score}%. Status: " . ($passed ? 'PASSED' : 'NEEDS IMPROVEMENT'),
                    'type' => 'training',
                    'is_read' => 0,
                    'related_id' => $id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return redirect()->route('trainer.enrollments.index')->with('success', 'Assessment evaluation saved successfully.');
        }

        $answers = is_array($enrollment->answers) ? $enrollment->answers : json_decode($enrollment->answers ?? '[]', true);

        return view('trainer.enrollments.evaluate', compact('enrollment', 'answers'));
    }

    /**
     * Conduct Assessment Interface (Theory & Practical Demonstration).
     */
    public function conductAssessment(Request $request, $id)
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

        $answers = is_array($enrollment->answers) ? $enrollment->answers : json_decode($enrollment->answers ?? '[]', true);
        $assessments = TrainingAssessment::where('training_id', $enrollment->training_id)->get();

        return view('trainer.enrollments.assessment', compact('enrollment', 'answers', 'assessments'));
    }

    /**
     * Submit Conducted Assessment.
     */
    public function submitAssessment(Request $request, $id)
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
            ->first();

        if (!$enrollment) {
            return redirect()->route('trainer.enrollments.index')->with('error', 'Enrollment not found.');
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

        return redirect()->route('trainer.enrollments.index')
            ->with('success', "Assessment submitted successfully! Score: {$score}% (" . ($passed ? 'PASSED' : 'NEEDS RETAKE') . ").");
    }

    /**
     * Mark Enrollment Completion & Award Verified Skills.
     */
    public function markCompletion(Request $request, $id)
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
     * Figure 12: Generate Completion Certificate.
     */
    public function generateCertificate(Request $request, $id)
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
    public function previewCertificate($id)
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
                'training_programs.training_type'
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
                'training_programs.training_type',
                'training_programs.duration_months',
                'training_programs.description',
                'training_programs.auto_generate_certificate',
                'training_programs.skills',
                'training_programs.passing_score',
                'training_programs.created_at',
                'training_programs.updated_at'
            )
            ->orderBy('training_programs.training_id', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('trainer.courses.index', compact('courses'));
    }

    /**
     * Show detailed view of a Course, its Curriculum, Enrollees, and Assessments.
     */
    public function showCourse($id)
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
        $request->validate([
            'title' => 'required|string|max:150',
            'training_type' => 'required|in:online,laboratory_onsite',
            'duration_months' => 'required|integer|min:1|max:24',
            'description' => 'required|string|max:2000',
            'skills' => 'nullable|string|max:500',
            'passing_score' => 'nullable|integer|min:50|max:100',
            'auto_generate_certificate' => 'nullable',
            'topics' => 'nullable|array',
            'topics.*.title' => 'nullable|string|max:150',
            'topics.*.video_url' => 'nullable|string|max:255',
        ]);

        $trainerProfileId = $this->getProfileId();

        $autoCert = $request->has('auto_generate_certificate') ? (bool) $request->auto_generate_certificate : true;

        $course = TrainingProgram::create([
            'trainer_id' => $trainerProfileId,
            'title' => trim($request->title),
            'training_type' => $request->training_type,
            'duration_months' => (int) $request->duration_months,
            'description' => trim($request->description),
            'skills' => $request->skills ? trim($request->skills) : null,
            'passing_score' => $request->filled('passing_score') ? (int) $request->passing_score : 80,
            'auto_generate_certificate' => $autoCert,
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

        return redirect()->route('trainer.courses')
            ->with('success', "Training course '{$course->title}' created successfully! You can now author assessment questions.");
    }

    /**
     * Update an existing Training Course.
     */
    public function updateCourse(Request $request, $id)
    {
        $course = TrainingProgram::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:150',
            'training_type' => 'required|in:online,laboratory_onsite',
            'duration_months' => 'required|integer|min:1|max:24',
            'description' => 'required|string|max:2000',
            'skills' => 'nullable|string|max:500',
            'passing_score' => 'nullable|integer|min:50|max:100',
            'auto_generate_certificate' => 'nullable',
        ]);

        $autoCert = $request->has('auto_generate_certificate') ? (bool) $request->auto_generate_certificate : true;

        $course->update([
            'title' => trim($request->title),
            'training_type' => $request->training_type,
            'duration_months' => (int) $request->duration_months,
            'description' => trim($request->description),
            'skills' => $request->skills ? trim($request->skills) : null,
            'passing_score' => $request->filled('passing_score') ? (int) $request->passing_score : 80,
            'auto_generate_certificate' => $autoCert,
        ]);

        return back()->with('success', "Training course '{$course->title}' updated successfully.");
    }

    /**
     * Delete a Training Course.
     */
    public function destroyCourse($id)
    {
        $course = TrainingProgram::findOrFail($id);

        // Check active trainees
        $activeCount = TrainingEnrollment::where('training_id', $id)
            ->whereIn('status', ['enrolled', 'in_progress'])
            ->count();

        if ($activeCount > 0) {
            return back()->with('error', "Cannot delete course '{$course->title}' because it has {$activeCount} active trainee(s). Complete or transfer enrollments first.");
        }

        // Clean cascade
        TrainingAssessment::where('training_id', $id)->delete();
        TrainingTopic::where('training_id', $id)->delete();
        TrainingEnrollment::where('training_id', $id)->delete();
        $course->delete();

        return redirect()->route('trainer.courses')->with('success', "Training course '{$course->title}' deleted successfully.");
    }

    // =========================================================================
    // 2. ASSESSMENT CREATION & MANAGEMENT (Strictly Trainer-Authored)
    // =========================================================================

    /**
     * Store a trainer-authored Assessment Question.
     */
    public function storeAssessment(Request $request, $id)
    {
        $course = TrainingProgram::findOrFail($id);

        $request->validate([
            'question' => 'required|string|max:1000',
            'question_type' => 'nullable|string|in:multiple_choice,true_false',
            'options' => 'required|array|min:2|max:6',
            'options.*' => 'required|string|max:300',
            'correct_answer' => 'required|integer|min:0',
            'explanation' => 'nullable|string|max:1000',
            'points' => 'nullable|integer|min:1|max:10',
        ]);

        TrainingAssessment::create([
            'training_id' => $course->training_id,
            'question' => trim($request->question),
            'question_type' => $request->question_type ?: 'multiple_choice',
            'options' => array_values($request->options),
            'correct_answer' => (int) $request->correct_answer,
            'explanation' => $request->explanation ? trim($request->explanation) : null,
            'points' => $request->filled('points') ? (int) $request->points : 1,
        ]);

        return back()->with('success', 'Assessment question added successfully.');
    }

    /**
     * Update an existing Assessment Question.
     */
    public function updateAssessment(Request $request, $id, $assessmentId)
    {
        $assessment = TrainingAssessment::where('training_id', $id)->where('assessment_id', $assessmentId)->firstOrFail();

        $request->validate([
            'question' => 'required|string|max:1000',
            'options' => 'required|array|min:2|max:6',
            'options.*' => 'required|string|max:300',
            'correct_answer' => 'required|integer|min:0',
            'explanation' => 'nullable|string|max:1000',
            'points' => 'nullable|integer|min:1|max:10',
        ]);

        $assessment->update([
            'question' => trim($request->question),
            'options' => array_values($request->options),
            'correct_answer' => (int) $request->correct_answer,
            'explanation' => $request->explanation ? trim($request->explanation) : null,
            'points' => $request->filled('points') ? (int) $request->points : 1,
        ]);

        return back()->with('success', 'Assessment question updated successfully.');
    }

    /**
     * Delete an Assessment Question.
     */
    public function destroyAssessment($id, $assessmentId)
    {
        $assessment = TrainingAssessment::where('training_id', $id)->where('assessment_id', $assessmentId)->firstOrFail();
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
            ->paginate(15);

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

        $jobseekers = $query->orderBy('jobseekers.first_name')->paginate(12)->withQueryString();

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
            ->paginate(15);

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

    public function markNotificationRead($id)
    {
        DB::table('notifications')
            ->where('notification_id', $id)
            ->where('user_id', Auth::id())
            ->update(['is_read' => 1]);

        return back()->with('success', 'Notification marked as read.');
    }
}
