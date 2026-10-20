<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['nullable', 'string', 'in:jobseeker,employer'],
        ]);

        $role = $request->input('role', 'jobseeker');
        $isApproved = ($role === 'jobseeker') ? 1 : 0;

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            'status' => 'active',
            'is_approved' => $isApproved,
        ]);

        $nameParts = preg_split('/\s+/', trim($request->name), 2);
        $firstName = $nameParts[0] ?? 'User';
        $lastName = $nameParts[1] ?? 'User';

        try {
            if ($role === 'jobseeker') {
                DB::table('jobseekers')->insert([
                    'user_id' => $user->user_id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $request->email,
                    'mobile_number' => $request->input('mobile_number', 'N/A'),
                    'sex' => 'Prefer not to say',
                    'civil_status' => 'Single',
                    'citizenship' => 'Filipino',
                    'birth_date' => now()->subYears(20)->format('Y-m-d'),
                    'employment_status' => 'Unemployed',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } elseif ($role === 'employer') {
                DB::table('employers')->insert([
                    'user_id' => $user->user_id,
                    'company_name' => $request->name,
                    'trade_name' => $request->name,
                    'email' => $request->email,
                    'contact_person' => $request->name,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('user_profiles')->insert([
                'user_id' => $user->user_id,
                'full_name' => $request->name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Log but allow registration to succeed
            report($e);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
