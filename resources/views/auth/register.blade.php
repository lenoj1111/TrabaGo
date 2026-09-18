@extends('layouts.public')

@section('title', 'Register - DMDP Cebu City | TrabaGo')

@section('content')
<div class="min-h-screen bg-gray-50 py-10 md:py-16">
    <div class="max-w-4xl mx-auto px-5">

        <!-- Page Header -->
        <div class="text-center max-w-xl mx-auto mb-10">
            <p class="text-xs font-semibold text-green-600 uppercase tracking-widest mb-2">Create Your Account</p>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">
                Join <span class="text-green-600">TrabaGo</span>
            </h1>
            <p class="text-gray-500 text-sm mt-2 leading-relaxed">
                Select your account type to access verified jobs, free vocational training, or enterprise workforce recruitment.
            </p>
        </div>

        <!-- Two Role Selection Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
            
            <!-- Card 1: Jobseeker -->
            <div class="bg-white border border-gray-200 hover:border-green-500 rounded-2xl p-7 sm:p-8 transition-colors flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-5">
                        <div class="w-12 h-12 rounded-xl bg-green-600 flex items-center justify-center text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-green-700 bg-green-50 px-2.5 py-1 rounded-full">For Individuals</span>
                    </div>

                    <p class="text-xs font-semibold uppercase tracking-wider text-green-600 mb-1">Careers & Training</p>
                    <h2 class="text-xl font-extrabold text-gray-900">Jobseeker / Trainee</h2>
                    <p class="text-gray-500 text-sm mt-2 leading-relaxed">
                        Looking for jobs in Cebu City, AI career matching, or free DMDP-certified vocational training.
                    </p>

                    <div class="border-t border-gray-100 my-5"></div>

                    <ul class="space-y-2.5 text-sm text-gray-600">
                        <li class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-green-600 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>AI Skill Matching & tailored job recommendations</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-green-600 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Free accredited vocational training courses</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-green-600 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Direct employer referrals & fast-track placement</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-green-600 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Priority matching for PWD, 4Ps, and youth</span>
                        </li>
                    </ul>
                </div>

                <div class="pt-7">
                    <a href="{{ route('jobseeker.register') }}" 
                       class="flex items-center justify-center w-full py-3 px-6 rounded-xl bg-green-600 hover:bg-green-700 text-white font-semibold text-sm transition-colors">
                        Register as Jobseeker →
                    </a>
                </div>
            </div>

            <!-- Card 2: Employer -->
            <div class="bg-white border border-gray-200 hover:border-green-500 rounded-2xl p-7 sm:p-8 transition-colors flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-5">
                        <div class="w-12 h-12 rounded-xl bg-green-700 flex items-center justify-center text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-green-700 bg-green-50 px-2.5 py-1 rounded-full">For Businesses</span>
                    </div>

                    <p class="text-xs font-semibold uppercase tracking-wider text-green-600 mb-1">Recruitment & Accreditation</p>
                    <h2 class="text-xl font-extrabold text-gray-900">Employer / Partner</h2>
                    <p class="text-gray-500 text-sm mt-2 leading-relaxed">
                        Hiring qualified manpower, posting vacancies, or seeking DMDP employer partner accreditation.
                    </p>

                    <div class="border-t border-gray-100 my-5"></div>

                    <ul class="space-y-2.5 text-sm text-gray-600">
                        <li class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-green-600 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Free vacancy posting to verified jobseekers</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-green-600 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>City Hall accreditation & partner status</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-green-600 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Access to certified & pre-screened talent</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-green-600 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Digital hiring pipeline & interview coordination</span>
                        </li>
                    </ul>
                </div>

                <div class="pt-7">
                    <a href="{{ route('employer.register') }}" 
                       class="flex items-center justify-center w-full py-3 px-6 rounded-xl bg-green-700 hover:bg-green-800 text-white font-semibold text-sm transition-colors">
                        Register as Employer →
                    </a>
                </div>
            </div>

        </div>

        <!-- Sign-in link -->
        <div class="text-center">
            <p class="text-sm text-gray-500">
                Already have an account? 
                <a href="{{ route('login') }}" class="font-semibold text-green-600 hover:text-green-700">
                    Sign in →
                </a>
            </p>
        </div>

    </div>
</div>
@endsection
