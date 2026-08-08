<?php

use Illuminate\Support\Facades\Route;
//use Illuminate\Support\Facades\Auth;//

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('homepage');  // Changed from 'welcome' to 'homepage'
})->name('home');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
//Auth::routes();//

/*
|--------------------------------------------------------------------------
| Jobseeker Routes
|--------------------------------------------------------------------------
*/

Route::prefix('jobseeker')->name('jobseeker.')->group(function () {

    Route::get('/register', function () {
        return view('jobseeker.register');
    })->name('register');

    Route::get('/login', function () {
        return view('jobseeker.login');
    })->name('login');

    // Protected routes - requires authentication
    Route::middleware(['auth'])->group(function () {
        Route::get('/', function () {
            return view('jobseeker.homepage');
        })->name('home');

        Route::get('/homepage', function () {
            return view('jobseeker.homepage');
        })->name('homepage');

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
        Route::get('/homepage', function () {
            return view('employer.homepage');
        })->name('homepage');
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