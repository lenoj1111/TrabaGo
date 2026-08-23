<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            'coursesCount',
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
     * View Training Programs.
     */
    public function courses()
    {
        $courses = DB::table('training_programs')
            ->leftJoin('training_enrollments', 'training_programs.training_id', '=', 'training_enrollments.training_id')
            ->select(
                'training_programs.*',
                DB::raw('COUNT(training_enrollments.enrollment_id) as enrolled_count'),
                DB::raw('SUM(CASE WHEN training_enrollments.certificate_issued = 1 THEN 1 ELSE 0 END) as certs_count')
            )
            ->groupBy(
                'training_programs.training_id',
                'training_programs.trainer_id',
                'training_programs.title',
                'training_programs.training_type',
                'training_programs.duration_months',
                'training_programs.description'
            )
            ->paginate(10);

        return view('trainer.courses.index', compact('courses'));
    }

    /**
     * Store a newly created Training Course.
     */
    public function storeCourse(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'training_type' => 'required|in:online,laboratory_onsite',
            'duration_months' => 'required|integer|min:1|max:24',
            'description' => 'required|string|max:2000',
            'topics' => 'nullable|array',
            'topics.*.title' => 'nullable|string|max:150',
            'topics.*.video_url' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $profile = DB::table('user_profiles')->where('user_id', $user->user_id)->first();
        if (!$profile) {
            $trainerProfileId = DB::table('user_profiles')->insertGetId([
                'user_id' => $user->user_id,
                'full_name' => explode('@', $user->email)[0],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $trainerProfileId = $profile->profile_id;
        }

        $trainingId = DB::table('training_programs')->insertGetId([
            'trainer_id' => $trainerProfileId,
            'title' => trim($request->title),
            'training_type' => $request->training_type,
            'duration_months' => (int) $request->duration_months,
            'description' => trim($request->description),
        ]);

        // Insert initial topics if provided
        if ($request->has('topics') && is_array($request->topics)) {
            $order = 1;
            foreach ($request->topics as $t) {
                if (!empty($t['title'])) {
                    DB::table('training_topics')->insert([
                        'training_id' => $trainingId,
                        'title' => trim($t['title']),
                        'video_url' => !empty($t['video_url']) ? trim($t['video_url']) : null,
                        'topic_order' => $order++,
                        'questions' => json_encode([
                            [
                                'question' => 'What is the primary core competency taught in ' . trim($t['title']) . '?',
                                'options' => [
                                    'Standard operating procedures and best practices',
                                    'Theoretical concepts only',
                                    'Unverified methodologies',
                                    'None of the above'
                                ],
                                'answer' => 0,
                            ]
                        ]),
                    ]);
                }
            }
        }

        return redirect()->route('trainer.courses')->with('success', 'Training course "' . $request->title . '" created successfully!');
    }

    /**
     * Trainer Profile.
     */
    public function profile()
    {
        $user = Auth::user();
        $profile = DB::table('user_profiles')->where('user_id', $user->user_id)->first();
        return view('trainer.profile', compact('user', 'profile'));
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
        ]);

        $user = Auth::user();
        DB::table('user_profiles')->updateOrInsert(
            ['user_id' => $user->user_id],
            [
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'office' => $request->office,
                'updated_at' => now(),
            ]
        );

        return back()->with('success', 'Trainer profile updated successfully.');
    }

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
