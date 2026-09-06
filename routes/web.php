<?php

use App\Http\Controllers\Admin\ApprovalsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JobPostingController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Employer\EmployerPortalController;
use App\Http\Controllers\EmployerRegistrationController;
use App\Http\Controllers\Jobseeker\JobseekerPortalController;
use App\Http\Controllers\JobseekerRegistrationController;
use App\Http\Controllers\Jpo\JpoPortalController;
use App\Http\Controllers\Lmo\LmoPortalController;
use App\Http\Controllers\Supervisor\SupervisorPortalController;
use App\Http\Controllers\Trainer\TrainerPortalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('homepage');
})->name('home');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Jobseeker Registration Routes (Public)
|--------------------------------------------------------------------------
*/

Route::get('/jobseeker/register', [JobseekerRegistrationController::class, 'showRegistrationForm'])->name('jobseeker.register');
Route::post('/jobseeker/register', [JobseekerRegistrationController::class, 'register'])->name('jobseeker.register.post');
Route::get('/jobseeker/register/success', [JobseekerRegistrationController::class, 'success'])->name('jobseeker.register.success');

/*
|--------------------------------------------------------------------------
| Employer Registration Routes (Public)
|--------------------------------------------------------------------------
*/

Route::get('/employer/register', [EmployerRegistrationController::class, 'showRegistrationForm'])->name('employer.register');
Route::post('/employer/register', [EmployerRegistrationController::class, 'register'])->name('employer.register.post');
Route::get('/employer/register/success', [EmployerRegistrationController::class, 'success'])->name('employer.register.success');

/*
|--------------------------------------------------------------------------
| 1. Jobseeker Web Routes (Figure 7 - Web & Mobile)
|--------------------------------------------------------------------------
*/

