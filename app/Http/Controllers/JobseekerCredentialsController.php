<?php

namespace App\Http\Controllers;

use App\Models\Jobseeker;
use App\Notifications\AccountCredentialsUpdatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Throwable;

class JobseekerCredentialsController extends Controller
{
    public function update(Request $request, int $id)
    {
        abort_unless(in_array(Auth::user()?->role, ['admin', 'jpo'], true), 403);

        $jobseeker = Jobseeker::with('user')->findOrFail($id);
        $user = $jobseeker->user;
        abort_unless($user && $user->role === 'jobseeker', 404);

        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($user->user_id, 'user_id'),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $updates = ['email' => $validated['email']];
        $passwordChanged = !empty($validated['password']);
        $emailChanged = $user->email !== $validated['email'];

        if ($passwordChanged) {
            $updates['password'] = Hash::make($validated['password']);
        }

        if (($emailChanged || $passwordChanged) && in_array(config('mail.default'), ['log', 'array', 'failover'], true)) {
            return redirect()->back()->withInput()->withErrors([
                'email' => 'Email delivery is not configured. Credentials were not changed.',
            ]);
        }

        try {
            DB::transaction(function () use ($user, $jobseeker, $updates, $emailChanged, $passwordChanged, $validated) {
                $user->update($updates);
                $jobseeker->update(['email' => $updates['email']]);

                if ($emailChanged || $passwordChanged) {
                    $user->notify(new AccountCredentialsUpdatedNotification(
                        $validated['email'],
                        $passwordChanged ? $validated['password'] : null
                    ));
                }
            });
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->back()->withInput()->withErrors([
                'email' => 'Credentials were not changed because the update email could not be delivered.',
            ]);
        }

        $successMessage = ($emailChanged || $passwordChanged)
            ? 'Jobseeker login credentials updated and an email was sent to the updated account email.'
            : 'Jobseeker login credentials were already up to date.';

        return redirect()->back()->with('success', $successMessage);
    }
}
