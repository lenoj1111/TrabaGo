@extends('layouts.jpo')

@section('title', 'JPO Command Center - TrabaGo DMDP')

@section('content')
<div class="min-h-screen bg-gray-50 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-semibold text-green-700 border border-green-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                        DMDP Job Placement Officer Division
                    </span>
                    <span class="text-xs text-gray-500">{{ now()->format('l, F d, Y') }}</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Placement Evaluation & Referral Intelligence</h1>
                <p class="text-xs text-gray-500">
                    Pre-screen candidate qualifications, endorse talent to employers, verify accreditations, and audit monthly placement filings.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('jpo.evaluations.jobseekers') }}" class="px-3.5 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-xs font-semibold transition-colors flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Evaluate Applicants</span>
                    @if($pendingJobseekers > 0)
                        <span class="px-1.5 py-0.2 rounded-full bg-white text-green-800 text-[10px] font-bold">{{ $pendingJobseekers }}</span>
                    @endif
                </a>
                <a href="{{ route('jpo.evaluations.accreditations') }}" class="px-3.5 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold transition-colors flex items-center gap-1.5">
                    <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Accreditations</span>
                    @if($pendingAccreditations > 0)
                        <span class="px-1.5 py-0.2 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">{{ $pendingAccreditations }}</span>
                    @endif
                </a>
            </div>
        </div>

        <!-- 4 Key Placement Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <div class="bg-white rounded-xl p-5 border border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Endorsed</span>
                    <span class="p-2 rounded-lg bg-green-50 text-green-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </span>
                </div>
                <div class="mt-2">
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalReferredJobseekers) }}</p>
                    <div class="flex items-center gap-1.5 mt-1 text-xs text-gray-500">
                        <span class="text-green-700 font-semibold">{{ $totalHiredReferred }} hired</span>
                        <span>&bull;</span>
                        <span>{{ $conversionRate }}% placement rate</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 border border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pending Candidate Audit</span>
                    <span class="p-2 rounded-lg bg-amber-50 text-amber-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <div class="mt-2">
                    <p class="text-2xl font-bold text-amber-600">{{ $pendingJobseekers }}</p>
                    <div class="flex items-center gap-1.5 mt-1 text-xs text-gray-500">
                        <span>Awaiting officer recommendation</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 border border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Accreditation Backlog</span>
                    <span class="p-2 rounded-lg bg-purple-50 text-purple-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                </div>
                <div class="mt-2">
                    <p class="text-2xl font-bold text-gray-900">{{ $pendingAccreditations }}</p>
                    <div class="flex items-center gap-1.5 mt-1 text-xs text-gray-500">
                        <span>Corporate permit inspection files</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 border border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Placement Reports</span>
                    <span class="p-2 rounded-lg bg-blue-50 text-blue-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                </div>
                <div class="mt-2">
                    <p class="text-2xl font-bold text-gray-900">{{ $pendingPlacementReports }}</p>
                    <div class="flex items-center gap-1.5 mt-1 text-xs text-gray-500">
                        <span>Monthly employer filings pending audit</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Middle Row: Monthly Referral Velocity Graph & Backlog Status -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Monthly Endorsement Velocity Chart (2 cols) -->
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-gray-200 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-100 pb-4">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">JPO Referral & Placement Conversion Rate</h2>
                        <p class="text-xs text-gray-500">6-Month historical performance of officer-endorsed applicants vs successful hires</p>
                    </div>
                    <div class="flex items-center gap-4 text-xs">
                        <div class="flex items-center gap-1.5">
                            <span class="h-3 w-3 rounded bg-green-600"></span>
                            <span class="text-gray-600">Referred</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="h-3 w-3 rounded bg-gray-900"></span>
                            <span class="text-gray-600">Hired</span>
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
                                <div class="w-4 sm:w-6 bg-green-600 rounded-t transition-all group-hover:bg-green-700 relative" style="height: {{ $refHeight }}px;">
                                    <span class="opacity-0 group-hover:opacity-100 absolute -top-6 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] px-1.5 py-0.5 rounded transition-opacity pointer-events-none whitespace-nowrap">
                                        {{ $t['referred'] }} referred
                                    </span>
                                </div>
                                <div class="w-4 sm:w-6 bg-gray-900 rounded-t transition-all group-hover:bg-gray-800 relative" style="height: {{ $hHeight }}px;">
                                    <span class="opacity-0 group-hover:opacity-100 absolute -top-6 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] px-1.5 py-0.5 rounded transition-opacity pointer-events-none whitespace-nowrap">
                                        {{ $t['hired'] }} hired
                                    </span>
                                </div>
                            </div>
                            <span class="text-[11px] font-medium text-gray-500">{{ $t['month'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-3 gap-3 pt-3 border-t border-gray-100 text-center">
                    <div class="p-2.5 rounded-lg bg-gray-50">
                        <p class="text-[10px] font-semibold text-gray-500 uppercase">Referral Conversion</p>
                        <p class="text-sm font-bold text-green-700 mt-0.5">{{ $conversionRate }}%</p>
                    </div>
                    <div class="p-2.5 rounded-lg bg-gray-50">
                        <p class="text-[10px] font-semibold text-gray-500 uppercase">Avg Review Turnaround</p>
                        <p class="text-sm font-bold text-gray-900 mt-0.5">24 Hours</p>
                    </div>
                    <div class="p-2.5 rounded-lg bg-gray-50">
                        <p class="text-[10px] font-semibold text-gray-500 uppercase">Target Compliance</p>
                        <p class="text-sm font-bold text-gray-900 mt-0.5">100%</p>
                    </div>
                </div>
            </div>

            <!-- JPO Action Backlog Breakdown (1 col) -->
            <div class="bg-white rounded-2xl p-6 border border-gray-200 space-y-5 flex flex-col justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Task Allocation & Pipeline</h2>
                    <p class="text-xs text-gray-500">Live operational priorities for placement facilitation</p>
                </div>

                <div class="space-y-4">
                    <a href="{{ route('jpo.evaluations.jobseekers') }}" class="block p-3.5 rounded-xl border border-gray-200 hover:border-green-600 transition-colors group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-900 group-hover:text-green-700">1. Candidate Pre-Screening</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                {{ $pendingJobseekers }} Pending
                            </span>
                        </div>
                        <p class="text-[11px] text-gray-500 mt-1">Audit skills match and officially refer candidates to employer vacancy pipelines.</p>
                    </a>

                    <a href="{{ route('jpo.evaluations.accreditations') }}" class="block p-3.5 rounded-xl border border-gray-200 hover:border-green-600 transition-colors group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-900 group-hover:text-green-700">2. Accreditation Verification</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                {{ $pendingAccreditations }} Pending
                            </span>
                        </div>
                        <p class="text-[11px] text-gray-500 mt-1">Verify business permits and tax documents, then forward to PESD Supervisor.</p>
                    </a>

                    <a href="{{ route('jpo.evaluations.placement-reports') }}" class="block p-3.5 rounded-xl border border-gray-200 hover:border-green-600 transition-colors group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-900 group-hover:text-green-700">3. Monthly Placement Reports</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $pendingPlacementReports }} Pending
                            </span>
                        </div>
                        <p class="text-[11px] text-gray-500 mt-1">Validate employer monthly hiring submissions for PESO archival and compliance.</p>
                    </a>
                </div>

                <div class="p-3 rounded-xl bg-green-50 border border-green-200 text-xs text-green-800">
                    <p class="font-bold">DMDP Performance Standard</p>
                    <p class="text-[11px] text-green-700 mt-0.5">Accreditation submissions are audited within 48 hours pursuant to City Ordinance rules.</p>
                </div>
            </div>

        </div>

        <!-- Bottom Row: Recent Applicants for Screening & Accreditation Papers -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Applicants Awaiting JPO Evaluation -->
            <div class="bg-white rounded-2xl p-6 border border-gray-200 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Candidates Awaiting JPO Screening</h2>
                        <p class="text-xs text-gray-500">Evaluate and officially endorse to employer vacancies</p>
                    </div>
                    <a href="{{ route('jpo.evaluations.jobseekers') }}" class="text-xs font-semibold text-green-700 hover:text-green-800">
                        View All &rarr;
                    </a>
                </div>

                @if(count($recentApplicants) > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($recentApplicants as $app)
                            <div class="py-3 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-gray-900 truncate">
                                        {{ $app->jobseeker->first_name ?? 'Candidate' }} {{ $app->jobseeker->last_name ?? '' }}
                                    </p>
                                    <p class="text-[11px] text-gray-500 mt-0.5 truncate">
                                        Applying for <span class="text-gray-800 font-medium">{{ $app->jobPosting->title ?? 'Position' }}</span> at <span class="text-green-700">{{ $app->jobPosting->employer->company_name ?? 'Employer' }}</span>
                                    </p>
                                </div>
                                <a href="{{ route('jpo.evaluations.jobseekers') }}" class="px-3 py-1.5 rounded-lg bg-gray-900 hover:bg-green-600 text-white text-xs font-semibold transition-colors shrink-0">
                                    Evaluate
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-8 text-center text-xs text-gray-500">
                        No candidates currently awaiting pre-screening.
                    </div>
                @endif
            </div>

            <!-- Accreditation Papers in Queue -->
            <div class="bg-white rounded-2xl p-6 border border-gray-200 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Accreditation Papers in Review</h2>
                        <p class="text-xs text-gray-500">Corporate partners awaiting permit validation</p>
                    </div>
                    <a href="{{ route('jpo.evaluations.accreditations') }}" class="text-xs font-semibold text-green-700 hover:text-green-800">
                        Accreditations &rarr;
                    </a>
                </div>

                @if(count($recentAccreditations) > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($recentAccreditations as $acc)
                            <div class="py-3 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-gray-900 truncate">{{ $acc->company_name }}</p>
                                    <p class="text-[11px] text-gray-500 mt-0.5 truncate">
                                        Submitted {{ \Carbon\Carbon::parse($acc->submitted_at ?? $acc->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <a href="{{ route('jpo.evaluations.accreditations') }}" class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold transition-colors shrink-0">
                                    Inspect Papers
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-8 text-center text-xs text-gray-500">
                        No accreditation filings in the review queue.
                    </div>
                @endif
            </div>

        </div>

    </div>
</div>
@endsection
