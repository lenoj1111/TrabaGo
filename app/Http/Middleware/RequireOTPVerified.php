<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireOTPVerified
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

        // Check if contact (email/phone) is verified via OTP
        if (!$user->isContactVerified()) {
            $message = 'Please verify your email or phone number with OTP first.';

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'CONTACT_UNVERIFIED',
                    'message' => $message,
                ], 403);
            }

            return redirect()->route('verification.notice')->with('error', $message);
        }

        return $next($request);
    }
}
