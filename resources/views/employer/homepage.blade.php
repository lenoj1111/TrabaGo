@extends('layouts.employer')

@section('title', 'Employer Dashboard - TrabaGo DMDP')

@section('content')
<div class="w-full max-w-[1680px] mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    <!-- Executive Welcome & Operations Header -->
    <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-8 text-white shadow-xl border border-emerald-500/20 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2">
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    DMDP Verified Partner
                </span>
                <span class="text-xs font-semibold text-slate-300 bg-white/10 px-2.5 py-0.5 rounded-full border border-white/10">{{ $employer->company_name }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Talent Acquisition & Recruitment Hub</h1>
            <p class="text-xs sm:text-sm text-slate-300 max-w-2xl">
                Track job vacancy performance, applicant conversion pipelines, and direct JPO placement endorsements in real time.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('employer.job-postings') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-lg shadow-emerald-900/30 hover:scale-105">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Post Job Vacancy</span>
            </a>
            <a href="{{ route('employer.referred-jobseekers') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-all border border-white/20 backdrop-blur-xs">
                <svg class="h-4 w-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Referred Candidates</span>
                @if($referredCount > 0)
                    <span class="px-2 py-0.5 rounded-full bg-emerald-500 text-white text-[10px] font-black">{{ $referredCount }}</span>
                @endif
            </a>
        </div>
    </div>

    <!-- Incomplete Accreditation Documents Alert -->
    @if($accreditation && ($accreditation->document_status === 'incomplete' || !empty($accreditation->document_incomplete_reason)))
        <div class="rounded-2xl border border-rose-300 bg-rose-50 p-5 text-rose-950 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="h-10 w-10 rounded-xl bg-rose-200/80 text-rose-800 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-rose-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full bg-rose-600 text-white text-[10px] font-black uppercase tracking-wider">Incomplete Accreditation Papers</span>
                        <span class="text-xs text-rose-800 font-bold">Action Needed</span>
                    </div>
                    <p class="text-xs font-black text-rose-950 mt-1">JPO Feedback: {{ $accreditation->document_incomplete_reason ?: 'Some required legal verification documents are incomplete.' }}</p>
                    <p class="text-[11px] text-rose-800 mt-0.5">Please review the reason and re-upload the deficient documents in your accreditation portal.</p>
                </div>
            </div>
            <a href="{{ route('employer.accreditation') }}" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shrink-0 transition-colors shadow-xs">
                View & Re-upload Documents &rarr;
            </a>
        </div>
    @endif

    <!-- 4 Key Performance Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Metric 1: Active Job Postings -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs hover:border-emerald-300 transition-all group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Active Vacancies</span>
                <span class="p-2.5 rounded-xl bg-emerald-50 text-emerald-700 group-hover:scale-105 transition-transform">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <p class="text-3xl font-black text-slate-900">{{ $approvedJobs }}</p>
                <div class="flex items-center gap-1.5 mt-1.5 text-xs text-slate-500">
                    <span class="font-semibold text-slate-700">{{ $totalJobs }} total postings</span>
                    <span>&bull;</span>
                    <span class="text-amber-700 font-semibold">{{ $pendingJobs }} pending</span>
                </div>
            </div>
        </div>

        <!-- Metric 2: Total Applicants -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs hover:border-blue-300 transition-all group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Candidates</span>
                <span class="p-2.5 rounded-xl bg-blue-50 text-blue-700 group-hover:scale-105 transition-transform">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <p class="text-3xl font-black text-slate-900">{{ number_format($totalApplicants) }}</p>
                <div class="flex items-center gap-1.5 mt-1.5 text-xs text-slate-500">
                    <span class="text-blue-700 font-semibold">{{ $interviewCount }} in interview</span>
                    <span>&bull;</span>
                    <span class="text-emerald-700 font-semibold">{{ $hiredCount }} hired</span>
                </div>
            </div>
        </div>

        <!-- Metric 3: JPO Referrals -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs hover:border-purple-300 transition-all group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">JPO Pre-Screened</span>
                <span class="p-2.5 rounded-xl bg-purple-50 text-purple-700 group-hover:scale-105 transition-transform">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <p class="text-3xl font-black text-slate-900">{{ $referredCount }}</p>
                <div class="flex items-center gap-1.5 mt-1.5 text-xs text-slate-500">
                    <span class="text-purple-700 font-semibold">City Officer endorsed</span>
                </div>
            </div>
        </div>

        <!-- Metric 4: Accreditation Status -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs hover:border-amber-300 transition-all group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Accreditation</span>
                <span class="p-2.5 rounded-xl bg-amber-50 text-amber-700 group-hover:scale-105 transition-transform">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                @if($employer->is_accredited || ($accreditation && $accreditation->status === 'admin_approved'))
                    <p class="text-xl font-bold text-emerald-700 flex items-center gap-1.5">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Accredited Partner
                    </p>
                    <span class="text-xs text-slate-500 block mt-1">Authorized for DMDP facilitation</span>
                @elseif($accreditation && in_array($accreditation->status, ['supervisor_approved', 'jpo_approved']))
                    <p class="text-xl font-bold text-blue-700 flex items-center gap-1.5">
                        <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                        JPO Recommended
                    </p>
                    <span class="text-xs text-slate-500 block mt-1">Pending Admin authorization</span>
                @elseif($accreditation && $accreditation->status === 'submitted_to_jpo')
                    <p class="text-xl font-bold text-amber-700 flex items-center gap-1.5">
                        <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                        Under Review
                    </p>
                    <span class="text-xs text-slate-500 block mt-1">Awaiting JPO document audit</span>
                @else
                    <p class="text-xl font-bold text-slate-600 flex items-center gap-1.5">
                        <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                        Pending Upload
                    </p>
                    <span class="text-xs text-slate-500 block mt-1">Submit legal papers</span>
                @endif
            </div>
        </div>

    </div>

    <!-- Middle Row: Monthly Applicant Velocity Chart & Conversion Funnel -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Application Velocity Graph (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Application Velocity & Hiring Volume</h2>
                    <p class="text-xs text-slate-500">6-Month historical candidate submissions for {{ $employer->company_name }}</p>
                </div>
                <div class="flex items-center gap-4 text-xs font-semibold">
                    <div class="flex items-center gap-1.5">
                        <span class="h-3 w-3 rounded-sm bg-emerald-600"></span>
                        <span class="text-slate-600">Applications</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="h-3 w-3 rounded-sm bg-slate-900"></span>
                        <span class="text-slate-600">Hired</span>
                    </div>
                </div>
            </div>

            <!-- Responsive Bar Graph -->
            <div class="h-56 flex items-end justify-between gap-3 pt-4 px-2">
                @php
                    $maxVal = 1;
                    foreach($monthlyApplicantTrends as $trend) {
                        if($trend['applications'] > $maxVal) $maxVal = $trend['applications'];
                    }
                @endphp

                @foreach($monthlyApplicantTrends as $trend)
                    @php
                        $appHeight = max(8, round(($trend['applications'] / $maxVal) * 160));
                        $hireHeight = max(6, round(($trend['hires'] / $maxVal) * 160));
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end group">
                        <div class="w-full flex items-end justify-center gap-1.5 h-44">
                            <!-- App Bar -->
                            <div class="w-4 sm:w-6 bg-emerald-600 rounded-t transition-all group-hover:bg-emerald-500 relative" style="height: {{ $appHeight }}px;">
                                <span class="opacity-0 group-hover:opacity-100 absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] px-2 py-0.5 rounded shadow-sm transition-opacity pointer-events-none whitespace-nowrap z-10 font-bold">
                                    {{ $trend['applications'] }} apps
                                </span>
                            </div>
                            <!-- Hire Bar -->
                            <div class="w-4 sm:w-6 bg-slate-900 rounded-t transition-all group-hover:bg-slate-800 relative" style="height: {{ $hireHeight }}px;">
                                <span class="opacity-0 group-hover:opacity-100 absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] px-2 py-0.5 rounded shadow-sm transition-opacity pointer-events-none whitespace-nowrap z-10 font-bold">
                                    {{ $trend['hires'] }} hired
                                </span>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-500 truncate">{{ $trend['month'] }}</span>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-3 gap-3 pt-4 border-t border-slate-100 text-center">
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Hires</p>
                    <p class="text-sm font-black text-emerald-700 mt-0.5">{{ $hiredCount }} Placed</p>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">JPO Referral Share</p>
                    <p class="text-sm font-black text-slate-900 mt-0.5">
                        {{ $totalApplicants > 0 ? round(($referredCount / $totalApplicants) * 100, 1) : 0 }}%
                    </p>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Placement Rate</p>
                    <p class="text-sm font-black text-slate-900 mt-0.5">
                        {{ $totalApplicants > 0 ? round(($hiredCount / $totalApplicants) * 100, 1) : 0 }}%
                    </p>
                </div>
            </div>
        </div>

        <!-- Recruitment Pipeline Funnel (1 col) -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-5 flex flex-col justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Recruitment Conversion Funnel</h2>
                <p class="text-xs text-slate-500">Applicant progression through your hiring stages</p>
            </div>

            <div class="space-y-4">
                @php
                    $tA = max(1, $totalApplicants);
                    $pctReferred = round(($referredCount / $tA) * 100);
                    $pctInterview = round(($interviewCount / $tA) * 100);
                    $pctHired = round(($hiredCount / $tA) * 100);
                @endphp

                <!-- Stage 1 -->
                <div class="space-y-1.5">
                    <div class="flex justify-between text-xs font-bold">
                        <span class="text-slate-700">1. Applications Received</span>
                        <span class="text-slate-900">{{ $totalApplicants }} (100%)</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                        <div class="bg-slate-400 h-full rounded-full" style="width: 100%;"></div>
                    </div>
                </div>

                <!-- Stage 2 -->
                <div class="space-y-1.5">
                    <div class="flex justify-between text-xs font-bold">
                        <span class="text-purple-900">2. JPO Pre-Screened Referrals</span>
                        <span class="text-purple-700">{{ $referredCount }} ({{ $pctReferred }}%)</span>
                    </div>
                    <div class="w-full bg-purple-50 h-2.5 rounded-full overflow-hidden">
                        <div class="bg-purple-600 h-full rounded-full" style="width: {{ $pctReferred }}%;"></div>
                    </div>
                </div>

                <!-- Stage 3 -->
                <div class="space-y-1.5">
                    <div class="flex justify-between text-xs font-bold">
                        <span class="text-blue-900">3. Under Interview</span>
                        <span class="text-blue-700">{{ $interviewCount }} ({{ $pctInterview }}%)</span>
                    </div>
                    <div class="w-full bg-blue-50 h-2.5 rounded-full overflow-hidden">
                        <div class="bg-blue-600 h-full rounded-full" style="width: {{ $pctInterview }}%;"></div>
                    </div>
                </div>

                <!-- Stage 4 -->
                <div class="space-y-1.5">
                    <div class="flex justify-between text-xs font-bold">
                        <span class="text-emerald-900">4. Offers Extended / Hired</span>
                        <span class="text-emerald-700">{{ $hiredCount }} ({{ $pctHired }}%)</span>
                    </div>
                    <div class="w-full bg-emerald-50 h-2.5 rounded-full overflow-hidden">
                        <div class="bg-emerald-600 h-full rounded-full" style="width: {{ $pctHired }}%;"></div>
                    </div>
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-emerald-50/60 border border-emerald-100 text-xs text-emerald-900">
                <span class="font-bold">Pro-Tip:</span> Review JPO-endorsed candidates promptly to maintain priority placement status on the TrabaGo AI Matching engine.
            </div>
        </div>

    </div>

    <!-- Bottom Row: Top Active Postings & Recent Candidates -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Top Performing Job Vacancies -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Top Job Postings by Influx</h2>
                    <p class="text-xs text-slate-500">Applicant interest across active positions</p>
                </div>
                <a href="{{ route('employer.job-postings') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 transition-colors">
                    Manage Jobs &rarr;
                </a>
            </div>

            @if(count($topJobs) > 0)
                <div class="divide-y divide-slate-100">
                    @foreach($topJobs as $job)
                        <div class="py-3.5 flex items-center justify-between gap-3 hover:bg-slate-50/60 transition-colors rounded-xl px-2">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $job->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                        {{ ucfirst($job->status) }}
                                    </span>
                                    <p class="text-xs font-bold text-slate-900 truncate">{{ $job->title }}</p>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-1 truncate">
                                    {{ $job->employment_type ?? 'Full-time' }} &bull; Posted {{ $job->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-xs font-bold text-slate-900 block">{{ $job->applications_count }} applicants</span>
                                <a href="{{ route('employer.job-postings.show', $job->job_id) }}" class="inline-block text-[11px] text-emerald-700 hover:text-emerald-800 font-semibold mt-0.5">
                                    View details &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-12 text-center text-xs text-slate-500">
                    <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400 mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="font-bold text-slate-700">No active job vacancies created yet</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Publish your openings to start receiving candidates from DMDP.</p>
                </div>
            @endif
        </div>

        <!-- Recent Applicants / Candidate Stream -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Recent Candidate Influx</h2>
                    <p class="text-xs text-slate-500">Latest jobseekers applying to your vacancies</p>
                </div>
                <a href="{{ route('employer.applications') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 transition-colors">
                    All Applicants &rarr;
                </a>
            </div>

            @if(count($recentApplicants) > 0)
                <div class="divide-y divide-slate-100">
                    @foreach($recentApplicants as $applicant)
                        <div class="py-3.5 flex items-center justify-between gap-3 hover:bg-slate-50/60 transition-colors rounded-xl px-2">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="h-8 w-8 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0 border border-slate-200">
                                    {{ strtoupper(substr($applicant->jobseeker->first_name ?? 'C', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        @if($applicant->referred_by_jpo)
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                JPO Endorsed
                                            </span>
                                        @endif
                                        <p class="text-xs font-bold text-slate-900 truncate">
                                            {{ $applicant->jobseeker->first_name ?? 'Candidate' }} {{ $applicant->jobseeker->last_name ?? '' }}
                                        </p>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5 truncate">
                                        Applied for <span class="text-slate-800 font-semibold">{{ $applicant->jobPosting->title ?? 'Position' }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $applicant->status === 'hired' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($applicant->status === 'interview' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-700') }}">
                                    {{ ucfirst($applicant->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-12 text-center text-xs text-slate-500">
                    <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400 mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <p class="font-bold text-slate-700">No applicants received yet</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">As applicants submit credentials or JPOs endorse candidates, they will appear here.</p>
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
