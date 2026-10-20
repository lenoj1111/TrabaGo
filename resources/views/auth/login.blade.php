@extends('layouts.public')

@section('title', 'Account Login - DMDP Cebu City | TrabaGo')

@section('content')
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center bg-gradient-to-br from-slate-50 via-emerald-50/25 to-slate-100/90 py-10 sm:py-14 lg:py-16 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-5xl xl:max-w-6xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 bg-white border border-slate-200/90 rounded-3xl shadow-2xl shadow-emerald-950/5 overflow-hidden">
            
            <!-- Left Banner (lg:col-span-5) -->
            <div class="relative overflow-hidden bg-gradient-to-br from-emerald-950 via-emerald-900 to-slate-950 p-8 sm:p-10 lg:p-12 text-white flex flex-col justify-between lg:col-span-5 min-h-[380px] lg:min-h-[580px]">
                <!-- Decorative Blur Accents -->
                <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full bg-emerald-500/20 blur-3xl pointer-events-none"></div>
                <div class="absolute -left-16 -bottom-16 w-56 h-56 rounded-full bg-teal-400/15 blur-3xl pointer-events-none"></div>

                <div class="relative z-10 space-y-7">
                    <!-- Brand Badge -->
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center text-white font-black text-xl shadow-lg shadow-emerald-600/30">
                            T
                        </div>
                        <div>
                            <span class="block font-black text-lg tracking-tight text-white leading-none">
                                Traba<span class="text-emerald-400">Go</span>
                            </span>
                            <span class="text-[10px] font-bold text-emerald-300 uppercase tracking-widest">
                                Cebu City PESO Portal
                            </span>
                        </div>
                    </div>

                    <!-- Main Welcome Heading -->
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-500/15 text-emerald-300 border border-emerald-400/20 mb-3.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            DMDP × Cebu City Government
                        </span>
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white leading-tight">
                            Welcome back.
                        </h1>
                        <p class="text-emerald-100/85 text-sm sm:text-base leading-relaxed mt-3 max-w-md">
                            Your single gateway to AI-powered skill matching, accredited vocational trainings, and trusted local employment in Cebu City.
                        </p>
                    </div>

                    <!-- Highlight Features -->
                    <div class="pt-5 border-t border-emerald-800/60 space-y-3.5">
                        <div class="flex items-start gap-3 bg-white/5 border border-white/10 rounded-2xl p-3.5 backdrop-blur-sm">
                            <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-300 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-xs font-bold text-white uppercase tracking-wider">Unified Single Sign-On</h2>
                                <p class="text-xs text-emerald-200/80 mt-0.5">Direct portal routing for Jobseekers, Employers, JPO Officers, & Trainers.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 bg-white/5 border border-white/10 rounded-2xl p-3.5 backdrop-blur-sm">
                            <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-300 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-xs font-bold text-white uppercase tracking-wider">City-Wide Placement</h2>
                                <p class="text-xs text-emerald-200/80 mt-0.5">Connecting all 80 barangays to accredited manpower and career growth.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 pt-8 mt-6 border-t border-emerald-800/40 text-xs text-emerald-300/70 flex items-center justify-between">
                    <span>Department of Manpower Development & Placement</span>
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        SSL 256-Bit Secure
                    </span>
                </div>
            </div>

            <!-- Right Login Form (lg:col-span-7) -->
            <div class="p-8 sm:p-10 lg:p-14 flex flex-col justify-center lg:col-span-7 bg-white">
                <div class="max-w-lg w-full mx-auto">
                    
                    <!-- Form Header -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200">
                                 Login
                            </span>
                            
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-2 tracking-tight">Account Login</h2>
                        <p class="text-sm text-slate-500 mt-1.5 leading-relaxed">
                            Please enter your email and password to access your dashboard.
                        </p>
                    </div>

                    <!-- Session Notification / Status -->
                    @if (session('status'))
                        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50/90 p-4 text-sm font-semibold text-emerald-800 flex items-center gap-3 shadow-xs">
                            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    <!-- Error Alert -->
                    @if ($errors->any())
                        <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50/90 p-4 text-sm font-semibold text-rose-800 flex items-start gap-3 shadow-xs">
                            <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-rose-900">Authentication Failed</p>
                                <p class="text-xs text-rose-700 mt-0.5 leading-relaxed">{{ $errors->first() }}</p>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <!-- Email Input -->
                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Email Address <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/></svg>
                                </div>
                                <input id="email" 
                                       type="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autofocus 
                                       autocomplete="username"
                                       autocorrect="off"
                                       autocapitalize="off"
                                       enterkeyhint="next"
                                       placeholder="you@example.com"
                                       class="w-full h-12 pl-12 pr-4 bg-slate-50/70 hover:bg-white focus:bg-white border {{ $errors->has('email') ? 'border-rose-300 ring-2 ring-rose-200' : 'border-slate-200 focus:border-emerald-600' }} rounded-xl text-sm sm:text-base text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200">
                            </div>
                            @if ($errors->has('email'))
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $errors->first('email') }}</p>
                            @endif
                        </div>

                        <!-- Password Input -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                    Password <span class="text-rose-500">*</span>
                                </label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 hover:underline transition-colors">
                                        Forgot password?
                                    </a>
                                @endif
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <input id="password" 
                                       type="password" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password"
                                       enterkeyhint="done"
                                       placeholder="••••••••"
                                       class="w-full h-12 pl-12 pr-12 bg-slate-50/70 hover:bg-white focus:bg-white border {{ $errors->has('password') ? 'border-rose-300 ring-2 ring-rose-200' : 'border-slate-200 focus:border-emerald-600' }} rounded-xl text-sm sm:text-base text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200">
                                <button type="button" 
                                        id="toggleLoginPassword" 
                                        aria-label="Toggle password visibility"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 transition-colors" 
                                        tabindex="-1">
                                    <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                </button>
                            </div>
                            @if ($errors->has('password'))
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $errors->first('password') }}</p>
                            @endif
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between pt-1">
                            <label for="remember" class="flex items-center gap-2.5 text-sm font-medium text-slate-600 cursor-pointer select-none">
                                <input id="remember" 
                                       type="checkbox" 
                                       name="remember" 
                                       class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                                <span>Remember my login on this device</span>
                            </label>
                        </div>

                        <!-- Sign In Button -->
                        <div class="pt-2">
                            <button type="submit" 
                                    class="w-full h-12 py-3.5 px-6 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-[0.99] text-white font-bold text-sm sm:text-base transition-all duration-200 shadow-lg shadow-emerald-600/25 flex items-center justify-center gap-2 cursor-pointer">
                                <span>Sign In to TrabaGo</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </form>

                    <!-- Registration Options Footer -->
                    <div class="mt-8 pt-7 border-t border-slate-100">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider text-center mb-3.5">
                            New to DMDP TrabaGo?
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <a href="{{ route('jobseeker.register') }}" 
                               class="flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/50 text-xs font-bold text-slate-700 hover:text-emerald-700 transition-all text-center">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>Register as Jobseeker</span>
                            </a>
                            <a href="{{ route('employer.register') }}" 
                               class="flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl border border-slate-200 hover:border-teal-500 hover:bg-teal-50/50 text-xs font-bold text-slate-700 hover:text-teal-700 transition-all text-center">
                                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span>Register as Employer</span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggleLoginPassword');
    const pwdInput = document.getElementById('password');
    if (toggleBtn && pwdInput) {
        toggleBtn.addEventListener('click', function() {
            const eyeOpen = this.querySelector('.eye-open');
            const eyeClosed = this.querySelector('.eye-closed');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                pwdInput.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        });
    }
});
</script>
@endpush
@endsection
