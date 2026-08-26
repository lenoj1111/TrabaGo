<?php

use App\Http\Controllers\JobseekerRegistrationController;
use App\Http\Controllers\EmployerRegistrationController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\JobPostingController;
use App\Http\Controllers\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('homepage');
})->name('home');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| JOBSEEKER REGISTRATION ROUTES (Public)
|--------------------------------------------------------------------------
*/

Route::get('/jobseeker/register', [JobseekerRegistrationController::class, 'showRegistrationForm'])->name('jobseeker.register');
Route::post('/jobseeker/register', [JobseekerRegistrationController::class, 'register'])->name('jobseeker.register.post');
Route::get('/jobseeker/register/success', [JobseekerRegistrationController::class, 'success'])->name('jobseeker.register.success');

/*
|--------------------------------------------------------------------------
| EMPLOYER REGISTRATION ROUTES (Public)
|--------------------------------------------------------------------------
*/

Route::get('/employer/register', [EmployerRegistrationController::class, 'showRegistrationForm'])->name('employer.register');
Route::post('/employer/register', [EmployerRegistrationController::class, 'register'])->name('employer.register.post');
Route::get('/employer/register/success', [EmployerRegistrationController::class, 'success'])->name('employer.register.success');

/*
|--------------------------------------------------------------------------
| JOBSEEKER ROUTES (Authenticated)
|--------------------------------------------------------------------------
*/

Route::prefix('jobseeker')->name('jobseeker.')->middleware(['auth'])->group(function () {
    Route::get('/home', function () {
        return view('jobseeker.homepage');
    })->name('home');

    Route::get('/profile', function () {
        return view('jobseeker.profile');
    })->name('profile');
});

/*
|--------------------------------------------------------------------------
| EMPLOYER ROUTES (Authenticated)
|--------------------------------------------------------------------------
*/

