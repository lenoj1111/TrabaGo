@extends('layouts.public')

@section('title', 'Employer Registration - DMDP Cebu City | TrabaGo')

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
                Looking for jobs? 
                <a href="{{ route('jobseeker.register') }}" class="font-semibold text-emerald-600 hover:text-emerald-700 hover:underline">Register as Jobseeker</a>
            </div>
        </div>

        <!-- Hero Header Card -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-950 via-teal-950 to-slate-950 text-white shadow-xl shadow-emerald-950/20 p-6 sm:p-8 md:p-10 mb-8 border border-emerald-800/40">
            <!-- Background Decorative Accents -->
            <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-teal-500/10 blur-3xl pointer-events-none"></div>
            <div class="absolute right-1/4 -bottom-12 w-48 h-48 rounded-full bg-emerald-400/10 blur-2xl pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-300 border border-emerald-400/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        DMDP Employer Accreditation Program
                    </span>
                    <span class="text-xs font-medium text-emerald-200/70 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Official City Hall Partner Status
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight text-white">
                    Employer & Recruitment Partner Registration
                </h1>
                <p class="text-emerald-100/80 text-sm sm:text-base mt-2 max-w-3xl leading-relaxed">
                    Register your company with DMDP Cebu City to post job vacancies, access verified pre-screened talent, participate in local job fairs, and obtain City Hall employer accreditation.
                </p>

                <!-- Live Progress Bar in Header -->
                <div class="mt-6 pt-5 border-t border-emerald-800/60">
                    <div class="flex items-center justify-between text-xs font-semibold text-emerald-200 mb-2">
                        <span id="headerStepText">Step 1 of 4 · Account Setup</span>
                        <span id="headerPercentText" class="text-emerald-400">25% Completed</span>
                    </div>
                    <div class="w-full bg-emerald-950/60 rounded-full h-2 overflow-hidden border border-emerald-800/40">
                        <div id="headerProgressBar" class="h-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-300 rounded-full" style="width: 25%;"></div>
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
        <form method="POST" action="{{ route('employer.register.post') }}" class="bg-white border border-slate-200/90 rounded-3xl shadow-sm overflow-hidden" id="registrationForm" enctype="multipart/form-data" novalidate>
            @csrf
            <input type="hidden" name="role" value="employer">

            <!-- Stepper Navigation -->
            <div class="px-4 sm:px-8 py-5 bg-slate-50/80 border-b border-slate-200/70">
                <div class="flex items-center justify-between max-w-4xl mx-auto gap-2 sm:gap-4" id="stepIndicator">
                    @php
                        $employerSteps = [
                            ['num' => 1, 'name' => 'Account', 'sub' => 'Credentials'],
                            ['num' => 2, 'name' => 'Company', 'sub' => 'Business Profile'],
                            ['num' => 3, 'name' => 'Contact', 'sub' => 'Owner & HR Contacts'],
                            ['num' => 4, 'name' => 'Documents', 'sub' => 'Accreditation'],
                        ];
                    @endphp

                    @foreach($employerSteps as $idx => $step)
                        <button type="button" class="step-dot flex items-center gap-2.5 group cursor-pointer text-left transition-all py-1.5 px-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20" data-step="{{ $step['num'] }}">
                            <div class="step-number w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs bg-slate-200 text-slate-600 transition-all duration-200 group-hover:scale-105 shadow-xs">
                                {{ $step['num'] }}
                            </div>
                            <div class="step-meta hidden sm:block leading-tight">
                                <span class="step-title block text-xs font-bold text-slate-700 transition-colors">{{ $step['name'] }}</span>
                                <span class="step-sub block text-[10px] text-slate-400 font-medium">{{ $step['sub'] }}</span>
                            </div>
                        </button>
                        @if(!$loop->last)
                            <div class="step-line flex-1 h-0.5 bg-slate-200 rounded-full transition-colors duration-200 min-w-6"></div>
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
                        <h2 class="text-lg font-bold text-slate-900">Corporate Account Credentials</h2>
                        <p class="text-xs text-slate-500">Official administrator access to manage vacancies and candidates</p>
                    </div>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Email -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="email">
                                Official Company Email Address (Primary Sign-In) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="hr@yourcompany.com"
                                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('email') border-rose-400 ring-2 ring-rose-100 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1">Primary corporate email used for portal authentication and notifications. Owner and authorized contact details are entered in Step 3.</p>
                            @error('email')<span class="text-xs text-rose-600 font-semibold mt-1 flex items-center gap-1">{{ $message }}</span>@enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="password">
                                Master Password <span class="text-rose-500">*</span>
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
                            @error('password')<span class="text-xs text-rose-600 font-semibold mt-1 flex items-center gap-1">{{ $message }}</span>@enderror
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

                    <!-- Role Badge Card -->
                    <div class="p-4 rounded-2xl bg-teal-50/70 border border-teal-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-teal-700 text-white flex items-center justify-center font-black text-sm">
                                EP
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">Designated Role: Employer / Enterprise Partner</span>
                                <span class="text-[11px] text-slate-600">Post unlimited vacancies, interview candidates, and receive verified manpower</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-teal-800 bg-teal-200/60 px-2.5 py-1 rounded-full">
                            Accreditation Track
                        </span>
                    </div>
                </div>
            </div>

            <!-- STEP 2: Company Information -->
            <div class="step-content hidden" data-step="2">
                <div class="px-6 py-5 bg-gradient-to-r from-slate-50 via-white to-slate-50 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-extrabold text-base shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Company & Enterprise Profile</h2>
                        <p class="text-xs text-slate-500">Provide official details as registered with SEC, DTI, or CDA</p>
                    </div>
                </div>

                <div class="p-6 sm:p-8 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Company Name -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="company_name">
                                Registered Company / Business Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" required
                                   placeholder="e.g. Acme Philippines Technologies Inc."
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('company_name') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                            @error('company_name')<span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span>@enderror
                        </div>

                        <!-- Company Description -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="company_description">
                                Brief Company Overview / Business Activity
                            </label>
                            <textarea id="company_description" name="company_description" rows="3"
                                      placeholder="Briefly describe your company's core operations, services, or products..."
                                      class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition">{{ old('company_description') }}</textarea>
                        </div>

                        <!-- Industry -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="industry">
                                Primary Industry Sector
                            </label>
                            <select id="industry" name="industry" class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition">
                                <option value="">Select industry sector...</option>
                                @foreach([
                                    'Agriculture, Forestry and Fishing',
                                    'Mining and Quarrying',
                                    'Manufacturing',
                                    'Electricity, Gas, Steam and Air Conditioning Supply',
                                    'Water Supply; Sewerage, Waste Management and Remediation Activities',
                                    'Construction',
                                    'Wholesale and Retail Trade; Repair of Motor Vehicles and Motorcycles',
                                    'Transportation and Storage',
                                    'Accommodation and Food Service Activities',
                                    'Publishing, Broadcasting, and Content Production and Distribution Activities',
                                    'Telecommunications, Computer Programming, Consultancy, Computing Infrastructure, and Other Information Service Activities',
                                    'Financial and Insurance Activities',
                                    'Real Estate Activities',
                                    'Professional, Scientific and Technical Activities',
                                    'Administrative and Support Service Activities',
                                    'Public Administration and Defense; Compulsory Social Security',
                                    'Education',
                                    'Human Health and Social Work Activities',
                                    'Arts, Sports and Recreation',
                                    'Other Service Activities',
                                    'Activities of Households as Employers; Undifferentiated Goods- and Services-Producing Activities of Households for Own Use',
                                    'Activities of Extraterritorial Organizations and Bodies',
                                ] as $ind)
                                    <option value="{{ $ind }}" {{ old('industry') == $ind ? 'selected' : '' }}>{{ $ind }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Company Size -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="company_size">
                                Organization Size / Headcount
                            </label>
                            <select id="company_size" name="company_size" class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition">
                                <option value="">Select headcount range...</option>
                                <option value="1-10" {{ old('company_size') == '1-10' ? 'selected' : '' }}>1-10 employees (Micro)</option>
                                <option value="11-50" {{ old('company_size') == '11-50' ? 'selected' : '' }}>11-50 employees (Small)</option>
                                <option value="51-200" {{ old('company_size') == '51-200' ? 'selected' : '' }}>51-200 employees (Medium)</option>
                                <option value="201-500" {{ old('company_size') == '201-500' ? 'selected' : '' }}>201-500 employees (Large)</option>
                                <option value="501-1000" {{ old('company_size') == '501-1000' ? 'selected' : '' }}>501-1000 employees (Enterprise)</option>
                                <option value="1000+" {{ old('company_size') == '1000+' ? 'selected' : '' }}>1000+ employees (Major Corporation)</option>
                            </select>
                        </div>

                        <!-- Website URL -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="website">
                                Official Website or Social Page
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-medium text-slate-400">https://</span>
                                <input type="text" id="website" name="website" value="{{ old('website') }}" placeholder="www.yourcompany.com"
                                       class="w-full pl-16 pr-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                            </div>
                        </div>

                        <!-- Company Type -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="company_type">
                                Business Entity Type
                            </label>
                            <select id="company_type" name="company_type" class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition">
                                <option value="">Select legal structure...</option>
                                <option value="Private" {{ old('company_type') == 'Private' ? 'selected' : '' }}>Private Corporation</option>
                                <option value="Public" {{ old('company_type') == 'Public' ? 'selected' : '' }}>Publicly Listed Corporation</option>
                                <option value="Sole Proprietorship" {{ old('company_type') == 'Sole Proprietorship' ? 'selected' : '' }}>Sole Proprietorship</option>
                                <option value="Partnership" {{ old('company_type') == 'Partnership' ? 'selected' : '' }}>Partnership</option>
                                <option value="Government" {{ old('company_type') == 'Government' ? 'selected' : '' }}>Government / State Enterprise</option>
                                <option value="Non-Profit" {{ old('company_type') == 'Non-Profit' ? 'selected' : '' }}>Non-Profit / NGO</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 3: Contact & Headquarters -->
            <div class="step-content hidden" data-step="3">
                <div class="px-6 py-5 bg-gradient-to-r from-slate-50 via-white to-slate-50 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-extrabold text-base shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Owner &amp; Authorized Contacts</h2>
                        <p class="text-xs text-slate-500">Register the company owner as the primary authorized person, with an optional secondary HR or authorized contact and workplace headquarters in Region 7</p>
                    </div>
                </div>

                <div class="p-6 sm:p-8 space-y-5">
                    <!-- Owner / Primary Authorized Person -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="contact_person">
                                Owner / Primary Authorized Person (Full Name) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person') }}" required
                                   placeholder="e.g. Maria Clara Santos"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('contact_person') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                            @error('contact_person')<span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="contact_position">
                                Owner's Position / Designation <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="contact_position" name="contact_position" value="{{ old('contact_position', 'Owner') }}" required
                                   placeholder="e.g. Owner / Proprietor"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('contact_position') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                            @error('contact_position')<span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="phone">
                                Direct Landline / Telephone <span class="text-slate-400 font-normal normal-case">(optional)</span>
                            </label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                   placeholder="(032) 123-4567"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('phone') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                            @error('phone')<span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="mobile">
                                Owner's Mobile Number <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-semibold text-slate-500">+63</span>
                                <input type="tel" id="mobile" name="mobile" value="{{ old('mobile') }}" placeholder="917 123 4567" required
                                       class="w-full pl-12 pr-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('mobile') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                            </div>
                            @error('mobile')<span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <!-- Primary Authorized Person Email -->
                    <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-4">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200/80">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Owner's Official Email</h3>
                                    <p class="text-[11px] text-slate-500">Primary email for the owner and company recruitment correspondence</p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Primary Contact</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="primary_email">
                                Owner's Official Email <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" id="primary_email" name="primary_email" value="{{ old('primary_email', old('email')) }}" required
                                   placeholder="e.g. owner@company.com"
                                   class="w-full px-4 py-2.5 bg-white border @error('primary_email') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                            @error('primary_email')<span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <!-- Optional Secondary Authorized Person -->
                    <div class="p-5 rounded-2xl bg-white border border-slate-200 space-y-4" data-secondary-contact>
                        <div class="pb-2 border-b border-slate-200/80">
                            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Secondary Authorized Person <span class="text-slate-400 normal-case font-medium">(optional)</span></h3>
                            <p class="text-[11px] text-slate-500 mt-1">Add an HR representative or another person authorized to act for the company. If you start this section, provide their name, position, mobile number, and email. Landline is optional.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="secondary_contact_person">
                                    Full Name
                                </label>
                                <input type="text" id="secondary_contact_person" name="secondary_contact_person" value="{{ old('secondary_contact_person') }}" placeholder="e.g. Maria Clara Santos"
                                       class="w-full px-4 py-2.5 bg-white border @error('secondary_contact_person') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                                @error('secondary_contact_person')<span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="secondary_contact_position">
                                    Position / Designation
                                </label>
                                <input type="text" id="secondary_contact_position" name="secondary_contact_position" value="{{ old('secondary_contact_position') }}" placeholder="e.g. HR Manager / Recruitment Lead"
                                       class="w-full px-4 py-2.5 bg-white border @error('secondary_contact_position') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                                @error('secondary_contact_position')<span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="secondary_mobile">
                                    Mobile Number
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-semibold text-slate-500">+63</span>
                                    <input type="tel" id="secondary_mobile" name="secondary_mobile" value="{{ old('secondary_mobile') }}" placeholder="917 123 4567"
                                           class="w-full pl-12 pr-4 py-2.5 bg-white border @error('secondary_mobile') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                                </div>
                                @error('secondary_mobile')<span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="secondary_phone">
                                    Direct Landline / Telephone <span class="text-slate-400 font-normal normal-case">(optional)</span>
                                </label>
                                <input type="tel" id="secondary_phone" name="secondary_phone" value="{{ old('secondary_phone') }}" placeholder="(032) 123-4567"
                                       class="w-full px-4 py-2.5 bg-white border @error('secondary_phone') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                                @error('secondary_phone')<span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span>@enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="secondary_email">
                                    Official Email
                                </label>
                                <input type="email" id="secondary_email" name="secondary_email" value="{{ old('secondary_email') }}" placeholder="e.g. recruitment@company.com"
                                       class="w-full px-4 py-2.5 bg-white border @error('secondary_email') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                                @error('secondary_email')<span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- Workplace Address Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- Company Address -->
                        <div class="md:col-span-2 pt-2 border-t border-slate-100">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="address">
                                Street Address / Building / Floor No.
                            </label>
                            <input type="text" id="address" name="address" value="{{ old('address') }}" placeholder="e.g. 10F Cebu IT Tower, Cardinal Rosales Ave., Cebu Business Park"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                        </div>

                        <!-- City -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="city">
                                City / Municipality
                            </label>
                            <input type="text" id="city" name="city" value="{{ old('city', 'Cebu City') }}"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                        </div>

                        <!-- Province -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="province">
                                Province
                            </label>
                            <input type="text" id="province" name="province" value="{{ old('province', 'Cebu') }}"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                        </div>

                        <!-- Postal Code -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="postal_code">
                                Postal Code
                            </label>
                            <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', '6000') }}"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition" />
                        </div>

                        <!-- Country -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="country">
                                Country
                            </label>
                            <input type="text" id="country" name="country" value="{{ old('country', 'Philippines') }}" readonly
                                   class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm text-slate-700 font-medium cursor-not-allowed" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 4: Accreditation Documents -->
            <div class="step-content hidden" data-step="4">
                <div class="px-6 py-5 bg-gradient-to-r from-slate-50 via-white to-slate-50 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-extrabold text-base shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Required Documents and Validity Period</h2>
                        <p class="text-xs text-slate-500">Official DOLE and DMDP Cebu City requirements for verified hiring (Optional at registration, upload anytime before posting jobs)</p>
                    </div>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    <!-- Guidelines Notification Banner -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-emerald-50/90 border border-emerald-200 flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="text-xs text-emerald-950 leading-relaxed">
                            <span class="font-bold text-sm block text-emerald-950 mb-1">Optional at Initial Registration &bull; Required to Post Jobs</span>
                            <p class="text-xs text-emerald-800">
                                You can finish registration today without uploading documents. You will get immediate access to your company profile setup screen. When you are ready to publish job postings, simply submit your accreditation documents via the Accreditation Hub and await Admin/JPO verification. Original and other documents, when applicable, should be presented for validation. Supported formats: PDF, DOC, DOCX, JPG, PNG (Max 10MB per file).
                            </p>
                        </div>
                    </div>

                    <!-- Core Documents -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">1. Core Business Credentials <span class="font-normal text-slate-400 lowercase">(optional now, upload anytime)</span></h3>
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-full">Standard Employers</span>
                        </div>

                        @php
                            $coreDocs = [
                                [
                                    'key' => 'bir_2303',
                                    'title' => 'BIR Certificate of Registration (Form 2303)',
                                    'desc' => 'Valid and active Bureau of Internal Revenue business registration',
                                    'badge' => 'Form 2303',
                                    'badgeColor' => 'bg-emerald-100 text-emerald-800'
                                ],
                                [
                                    'key' => 'sec_dti',
                                    'title' => 'SEC Registration or DTI Registration',
                                    'desc' => 'Articles of Incorporation or DTI Certificate of Business Name Registration',
                                    'badge' => 'SEC / DTI',
                                    'badgeColor' => 'bg-emerald-100 text-emerald-800'
                                ],
                                [
                                    'key' => 'mayors_permit',
                                    'title' => "Mayor's Business Permit (" . date('Y') . ")",
                                    'desc' => 'Current calendar year Cebu City or local government business permit',
                                    'badge' => 'CY ' . date('Y'),
                                    'badgeColor' => 'bg-emerald-100 text-emerald-800'
                                ],
                                [
                                    'key' => 'philjobnet_proof',
                                    'title' => 'PhilJobNet Proof of Registration',
                                    'desc' => 'Certificate or screenshot of active registration on the national PhilJobNet portal',
                                    'badge' => 'PhilJobNet',
                                    'badgeColor' => 'bg-slate-100 text-slate-700'
                                ],
                                [
                                    'key' => 'letter_of_intent',
                                    'title' => 'Letter of Intent (DMDP Cebu City)',
                                    'desc' => 'Official letter expressing intent to partner for recruitment and job placement',
                                    'badge' => 'Partnership',
                                    'badgeColor' => 'bg-slate-100 text-slate-700'
                                ],
                            ];
                        @endphp

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($coreDocs as $doc)
                                <div class="file-upload-card p-4 rounded-2xl border border-slate-200 bg-white hover:border-emerald-300 transition-colors">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-2">
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-bold text-slate-900">{{ $doc['title'] }}</span>
                                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $doc['badgeColor'] }}">{{ $doc['badge'] }}</span>
                                                </div>
                                                <p class="text-xs text-slate-500 mt-0.5">{{ $doc['desc'] }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-2 flex items-center gap-3">
                                        <input type="file" name="documents[{{ $doc['key'] }}]" id="doc_{{ $doc['key'] }}" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                               class="doc-file-input block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 file:cursor-pointer transition" />
                                    </div>
                                    <div class="file-status-pill text-[11px] font-medium text-emerald-700 mt-1.5 hidden flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span class="file-name truncate"></span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Specialized Agency Documents (Collapsible/Organized) -->
                    <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/70 space-y-4">
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">2. Specialized Agency Licenses <span class="font-normal text-slate-400 lowercase">(if applicable)</span></h3>
                            <p class="text-xs text-slate-500 mt-0.5">Required only for Private Recruitment Agencies (PRA), Job Contractors (DOLE DO 174), or Overseas Placement Agencies (DMW).</p>
                        </div>

                        @php
                            $specialDocs = [
                                [
                                    'key' => 'dole_license',
                                    'title' => 'DOLE License / DO 174 Registration',
                                    'desc' => 'Mandatory for licensed subcontractors and manpower service contractors'
                                ],
                                [
                                    'key' => 'dmw_license',
                                    'title' => 'Department of Migrant Workers (DMW) License',
                                    'desc' => 'For overseas recruitment and placement agencies'
                                ],
                                [
                                    'key' => 'dmw_job_orders',
                                    'title' => 'Approved DMW Job Orders / Verified Principal Orders',
                                    'desc' => 'Current validated job orders for overseas hiring campaigns'
                                ],
                            ];
                        @endphp

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                            @foreach($specialDocs as $sDoc)
                                <div class="p-3.5 rounded-xl border border-slate-200 bg-white">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <span class="text-xs font-bold text-slate-800">{{ $sDoc['title'] }}</span>
                                        <span class="text-[10px] font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md">As Applicable</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mb-2">{{ $sDoc['desc'] }}</p>
                                    <input type="file" name="documents[{{ $sDoc['key'] }}]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                           class="doc-file-input block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 file:cursor-pointer transition" />
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Terms & Conditions Acceptance -->
                    <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" id="terms" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} required class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 border-slate-300" />
                            <div class="text-xs text-slate-700 leading-relaxed">
                                <span class="font-bold text-slate-900 block mb-0.5">Accreditation Agreement & Data Privacy Consent <span class="text-rose-500">*</span></span>
                                We certify that our enterprise complies with Philippine labor standards (Labor Code of the Philippines, DOLE issuances, and Cebu City ordinances). We authorize DMDP Cebu City to verify our business credentials and publish our verified job vacancies to registered jobseekers in accordance with Republic Act No. 10173 (Data Privacy Act of 2012).
                            </div>
                        </label>
                        @error('terms')<span class="text-xs text-rose-600 font-semibold mt-2 block">{{ $message }}</span>@enderror
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
                        Submit Employer Application
                    </button>
                </div>
            </div>
        </form>

        <!-- Help / Support Footer -->
        <div class="mt-8 text-center text-xs text-slate-500 space-y-1">
            <p>For inquiries regarding enterprise partnerships, contact DMDP Employer Relations at dmdp@cebucity.gov.ph or (032) 888-1234.</p>
            <p class="text-slate-400">Department of Manpower Development and Placement · Cebu City Government</p>
        </div>

    </div>
</div>

@push('styles')
<style>
/* Stepper Active Styles */
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
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 4;
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
    const secondaryContactGroup = document.querySelector('[data-secondary-contact]');
    const secondaryContactFields = secondaryContactGroup
        ? Array.from(secondaryContactGroup.querySelectorAll('input'))
        : [];
    const requiredSecondaryContactFields = ['secondary_contact_person', 'secondary_contact_position', 'secondary_mobile', 'secondary_email']
        .map(name => secondaryContactGroup?.querySelector(`[name="${name}"]`))
        .filter(Boolean);

    function syncSecondaryContactRequirements() {
        const hasSecondaryContactDetails = secondaryContactFields.some(input => input.value.trim() !== '');
        requiredSecondaryContactFields.forEach(input => {
            input.required = hasSecondaryContactDetails;
        });
    }

    secondaryContactFields.forEach(input => {
        input.addEventListener('input', syncSecondaryContactRequirements);
    });
    syncSecondaryContactRequirements();

    const stepTitles = [
        'Account Credentials',
        'Company Profile',
        'Owner & Authorized Contacts',
        'Accreditation Documents & Terms'
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
        } else {
            nextBtn.classList.remove('hidden');
            submitBtn.classList.add('hidden');
        }
    }

    function validateStep(step) {
        const c = document.querySelector(`.step-content[data-step="${step}"]`);
        if (!c) return true;

        if (step === 3) syncSecondaryContactRequirements();
        const inputs = c.querySelectorAll('input[required], select[required], textarea[required]');
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

    // Document file upload feedback pill
    document.querySelectorAll('.doc-file-input').forEach(input => {
        input.addEventListener('change', function() {
            const card = this.closest('.file-upload-card');
            if (!card) return;
            const pill = card.querySelector('.file-status-pill');
            const nameSpan = card.querySelector('.file-name');
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const sizeKb = Math.round(file.size / 1024);
                if (nameSpan) nameSpan.textContent = `Selected: ${file.name} (${sizeKb} KB)`;
                if (pill) pill.classList.remove('hidden');
                card.classList.add('border-emerald-400', 'bg-emerald-50/20');
            } else {
                if (pill) pill.classList.add('hidden');
                card.classList.remove('border-emerald-400', 'bg-emerald-50/20');
            }
        });
    });

    // Form submit handling
    form.addEventListener('submit', function(e) {
        const terms = document.getElementById('terms');
        if (terms && !terms.checked) {
            e.preventDefault();
            alert('Please review and check the Accreditation Agreement & Data Privacy Consent checkbox to proceed.');
            terms.focus();
            return false;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Submitting Employer Application...
        `;
    });

    // Sync Step 1 primary email with Step 3 primary_email unless manually edited
    const emailField = document.getElementById('email');
    const primaryEmailField = document.getElementById('primary_email');
    if (emailField && primaryEmailField) {
        if (!primaryEmailField.value && emailField.value) {
            primaryEmailField.value = emailField.value;
        }
        emailField.addEventListener('input', function() {
            if (!primaryEmailField.dataset.userEdited) {
                primaryEmailField.value = this.value;
            }
        });
        primaryEmailField.addEventListener('input', function() {
            primaryEmailField.dataset.userEdited = 'true';
        });
    }

    // Auto-detect Laravel errors and jump to step
    @if($errors->any())
        const errorFields = @json($errors->keys());
        let targetErrorStep = 1;

        if (errorFields.some(f => ['email', 'password', 'role'].includes(f))) {
            targetErrorStep = 1;
        } else if (errorFields.some(f => ['company_name', 'company_description', 'industry', 'company_size', 'website', 'company_type'].includes(f))) {
            targetErrorStep = 2;
        } else if (errorFields.some(f => ['contact_person', 'contact_position', 'phone', 'mobile', 'primary_email', 'secondary_contact_person', 'secondary_contact_position', 'secondary_phone', 'secondary_mobile', 'secondary_email', 'address', 'city', 'province', 'postal_code'].includes(f))) {
            targetErrorStep = 3;
        } else if (errorFields.some(f => f.startsWith('documents') || f === 'terms')) {
            targetErrorStep = 4;
        }
        currentStep = targetErrorStep;
    @endif

    updateStepUI();
});
</script>
@endpush
@endsection