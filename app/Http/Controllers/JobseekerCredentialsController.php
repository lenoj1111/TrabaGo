<?php

namespace App\Http\Controllers;

use App\Models\Jobseeker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
        if (!empty($validated['password'])) {
            $updates['password'] = Hash::make($validated['password']);
        }

        DB::transaction(function () use ($user, $jobseeker, $updates) {
            $user->update($updates);
            $jobseeker->update(['email' => $updates['email']]);
        });

        return redirect()->back()->with('success', 'Jobseeker login credentials updated.');
    }
}
