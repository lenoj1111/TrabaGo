@extends('layouts.employer')

@section('title', 'Employer Dashboard - TrabaGo')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    
    <!-- Welcome / Header Card -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-semibold text-green-700 border border-green-200">
                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                    DMDP Verified Employer Partner
                </span>
                <span class="text-xs text-gray-500">{{ $employer->company_name }}</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Talent Acquisition & Recruitment Analytics</h1>
            <p class="text-xs text-gray-500">
                Track job vacancy performance, applicant conversion pipelines, and direct JPO placement endorsements.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('employer.job-postings') }}" class="px-3.5 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-xs font-semibold transition-colors flex items-center gap-1.5">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Post Job Vacancy</span>
            </a>
            <a href="{{ route('employer.referred-jobseekers') }}" class="px-3.5 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold transition-colors flex items-center gap-1.5">
                <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Referred Candidates</span>
                @if($referredCount > 0)
                    <span class="px-1.5 py-0.2 rounded-full bg-green-100 text-green-800 text-[10px] font-bold">{{ $referredCount }}</span>
                @endif
            </a>
        </div>
    </div>

    <!-- 4 Key Performance Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="bg-white rounded-xl p-5 border border-gray-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Active Job Postings</span>
                <span class="p-2 rounded-lg bg-green-50 text-green-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </span>
            </div>
            <div class="mt-2">
                <p class="text-2xl font-bold text-gray-900">{{ $approvedJobs }}</p>
                <div class="flex items-center gap-1.5 mt-1 text-xs text-gray-500">
                    <span>{{ $totalJobs }} total vacancies</span>
                    <span>&bull;</span>
                    <span class="text-amber-600 font-semibold">{{ $pendingJobs }} pending review</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-gray-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Applicants</span>
                <span class="p-2 rounded-lg bg-blue-50 text-blue-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
            </div>
            <div class="mt-2">
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalApplicants) }}</p>
                <div class="flex items-center gap-1.5 mt-1 text-xs text-gray-500">
                    <span class="text-blue-700 font-semibold">{{ $interviewCount }} in interview</span>
                    <span>&bull;</span>
                    <span class="text-green-700 font-semibold">{{ $hiredCount }} hired</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-gray-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">JPO Referrals</span>
                <span class="p-2 rounded-lg bg-purple-50 text-purple-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </span>
            </div>
            <div class="mt-2">
                <p class="text-2xl font-bold text-gray-900">{{ $referredCount }}</p>
                <div class="flex items-center gap-1.5 mt-1 text-xs text-gray-500">
                    <span>Officer pre-screened candidates</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-gray-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Accreditation Status</span>
                <span class="p-2 rounded-lg bg-amber-50 text-amber-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </span>
            </div>
            <div class="mt-2">
                @if($employer->is_accredited || ($accreditation && $accreditation->status === 'admin_approved'))
                    <p class="text-base font-bold text-green-700">Accredited Partner</p>
                    <span class="text-[11px] text-gray-500 block mt-0.5">Authorized for DMDP facilitation</span>
                @elseif($accreditation && $accreditation->status === 'supervisor_approved')
                    <p class="text-base font-bold text-blue-700">Supervisor Endorsed</p>
                    <span class="text-[11px] text-gray-500 block mt-0.5">With Central Administration</span>
                @elseif($accreditation && $accreditation->status === 'submitted_to_jpo')
                    <p class="text-base font-bold text-amber-700">Under Review</p>
                    <span class="text-[11px] text-gray-500 block mt-0.5">Awaiting JPO document audit</span>
                @else
                    <p class="text-base font-bold text-gray-600">Pending Upload</p>
                    <span class="text-[11px] text-gray-500 block mt-0.5">Submit registration documents</span>
                @endif
            </div>
        </div>

    </div>

    <!-- Middle Row: Monthly Applicant Chart & Pipeline Funnel -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Monthly Application Influx Chart (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-gray-200 space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-100 pb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Application Velocity & Hiring Volume</h2>
                    <p class="text-xs text-gray-500">6-Month trend of candidate submissions for {{ $employer->company_name }}</p>
                </div>
                <div class="flex items-center gap-4 text-xs">
                    <div class="flex items-center gap-1.5">
                        <span class="h-3 w-3 rounded bg-green-600"></span>
                        <span class="text-gray-600">Applications</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="h-3 w-3 rounded bg-gray-900"></span>
                        <span class="text-gray-600">Hired</span>
                    </div>
                </div>
            </div>

            <!-- Responsive Bar Graph -->
            <div class="h-56 flex items-end justify-between gap-4 pt-4 px-2">
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
                            <div class="w-4 sm:w-6 bg-green-600 rounded-t transition-all group-hover:bg-green-700 relative" style="height: {{ $appHeight }}px;">
                                <span class="opacity-0 group-hover:opacity-100 absolute -top-6 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] px-1.5 py-0.5 rounded transition-opacity pointer-events-none whitespace-nowrap">
                                    {{ $trend['applications'] }} apps
                                </span>
                            </div>
                            <!-- Hire Bar -->
                            <div class="w-4 sm:w-6 bg-gray-900 rounded-t transition-all group-hover:bg-gray-800 relative" style="height: {{ $hireHeight }}px;">
                                <span class="opacity-0 group-hover:opacity-100 absolute -top-6 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] px-1.5 py-0.5 rounded transition-opacity pointer-events-none whitespace-nowrap">
                                    {{ $trend['hires'] }} hired
                                </span>
                            </div>
                        </div>
                        <span class="text-[11px] font-medium text-gray-500">{{ $trend['month'] }}</span>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-3 gap-3 pt-3 border-t border-gray-100 text-center">
                <div class="p-2.5 rounded-lg bg-gray-50">
                    <p class="text-[10px] font-semibold text-gray-500 uppercase">Total Hires</p>
                    <p class="text-sm font-bold text-green-700 mt-0.5">{{ $hiredCount }} Candidates</p>
                </div>
                <div class="p-2.5 rounded-lg bg-gray-50">
                    <p class="text-[10px] font-semibold text-gray-500 uppercase">JPO Referral Share</p>
                    <p class="text-sm font-bold text-gray-900 mt-0.5">
                        {{ $totalApplicants > 0 ? round(($referredCount / $totalApplicants) * 100, 1) : 0 }}%
                    </p>
                </div>
                <div class="p-2.5 rounded-lg bg-gray-50">
                    <p class="text-[10px] font-semibold text-gray-500 uppercase">Placement Rate</p>
                    <p class="text-sm font-bold text-gray-900 mt-0.5">
                        {{ $totalApplicants > 0 ? round(($hiredCount / $totalApplicants) * 100, 1) : 0 }}%
                    </p>
                </div>
            </div>
        </div>

        <!-- Recruitment Pipeline Funnel (1 col) -->
        <div class="bg-white rounded-2xl p-6 border border-gray-200 space-y-5 flex flex-col justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-900">Recruitment Conversion Funnel</h2>
                <p class="text-xs text-gray-500">Applicant progression through your hiring stages</p>
            </div>

            <div class="space-y-3.5">
                @php
                    $tA = max(1, $totalApplicants);
                    $pctReferred = round(($referredCount / $tA) * 100);
                    $pctInterview = round(($interviewCount / $tA) * 100);
                    $pctHired = round(($hiredCount / $tA) * 100);
                @endphp

                <div class="space-y-1">
                    <div class="flex justify-between text-xs font-semibold">
                        <span class="text-gray-700">1. Applications Received</span>
                        <span class="text-gray-900">{{ $totalApplicants }} (100%)</span>
                    </div>
                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-gray-400 h-full rounded-full" style="width: 100%;"></div>
                    </div>
                </div>

                <div class="space-y-1">
                    <div class="flex justify-between text-xs font-semibold">
                        <span class="text-gray-700">2. JPO Pre-Screened Referrals</span>
                        <span class="text-purple-700">{{ $referredCount }} ({{ $pctReferred }}%)</span>
                    </div>
                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-purple-600 h-full rounded-full" style="width: {{ $pctReferred }}%;"></div>
                    </div>
                </div>

                <div class="space-y-1">
                    <div class="flex justify-between text-xs font-semibold">
                        <span class="text-gray-700">3. Under Interview</span>
                        <span class="text-blue-700">{{ $interviewCount }} ({{ $pctInterview }}%)</span>
                    </div>
                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-blue-600 h-full rounded-full" style="width: {{ $pctInterview }}%;"></div>
                    </div>
                </div>

                <div class="space-y-1">
                    <div class="flex justify-between text-xs font-semibold">
                        <span class="text-gray-700">4. Extended Job Offer / Hired</span>
                        <span class="text-green-700">{{ $hiredCount }} ({{ $pctHired }}%)</span>
                    </div>
                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-green-600 h-full rounded-full" style="width: {{ $pctHired }}%;"></div>
                    </div>
                </div>
            </div>

            <div class="p-3 rounded-xl bg-gray-50 border border-gray-200 text-xs text-gray-700">
                <span class="font-bold text-gray-900">Pro-Tip:</span> Review JPO-referred candidates promptly to maintain priority placement listing on TrabaGo.
            </div>
        </div>

    </div>

    <!-- Bottom Row: Top Active Postings & Recent Applicants -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Top Performing Job Vacancies -->
        <div class="bg-white rounded-2xl p-6 border border-gray-200 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Top Job Postings by Influx</h2>
                    <p class="text-xs text-gray-500">Applicant interest across active positions</p>
                </div>
                <a href="{{ route('employer.job-postings') }}" class="text-xs font-semibold text-green-700 hover:text-green-800">
                    Manage Jobs &rarr;
                </a>
            </div>

            @if(count($topJobs) > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($topJobs as $job)
                        <div class="py-3 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $job->status === 'approved' ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ ucfirst($job->status) }}
                                    </span>
                                    <p class="text-xs font-bold text-gray-900 truncate">{{ $job->title }}</p>
                                </div>
                                <p class="text-[11px] text-gray-500 mt-0.5 truncate">
                                    {{ $job->employment_type ?? 'Full-time' }} &bull; Posted {{ $job->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-xs font-bold text-gray-900">{{ $job->applications_count }} applicants</span>
                                <a href="{{ route('employer.job-postings.show', $job->job_id) }}" class="block text-[11px] text-green-700 hover:text-green-800 font-medium">
                                    View details &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-8 text-center text-xs text-gray-500">
                    No active job postings created yet.
                </div>
            @endif
        </div>

        <!-- Recent Applicants / Candidate Stream -->
        <div class="bg-white rounded-2xl p-6 border border-gray-200 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Recent Candidate Influx</h2>
                    <p class="text-xs text-gray-500">Latest jobseekers applying to your vacancies</p>
                </div>
                <a href="{{ route('employer.applications') }}" class="text-xs font-semibold text-green-700 hover:text-green-800">
                    All Applicants &rarr;
                </a>
            </div>

            @if(count($recentApplicants) > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($recentApplicants as $applicant)
                        <div class="py-3 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    @if($applicant->referred_by_jpo)
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                            JPO Endorsed
                                        </span>
                                    @endif
                                    <p class="text-xs font-bold text-gray-900 truncate">
                                        {{ $applicant->jobseeker->first_name ?? 'Candidate' }} {{ $applicant->jobseeker->last_name ?? '' }}
                                    </p>
                                </div>
                                <p class="text-[11px] text-gray-500 mt-0.5 truncate">
                                    Applied for <span class="text-gray-800 font-medium">{{ $applicant->jobPosting->title ?? 'Position' }}</span>
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $applicant->status === 'hired' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($applicant->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-8 text-center text-xs text-gray-500">
                    No applicants received yet.
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
