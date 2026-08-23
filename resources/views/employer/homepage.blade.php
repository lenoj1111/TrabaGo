@extends('layouts.employer')

@section('title', 'Employer Dashboard - TrabaGo')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    <!-- Hero Banner with Deep Emerald Forest Gradient -->
    <div class="relative overflow-hidden rounded-3xl p-6 sm:p-10 text-white shadow-2xl" style="background: linear-gradient(135deg, #022c22 0%, #064e3b 50%, #047857 100%);">
        <div class="absolute -right-16 -bottom-16 w-64 h-64 rounded-full bg-emerald-400/10 blur-2xl"></div>
        <div class="absolute -left-16 -top-16 w-64 h-64 rounded-full bg-teal-400/10 blur-2xl"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white/10 backdrop-blur-md border border-emerald-400/30 text-xs font-black text-emerald-300">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    DMDP Verified Employer Hub
                </div>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-white">
                    Welcome to TrabaGo Employer Portal
                </h1>
                <p class="text-sm text-emerald-100/80 max-w-xl leading-relaxed">
                    Publish job vacancies, discover skill-certified candidates via AI cosine-matching, and manage applicant pipelines with DMDP Cebu City.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('employer.job-postings') }}" class="px-5 py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-black text-xs shadow-lg shadow-emerald-500/25 transition-all hover:scale-105">
                    + Post New Job Vacancy
                </a>
                <a href="{{ route('employer.profile') }}" class="px-4 py-3 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 transition-all">
                    Edit Company Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Metric Cards in Green Palette -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Active Jobs -->
        <div class="bg-white rounded-3xl p-6 border border-emerald-100/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Active Job Postings</span>
                <div class="h-10 w-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black">
                    💼
                </div>
            </div>
            <div class="mt-4">
                <p class="text-3xl font-black text-slate-900">{{ $approvedJobs }}</p>
                <span class="text-[11px] font-semibold text-emerald-700 mt-1 block">{{ $totalJobs }} total postings ({{ $pendingJobs }} pending review)</span>
            </div>
        </div>

        <!-- Total Applicants -->
        <div class="bg-white rounded-3xl p-6 border border-emerald-100/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Applicants</span>
                <div class="h-10 w-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center font-black">
                    👥
                </div>
            </div>
            <div class="mt-4">
                <p class="text-3xl font-black text-slate-900">{{ $totalApplicants }}</p>
                <span class="text-[11px] font-semibold text-teal-700 mt-1 block">{{ $hiredCount }} candidates hired</span>
            </div>
        </div>

        <!-- Referred Candidates -->
        <div class="bg-white rounded-3xl p-6 border border-emerald-100/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">JPO Referrals</span>
                <div class="h-10 w-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black">
                    ⭐
                </div>
            </div>
            <div class="mt-4">
                <p class="text-3xl font-black text-slate-900">{{ $referredCount }}</p>
                <span class="text-[11px] font-semibold text-emerald-700 mt-1 block">Endorsed by Placement Officer</span>
            </div>
        </div>

        <!-- Accreditation Status -->
        <div class="bg-white rounded-3xl p-6 border border-emerald-100/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Accreditation</span>
                <div class="h-10 w-10 rounded-2xl {{ ($employer->is_accredited || ($accreditation && $accreditation->status === 'admin_approved')) ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} flex items-center justify-center font-black">
                    🏛️
                </div>
            </div>
            <div class="mt-4">
                @if($employer->is_accredited || ($accreditation && $accreditation->status === 'admin_approved'))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 text-xs font-extrabold">
                        ✓ Accredited
                    </span>
                    <span class="text-[11px] font-medium text-emerald-700 mt-1.5 block">Officially authorized partner</span>
                @elseif($accreditation && $accreditation->status === 'supervisor_approved')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-teal-100 text-teal-800 border border-teal-300 text-xs font-extrabold">
                        🏛️ With Admin
                    </span>
                    <span class="text-[11px] font-medium text-teal-700 mt-1.5 block">Endorsed by PESD Supervisor</span>
                @elseif($accreditation && $accreditation->status === 'jpo_approved')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-100 text-blue-800 border border-blue-300 text-xs font-extrabold">
                        📋 With Supervisor
                    </span>
                    <span class="text-[11px] font-medium text-blue-700 mt-1.5 block">Recommended by JPO</span>
                @elseif($accreditation && $accreditation->status === 'submitted_to_jpo')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-800 border border-amber-300 text-xs font-extrabold">
                        ⏳ Under Review
                    </span>
                    <span class="text-[11px] font-medium text-amber-700 mt-1.5 block">Awaiting JPO evaluation</span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-300 text-xs font-extrabold">
                        Not Submitted
                    </span>
                    <span class="text-[11px] font-medium text-slate-500 mt-1.5 block">Documents pending upload</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Navigation & Getting Started -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Next Steps Card -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 sm:p-8 border border-emerald-100 shadow-sm space-y-6">
            <div>
                <h2 class="text-lg font-black text-slate-900">Employer Onboarding Checklist</h2>
                <p class="text-xs text-slate-500 mt-0.5">Complete these steps to unlock full recruitment features</p>
            </div>

            <div class="space-y-3">
                <div class="flex items-start gap-3.5 p-4 rounded-2xl {{ ($employer->is_accredited || ($accreditation && $accreditation->status === 'admin_approved')) ? 'bg-emerald-50/60 border border-emerald-200' : 'bg-slate-50 border border-slate-100' }}">
                    <div class="h-6 w-6 rounded-full {{ ($employer->is_accredited || ($accreditation && $accreditation->status === 'admin_approved')) ? 'bg-emerald-600 text-white' : 'bg-emerald-100 text-emerald-800' }} font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                        {{ ($employer->is_accredited || ($accreditation && $accreditation->status === 'admin_approved')) ? '✓' : '1' }}
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-slate-900">Company Accreditation</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            @if($employer->is_accredited || ($accreditation && $accreditation->status === 'admin_approved'))
                                Officially accredited with Cebu City DMDP.
                            @elseif($accreditation)
                                Submitted and currently under DMDP officer evaluation.
                            @else
                                Upload SEC/DTI registration and Mayor's permit for verification.
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('employer.accreditation') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">
                        @if($employer->is_accredited || ($accreditation && $accreditation->status === 'admin_approved'))
                            View Status &rarr;
                        @else
                            Upload &rarr;
                        @endif
                    </a>
                </div>

                <div class="flex items-start gap-3.5 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                    <div class="h-6 w-6 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">2</div>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-slate-900">Publish Your Job Vacancy</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Define job title, salary, required skills, and PWD accessibility parameters.</p>
                    </div>
                    <a href="{{ route('employer.job-postings') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">Create &rarr;</a>
                </div>

                <div class="flex items-start gap-3.5 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                    <div class="h-6 w-6 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">3</div>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-slate-900">Match & Hire Certified Jobseekers</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">View cosine similarity scores and schedule onsite or online interviews.</p>
                    </div>
                    <a href="{{ route('employer.referred-jobseekers') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">View &rarr;</a>
                </div>
            </div>
        </div>

        <!-- DMDP Help Box -->
        <div class="bg-gradient-to-br from-emerald-900 via-emerald-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white flex flex-col justify-between shadow-xl">
            <div class="space-y-3">
                <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Direct Assistance</span>
                <h3 class="text-xl font-black">Need Help with Recruitment?</h3>
                <p class="text-xs text-emerald-100/80 leading-relaxed">
                    The DMDP Placement Officer team assists employers in hiring qualified PWDs, vocational graduates, and technical professionals across Cebu City.
                </p>
            </div>

            <div class="pt-6 border-t border-emerald-800/60 mt-6 space-y-2 text-xs text-emerald-200">
                <p class="flex items-center gap-2">
                    <span class="text-emerald-400 font-bold">📍</span> 2F Cebu City Hall Annex
                </p>
                <p class="flex items-center gap-2">
                    <span class="text-emerald-400 font-bold">📞</span> (032) 888-1234
                </p>
            </div>
        </div>
    </div>

</div>
@endsection
