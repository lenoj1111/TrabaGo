<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupervisorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        if (!in_array($user->role, ['supervisor', 'pesd_supervisor', 'admin'])) {
            abort(403, 'Unauthorized. PESD Supervisor only.');
        }

        return $next($request);
    }
}
