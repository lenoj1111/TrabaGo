@extends('layouts.public')

@section('title', 'DMDP Cebu City - Department of Manpower Development and Placement | TrabaGo')

@push('styles')
<style>
    /*
     * ADVANCED UI UPGRADES:
     * 1. Eye-strain reduction (soft off-whites, deeper greens).
     * 2. Premium glassmorphism and subtle hover interactions.
     * 3. HIGHLY VISIBLE live animated backgrounds (Grid + Sweeping Light Beam).
     */

    .home-hero {
        background:
            radial-gradient(ellipse at 20% 10%, rgba(16, 185, 129, 0.15), transparent 45%),
            radial-gradient(ellipse at 85% 30%, rgba(13, 148, 136, 0.1), transparent 40%),
            linear-gradient(135deg, #022117 0%, #04382b 50%, #020e14 100%);
        background-size: 140% 140%;
    }

    .home-card {
        background: #fcfdfc;
        border: 1px solid rgba(0, 0, 0, 0.03);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        transition: transform 350ms cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 350ms ease, border-color 350ms ease;
    }

    .home-card:hover,
    .home-card:focus-within {
        transform: translateY(-4px);
        background: #ffffff;
        border-color: rgba(16, 185, 129, 0.15);
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.08), 0 10px 15px -5px rgba(0, 0, 0, 0.03);
    }

    .has-reveal [data-reveal].is-visible.home-card:hover,
    .has-reveal [data-reveal].is-visible.home-card:focus-within {
        transform: translateY(-4px);
    }

    .home-action {
        transition: transform 250ms cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 250ms ease, background-color 250ms ease;
    }

    .home-action:hover,
    .home-action:focus-visible {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px rgba(6, 78, 59, 0.3);
    }

    .home-action:active {
        transform: translateY(0) scale(0.98);
    }

    /* Premium scroll reveal */
    .has-reveal [data-reveal] {
        opacity: 0;
        filter: blur(4px);
        transform: translate3d(0, 30px, 0) scale(0.98);
        transition:
            opacity 800ms cubic-bezier(0.2, 0.8, 0.2, 1) var(--reveal-delay, 0ms),
            filter 800ms cubic-bezier(0.2, 0.8, 0.2, 1) var(--reveal-delay, 0ms),
            transform 800ms cubic-bezier(0.2, 0.8, 0.2, 1) var(--reveal-delay, 0ms);
        will-change: opacity, filter, transform;
    }

    .has-reveal [data-reveal].is-visible {
        opacity: 1;
        filter: blur(0);
        transform: translate3d(0, 0, 0) scale(1);
    }

    /* --- HIGHLY VISIBLE LIVE ANIMATED BACKGROUNDS --- */

    /* 1. Sweeping Light Beam */
    @keyframes sweepLight {
        0% {
            transform: translateX(-100%) skewX(-15deg);
            opacity: 0;
        }

        10% {
            opacity: 0.8;
        }

        50% {
            opacity: 0.6;
        }

        90% {
            opacity: 0.8;
        }

        100% {
            transform: translateX(200%) skewX(-15deg);
            opacity: 0;
        }
    }

    .light-beam {
        position: absolute;
        top: -50%;
        left: 0;
        width: 50%;
        height: 200%;
        background: linear-gradient(90deg,
                transparent 0%,
                rgba(16, 185, 129, 0.08) 20%,
                rgba(52, 211, 153, 0.2) 50%,
                rgba(16, 185, 129, 0.08) 80%,
                transparent 100%);
        pointer-events: none;
        z-index: 0;
        animation: sweepLight 12s linear infinite;
        will-change: transform, opacity;
    }

    /* 2. Highly Visible Moving Grid */
    @keyframes panGrid {
        0% {
            background-position: 0% 0%;
        }

        100% {
            background-position: 100% 100%;
        }
    }

    .animated-grid {
        position: absolute;
        inset: 0;
        pointer-events: none;
        z-index: 0;
        background-image:
            linear-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.08) 1px, transparent 1px);
        background-size: 50px 50px;
        animation: panGrid 60s linear infinite;
        mask-image: radial-gradient(ellipse at center, black 50%, transparent 90%);
        -webkit-mask-image: radial-gradient(ellipse at center, black 50%, transparent 90%);
        will-change: background-position;
    }

    @media (prefers-reduced-motion: reduce) {

        .home-hero,
        .home-card,
        .home-action,
        .home-hero .animate-pulse,
        .has-reveal [data-reveal],
        .has-reveal [data-reveal].is-visible,
        .light-beam,
        .animated-grid {
            animation: none !important;
            transition: none !important;
            transform: none !important;
            filter: none !important;
        }

        .light-beam {
            display: none;
        }
    }
