<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

        // Find user in database
        $user = DB::table('users')->where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // Store user in session
            session(['user_id' => $user->user_id]);
            session(['user_role' => $user->role]);
            session(['user_email' => $user->email]);

            // Debug: Check if session is set
            echo "User ID: " . session('user_id') . "<br>";
            echo "User Role: " . session('user_role') . "<br>";
            echo "Redirecting to: " . route('admin.users.index') . "<br>";
            
            // Redirect based on role
            if ($user->role === 'admin') {
                return redirect()->route('admin.users.index');
            }

            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login');
    }
}