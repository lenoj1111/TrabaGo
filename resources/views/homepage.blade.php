@extends('layouts.public')

@section('title', 'DMDP Cebu City - Department of Manpower Development and Placement')

@section('content')
<div class="bg-white">
    <!-- Breadcrumb -->
    <div class="bg-emerald-50/60 border-b border-emerald-100">
        <div class="max-w-6xl mx-auto px-5 py-3">
            <nav class="text-xs font-semibold">
                <ol class="flex items-center gap-2 text-slate-600">
                    <li><a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Home</a></li>
                    <li><span class="text-slate-300">/</span></li>
                    <li><a href="#" class="hover:text-emerald-700 transition-colors">Offices</a></li>
                    <li><span class="text-slate-300">/</span></li>
                    <li class="text-emerald-800 font-bold">Department of Manpower Development and Placement (DMDP)</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Hero Section with TrabaGo Green Palette -->
    <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #022c22 0%, #064e3b 50%, #047857 100%);">
        <!-- Ambient light circles -->
        <div class="absolute -right-20 -top-20 h-96 w-96 rounded-full bg-emerald-400/15 blur-3xl"></div>
        <div class="absolute -bottom-20 left-40 h-96 w-96 rounded-full bg-teal-400/15 blur-3xl"></div>

        <!-- TrabaGo Floating Badge - Top Right -->
        <div class="absolute top-8 right-8 z-10 hidden md:block">
            <div class="bg-white/10 backdrop-blur-md border border-emerald-400/30 rounded-2xl px-6 py-3 shadow-2xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <svg class="w-5 h-5 text-slate-950 font-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-emerald-300 text-[10px] font-bold tracking-widest uppercase">POWERED BY</p>
                        <p class="text-white font-extrabold text-xl tracking-tight leading-none mt-0.5">Traba<span class="text-emerald-400">Go</span></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="relative max-w-6xl mx-auto px-5 py-16 md:py-24">
            <!-- Official Seal Badge -->
            <div class="inline-flex items-center gap-3 bg-white/10 backdrop-blur px-5 py-2.5 rounded-full mb-6 border border-emerald-400/30">
                <div class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center shadow-md">
                    <svg class="w-4 h-4 text-slate-950" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0h8v12H6V4z" clip-rule="evenodd"/>
                        <path d="M8 6h4v2H8V6zm0 4h4v2H8v-2z"/>
                    </svg>
                </div>
                <span class="text-white font-bold text-xs tracking-wider">OFFICIAL PORTAL</span>
                <span class="w-px h-4 bg-white/20"></span>
                <span class="text-emerald-300 text-xs font-bold">DMDP CEBU CITY</span>
            </div>
            
            <!-- Main Title with TrabaGo Highlight -->
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-10">
                <!-- Left Side - Title -->
                <div class="flex-1 space-y-4">
                    <h1 class="text-4xl md:text-6xl font-black mb-4 leading-tight text-white tracking-tight">
                        Department of Manpower
                        <span class="block text-3xl md:text-5xl mt-2 text-emerald-300">Development & Placement</span>
                        <span class="block text-2xl md:text-3xl mt-1 text-white/80">(DMDP Cebu City)</span>
                    </h1>
                    
                    <!-- Tagline with decorative border -->
                    <div class="relative inline-block mt-4 pl-4 border-l-4 border-emerald-400">
                        <p class="text-xl md:text-2xl text-emerald-100 font-medium italic">
                            "Dugang Makat-unan, alang sa Disenting Panginabuhian"
                        </p>
                    </div>
                </div>

                <!-- Right Side - TrabaGo Card with Register Buttons -->
                <div class="shrink-0 w-full lg:w-[360px]">
                    <div class="relative bg-white/10 backdrop-blur-xl border border-emerald-400/30 rounded-3xl p-6 md:p-8 shadow-2xl text-center space-y-5">
                        <!-- Large TrabaGo Icon -->
                        <div class="w-20 h-20 rounded-3xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center mx-auto shadow-xl shadow-emerald-500/40">
                            <svg class="w-10 h-10 text-slate-950 font-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        
                        <div>
                            <div class="text-emerald-300 text-xs font-extrabold tracking-widest uppercase">Empowering Cebuanos Through</div>
                            <div class="text-3xl md:text-4xl font-black text-white mt-1">
                                Traba<span class="text-emerald-400">Go</span>
                            </div>
                            <p class="text-slate-300 text-xs mt-2 leading-relaxed">
                                AI Cosine-Similarity Skill Matching, Vocational Training & Direct Employment
                            </p>
                        </div>

                        <!-- Divider -->
                        <div class="border-t border-white/10 my-4"></div>

                        <!-- Register Buttons in Green Palette -->
                        <div class="space-y-3">
                            <a href="{{ route('jobseeker.register') }}" 
                               class="flex items-center justify-center gap-2 w-full px-5 py-3.5 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-extrabold text-sm rounded-2xl transition-all shadow-lg shadow-emerald-600/30 hover:scale-[1.02]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Register as Jobseeker
                            </a>
                            
                            <a href="{{ route('employer.register') }}" 
                               class="flex items-center justify-center gap-2 w-full px-5 py-3 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-2xl transition-all border border-emerald-400/30">
                                Register as Employer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bottom Info Badges -->
            <div class="flex flex-wrap items-center gap-3 text-xs mt-10">
                <span class="inline-flex items-center gap-1.5 bg-white/10 backdrop-blur px-3.5 py-1.5 rounded-full border border-emerald-400/20 text-emerald-200">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    Verified Cebu City Job Listings
                </span>
                <span class="inline-flex items-center gap-1.5 bg-white/10 backdrop-blur px-3.5 py-1.5 rounded-full border border-emerald-400/20 text-emerald-200">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    Free Vocational Skill Assessment
                </span>
                <span class="inline-flex items-center gap-1.5 bg-white/10 backdrop-blur px-3.5 py-1.5 rounded-full border border-emerald-400/20 text-emerald-200">
                    <span class="h-2 w-2 rounded-full bg-teal-400"></span>
                    ♿ PWD-Inclusive Career Matching
                </span>
            </div>
        </div>
    </div>

    <!-- Bottom CTA Banner in Emerald Theme -->
    <div class="relative overflow-hidden py-16 px-5 text-center" style="background: linear-gradient(135deg, #022c22 0%, #064e3b 100%);">
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="inline-flex items-center gap-2 bg-emerald-500/20 border border-emerald-400/30 px-4 py-1.5 rounded-full text-xs font-bold text-emerald-300">
                Powered by TrabaGo Engine
            </div>
            <h3 class="text-3xl sm:text-4xl font-black text-white">Be Part of the Cebu City Workforce</h3>
            <p class="text-sm text-emerald-100/80 max-w-xl mx-auto leading-relaxed">
                Connect directly with accredited Cebu employers and boost your employability through free skills certifications.
            </p>
            <div class="flex flex-wrap justify-center gap-4 pt-2">
                <a href="{{ route('jobseeker.register') }}" 
                   class="inline-flex items-center gap-2 px-8 py-3.5 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-black text-sm rounded-2xl shadow-xl shadow-emerald-600/30 transition-all hover:scale-105">
                    Start Matching Jobs &rarr;
                </a>
                <a href="{{ route('login') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3.5 bg-white/10 hover:bg-white/20 text-white font-bold text-sm rounded-2xl border border-white/20">
                    Existing User Login
                </a>
            </div>
        </div>
    </div>
</div>
@endsection