Route::prefix('employer')->name('employer.')->middleware(['auth'])->group(function () {
    Route::get('/home', function () {
        $user = auth()->user();
        $employer = DB::table('employers')
            ->where('user_id', $user->user_id)
            ->first();

        $activePostings = $employer
            ? DB::table('job_postings')->where('employer_id', $employer->employer_id)->where('status', 'approved')->count()
            : 0;
        $totalApplications = $employer
            ? DB::table('job_applications')
                ->join('job_postings', 'job_applications.job_id', '=', 'job_postings.job_id')
                ->where('job_postings.employer_id', $employer->employer_id)
                ->count()
            : 0;
        $shortlistedApplications = $employer
            ? DB::table('job_applications')
                ->join('job_postings', 'job_applications.job_id', '=', 'job_postings.job_id')
                ->where('job_postings.employer_id', $employer->employer_id)
                ->whereIn('job_applications.status', ['reviewed', 'interview', 'hired'])
                ->count()
            : 0;

        return view('employer.homepage', compact(
            'employer',
            'activePostings',
            'totalApplications',
            'shortlistedApplications'
        ));
    })->name('home');

    Route::get('/dashboard', function () {
        return view('employer.dashboard');
    })->name('dashboard');

    Route::get('/profile', function () {
        return view('employer.profile');
    })->name('profile');

    Route::get('/job-postings', function () {
        $employer = DB::table('employers')
            ->where('user_id', auth()->user()->user_id)
            ->first();

        abort_unless($employer, 404, 'Employer profile not found.');

        $jobPostings = DB::table('job_postings')
            ->where('employer_id', $employer->employer_id)
            ->orderByDesc('created_at')
            ->get();

        return view('employer.job-postings', compact('employer', 'jobPostings'));
    })->name('job-postings');

    Route::post('/job-postings', function (Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'required|string',
            'qualifications' => 'nullable|string',
            'vacancy_count' => 'required|integer|min:1',
            'valid_until' => 'required|date|after:today',
            'accepts_disability' => 'nullable|boolean',
            'disability_type' => 'nullable|string|max:100',
        ]);

        $employer = DB::table('employers')
            ->where('user_id', auth()->user()->user_id)
            ->first();

        abort_unless($employer, 404, 'Employer profile not found.');

        if (!$employer->is_accredited) {
            return redirect()->back()
                ->with('error', 'Your employer account must be accredited before posting a job.')
                ->withInput();
        }

        DB::table('job_postings')->insert([
            'employer_id' => $employer->employer_id,
            'admin_id' => null,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'qualifications' => $validated['qualifications'] ?? null,
            'vacancy_count' => $validated['vacancy_count'],
            'valid_until' => $validated['valid_until'],
            'accepts_disability' => $validated['accepts_disability'] ?? 0,
            'disability_type' => $validated['disability_type'] ?? null,
            'status' => 'pending',
            'created_by' => 'employer',
            'created_at' => now()->toDateString(),
            'approved_at' => null,
        ]);

        return redirect()->route('employer.job-postings')
            ->with('success', 'Job posting submitted for admin review.');
    })->name('job-postings.store');

    Route::post('/job-postings/{id}/close', function (int $id) {
        $employer = DB::table('employers')
            ->where('user_id', auth()->user()->user_id)
            ->first();

        abort_unless($employer, 404, 'Employer profile not found.');

        DB::table('job_postings')
            ->where('job_id', $id)
            ->where('employer_id', $employer->employer_id)
            ->whereIn('status', ['pending', 'approved'])
            ->update(['status' => 'closed']);

        return redirect()->route('employer.job-postings')
            ->with('success', 'Job posting closed successfully.');
    })->name('job-postings.close');

    Route::get('/applications', function () {
        return view('employer.applications');
    })->name('applications');

    Route::get('/accreditation', function () {
        return view('employer.accreditation');
    })->name('accreditation');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (Authenticated + Admin Role)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::get('/users', function () {
        $users = DB::table('users')->paginate(15);
        
        $counts = [
            'total' => DB::table('users')->count(),
            'active' => DB::table('users')->where('status', 'active')->count(),
            'inactive' => DB::table('users')->where('status', 'inactive')->count(),
            'pending' => DB::table('users')->where('is_approved', 0)->count(),
        ];
        
        $roles = ['admin', 'jpo', 'trainer', 'lmo', 'employer', 'jobseeker'];
        $statuses = ['active', 'inactive'];
        
        return view('admin.manageusers', compact('users', 'counts', 'roles', 'statuses'));
    })->name('users.index');
    
    Route::get('/users/create', function () {
        $roles = ['admin', 'jpo', 'trainer', 'lmo', 'employer', 'jobseeker'];
        return view('admin.users-create', compact('roles'));
    })->name('users.create');
    
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserManagementController::class, 'update'])->name('users.update');
    Route::post('/users/{id}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/{id}/approve', [UserManagementController::class, 'approve'])->name('users.approve');
    Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    
    // Employer Management
    Route::get('/employers', function () {
        $employers = DB::table('employers')
            ->leftJoin('users', 'employers.user_id', '=', 'users.user_id')
            ->select('employers.*', 'users.email')
            ->paginate(15);
        
        $totalEmployers = DB::table('employers')->count();
        $accreditedEmployers = DB::table('employers')->where('is_accredited', 1)->count();
        $pendingAccreditation = DB::table('employers')->where('is_accredited', 0)->count();
        
        return view('admin.employers', compact('employers', 'totalEmployers', 'accreditedEmployers', 'pendingAccreditation'));
    })->name('employers');
    Route::get('/employers/{id}/documents', [UserManagementController::class, 'showEmployerDocuments'])->name('employers.documents');
    Route::post('/employers/{id}/accredit', [UserManagementController::class, 'accreditEmployer'])->name('employers.accredit');
    
    // Job Postings - Static View (Your Design) - Inside jobpostings folder
    Route::get('/job-postings', function () {
        $stats = [
            'total' => DB::table('job_postings')->count(),
            'pending' => DB::table('job_postings')->where('status', 'pending')->count(),
            'approved' => DB::table('job_postings')->where('status', 'approved')->count(),
            'rejected' => DB::table('job_postings')->where('status', 'rejected')->count(),
            'closed' => DB::table('job_postings')->where('status', 'closed')->count(),
            'expired' => DB::table('job_postings')
                ->where('status', 'approved')
                ->where('valid_until', '<', now()->toDateString())
                ->count(),
        ];
        
        $jobPostings = DB::table('job_postings')
            ->leftJoin('employers', 'job_postings.employer_id', '=', 'employers.employer_id')
            ->select('job_postings.*', 'employers.company_name')
            ->paginate(15);
        
        // Get employers for the modal dropdown
        $employers = DB::table('employers as e')
            ->join('users as u', 'e.user_id', '=', 'u.user_id')
            ->where('e.is_accredited', 1)
            ->where('u.status', 'active')
            ->select('e.employer_id', 'e.company_name', 'u.email')
            ->orderBy('e.company_name')
            ->get();
        
        return view('admin.jobpostings.job-postings', compact('stats', 'jobPostings', 'employers'));
    })->name('job-postings');
    
    // Job Postings - Dynamic CRUD (Controller)
    Route::get('/job-postings-list', [JobPostingController::class, 'index'])->name('job-postings-list.index');
    Route::get('/job-postings-list/create', [JobPostingController::class, 'create'])->name('job-postings-list.create');
    Route::post('/job-postings-list', [JobPostingController::class, 'store'])->name('job-postings-list.store');
    Route::get('/job-postings-list/{job_posting}', [JobPostingController::class, 'show'])->name('job-postings-list.show');
    Route::get('/job-postings-list/{job_posting}/edit', [JobPostingController::class, 'edit'])->name('job-postings-list.edit');
    Route::put('/job-postings-list/{job_posting}', [JobPostingController::class, 'update'])->name('job-postings-list.update');
    Route::delete('/job-postings-list/{job_posting}', [JobPostingController::class, 'destroy'])->name('job-postings-list.destroy');
    
    // Custom Job Posting Actions
    Route::post('job-postings-list/{job_posting}/approve', [JobPostingController::class, 'approve'])->name('job-postings-list.approve');
    Route::post('job-postings-list/{job_posting}/reject', [JobPostingController::class, 'reject'])->name('job-postings-list.reject');
    Route::post('job-postings-list/{job_posting}/close', [JobPostingController::class, 'close'])->name('job-postings-list.close');
    Route::post('job-postings-list/bulk-action', [JobPostingController::class, 'bulkAction'])->name('job-postings-list.bulk-action');
    Route::get('job-postings-list/export', [JobPostingController::class, 'export'])->name('job-postings-list.export');
    
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
            'staff_count' => DB::table('users')->whereIn('role', ['jpo', 'trainer', 'lmo'])->count(),
        ];
        
        return view('admin.reports', compact('stats'));
    })->name('reports');
});

/*
|--------------------------------------------------------------------------
| TEST ROUTES (Remove in production)
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