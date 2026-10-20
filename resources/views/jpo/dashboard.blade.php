@extends('layouts.jpo')

@section('title', 'JPO Command Center - TrabaGo DMDP')

@section('content')
<div class="min-h-screen bg-slate-50/70 px-4 py-6 sm:px-6 lg:px-8">
    <div class="w-full max-w-[1680px] mx-auto space-y-6">
        
        <!-- HEADER HERO -->
        <header class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-slate-900 to-emerald-950 p-6 sm:p-8 text-white shadow-lg border border-slate-800">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-semibold text-emerald-300 border border-emerald-500/30">
                            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            DMDP Placement Officer Operations
                        </span>
                        <span class="text-xs text-slate-400">{{ now()->format('l, F d, Y') }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                        Placement Evaluation & Referral Intelligence
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                        Pre-screen candidate qualifications, endorse talent to accredited employers, audit corporate permit filings, and verify monthly placement reports.
                    </p>
                </div>

                <!-- Action Toolbar -->
                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <a href="{{ route('jpo.evaluations.jobseekers') }}" 
                       class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white px-4 py-2.5 text-xs font-bold shadow-md shadow-emerald-950/40 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Evaluate Applicants</span>
                        @if($pendingJobseekers > 0)
                            <span class="px-2 py-0.5 rounded-full bg-white text-emerald-900 text-[10px] font-black">{{ $pendingJobseekers }}</span>
                        @endif
                    </a>

                    <a href="{{ route('jpo.evaluations.accreditations') }}" 
                       class="inline-flex items-center gap-2 rounded-xl bg-slate-800 hover:bg-slate-700 active:scale-95 text-slate-200 border border-slate-700 px-3.5 py-2.5 text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400">
                        <svg class="h-4 w-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Accreditations</span>
                        @if($pendingAccreditations > 0)
                            <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px] font-bold">{{ $pendingAccreditations }}</span>
                        @endif
                    </a>
                </div>
            </div>
        </header>

        <!-- 4 KEY PLACEMENT METRICS -->
        <section aria-label="Placement Metrics Summary" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- 1. Total Endorsed -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:border-slate-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-600">Total Endorsed</span>
                    <div class="h-9 w-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shadow-inner" aria-hidden="true">
                        <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($totalReferredJobseekers) }}</p>
                    <div class="mt-2.5 flex items-center justify-between text-xs text-slate-600 pt-2 border-t border-slate-100 font-medium">
                        <span class="text-emerald-700 font-bold">{{ $totalHiredReferred }} hired</span>
                        <span>&bull;</span>
                        <span class="font-bold text-slate-800">{{ $conversionRate }}% placement rate</span>
                    </div>
                </div>
            </div>

            <!-- 2. Pending Candidate Audit -->
            <div class="rounded-2xl border border-amber-200/80 bg-amber-50/40 p-5 shadow-sm hover:border-amber-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-800">Pending Candidate Audit</span>
                    <div class="h-9 w-9 rounded-xl bg-amber-100 text-amber-900 flex items-center justify-center shadow-inner" aria-hidden="true">
                        <svg class="w-5 h-5 text-amber-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-3xl font-black text-amber-950 tracking-tight">{{ number_format($pendingJobseekers) }}</p>
                    <div class="mt-2.5 flex items-center justify-between text-xs text-amber-900 pt-2 border-t border-amber-200/60 font-medium">
                        <span>Awaiting officer evaluation & referral</span>
                    </div>
                </div>
            </div>

            <!-- 3. Accreditation Backlog -->
            <div class="rounded-2xl border border-purple-200/80 bg-purple-50/40 p-5 shadow-sm hover:border-purple-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-purple-800">Accreditation Backlog</span>
                    <div class="h-9 w-9 rounded-xl bg-purple-100 text-purple-900 flex items-center justify-center shadow-inner" aria-hidden="true">
                        <svg class="w-5 h-5 text-purple-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-3xl font-black text-purple-950 tracking-tight">{{ number_format($pendingAccreditations) }}</p>
                    <div class="mt-2.5 flex items-center justify-between text-xs text-purple-900 pt-2 border-t border-purple-200/60 font-medium">
                        <span>Corporate permits pending audit</span>
                    </div>
                </div>
            </div>

            <!-- 4. Placement Reports -->
            <div class="rounded-2xl border border-blue-200/80 bg-blue-50/40 p-5 shadow-sm hover:border-blue-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-800">Placement Reports</span>
                    <div class="h-9 w-9 rounded-xl bg-blue-100 text-blue-900 flex items-center justify-center shadow-inner" aria-hidden="true">
                        <svg class="w-5 h-5 text-blue-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-3xl font-black text-blue-950 tracking-tight">{{ number_format($pendingPlacementReports) }}</p>
                    <div class="mt-2.5 flex items-center justify-between text-xs text-blue-900 pt-2 border-t border-blue-200/60 font-medium">
                        <span>Monthly employer filings pending review</span>
                    </div>
                </div>
            </div>

        </section>

        <!-- MIDDLE ROW: MONTHLY REFERRAL VELOCITY GRAPH & TASK PIPELINE -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Monthly Endorsement Velocity Chart (2 cols) -->
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">JPO Referral & Placement Conversion Rate</h2>
                        <p class="text-xs text-slate-500">6-Month historical performance of officer-endorsed applicants vs successful hires</p>
                    </div>
                    <div class="flex items-center gap-4 text-xs font-semibold">
                        <div class="flex items-center gap-1.5">
                            <span class="h-3 w-3 rounded bg-emerald-600"></span>
                            <span class="text-slate-700">Referred</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="h-3 w-3 rounded bg-slate-900"></span>
                            <span class="text-slate-700">Hired</span>
                        </div>
                    </div>
                </div>

                <!-- Responsive Graph -->
                <div class="h-56 flex items-end justify-between gap-4 pt-4 px-2">
                    @php
                        $maxRef = 1;
                        foreach($monthlyReferralTrends as $t) {
                            if($t['referred'] > $maxRef) $maxRef = $t['referred'];
                        }
                    @endphp

                    @foreach($monthlyReferralTrends as $t)
                        @php
                            $refHeight = max(8, round(($t['referred'] / $maxRef) * 160));
                            $hHeight = max(6, round(($t['hired'] / $maxRef) * 160));
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end group">
                            <div class="w-full flex items-end justify-center gap-1.5 h-44">
                                <div class="w-4 sm:w-6 bg-emerald-600 rounded-t transition-all group-hover:bg-emerald-700 relative" style="height: {{ $refHeight }}px;">
                                    <span class="opacity-0 group-hover:opacity-100 absolute -top-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] px-2 py-0.5 rounded shadow pointer-events-none whitespace-nowrap transition-opacity">
                                        {{ $t['referred'] }} referred
                                    </span>
                                </div>
                                <div class="w-4 sm:w-6 bg-slate-900 rounded-t transition-all group-hover:bg-slate-800 relative" style="height: {{ $hHeight }}px;">
                                    <span class="opacity-0 group-hover:opacity-100 absolute -top-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] px-2 py-0.5 rounded shadow pointer-events-none whitespace-nowrap transition-opacity">
                                        {{ $t['hired'] }} hired
                                    </span>
                                </div>
                            </div>
                            <span class="text-xs font-semibold text-slate-500">{{ $t['month'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-3 gap-3 pt-3 border-t border-slate-100 text-center">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Referral Conversion</p>
                        <p class="text-base font-black text-emerald-700 mt-0.5">{{ $conversionRate }}%</p>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Avg Turnaround</p>
                        <p class="text-base font-black text-slate-900 mt-0.5">24 Hours</p>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Target Compliance</p>
                        <p class="text-base font-black text-slate-900 mt-0.5">100%</p>
                    </div>
                </div>
            </div>

            <!-- JPO Action Backlog Breakdown (1 col) -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4 flex flex-col justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Task Allocation & Pipeline</h2>
                    <p class="text-xs text-slate-500">Live operational priorities for placement facilitation</p>
                </div>

                <div class="space-y-3">
                    <a href="{{ route('jpo.evaluations.jobseekers') }}" class="block p-3.5 rounded-xl border border-slate-200 hover:border-emerald-600 transition-colors group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900 group-hover:text-emerald-700">1. Candidate Pre-Screening</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                {{ $pendingJobseekers }} Pending
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Audit skills match and officially refer candidates to employer vacancy pipelines.</p>
                    </a>

                    <a href="{{ route('jpo.evaluations.accreditations') }}" class="block p-3.5 rounded-xl border border-slate-200 hover:border-emerald-600 transition-colors group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900 group-hover:text-emerald-700">2. Accreditation Verification</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-800 border border-purple-200">
                                {{ $pendingAccreditations }} Pending
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Verify business permits and tax documents, then forward to Admin approvals.</p>
                    </a>

                    <a href="{{ route('jpo.evaluations.placement-reports') }}" class="block p-3.5 rounded-xl border border-slate-200 hover:border-emerald-600 transition-colors group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900 group-hover:text-emerald-700">3. Monthly Placement Reports</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                {{ $pendingPlacementReports }} Pending
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Validate employer monthly hiring submissions for PESO archival and compliance.</p>
                    </a>
                </div>

                <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900">
                    <p class="font-bold">DMDP Performance Standard</p>
                    <p class="text-xs text-emerald-800 mt-0.5">Accreditation submissions are audited within 48 hours pursuant to City Ordinance rules.</p>
                </div>
            </div>

        </div>

        <!-- BOTTOM ROW: RECENT APPLICANTS FOR SCREENING & ACCREDITATION PAPERS -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Applicants Awaiting JPO Evaluation -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Candidates Awaiting JPO Screening</h2>
                        <p class="text-xs text-slate-500">Evaluate and officially endorse to employer vacancies</p>
                    </div>
                    <a href="{{ route('jpo.evaluations.jobseekers') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 hover:underline">
                        View All &rarr;
                    </a>
                </div>

                @if(count($recentApplicants) > 0)
                    <div class="divide-y divide-slate-100">
                        @foreach($recentApplicants as $app)
                            <div class="py-3 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-900 truncate">
                                        {{ $app->jobseeker->first_name ?? 'Candidate' }} {{ $app->jobseeker->last_name ?? '' }}
                                    </p>
                                    <p class="text-xs text-slate-500 mt-0.5 truncate">
                                        Applying for <strong class="text-slate-800 font-semibold">{{ $app->jobPosting->title ?? 'Position' }}</strong> at <span class="text-emerald-700 font-medium">{{ $app->jobPosting->employer->company_name ?? 'Employer' }}</span>
                                    </p>
                                </div>
                                <a href="{{ route('jpo.evaluations.jobseekers') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-emerald-600 active:scale-95 text-white text-xs font-bold transition-all shrink-0 shadow-sm">
                                    Evaluate
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-10 text-center text-xs text-slate-400">
                        No candidates currently awaiting pre-screening.
                    </div>
                @endif
            </div>

            <!-- Accreditation Papers in Queue -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Accreditation Papers in Review</h2>
                        <p class="text-xs text-slate-500">Corporate partners awaiting permit validation</p>
                    </div>
                    <a href="{{ route('jpo.evaluations.accreditations') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 hover:underline">
                        Accreditations &rarr;
                    </a>
                </div>

                @if(count($recentAccreditations) > 0)
                    <div class="divide-y divide-slate-100">
                        @foreach($recentAccreditations as $acc)
                            <div class="py-3 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-900 truncate">{{ $acc->company_name }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5 truncate">
                                        Submitted {{ \Carbon\Carbon::parse($acc->submitted_at ?? $acc->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <a href="{{ route('jpo.evaluations.accreditations') }}" class="px-3.5 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 active:scale-95 text-slate-800 text-xs font-bold transition-all shrink-0 shadow-sm">
                                    Inspect Papers
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-10 text-center text-xs text-slate-400">
                        No accreditation filings in the review queue.
                    </div>
                @endif
            </div>

        </div>

    </div>
</div>
@endsection
