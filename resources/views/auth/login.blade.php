@extends('layouts.public')

@section('title', 'Account Login - DMDP Cebu City')

@section('content')
<div class="min-h-screen bg-gray-50 py-10 md:py-16">
    <div class="max-w-4xl mx-auto px-5">
        <div class="grid grid-cols-1 lg:grid-cols-2 bg-white border border-gray-200 rounded-2xl overflow-hidden">
            
            <!-- Left Banner -->
            <div class="bg-green-950 px-7 py-10 md:px-10 md:py-14 text-white">
                <div class="space-y-5">
                    <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center text-white font-black text-xl">
                        T
                    </div>

                    <div>
                        <p class="text-green-400 text-xs font-semibold uppercase tracking-widest mb-2">DMDP × TrabaGo</p>
                        <h1 class="text-3xl font-extrabold text-white leading-tight">Welcome back.</h1>
                        <p class="text-green-100/70 text-sm leading-relaxed mt-2 max-w-sm">Sign in to access AI skill-matched jobs, certified vocational trainings, and recruitment pipelines.</p>
                    </div>

                    <div class="pt-4 border-t border-green-800/50 text-xs text-green-300/70">
                        <span class="flex items-center gap-1.5 font-medium">
                            <span class="h-1.5 w-1.5 rounded-full bg-green-400"></span>
                            AI-Powered Skill Matching
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right Login Form -->
            <div class="px-7 py-10 md:px-10 md:py-14">
                <div class="mb-7">
                    <p class="text-xs font-semibold uppercase tracking-wider text-green-600">Access Portal</p>
                    <h2 class="text-2xl font-extrabold text-gray-900 mt-1">Account Login</h2>
                    <p class="text-sm text-gray-500 mt-1">Enter your credentials to continue.</p>
                </div>

                @if (session('status'))
                    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               placeholder="you@example.com"
                               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-medium text-green-600 hover:text-green-700">Forgot password?</a>
                            @endif
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none">
                    </div>

                    <div class="flex items-center py-1">
                        <label for="remember" class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                            <input id="remember" type="checkbox" name="remember" class="w-4 h-4 text-green-600 rounded border-gray-300 focus:ring-green-500">
                            Remember me
                        </label>
                    </div>

                    <button type="submit" class="w-full py-3 px-6 rounded-lg bg-green-600 hover:bg-green-700 text-white font-semibold text-sm transition-colors">
                        Sign In →
                    </button>
                </form>

                <div class="mt-7 pt-5 border-t border-gray-100 text-center">
                    <p class="text-sm text-gray-500">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="font-semibold text-green-600 hover:text-green-700">Create an account →</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
