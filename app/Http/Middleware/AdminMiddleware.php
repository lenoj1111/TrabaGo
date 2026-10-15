<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in using Laravel's Auth facade
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Check if the authenticated user has the admin role
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access. Admin only.');
        }

        return $next($request);
    }
}