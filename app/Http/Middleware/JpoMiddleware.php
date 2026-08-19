<?php
// app/Http/Middleware/JpoMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JpoMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== 'jpo') {
            abort(403, 'Unauthorized. JPO only.');
        }

        return $next($request);
    }
}