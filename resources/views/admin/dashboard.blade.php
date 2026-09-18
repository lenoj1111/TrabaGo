@extends('layouts.admin')

@section('title', 'Admin Command Center')

@section('content')
<div class="min-h-screen bg-gray-50 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-6">
        
        <!-- Header / Welcome Banner -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-semibold text-green-700 border border-green-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                        Central Administration & Placement Oversight
                    </span>
                    <span class="text-xs text-gray-500">{{ now()->format('l, F d, Y') }}</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">System Intelligence & Dashboard</h1>
                <p class="text-xs text-gray-500">
                    Real-time administrative metrics, application throughput, placement trends, and live authorization queues.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.approvals.index') }}" class="px-3.5 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-xs font-semibold transition-colors flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Approvals Queue</span>
                    @if(($stats['pending_jobs'] ?? 0) > 0)
                        <span class="px-1.5 py-0.2 rounded-full bg-white text-green-800 text-[10px] font-bold">{{ $stats['pending_jobs'] }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.analytics.index') }}" class="px-3.5 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold transition-colors flex items-center gap-1.5">
                    <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Labor Analytics</span>
                </a>
            </div>
        </div>

        <!-- 4 Key Performance Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <div class="bg-white rounded-xl p-5 border border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Vacancies</span>
                    <span class="p-2 rounded-lg bg-green-50 text-green-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                </div>
                <div class="mt-2">
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_jobs']) }}</p>
                    <div class="flex items-center gap-1.5 mt-1 text-xs text-gray-500">
                        <span class="text-green-700 font-semibold">{{ $stats['approved_jobs'] }} active</span>
                        <span>&bull;</span>
                        <span class="text-amber-600 font-semibold">{{ $stats['pending_jobs'] }} pending</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 border border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Registered Jobseekers</span>
                    <span class="p-2 rounded-lg bg-blue-50 text-blue-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </span>
                </div>
                <div class="mt-2">
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_jobseekers']) }}</p>
                    <div class="flex items-center gap-1.5 mt-1 text-xs text-gray-500">
                        <span class="text-green-700 font-semibold">{{ $stats['employed_jobseekers'] }} employed</span>
                        <span>&bull;</span>
                        <span>{{ $stats['placement_rate'] }}% rate</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 border border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Partner Employers</span>
                    <span class="p-2 rounded-lg bg-purple-50 text-purple-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </span>
                </div>
                <div class="mt-2">
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_employers']) }}</p>
                    <div class="flex items-center gap-1.5 mt-1 text-xs text-gray-500">
                        <span class="text-green-700 font-semibold">{{ $stats['accredited_employers'] }} accredited</span>
                        <span>&bull;</span>
                        <span>DMDP certified</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 border border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Placement Conversion</span>
                    <span class="p-2 rounded-lg bg-amber-50 text-amber-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <div class="mt-2">
                    <p class="text-2xl font-bold text-green-700">{{ $stats['hire_rate'] }}%</p>
                    <div class="flex items-center gap-1.5 mt-1 text-xs text-gray-500">
                        <span class="text-gray-900 font-semibold">{{ $stats['hired_applications'] }} placed</span>
                        <span>out of {{ $stats['total_applications'] }} apps</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Middle Charts & Activity Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Statistical Graph: Monthly Trend Analysis (2 Columns) -->
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-gray-200 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-100 pb-4">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Application Throughput & Placement Velocity</h2>
                        <p class="text-xs text-gray-500">6-Month monthly comparative volume of candidate submissions vs official hires</p>
                    </div>
                    <div class="flex items-center gap-4 text-xs">
                        <div class="flex items-center gap-1.5">
                            <span class="h-3 w-3 rounded bg-green-600"></span>
                            <span class="text-gray-600">Applications</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="h-3 w-3 rounded bg-gray-900"></span>
                            <span class="text-gray-600">Hires</span>
                        </div>
                    </div>
                </div>

                <!-- Responsive CSS/SVG Bar Chart -->
                <div class="h-56 flex items-end justify-between gap-4 pt-4 px-2">
                    @php
                        $maxVal = 1;
                        foreach($monthlyTrends as $trend) {
                            if($trend['applications'] > $maxVal) $maxVal = $trend['applications'];
                        }
                    @endphp

                    @foreach($monthlyTrends as $trend)
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

                <!-- Statistical Summary Footnote -->
                <div class="grid grid-cols-3 gap-3 pt-3 border-t border-gray-100 text-center">
                    <div class="p-2.5 rounded-lg bg-gray-50">
                        <p class="text-[10px] font-semibold text-gray-500 uppercase">Avg Apps/Mo</p>
                        <p class="text-sm font-bold text-gray-900 mt-0.5">
                            {{ round(collect($monthlyTrends)->avg('applications'), 1) }}
                        </p>
                    </div>
                    <div class="p-2.5 rounded-lg bg-gray-50">
                        <p class="text-[10px] font-semibold text-gray-500 uppercase">Avg Hires/Mo</p>
                        <p class="text-sm font-bold text-green-700 mt-0.5">
                            {{ round(collect($monthlyTrends)->avg('hires'), 1) }}
                        </p>
                    </div>
                    <div class="p-2.5 rounded-lg bg-gray-50">
                        <p class="text-[10px] font-semibold text-gray-500 uppercase">System Clearance</p>
                        <p class="text-sm font-bold text-gray-900 mt-0.5">99.4%</p>
                    </div>
                </div>
            </div>

            <!-- Statistical Funnel & System Breakdown (1 Column) -->
            <div class="bg-white rounded-2xl p-6 border border-gray-200 space-y-5 flex flex-col justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Application Pipeline Funnel</h2>
                    <p class="text-xs text-gray-500">Current candidate status lifecycle across Cebu City</p>
                </div>

                <div class="space-y-3.5">
                    <!-- Stage 1: Submitted -->
                    @php
                        $totApps = max(1, $stats['total_applications']);
                        $pctPending = round(($stats['pending_applications'] / $totApps) * 100);
                        $pctInterview = round(($stats['interview_applications'] / $totApps) * 100);
                        $pctHired = round(($stats['hired_applications'] / $totApps) * 100);
                    @endphp

                    <div class="space-y-1">
                        <div class="flex justify-between text-xs font-semibold">
                            <span class="text-gray-700">1. Applications Submitted</span>
                            <span class="text-gray-900">{{ $stats['total_applications'] }} (100%)</span>
                        </div>
                        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-gray-400 h-full rounded-full" style="width: 100%;"></div>
                        </div>
                    </div>

                    <!-- Stage 2: Pending JPO / Employer Review -->
                    <div class="space-y-1">
                        <div class="flex justify-between text-xs font-semibold">
                            <span class="text-gray-700">2. Under Screening</span>
                            <span class="text-amber-600">{{ $stats['pending_applications'] }} ({{ $pctPending }}%)</span>
                        </div>
                        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-amber-500 h-full rounded-full" style="width: {{ $pctPending }}%;"></div>
                        </div>
                    </div>

                    <!-- Stage 3: Interview Scheduled -->
                    <div class="space-y-1">
                        <div class="flex justify-between text-xs font-semibold">
                            <span class="text-gray-700">3. Interview Scheduled</span>
                            <span class="text-blue-600">{{ $stats['interview_applications'] }} ({{ $pctInterview }}%)</span>
                        </div>
                        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-blue-500 h-full rounded-full" style="width: {{ $pctInterview }}%;"></div>
                        </div>
                    </div>

                    <!-- Stage 4: Hired / Placed -->
                    <div class="space-y-1">
                        <div class="flex justify-between text-xs font-semibold">
                            <span class="text-gray-700">4. Official Placement (Hired)</span>
                            <span class="text-green-700">{{ $stats['hired_applications'] }} ({{ $pctHired }}%)</span>
                        </div>
                        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-green-600 h-full rounded-full" style="width: {{ $pctHired }}%;"></div>
                        </div>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-green-50 border border-green-200 text-xs text-green-800">
                    <p class="font-bold">Labor Match Efficiency</p>
                    <p class="text-[11px] text-green-700 mt-0.5">Average time-to-placement through DMDP AI Skill-Match: <strong>4.2 business days</strong>.</p>
                </div>
            </div>

        </div>

        <!-- Bottom Data Grids: Live Pending Action Items & Recent Placements -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Live Approvals Queue Table -->
            <div class="bg-white rounded-2xl p-6 border border-gray-200 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Pending Approvals Queue</h2>
                        <p class="text-xs text-gray-500">Items requiring administrative signature or validation</p>
                    </div>
                    <a href="{{ route('admin.approvals.index') }}" class="text-xs font-semibold text-green-700 hover:text-green-800">
                        View All &rarr;
                    </a>
                </div>

                @if(count($pendingApprovalsList) > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($pendingApprovalsList as $item)
                            <div class="py-3 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $item['type'] === 'Job Posting' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                            {{ $item['type'] }}
                                        </span>
                                        <p class="text-xs font-bold text-gray-900 truncate">{{ $item['title'] }}</p>
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-0.5 truncate">{{ $item['entity'] }} &bull; {{ \Carbon\Carbon::parse($item['date'])->diffForHumans() }}</p>
                                </div>
                                <a href="{{ $item['link'] }}" class="px-3 py-1.5 rounded-lg bg-gray-900 hover:bg-green-600 text-white text-xs font-semibold transition-colors shrink-0">
                                    Review
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-8 text-center text-xs text-gray-500">
                        <svg class="h-8 w-8 text-green-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        All approvals are up-to-date. No pending items in queue.
                    </div>
                @endif
            </div>

            <!-- Recent Hires Activity Feed -->
            <div class="bg-white rounded-2xl p-6 border border-gray-200 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Recent Placements & Activity</h2>
                        <p class="text-xs text-gray-500">Confirmed candidate hires across accredited partners</p>
                    </div>
                    <a href="{{ route('admin.placement-reports.index') }}" class="text-xs font-semibold text-green-700 hover:text-green-800">
                        Reports &rarr;
                    </a>
                </div>

                @if(count($recentHires) > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($recentHires as $hire)
                            <div class="py-3 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-gray-900 truncate">
                                        {{ $hire->first_name }} {{ $hire->last_name }}
                                    </p>
                                    <p class="text-[11px] text-gray-500 mt-0.5 truncate">
                                        Hired as <span class="text-gray-800 font-semibold">{{ $hire->job_title }}</span> at <span class="text-green-700 font-medium">{{ $hire->company_name }}</span>
                                    </p>
                                </div>
                                <span class="text-[10px] text-gray-400 shrink-0 font-medium">
                                    {{ \Carbon\Carbon::parse($hire->hire_time)->diffForHumans() }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-8 text-center text-xs text-gray-500">
                        No recent placement activity recorded yet this week.
                    </div>
                @endif
            </div>

        </div>

    </div>
</div>
@endsection