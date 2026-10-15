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
| API Routes for TrabaGo Mobile App & Web Client
|--------------------------------------------------------------------------
*/

// --- AUTHENTICATION (PUBLIC) ---
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/recover-account', [AuthController::class, 'recoverAccount']);
});

// --- DASHBOARD (AI Skill Matching, Metrics & Recommendations) ---
Route::get('/dashboard', [JobController::class, 'getDashboard']);

// --- JOBS (PUBLIC / HYBRID) ---
Route::prefix('jobs')->group(function () {
    Route::get('/', [JobController::class, 'getAll']);
    Route::get('/{id}', [JobController::class, 'getById']);
});

// --- TRAINING & CERTIFICATES (PUBLIC / HYBRID) ---
Route::prefix('training')->group(function () {
    Route::get('/', [TrainingController::class, 'getAll']);
    Route::get('/certificates', [TrainingController::class, 'getCertificates']);
    Route::get('/{id}', [TrainingController::class, 'getById']);
});

// --- PROTECTED ROUTES (SANCTUM) ---
Route::middleware('auth:sanctum')->group(function () {
    // User / Profile
    Route::get('/auth/profile', [AuthController::class, 'getProfile']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);
    Route::post('/auth/skills/add', [AuthController::class, 'addSkill']);
    Route::delete('/auth/skills/{id}', [AuthController::class, 'removeSkill']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Document Uploads & Hub
    Route::get('/documents', [DocumentController::class, 'getAll']);
    Route::post('/documents/upload', [DocumentController::class, 'upload']);
    Route::post('/documents/{id}/review', [DocumentController::class, 'review']);
    Route::delete('/documents/{id}', [DocumentController::class, 'delete']);

    // Dashboard (Authenticated with tailored AI Cosine Similarity rankings)
    Route::get('/jobseeker/dashboard', [JobController::class, 'getDashboard']);

    // Applications Lifecycle (Guarded by requireAccountVerified)
    Route::get('/applications', [ApplicationController::class, 'getAll']);
    Route::post('/applications', [ApplicationController::class, 'submit'])->middleware('requireAccountVerified');
    Route::post('/jobs/{id}/apply', [ApplicationController::class, 'submit'])->middleware('requireAccountVerified');
    Route::delete('/applications/{id}', [ApplicationController::class, 'withdraw']);
    Route::post('/applications/{id}/accept-offer', [ApplicationController::class, 'acceptOffer']);
    Route::post('/applications/{id}/decline-offer', [ApplicationController::class, 'declineOffer']);
    Route::post('/applications/request-resignation', [ApplicationController::class, 'requestResignation']);

    // Job Posting Creation (Employer only, guarded by requireAccountVerified)
    Route::post('/jobs', [JobController::class, 'store'])->middleware('requireAccountVerified');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'getAll']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', function (Request $request) {
        \App\Models\Notification::where('user_id', $request->user()->user_id)->update(['is_read' => true]);
        return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
    });

    // Training & Quizzes
    Route::post('/training/{id}/enroll', [TrainingController::class, 'enroll']);
    Route::post('/training/{id}/quiz-result', [TrainingController::class, 'submitQuiz']);
});