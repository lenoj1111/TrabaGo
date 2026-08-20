<?php

use App\Http\Controllers\JobseekerRegistrationController;
use App\Http\Controllers\EmployerRegistrationController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;  
use App\Http\Controllers\Admin\UserManagementController;

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
| Public Routes
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
| Jobseeker Routes
|--------------------------------------------------------------------------
*/

Route::prefix('jobseeker')->name('jobseeker.')->group(function () {

    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    // Protected routes - requires authentication
    Route::middleware(['auth'])->group(function () {
        Route::get('/home', function () {
            return view('jobseeker.homepage');
        })->name('home');

        Route::get('/profile', function () {
            return view('jobseeker.profile');
        })->name('profile');
    });
});

/*
|--------------------------------------------------------------------------
| Employer Routes
|--------------------------------------------------------------------------
*/

Route::prefix('employer')->name('employer.')->group(function () {

    Route::get('/login', function () {
        return view('employer.login');
    })->name('login');

    // Protected routes - requires authentication
    Route::middleware(['auth'])->group(function () {
        Route::get('/home', function () {
            return view('employer.homepage');
        })->name('home');

        Route::get('/dashboard', function () {
            return view('employer.dashboard');
        })->name('dashboard');

        Route::get('/profile', function () {
            return view('employer.profile');
        })->name('profile');

        Route::get('/job-postings', function () {
            return view('employer.job-postings');
        })->name('job-postings');

        Route::get('/applications', function () {
            return view('employer.applications');
        })->name('applications');

        Route::get('/accreditation', function () {
            return view('employer.accreditation');
        })->name('accreditation');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login', function () {
        return view('admin.login');
    })->name('login');

    // Protected routes - requires authentication and admin role
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::get('/employers', function () {
            return view('admin.employers');
        })->name('employers');

        Route::get('/job-postings', function () {
            return view('admin.job-postings');
        })->name('job-postings');

        Route::get('/reports', function () {
            return view('admin.reports');
        })->name('reports');
    });
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


/*
|--------------------------------------------------------------------------
| Admin User Management Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserManagementController::class, 'update'])->name('users.update');
    Route::post('/users/{id}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/{id}/approve', [UserManagementController::class, 'approve'])->name('users.approve');
    Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');
});

// Test route after login
Route::get('/dashboard', function () {
    return 'Welcome! User ID: ' . session('user_id') . ', Role: ' . session('user_role');
})->middleware(['auth']);