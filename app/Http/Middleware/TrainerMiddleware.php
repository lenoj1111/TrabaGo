<?php
// app/Http/Middleware/TrainerMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== 'trainer') {
            abort(403, 'Unauthorized. Trainer only.');
        }

        if (Auth::user()->status === 'pending' || !Auth::user()->is_approved) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors([
                'email' => 'Your trainer account is pending Administrator approval. Please contact the DMDP administrator.',
            ]);
        }

        return $next($request);
    }
}