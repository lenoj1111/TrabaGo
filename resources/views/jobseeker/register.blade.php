@extends('layouts.public')

@section('title', 'Jobseeker Registration - DMDP Cebu City | TrabaGo')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-slate-50 via-emerald-50/20 to-slate-50 py-8 md:py-14">
    <div class="max-w-6xl xl:max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Top Breadcrumb / Return Link -->
        <div class="flex items-center justify-between mb-5">
            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-emerald-700 transition-colors py-1 px-2.5 rounded-lg hover:bg-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back to Role Selection</span>
            </a>
            <div class="text-xs text-slate-500">
                Already registered?
                <a href="{{ route('login') }}" class="font-semibold text-emerald-600 hover:text-emerald-700 hover:underline">Log in</a>
            </div>
        </div>

        <!-- Hero Header Card (collapses after step 1) -->
        <div id="heroCard" class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-950 via-emerald-900 to-slate-950 text-white shadow-xl shadow-emerald-950/20 p-6 sm:p-8 md:p-10 mb-8 border border-emerald-800/40 transition-all duration-300">
            <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>
            <div class="absolute right-1/3 -bottom-12 w-48 h-48 rounded-full bg-emerald-400/10 blur-2xl pointer-events-none"></div>

            <div class="relative z-10">
                <div id="heroContent">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-200 border border-emerald-400/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            DOLE NSRP &amp; DMDP Cebu City
                        </span>
                        <span class="text-xs font-medium text-emerald-100/90 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Takes ~4-5 minutes
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight text-white">
                        Jobseeker &amp; Trainee Registration
                    </h1>
                    <p class="text-emerald-100/85 text-sm sm:text-base mt-2 max-w-3xl leading-relaxed">
                        Create your profile to access automated AI job recommendations, free city-sponsored vocational skills training, and direct referrals to accredited employers.
                    </p>
                </div>

                <!-- Live Progress Bar in Header -->
                <div class="mt-6 pt-5 border-t border-emerald-800/60">
                    <div class="flex items-center justify-between text-xs font-semibold text-emerald-200 mb-2">
                        <span id="headerStepText" role="status" aria-live="polite">Step 1 of 8 · Account Credentials</span>
                        <span id="headerPercentText" class="text-emerald-300">13% Completed</span>
                    </div>
                    <div class="w-full bg-emerald-950/60 rounded-full h-2 overflow-hidden border border-emerald-800/40">
                        <div id="headerProgressBar" class="h-full bg-gradient-to-r from-emerald-500 to-emerald-300 transition-all duration-300 rounded-full" style="width: 13%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Global Alert Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <p class="font-bold">Success!</p>
                    <p class="text-emerald-700 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-sm flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <p class="font-bold">Registration Alert</p>
                    <p class="text-rose-700 mt-0.5">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-sm shadow-sm" id="laravelErrorBox">
                <div class="flex items-center gap-2 font-bold mb-1 text-rose-800">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Please review the highlighted fields to continue:
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs text-rose-700 ml-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Registration Form Container -->
        <form method="POST" action="{{ route('jobseeker.register.post') }}" class="bg-white border border-slate-200/90 rounded-3xl shadow-sm overflow-hidden" id="registrationForm" novalidate autocomplete="on">
            @csrf
            <input type="hidden" name="role" value="jobseeker">

            <!-- Stepper Navigation -->
            <div class="px-3 sm:px-6 py-4 bg-slate-50/80 border-b border-slate-200/70">
                <!-- Desktop / Tablet Stepper -->
                <div class="hidden md:flex items-center justify-between max-w-6xl mx-auto gap-1.5 lg:gap-2" id="stepIndicator" role="navigation" aria-label="Registration progress">
                    @php
                        $stepDefs = [
                            ['num' => 1, 'name' => 'Account', 'sub' => 'Credentials'],
                            ['num' => 2, 'name' => 'Personal', 'sub' => 'Basic info'],
                            ['num' => 3, 'name' => 'Address', 'sub' => 'Location'],
                            ['num' => 4, 'name' => 'Education', 'sub' => 'Academic'],
                            ['num' => 5, 'name' => 'Skills', 'sub' => 'Talents'],
                            ['num' => 6, 'name' => 'Preferences', 'sub' => 'Job goals'],
                            ['num' => 7, 'name' => 'Support', 'sub' => 'Social'],
                            ['num' => 8, 'name' => 'Review', 'sub' => 'Submit'],
                        ];
                    @endphp

                    @foreach($stepDefs as $idx => $step)
                        <button type="button" class="step-dot flex items-center gap-2 group cursor-pointer text-left transition-all py-1.5 px-1.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/40" data-step="{{ $step['num'] }}" aria-label="Step {{ $step['num'] }}: {{ $step['name'] }} - {{ $step['sub'] }}">
                            <div class="step-number w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs bg-slate-200 text-slate-600 transition-all duration-200 group-hover:scale-105 shadow-xs">
                                {{ $step['num'] }}
                            </div>
                            <div class="step-meta hidden xl:block leading-tight">
                                <span class="step-title block text-xs font-bold text-slate-700 transition-colors">{{ $step['name'] }}</span>
                                <span class="step-sub block text-[10px] text-slate-400 font-medium">{{ $step['sub'] }}</span>
                            </div>
                        </button>
                        @if(!$loop->last)
                            <div class="step-line flex-1 h-0.5 bg-slate-200 rounded-full transition-colors duration-200 min-w-3"></div>
                        @endif
                    @endforeach
                </div>

                <!-- Mobile Stepper (compact) -->
                <div class="md:hidden flex items-center justify-between gap-3" id="mobileStepper">
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-bold text-slate-800" id="mobileStepLabel">Account Credentials</span>
                            <span class="text-[10px] font-semibold text-emerald-700" id="mobileStepCount">1 / 8</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                            <div id="mobileProgressBar" class="h-full bg-emerald-600 transition-all duration-300 rounded-full" style="width: 13%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 1: Account Credentials -->
            <div class="step-content" data-step="1">
                <div class="px-6 py-5 bg-gradient-to-r from-slate-50 via-white to-slate-50 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-extrabold text-base shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-lg font-bold text-slate-900">Account Credentials</h2>
                        <p class="text-xs text-slate-500">Create the login credentials for your TrabaGo portal profile</p>
                    </div>
                    <span class="hidden sm:inline text-[10px] font-medium text-slate-400 bg-slate-100 px-2 py-1 rounded-full">~30 sec</span>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Email -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="email">
                                Email Address <span class="text-rose-500" aria-hidden="true">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="name@example.com" aria-describedby="emailHelp"
                                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('email') border-rose-400 ring-2 ring-rose-100 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1" id="emailHelp">We'll send match alerts and training invitations to this email.</p>
                            @error('email')
                                <span class="text-xs text-rose-600 font-semibold mt-1 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="password">
                                Password <span class="text-rose-500" aria-hidden="true">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <input type="password" id="password" name="password" required autocomplete="new-password" placeholder="At least 8 characters" aria-describedby="pwdRequirements"
                                       class="w-full pl-10 pr-10 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('password') border-rose-400 ring-2 ring-rose-100 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                                <button type="button" class="toggle-password-btn absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600" data-target="password" tabindex="-1" aria-label="Toggle password visibility">
                                    <svg class="w-4 h-4 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg class="w-4 h-4 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                </button>
                            </div>

                            <!-- Password requirements checklist -->
                            <div id="pwdRequirements" class="mt-2.5 space-y-1.5 text-[11px] text-slate-500">
                                <div class="flex items-center gap-1.5" data-pwd-req="length">
                                    <svg class="w-3.5 h-3.5 text-slate-300 shrink-0 pwd-req-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/></svg>
                                    <span>At least 8 characters</span>
                                </div>
                                <div class="flex items-center gap-1.5" data-pwd-req="mixed">
                                    <svg class="w-3.5 h-3.5 text-slate-300 shrink-0 pwd-req-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/></svg>
                                    <span>Uppercase &amp; lowercase letter</span>
                                </div>
                                <div class="flex items-center gap-1.5" data-pwd-req="number">
                                    <svg class="w-3.5 h-3.5 text-slate-300 shrink-0 pwd-req-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/></svg>
                                    <span>At least one number</span>
                                </div>
                            </div>

                            <div class="mt-1.5" aria-live="polite">
                                <span id="pwdStrengthText" class="text-[11px] font-medium text-slate-400">Password strength</span>
                            </div>
                            @error('password')
                                <span class="text-xs text-rose-600 font-semibold mt-1 flex items-center gap-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="password_confirmation">
                                Confirm Password <span class="text-rose-500" aria-hidden="true">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat your password"
                                       class="w-full pl-10 pr-10 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                                <button type="button" class="toggle-password-btn absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600" data-target="password_confirmation" tabindex="-1" aria-label="Toggle confirm password visibility">
                                    <svg class="w-4 h-4 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg class="w-4 h-4 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                </button>
                            </div>
                            <span id="pwdMatchNotice" class="text-[11px] font-medium text-slate-400 mt-1 block" aria-live="polite"></span>
                        </div>
                    </div>

                    <!-- Account Type Badge -->
                    <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-100 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black text-sm shrink-0">
                                JS
                            </div>
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-slate-900 block">Jobseeker Account</span>
                                <span class="text-[11px] text-slate-600 block">Free · Government-backed access to DMDP programs</span>
                            </div>
                        </div>
                        <span class="hidden sm:inline-flex items-center gap-1 text-[11px] font-bold text-emerald-800 bg-emerald-200/60 px-2.5 py-1 rounded-full shrink-0">
                            No fees
                        </span>
                    </div>

                    <!-- Early consent notice -->
                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        By continuing, you agree to our
                        <button type="button" class="text-emerald-700 font-semibold hover:underline" onclick="document.getElementById('privacyModal').classList.remove('hidden')">Data Privacy Notice (RA 10173)</button>.
                        Your information is used solely for DMDP employment referrals and training programs.
                    </p>
                </div>
            </div>

            <!-- STEP 2: Personal Information -->
            <div class="step-content hidden" data-step="2">
                <div class="px-6 py-5 bg-gradient-to-r from-slate-50 via-white to-slate-50 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-extrabold text-base shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-lg font-bold text-slate-900">Personal Information</h2>
                        <p class="text-xs text-slate-500">Official profile information compliant with DOLE NSRP Form 1</p>
                    </div>
                    <span class="hidden sm:inline text-[10px] font-medium text-slate-400 bg-slate-100 px-2 py-1 rounded-full">~45 sec</span>
                </div>

                <div class="p-6 sm:p-8 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <!-- First Name -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="first_name">
                                First Name <span class="text-rose-500" aria-hidden="true">*</span>
                            </label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required
                                   placeholder="e.g. Juan"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('first_name') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                            @error('first_name')<span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span>@enderror
                        </div>

                        <!-- Middle Name -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="middle_name">
                                Middle Name <span class="text-slate-400 font-normal lowercase">(optional)</span>
                            </label>
                            <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}"
                                   placeholder="e.g. Santos"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="last_name">
                                Last Name <span class="text-rose-500" aria-hidden="true">*</span>
                            </label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required
                                   placeholder="e.g. Dela Cruz"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('last_name') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                            @error('last_name')<span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span>@enderror
                        </div>

                        <!-- Birth Date -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="birth_date">
                                Birth Date <span class="text-rose-500" aria-hidden="true">*</span>
                            </label>
                            <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required
                                   max="{{ now()->subYears(15)->format('Y-m-d') }}"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('birth_date') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                            <span id="agePreview" class="text-[11px] text-emerald-700 font-medium mt-1 block" aria-live="polite"></span>
                            @error('birth_date')<span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span>@enderror
                        </div>

                        <!-- Sex -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="sex">
                                Sex <span class="text-rose-500" aria-hidden="true">*</span>
                            </label>
                            <select id="sex" name="sex" required class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('sex') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                                <option value="">Select sex...</option>
                                <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Prefer not to say" {{ old('sex') == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                            </select>
                            @error('sex')<span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span>@enderror
                        </div>

                        <!-- Civil Status -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="civil_status">
                                Civil Status <span class="text-rose-500" aria-hidden="true">*</span>
                            </label>
                            <select id="civil_status" name="civil_status" required class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('civil_status') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                                <option value="">Select status...</option>
                                <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>Single</option>
                                <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>Married</option>
                                <option value="Divorced" {{ old('civil_status') == 'Divorced' ? 'selected' : '' }}>Divorced / Separated</option>
                                <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                            </select>
                            @error('civil_status')<span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span>@enderror
                        </div>

                        <!-- Citizenship -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="citizenship">
                                Citizenship <span class="text-rose-500" aria-hidden="true">*</span>
                            </label>
                            <input type="text" id="citizenship" name="citizenship" value="{{ old('citizenship', 'Filipino') }}" required
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('citizenship') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                            @error('citizenship')<span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span>@enderror
                        </div>

                        <!-- Mobile Number -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="mobile_number">
                                Mobile Number <span class="text-rose-500" aria-hidden="true">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-semibold text-slate-500">+63</span>
                                <input type="tel" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" required placeholder="9123456789"
                                       inputmode="numeric" pattern="9[0-9]{9}" maxlength="10" aria-describedby="mobileHelp"
                                       class="w-full pl-12 pr-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('mobile_number') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1" id="mobileHelp">10 digits starting with 9 (e.g. 9123456789)</p>
                            @error('mobile_number')<span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span>@enderror
                        </div>

                        <!-- Employment Status -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="employment_status">
                                Employment Status <span class="text-rose-500" aria-hidden="true">*</span>
                            </label>
                            <select id="employment_status" name="employment_status" required class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('employment_status') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                                <option value="">Select status...</option>
                                <option value="Unemployed" {{ old('employment_status') == 'Unemployed' ? 'selected' : '' }}>Unemployed (Looking for work)</option>
                                <option value="Fresh Graduate" {{ old('employment_status') == 'Fresh Graduate' ? 'selected' : '' }}>Fresh Graduate</option>
                                <option value="Employed" {{ old('employment_status') == 'Employed' ? 'selected' : '' }}>Employed (Seeking career shift)</option>
                                <option value="Underemployed" {{ old('employment_status') == 'Underemployed' ? 'selected' : '' }}>Underemployed (Part-time / contractual)</option>
                                <option value="Self-employed" {{ old('employment_status') == 'Self-employed' ? 'selected' : '' }}>Self-employed / Freelancer</option>
                            </select>
                            @error('employment_status')<span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 3: Address & Residence -->
            <div class="step-content hidden" data-step="3">
                <div class="px-6 py-5 bg-gradient-to-r from-slate-50 via-white to-slate-50 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-extrabold text-base shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-lg font-bold text-slate-900">Current Residence &amp; Location</h2>
                        <p class="text-xs text-slate-500">Your address helps DMDP match you with local barangay job fairs and training centers</p>
                    </div>
                    <span class="hidden sm:inline text-[10px] font-medium text-slate-400 bg-slate-100 px-2 py-1 rounded-full">~20 sec</span>
                </div>

                <div class="p-6 sm:p-8 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Street Address -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="address_street">
                                Street Address / House / Bldg No. <span class="text-slate-400 font-normal lowercase">(optional)</span>
                            </label>
                            <input type="text" id="address_street" name="address[street]" value="{{ old('address.street') }}" placeholder="Unit/House No., Street Name, Subdivision"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                        </div>

                        <!-- Barangay -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="address_barangay">
                                Barangay <span class="text-rose-500">*</span>
                            </label>
                            @php
                                $cebuBarangays = \App\Services\BarangayDistributionService::getBarangayNames();
                            @endphp
                            <div class="relative">
                                <input list="cebu_barangays_list" id="address_barangay" name="address[barangay]" value="{{ old('address.barangay') }}" placeholder="Start typing your barangay..." required autocomplete="off"
                                       class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('address.barangay') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                                <datalist id="cebu_barangays_list">
                                    @foreach($cebuBarangays as $bName)
                                        <option value="{{ $bName }}">{{ $bName }}</option>
                                    @endforeach
                                </datalist>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1">Select or type your barangay for localized manpower distribution across Region 7.</p>
                        </div>

                        <!-- City -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="address_city">
                                City / Municipality <span class="text-rose-500">*</span>
                            </label>
                            <input list="region7_cities_list" type="text" id="address_city" name="address[city]" value="{{ old('address.city', 'Cebu City') }}" placeholder="e.g. Cebu City, Mandaue, Tagbilaran" required
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                            <datalist id="region7_cities_list">
                                <option value="Cebu City">
                                <option value="Mandaue City">
                                <option value="Lapu-Lapu City">
                                <option value="Talisay City">
                                <option value="Toledo City">
                                <option value="Danao City">
                                <option value="City of Naga">
                                <option value="Carcar City">
                                <option value="Bogo City">
                                <option value="Tagbilaran City">
                                <option value="Dumaguete City">
                                <option value="Bayawan City">
                                <option value="Bais City">
                                <option value="Tanjay City">
                                <option value="Guihulngan City">
                                <option value="Canlaon City">
                                <option value="Siquijor">
                            </datalist>
                        </div>

                        <!-- Province -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="address_province">
                                Province <span class="text-rose-500">*</span>
                            </label>
                            <input list="region7_provinces_list" type="text" id="address_province" name="address[province]" value="{{ old('address.province', 'Cebu') }}" placeholder="e.g. Cebu, Bohol, Negros Oriental, Siquijor" required
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                            <datalist id="region7_provinces_list">
                                <option value="Cebu">
                                <option value="Bohol">
                                <option value="Negros Oriental">
                                <option value="Siquijor">
                            </datalist>
                        </div>

                        <!-- Postal Code -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="address_postal">
                                Postal Code
                            </label>
                            <input type="text" id="address_postal" name="address[postal_code]" value="{{ old('address.postal_code', '6000') }}"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                        </div>

                        <!-- Country -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="address_country">
                                Country
                            </label>
                            <input type="text" id="address_country" name="address[country]" value="{{ old('address.country', 'Philippines') }}" readonly
                                   class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm text-slate-700 font-medium cursor-not-allowed" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 4: Educational Background -->
            <div class="step-content hidden" data-step="4">
                <div class="px-6 py-5 bg-gradient-to-r from-slate-50 via-white to-slate-50 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-extrabold text-base shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-lg font-bold text-slate-900">Educational Background</h2>
                        <p class="text-xs text-slate-500">Highest educational attainment and vocational certifications</p>
                    </div>
                    <span class="hidden sm:inline text-[10px] font-medium text-slate-400 bg-slate-100 px-2 py-1 rounded-full">~20 sec</span>
                </div>

                <div class="p-6 sm:p-8 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Education Level -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="education_level">
                                Highest Educational Attainment <span class="text-rose-500" aria-hidden="true">*</span>
                            </label>
                            <select id="education_level" name="education[level]" required class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                                <option value="">Select highest level attained...</option>
                                <option value="Elementary" {{ old('education.level') == 'Elementary' ? 'selected' : '' }}>Elementary Level / Graduate</option>
                                <option value="High School" {{ old('education.level') == 'High School' ? 'selected' : '' }}>Junior High School</option>
                                <option value="Senior High School" {{ old('education.level') == 'Senior High School' ? 'selected' : '' }}>Senior High School Graduate</option>
                                <option value="Vocational" {{ old('education.level') == 'Vocational' ? 'selected' : '' }}>Technical / Vocational Course (TESDA / DMDP)</option>
                                <option value="College" {{ old('education.level') == 'College' ? 'selected' : '' }}>College Undergraduate / Bachelor's Degree</option>
                                <option value="Post Graduate" {{ old('education.level') == 'Post Graduate' ? 'selected' : '' }}>Post Graduate (Master's / Doctorate)</option>
                            </select>
                        </div>

                        <!-- Course / Major -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="education_course">
                                Course / Major / Track <span class="text-slate-400 font-normal lowercase">(optional)</span>
                            </label>
                            <input type="text" id="education_course" name="education[course]" value="{{ old('education.course') }}" placeholder="e.g. BS Information Technology, General Academic Strand, Electrical Installation"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                        </div>

                        <!-- School / Institution -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="education_school">
                                School / University / Training Institution <span class="text-slate-400 font-normal lowercase">(optional)</span>
                            </label>
                            <input type="text" id="education_school" name="education[school]" value="{{ old('education.school') }}" placeholder="e.g. Cebu Technological University, DMDP Training Center"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                        </div>

                        <!-- Year Graduated -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="education_year">
                                Year Graduated / Last Attended <span class="text-slate-400 font-normal lowercase">(optional)</span>
                            </label>
                            <input type="number" id="education_year" name="education[year_graduated]" value="{{ old('education.year_graduated') }}" min="1960" max="{{ date('Y') + 1 }}" placeholder="{{ date('Y') }}"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 5: Skills Assessment -->
            <div class="step-content hidden" data-step="5">
                <div class="px-6 py-5 bg-gradient-to-r from-slate-50 via-white to-slate-50 border-b border-slate-100 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-extrabold text-base shrink-0 shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Skills Assessment &amp; Talents</h2>
                            <p class="text-xs text-slate-500">Powering our AI matching engine to connect you with the right job vacancies</p>
                        </div>
                    </div>
                    <span class="hidden sm:inline text-[10px] font-medium text-slate-400 bg-slate-100 px-2 py-1 rounded-full">~60 sec</span>
                </div>

                <div class="p-6 sm:p-8 space-y-7">
                    <!-- 21st Century Skills -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">1. 21st Century Core Skills <span class="text-rose-500" aria-hidden="true">*</span></h3>
                                <p class="text-xs text-slate-500">Choose the soft skills and capabilities you excel at (pick up to 5)</p>
                            </div>
                            <span id="centuryCountBadge" class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">0 / 5 selected</span>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 text-xs" role="group" aria-label="21st century skills">
                            @php
                                $centurySkills = [
                                    'Innovation', 'Team Work', 'Multitasking', 'Work Ethics',
                                    'Self Motivation', 'Creative Problem Solving', 'Problem Solving',
                                    'Critical Thinking', 'Decision Making', 'Stress Tolerance',
                                    'Planning and Organizing', 'Social Perceptiveness',
                                    'English Functional Skills', 'English Comprehension', 'Math Functional Skill'
                                ];
                            @endphp
                            @foreach($centurySkills as $cs)
                                <label class="skill-card flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 bg-white hover:border-emerald-300 hover:bg-emerald-50/30 cursor-pointer transition-all duration-150 select-none has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50">
                                    <input type="checkbox" name="skills_century[]" value="{{ $cs }}" class="century-checkbox rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 border-slate-300">
                                    <span class="font-medium text-slate-800">{{ $cs }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-[11px] font-semibold text-rose-600 mt-2 hidden" id="centuryError">Please select at least one core skill.</p>
                    </div>

                    <!-- Technical Skills -->
                    <div class="pt-5 border-t border-slate-100">
                        <div class="mb-3">
                            <h3 class="text-sm font-bold text-slate-900">2. Practical Technical Skills</h3>
                            <p class="text-xs text-slate-500">Skills acquired through hands-on work, apprenticeships, or personal practice</p>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5 text-xs" role="group" aria-label="Practical technical skills">
                            @php
                                $informalList = [
                                    'Carpentry', 'Masonry', 'Welding', 'Auto Mechanic',
                                    'Plumbing', 'Driving', 'Gardening', 'Tailoring',
                                    'Photography', 'Hairdressing', 'Cooking', 'Baking'
                                ];
                            @endphp
                            @foreach($informalList as $is)
                                <label class="skill-card flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 bg-white hover:border-emerald-300 hover:bg-emerald-50/30 cursor-pointer transition-all duration-150 select-none has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50">
                                    <input type="checkbox" name="skills_informal_tech[]" value="{{ $is }}" class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 border-slate-300">
                                    <span class="font-medium text-slate-800">{{ $is }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Specialized / Custom Skills -->
                    <div class="pt-5 border-t border-slate-100">
                        <div class="mb-3">
                            <h3 class="text-sm font-bold text-slate-900">3. Specialized or Professional Skills</h3>
                            <p class="text-xs text-slate-500">Add any specific technologies, trade licenses, tools, or software proficiencies</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <input type="text" name="skills[0][name]" value="{{ old('skills.0.name') }}" placeholder="e.g. QuickBooks, Python, Heavy Equipment Operation"
                                       class="custom-skill-input w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                                <input type="hidden" name="skills[0][type]" value="technical" />
                            </div>
                            <div>
                                <input type="text" name="skills[1][name]" value="{{ old('skills.1.name') }}" placeholder="e.g. Graphic Design, AutoCAD, BPO Customer Service"
                                       class="custom-skill-input w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                                <input type="hidden" name="skills[1][type]" value="technical" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 6: Job Preferences -->
            <div class="step-content hidden" data-step="6">
                <div class="px-6 py-5 bg-gradient-to-r from-slate-50 via-white to-slate-50 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-extrabold text-base shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-lg font-bold text-slate-900">Job Preferences &amp; Career Targets</h2>
                        <p class="text-xs text-slate-500">Define what roles, industries, and compensation you are looking for</p>
                    </div>
                    <span class="hidden sm:inline text-[10px] font-medium text-slate-400 bg-slate-100 px-2 py-1 rounded-full">~45 sec</span>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    <!-- Desired Occupations -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Target Occupations <span class="text-slate-400 font-normal lowercase">(ranked by preference)</span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <span class="text-[11px] font-semibold text-emerald-700 block mb-1">Primary Choice</span>
                                <input type="text" name="occupation1" value="{{ old('occupation1') }}" placeholder="e.g. Call Center Agent" list="occupationSuggestions"
                                       class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                            </div>
                            <div>
                                <span class="text-[11px] font-semibold text-slate-600 block mb-1">Secondary Choice</span>
                                <input type="text" name="occupation2" value="{{ old('occupation2') }}" placeholder="e.g. Administrative Clerk" list="occupationSuggestions"
                                       class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                            </div>
                            <div>
                                <span class="text-[11px] font-semibold text-slate-600 block mb-1">Tertiary Choice</span>
                                <input type="text" name="occupation3" value="{{ old('occupation3') }}" placeholder="e.g. Sales Associate" list="occupationSuggestions"
                                       class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                            </div>
                        </div>
                        <datalist id="occupationSuggestions">
                            @foreach(['Call Center Agent','Customer Service Representative','Administrative Clerk','Sales Associate','Cashier','Warehouse Staff','Delivery Driver','Security Guard','Electrician','Plumber','Welder','Carpenter','Mason','Auto Mechanic','Cook / Chef','Baker','Barista','Waiter / Waitress','Hotel Front Desk','Housekeeping','Caregiver','Nurse','Medical Technologist','Pharmacist Assistant','Teacher','Tutor','Accounting Staff','Bookkeeper','HR Assistant','IT Support','Web Developer','Graphic Designer','Encoder','Data Entry','Production Worker','Quality Control','Machine Operator','Forklift Operator','Truck Driver'] as $occ)
                                <option value="{{ $occ }}"></option>
                            @endforeach
                        </datalist>
                    </div>

                    <!-- Target Industries -->
                    <div class="pt-4 border-t border-slate-100">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Industry Sectors
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <input type="text" name="industry1" value="{{ old('industry1') }}" placeholder="e.g. BPO / Customer Care" list="industrySuggestions"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                            <input type="text" name="industry2" value="{{ old('industry2') }}" placeholder="e.g. Hospitality / Tourism" list="industrySuggestions"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                            <input type="text" name="industry3" value="{{ old('industry3') }}" placeholder="e.g. Construction / Trades" list="industrySuggestions"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                        </div>
                        <datalist id="industrySuggestions">
                            @foreach(['BPO / Customer Care','Hospitality / Tourism','Retail / Sales','Construction / Trades','Manufacturing','Information Technology','Healthcare','Education','Logistics / Transport','Food & Beverage','Real Estate','Finance / Banking','Government','Maritime','Agriculture','Automotive','Beauty & Wellness','Media / Creative'] as $ind)
                                <option value="{{ $ind }}"></option>
                            @endforeach
                        </datalist>
                    </div>

                    <!-- Compensation & Location -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="salary_min">
                                Expected Monthly Salary (PHP)
                                <button type="button" class="why-tooltip inline-flex items-center justify-center w-4 h-4 rounded-full bg-slate-200 text-slate-600 text-[10px] font-bold hover:bg-emerald-200 hover:text-emerald-800 transition-colors align-middle ml-0.5" aria-label="Why we ask about salary" data-tooltip="Salary expectations help DMDP match you with employers whose budget aligns with your needs, so you're not referred to jobs below your target. This is confidential and never shared without your consent.">?</button>
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-sm font-bold text-slate-500">₱</span>
                                    <input type="text" id="salary_min" name="salary_min" value="{{ old('salary_min') }}" placeholder="Min (e.g. 15000)"
                                           inputmode="numeric" pattern="[0-9,]*"
                                           class="w-full pl-8 pr-3 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                                </div>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-sm font-bold text-slate-500">₱</span>
                                    <input type="text" id="salary_max" name="salary_max" value="{{ old('salary_max') }}" placeholder="Max (e.g. 25000)"
                                           inputmode="numeric" pattern="[0-9,]*"
                                           class="w-full pl-8 pr-3 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1">Leave blank if open to any offer. This is kept confidential.</p>
                            <p class="text-[11px] font-semibold text-rose-600 mt-1 hidden" id="salaryError">Maximum must be greater than minimum.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="preferred_location">
                                Preferred Work Location
                            </label>
                            <input type="text" id="preferred_location" name="preferred_location" value="{{ old('preferred_location', 'Cebu City') }}" placeholder="e.g. Cebu City, IT Park, Mandaue"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 7: Social Status -->
            <div class="step-content hidden" data-step="7">
                <div class="px-6 py-5 bg-gradient-to-r from-slate-50 via-white to-slate-50 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-extrabold text-base shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-lg font-bold text-slate-900">Social Status &amp; Government Support</h2>
                        <p class="text-xs text-slate-500">Eligibility for DMDP and DOLE priority programs</p>
                    </div>
                    <span class="hidden sm:inline text-[10px] font-medium text-slate-400 bg-slate-100 px-2 py-1 rounded-full">~30 sec</span>
                </div>

                <div class="p-6 sm:p-8 space-y-4">
                    <p class="text-xs text-slate-600 leading-relaxed">
                        These categories help DMDP route your application to priority employment and livelihood programs under DOLE guidelines. All fields are optional and protected under the Data Privacy Act.
                    </p>

                    <!-- 4Ps Card -->
                    <div class="p-4 rounded-2xl border border-slate-200 bg-white hover:border-emerald-300 transition-colors">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" id="is_4ps_checkbox" name="is_4ps" value="1" {{ old('is_4ps') ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 border-slate-300" />
                            <div class="flex-1">
                                <span class="text-sm font-bold text-slate-900 inline-flex items-center gap-1.5 flex-wrap">
                                    Pantawid Pamilyang Pilipino Program (4Ps) Beneficiary
                                    <button type="button" class="why-tooltip inline-flex items-center justify-center w-4 h-4 rounded-full bg-slate-200 text-slate-600 text-[10px] font-bold hover:bg-emerald-200 hover:text-emerald-800 transition-colors" aria-label="Why we ask about 4Ps" data-tooltip="4Ps beneficiaries are given priority for DMDP livelihood assistance and emergency employment under DOLE guidelines. Marking this helps us route your application to the right support programs. Your status is verified only with your consent.">?</button>
                                </span>
                                <span class="text-xs text-slate-500 block mt-0.5">Priority inclusion for DMDP livelihood assistance and emergency employment</span>
                            </div>
                        </label>
                        <div id="householdIdField" class="mt-3 pt-3 border-t border-slate-100 {{ old('is_4ps') ? '' : 'hidden' }}">
                            <label class="block text-xs font-semibold text-slate-700 mb-1" for="household_id">4Ps Household Identification Number</label>
                            <input type="text" id="household_id" name="household_id" value="{{ old('household_id') }}" placeholder="e.g. 0722-12345678"
                                   class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600" />
                        </div>
                    </div>

                    <!-- OFW Card -->
                    <div class="p-4 rounded-2xl border border-slate-200 bg-white hover:border-emerald-300 transition-colors">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="is_ofw" value="1" {{ old('is_ofw') ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 border-slate-300" />
                            <div class="flex-1">
                                <span class="text-sm font-bold text-slate-900 block">OFW or Dependent of an OFW</span>
                                <span class="text-xs text-slate-500">Eligibility for DMDP repatriation reintegration programs and local placement</span>
                            </div>
                        </label>
                    </div>

                    <!-- PWD Card -->
                    <div class="p-4 rounded-2xl border border-slate-200 bg-white hover:border-emerald-300 transition-colors">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" id="is_pwd_checkbox" name="is_pwd" value="1" {{ old('is_pwd') ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 border-slate-300" />
                            <div class="flex-1">
                                <span class="text-sm font-bold text-slate-900 inline-flex items-center gap-1.5 flex-wrap">
                                    Person with Disability (PWD)
                                    <button type="button" class="why-tooltip inline-flex items-center justify-center w-4 h-4 rounded-full bg-slate-200 text-slate-600 text-[10px] font-bold hover:bg-emerald-200 hover:text-emerald-800 transition-colors" aria-label="Why we ask about PWD status" data-tooltip="Under RA 10524, PWDs are entitled to inclusive workplace referral and accessibility accommodations. This helps us prioritize employers with PWD-friendly workplaces and connect you with appropriate assistance. Disclosure is voluntary and protected by the Data Privacy Act.">?</button>
                                </span>
                                <span class="text-xs text-slate-500 block mt-0.5">Inclusive workplace referral under RA 10524 with accessibility accommodation</span>
                            </div>
                        </label>
                        <div id="pwdTypeField" class="mt-3 pt-3 border-t border-slate-100 {{ old('is_pwd') ? '' : 'hidden' }}">
                            <label class="block text-xs font-semibold text-slate-700 mb-1" for="pwd_type">Type of Disability / Assistance Needed</label>
                            <input type="text" id="pwd_type" name="pwd_type" value="{{ old('pwd_type') }}" placeholder="e.g. Visual, Hearing, Orthopedic, Speech"
                                   class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600" />
                        </div>
                    </div>

                    <!-- Note about next step -->
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex items-start gap-2.5 text-[11px] text-slate-600">
                        <svg class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Next, you'll review all your answers and confirm the certification before submitting.</span>
                    </div>
                </div>
            </div>

            <!-- STEP 8: Review & Submit -->
            <div class="step-content hidden" data-step="8">
                <div class="px-6 py-5 bg-gradient-to-r from-slate-50 via-white to-slate-50 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-extrabold text-base shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-lg font-bold text-slate-900">Review &amp; Confirm</h2>
                        <p class="text-xs text-slate-500">Verify your answers, then confirm the certification to submit</p>
                    </div>
                    <span class="hidden sm:inline text-[10px] font-medium text-slate-400 bg-slate-100 px-2 py-1 rounded-full">~30 sec</span>
                </div>

                <div class="p-6 sm:p-8 space-y-6">

                    <!-- Success-style summary banner -->
                    <div class="rounded-2xl bg-gradient-to-br from-emerald-50 to-white border border-emerald-100 p-4 flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-900">Almost done!</p>
                            <p class="text-xs text-slate-600 mt-0.5">Review each section below. Click "Edit" to fix anything before submitting.</p>
                        </div>

                        <!-- LGBTQ+ Membership Card -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-white hover:border-emerald-300 transition-colors">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="is_lgbtq" value="1" {{ old('is_lgbtq') ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 border-slate-300" />
                                <div class="flex-1">
                                    <span class="text-sm font-bold text-slate-900 block">LGBTQ+ Community Member</span>
                                    <span class="text-xs text-slate-500">Equal employment opportunity and anti-discrimination tracking under Region 7 PESO</span>
                                </div>
                            </label>
                        </div>

                        <!-- Senior Citizen Card -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-white hover:border-emerald-300 transition-colors">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="is_senior_citizen" value="1" {{ old('is_senior_citizen') ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 border-slate-300" />
                                <div class="flex-1">
                                    <span class="text-sm font-bold text-slate-900 block">Senior Citizen (60 years old and above)</span>
                                    <span class="text-xs text-slate-500">Elderly workforce inclusion and active productivity opportunities under RA 10911</span>
                                </div>
                            </label>
                        </div>

                        <!-- Immigrant Worker Card -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-white hover:border-emerald-300 transition-colors">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" id="is_immigrant_checkbox" name="is_immigrant" value="1" {{ old('is_immigrant') ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 border-slate-300" />
                                <div class="flex-1">
                                    <span class="text-sm font-bold text-slate-900 block">Immigrant / Migrant Worker / Foreign National</span>
                                    <span class="text-xs text-slate-500">Support & monitoring for immigrant laborers and inter-regional transferees to Region 7</span>
                                </div>
                            </label>
                            <div id="immigrantOriginField" class="mt-3 pt-3 border-t border-slate-100 {{ old('is_immigrant') ? '' : 'hidden' }}">
                                <label class="block text-xs font-semibold text-slate-700 mb-1" for="immigrant_origin">Country / Province of Origin or Visa/Permit Details</label>
                                <input type="text" id="immigrant_origin" name="immigrant_origin" value="{{ old('immigrant_origin') }}" placeholder="e.g. Country of Origin, Alien Employment Permit (AEP), or Transferee Province"
                                       class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                            </div>
                        </div>

                        <input type="hidden" name="region" value="Region VII - Central Visayas" />
                    </div>

                    <!-- Review Panel -->
                    <div class="rounded-2xl border border-slate-200 overflow-hidden">
                        <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Your Answers</span>
                        </div>
                        <div id="reviewPanel" class="p-4 sm:p-5 bg-white space-y-4">
                            <!-- Step 1 -->
                            <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Step 1 · Account</span>
                                    <p class="text-xs text-slate-700 mt-0.5 break-words"><span class="font-semibold">Email:</span> <span data-review="email">—</span></p>
                                </div>
                                <button type="button" class="review-edit-btn text-[11px] font-semibold text-emerald-700 hover:underline shrink-0" data-edit-step="1">Edit</button>
                            </div>
                            <!-- Step 2 -->
                            <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Step 2 · Personal</span>
                                    <p class="text-xs text-slate-700 mt-0.5"><span class="font-semibold">Name:</span> <span data-review="name">—</span></p>
                                    <p class="text-xs text-slate-700"><span class="font-semibold">Birth date:</span> <span data-review="birth_date">—</span></p>
                                    <p class="text-xs text-slate-700"><span class="font-semibold">Mobile:</span> <span data-review="mobile">—</span></p>
                                </div>
                                <button type="button" class="review-edit-btn text-[11px] font-semibold text-emerald-700 hover:underline shrink-0" data-edit-step="2">Edit</button>
                            </div>
                            <!-- Step 3 -->
                            <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Step 3 · Address</span>
                                    <p class="text-xs text-slate-700 mt-0.5"><span class="font-semibold">Barangay:</span> <span data-review="barangay">—</span></p>
                                </div>
                                <button type="button" class="review-edit-btn text-[11px] font-semibold text-emerald-700 hover:underline shrink-0" data-edit-step="3">Edit</button>
                            </div>
                            <!-- Step 4 -->
                            <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Step 4 · Education</span>
                                    <p class="text-xs text-slate-700 mt-0.5"><span class="font-semibold">Level:</span> <span data-review="education">—</span></p>
                                </div>
                                <button type="button" class="review-edit-btn text-[11px] font-semibold text-emerald-700 hover:underline shrink-0" data-edit-step="4">Edit</button>
                            </div>
                            <!-- Step 5 -->
                            <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Step 5 · Skills</span>
                                    <p class="text-xs text-slate-700 mt-0.5"><span class="font-semibold">Selected:</span> <span data-review="skills">—</span></p>
                                </div>
                                <button type="button" class="review-edit-btn text-[11px] font-semibold text-emerald-700 hover:underline shrink-0" data-edit-step="5">Edit</button>
                            </div>
                            <!-- Step 6 -->
                            <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Step 6 · Preferences</span>
                                    <p class="text-xs text-slate-700 mt-0.5"><span class="font-semibold">Primary occupation:</span> <span data-review="occupation">—</span></p>
                                    <p class="text-xs text-slate-700"><span class="font-semibold">Salary range:</span> <span data-review="salary">—</span></p>
                                </div>
                                <button type="button" class="review-edit-btn text-[11px] font-semibold text-emerald-700 hover:underline shrink-0" data-edit-step="6">Edit</button>
                            </div>
                            <!-- Step 7 -->
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Step 7 · Social Support</span>
                                    <p class="text-xs text-slate-700 mt-0.5"><span class="font-semibold">Programs:</span> <span data-review="social">None selected</span></p>
                                </div>
                                <button type="button" class="review-edit-btn text-[11px] font-semibold text-emerald-700 hover:underline shrink-0" data-edit-step="7">Edit</button>
                            </div>
                        </div>
                    </div>

                    <!-- Privacy & Consent -->
                    <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" id="termsCheck" required class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 border-slate-300" />
                            <span class="text-xs text-slate-700 leading-relaxed">
                                I hereby certify that all information submitted is true and correct in accordance with Republic Act No. 8759 (PESO Act) and Republic Act No. 10173 (Data Privacy Act of 2012). I authorize the Department of Manpower Development and Placement (DMDP) Cebu City to process my credentials for employment referrals and vocational training programs.
                            </span>
                        </label>
                        <p class="text-[11px] font-semibold text-rose-600 mt-2 hidden" id="termsError">You must agree to the certification before completing registration.</p>
                    </div>
                </div>
            </div>

            <!-- Sticky Navigation -->
            <div class="px-6 py-4 bg-slate-50/90 border-t border-slate-200/80 flex items-center justify-between sticky bottom-0 z-20">
                <button type="button" id="prevBtn" class="px-5 py-2.5 text-slate-700 font-semibold rounded-xl border border-slate-300 bg-white hover:bg-slate-100 active:scale-[0.98] transition-all flex items-center gap-2 text-sm shadow-xs hidden">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Previous
                </button>

                <div class="flex items-center gap-2 ml-auto">
                    <button type="button" id="saveDraftBtn" class="px-4 py-2.5 text-slate-600 font-semibold rounded-xl border border-slate-300 bg-white hover:bg-slate-100 active:scale-[0.98] transition-all text-xs shadow-xs">
                        Save draft
                    </button>
                    <button type="button" id="nextBtn" class="px-7 py-2.5 text-white font-bold rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] transition-all flex items-center gap-2 text-sm shadow-lg shadow-emerald-600/20">
                        Continue
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <button type="submit" id="submitBtn" class="px-8 py-2.5 text-white font-bold rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] transition-all flex items-center gap-2 text-sm shadow-lg shadow-emerald-600/25 hidden">
                        <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Complete Registration
                    </button>
                </div>
            </div>
        </form>

        <!-- Help Footer -->
        <div class="mt-8 text-center text-xs text-slate-500 space-y-1">
            <p>Need assistance with your registration? Contact DMDP Cebu City Hall at (032) 888-1234 or visit the 2F Annex Building.</p>
            <p class="text-slate-400">Department of Manpower Development and Placement · Cebu City Government</p>
        </div>

    </div>
</div>

<!-- Privacy Modal -->
<div id="privacyModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-base font-bold text-slate-900">Data Privacy Notice (RA 10173)</h3>
            <button type="button" onclick="document.getElementById('privacyModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600" aria-label="Close">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="text-xs text-slate-600 space-y-2 max-h-80 overflow-y-auto leading-relaxed">
            <p>Your personal information is collected and processed by the Department of Manpower Development and Placement (DMDP) Cebu City under the Data Privacy Act of 2012 (RA 10173).</p>
            <p><strong>Purpose:</strong> To provide employment referrals, training program invitations, and labor market matching services.</p>
            <p><strong>Disclosure:</strong> Your information may be shared with accredited employers only when you are referred to a specific vacancy, or with training providers when you enroll in a program.</p>
            <p><strong>Retention:</strong> Your profile is retained for the duration of your registration and up to two (2) years of inactivity, after which it is archived per government records policy.</p>
            <p><strong>Rights:</strong> You have the right to access, correct, or request deletion of your data by contacting the DMDP Data Protection Officer.</p>
            <p><strong>Consent:</strong> By completing this registration, you consent to the processing described above.</p>
        </div>
        <button type="button" onclick="document.getElementById('privacyModal').classList.add('hidden')" class="mt-4 w-full py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-700">Got it</button>
    </div>
</div>

<!-- Toast container -->
<div id="toastContainer" class="fixed bottom-4 right-4 z-[60] space-y-2 pointer-events-none" aria-live="polite"></div>

<!-- Global tooltip popup -->
<div id="globalTooltip" class="hidden fixed z-[70] max-w-xs px-3 py-2 rounded-xl bg-slate-900 text-white text-xs leading-snug shadow-xl pointer-events-none"></div>

@push('styles')
<style>
/* Step styling */
.step-dot.active .step-number {
    background-color: #059669;
    color: #ffffff;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2);
}
.step-dot.active .step-title { color: #065f46; }
.step-dot.completed .step-number { background-color: #10b981; color: #ffffff; }
.step-dot.completed .step-title { color: #047857; }
.step-dot:disabled, .step-dot[aria-disabled="true"] {
    opacity: 0.5;
    cursor: not-allowed;
}
.step-line.completed { background-color: #10b981; }

/* Skill card selection */
.skill-card:has(input:checked) {
    border-color: #10b981;
    background-color: #ecfdf5;
}

/* Toast animations */
@keyframes toastIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
.toast-item {
    animation: toastIn 0.2s ease-out;
    pointer-events: auto;
}

/* Hero collapse transition */
#heroCard.collapsed #heroContent { display: none; }
#heroCard.collapsed {
    padding: 1rem 1.25rem;
    margin-bottom: 1rem;
}

/* Soft focus ring for keyboard nav */
input:focus-visible, select:focus-visible, textarea:focus-visible, button:focus-visible {
    outline: none;
}

/* Barangay combobox active option */
.barangay-option[aria-selected="true"] {
    background-color: #ecfdf5;
    color: #065f46;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ---------- Config ----------
    const DRAFT_KEY = 'trabago_jobseeker_draft_v2';
    const TOTAL_STEPS = 8;
    const stepTitles = [
        'Account Credentials',
        'Personal Information',
        'Residence & Address',
        'Educational Attainment',
        'Skills Assessment',
        'Job Preferences',
        'Social Status',
        'Review & Confirm'
    ];
    const barangayOptions = @json($cebuBarangays);

    // ---------- Elements ----------
    let currentStep = 1;
    const form = document.getElementById('registrationForm');
    const stepContents = document.querySelectorAll('.step-content');
    const stepDots = document.querySelectorAll('.step-dot');
    const stepLines = document.querySelectorAll('.step-line');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const saveDraftBtn = document.getElementById('saveDraftBtn');
    const headerStepText = document.getElementById('headerStepText');
    const headerPercentText = document.getElementById('headerPercentText');
    const headerProgressBar = document.getElementById('headerProgressBar');
    const heroCard = document.getElementById('heroCard');
    const mobileStepLabel = document.getElementById('mobileStepLabel');
    const mobileStepCount = document.getElementById('mobileStepCount');
    const mobileProgressBar = document.getElementById('mobileProgressBar');
    const toastContainer = document.getElementById('toastContainer');

    // ---------- Toast helper ----------
    function showToast(message, type = 'info') {
        const colors = {
            info: 'bg-slate-900 text-white',
            success: 'bg-emerald-600 text-white',
            error: 'bg-rose-600 text-white',
            warning: 'bg-amber-500 text-white',
        };
        const el = document.createElement('div');
        el.className = `toast-item ${colors[type] || colors.info} px-4 py-2.5 rounded-xl shadow-lg text-xs font-semibold max-w-xs`;
        el.textContent = message;
        toastContainer.appendChild(el);
        setTimeout(() => {
            el.style.transition = 'opacity 0.3s, transform 0.3s';
            el.style.opacity = '0';
            el.style.transform = 'translateY(8px)';
            setTimeout(() => el.remove(), 300);
        }, 3000);
    }

    // ---------- Step UI ----------
    function updateStepUI() {
        stepContents.forEach(c => {
            c.classList.toggle('hidden', parseInt(c.dataset.step) !== currentStep);
        });

        stepDots.forEach(d => {
            const s = parseInt(d.dataset.step);
            d.classList.remove('active', 'completed');
            d.setAttribute('aria-current', 'false');
            if (s === currentStep) {
                d.classList.add('active');
                d.setAttribute('aria-current', 'step');
            } else if (s < currentStep) {
                d.classList.add('completed');
            }
            const isFuture = s > currentStep;
            d.disabled = isFuture;
            d.setAttribute('aria-disabled', isFuture ? 'true' : 'false');
        });

        stepLines.forEach((l, idx) => {
            l.classList.toggle('completed', idx < currentStep - 1);
        });

        const percent = Math.round((currentStep / TOTAL_STEPS) * 100);
        headerStepText.textContent = `Step ${currentStep} of ${TOTAL_STEPS} · ${stepTitles[currentStep - 1]}`;
        headerPercentText.textContent = `${percent}% Completed`;
        headerProgressBar.style.width = `${percent}%`;
        mobileStepLabel.textContent = stepTitles[currentStep - 1];
        mobileStepCount.textContent = `${currentStep} / ${TOTAL_STEPS}`;
        mobileProgressBar.style.width = `${percent}%`;

        prevBtn.classList.toggle('hidden', currentStep === 1);

        const isLast = currentStep === TOTAL_STEPS;
        nextBtn.classList.toggle('hidden', isLast);
        submitBtn.classList.toggle('hidden', !isLast);
        saveDraftBtn.classList.toggle('hidden', isLast);

        heroCard.classList.toggle('collapsed', currentStep > 1);

        document.querySelector(`.step-content[data-step="${currentStep}"]`)
            ?.querySelectorAll('.border-rose-400').forEach(el => {
                if (el.value && el.value.trim() !== '') {
                    el.classList.remove('border-rose-400', 'ring-2', 'ring-rose-100');
                }
            });

        if (isLast) updateSummaryReview();
    }

    // ---------- Validation ----------
    function validateStep(step) {
        const c = document.querySelector(`.step-content[data-step="${step}"]`);
        if (!c) return true;
        let isValid = true;
        let firstInvalid = null;

        c.querySelectorAll('input[required], select[required], textarea[required]').forEach(input => {
            if (input.id === 'termsCheck') return;
            let valid = true;
            if (input.type === 'checkbox') valid = input.checked;
            else valid = input.value.trim() !== '';

            if (input.id === 'mobile_number' && valid) {
                valid = /^9\d{9}$/.test(input.value.trim());
            }

            input.classList.toggle('border-rose-400', !valid);
            input.classList.toggle('ring-2', !valid);
            input.classList.toggle('ring-rose-100', !valid);
            if (!valid) {
                isValid = false;
                if (!firstInvalid) firstInvalid = input;
            }
        });

        if (step === 1) {
            const pwd = document.getElementById('password');
            const pwdConf = document.getElementById('password_confirmation');
            if (pwd.value && pwdConf.value && pwd.value !== pwdConf.value) {
                isValid = false;
                pwdConf.classList.add('border-rose-400', 'ring-2', 'ring-rose-100');
                if (!firstInvalid) firstInvalid = pwdConf;
            }
        }

        if (step === 5) {
            const checked = c.querySelectorAll('.century-checkbox:checked').length;
            const err = document.getElementById('centuryError');
            if (checked === 0) {
                isValid = false;
                err?.classList.remove('hidden');
                if (!firstInvalid) firstInvalid = c.querySelector('.century-checkbox');
            } else {
                err?.classList.add('hidden');
            }
        }

        if (step === 3) {
            const brgy = document.getElementById('address_barangay');
            const err = document.getElementById('barangayError');
            const cityInput = document.getElementById('address_city')?.value?.trim() || '';
            const isCebuCity = cityInput.toLowerCase() === 'cebu city';
            if (brgy.value.trim() && isCebuCity && !barangayOptions.includes(brgy.value.trim())) {
                isValid = false;
                brgy.classList.add('border-rose-400');
                err?.classList.remove('hidden');
                if (!firstInvalid) firstInvalid = brgy;
            } else {
                err?.classList.add('hidden');
                brgy.classList.remove('border-rose-400');
            }
        }

        if (step === 6) {
            const min = parseInt((document.getElementById('salary_min')?.value || '').replace(/,/g, '')) || 0;
            const max = parseInt((document.getElementById('salary_max')?.value || '').replace(/,/g, '')) || 0;
            const err = document.getElementById('salaryError');
            if (min && max && max < min) {
                isValid = false;
                err?.classList.remove('hidden');
                if (!firstInvalid) firstInvalid = document.getElementById('salary_max');
            } else {
                err?.classList.add('hidden');
            }
        }

        if (firstInvalid) {
            firstInvalid.focus();
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return isValid;
    }

    function goToStep(step) {
        if (step > currentStep && !validateStep(currentStep)) {
            showToast('Please complete the required fields on this step.', 'warning');
            return;
        }
        currentStep = Math.max(1, Math.min(TOTAL_STEPS, step));
        updateStepUI();
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        saveDraft();
    }

    nextBtn.addEventListener('click', () => goToStep(currentStep + 1));
    prevBtn.addEventListener('click', () => goToStep(currentStep - 1));

    stepDots.forEach(dot => {
        dot.addEventListener('click', function() {
            const targetStep = parseInt(this.dataset.step);
            if (targetStep < currentStep || validateStep(currentStep)) {
                goToStep(targetStep);
            } else {
                showToast('Complete the current step before jumping ahead.', 'warning');
            }
        });
    });

    // ---------- Password visibility ----------
    document.querySelectorAll('.toggle-password-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = document.getElementById(this.dataset.target);
            const eyeOpen = this.querySelector('.eye-open');
            const eyeClosed = this.querySelector('.eye-closed');
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            eyeOpen.classList.toggle('hidden', show);
            eyeClosed.classList.toggle('hidden', !show);
        });
    });

    // ---------- Password strength & checklist (3 requirements) ----------
    const pwdInput = document.getElementById('password');
    const pwdConfInput = document.getElementById('password_confirmation');
    const pwdStrengthText = document.getElementById('pwdStrengthText');
    const pwdMatchNotice = document.getElementById('pwdMatchNotice');

    function setReq(el, met) {
        const icon = el.querySelector('.pwd-req-icon');
        if (!icon) return;
        if (met) {
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>`;
            icon.classList.remove('text-slate-300');
            icon.classList.add('text-emerald-600');
            el.querySelector('span').classList.add('text-emerald-700');
            el.querySelector('span').classList.remove('text-slate-500');
        } else {
            icon.innerHTML = `<circle cx="12" cy="12" r="9" stroke-width="2"/>`;
            icon.classList.remove('text-emerald-600');
            icon.classList.add('text-slate-300');
            el.querySelector('span').classList.remove('text-emerald-700');
            el.querySelector('span').classList.add('text-slate-500');
        }
    }

    function evalPassword() {
        const val = pwdInput.value;
        const reqs = {
            length: val.length >= 8,
            mixed: /[A-Z]/.test(val) && /[a-z]/.test(val),
            number: /[0-9]/.test(val),
        };
        document.querySelectorAll('[data-pwd-req]').forEach(el => {
            setReq(el, reqs[el.dataset.pwdReq]);
        });
        const score = Object.values(reqs).filter(Boolean).length;
        const labels = ['Too weak', 'Fair', 'Strong'];
        pwdStrengthText.textContent = val ? `Strength: ${labels[score]}` : 'Password strength';
        pwdStrengthText.className = 'text-[11px] font-medium ' +
            (score <= 1 ? 'text-rose-500' : score === 2 ? 'text-amber-500' : 'text-emerald-600');
        checkPasswordMatch();
    }

    pwdInput?.addEventListener('input', evalPassword);

    function checkPasswordMatch() {
        if (!pwdConfInput?.value) {
            pwdMatchNotice.textContent = '';
            return;
        }
        if (pwdInput.value === pwdConfInput.value) {
            pwdMatchNotice.innerHTML = '<span class="inline-flex items-center gap-1"><svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Passwords match</span>';
            pwdMatchNotice.className = 'text-[11px] font-medium text-emerald-600 mt-1 block';
            pwdConfInput.classList.remove('border-rose-400');
        } else {
            pwdMatchNotice.innerHTML = '<span class="inline-flex items-center gap-1"><svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg> Passwords do not match</span>';
            pwdMatchNotice.className = 'text-[11px] font-medium text-rose-500 mt-1 block';
        }
    }
    pwdConfInput?.addEventListener('input', checkPasswordMatch);

    // ---------- Birth date ----------
    const birthDateInput = document.getElementById('birth_date');
    const agePreview = document.getElementById('agePreview');
    birthDateInput?.addEventListener('change', function() {
        if (!this.value) { agePreview.textContent = ''; return; }
        const [y, m, d] = this.value.split('-').map(Number);
        const dob = new Date(y, m - 1, d);
        const now = new Date();
        let age = now.getFullYear() - dob.getFullYear();
        const beforeBirthday = (now.getMonth() < dob.getMonth()) ||
            (now.getMonth() === dob.getMonth() && now.getDate() < dob.getDate());
        if (beforeBirthday) age--;
        if (isNaN(age) || age < 0) { agePreview.textContent = ''; return; }
        if (age < 15) {
            agePreview.textContent = `${age} years old — must be at least 15 to register`;
            agePreview.className = 'text-[11px] text-rose-600 font-semibold mt-1 block';
            this.classList.add('border-rose-400');
        } else {
            agePreview.textContent = `${age} years old`;
            agePreview.className = 'text-[11px] text-emerald-700 font-medium mt-1 block';
            this.classList.remove('border-rose-400');
        }
    });

    // ---------- Mobile number formatting ----------
    const mobileInput = document.getElementById('mobile_number');
    mobileInput?.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
    });

    // ---------- 21st century skills ----------
    const centuryCheckboxes = document.querySelectorAll('.century-checkbox');
    const centuryCountBadge = document.getElementById('centuryCountBadge');
    function updateCenturyCount() {
        const n = document.querySelectorAll('.century-checkbox:checked').length;
        if (!centuryCountBadge) return;
        centuryCountBadge.textContent = `${n} / 5 selected`;
        centuryCountBadge.className = n === 5
            ? 'text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800'
            : 'text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700';
        if (n > 0) document.getElementById('centuryError')?.classList.add('hidden');
    }
    centuryCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const checked = document.querySelectorAll('.century-checkbox:checked');
            if (checked.length > 5) {
                this.checked = false;
                showToast('You can select up to 5 core skills only.', 'warning');
            }
            updateCenturyCount();
        });
    });

    // ---------- 4Ps / PWD toggles ----------
    document.getElementById('is_4ps_checkbox')?.addEventListener('change', function() {
        document.getElementById('householdIdField').classList.toggle('hidden', !this.checked);
    });
    document.getElementById('is_pwd_checkbox')?.addEventListener('change', function() {
        document.getElementById('pwdTypeField').classList.toggle('hidden', !this.checked);
    });

    // ---------- Salary formatting ----------
    ['salary_min', 'salary_max'].forEach(id => {
        const el = document.getElementById(id);
        el?.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9,]/g, '');
        });
    });

    // ---------- Tooltips ----------
    const tooltip = document.getElementById('globalTooltip');
    document.querySelectorAll('.why-tooltip').forEach(btn => {
        const show = () => {
            tooltip.textContent = btn.dataset.tooltip || '';
            tooltip.classList.remove('hidden');
            const rect = btn.getBoundingClientRect();
            const ttRect = tooltip.getBoundingClientRect();
            let left = rect.left + rect.width / 2 - ttRect.width / 2;
            let top = rect.bottom + 8;
            if (left < 8) left = 8;
            if (left + ttRect.width > window.innerWidth - 8) left = window.innerWidth - ttRect.width - 8;
            if (top + ttRect.height > window.innerHeight - 8) top = rect.top - ttRect.height - 8;
            tooltip.style.left = `${left}px`;
            tooltip.style.top = `${top}px`;
        };
        const hide = () => tooltip.classList.add('hidden');
        btn.addEventListener('mouseenter', show);
        btn.addEventListener('focus', show);
        btn.addEventListener('mouseleave', hide);
        btn.addEventListener('blur', hide);
        btn.addEventListener('click', (e) => { e.preventDefault(); show(); });
    });

    // ---------- Barangay combobox ----------
    const brgyInput = document.getElementById('address_barangay');
    const brgyListbox = document.getElementById('barangayListbox');
    let brgyActiveIndex = -1;

    function renderBrgyList(query) {
        const q = query.trim().toLowerCase();
        const filtered = q
            ? barangayOptions.filter(b => b.toLowerCase().includes(q)).slice(0, 12)
            : barangayOptions.slice(0, 12);
        if (filtered.length === 0) {
            brgyListbox.innerHTML = '<div class="px-3 py-2 text-xs text-slate-400">No match found</div>';
        } else {
            brgyListbox.innerHTML = filtered.map((b, i) =>
                `<div class="barangay-option px-3 py-2 text-xs cursor-pointer hover:bg-emerald-50" role="option" data-value="${b}" data-index="${i}">${b}</div>`
            ).join('');
        }
        brgyListbox.classList.remove('hidden');
        brgyInput.setAttribute('aria-expanded', 'true');
        brgyActiveIndex = -1;
    }

    function hideBrgyList() {
        brgyListbox.classList.add('hidden');
        brgyInput.setAttribute('aria-expanded', 'false');
        brgyActiveIndex = -1;
    }

    brgyInput?.addEventListener('focus', () => renderBrgyList(brgyInput.value));
    brgyInput?.addEventListener('input', function() {
        renderBrgyList(this.value);
        document.getElementById('barangayError')?.classList.add('hidden');
        this.classList.remove('border-rose-400');
    });
    brgyInput?.addEventListener('blur', () => {
        setTimeout(hideBrgyList, 150);
        const v = brgyInput.value.trim();
        const err = document.getElementById('barangayError');
        const cityInput = document.getElementById('address_city')?.value?.trim() || '';
        const isCebuCity = cityInput.toLowerCase() === 'cebu city';
        if (v && isCebuCity && !barangayOptions.includes(v)) {
            err?.classList.remove('hidden');
            brgyInput.classList.add('border-rose-400');
        } else {
            err?.classList.add('hidden');
            brgyInput.classList.remove('border-rose-400');
        }
    });
    brgyInput?.addEventListener('keydown', function(e) {
        if (brgyListbox.classList.contains('hidden')) return;
        const opts = brgyListbox.querySelectorAll('.barangay-option');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            brgyActiveIndex = Math.min(brgyActiveIndex + 1, opts.length - 1);
            opts.forEach((o, i) => o.setAttribute('aria-selected', i === brgyActiveIndex ? 'true' : 'false'));
            opts[brgyActiveIndex]?.scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            brgyActiveIndex = Math.max(brgyActiveIndex - 1, 0);
            opts.forEach((o, i) => o.setAttribute('aria-selected', i === brgyActiveIndex ? 'true' : 'false'));
            opts[brgyActiveIndex]?.scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'Enter' && brgyActiveIndex >= 0) {
            e.preventDefault();
            brgyInput.value = opts[brgyActiveIndex].dataset.value;
            hideBrgyList();
            brgyInput.classList.remove('border-rose-400');
            document.getElementById('barangayError')?.classList.add('hidden');
        } else if (e.key === 'Escape') {
            hideBrgyList();
        }
    });
    brgyListbox?.addEventListener('mousedown', (e) => {
        const opt = e.target.closest('.barangay-option');
        if (opt) {
            e.preventDefault();
            brgyInput.value = opt.dataset.value;
            hideBrgyList();
            brgyInput.classList.remove('border-rose-400');
            document.getElementById('barangayError')?.classList.add('hidden');
        }
    });

    // ---------- Review panel ----------
    const reviewPanel = document.getElementById('reviewPanel');
    document.querySelectorAll('.review-edit-btn').forEach(btn => {
        btn.addEventListener('click', () => goToStep(parseInt(btn.dataset.editStep)));
    });

    function setReview(name, value) {
        const el = reviewPanel?.querySelector(`[data-review="${name}"]`);
        if (el) el.textContent = value && value.trim() ? value : '—';
    }

    // Immigrant toggle
    const isImmigrant = document.getElementById('is_immigrant_checkbox');
    const immigrantOriginField = document.getElementById('immigrantOriginField');
    if (isImmigrant && immigrantOriginField) {
        isImmigrant.addEventListener('change', function() {
            immigrantOriginField.classList.toggle('hidden', !this.checked);
        });
    }

    // Summary Review in Step 7
    function updateSummaryReview() {
        setReview('email', document.getElementById('email')?.value || '');
        const fn = document.getElementById('first_name')?.value || '';
        const mn = document.getElementById('middle_name')?.value || '';
        const ln = document.getElementById('last_name')?.value || '';
        const em = document.getElementById('email')?.value || '';
        const brgy = document.getElementById('address_barangay')?.value || '';
        const edu = document.getElementById('education_level')?.value || '';

        setReview('name', [fn, mn, ln].filter(Boolean).join(' '));
        setReview('birth_date', document.getElementById('birth_date')?.value || '');
        setReview('mobile', document.getElementById('mobile_number')?.value ? `+63 ${document.getElementById('mobile_number').value}` : '');
        setReview('barangay', brgy || '');
        setReview('education', edu || '');
        const skills = [...document.querySelectorAll('.century-checkbox:checked')].map(c => c.value);
        setReview('skills', skills.join(', '));
        setReview('occupation', document.getElementById('occupation1')?.value || '');
        const smin = document.getElementById('salary_min')?.value || '';
        const smax = document.getElementById('salary_max')?.value || '';
        setReview('salary', smin || smax ? `₱${smin || '0'} - ₱${smax || '?'}` : '');
        const social = [];
        if (document.getElementById('is_4ps_checkbox')?.checked) social.push('4Ps');
        if (document.querySelector('input[name="is_ofw"]')?.checked) social.push('OFW');
        if (document.getElementById('is_pwd_checkbox')?.checked) social.push('PWD');
        if (document.getElementById('is_lgbtq_checkbox')?.checked) social.push('LGBTQ+');
        if (document.getElementById('is_senior_checkbox')?.checked) social.push('Senior Citizen');
        if (document.getElementById('is_immigrant_checkbox')?.checked) social.push('Immigrant Worker');
        setReview('social', social.length ? social.join(', ') : 'None selected');

        const sName = document.getElementById('summaryName');
        const sEmail = document.getElementById('summaryEmail');
        const sBrgy = document.getElementById('summaryBarangay');
        const sEdu = document.getElementById('summaryEdu');

        if (sName) sName.textContent = (fn || ln) ? `${fn} ${ln}` : 'Not provided';
        if (sEmail) sEmail.textContent = em || 'Not provided';
        if (sBrgy) sBrgy.textContent = brgy || 'Not specified';
        if (sEdu) sEdu.textContent = edu || 'Not provided';
    }

    // ---------- Draft save/restore ----------
    function collectDraft() {
        const data = {};
        form.querySelectorAll('input, select, textarea').forEach(el => {
            if (!el.name || el.type === 'password' || el.type === 'hidden' || el.name === '_token') return;
            if (el.type === 'checkbox') {
                if (el.name.endsWith('[]')) {
                    data[el.name] = data[el.name] || [];
                    if (el.checked) data[el.name].push(el.value);
                } else {
                    data[el.name] = el.checked;
                }
            } else {
                data[el.name] = el.value;
            }
        });
        return { data, step: currentStep, savedAt: Date.now() };
    }

    function saveDraft() {
        try {
            localStorage.setItem(DRAFT_KEY, JSON.stringify(collectDraft()));
        } catch (e) { /* quota / disabled */ }
    }

    function restoreDraft() {
        let raw;
        try { raw = localStorage.getItem(DRAFT_KEY); } catch (e) { return; }
        if (!raw) return;
        let parsed;
        try { parsed = JSON.parse(raw); } catch (e) { return; }
        if (!parsed || !parsed.data) return;
        const data = parsed.data;

        form.querySelectorAll('input, select, textarea').forEach(el => {
            if (!el.name || el.type === 'password' || el.type === 'hidden') return;
            if (!(el.name in data)) return;
            const v = data[el.name];
            if (el.type === 'checkbox') {
                if (el.name.endsWith('[]')) {
                    el.checked = Array.isArray(v) && v.includes(el.value);
                } else {
                    el.checked = !!v;
                }
            } else {
                el.value = v;
            }
        });

        document.getElementById('is_4ps_checkbox')?.dispatchEvent(new Event('change'));
        document.getElementById('is_pwd_checkbox')?.dispatchEvent(new Event('change'));
        pwdInput?.dispatchEvent(new Event('input'));
        updateCenturyCount();
        if (parsed.step && parsed.step >= 1 && parsed.step <= TOTAL_STEPS) {
            currentStep = parsed.step;
        }
    }

    saveDraftBtn?.addEventListener('click', () => {
        saveDraft();
        showToast('Draft saved on this device. You can return later to continue.', 'success');
    });

    let draftTimer;
    form.addEventListener('input', () => {
        clearTimeout(draftTimer);
        draftTimer = setTimeout(saveDraft, 1500);
    });

    // ---------- Submit ----------
    form.addEventListener('submit', function(e) {
        const terms = document.getElementById('termsCheck');
        const termsErr = document.getElementById('termsError');
        if (!terms.checked) {
            e.preventDefault();
            termsErr?.classList.remove('hidden');
            terms.focus();
            terms.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }
        termsErr?.classList.add('hidden');

        submitBtn.disabled = true;
        nextBtn.disabled = true;
        prevBtn.disabled = true;
        saveDraftBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Submitting Profile...
        `;
        try { localStorage.removeItem(DRAFT_KEY); } catch (e) {}
    });

    // ---------- Auto-open step with Laravel errors ----------
    @if($errors->any())
        const errorFields = @json($errors->keys());
        let targetErrorStep = 1;
        if (errorFields.some(f => ['email','password','role'].includes(f))) targetErrorStep = 1;
        else if (errorFields.some(f => ['first_name','last_name','middle_name','birth_date','sex','civil_status','citizenship','mobile_number','employment_status'].includes(f))) targetErrorStep = 2;
        else if (errorFields.some(f => f.startsWith('address'))) targetErrorStep = 3;
        else if (errorFields.some(f => f.startsWith('education'))) targetErrorStep = 4;
        else if (errorFields.some(f => f.startsWith('skills'))) targetErrorStep = 5;
        else if (errorFields.some(f => ['occupation1','industry1','preferred_location','salary_expectation','salary_min','salary_max'].includes(f))) targetErrorStep = 6;
        else if (errorFields.some(f => ['is_4ps','is_pwd','is_ofw','household_id','pwd_type'].includes(f))) targetErrorStep = 7;
        else if (errorFields.some(f => ['termsCheck','terms','consent'].includes(f))) targetErrorStep = 8;
        currentStep = targetErrorStep;
    @endif

    // ---------- Init ----------
    const hadErrors = {{ $errors->any() ? 'true' : 'false' }};
    if (!hadErrors) {
        restoreDraft();
        try {
            const raw = localStorage.getItem(DRAFT_KEY);
            if (raw && currentStep > 1) {
                showToast(`Resumed your saved draft (Step ${currentStep} of ${TOTAL_STEPS}).`, 'info');
            }
        } catch (e) {}
    }
    evalPassword();
    updateCenturyCount();
    updateStepUI();
});
</script>
@endpush
@endsection