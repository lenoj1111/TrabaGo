<?php

namespace App\Traits;

use App\Models\Jobseeker;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait ResolvesJobseeker
{
    /**
     * Resolve the authenticated User from Sanctum or the current request/session.
     */
    protected function resolveUser(?Request $request = null): ?User
    {
        return auth('sanctum')->user() 
            ?? ($request ? ($request->user() ?? Auth::user()) : Auth::user());
    }

    /**
     * Resolve the Jobseeker model associated with the authenticated user,
     * optionally creating the initial record if it does not already exist.
     */
    protected function resolveJobseeker(?Request $request = null, bool $autoCreate = true): ?Jobseeker
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return null;
        }

        $jobseeker = $user->jobseeker ?? Jobseeker::where('user_id', $user->user_id)->first();

        if (!$jobseeker && $autoCreate) {
            $nameParts = explode(' ', $user->full_name ?? '', 2);
            $firstName = !empty($nameParts[0]) ? $nameParts[0] : (explode('@', $user->email ?? '')[0] ?: 'Jobseeker');
            $lastName = !empty($nameParts[1]) ? $nameParts[1] : 'User';

            $jobseeker = Jobseeker::firstOrCreate(
                ['user_id' => $user->user_id],
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $user->email,
                    'employment_status' => 'Looking for job',
                    'citizenship' => 'Filipino',
                ]
            );
        }

        return $jobseeker;
    }
}
