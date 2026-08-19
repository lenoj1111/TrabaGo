<?php
// app/Http/Middleware/LmoMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LmoMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== 'lmo') {
            abort(403, 'Unauthorized. LMO only.');
        }

        return $next($request);
    }
}