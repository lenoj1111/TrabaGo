@extends('layouts.public')

@section('title', 'Account Login - DMDP Cebu City')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-emerald-50/60 to-white py-10 md:py-16">
    <div class="max-w-5xl mx-auto px-5">
        <div class="grid grid-cols-1 lg:grid-cols-2 bg-white border border-emerald-100 rounded-3xl shadow-card overflow-hidden">
            <!-- Left Banner with Emerald Forest Theme -->
            <div class="relative overflow-hidden px-7 py-10 md:px-10 md:py-14 text-white" style="background: linear-gradient(135deg, #022c22 0%, #064e3b 55%, #047857 100%);">
                <div class="absolute -right-16 -bottom-20 w-64 h-64 rounded-full border border-emerald-400/20"></div>
                <div class="absolute -right-8 -bottom-12 w-48 h-48 rounded-full border border-teal-400/20"></div>
                
                <div class="relative space-y-6">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <svg class="w-7 h-7 text-slate-950 font-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>

                    <div>
                        <p class="text-emerald-300 text-xs font-black uppercase tracking-widest mb-2">DMDP x TrabaGo JobMatch</p>
                        <h1 class="text-3xl md:text-4xl font-black leading-tight text-white">Welcome back.</h1>
                        <p class="text-emerald-100/80 text-sm leading-relaxed mt-2 max-w-sm">Sign in to access AI skill-matched jobs, certified vocational trainings, and recruitment pipelines.</p>
                    </div>

                    <div class="pt-4 border-t border-emerald-700/50 flex items-center gap-4 text-xs text-emerald-200">
                        <span class="flex items-center gap-1.5 font-semibold">
                            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Live Cosine-Similarity Match
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right Login Form -->
            <div class="px-7 py-10 md:px-10 md:py-14">
                <div class="mb-8">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Access Portal</span>
                    <h2 class="text-2xl font-black text-slate-900 mt-1">Account Login</h2>
                    <p class="text-xs text-slate-500 mt-1">Enter your credentials to continue to your dashboard.</p>
                </div>

                @if (session('status'))
                    <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-bold text-emerald-800">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-bold text-rose-800">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               placeholder="you@example.com"
                               class="w-full px-4 py-3 bg-white border @error('email') border-rose-500 @else border-slate-200 @enderror rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-500 transition outline-none">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Password</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">Forgot password?</a>
                            @endif
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full px-4 py-3 bg-white border @error('password') border-rose-500 @else border-slate-200 @enderror rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-500 transition outline-none">
                    </div>

                    <div class="flex items-center justify-between py-1">
                        <label for="remember" class="flex items-center gap-2 text-xs font-semibold text-slate-600 cursor-pointer">
                            <input id="remember" type="checkbox" name="remember" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                            Remember me
                        </label>
                    </div>

                    <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-black text-sm shadow-lg shadow-emerald-600/30 transition-all hover:scale-[1.01]">
                        Sign In &rarr;
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                    <p class="text-xs text-slate-500">
                        Don't have an account?
                        <a href="{{ route('jobseeker.register') }}" class="font-bold text-emerald-600 hover:underline">Register as Jobseeker</a>
                        &bull;
                        <a href="{{ route('employer.register') }}" class="font-bold text-emerald-600 hover:underline">Employer Registration</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
