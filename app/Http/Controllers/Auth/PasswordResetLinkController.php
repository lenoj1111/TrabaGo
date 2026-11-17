<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Notifications\TemporaryPasswordNotification;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        if (in_array(config('mail.default'), ['log', 'array', 'failover'], true)) {
            return back()->withInput($validated)->withErrors([
                'email' => __('Password reset email is not configured. Please contact support.'),
            ]);
        }

        $user = User::where('email', $validated['email'])->first();

        if ($user) {
            $temporaryPassword = Str::random(20);

            try {
                DB::transaction(function () use ($user, $temporaryPassword) {
                    $user->forceFill([
                        'password' => Hash::make($temporaryPassword),
                        'remember_token' => Str::random(60),
                    ])->save();

                    $user->notify(new TemporaryPasswordNotification($temporaryPassword));
                });
            } catch (Throwable $exception) {
                report($exception);

                return back()->withInput($validated)->withErrors([
                    'email' => __('We could not send a temporary password. Please try again later.'),
                ]);
            }
        }

        return back()->with('status', __('If an account exists for that email, a temporary password has been emailed.'));
    }

    /**
     * Handle a password reset link request from the mobile app.
     */
    public function storeApi(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        Password::sendResetLink($validated);

        return response()->json([
            'success' => true,
            'message' => __('If an account exists for that email, a password reset link has been sent.'),
        ]);
    }
}
