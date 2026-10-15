@extends('layouts.public')

@section('title', 'Select Account Type - DMDP Cebu City | TrabaGo')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-slate-50 via-emerald-50/20 to-slate-50 py-10 md:py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">

        <!-- Page Header -->
        <div class="text-center max-w-2xl mx-auto mb-10 md:mb-12">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200 mb-3 shadow-xs">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Official Cebu City Manpower Portal
            </span>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                Join <span class="text-emerald-600">TrabaGo</span> Cebu City
            </h1>
            <p class="text-slate-600 text-sm sm:text-base mt-3 leading-relaxed">
                Connect with verified local employers, unlock DMDP-certified vocational training courses, or hire accredited manpower backed by the Cebu City Government.
            </p>
        </div>

        <!-- Two Role Selection Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 mb-12">
            
            <!-- Card 1: Jobseeker -->
            <div class="relative group bg-white border border-slate-200/90 hover:border-emerald-500 rounded-3xl p-7 sm:p-9 transition-all duration-300 hover:shadow-xl hover:shadow-emerald-950/5 hover:-translate-y-1 flex flex-col justify-between">
                <!-- Recommended badge -->
                <div class="absolute -top-3 left-6">
                    <span class="px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-emerald-600 text-white shadow-sm shadow-emerald-600/30">
                        For Individuals
                    </span>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-6 pt-2">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white shadow-md shadow-emerald-600/25 group-hover:scale-105 transition-transform">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-emerald-800 bg-emerald-50 border border-emerald-200/60 px-3 py-1 rounded-full">
                            Free Registration
                        </span>
                    </div>

                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-600 mb-1">Careers & Training</p>
                    <h2 class="text-2xl font-black text-slate-900 group-hover:text-emerald-700 transition-colors">Jobseeker / Trainee</h2>
                    <p class="text-slate-600 text-sm mt-2 leading-relaxed">
                        Looking for verified jobs in Cebu City, automated AI career recommendations, or free DMDP-certified vocational training courses.
                    </p>

                    <div class="border-t border-slate-100 my-6"></div>

                    <ul class="space-y-3 text-sm text-slate-700">
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                            <span class="text-xs sm:text-sm font-medium">AI Skill Matching & tailored job opportunities</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                            <span class="text-xs sm:text-sm font-medium">Free accredited vocational training & certificates</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                            <span class="text-xs sm:text-sm font-medium">Direct employer referrals & fast-track placement</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                            <span class="text-xs sm:text-sm font-medium">Priority placement for PWD, 4Ps, and youth</span>
                        </li>
                    </ul>
                </div>

                <div class="pt-8">
                    <a href="{{ route('jobseeker.register') }}" 
                       class="flex items-center justify-center gap-2 w-full py-3.5 px-6 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm transition-all duration-200 shadow-lg shadow-emerald-600/25 group-hover:scale-[1.02]">
                        <span>Register as Jobseeker</span>
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>

            <!-- Card 2: Employer -->
            <div class="relative group bg-white border border-slate-200/90 hover:border-teal-600 rounded-3xl p-7 sm:p-9 transition-all duration-300 hover:shadow-xl hover:shadow-teal-950/5 hover:-translate-y-1 flex flex-col justify-between">
                <!-- Badge -->
                <div class="absolute -top-3 left-6">
                    <span class="px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-teal-800 text-white shadow-sm shadow-teal-800/30">
                        For Businesses
                    </span>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-6 pt-2">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-teal-600 to-slate-900 flex items-center justify-center text-white shadow-md shadow-teal-700/25 group-hover:scale-105 transition-transform">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-teal-800 bg-teal-50 border border-teal-200/60 px-3 py-1 rounded-full">
                            Accreditation Track
                        </span>
                    </div>

                    <p class="text-xs font-bold uppercase tracking-wider text-teal-700 mb-1">Recruitment & Accreditation</p>
                    <h2 class="text-2xl font-black text-slate-900 group-hover:text-teal-800 transition-colors">Employer / Partner</h2>
                    <p class="text-slate-600 text-sm mt-2 leading-relaxed">
                        Hiring qualified manpower, posting job vacancies, or seeking official DMDP Cebu City employer accreditation.
                    </p>

                    <div class="border-t border-slate-100 my-6"></div>

                    <ul class="space-y-3 text-sm text-slate-700">
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                            <span class="text-xs sm:text-sm font-medium">Free vacancy posting to thousands of active candidates</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                            <span class="text-xs sm:text-sm font-medium">City Hall employer accreditation & verified badge</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                            <span class="text-xs sm:text-sm font-medium">Pre-screened & certified talent from DMDP training</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                            <span class="text-xs sm:text-sm font-medium">Special recruitment activity & barangay job fair access</span>
                        </li>
                    </ul>
                </div>

                <div class="pt-8">
                    <a href="{{ route('employer.register') }}" 
                       class="flex items-center justify-center gap-2 w-full py-3.5 px-6 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm transition-all duration-200 shadow-lg shadow-slate-900/20 group-hover:scale-[1.02]">
                        <span>Register as Employer</span>
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>

        </div>

        <!-- Sign-in link & Security Note -->
        <div class="text-center space-y-3">
            <p class="text-sm text-slate-600">
                Already registered with TrabaGo? 
                <a href="{{ route('login') }}" class="font-bold text-emerald-600 hover:text-emerald-700 hover:underline">
                    Sign in to your account &rarr;
                </a>
            </p>
            <p class="text-xs text-slate-400">
                Data protected under Republic Act No. 10173 (Data Privacy Act of 2012) · Cebu City Government
            </p>
        </div>

    </div>
</div>
@endsection
