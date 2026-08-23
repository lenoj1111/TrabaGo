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
use App\Http\Middleware\LmoMiddleware;
use App\Http\Middleware\SupervisorMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register custom route middleware
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'employer' => EmployerMiddleware::class,
            'jobseeker' => JobseekerMiddleware::class,
            'jpo' => JpoMiddleware::class,
            'supervisor' => SupervisorMiddleware::class,
            'trainer' => TrainerMiddleware::class,
            'lmo' => LmoMiddleware::class,
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
        //
    })->create();