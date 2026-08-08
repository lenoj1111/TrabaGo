@extends('layouts.public')

@section('title', 'DMDP Cebu City - Department of Manpower Development and Placement')

@section('content')
<div class="bg-white">
    <!-- Breadcrumb -->
    <div class="bg-brand-50/50 border-b border-brand-100">
        <div class="max-w-6xl mx-auto px-5 py-3">
            <nav class="text-sm">
                <ol class="flex items-center gap-2 text-brand-700">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-900 transition-colors">Home</a></li>
                    <li><span class="text-brand-300">/</span></li>
                    <li><a href="#" class="hover:text-brand-900 transition-colors">Offices</a></li>
                    <li><span class="text-brand-300">/</span></li>
                    <li class="text-brand-900 font-semibold">Department of Manpower Development and Placement (DMDP)</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Hero Section with TrabaGo Highlight -->
<div class="relative overflow-hidden" style="background: linear-gradient(135deg, #1b2739 0%, #33455e 50%, #405673 100%);">
    <!-- Decorative elements -->
    <div class="absolute inset-0 opacity-5">
        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
            <pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse">
                <circle cx="2" cy="2" r="1" fill="white" />
            </pattern>
            <rect width="100" height="100" fill="url(#grid)" />
        </svg>
    </div>
    
    <!-- Philippine Flag Colors Stripes -->
    <div class="absolute top-0 left-0 w-full h-1.5 flex">
        <div class="w-1/3 h-full" style="background: #1b2739;"></div>
        <div class="w-1/3 h-full" style="background: #b3894a;"></div>
        <div class="w-1/3 h-full" style="background: #ce1126;"></div>
    </div>

    <!-- Sun Rays Decoration -->
    <div class="absolute right-0 bottom-0 opacity-10">
        <svg width="300" height="300" viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="40" fill="none" stroke="#b3894a" stroke-width="2"/>
            <circle cx="50" cy="50" r="30" fill="none" stroke="#b3894a" stroke-width="2"/>
            <circle cx="50" cy="50" r="20" fill="none" stroke="#b3894a" stroke-width="2"/>
            <circle cx="50" cy="50" r="10" fill="none" stroke="#b3894a" stroke-width="2"/>
            <line x1="50" y1="5" x2="50" y2="15" stroke="#b3894a" stroke-width="2"/>
            <line x1="50" y1="85" x2="50" y2="95" stroke="#b3894a" stroke-width="2"/>
            <line x1="5" y1="50" x2="15" y2="50" stroke="#b3894a" stroke-width="2"/>
            <line x1="85" y1="50" x2="95" y2="50" stroke="#b3894a" stroke-width="2"/>
            <line x1="18" y1="18" x2="25" y2="25" stroke="#b3894a" stroke-width="2"/>
            <line x1="75" y1="75" x2="82" y2="82" stroke="#b3894a" stroke-width="2"/>
            <line x1="18" y1="82" x2="25" y2="75" stroke="#b3894a" stroke-width="2"/>
            <line x1="75" y1="18" x2="82" y2="25" stroke="#b3894a" stroke-width="2"/>
        </svg>
    </div>

    <!-- TrabaGo Floating Badge - Top Right -->
    <div class="absolute top-8 right-8 z-10 hidden md:block">
        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-6 py-3 shadow-2xl">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gold-500 flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white text-xs font-medium tracking-wider">POWERED BY</p>
                    <p class="text-gold-400 font-bold text-xl tracking-tight">Traba<span class="text-white">Go</span></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="relative max-w-6xl mx-auto px-5 py-16 md:py-20">
        <!-- Official Seal Badge -->
        <div class="inline-flex items-center gap-3 bg-white/10 backdrop-blur px-5 py-2.5 rounded-full mb-6 border border-white/20">
            <div class="w-10 h-10 rounded-full bg-gold-500 flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0h8v12H6V4z" clip-rule="evenodd"/>
                    <path d="M8 6h4v2H8V6zm0 4h4v2H8v-2z"/>
                </svg>
            </div>
            <span class="text-white font-medium text-sm tracking-wider">OFFICIAL WEBSITE</span>
            <span class="w-px h-6 bg-white/20"></span>
            <span class="text-gold-300 text-sm font-semibold">DMDP CEBU CITY</span>
            <span class="w-px h-6 bg-white/20"></span>
            <span class="text-white/60 text-xs">Powered by</span>
            <span class="text-gold-400 font-bold text-sm">Traba<span class="text-white">Go</span></span>
        </div>
        
        <!-- Main Title with TrabaGo Highlight -->
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-8">
            <!-- Left Side - Title -->
            <div class="flex-1">
                <h1 class="text-4xl md:text-6xl font-bold mb-4 leading-tight text-white">
                    Department of Manpower
                    <span class="block text-3xl md:text-5xl mt-2" style="color: #b3894a;">Development and Placement</span>
                    <span class="block text-2xl md:text-3xl mt-1 text-white/80">(DMDP)</span>
                </h1>
                
                <!-- Tagline with decorative border -->
                <div class="relative inline-block mt-6">
                    <div class="absolute -left-4 top-1/2 -translate-y-1/2 w-1 h-12" style="background: #b3894a;"></div>
                    <p class="text-xl md:text-2xl text-white/90 pl-6 font-light italic">
                        "Dugang Makat-unan, alang sa Disenting Panginabuhian"
                    </p>
                </div>
            </div>

            <!-- Right Side - TrabaGo Box with Register Buttons -->
            <div class="lg:ml-8 shrink-0 w-full lg:w-[340px]">
                <div class="relative group">
                    <!-- Glow effect -->
                    <div class="absolute -inset-1 bg-gradient-to-r from-gold-500/20 to-gold-400/20 rounded-2xl blur-xl group-hover:blur-2xl transition duration-500"></div>
                    
                    <div class="relative bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 md:p-8 shadow-2xl hover:shadow-gold-500/20 transition-all duration-300 hover:-translate-y-1">
                        <div class="flex flex-col items-center text-center">
                            <!-- Large TrabaGo Logo -->
                            <div class="w-20 h-20 rounded-2xl bg-gold-500 flex items-center justify-center mb-4 shadow-lg shadow-gold-500/30">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            
                            <div class="text-white/60 text-xs font-medium tracking-widest uppercase mb-1">Empowering Cebuanos Through</div>
                            
                            <div class="text-4xl md:text-5xl font-extrabold tracking-tight">
                                <span class="text-gold-400">Traba</span><span class="text-white">Go</span>
                            </div>
                            
                            <div class="w-16 h-0.5 bg-gold-500/50 my-3"></div>
                            
                            <p class="text-white/80 text-sm font-light max-w-xs">
                                Your Gateway to Decent Employment and Skills Development
                            </p>
                            
                            <div class="mt-4 flex items-center gap-2">
                                <span class="px-3 py-1 bg-gold-500/20 border border-gold-500/30 rounded-full text-gold-300 text-xs font-semibold">
                                    DMDP x TrabaGo
                                </span>
                                <span class="px-3 py-1 bg-white/10 border border-white/10 rounded-full text-white/60 text-xs">
                                    Cebu City
                                </span>
                            </div>

                            <!-- Divider -->
                            <div class="w-full border-t border-white/10 my-4"></div>

                            <!-- REGISTER BUTTONS - Under TrabaGo Box -->
                            <div class="w-full space-y-3">
                                <p class="text-white/70 text-sm font-medium">Register now as:</p>
                                
                                <a href="{{ route('jobseeker.register') }}" 
                                   class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-gold-500 hover:bg-gold-600 text-white font-semibold rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Job Seeker
                                </a>
                                
                                <a href="{{ route('employer.register') }}" 
                                   class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-lg transition-all duration-300 border border-white/20 hover:border-white/40 hover:-translate-y-0.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    Employer
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Info Badges -->
        <div class="flex flex-wrap items-center gap-4 text-sm mt-8">
            <span class="inline-flex items-center gap-2 bg-white/10 backdrop-blur px-4 py-2 rounded-full border border-white/10">
                <svg class="w-4 h-4 text-gold-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                <span class="text-white/80">Published: January 10, 2024</span>
            </span>
            <span class="inline-flex items-center gap-2 bg-white/10 backdrop-blur px-4 py-2 rounded-full border border-white/10">
                <svg class="w-4 h-4 text-gold-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <span class="text-white/80">Updated: March 15, 2024</span>
            </span>
            <span class="inline-flex items-center gap-2 bg-gold-500/20 backdrop-blur px-4 py-2 rounded-full border border-gold-500/30">
                <svg class="w-4 h-4 text-gold-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/>
                </svg>
                <span class="text-gold-300 font-medium">TrabaGo Platform</span>
            </span>
        </div>
    </div>
