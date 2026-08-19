<?php
// app/Http/Middleware/JobseekerMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobseekerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== 'jobseeker') {
            abort(403, 'Unauthorized. Jobseeker only.');
        }

        return $next($request);
    }
}