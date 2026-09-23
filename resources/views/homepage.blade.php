@extends('layouts.public')

@section('title', 'DMDP Cebu City - Department of Manpower Development and Placement')

@section('content')
<div class="bg-white">

    <!-- Hero Section -->
    <div class="bg-green-950 relative overflow-hidden">
        <div class="max-w-5xl mx-auto px-5 py-20 md:py-24">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-12">
                
                <!-- Left: Title -->
                <div class="flex-1 space-y-5">
                    <div class="inline-flex items-center gap-2 rounded-full bg-green-900/60 border border-green-800/80 px-3.5 py-1.5 text-xs font-semibold text-green-300">
                        <span class="h-2 w-2 rounded-full bg-green-400 animate-pulse"></span>
                        Cebu City Government &bull; DMDP PESO Portal
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl font-extrabold text-white leading-tight tracking-tight">
                        Department of Manpower
                        <span class="block text-green-400 mt-1">Development &amp; Placement</span>
                    </h1>
                    
                    <p class="text-green-100/75 text-sm sm:text-base leading-relaxed max-w-lg">
                        <em class="font-medium text-green-200">"Dugang Makat-unan, alang sa Disenting Panginabuhian"</em> — Bridging Cebuano jobseekers to sustainable careers through skill development and verified job placement.
                    </p>

                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-green-300/90 bg-green-900/50 border border-green-800/50 px-3 py-1.5 rounded-full">
                            <span class="h-1.5 w-1.5 rounded-full bg-green-400"></span>
                            Verified Job Listings
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-green-300/90 bg-green-900/50 border border-green-800/50 px-3 py-1.5 rounded-full">
                            <span class="h-1.5 w-1.5 rounded-full bg-green-400"></span>
                            Free Vocational Training
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-green-300/90 bg-green-900/50 border border-green-800/50 px-3 py-1.5 rounded-full">
                            <span class="h-1.5 w-1.5 rounded-full bg-green-400"></span>
                            PWD-Inclusive Opportunities
                        </span>
                    </div>
                </div>

                <!-- Right: CTA Card -->
                <div class="shrink-0 w-full lg:w-[340px]">
                    <div class="bg-green-900/70 border border-green-800/80 rounded-2xl p-7 space-y-5 shadow-xl backdrop-blur-sm">
                        <div class="text-center">
                            <div class="w-14 h-14 bg-green-600 rounded-2xl flex items-center justify-center mx-auto text-white font-black text-2xl shadow-lg">
                                T
                            </div>
                            <p class="text-xs font-semibold text-green-400 uppercase tracking-wider mt-3">Empowering Cebuanos</p>
                            <p class="text-2xl font-extrabold text-white mt-1">
                                Traba<span class="text-green-400">Go</span>
                            </p>
                            <p class="text-green-200/70 text-xs mt-1.5">
                                AI Skill Matching &bull; Vocational Training &bull; Direct Placement
                            </p>
                        </div>

                        <div class="border-t border-green-800/60 pt-4 space-y-2.5">
                            <a href="{{ route('register') }}" 
                               class="flex items-center justify-center w-full px-5 py-3 bg-green-600 hover:bg-green-500 text-white font-semibold text-sm rounded-xl transition-colors shadow-md">
                                Get Started Free &rarr;
                            </a>
                            
                            <p class="text-xs text-green-300/70 text-center">
                                Existing member?
                                <a href="{{ route('login') }}" class="font-semibold text-green-400 hover:text-green-300">Sign In</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Official Mandate Section: Vision, Mission & Core Values -->
    <section class="py-16 md:py-24 bg-gray-50/70 border-b border-gray-200">
        <div class="max-w-5xl mx-auto px-5">
            
            <!-- Section Header -->
            <div class="text-center max-w-2xl mx-auto space-y-3 mb-14">
                <div class="inline-flex items-center gap-2 rounded-md bg-green-50 border border-green-200 px-3 py-1 text-xs font-bold text-green-700 uppercase tracking-wider">
                    Cebu City Government &bull; DMDP
                </div>
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl">
                    Vision, Mission &amp; Core Values
                </h2>
                <p class="text-sm text-gray-600 leading-relaxed">
                    The guiding framework of the Department of Manpower Development and Placement (DMDP) in fostering sustainable livelihood and vocational excellence.
                </p>
            </div>

            <!-- 3 Columns Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                
                <!-- 1. Vision Card -->
                <div class="rounded-2xl border border-gray-200 bg-white p-7 flex flex-col justify-between hover:border-green-300 hover:shadow-md transition-all">
                    <div class="space-y-5">
                        <!-- Hex / Icon Badge -->
                        <div class="flex items-center justify-between">
                            <div class="w-12 h-12 rounded-xl bg-green-600 text-white flex items-center justify-center shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-bold uppercase tracking-wider text-green-700 bg-green-50 border border-green-200 px-2.5 py-1 rounded-md">
                                Vision
                            </span>
                        </div>

                        <div>
                            <h3 class="text-xl font-extrabold text-gray-900 tracking-tight">
                                DMDP PESO
                            </h3>
                            <p class="text-xs font-semibold text-green-700 mt-0.5">
                                Public Employment Service Office
                            </p>
                        </div>

                        <ul class="space-y-1.5 text-xs text-gray-700 font-medium">
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-green-100 text-green-800 font-bold flex items-center justify-center text-[11px] shrink-0">D</span>
                                <span><strong class="text-gray-900">D</strong>eveloped</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-green-100 text-green-800 font-bold flex items-center justify-center text-[11px] shrink-0">M</span>
                                <span><strong class="text-gray-900">M</strong>eaningful</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-green-100 text-green-800 font-bold flex items-center justify-center text-[11px] shrink-0">D</span>
                                <span><strong class="text-gray-900">D</strong>ynamic</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-green-100 text-green-800 font-bold flex items-center justify-center text-[11px] shrink-0">P</span>
                                <span><strong class="text-gray-900">P</strong>rograms for</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-green-100 text-green-800 font-bold flex items-center justify-center text-[11px] shrink-0">P</span>
                                <span><strong class="text-gray-900">P</strong>eople's</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-green-100 text-green-800 font-bold flex items-center justify-center text-[11px] shrink-0">E</span>
                                <span><strong class="text-gray-900">E</strong>mployability</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-green-100 text-green-800 font-bold flex items-center justify-center text-[11px] shrink-0">S</span>
                                <span><strong class="text-gray-900">S</strong>kills and</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-green-100 text-green-800 font-bold flex items-center justify-center text-[11px] shrink-0">O</span>
                                <span><strong class="text-gray-900">O</strong>pportunities</span>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-6 border-t border-gray-100 mt-6">
                        <div class="flex items-center gap-2 text-xs text-gray-500 font-medium">
                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                            Continuous community program development
                        </div>
                    </div>
                </div>

                <!-- 2. Mission Card -->
                <div class="rounded-2xl border border-gray-200 bg-white p-7 flex flex-col justify-between hover:border-blue-300 hover:shadow-md transition-all">
                    <div class="space-y-5">
                        <!-- Hex / Icon Badge -->
                        <div class="flex items-center justify-between">
                            <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9" stroke-width="2"/>
                                    <circle cx="12" cy="12" r="5" stroke-width="2"/>
                                    <circle cx="12" cy="12" r="1" stroke-width="2" fill="currentColor"/>
                                </svg>
                            </div>
                            <span class="text-xs font-bold uppercase tracking-wider text-blue-700 bg-blue-50 border border-blue-200 px-2.5 py-1 rounded-md">
                                Mission
                            </span>
                        </div>

                        <div>
                            <h3 class="text-xl font-extrabold text-gray-900 tracking-tight">
                                Advancing AcCESS
                            </h3>
                            <p class="text-xs font-semibold text-blue-700 mt-0.5">
                                Strategic Milestone by 2025 &amp; Beyond
                            </p>
                        </div>

                        <ul class="space-y-1.5 text-xs text-gray-700 font-medium">
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-6 h-5 rounded-md bg-blue-100 text-blue-800 font-bold flex items-center justify-center text-[10px] shrink-0">Ac</span>
                                <span><strong class="text-gray-900">Ac</strong>celerating</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-6 h-5 rounded-md bg-blue-100 text-blue-800 font-bold flex items-center justify-center text-[10px] shrink-0">C</span>
                                <span><strong class="text-gray-900">C</strong>areer</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-6 h-5 rounded-md bg-blue-100 text-blue-800 font-bold flex items-center justify-center text-[10px] shrink-0">E</span>
                                <span><strong class="text-gray-900">E</strong>ntrepreneurial and</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-6 h-5 rounded-md bg-blue-100 text-blue-800 font-bold flex items-center justify-center text-[10px] shrink-0">S</span>
                                <span><strong class="text-gray-900">S</strong>kills development for</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-6 h-5 rounded-md bg-blue-100 text-blue-800 font-bold flex items-center justify-center text-[10px] shrink-0">S</span>
                                <span><strong class="text-gray-900">S</strong>uccess</span>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-6 border-t border-gray-100 mt-6">
                        <div class="flex items-center gap-2 text-xs text-gray-500 font-medium">
                            <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                            Expanding livelihood &amp; enterprise pathways
                        </div>
                    </div>
                </div>

                <!-- 3. Core Values Card -->
                <div class="rounded-2xl border border-gray-200 bg-white p-7 flex flex-col justify-between hover:border-green-300 hover:shadow-md transition-all">
                    <div class="space-y-5">
                        <!-- Hex / Icon Badge -->
                        <div class="flex items-center justify-between">
                            <div class="w-12 h-12 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-bold uppercase tracking-wider text-amber-800 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-md">
                                Core Values
                            </span>
                        </div>

                        <div>
                            <h3 class="text-xl font-extrabold text-gray-900 tracking-tight">
                                We EMPLOY
                            </h3>
                            <p class="text-xs font-semibold text-amber-700 mt-0.5">
                                Principles Guiding Public Service
                            </p>
                        </div>

                        <ul class="space-y-1.5 text-xs text-gray-700 font-medium">
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-amber-100 text-amber-800 font-bold flex items-center justify-center text-[11px] shrink-0">E</span>
                                <span><strong class="text-gray-900">E</strong>xcellence</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-amber-100 text-amber-800 font-bold flex items-center justify-center text-[11px] shrink-0">M</span>
                                <span><strong class="text-gray-900">M</strong>ission-driven</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-amber-100 text-amber-800 font-bold flex items-center justify-center text-[11px] shrink-0">P</span>
                                <span><strong class="text-gray-900">P</strong>eoples-oriented</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-amber-100 text-amber-800 font-bold flex items-center justify-center text-[11px] shrink-0">L</span>
                                <span><strong class="text-gray-900">L</strong>imitless</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-amber-100 text-amber-800 font-bold flex items-center justify-center text-[11px] shrink-0">O</span>
                                <span><strong class="text-gray-900">O</strong>pen-minded</span>
                            </li>
                            <li class="flex items-center gap-2 py-0.5">
                                <span class="w-5 h-5 rounded-md bg-amber-100 text-amber-800 font-bold flex items-center justify-center text-[11px] shrink-0">Y</span>
                                <span><strong class="text-gray-900">Y</strong>ielding results</span>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-6 border-t border-gray-100 mt-6">
                        <div class="flex items-center gap-2 text-xs text-gray-500 font-medium">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                            Dedicated to public accountability &amp; impact
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- Core Services Overview -->
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="p-6 rounded-2xl border border-gray-100 bg-gray-50/50 space-y-3">
                    <div class="w-10 h-10 rounded-lg bg-green-100 text-green-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900">Direct Job Matching</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Access accredited local and international job postings evaluated by the Public Employment Service Office.
                    </p>
                    <a href="{{ route('jobs.index') }}" class="text-xs font-semibold text-green-700 hover:text-green-800 inline-flex items-center gap-1">
                        Browse Vacancies &rarr;
                    </a>
                </div>

                <div class="p-6 rounded-2xl border border-gray-100 bg-gray-50/50 space-y-3">
                    <div class="w-10 h-10 rounded-lg bg-green-100 text-green-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900">Vocational Skills Training</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Free competency certifications and hands-on lab modules to elevate your market readiness.
                    </p>
                    <a href="{{ route('register') }}" class="text-xs font-semibold text-green-700 hover:text-green-800 inline-flex items-center gap-1">
                        Join Courses &rarr;
                    </a>
                </div>

                <div class="p-6 rounded-2xl border border-gray-100 bg-gray-50/50 space-y-3">
                    <div class="w-10 h-10 rounded-lg bg-green-100 text-green-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900">Employer Accreditation</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Verified business entities compliant with DOLE and local labor standards for fair, safe recruitment.
                    </p>
                    <a href="{{ route('register') }}" class="text-xs font-semibold text-green-700 hover:text-green-800 inline-flex items-center gap-1">
                        Accredit Company &rarr;
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- Bottom CTA -->
    <div class="bg-green-900 py-14 px-5 text-center">
        <div class="max-w-2xl mx-auto space-y-4">
            <h3 class="text-2xl sm:text-3xl font-extrabold text-white">Be Part of the Cebu City Workforce</h3>
            <p class="text-sm text-green-100/75 leading-relaxed">
                Connect directly with accredited employers and boost your employability through free skills certifications.
            </p>
            <div class="pt-2">
                <a href="{{ route('jobs.index') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-500 text-white font-semibold text-sm rounded-xl transition-colors shadow-lg">
                    Explore Job Vacancies &rarr;
                </a>
            </div>
        </div>
    </div>

</div>
@endsection