</div>
    <!-- Rest of your content remains the same -->
    <!-- ... About Section, Vision/Mission, Services, etc. ... -->
    
    <!-- Just make sure to add the TrabaGo mention in the footer CTA -->
    <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #1b2739 0%, #33455e 100%);">
        <div class="absolute inset-0 opacity-5">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <pattern id="grid3" width="20" height="20" patternUnits="userSpaceOnUse">
                    <circle cx="2" cy="2" r="1" fill="white" />
                </pattern>
                <rect width="100" height="100" fill="url(#grid3)" />
            </svg>
        </div>
        <div class="relative max-w-6xl mx-auto px-5 py-12 text-center">
            <div class="inline-flex items-center gap-3 bg-white/10 backdrop-blur px-4 py-2 rounded-full mb-4 border border-white/10">
                <span class="text-white/60 text-xs">Powered by</span>
                <span class="text-gold-400 font-bold text-lg">Traba<span class="text-white">Go</span></span>
            </div>
            <h3 class="text-2xl md:text-3xl font-bold text-white mb-3">Be Part of Our Workforce</h3>
            <p class="text-brand-300 max-w-2xl mx-auto mb-6">
                Take the first step towards a brighter future through <strong class="text-gold-400">TrabaGo</strong>. 
                Register now and access opportunities for skills development and meaningful employment.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('jobseeker.register') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-gold-500 hover:bg-gold-600 text-white font-semibold rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl">
                    Register as Job Seeker
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="{{ route('employer.register') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-lg transition-all duration-300 border border-white/20">
                    Register as Employer
                </a>
            </div>
            <p class="text-brand-400 text-xs mt-4">
                <span class="text-gold-400">TrabaGo</span> — Dugang Makat-unan, alang sa Disenting Panginabuhian
            </p>
        </div>
    </div>
</div>
@endsection