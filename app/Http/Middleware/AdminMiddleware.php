<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in via session
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        // Check if user has admin role
        if (session('user_role') !== 'admin') {
            abort(403, 'Unauthorized access. Admin only.');
        }

        return $next($request);
    }
}