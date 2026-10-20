<?php
// bootstrap/app.php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\EmployerMiddleware;
use App\Http\Middleware\JobseekerMiddleware;
use App\Http\Middleware\JpoMiddleware;
use App\Http\Middleware\TrainerMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        ['prefix' => 'api', 'middleware' => ['auth:sanctum']]
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register custom route middleware
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'employer' => EmployerMiddleware::class,
            'jobseeker' => JobseekerMiddleware::class,
            'jpo' => JpoMiddleware::class,
            'trainer' => TrainerMiddleware::class,
            'requireAccountVerified' => \App\Http\Middleware\RequireAccountVerified::class,
            'requireOTPVerified' => \App\Http\Middleware\RequireOTPVerified::class,
            'account.verified' => \App\Http\Middleware\RequireAccountVerified::class,
            'otp.verified' => \App\Http\Middleware\RequireOTPVerified::class,
        ]);
        
        // Add global middleware (runs on every request)
        // $middleware->append([
        //     \App\Http\Middleware\SomeGlobalMiddleware::class,
        // ]);
        
        // Add middleware groups
        // $middleware->group('api', [
        //     \Illuminate\Routing\Middleware\ThrottleRequests::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Enforce structured JSON error payloads for all API endpoints (error-handling-patterns)
        $exceptions->shouldRenderJsonWhen(function (\Illuminate\Http\Request $request, \Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });
    })->create();