Route::prefix('jobseeker')->name('jobseeker.')->group(function () {

    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::middleware(['auth'])->group(function () {
        // Home & Dashboard
        Route::get('/home', [JobseekerPortalController::class, 'index'])->name('home');

        // Job Search & AI Matching
        Route::get('/jobs', [JobseekerPortalController::class, 'jobs'])->name('jobs');
        Route::get('/jobs/{id}', [JobseekerPortalController::class, 'jobShow'])->name('jobs.show');
        Route::post('/jobs/{id}/apply', [JobseekerPortalController::class, 'apply'])->name('jobs.apply');

        // Application Pipeline & Tracking
        Route::get('/applications', [JobseekerPortalController::class, 'applications'])->name('applications');
        Route::post('/applications/{id}/accept-offer', [JobseekerPortalController::class, 'acceptOffer'])->name('applications.accept_offer');
        Route::post('/applications/{id}/decline-offer', [JobseekerPortalController::class, 'declineOffer'])->name('applications.decline_offer');
        Route::delete('/applications/{id}/withdraw', [JobseekerPortalController::class, 'withdrawApplication'])->name('applications.withdraw');
        Route::post('/resignation/request', [JobseekerPortalController::class, 'requestResignation'])->name('resignation.request');

        // Training Programs, Lessons & Quizzes
        Route::get('/training', [JobseekerPortalController::class, 'trainingIndex'])->name('training');
        Route::get('/training/skills', [JobseekerPortalController::class, 'trainingSkills'])->name('training.skills');
        Route::get('/training/enrollments', [JobseekerPortalController::class, 'trainingEnrollments'])->name('training.enrollments');
        Route::post('/training/{id}/enroll', [JobseekerPortalController::class, 'enrollTraining'])->name('training.enroll');
        Route::get('/training/{id}', [JobseekerPortalController::class, 'trainingShow'])->name('training.show');
        Route::get('/training/{id}/quiz', [JobseekerPortalController::class, 'trainingQuiz'])->name('training.quiz');
        Route::post('/training/{id}/quiz', [JobseekerPortalController::class, 'submitQuiz'])->name('training.quiz.submit');
        Route::get('/certificates/{id}/preview', [JobseekerPortalController::class, 'previewCertificate'])->name('certificates.preview');

        // Document Hub & Verification
        Route::get('/documents', [JobseekerPortalController::class, 'documents'])->name('documents');
        Route::post('/documents/upload', [JobseekerPortalController::class, 'uploadDocument'])->name('documents.upload');
        Route::delete('/documents/{category}', [JobseekerPortalController::class, 'deleteDocument'])->name('documents.delete');

        // Notification Center
        Route::get('/notifications', [JobseekerPortalController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/{id}/read', [JobseekerPortalController::class, 'markNotificationRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [JobseekerPortalController::class, 'markAllNotificationsRead'])->name('notifications.read_all');

        // Profile & Skills Matrix
        Route::get('/profile', [JobseekerPortalController::class, 'profile'])->name('profile');
        Route::post('/profile/update', [JobseekerPortalController::class, 'updateProfile'])->name('profile.update_info');
        Route::put('/profile', [JobseekerPortalController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/skills/sync', [JobseekerPortalController::class, 'syncSkills'])->name('profile.update_skills');
        Route::post('/profile/skills', [JobseekerPortalController::class, 'addSkill'])->name('profile.skills.add');
        Route::delete('/profile/skills/{id}', [JobseekerPortalController::class, 'removeSkill'])->name('profile.skills.remove');
        Route::post('/password/change', [JobseekerPortalController::class, 'changePassword'])->name('password.change');
    });
});

/*
|--------------------------------------------------------------------------
| 2. Employer Routes (Figure 9)
|--------------------------------------------------------------------------
*/

Route::prefix('employer')->name('employer.')->group(function () {

    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::middleware(['auth'])->group(function () {
        // Dashboard
        Route::get('/home', [EmployerPortalController::class, 'homepage'])->name('home');
        Route::get('/dashboard', [EmployerPortalController::class, 'homepage'])->name('dashboard');

        // 1. Manage Job Postings (Full CRUD & Administration Review)
        Route::get('/job-postings', [EmployerPortalController::class, 'jobPostings'])->name('job-postings');
        Route::get('/job-postings/create', [EmployerPortalController::class, 'createJobPosting'])->name('job-postings.create');
        Route::post('/job-postings', [EmployerPortalController::class, 'storeJobPosting'])->name('job-postings.store');
        Route::get('/job-postings/{id}', [EmployerPortalController::class, 'showJobPosting'])->name('job-postings.show');
        Route::get('/job-postings/{id}/edit', [EmployerPortalController::class, 'editJobPosting'])->name('job-postings.edit');
        Route::put('/job-postings/{id}', [EmployerPortalController::class, 'updateJobPosting'])->name('job-postings.update');
        Route::post('/job-postings/{id}/update', [EmployerPortalController::class, 'updateJobPosting'])->name('job-postings.update.post');
        Route::delete('/job-postings/{id}', [EmployerPortalController::class, 'destroyJobPosting'])->name('job-postings.destroy');
        Route::post('/job-postings/{id}/close', [EmployerPortalController::class, 'closeJobPosting'])->name('job-postings.close');

        // 2. Pass Accreditation Papers (Send to JPO)
        Route::get('/accreditation', [EmployerPortalController::class, 'accreditation'])->name('accreditation');
        Route::post('/accreditation/upload', [EmployerPortalController::class, 'submitAccreditation'])->name('accreditation.upload');
        Route::get('/accreditation/print', [EmployerPortalController::class, 'printAccreditation'])->name('accreditation.print');

        // 3. Review Referred Jobseekers (From JPO) & Schedule Interview / Hire / Mark Not Qualified
        Route::get('/referred-jobseekers', [EmployerPortalController::class, 'referredJobseekers'])->name('referred-jobseekers');
        Route::get('/applications', [EmployerPortalController::class, 'referredJobseekers'])->name('applications');
        Route::post('/applicants/{id}/status', [EmployerPortalController::class, 'updateApplicantStatus'])->name('applicants.update_status');
        Route::post('/applicants/{id}/resignation', [EmployerPortalController::class, 'respondResignation'])->name('applicants.resignation');

        // 4. Generate & View Placement Reports (Send to JPO)
        Route::get('/placement-reports', [EmployerPortalController::class, 'placementReports'])->name('placement-reports');
        Route::post('/placement-reports/generate', [EmployerPortalController::class, 'generatePlacementReport'])->name('placement-reports.generate');
        Route::get('/placement-reports/{id}', [EmployerPortalController::class, 'showPlacementReport'])->name('placement-reports.show');
        Route::get('/placement-reports/{id}/print', [EmployerPortalController::class, 'showPlacementReport'])->name('placement-reports.print');

        // 5. Company Profile & Password Management
        Route::get('/profile', [EmployerPortalController::class, 'profile'])->name('profile');
        Route::post('/profile/update', [EmployerPortalController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile', [EmployerPortalController::class, 'updateProfile'])->name('profile.update.put');
        Route::post('/password/reset', [EmployerPortalController::class, 'resetPassword'])->name('password.reset');
        Route::post('/password/change', [EmployerPortalController::class, 'resetPassword'])->name('password.change');

        // 6. Notifications Center
        Route::get('/notifications', [EmployerPortalController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/read-all', [EmployerPortalController::class, 'markAllNotificationsRead'])->name('notifications.read_all');
        Route::post('/notifications/{id}/read', [EmployerPortalController::class, 'markNotificationRead'])->name('notifications.read');
    });
});

/*
|--------------------------------------------------------------------------
| 3. Job Placement Officer (JPO) Routes (Figure 8)
|--------------------------------------------------------------------------
*/

Route::prefix('jpo')->name('jpo.')->middleware(['auth', 'jpo'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [JpoPortalController::class, 'dashboard'])->name('dashboard');

    // 1. Evaluate Jobseekers (Refer to Employer) & NSRP Form
    Route::get('/evaluations/jobseekers', [JpoPortalController::class, 'evaluateJobseekers'])->name('evaluations.jobseekers');
    Route::post('/evaluations/jobseekers/{id}/refer', [JpoPortalController::class, 'referJobseeker'])->name('evaluations.jobseekers.refer');
    Route::get('/evaluations/jobseekers/{id}/nsrp', [JpoPortalController::class, 'showNsrpForm'])->name('evaluations.jobseekers.nsrp');

    // 2. Evaluate Accreditation Papers (Recommend to Admin)
    Route::get('/evaluations/accreditations', [JpoPortalController::class, 'evaluateAccreditations'])->name('evaluations.accreditations');
    Route::post('/evaluations/accreditations/{id}/recommend', [JpoPortalController::class, 'recommendAccreditation'])->name('evaluations.accreditations.recommend');
    Route::post('/evaluations/accreditations/{id}/document-status', [JpoPortalController::class, 'updateDocumentStatus'])->name('evaluations.accreditations.document-status');
    Route::get('/evaluations/accreditations/{id}/print', [JpoPortalController::class, 'printAccreditation'])->name('evaluations.accreditations.print');

    // 3. Evaluate Placement Reports (Send to Admin)
    Route::get('/evaluations/placement-reports', [JpoPortalController::class, 'evaluatePlacementReports'])->name('evaluations.placement-reports');
    Route::get('/placement-reports', [JpoPortalController::class, 'evaluatePlacementReports'])->name('placement-reports');
    Route::post('/evaluations/placement-reports/{id}/forward', [JpoPortalController::class, 'forwardPlacementReport'])->name('evaluations.placement-reports.forward');
    Route::get('/evaluations/placement-reports/{id}', [JpoPortalController::class, 'showPlacementReport'])->name('evaluations.placement-reports.show');
    Route::get('/evaluations/placement-reports/{id}/print', [JpoPortalController::class, 'showPlacementReport'])->name('evaluations.placement-reports.print');

    // Officer Profile & Security Settings
    Route::get('/profile', [JpoPortalController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [JpoPortalController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [JpoPortalController::class, 'resetPassword'])->name('password.update');
    Route::post('/profile/reset-password', [JpoPortalController::class, 'resetPassword'])->name('profile.password');
    Route::get('/notifications', [JpoPortalController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/read-all', [JpoPortalController::class, 'markAllNotificationsRead'])->name('notifications.read_all');
    Route::post('/notifications/{id}/read', [JpoPortalController::class, 'markNotificationRead'])->name('notifications.read');
});

/*
|--------------------------------------------------------------------------
| 4. PESD Supervisor Routes (Figure 11)
|--------------------------------------------------------------------------
*/

Route::prefix('supervisor')->name('supervisor.')->middleware(['auth'])->group(function () {
    // Supervisor role removed - redirect all traffic to Admin portal
    Route::get('/dashboard', fn() => redirect()->route('admin.dashboard'))->name('dashboard');
    Route::get('/accreditations', fn() => redirect()->route('admin.approvals.index'))->name('accreditations');
    Route::post('/accreditations/{id}/approve', fn() => redirect()->route('admin.approvals.index'))->name('accreditations.approve');
    Route::get('/profile', fn() => redirect()->route('admin.profile'))->name('profile');
    Route::get('/notifications', fn() => redirect()->route('admin.notifications'))->name('notifications');
    Route::any('{any}', fn() => redirect()->route('admin.dashboard'))->where('any', '.*');
});

/*
|--------------------------------------------------------------------------
| 5. Trainer Routes (Figure 12)
|--------------------------------------------------------------------------
*/

Route::prefix('trainer')->name('trainer.')->middleware(['auth', 'trainer'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [TrainerPortalController::class, 'dashboard'])->name('dashboard');

    // 1. Manage Enrollments
    Route::get('/enrollments', [TrainerPortalController::class, 'enrollments'])->name('enrollments.index');

    // 2. Update Enrollment Status & Mark Completion
    Route::post('/enrollments/{id}/status', [TrainerPortalController::class, 'updateEnrollmentStatus'])->name('enrollments.status');
    Route::post('/enrollments/{id}/complete', [TrainerPortalController::class, 'markCompletion'])->name('enrollments.complete');

    // 3. Conduct Assessment / Evaluate Training Course Answer
    Route::get('/enrollments/{id}/assessment', [TrainerPortalController::class, 'conductAssessment'])->name('enrollments.assessment');
    Route::post('/enrollments/{id}/assessment', [TrainerPortalController::class, 'submitAssessment'])->name('enrollments.assessment.submit');
    Route::get('/enrollments/{id}/evaluate', [TrainerPortalController::class, 'evaluateAnswer'])->name('enrollments.evaluate');
    Route::post('/enrollments/{id}/evaluate', [TrainerPortalController::class, 'evaluateAnswer'])->name('enrollments.evaluate.submit');

    // 4. Generate Certificate
    Route::post('/enrollments/{id}/certificate', [TrainerPortalController::class, 'generateCertificate'])->name('enrollments.certificate');
    Route::get('/certificates/{id}/preview', [TrainerPortalController::class, 'previewCertificate'])->name('certificates.preview');

    // Course Modules & Full CRUD
    Route::get('/courses', [TrainerPortalController::class, 'courses'])->name('courses');
    Route::post('/courses', [TrainerPortalController::class, 'storeCourse'])->name('courses.store');
    Route::get('/courses/{id}', [TrainerPortalController::class, 'showCourse'])->name('courses.show');
    Route::put('/courses/{id}', [TrainerPortalController::class, 'updateCourse'])->name('courses.update');
    Route::delete('/courses/{id}', [TrainerPortalController::class, 'destroyCourse'])->name('courses.destroy');

    // Trainer Assessment Question Management (Trainer-authored)
    Route::post('/courses/{id}/assessments', [TrainerPortalController::class, 'storeAssessment'])->name('courses.assessments.store');
    Route::put('/courses/{id}/assessments/{assessmentId}', [TrainerPortalController::class, 'updateAssessment'])->name('courses.assessments.update');
    Route::delete('/courses/{id}/assessments/{assessmentId}', [TrainerPortalController::class, 'destroyAssessment'])->name('courses.assessments.destroy');

    // Collaborator Trainer Accounts (Requires Admin Approval)
    Route::get('/collaborators', [TrainerPortalController::class, 'collaborators'])->name('collaborators');
    Route::post('/collaborators', [TrainerPortalController::class, 'storeCollaborator'])->name('collaborators.store');

    // Training Skills Enrollment
    Route::get('/skills-enrollment', [TrainerPortalController::class, 'skillsEnrollment'])->name('skills.enrollment');
    Route::post('/skills-enrollment', [TrainerPortalController::class, 'storeSkillsEnrollment'])->name('skills.enrollment.store');

    // Profile & Password Reset
    Route::get('/profile', [TrainerPortalController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [TrainerPortalController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/reset-password', [TrainerPortalController::class, 'resetPassword'])->name('password.update');

    // Notifications
    Route::get('/notifications', [TrainerPortalController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/read-all', [TrainerPortalController::class, 'markAllNotificationsRead'])->name('notifications.read_all');
    Route::post('/notifications/{id}/read', [TrainerPortalController::class, 'markNotificationRead'])->name('notifications.read');
});

/*
|--------------------------------------------------------------------------
| 6. Labor Market Information Officer (LMO) Routes (Figure 13)
|--------------------------------------------------------------------------
*/

Route::prefix('lmo')->name('lmo.')->middleware(['auth'])->group(function () {
    // LMO role removed - Admin handles all responsibilities
    Route::get('/dashboard', fn() => redirect()->route('admin.dashboard'))->name('dashboard');
    Route::get('/jobseekers/supervise', fn() => redirect()->route('admin.jobseekers.index'))->name('jobseekers.supervise');
    Route::get('/analytics', fn() => redirect()->route('admin.analytics.index'))->name('analytics.index');
    Route::get('/profile', fn() => redirect()->route('admin.profile'))->name('profile');
    Route::get('/notifications', fn() => redirect()->route('admin.notifications'))->name('notifications');
    Route::any('{any}', fn() => redirect()->route('admin.dashboard'))->where('any', '.*');
});

/*
|--------------------------------------------------------------------------
| 7. Admin Routes (Figure 10)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Manage Approvals Center (3 Pillars: Jobs, Accreditations, Placement Reports)
    Route::get('/approvals', [ApprovalsController::class, 'index'])->name('approvals.index');
    Route::post('/approvals/job-postings/{id}/approve', [ApprovalsController::class, 'approveJobPosting'])->name('approvals.job-postings.approve');
    Route::post('/approvals/job-postings/{id}/reject', [ApprovalsController::class, 'rejectJobPosting'])->name('approvals.job-postings.reject');
    Route::post('/approvals/accreditations/{id}/approve', [ApprovalsController::class, 'approveAccreditation'])->name('approvals.accreditations.approve');
    Route::post('/approvals/accreditations/{id}/reject', [ApprovalsController::class, 'rejectAccreditation'])->name('approvals.accreditations.reject');
    Route::get('/approvals/accreditations/{id}/print', [ApprovalsController::class, 'printAccreditation'])->name('approvals.accreditations.print');
    Route::post('/approvals/placement-reports/{id}/approve', [ApprovalsController::class, 'approvePlacementReport'])->name('approvals.placement-reports.approve');
    Route::post('/approvals/placement-reports/{id}/reject', [ApprovalsController::class, 'rejectPlacementReport'])->name('approvals.placement-reports.reject');
    Route::post('/approvals/trainers/{id}/approve', [ApprovalsController::class, 'approveTrainer'])->name('approvals.trainers.approve');
    Route::post('/approvals/trainers/{id}/reject', [ApprovalsController::class, 'rejectTrainer'])->name('approvals.trainers.reject');

    // Placement Reports Management Directory
    Route::get('/placement-reports', [ApprovalsController::class, 'placementReports'])->name('placement-reports.index');
    Route::get('/placement-reports/{id}', [ApprovalsController::class, 'showPlacementReport'])->name('placement-reports.show');
    Route::get('/placement-reports/{id}/print', [ApprovalsController::class, 'showPlacementReport'])->name('placement-reports.print');

    // View Jobseeker Status Directory
    Route::get('/jobseekers', [ApprovalsController::class, 'jobseekers'])->name('jobseekers.index');

    // Employee Account Management (Create, Activate, Deactivate)
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserManagementController::class, 'update'])->name('users.update');
    Route::post('/users/{id}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/{id}/approve', [UserManagementController::class, 'approve'])->name('users.approve');
    Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');

    // Employer Management
    Route::get('/employers', function (Request $request) {
        $query = DB::table('employers')
            ->leftJoin('users', 'employers.user_id', '=', 'users.user_id')
            ->leftJoin('employer_accreditation', 'employers.employer_id', '=', 'employer_accreditation.employer_id')
            ->select(
                'employers.*',
                'users.user_id',
                'users.email',
                'users.status as user_status',
                'users.is_approved as user_approved',
                'employer_accreditation.accreditation_id',
                'employer_accreditation.documents',
                'employer_accreditation.status as accreditation_status',
                'employer_accreditation.jpo_reviewed',
                'employer_accreditation.jpo_remarks'
            )
            ->selectRaw('(SELECT COUNT(*) FROM job_postings WHERE employer_id = employers.employer_id) as jobs_count');

        if ($request->has('search') && $request->search != '') {
            $query->where('employers.company_name', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'accredited') {
                $query->where('employers.is_accredited', 1);
            } elseif ($request->status == 'pending') {
                $query->where('employers.is_accredited', 0);
            }
        }

        $employers = $query->paginate(15)->withQueryString();

        $totalEmployers = DB::table('employers')->count();
        $accreditedEmployers = DB::table('employers')->where('is_accredited', 1)->count();
        $pendingAccreditation = DB::table('employers')->where('is_accredited', 0)->count();

        return view('admin.employers', compact('employers', 'totalEmployers', 'accreditedEmployers', 'pendingAccreditation'));
    })->name('employers');

    Route::post('/employers/{id}/accredit', function ($id) {
        $accreditation = DB::table('employer_accreditation')
            ->where('employer_id', $id)
            ->first();

        // Enforce: Admin cannot accredit an employer without the recommending approval of the JPO
        if (!$accreditation || !$accreditation->jpo_reviewed || !in_array($accreditation->status, ['jpo_approved', 'supervisor_approved'])) {
            return response()->json([
                'success' => false,
                'error' => 'Cannot accredit employer without the recommending approval of the JPO. JPO evaluation is required first.'
            ], 422);
        }

        DB::table('employers')->where('employer_id', $id)->update([
            'is_accredited' => 1,
            'accredited_at' => now()->toDateString(),
        ]);

        DB::table('employer_accreditation')->where('employer_id', $id)->update([
            'status' => 'admin_approved',
            'admin_approved' => 1,
            'admin_approved_at' => now(),
            'approved_at' => now()->toDateString(),
        ]);

        return response()->json(['success' => 'Employer accredited successfully.']);
    })->name('employers.accredit');

    // Job Postings Management (Create direct, Deactivate/Close)
    Route::get('/job-postings', [JobPostingController::class, 'index'])->name('job-postings');
    Route::get('/job-postings-index', [JobPostingController::class, 'index'])->name('job-postings.index');
    Route::get('/job-postings/create', [JobPostingController::class, 'create'])->name('job-postings.create');
    Route::post('/job-postings', [JobPostingController::class, 'store'])->name('job-postings.store');
    Route::get('/job-postings/export', [JobPostingController::class, 'export'])->name('job-postings.export');
    Route::post('/job-postings/bulk-action', [JobPostingController::class, 'bulkAction'])->name('job-postings.bulk-action');
    Route::get('/job-postings/{id}', [JobPostingController::class, 'show'])->name('job-postings.show');
    Route::get('/job-postings/{id}/edit', [JobPostingController::class, 'edit'])->name('job-postings.edit');
    Route::put('/job-postings/{id}', [JobPostingController::class, 'update'])->name('job-postings.update');
    Route::delete('/job-postings/{id}', [JobPostingController::class, 'destroy'])->name('job-postings.destroy');
    Route::post('/job-postings/{id}/approve', [JobPostingController::class, 'approve'])->name('job-postings.approve');
    Route::post('/job-postings/{id}/reject', [JobPostingController::class, 'reject'])->name('job-postings.reject');
    Route::post('/job-postings/{id}/close', [JobPostingController::class, 'close'])->name('job-postings.close');

    // Aliases for job-postings-list named routes
    Route::get('/job-postings-list', [JobPostingController::class, 'index'])->name('job-postings-list.index');
    Route::get('/job-postings-list/create', [JobPostingController::class, 'create'])->name('job-postings-list.create');
    Route::post('/job-postings-list', [JobPostingController::class, 'store'])->name('job-postings-list.store');
    Route::get('/job-postings-list/export', [JobPostingController::class, 'export'])->name('job-postings-list.export');
    Route::post('/job-postings-list/bulk-action', [JobPostingController::class, 'bulkAction'])->name('job-postings-list.bulk-action');
    Route::get('/job-postings-list/{id}', [JobPostingController::class, 'show'])->name('job-postings-list.show');
    Route::get('/job-postings-list/{id}/edit', [JobPostingController::class, 'edit'])->name('job-postings-list.edit');
    Route::put('/job-postings-list/{id}', [JobPostingController::class, 'update'])->name('job-postings-list.update');
    Route::delete('/job-postings-list/{id}', [JobPostingController::class, 'destroy'])->name('job-postings-list.destroy');
    Route::post('/job-postings-list/{id}/approve', [JobPostingController::class, 'approve'])->name('job-postings-list.approve');
    Route::post('/job-postings-list/{id}/reject', [JobPostingController::class, 'reject'])->name('job-postings-list.reject');
    Route::post('/job-postings-list/{id}/close', [JobPostingController::class, 'close'])->name('job-postings-list.close');

    // Reports
    Route::get('/reports', function () {
        $stats = [
            'total_jobs' => DB::table('job_postings')->count(),
            'pending_jobs' => DB::table('job_postings')->where('status', 'pending')->count(),
            'approved' => DB::table('job_postings')->where('status', 'approved')->count(),
            'rejected' => DB::table('job_postings')->where('status', 'rejected')->count(),
            'closed' => DB::table('job_postings')->where('status', 'closed')->count(),
            'total_employers' => DB::table('employers')->count(),
            'total_jobseekers' => DB::table('jobseekers')->count(),
            'total_applications' => DB::table('job_applications')->count(),
            'admin_count' => DB::table('users')->where('role', 'admin')->count(),
            'staff_count' => DB::table('users')->whereIn('role', ['jpo', 'trainer'])->count(),
            'total_placement_reports' => DB::table('placement_reports')->count(),
            'approved_placements' => DB::table('placement_reports')->where('status', 'approved')->count(),
        ];

        return view('admin.reports', compact('stats'));
    })->name('reports');

    // Labor Market Insights & Analytics (Admin handling LMO responsibilities)
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics.index');
    Route::get('/market-insights', [DashboardController::class, 'analytics'])->name('market-insights');

    // Admin Profile & Security Credentials
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/reset-password', [DashboardController::class, 'resetPassword'])->name('profile.reset-password');
    Route::post('/password/reset', [DashboardController::class, 'resetPassword'])->name('password.reset');

    // Admin Notification Center
    Route::get('/notifications', [DashboardController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/{id}/read', [DashboardController::class, 'markNotificationRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [DashboardController::class, 'markAllNotificationsRead'])->name('notifications.read_all');
});

/*
|--------------------------------------------------------------------------
| Test Routes (Remove in production)
|--------------------------------------------------------------------------
*/

Route::get('/test-db', function () {
    $users = DB::table('users')->get();
    return response()->json($users);
});

Route::get('/test-login', function () {
    if (Auth::check()) {
        $user = Auth::user();
        return [
            'authenticated' => true,
            'user_id' => $user->user_id,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
            'is_approved' => $user->is_approved,
        ];
    }
    return ['authenticated' => false];
});