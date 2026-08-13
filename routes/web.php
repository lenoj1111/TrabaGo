<?php

use App\Http\Controllers\JobseekerRegistrationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;  

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
| Jobseeker Routes
|--------------------------------------------------------------------------
*/

Route::prefix('jobseeker')->name('jobseeker.')->group(function () {

    Route::get('/login', function () {
        return view('jobseeker.login');
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

    Route::get('/register', function () {
        return view('employer.register');
    })->name('register');

    Route::get('/login', function () {
        return view('employer.login');
    })->name('login');

    // Protected routes - requires authentication
    Route::middleware(['auth'])->group(function () {
        Route::get('/home', function () {
            return view('employer.homepage');
        })->name('home');
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