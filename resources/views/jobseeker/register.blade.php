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

        <!-- Hero Header Card -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-950 via-emerald-900 to-slate-950 text-white shadow-xl shadow-emerald-950/20 p-6 sm:p-8 md:p-10 mb-8 border border-emerald-800/40">
            <!-- Background Decorative Accents -->
            <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>
            <div class="absolute right-1/3 -bottom-12 w-48 h-48 rounded-full bg-emerald-400/10 blur-2xl pointer-events-none"></div>
            
            <div class="relative z-10">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-300 border border-emerald-400/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        DOLE NSRP & DMDP Cebu City
                    </span>
                    <span class="text-xs font-medium text-emerald-200/70 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Takes ~3-4 minutes
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight text-white">
                    Jobseeker & Trainee Registration
                </h1>
                <p class="text-emerald-100/80 text-sm sm:text-base mt-2 max-w-3xl leading-relaxed">
                    Create your profile to access automated AI job recommendations, free city-sponsored vocational skills training, and direct referrals to accredited employers.
                </p>

                <!-- Live Progress Bar in Header -->
                <div class="mt-6 pt-5 border-t border-emerald-800/60">
                    <div class="flex items-center justify-between text-xs font-semibold text-emerald-200 mb-2">
                        <span id="headerStepText">Step 1 of 7 · Account Credentials</span>
                        <span id="headerPercentText" class="text-emerald-400">14% Completed</span>
                    </div>
                    <div class="w-full bg-emerald-950/60 rounded-full h-2 overflow-hidden border border-emerald-800/40">
                        <div id="headerProgressBar" class="h-full bg-gradient-to-r from-emerald-500 to-emerald-400 transition-all duration-300 rounded-full" style="width: 14%;"></div>
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
        <form method="POST" action="{{ route('jobseeker.register.post') }}" class="bg-white border border-slate-200/90 rounded-3xl shadow-sm overflow-hidden" id="registrationForm" novalidate>
            @csrf
            <input type="hidden" name="role" value="jobseeker">

            <!-- Stepper Navigation (Desktop & Tablet) -->
            <div class="px-4 sm:px-8 py-5 bg-slate-50/80 border-b border-slate-200/70 overflow-x-auto">
                <div class="flex items-center justify-between min-w-[640px] max-w-5xl mx-auto gap-2 sm:gap-3" id="stepIndicator">
                    @php
                        $stepDefs = [
                            ['num' => 1, 'name' => 'Account', 'sub' => 'Credentials'],
                            ['num' => 2, 'name' => 'Personal', 'sub' => 'Basic info'],
                            ['num' => 3, 'name' => 'Address', 'sub' => 'Location'],
                            ['num' => 4, 'name' => 'Education', 'sub' => 'Academic'],
                            ['num' => 5, 'name' => 'Skills', 'sub' => 'Talents'],
                            ['num' => 6, 'name' => 'Preferences', 'sub' => 'Job goals'],
                            ['num' => 7, 'name' => 'Social', 'sub' => 'Finalize'],
                        ];
                    @endphp

                    @foreach($stepDefs as $idx => $step)
                        <button type="button" class="step-dot flex items-center gap-2 group cursor-pointer text-left transition-all py-1.5 px-2 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20" data-step="{{ $step['num'] }}" title="{{ $step['name'] }} - {{ $step['sub'] }}">
                            <div class="step-number w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs bg-slate-200 text-slate-600 transition-all duration-200 group-hover:scale-105 shadow-xs">
                                {{ $step['num'] }}
                            </div>
                            <div class="step-meta hidden md:block leading-tight">
                                <span class="step-title block text-xs font-bold text-slate-700 transition-colors">{{ $step['name'] }}</span>
                                <span class="step-sub block text-[10px] text-slate-400 font-medium">{{ $step['sub'] }}</span>
                            </div>
                        </button>
                        @if(!$loop->last)
                            <div class="step-line flex-1 h-0.5 bg-slate-200 rounded-full transition-colors duration-200 min-w-4"></div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- STEP 1: Account Credentials -->
            <div class="step-content" data-step="1">
                <div class="px-6 py-5 bg-gradient-to-r from-slate-50 via-white to-slate-50 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-extrabold text-base shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Account Credentials</h2>
                        <p class="text-xs text-slate-500">Create the login credentials for your TrabaGo portal profile</p>
                    </div>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Email -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="email">
                                Email Address <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="name@example.com"
                                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('email') border-rose-400 ring-2 ring-rose-100 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1">We'll send match alerts and training invitations to this email.</p>
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
                                Password <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <input type="password" id="password" name="password" required autocomplete="new-password" placeholder="At least 8 characters"
                                       class="w-full pl-10 pr-10 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('password') border-rose-400 ring-2 ring-rose-100 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                                <button type="button" class="toggle-password-btn absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600" data-target="password" tabindex="-1">
                                    <svg class="w-4 h-4 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg class="w-4 h-4 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                </button>
                            </div>

                            <!-- Password strength meter -->
                            <div class="mt-2 space-y-1">
                                <div class="flex gap-1 h-1 w-full bg-slate-100 rounded-full overflow-hidden">
                                    <div id="pwdBar1" class="h-full w-1/4 bg-slate-200 transition-colors"></div>
                                    <div id="pwdBar2" class="h-full w-1/4 bg-slate-200 transition-colors"></div>
                                    <div id="pwdBar3" class="h-full w-1/4 bg-slate-200 transition-colors"></div>
                                    <div id="pwdBar4" class="h-full w-1/4 bg-slate-200 transition-colors"></div>
                                </div>
                                <span id="pwdStrengthText" class="text-[11px] font-medium text-slate-400">Password strength</span>
                            </div>
                            @error('password')
                                <span class="text-xs text-rose-600 font-semibold mt-1 flex items-center gap-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="password_confirmation">
                                Confirm Password <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat your password"
                                       class="w-full pl-10 pr-10 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                                <button type="button" class="toggle-password-btn absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600" data-target="password_confirmation" tabindex="-1">
                                    <svg class="w-4 h-4 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg class="w-4 h-4 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                </button>
                            </div>
                            <span id="pwdMatchNotice" class="text-[11px] font-medium text-slate-400 mt-1 block"></span>
                        </div>
                    </div>

                    <!-- Account Type Badge Card -->
                    <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black text-sm">
                                JS
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">Designated Role: Jobseeker / Trainee</span>
                                <span class="text-[11px] text-slate-600">Access to DMDP training programs & certified job placements</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-800 bg-emerald-200/60 px-2.5 py-1 rounded-full">
                            Verified Track
                        </span>
                    </div>
                </div>
            </div>

            <!-- STEP 2: Personal Information -->
            <div class="step-content hidden" data-step="2">
                <div class="px-6 py-5 bg-gradient-to-r from-slate-50 via-white to-slate-50 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-extrabold text-base shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Personal Information</h2>
                        <p class="text-xs text-slate-500">Official profile information compliant with DOLE NSRP Form 1</p>
                    </div>
                </div>

                <div class="p-6 sm:p-8 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <!-- First Name -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="first_name">
                                First Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required
                                   placeholder="e.g. Juan"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('first_name') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                            @error('first_name')<span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span>@enderror
                        </div>

                        <!-- Middle Name -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="middle_name">
                                Middle Name <span class="text-slate-400 font-normal lowercase">(optional)</span>
                            </label>
                            <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}"
                                   placeholder="e.g. Santos"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="last_name">
                                Last Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required
                                   placeholder="e.g. Dela Cruz"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('last_name') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                            @error('last_name')<span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span>@enderror
                        </div>

                        <!-- Birth Date -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="birth_date">
                                Birth Date <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('birth_date') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                            <span id="agePreview" class="text-[11px] text-emerald-700 font-medium mt-1 block"></span>
                            @error('birth_date')<span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span>@enderror
                        </div>

                        <!-- Sex -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="sex">
                                Sex <span class="text-rose-500">*</span>
                            </label>
                            <select id="sex" name="sex" required class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('sex') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition">
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
                                Civil Status <span class="text-rose-500">*</span>
                            </label>
                            <select id="civil_status" name="civil_status" required class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('civil_status') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition">
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
                                Citizenship <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="citizenship" name="citizenship" value="{{ old('citizenship', 'Filipino') }}" required
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('citizenship') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                            @error('citizenship')<span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span>@enderror
                        </div>

                        <!-- Mobile Number -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="mobile_number">
                                Mobile Number <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-semibold text-slate-500">+63</span>
                                <input type="tel" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" required placeholder="912 345 6789"
                                       class="w-full pl-12 pr-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('mobile_number') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                            </div>
                            @error('mobile_number')<span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span>@enderror
                        </div>

                        <!-- Employment Status -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="employment_status">
                                Employment Status <span class="text-rose-500">*</span>
                            </label>
                            <select id="employment_status" name="employment_status" required class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('employment_status') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition">
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
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Current Residence & Location</h2>
                        <p class="text-xs text-slate-500">Your address helps DMDP match you with local barangay job fairs and training centers</p>
                    </div>
                </div>

                <div class="p-6 sm:p-8 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Street Address -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="address_street">
                                Street Address / House / Bldg No.
                            </label>
                            <input type="text" id="address_street" name="address[street]" value="{{ old('address.street') }}" placeholder="Unit/House No., Street Name, Subdivision"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
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
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
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
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Educational Background</h2>
                        <p class="text-xs text-slate-500">Highest educational attainment and vocational certifications</p>
                    </div>
                </div>

                <div class="p-6 sm:p-8 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Education Level -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="education_level">
                                Highest Educational Attainment <span class="text-rose-500">*</span>
                            </label>
                            <select id="education_level" name="education[level]" required class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition">
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
                                Course / Major / Track
                            </label>
                            <input type="text" id="education_course" name="education[course]" value="{{ old('education.course') }}" placeholder="e.g. BS Information Technology, General Academic Strand, Electrical Installation"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                        </div>

                        <!-- School / Institution -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="education_school">
                                School / University / Training Institution
                            </label>
                            <input type="text" id="education_school" name="education[school]" value="{{ old('education.school') }}" placeholder="e.g. Cebu Technological University, DMDP Training Center"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                        </div>

                        <!-- Year Graduated -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="education_year">
                                Year Graduated / Last Attended
                            </label>
                            <input type="number" id="education_year" name="education[year_graduated]" value="{{ old('education.year_graduated') }}" min="1960" max="{{ date('Y') + 1 }}" placeholder="{{ date('Y') }}"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 5: Skills Assessment (DOLE NSRP Compliant) -->
            <div class="step-content hidden" data-step="5">
                <div class="px-6 py-5 bg-gradient-to-r from-slate-50 via-white to-slate-50 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-extrabold text-base shrink-0 shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Skills Assessment & Talents</h2>
                            <p class="text-xs text-slate-500">Powering our AI matching engine to connect you with the right job vacancies</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-8 space-y-7">
                    <!-- 21st Century Skills -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">1. 21st Century Core Skills</h3>
                                <p class="text-xs text-slate-500">Choose the soft skills and capabilities you excel at (pick up to 5)</p>
                            </div>
                            <span id="centuryCountBadge" class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">0 / 5 selected</span>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 text-xs">
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
                                <label class="skill-card flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 bg-white hover:border-emerald-300 hover:bg-emerald-50/30 cursor-pointer transition-all duration-150 select-none">
                                    <input type="checkbox" name="skills_century[]" value="{{ $cs }}" class="century-checkbox rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 border-slate-300">
                                    <span class="font-medium text-slate-800">{{ $cs }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Technical Skills Acquired Without Formal Training -->
                    <div class="pt-5 border-t border-slate-100">
                        <div class="mb-3">
                            <h3 class="text-sm font-bold text-slate-900">2. Practical Technical Skills</h3>
                            <p class="text-xs text-slate-500">Skills acquired through hands-on work, apprenticeships, or personal practice</p>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5 text-xs">
                            @php 
                                $informalList = [
                                    'Carpentry', 'Masonry', 'Welding', 'Auto Mechanic', 
                                    'Plumbing', 'Driving', 'Gardening', 'Tailoring', 
                                    'Photography', 'Hairdressing', 'Cooking', 'Baking'
                                ]; 
                            @endphp
                            @foreach($informalList as $is)
                                <label class="skill-card flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 bg-white hover:border-emerald-300 hover:bg-emerald-50/30 cursor-pointer transition-all duration-150 select-none">
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
                                       class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                                <input type="hidden" name="skills[0][type]" value="technical" />
                            </div>
                            <div>
                                <input type="text" name="skills[1][name]" value="{{ old('skills.1.name') }}" placeholder="e.g. Graphic Design, AutoCAD, BPO Customer Service"
                                       class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                                <input type="hidden" name="skills[1][type]" value="technical" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 6: Job Preferences & Target Career -->
            <div class="step-content hidden" data-step="6">
                <div class="px-6 py-5 bg-gradient-to-r from-slate-50 via-white to-slate-50 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-extrabold text-base shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Job Preferences & Career Targets</h2>
                        <p class="text-xs text-slate-500">Define what roles, industries, and compensation you are looking for</p>
                    </div>
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
                                <input type="text" name="occupation1" value="{{ old('occupation1') }}" placeholder="e.g. Call Center Agent"
                                       class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                            </div>
                            <div>
                                <span class="text-[11px] font-semibold text-slate-600 block mb-1">Secondary Choice</span>
                                <input type="text" name="occupation2" value="{{ old('occupation2') }}" placeholder="e.g. Administrative Clerk"
                                       class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                            </div>
                            <div>
                                <span class="text-[11px] font-semibold text-slate-600 block mb-1">Tertiary Choice</span>
                                <input type="text" name="occupation3" value="{{ old('occupation3') }}" placeholder="e.g. Sales Associate"
                                       class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                            </div>
                        </div>
                    </div>

                    <!-- Target Industries -->
                    <div class="pt-4 border-t border-slate-100">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Industry Sectors
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <input type="text" name="industry1" value="{{ old('industry1') }}" placeholder="e.g. BPO / Customer Care"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                            <input type="text" name="industry2" value="{{ old('industry2') }}" placeholder="e.g. Hospitality / Tourism"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                            <input type="text" name="industry3" value="{{ old('industry3') }}" placeholder="e.g. Construction / Trades"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                        </div>
                    </div>

                    <!-- Compensation & Location -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="salary_expectation">
                                Expected Monthly Salary (PHP)
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-sm font-bold text-slate-500">₱</span>
                                <input type="text" id="salary_expectation" name="salary_expectation" value="{{ old('salary_expectation') }}" placeholder="e.g. 18,000 - 25,000"
                                       class="w-full pl-8 pr-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="preferred_location">
                                Preferred Work Location
                            </label>
                            <input type="text" id="preferred_location" name="preferred_location" value="{{ old('preferred_location', 'Cebu City') }}" placeholder="e.g. Cebu City, IT Park, Mandaue"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 7: Social Status & Final Review -->
            <div class="step-content hidden" data-step="7">
                <div class="px-6 py-5 bg-gradient-to-r from-slate-50 via-white to-slate-50 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-extrabold text-base shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Social Status & Final Confirmation</h2>
                        <p class="text-xs text-slate-500">Government social support programs and confirmation of truthfulness</p>
                    </div>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    <!-- Social Programs Cards -->
                    <div class="space-y-3">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-700">Special Beneficiary Categories (Check all that apply):</p>
                        
                        <!-- 4Ps Card -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-white hover:border-emerald-300 transition-colors">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" id="is_4ps_checkbox" name="is_4ps" value="1" {{ old('is_4ps') ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 border-slate-300" />
                                <div class="flex-1">
                                    <span class="text-sm font-bold text-slate-900 block">Pantawid Pamilyang Pilipino Program (4Ps) Beneficiary</span>
                                    <span class="text-xs text-slate-500">Priority inclusion for DMDP livelihood assistance and emergency employment</span>
                                </div>
                            </label>
                            <div id="householdIdField" class="mt-3 pt-3 border-t border-slate-100 {{ old('is_4ps') ? '' : 'hidden' }}">
                                <label class="block text-xs font-semibold text-slate-700 mb-1" for="household_id">4Ps Household Identification Number</label>
                                <input type="text" id="household_id" name="household_id" value="{{ old('household_id') }}" placeholder="e.g. 0722-12345678"
                                       class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500" />
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
                                    <span class="text-sm font-bold text-slate-900 block">Person with Disability (PWD)</span>
                                    <span class="text-xs text-slate-500">Inclusive workplace referral under RA 10524 with accessibility accommodation</span>
                                </div>
                            </label>
                            <div id="pwdTypeField" class="mt-3 pt-3 border-t border-slate-100 {{ old('is_pwd') ? '' : 'hidden' }}">
                                <label class="block text-xs font-semibold text-slate-700 mb-1" for="pwd_type">Type of Disability / Assistance Needed</label>
                                <input type="text" id="pwd_type" name="pwd_type" value="{{ old('pwd_type') }}" placeholder="e.g. Visual, Hearing, Orthopedic, Speech"
                                       class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                            </div>
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

                    <!-- Profile Summary Card -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Quick Profile Review</h3>
                            <span class="text-[10px] text-emerald-700 font-bold bg-emerald-100/70 px-2 py-0.5 rounded-full">Ready to Register</span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                            <div class="bg-white p-2.5 rounded-xl border border-slate-100">
                                <span class="text-[10px] text-slate-400 block font-medium">Full Name</span>
                                <span id="summaryName" class="font-bold text-slate-800 truncate block">-</span>
                            </div>
                            <div class="bg-white p-2.5 rounded-xl border border-slate-100">
                                <span class="text-[10px] text-slate-400 block font-medium">Email</span>
                                <span id="summaryEmail" class="font-bold text-slate-800 truncate block">-</span>
                            </div>
                            <div class="bg-white p-2.5 rounded-xl border border-slate-100">
                                <span class="text-[10px] text-slate-400 block font-medium">Barangay</span>
                                <span id="summaryBarangay" class="font-bold text-slate-800 truncate block">-</span>
                            </div>
                            <div class="bg-white p-2.5 rounded-xl border border-slate-100">
                                <span class="text-[10px] text-slate-400 block font-medium">Education</span>
                                <span id="summaryEdu" class="font-bold text-slate-800 truncate block">-</span>
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
                    </div>
                </div>
            </div>

            <!-- Sticky Navigation Bar (Previous, Next, Submit) -->
            <div class="px-6 py-4 bg-slate-50/90 border-t border-slate-200/80 flex items-center justify-between">
                <button type="button" id="prevBtn" class="px-5 py-2.5 text-slate-700 font-semibold rounded-xl border border-slate-300 bg-white hover:bg-slate-100 active:scale-[0.98] transition-all flex items-center gap-2 text-sm shadow-xs hidden">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Previous
                </button>

                <div class="flex items-center gap-2 ml-auto">
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

        <!-- Help / Support Footer -->
        <div class="mt-8 text-center text-xs text-slate-500 space-y-1">
            <p>Need assistance with your registration? Contact DMDP Cebu City Hall at (032) 888-1234 or visit the 2F Annex Building.</p>
            <p class="text-slate-400">Department of Manpower Development and Placement · Cebu City Government</p>
        </div>

    </div>
</div>

@push('styles')
<style>
/* Active step styling */
.step-dot.active .step-number {
    background-color: #059669;
    color: #ffffff;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2);
}
.step-dot.active .step-title {
    color: #065f46;
}
.step-dot.completed .step-number {
    background-color: #10b981;
    color: #ffffff;
}
.step-dot.completed .step-title {
    color: #047857;
}
.step-line.completed {
    background-color: #10b981;
}
/* Skill card selection */
.skill-card:has(input:checked) {
    border-color: #10b981;
    background-color: #ecfdf5;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 7;
    const form = document.getElementById('registrationForm');
    const stepContents = document.querySelectorAll('.step-content');
    const stepDots = document.querySelectorAll('.step-dot');
    const stepLines = document.querySelectorAll('.step-line');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const headerStepText = document.getElementById('headerStepText');
    const headerPercentText = document.getElementById('headerPercentText');
    const headerProgressBar = document.getElementById('headerProgressBar');

    const stepTitles = [
        'Account Credentials',
        'Personal Information',
        'Residence & Address',
        'Educational Attainment',
        'Skills Assessment',
        'Job Preferences',
        'Social Status & Confirmation'
    ];

    function updateStepUI() {
        stepContents.forEach(c => {
            const s = parseInt(c.dataset.step);
            if (s === currentStep) {
                c.classList.remove('hidden');
            } else {
                c.classList.add('hidden');
            }
        });

        stepDots.forEach(d => {
            const s = parseInt(d.dataset.step);
            d.classList.remove('active', 'completed');
            if (s === currentStep) {
                d.classList.add('active');
            } else if (s < currentStep) {
                d.classList.add('completed');
            }
        });

        stepLines.forEach((l, idx) => {
            if (idx < currentStep - 1) {
                l.classList.add('completed');
            } else {
                l.classList.remove('completed');
            }
        });

        const percent = Math.round((currentStep / totalSteps) * 100);
        if (headerStepText) headerStepText.textContent = `Step ${currentStep} of ${totalSteps} · ${stepTitles[currentStep - 1]}`;
        if (headerPercentText) headerPercentText.textContent = `${percent}% Completed`;
        if (headerProgressBar) headerProgressBar.style.width = `${percent}%`;

        if (currentStep === 1) {
            prevBtn.classList.add('hidden');
        } else {
            prevBtn.classList.remove('hidden');
        }

        if (currentStep === totalSteps) {
            nextBtn.classList.add('hidden');
            submitBtn.classList.remove('hidden');
            updateSummaryReview();
        } else {
            nextBtn.classList.remove('hidden');
            submitBtn.classList.add('hidden');
        }
    }

    function validateStep(step) {
        const c = document.querySelector(`.step-content[data-step="${step}"]`);
        if (!c) return true;

        const inputs = c.querySelectorAll('input[required], select[required]');
        let isValid = true;
        let firstInvalid = null;

        inputs.forEach(input => {
            let fieldValid = true;
            if (input.type === 'checkbox') {
                fieldValid = input.checked;
            } else {
                fieldValid = input.value.trim() !== '';
            }

            if (!fieldValid) {
                isValid = false;
                input.classList.add('border-rose-400', 'ring-2', 'ring-rose-100');
                if (!firstInvalid) firstInvalid = input;
            } else {
                input.classList.remove('border-rose-400', 'ring-2', 'ring-rose-100');
            }
        });

        // Specific Step 1 check: password match
        if (step === 1) {
            const pwd = document.getElementById('password');
            const pwdConf = document.getElementById('password_confirmation');
            if (pwd && pwdConf && pwd.value && pwdConf.value && pwd.value !== pwdConf.value) {
                isValid = false;
                pwdConf.classList.add('border-rose-400', 'ring-2', 'ring-rose-100');
                if (!firstInvalid) firstInvalid = pwdConf;
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
            return;
        }
        currentStep = step;
        updateStepUI();
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    nextBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (validateStep(currentStep) && currentStep < totalSteps) {
            goToStep(currentStep + 1);
        }
    });

    prevBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (currentStep > 1) {
            goToStep(currentStep - 1);
        }
    });

    stepDots.forEach(dot => {
        dot.addEventListener('click', function() {
            const targetStep = parseInt(this.dataset.step);
            if (targetStep < currentStep || validateStep(currentStep)) {
                goToStep(targetStep);
            }
        });
    });

    // Toggle Password Visibility
    document.querySelectorAll('.toggle-password-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            const eyeOpen = this.querySelector('.eye-open');
            const eyeClosed = this.querySelector('.eye-closed');

            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        });
    });

    // Password strength calculation
    const pwdInput = document.getElementById('password');
    const pwdConfInput = document.getElementById('password_confirmation');
    const pwdStrengthText = document.getElementById('pwdStrengthText');
    const pwdMatchNotice = document.getElementById('pwdMatchNotice');

    if (pwdInput) {
        pwdInput.addEventListener('input', function() {
            const val = this.value;
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const bars = [
                document.getElementById('pwdBar1'),
                document.getElementById('pwdBar2'),
                document.getElementById('pwdBar3'),
                document.getElementById('pwdBar4')
            ];

            bars.forEach((b, i) => {
                b.className = 'h-full w-1/4 transition-colors';
                if (i < score) {
                    if (score === 1) b.classList.add('bg-rose-500');
                    else if (score === 2) b.classList.add('bg-amber-400');
                    else if (score === 3) b.classList.add('bg-emerald-400');
                    else b.classList.add('bg-emerald-600');
                } else {
                    b.classList.add('bg-slate-200');
                }
            });

            const labels = ['Too weak (at least 8 chars)', 'Fair', 'Good', 'Strong'];
            pwdStrengthText.textContent = val ? labels[score - 1] || 'Too weak' : 'Password strength';
            if (score <= 1) pwdStrengthText.className = 'text-[11px] font-medium text-rose-500';
            else if (score === 2) pwdStrengthText.className = 'text-[11px] font-medium text-amber-500';
            else pwdStrengthText.className = 'text-[11px] font-medium text-emerald-600';

            checkPasswordMatch();
        });
    }

    if (pwdConfInput) {
        pwdConfInput.addEventListener('input', checkPasswordMatch);
    }

    function checkPasswordMatch() {
        if (!pwdConfInput || !pwdInput || !pwdConfInput.value) {
            if (pwdMatchNotice) pwdMatchNotice.textContent = '';
            return;
        }
        if (pwdInput.value === pwdConfInput.value) {
            pwdMatchNotice.innerHTML = '<span class="inline-flex items-center gap-1"><svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Passwords match</span>';
            pwdMatchNotice.className = 'text-[11px] font-medium text-emerald-600 mt-1 block';
            pwdConfInput.classList.remove('border-rose-400');
        } else {
            pwdMatchNotice.innerHTML = '<span class="inline-flex items-center gap-1"><svg class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg> Passwords do not match</span>';
            pwdMatchNotice.className = 'text-[11px] font-medium text-rose-500 mt-1 block';
        }
    }

    // Birth date age preview
    const birthDateInput = document.getElementById('birth_date');
    const agePreview = document.getElementById('agePreview');
    if (birthDateInput) {
        birthDateInput.addEventListener('change', function() {
            if (!this.value) { agePreview.textContent = ''; return; }
            const diff = Date.now() - new Date(this.value).getTime();
            const ageDate = new Date(diff);
            const age = Math.abs(ageDate.getUTCFullYear() - 1970);
            if (!isNaN(age)) {
                agePreview.textContent = `${age} years old`;
            }
        });
    }

    // 21st Century Skills counter limit
    const centuryCheckboxes = document.querySelectorAll('.century-checkbox');
    const centuryCountBadge = document.getElementById('centuryCountBadge');
    centuryCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const checked = document.querySelectorAll('.century-checkbox:checked');
            if (checked.length > 5) {
                this.checked = false;
                alert('You can select a maximum of 5 core 21st century skills.');
            }
            const currentChecked = document.querySelectorAll('.century-checkbox:checked').length;
            if (centuryCountBadge) {
                centuryCountBadge.textContent = `${currentChecked} / 5 selected`;
                if (currentChecked === 5) {
                    centuryCountBadge.className = 'text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800';
                } else {
                    centuryCountBadge.className = 'text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700';
                }
            }
        });
    });

    // 4Ps toggle
    const is4ps = document.getElementById('is_4ps_checkbox');
    const householdIdField = document.getElementById('householdIdField');
    if (is4ps && householdIdField) {
        is4ps.addEventListener('change', function() {
            householdIdField.classList.toggle('hidden', !this.checked);
        });
    }

    // PWD toggle
    const isPwd = document.getElementById('is_pwd_checkbox');
    const pwdTypeField = document.getElementById('pwdTypeField');
    if (isPwd && pwdTypeField) {
        isPwd.addEventListener('change', function() {
            pwdTypeField.classList.toggle('hidden', !this.checked);
        });
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
        const fn = document.getElementById('first_name')?.value || '';
        const ln = document.getElementById('last_name')?.value || '';
        const em = document.getElementById('email')?.value || '';
        const brgy = document.getElementById('address_barangay')?.value || '';
        const edu = document.getElementById('education_level')?.value || '';

        const sName = document.getElementById('summaryName');
        const sEmail = document.getElementById('summaryEmail');
        const sBrgy = document.getElementById('summaryBarangay');
        const sEdu = document.getElementById('summaryEdu');

        if (sName) sName.textContent = (fn || ln) ? `${fn} ${ln}` : 'Not provided';
        if (sEmail) sEmail.textContent = em || 'Not provided';
        if (sBrgy) sBrgy.textContent = brgy || 'Not specified';
        if (sEdu) sEdu.textContent = edu || 'Not provided';
    }

    // Submit handler with prevention of double submission
    form.addEventListener('submit', function(e) {
        const terms = document.getElementById('termsCheck');
        if (terms && !terms.checked) {
            e.preventDefault();
            alert('Please review and check the certification agreement checkbox before completing registration.');
            terms.focus();
            return false;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Submitting Profile...
        `;
    });

    // Auto-detect Laravel errors and open corresponding step
    @if($errors->any())
        const errorFields = @json($errors->keys());
        let targetErrorStep = 1;

        if (errorFields.some(f => ['email', 'password', 'role'].includes(f))) {
            targetErrorStep = 1;
        } else if (errorFields.some(f => ['first_name', 'last_name', 'middle_name', 'birth_date', 'sex', 'civil_status', 'citizenship', 'mobile_number', 'employment_status'].includes(f))) {
            targetErrorStep = 2;
        } else if (errorFields.some(f => f.startsWith('address'))) {
            targetErrorStep = 3;
        } else if (errorFields.some(f => f.startsWith('education'))) {
            targetErrorStep = 4;
        } else if (errorFields.some(f => f.startsWith('skills'))) {
            targetErrorStep = 5;
        } else if (errorFields.some(f => ['occupation1', 'industry1', 'preferred_location', 'salary_expectation'].includes(f))) {
            targetErrorStep = 6;
        } else if (errorFields.some(f => ['is_4ps', 'is_pwd', 'is_ofw', 'household_id'].includes(f))) {
            targetErrorStep = 7;
        }
        currentStep = targetErrorStep;
    @endif

    updateStepUI();
});
</script>
@endpush
@endsection