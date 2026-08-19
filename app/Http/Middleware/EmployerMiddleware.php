<?php
// app/Http/Middleware/EmployerMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== 'employer') {
            abort(403, 'Unauthorized. Employer only.');
        }

        return $next($request);
    }
}