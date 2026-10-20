@extends('layouts.public')

@section('title', 'DMDP Cebu City - Department of Manpower Development and Placement | TrabaGo')

@section('content')
<div class="bg-white">

    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-emerald-950 via-emerald-900 to-slate-950 text-white">
        <!-- Ambient Glow Elements -->
        <div class="absolute -right-24 -top-24 w-96 h-96 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute left-1/3 -bottom-24 w-80 h-80 rounded-full bg-emerald-400/10 blur-3xl pointer-events-none"></div>
        <div class="absolute left-10 top-20 w-40 h-40 rounded-full bg-teal-500/10 blur-2xl pointer-events-none"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-16 sm:py-20 md:py-24 relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-12">
                
                <!-- Left: Headline & Live Search -->
                <div class="flex-1 space-y-6">
                    <div class="inline-flex items-center gap-2 rounded-full bg-emerald-500/15 border border-emerald-400/25 px-4 py-1.5 text-xs font-bold text-emerald-300 shadow-xs">
                        <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Cebu City Government &bull; Official DMDP PESO Portal
                    </div>
                    
                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-5xl font-black text-white leading-tight tracking-tight">
                        Department of Manpower
                        <span class="block bg-gradient-to-r from-emerald-400 via-teal-300 to-emerald-200 bg-clip-text text-transparent mt-1">
                            Development &amp; Placement
                        </span>
                    </h1>
                    
                    <p class="text-emerald-100/80 text-sm sm:text-base leading-relaxed max-w-xl">
                        <em class="font-semibold text-emerald-300">"Dugang Makat-unan, alang sa Disenting Panginabuhian"</em> — Bridging Cebuano jobseekers to verified careers through automated AI skill matching, free vocational training, and direct placement with accredited employers.
                    </p>

                    <!-- Instant Quick Job Search Bar -->
                    <form action="{{ route('jobs.index') }}" method="GET" class="p-2 bg-emerald-900/60 border border-emerald-700/60 rounded-2xl shadow-xl backdrop-blur-md max-w-xl">
                        <div class="flex flex-col sm:flex-row gap-2">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <input type="text" name="q" placeholder="Job title, technical skill, or company..." 
                                       class="w-full pl-10 pr-3 py-2.5 bg-emerald-950/70 border border-emerald-800/80 rounded-xl text-white placeholder-emerald-300/50 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 transition" />
                            </div>
                            <button type="submit" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs sm:text-sm rounded-xl transition-all shadow-md shadow-emerald-500/20 active:scale-95 shrink-0">
                                Search Jobs &rarr;
                            </button>
                        </div>
                    </form>

                    <!-- Trust Pill Badges -->
                    <div class="flex flex-wrap items-center gap-2.5 pt-1">
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-300/90 bg-emerald-900/40 border border-emerald-800/50 px-3 py-1.5 rounded-full">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                            Verified DOLE Job Postings
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-300/90 bg-emerald-900/40 border border-emerald-800/50 px-3 py-1.5 rounded-full">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                            100% Free Vocational Courses
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-300/90 bg-emerald-900/40 border border-emerald-800/50 px-3 py-1.5 rounded-full">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                            PWD-Inclusive Opportunities
                        </span>
                    </div>
                </div>

                <!-- Right: Action Portal Card -->
                <div class="shrink-0 w-full lg:w-[350px]">
                    <div class="bg-gradient-to-b from-emerald-900/80 to-slate-900/90 border border-emerald-700/60 rounded-3xl p-7 space-y-6 shadow-2xl backdrop-blur-md">
                        <div class="text-center space-y-2">
                            <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto text-white font-black text-2xl shadow-lg shadow-emerald-500/30">
                                T
                            </div>
                            <p class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Public Employment Portal</p>
                            <h2 class="text-2xl font-black text-white">
                                Traba<span class="text-emerald-400">Go</span> Cebu City
                            </h2>
                            <p class="text-emerald-100/70 text-xs leading-relaxed max-w-xs mx-auto">
                                AI Skill Matching &bull; Certified Vocational Training &bull; Direct Employer Referrals
                            </p>
                        </div>

                        <div class="border-t border-emerald-800/60 pt-5 space-y-3">
                            <a href="{{ route('jobseeker.register') }}" 
                               class="flex items-center justify-center gap-2 w-full px-5 py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm rounded-xl transition-all shadow-lg shadow-emerald-600/30 active:scale-95">
                                <span>Register as Jobseeker</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                            
                            <a href="{{ route('employer.register') }}" 
                               class="flex items-center justify-center gap-2 w-full px-5 py-3 bg-emerald-950/70 hover:bg-emerald-900/80 border border-emerald-700/60 text-emerald-200 font-bold text-xs rounded-xl transition-all">
                                <span>Register as Employer Partner</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>

                            <div class="pt-2 text-center text-xs text-emerald-200/70">
                                Already registered?
                                <a href="{{ route('login') }}" class="font-bold text-emerald-400 hover:text-emerald-300 hover:underline ml-1">Sign In to Account</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Live Statistics Counter Bar -->
    <div class="border-y border-slate-200/80 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div class="p-2">
                    <span class="block text-2xl sm:text-3xl font-black text-slate-900">80</span>
                    <span class="block text-xs font-semibold text-slate-500 mt-0.5">Cebu City Barangays</span>
                </div>
                <div class="p-2">
                    <span class="block text-2xl sm:text-3xl font-black text-emerald-600">100%</span>
                    <span class="block text-xs font-semibold text-slate-500 mt-0.5">Free Vocational Courses</span>
                </div>
                <div class="p-2">
                    <span class="block text-2xl sm:text-3xl font-black text-slate-900">DOLE</span>
                    <span class="block text-xs font-semibold text-slate-500 mt-0.5">Accredited Standards</span>
                </div>
                <div class="p-2">
                    <span class="block text-2xl sm:text-3xl font-black text-emerald-600">AI</span>
                    <span class="block text-xs font-semibold text-slate-500 mt-0.5">Skill-Matched Placements</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Official Mandate Section: Vision, Mission & Core Values -->
    <section class="py-16 md:py-24 bg-gradient-to-b from-white to-slate-50/70 border-b border-slate-200/70">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            
            <!-- Section Header -->
            <div class="text-center max-w-2xl mx-auto space-y-3 mb-14">
                <div class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100/80 border border-emerald-200/80 px-3.5 py-1 text-xs font-bold text-emerald-800 uppercase tracking-wider">
                    Cebu City Government &bull; DMDP Mandate
                </div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight sm:text-4xl">
                    Vision, Mission &amp; Core Values
                </h2>
                <p class="text-sm text-slate-600 leading-relaxed">
                    The strategic governance framework of the Department of Manpower Development and Placement (DMDP) in fostering sustainable livelihood, equal opportunity, and vocational excellence.
                </p>
            </div>

            <!-- 3 Columns Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                
                <!-- 1. Vision Card -->
                <div class="rounded-3xl border border-slate-200/90 bg-white p-7 sm:p-8 flex flex-col justify-between hover:border-emerald-500 hover:shadow-xl hover:shadow-emerald-950/5 transition-all duration-200 group">
                    <div class="space-y-5">
                        <div class="flex items-center justify-between">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700 text-white flex items-center justify-center shadow-md shadow-emerald-600/20 group-hover:scale-105 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full">
                                Vision
                            </span>
                        </div>

                        <div>
                            <h3 class="text-xl font-black text-slate-900 tracking-tight">
                                DMDP PESO
                            </h3>
                            <p class="text-xs font-bold text-emerald-700 mt-0.5">
                                Public Employment Service Office
                            </p>
                        </div>

                        <ul class="space-y-1.5 text-xs text-slate-700 font-medium">
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-[11px] shrink-0">D</span>
                                <span><strong class="text-slate-900">D</strong>eveloped</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-[11px] shrink-0">M</span>
                                <span><strong class="text-slate-900">M</strong>eaningful</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-[11px] shrink-0">D</span>
                                <span><strong class="text-slate-900">D</strong>ynamic</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-[11px] shrink-0">P</span>
                                <span><strong class="text-slate-900">P</strong>rograms for</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-[11px] shrink-0">P</span>
                                <span><strong class="text-slate-900">P</strong>eople's</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-[11px] shrink-0">E</span>
                                <span><strong class="text-slate-900">E</strong>mployability</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-[11px] shrink-0">S</span>
                                <span><strong class="text-slate-900">S</strong>kills and</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-[11px] shrink-0">O</span>
                                <span><strong class="text-slate-900">O</strong>pportunities</span>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-6 border-t border-slate-100 mt-6">
                        <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                            Continuous community program development
                        </div>
                    </div>
                </div>

                <!-- 2. Mission Card -->
                <div class="rounded-3xl border border-slate-200/90 bg-white p-7 sm:p-8 flex flex-col justify-between hover:border-teal-500 hover:shadow-xl hover:shadow-teal-950/5 transition-all duration-200 group">
                    <div class="space-y-5">
                        <div class="flex items-center justify-between">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-teal-600 to-slate-900 text-white flex items-center justify-center shadow-md shadow-teal-700/20 group-hover:scale-105 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9" stroke-width="2"/>
                                    <circle cx="12" cy="12" r="5" stroke-width="2"/>
                                    <circle cx="12" cy="12" r="1" stroke-width="2" fill="currentColor"/>
                                </svg>
                            </div>
                            <span class="text-xs font-bold uppercase tracking-wider text-teal-800 bg-teal-50 border border-teal-200 px-3 py-1 rounded-full">
                                Mission
                            </span>
                        </div>

                        <div>
                            <h3 class="text-xl font-black text-slate-900 tracking-tight">
                                Advancing AcCESS
                            </h3>
                            <p class="text-xs font-bold text-teal-700 mt-0.5">
                                Strategic Milestone by 2025 &amp; Beyond
                            </p>
                        </div>

                        <ul class="space-y-1.5 text-xs text-slate-700 font-medium">
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-6 h-5 rounded-md bg-teal-100 text-teal-800 font-bold flex items-center justify-center text-[10px] shrink-0">Ac</span>
                                <span><strong class="text-slate-900">Ac</strong>celerating</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-6 h-5 rounded-md bg-teal-100 text-teal-800 font-bold flex items-center justify-center text-[10px] shrink-0">C</span>
                                <span><strong class="text-slate-900">C</strong>areer</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-6 h-5 rounded-md bg-teal-100 text-teal-800 font-bold flex items-center justify-center text-[10px] shrink-0">E</span>
                                <span><strong class="text-slate-900">E</strong>ntrepreneurial and</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-6 h-5 rounded-md bg-teal-100 text-teal-800 font-bold flex items-center justify-center text-[10px] shrink-0">S</span>
                                <span><strong class="text-slate-900">S</strong>kills development for</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-6 h-5 rounded-md bg-teal-100 text-teal-800 font-bold flex items-center justify-center text-[10px] shrink-0">S</span>
                                <span><strong class="text-slate-900">S</strong>uccess</span>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-6 border-t border-slate-100 mt-6">
                        <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                            <span class="h-1.5 w-1.5 rounded-full bg-teal-600"></span>
                            Expanding livelihood &amp; enterprise pathways
                        </div>
                    </div>
                </div>

                <!-- 3. Core Values Card -->
                <div class="rounded-3xl border border-slate-200/90 bg-white p-7 sm:p-8 flex flex-col justify-between hover:border-amber-400 hover:shadow-xl hover:shadow-amber-950/5 transition-all duration-200 group">
                    <div class="space-y-5">
                        <div class="flex items-center justify-between">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-700 text-white flex items-center justify-center shadow-md shadow-amber-600/20 group-hover:scale-105 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-bold uppercase tracking-wider text-amber-800 bg-amber-50 border border-amber-200 px-3 py-1 rounded-full">
                                Core Values
                            </span>
                        </div>

                        <div>
                            <h3 class="text-xl font-black text-slate-900 tracking-tight">
                                We EMPLOY
                            </h3>
                            <p class="text-xs font-bold text-amber-700 mt-0.5">
                                Principles Guiding Public Service
                            </p>
                        </div>

                        <ul class="space-y-1.5 text-xs text-slate-700 font-medium">
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-amber-100 text-amber-800 font-bold flex items-center justify-center text-[11px] shrink-0">E</span>
                                <span><strong class="text-slate-900">E</strong>xcellence</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-amber-100 text-amber-800 font-bold flex items-center justify-center text-[11px] shrink-0">M</span>
                                <span><strong class="text-slate-900">M</strong>ission-driven</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-amber-100 text-amber-800 font-bold flex items-center justify-center text-[11px] shrink-0">P</span>
                                <span><strong class="text-slate-900">P</strong>eoples-oriented</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-amber-100 text-amber-800 font-bold flex items-center justify-center text-[11px] shrink-0">L</span>
                                <span><strong class="text-slate-900">L</strong>imitless</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-amber-100 text-amber-800 font-bold flex items-center justify-center text-[11px] shrink-0">O</span>
                                <span><strong class="text-slate-900">O</strong>pen-minded</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-amber-100 text-amber-800 font-bold flex items-center justify-center text-[11px] shrink-0">Y</span>
                                <span><strong class="text-slate-900">Y</strong>ielding results</span>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-6 border-t border-slate-100 mt-6">
                        <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                            Dedicated to public accountability &amp; impact
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- How TrabaGo Works (Step Progression) -->
    <section class="py-16 md:py-20 bg-slate-50/60">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            <div class="text-center max-w-xl mx-auto mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 block mb-2">Simple 3-Step Process</span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900">How TrabaGo Connects You</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">From registration to employment contract with Cebu City support</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 rounded-3xl bg-white border border-slate-200/80 shadow-xs relative">
                    <span class="w-9 h-9 rounded-2xl bg-emerald-100 text-emerald-800 font-black text-sm flex items-center justify-center mb-4">1</span>
                    <h3 class="text-base font-bold text-slate-900">Create Profile & Verify</h3>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Register with your Cebu City residency details and complete your educational background and skills inventory.
                    </p>
                </div>

                <div class="p-6 rounded-3xl bg-white border border-slate-200/80 shadow-xs relative">
                    <span class="w-9 h-9 rounded-2xl bg-emerald-100 text-emerald-800 font-black text-sm flex items-center justify-center mb-4">2</span>
                    <h3 class="text-base font-bold text-slate-900">AI Match & Skills Training</h3>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Our engine computes skill compatibility with employer job orders while offering free courses for qualification gaps.
                    </p>
                </div>

                <div class="p-6 rounded-3xl bg-white border border-slate-200/80 shadow-xs relative">
                    <span class="w-9 h-9 rounded-2xl bg-emerald-100 text-emerald-800 font-black text-sm flex items-center justify-center mb-4">3</span>
                    <h3 class="text-base font-bold text-slate-900">Referral & Placement</h3>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Receive official DMDP referral notices, interview invitations, and secure sustainable placement with accredited firms.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Services Overview -->
    <section class="py-16 md:py-24 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            <div class="text-center max-w-xl mx-auto mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 block mb-2">Our Public Services</span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900">Built for Cebuano Workers & Employers</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="p-7 rounded-3xl border border-slate-200/90 bg-white hover:border-emerald-500 hover:shadow-xl hover:shadow-emerald-950/5 transition-all duration-200 space-y-4 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Direct Job Placement</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Access accredited local and international job postings evaluated and verified by the Public Employment Service Office.
                        </p>
                    </div>
                    <a href="{{ route('jobs.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 inline-flex items-center gap-1.5 pt-2">
                        <span>Browse Verified Vacancies</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                <div class="p-7 rounded-3xl border border-slate-200/90 bg-white hover:border-emerald-500 hover:shadow-xl hover:shadow-emerald-950/5 transition-all duration-200 space-y-4 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Vocational Skills Training</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Free competency certifications, laboratory modules, and industry certificates sponsored by Cebu City Hall.
                        </p>
                    </div>
                    <a href="{{ route('jobseeker.register') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 inline-flex items-center gap-1.5 pt-2">
                        <span>Enroll in Free Courses</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                <div class="p-7 rounded-3xl border border-slate-200/90 bg-white hover:border-emerald-500 hover:shadow-xl hover:shadow-emerald-950/5 transition-all duration-200 space-y-4 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Employer Accreditation</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Verified business entities compliant with DOLE and Cebu City labor guidelines for fair and fast manpower recruitment.
                        </p>
                    </div>
                    <a href="{{ route('employer.register') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 inline-flex items-center gap-1.5 pt-2">
                        <span>Accredit Your Company</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- Bottom CTA Banner -->
    <div class="relative overflow-hidden bg-gradient-to-r from-emerald-950 via-emerald-900 to-slate-950 py-16 px-4 sm:px-6 text-center text-white">
        <div class="max-w-2xl mx-auto space-y-5 relative z-10">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/25">
                Join the Cebuano Workforce
            </span>
            <h3 class="text-2xl sm:text-3xl md:text-4xl font-black text-white">
                Take the Next Step in Your Career Today
            </h3>
            <p class="text-sm text-emerald-100/75 leading-relaxed max-w-lg mx-auto">
                Connect directly with accredited employers, get AI skill-matched recommendations, and enroll in certified vocational programs for free.
            </p>
            <div class="pt-3 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('jobs.index') }}" 
                   class="w-full sm:w-auto px-7 py-3.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-sm rounded-xl transition-all shadow-lg shadow-emerald-500/25 active:scale-95">
                    Explore Job Vacancies &rarr;
                </a>
                <a href="{{ route('register') }}" 
                   class="w-full sm:w-auto px-7 py-3.5 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-sm rounded-xl transition-all active:scale-95">
                    Create Free Account
                </a>
            </div>
        </div>
    </div>

</div>
@endsection