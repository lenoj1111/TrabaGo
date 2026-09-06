<?php
// app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Check if user is pending approval
            if ($user->status === 'pending' || !$user->is_approved) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => ($user->role === 'trainer')
                        ? 'Your trainer account is pending Administrator approval. Please contact the DMDP administrator.'
                        : 'Your account is pending approval.',
                ]);
            }

            // Check if user is active
            if ($user->status !== 'active') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'Your account is inactive. Please contact support.',
                ]);
            }

            // Route each role to its own dedicated dashboard
            switch ($user->role) {
                case 'admin':
                case 'supervisor':
                case 'pesd_supervisor':
                    return redirect('/admin/dashboard');
                case 'jpo':
                    return redirect('/jpo/dashboard');
                case 'trainer':
                    return redirect('/trainer/dashboard');
                case 'employer':
                    return redirect('/employer/home');
                case 'jobseeker':
                    return redirect('/jobseeker/home');
                default:
                    return redirect('/');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}