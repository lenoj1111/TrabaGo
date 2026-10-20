<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAccountVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'UNAUTHENTICATED',
                    'message' => 'Authentication required.',
                ], 401);
            }
            return redirect()->route('login');
        }

        // Admins, JPOs, and Trainers are administrative roles
        if (in_array($user->role, ['admin', 'jpo', 'trainer'])) {
            return $next($request);
        }

        // Check if account is verified
        if (!$user->isAccountVerified()) {
            $isJobseeker = ($user->role === 'jobseeker');
            $message = $isJobseeker
                ? 'Your account is currently Unverified. Please complete your profile verification to unlock this feature.'
                : 'Your account is currently Unverified. Please complete your company accreditation and profile verification to unlock job posting.';

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'ACCOUNT_UNVERIFIED',
                    'message' => $message,
                    'account_status' => $user->account_status,
                ], 403);
            }

            if ($isJobseeker) {
                return redirect()->route('jobseeker.profile')->with('error', $message);
            } else {
                return redirect()->route('employer.accreditation')->with('error', $message);
            }
        }

        return $next($request);
    }
}