</style>
@endpush

@section('content')
<div class="bg-[#f4f7f5]">

    <!-- Hero Section -->
    <div class="home-hero relative overflow-hidden text-white">

        <!-- LIVE BACKGROUND ELEMENTS -->
        <div class="animated-grid"></div>
        <div class="light-beam"></div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16 sm:py-24 relative z-10">

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-12" data-reveal-stagger>

                <!-- Left: Headline & Live Search -->
                <div class="flex-1 space-y-7">
                    <!-- Official logos above the badge -->


                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 rounded-full bg-emerald-500/10 border border-emerald-400/20 px-4 py-1.5 text-xs font-bold text-emerald-300 backdrop-blur-md">
                        <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Cebu City Government &bull; Official DMDP PESO Portal
                    </div>

                    <h1 class="text-4xl sm:text-5xl md:text-6xl font-black text-white leading-[1.1] tracking-tight drop-shadow-lg">
                        Department of Manpower
                        <span class="block bg-gradient-to-r from-emerald-300 via-teal-200 to-emerald-400 bg-clip-text text-transparent mt-1 pb-1">
                            Development &amp; Placement
                        </span>
                    </h1>

                    <p class="text-emerald-50/80 text-base sm:text-lg leading-relaxed max-w-xl drop-shadow-md">
                        <em class="font-semibold text-emerald-300">"Dugang Makat-unan, alang sa Disenting Panginabuhian"</em> — Bridging Cebuano jobseekers to verified careers through automated AI skill matching, free vocational training, and direct placement.
                    </p>

                    <!-- Instant Quick Job Search Bar -->
                    <form action="{{ route('jobs.index') }}" method="GET" class="p-2 bg-slate-950/50 border border-white/20 rounded-2xl shadow-2xl backdrop-blur-xl max-w-xl">
                        <div class="flex flex-col sm:flex-row gap-2">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-emerald-400/80">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" name="q" placeholder="Job title, technical skill, or company..."
                                    class="w-full pl-11 pr-4 py-3 bg-transparent border-none rounded-xl text-white placeholder-slate-300 text-sm focus:outline-none focus:ring-0" />
                            </div>
                            <button type="submit" class="home-action px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-sm rounded-xl transition-all shadow-lg shadow-emerald-500/30 shrink-0">
                                Search Jobs &rarr;
                            </button>
                        </div>
                    </form>

                    <!-- Trust Pill Badges -->
                    <div class="flex flex-wrap items-center gap-2.5 pt-2">
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-100 bg-white/10 border border-white/20 px-3 py-1.5 rounded-full backdrop-blur-md">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                            Verified DOLE Job Postings
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-100 bg-white/10 border border-white/20 px-3 py-1.5 rounded-full backdrop-blur-md">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                            100% Free Vocational Courses
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-100 bg-white/10 border border-white/20 px-3 py-1.5 rounded-full backdrop-blur-md">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                            PWD-Inclusive Opportunities
                        </span>
                    </div>
                </div>

                <!-- Right: Action Portal Card (Enhanced Depth) -->
                <div class="shrink-0 w-full lg:w-[380px]">
                    <div class="rounded-3xl border border-white/20 bg-slate-900/70 p-8 space-y-7 shadow-2xl shadow-black/50 backdrop-blur-2xl">

                        <!-- Joint Partnership Logos -->
                        <div class="flex items-center justify-center gap-4 pt-2">
                            <!-- Cebu Logo -->
                            <img src="{{ asset('images/cebu.png') }}"
                                alt="Cebu City Government Logo"
                                class="w-16 h-16 sm:w-20 sm:h-20 object-contain drop-shadow-lg" />

                            <!-- TrabaGo Logo (Center) -->
                            <div class="flex flex-col items-center">
                                <img src="{{ asset('images/logo.png') }}"
                                    alt="TrabaGo Logo"
                                    class="w-12 h-12 sm:w-14 sm:h-14 object-contain drop-shadow-lg" />
                                <span class="h-1 w-1 rounded-full bg-emerald-400 mt-2 opacity-50"></span>
                            </div>

                            <!-- DMDP Logo -->
                            <img src="{{ asset('images/dmdp.png') }}"
                                alt="DMDP Logo"
                                class="w-16 h-16 sm:w-20 sm:h-20 object-contain drop-shadow-lg" />
                        </div>

                        <!-- Text & Badges -->
                        <div class="text-center space-y-3">
                            <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest border-b border-white/10 pb-2 inline-block">
                                Official Partnership Portal
                            </p>
                            <h2 class="text-3xl font-black text-white tracking-tight mt-2">
                                Traba<span class="text-emerald-400">Go</span> Cebu City
                            </h2>
                            <p class="text-slate-300 text-xs leading-relaxed max-w-xs mx-auto">
                                AI Skill Matching &bull; Certified Vocational Training &bull; Direct Employer Referrals
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="border-t border-white/20 pt-6 space-y-3">
                            <a href="{{ route('jobseeker.register') }}"
                                class="home-action flex items-center justify-center gap-2 w-full px-5 py-3.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-sm rounded-xl transition-all shadow-lg shadow-emerald-500/30">
                                <span>Register as Jobseeker</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>

                            <a href="{{ route('employer.register') }}"
                                class="home-action flex items-center justify-center gap-2 w-full px-5 py-3 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-xs rounded-xl transition-all">
                                <span>Register as Employer Partner</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>

                            <div class="pt-3 text-center text-xs text-slate-300">
                                Already registered?
                                <a href="{{ route('login') }}" class="font-bold text-emerald-400 hover:text-emerald-300 hover:underline ml-1 transition-colors">Sign In to Account</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Live Statistics Counter Bar -->
    <div class="bg-[#eaf0ec] border-b border-slate-200/50" data-reveal-stagger>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center divide-x divide-slate-200/60">
                <div class="p-2">
                    <span class="block text-3xl font-black text-slate-800">80</span>
                    <span class="block text-xs font-bold text-slate-500 mt-1 uppercase tracking-wider">Cebu City Barangays</span>
                </div>
                <div class="p-2">
                    <span class="block text-3xl font-black text-emerald-600">100%</span>
                    <span class="block text-xs font-bold text-slate-500 mt-1 uppercase tracking-wider">Free Vocational Courses</span>
                </div>
                <div class="p-2">
                    <span class="block text-3xl font-black text-slate-800">DOLE</span>
                    <span class="block text-xs font-bold text-slate-500 mt-1 uppercase tracking-wider">Accredited Standards</span>
                </div>
                <div class="p-2">
                    <span class="block text-3xl font-black text-emerald-600">AI</span>
                    <span class="block text-xs font-bold text-slate-500 mt-1 uppercase tracking-wider">Skill-Matched Placements</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Official Mandate Section: Vision, Mission & Core Values -->
    <section class="py-20 md:py-28 bg-gradient-to-b from-[#f4f7f5] to-[#eef3ef]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">

            <div class="max-w-4xl mx-auto mb-16" data-reveal>
                <div class="flex items-center justify-center gap-6 sm:gap-10">

                    <!-- DMDP logo (left) -->
                    <img src="{{ asset('images/dmdp.png') }}"
                        alt="DMDP Logo"
                        class="shrink-0 w-20 h-20 sm:w-28 sm:h-28 md:w-32 md:h-32 object-contain drop-shadow-md" />

                    <!-- Center text -->
                    <div class="text-center space-y-4 max-w-xl">
                        <div class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100/50 border border-emerald-200/50 px-3.5 py-1 text-xs font-bold text-emerald-800 uppercase tracking-wider">
                            Cebu City Government &bull; DMDP Mandate
                        </div>
                        <h2 class="text-3xl font-black text-slate-900 tracking-tight sm:text-4xl">
                            Vision, Mission &amp; Core Values
                        </h2>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            The strategic governance framework of the Department of Manpower Development and Placement (DMDP) in fostering sustainable livelihood, equal opportunity, and vocational excellence.
                        </p>
                    </div>

                    <!-- Cebu City logo (right) -->
                    <img src="{{ asset('images/cebu.png') }}"
                        alt="Cebu City Government Logo"
                        class="shrink-0 w-20 h-20 sm:w-28 sm:h-28 md:w-32 md:h-32 object-contain drop-shadow-md" />

                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8" data-reveal-stagger>

                <!-- 1. Vision Card -->
                <div class="home-card rounded-3xl p-8 flex flex-col justify-between group">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-sm ring-1 ring-emerald-100 group-hover:scale-105 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800 bg-emerald-50 border border-emerald-100 px-3 py-1 rounded-full">
                                Vision
                            </span>
                        </div>

                        <div>
                            <h3 class="text-xl font-black text-slate-900 tracking-tight">DMDP PESO</h3>
                            <p class="text-xs font-bold text-emerald-600 mt-1">Public Employment Service Office</p>
                        </div>

                        <ul class="space-y-2 text-xs text-slate-600 font-medium">
                            <li class="flex items-center gap-2.5"><span class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-700 font-bold flex items-center justify-center text-[10px] shrink-0 border border-emerald-100/50">D</span><span><strong class="text-slate-800">D</strong>eveloped</span></li>
                            <li class="flex items-center gap-2.5"><span class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-700 font-bold flex items-center justify-center text-[10px] shrink-0 border border-emerald-100/50">M</span><span><strong class="text-slate-800">M</strong>eaningful</span></li>
                            <li class="flex items-center gap-2.5"><span class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-700 font-bold flex items-center justify-center text-[10px] shrink-0 border border-emerald-100/50">D</span><span><strong class="text-slate-800">D</strong>ynamic</span></li>
                            <li class="flex items-center gap-2.5"><span class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-700 font-bold flex items-center justify-center text-[10px] shrink-0 border border-emerald-100/50">P</span><span><strong class="text-slate-800">P</strong>rograms for</span></li>
                            <li class="flex items-center gap-2.5"><span class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-700 font-bold flex items-center justify-center text-[10px] shrink-0 border border-emerald-100/50">P</span><span><strong class="text-slate-800">P</strong>eople's</span></li>
                            <li class="flex items-center gap-2.5"><span class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-700 font-bold flex items-center justify-center text-[10px] shrink-0 border border-emerald-100/50">E</span><span><strong class="text-slate-800">E</strong>mployability</span></li>
                            <li class="flex items-center gap-2.5"><span class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-700 font-bold flex items-center justify-center text-[10px] shrink-0 border border-emerald-100/50">S</span><span><strong class="text-slate-800">S</strong>kills and</span></li>
                            <li class="flex items-center gap-2.5"><span class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-700 font-bold flex items-center justify-center text-[10px] shrink-0 border border-emerald-100/50">O</span><span><strong class="text-slate-800">O</strong>pportunities</span></li>
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
                <div class="home-card rounded-3xl p-8 flex flex-col justify-between group">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center shadow-sm ring-1 ring-teal-100 group-hover:scale-105 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9" stroke-width="2" />
                                    <circle cx="12" cy="12" r="5" stroke-width="2" />
                                    <circle cx="12" cy="12" r="1" stroke-width="2" fill="currentColor" />
                                </svg>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-teal-800 bg-teal-50 border border-teal-100 px-3 py-1 rounded-full">Mission</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900 tracking-tight">Advancing AcCESS</h3>
                            <p class="text-xs font-bold text-teal-600 mt-1">Strategic Milestone by 2025 &amp; Beyond</p>
                        </div>
                        <ul class="space-y-2 text-xs text-slate-600 font-medium">
                            <li class="flex items-center gap-2.5"><span class="w-7 h-6 rounded-md bg-teal-50 text-teal-700 font-bold flex items-center justify-center text-[10px] shrink-0 border border-teal-100/50">Ac</span><span><strong class="text-slate-800">Ac</strong>celerating</span></li>
                            <li class="flex items-center gap-2.5"><span class="w-7 h-6 rounded-md bg-teal-50 text-teal-700 font-bold flex items-center justify-center text-[10px] shrink-0 border border-teal-100/50">C</span><span><strong class="text-slate-800">C</strong>areer</span></li>
                            <li class="flex items-center gap-2.5"><span class="w-7 h-6 rounded-md bg-teal-50 text-teal-700 font-bold flex items-center justify-center text-[10px] shrink-0 border border-teal-100/50">E</span><span><strong class="text-slate-800">E</strong>ntrepreneurial and</span></li>
                            <li class="flex items-center gap-2.5"><span class="w-7 h-6 rounded-md bg-teal-50 text-teal-700 font-bold flex items-center justify-center text-[10px] shrink-0 border border-teal-100/50">S</span><span><strong class="text-slate-800">S</strong>kills development for</span></li>
                            <li class="flex items-center gap-2.5"><span class="w-7 h-6 rounded-md bg-teal-50 text-teal-700 font-bold flex items-center justify-center text-[10px] shrink-0 border border-teal-100/50">S</span><span><strong class="text-slate-800">S</strong>uccess</span></li>
                        </ul>
                    </div>
                    <div class="pt-6 border-t border-slate-100 mt-6">
                        <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                            <span class="h-1.5 w-1.5 rounded-full bg-teal-500"></span>
                            Expanding livelihood &amp; enterprise pathways
                        </div>
                    </div>
                </div>

                <!-- 3. Core Values Card -->
                <div class="home-card rounded-3xl p-8 flex flex-col justify-between group">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shadow-sm ring-1 ring-amber-100 group-hover:scale-105 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 bg-amber-50 border border-amber-100 px-3 py-1 rounded-full">Core Values</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900 tracking-tight">We EMPLOY</h3>
                            <p class="text-xs font-bold text-amber-600 mt-1">Principles Guiding Public Service</p>
                        </div>
                        <ul class="space-y-2 text-xs text-slate-600 font-medium">
                            <li class="flex items-center gap-2.5"><span class="w-6 h-6 rounded-md bg-amber-50 text-amber-700 font-bold flex items-center justify-center text-[10px] shrink-0 border border-amber-100/50">E</span><span><strong class="text-slate-800">E</strong>xcellence</span></li>
                            <li class="flex items-center gap-2.5"><span class="w-6 h-6 rounded-md bg-amber-50 text-amber-700 font-bold flex items-center justify-center text-[10px] shrink-0 border border-amber-100/50">M</span><span><strong class="text-slate-800">M</strong>ission-driven</span></li>
                            <li class="flex items-center gap-2.5"><span class="w-6 h-6 rounded-md bg-amber-50 text-amber-700 font-bold flex items-center justify-center text-[10px] shrink-0 border border-amber-100/50">P</span><span><strong class="text-slate-800">P</strong>eoples-oriented</span></li>
                            <li class="flex items-center gap-2.5"><span class="w-6 h-6 rounded-md bg-amber-50 text-amber-700 font-bold flex items-center justify-center text-[10px] shrink-0 border border-amber-100/50">L</span><span><strong class="text-slate-800">L</strong>imitless</span></li>
                            <li class="flex items-center gap-2.5"><span class="w-6 h-6 rounded-md bg-amber-50 text-amber-700 font-bold flex items-center justify-center text-[10px] shrink-0 border border-amber-100/50">O</span><span><strong class="text-slate-800">O</strong>pen-minded</span></li>
                            <li class="flex items-center gap-2.5"><span class="w-6 h-6 rounded-md bg-amber-50 text-amber-700 font-bold flex items-center justify-center text-[10px] shrink-0 border border-amber-100/50">Y</span><span><strong class="text-slate-800">Y</strong>ielding results</span></li>
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

    <!-- How TrabaGo Works & Core Services -->
    <section class="py-20 md:py-24 bg-[#eef3ef]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">

            <!-- Step Process -->
            <div class="text-center max-w-xl mx-auto mb-14" data-reveal>
                <span class="text-[11px] font-bold uppercase tracking-widest text-emerald-600 block mb-2">Simple 3-Step Process</span>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">How TrabaGo Connects You</h2>
                <p class="text-sm text-slate-500 mt-2">From registration to employment contract with Cebu City support</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-24" data-reveal-stagger>
                <div class="home-card p-8 rounded-3xl relative">
                    <span class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 font-black text-sm flex items-center justify-center mb-5 ring-1 ring-emerald-100">1</span>
                    <h3 class="text-base font-bold text-slate-900">Create Profile & Verify</h3>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">Register with your Cebu City residency details and complete your educational background and skills inventory.</p>
                </div>
                <div class="home-card p-8 rounded-3xl relative">
                    <span class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 font-black text-sm flex items-center justify-center mb-5 ring-1 ring-emerald-100">2</span>
                    <h3 class="text-base font-bold text-slate-900">AI Match & Skills Training</h3>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">Our engine computes skill compatibility with employer job orders while offering free courses for qualification gaps.</p>
                </div>
                <div class="home-card p-8 rounded-3xl relative">
                    <span class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 font-black text-sm flex items-center justify-center mb-5 ring-1 ring-emerald-100">3</span>
                    <h3 class="text-base font-bold text-slate-900">Referral & Placement</h3>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">Receive official DMDP referral notices, interview invitations, and secure sustainable placement with accredited firms.</p>
                </div>
            </div>

            <!-- Core Services -->
            <div class="text-center max-w-xl mx-auto mb-14" data-reveal>
                <span class="text-[11px] font-bold uppercase tracking-widest text-emerald-600 block mb-2">Our Public Services</span>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">Built for Cebuano Workers & Employers</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6" data-reveal-stagger>

                <div class="home-card p-8 rounded-3xl space-y-5 flex flex-col justify-between group">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center group-hover:bg-emerald-50 group-hover:text-emerald-600 transition-colors duration-300 ring-1 ring-slate-200/50 group-hover:ring-emerald-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Direct Job Placement</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Access accredited local and international job postings evaluated and verified by the Public Employment Service Office.</p>
                    </div>
                    <a href="{{ route('jobs.index') }}" class="text-xs font-bold text-slate-500 group-hover:text-emerald-600 inline-flex items-center gap-1.5 pt-2 transition-colors duration-300">
                        <span>Browse Verified Vacancies</span>
                        <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>

                <div class="home-card p-8 rounded-3xl space-y-5 flex flex-col justify-between group">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center group-hover:bg-emerald-50 group-hover:text-emerald-600 transition-colors duration-300 ring-1 ring-slate-200/50 group-hover:ring-emerald-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Vocational Skills Training</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Free competency certifications, laboratory modules, and industry certificates sponsored by Cebu City Hall.</p>
                    </div>
                    <a href="{{ route('jobseeker.register') }}" class="text-xs font-bold text-slate-500 group-hover:text-emerald-600 inline-flex items-center gap-1.5 pt-2 transition-colors duration-300">
                        <span>Enroll in Free Courses</span>
                        <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>

                <div class="home-card p-8 rounded-3xl space-y-5 flex flex-col justify-between group">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center group-hover:bg-emerald-50 group-hover:text-emerald-600 transition-colors duration-300 ring-1 ring-slate-200/50 group-hover:ring-emerald-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Employer Accreditation</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Verified business entities compliant with DOLE and Cebu City labor guidelines for fair and fast manpower recruitment.</p>
                    </div>
                    <a href="{{ route('employer.register') }}" class="text-xs font-bold text-slate-500 group-hover:text-emerald-600 inline-flex items-center gap-1.5 pt-2 transition-colors duration-300">
                        <span>Accredit Your Company</span>
                        <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- Bottom CTA Banner -->
    <div class="home-hero relative overflow-hidden py-20 px-4 sm:px-6 text-center text-white border-t border-white/10">

        <!-- LIVE BACKGROUND ELEMENTS FOR CTA -->
        <div class="animated-grid"></div>
        <div class="light-beam" style="animation-delay: 5s;"></div>

        <div class="max-w-2xl mx-auto space-y-6 relative z-10" data-reveal>
            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-bold bg-white/10 text-emerald-300 border border-white/20 backdrop-blur-md">
                Join the Cebuano Workforce
            </span>
            <h3 class="text-3xl sm:text-4xl md:text-5xl font-black text-white tracking-tight drop-shadow-md">
                Take the Next Step in Your Career Today
            </h3>
            <p class="text-base text-emerald-100/80 leading-relaxed max-w-lg mx-auto drop-shadow-sm">
                Connect directly with accredited employers, get AI skill-matched recommendations, and enroll in certified vocational programs for free.
            </p>
            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('jobs.index') }}"
                    class="home-action w-full sm:w-auto px-8 py-4 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-sm rounded-xl transition-all shadow-xl shadow-emerald-500/30">
                    Explore Job Vacancies &rarr;
                </a>
                <a href="{{ route('register') }}"
                    class="home-action w-full sm:w-auto px-8 py-4 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-sm rounded-xl backdrop-blur-md transition-all">
                    Create Free Account
                </a>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    (() => {
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (prefersReducedMotion || !('IntersectionObserver' in window)) {
            return;
        }

        const staggerGroups = document.querySelectorAll('[data-reveal-stagger]');
        staggerGroups.forEach((group) => {
            Array.from(group.children).forEach((item, index) => {
                item.dataset.reveal = '';
                item.style.setProperty('--reveal-delay', `${Math.min(index * 120, 360)}ms`);
            });
        });

        const revealItems = document.querySelectorAll('[data-reveal]');
        if (!revealItems.length) return;

        document.documentElement.classList.add('has-reveal');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                } else {
                    entry.target.classList.remove('is-visible');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: "0px 0px -50px 0px"
        });

        revealItems.forEach((item) => observer.observe(item));
    })();
</script>
@endpush