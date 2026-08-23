<?php

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\TrainingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for TrabaGo Mobile App & Web
|--------------------------------------------------------------------------
*/

// --- AUTHENTICATION (PUBLIC) ---
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// --- JOBS (PUBLIC) ---
Route::prefix('jobs')->group(function () {
    Route::get('/', [JobController::class, 'getAll']);
    Route::get('/{id}', [JobController::class, 'getById']);
});

// --- TRAINING (PUBLIC) ---
Route::prefix('training')->group(function () {
    Route::get('/', [TrainingController::class, 'getAll']);
    Route::get('/{id}', [TrainingController::class, 'getById']);
});

// --- DOCUMENT UPLOAD ---
Route::post('/documents/upload', [DocumentController::class, 'upload']);

// --- PROTECTED ROUTES (SANCTUM) ---
Route::middleware('auth:sanctum')->group(function () {
    // User / Profile
    Route::get('/auth/profile', [AuthController::class, 'getProfile']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Applications
    Route::get('/applications', [ApplicationController::class, 'getAll']);
    Route::post('/applications', [ApplicationController::class, 'submit']);
    Route::delete('/applications/{id}', [ApplicationController::class, 'withdraw']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'getAll']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    // Training Quiz Submission
    Route::post('/training/{id}/quiz-result', [TrainingController::class, 'submitQuiz']);
});

// Unauthenticated fallback handlers for smooth local development / testing
Route::get('/applications', [ApplicationController::class, 'getAll']);
Route::get('/notifications', [NotificationController::class, 'getAll']);
Route::post('/training/{id}/quiz-result', [TrainingController::class, 'submitQuiz']);