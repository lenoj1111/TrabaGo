@extends('layouts.public')

@section('title', 'Employer Registration Submitted - DMDP Cebu City | TrabaGo')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-slate-50 via-emerald-50/20 to-slate-50 py-12 md:py-20">
    <div class="max-w-2xl mx-auto px-4 sm:px-6">

        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xl shadow-teal-950/5 p-8 sm:p-12 text-center">
            
            <!-- Animated Success Icon -->
            <div class="relative w-20 h-20 mx-auto mb-6">
                <div class="absolute inset-0 rounded-full bg-teal-100 animate-ping opacity-25"></div>
                <div class="relative w-20 h-20 rounded-2xl bg-gradient-to-br from-teal-600 to-slate-900 flex items-center justify-center text-white shadow-lg shadow-teal-700/30">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>

            <!-- Title & Subtitle -->
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-teal-100 text-teal-800 border border-teal-200 mb-3">
                <span class="w-1.5 h-1.5 rounded-full bg-teal-600"></span>
                Account Created Successfully
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Employer Account Ready!
            </h1>
            <p class="text-slate-600 text-sm sm:text-base mt-2 max-w-lg mx-auto leading-relaxed">
                Thank you for registering with DMDP Cebu City. You can sign in immediately to configure your corporate profile and explore employer services.
            </p>

            <!-- What Happens Next Timeline -->
            <div class="mt-8 text-left bg-slate-50/80 rounded-2xl p-6 border border-slate-200/70 space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Account Access & Job Posting Requirements:</h3>
                
                <div class="space-y-3.5">
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-teal-700 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            1
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Sign In & Complete Company Profile</span>
                            <span class="text-xs text-slate-500 leading-normal">You have immediate access to your employer dashboard and company profile settings. No waiting period required for initial setup.</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            2
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Submit Business Documents for Accreditation</span>
                            <span class="text-xs text-slate-500 leading-normal">To unlock publishing job vacancies, upload your business credentials (BIR 2303, Mayor's Permit, SEC/DTI) in your Accreditation section.</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            3
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Admin/JPO Verification & Job Posting</span>
                            <span class="text-xs text-slate-500 leading-normal">Once evaluated and approved by DMDP officers, your account is verified and job posting will be fully unlocked.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-7 py-3 rounded-xl bg-teal-800 hover:bg-teal-900 text-white font-bold text-sm transition shadow-lg shadow-teal-900/20 active:scale-[0.98]">
                    Go to Account Login &rarr;
                </a>
                <a href="{{ route('home') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm transition active:scale-[0.98]">
                    Return to Portal Home
                </a>
            </div>

            <!-- Support Footer -->
            <p class="text-xs text-slate-400 mt-8">
                For expedited verification or corporate partnership inquiries, email <a href="mailto:dmdp@cebucity.gov.ph" class="text-teal-700 hover:underline">dmdp@cebucity.gov.ph</a> or call (032) 888-1234.
            </p>
        </div>

    </div>
</div>
@endsection