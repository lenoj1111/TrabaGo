@extends('layouts.admin')

@section('title', 'Platform Reports & Analytics')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="w-full max-w-[1680px] mx-auto space-y-8">

        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    DMDP Intelligence & Statistical Analytics
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Platform Reports & Analytics</h1>
                <p class="text-sm text-slate-300">
                    Comprehensive platform telemetry, employment placement performance, and system user demographic metrics.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('admin.placement-reports.index') }}" 
                   class="inline-flex items-center gap-2 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/20 px-5 py-3 text-xs font-bold text-white transition-all">
                    <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Placement Reports Directory &rarr;</span>
                </a>
                <button onclick="window.print()" 
                        class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 px-5 py-3 text-xs font-black text-white shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Print / Export Report</span>
                </button>
            </div>
        </div>

        <!-- 4 Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Jobs</span>
                    <p class="text-3xl font-black text-slate-900">{{ $stats['total_jobs'] ?? 0 }}</p>
                    <span class="text-[11px] text-slate-500">All registered postings</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Corporate Employers</span>
                    <p class="text-3xl font-black text-emerald-700">{{ $stats['total_employers'] ?? 0 }}</p>
                    <span class="text-[11px] text-emerald-800">Partner companies</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Jobseekers</span>
                    <p class="text-3xl font-black text-blue-700">{{ $stats['total_jobseekers'] ?? 0 }}</p>
                    <span class="text-[11px] text-blue-800">Registered candidates</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-blue-50 text-blue-800 border border-blue-200 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Candidate Applications</span>
                    <p class="text-3xl font-black text-purple-700">{{ $stats['total_applications'] ?? 0 }}</p>
                    <span class="text-[11px] text-purple-800">Application pipeline</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-purple-50 text-purple-800 border border-purple-200 flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
        </div>

        @php
            $monthlyMax = max(collect($charts['monthly']['applications'])
                ->merge($charts['monthly']['hires'])
                ->merge($charts['monthly']['training_starts'])
                ->max() ?? 0, 1);
            $applicationMax = max(collect($charts['application_statuses'])->max('total') ?? 0, 1);
            $trainingMax = max(collect($charts['training_statuses'])->max('total') ?? 0, 1);
            $skillTypeMax = max($charts['skill_types']['technical'], $charts['skill_types']['21st_century'], 1);
        @endphp

        <section class="space-y-4" aria-label="Platform trend charts">
            <div class="border-b border-slate-200 pb-3">
                <h2 class="text-lg font-black text-slate-900">Workforce & Service Trends</h2>
                <p class="text-xs text-slate-500">Live activity from the most recent 12 months and current workflow status.</p>
            </div>

            <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
                <article class="border border-slate-200 bg-white p-5 shadow-sm xl:col-span-2">
                    <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Monthly Platform Activity</h3>
                            <p class="mt-1 text-[11px] text-slate-500">Applications, recorded hires, and training starts</p>
                        </div>
                        <div class="flex flex-wrap gap-3 text-[10px] font-semibold text-slate-600">
                            <span class="inline-flex items-center gap-1.5"><i class="h-2.5 w-2.5 bg-emerald-600"></i>Applications</span>
                            <span class="inline-flex items-center gap-1.5"><i class="h-2.5 w-2.5 bg-blue-600"></i>Hires</span>
                            <span class="inline-flex items-center gap-1.5"><i class="h-2.5 w-2.5 bg-amber-500"></i>Training starts</span>
                        </div>
                    </div>
                    <div class="grid h-48 grid-cols-12 items-end gap-2 border-b border-l border-slate-200 px-2 pb-1">
                        @foreach($charts['monthly']['labels'] as $index => $monthLabel)
                            @php
                                $applicationValue = $charts['monthly']['applications'][$index];
                                $hireValue = $charts['monthly']['hires'][$index];
                                $trainingValue = $charts['monthly']['training_starts'][$index];
                            @endphp
                            <div class="flex h-full min-w-0 flex-col justify-end gap-1" title="{{ $monthLabel }}: {{ $applicationValue }} applications, {{ $hireValue }} hires, {{ $trainingValue }} training starts">
                                <div class="flex h-[88%] items-end justify-center gap-0.5">
                                    <span class="w-1/4 bg-emerald-600" style="height: {{ $applicationValue > 0 ? max(3, round(($applicationValue / $monthlyMax) * 100)) : 1 }}%"></span>
                                    <span class="w-1/4 bg-blue-600" style="height: {{ $hireValue > 0 ? max(3, round(($hireValue / $monthlyMax) * 100)) : 1 }}%"></span>
                                    <span class="w-1/4 bg-amber-500" style="height: {{ $trainingValue > 0 ? max(3, round(($trainingValue / $monthlyMax) * 100)) : 1 }}%"></span>
                                </div>
                                <span class="truncate text-center text-[9px] text-slate-500">{{ \Illuminate\Support\Carbon::createFromFormat('M Y', $monthLabel)->format('M') }}</span>
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-2 text-right text-[10px] text-slate-400">Hover a month to compare exact values.</p>
                </article>

                <article class="border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-900">Skill Category Coverage</h3>
                    <p class="mt-1 text-[11px] text-slate-500">Registered skill records by category</p>
                    @foreach(['technical' => 'Technical', '21st_century' => '21st Century'] as $key => $label)
                        @php $value = $charts['skill_types'][$key]; @endphp
                        <div class="mt-5 space-y-1.5">
                            <div class="flex justify-between text-xs"><span class="font-semibold text-slate-700">{{ $label }}</span><strong>{{ number_format($value) }}</strong></div>
                            <div class="h-3 overflow-hidden bg-slate-100"><div class="h-full {{ $key === 'technical' ? 'bg-cyan-600' : 'bg-emerald-600' }}" style="width: {{ round(($value / $skillTypeMax) * 100) }}%"></div></div>
                        </div>
                    @endforeach
                </article>

                <article class="border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-900">Application Pipeline</h3>
                    <p class="mt-1 text-[11px] text-slate-500">Current applications by workflow stage</p>
                    @forelse($charts['application_statuses'] as $status)
                        <div class="mt-4 space-y-1">
                            <div class="flex justify-between gap-2 text-[11px]"><span class="truncate capitalize text-slate-600">{{ str_replace('_', ' ', $status->status) }}</span><strong>{{ number_format($status->total) }}</strong></div>
                            <div class="h-2 overflow-hidden bg-slate-100"><div class="h-full bg-blue-600" style="width: {{ round(($status->total / $applicationMax) * 100) }}%"></div></div>
                        </div>
                    @empty
                        <p class="mt-6 text-xs text-slate-500">No application records yet.</p>
                    @endforelse
                </article>

                <article class="border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-900">Training Progress</h3>
                    <p class="mt-1 text-[11px] text-slate-500">Enrollments by current status</p>
                    @forelse($charts['training_statuses'] as $status)
                        <div class="mt-4 space-y-1">
                            <div class="flex justify-between gap-2 text-[11px]"><span class="truncate capitalize text-slate-600">{{ str_replace('_', ' ', $status->status) }}</span><strong>{{ number_format($status->total) }}</strong></div>
                            <div class="h-2 overflow-hidden bg-slate-100"><div class="h-full bg-amber-500" style="width: {{ round(($status->total / $trainingMax) * 100) }}%"></div></div>
                        </div>
                    @empty
                        <p class="mt-6 text-xs text-slate-500">No training enrollment records yet.</p>
                    @endforelse
                </article>
            </div>
        </section>

        <!-- Breakdown Analytics Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Jobs by Status Breakdown -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h2 class="text-lg font-black text-slate-900">Job Postings by Status</h2>
                    <p class="text-xs text-slate-500">Distribution of job listings across approval lifecycle</p>
                </div>

                @php
                    $totalJobs = max($stats['total_jobs'] ?? 0, 1);
                    $approvedPct = round((($stats['approved'] ?? 0) / $totalJobs) * 100);
                    $pendingPct = round((($stats['pending_jobs'] ?? 0) / $totalJobs) * 100);
                    $closedPct = round((($stats['closed'] ?? 0) / $totalJobs) * 100);
                    $rejectedPct = round((($stats['rejected'] ?? 0) / $totalJobs) * 100);
                @endphp

                <div class="space-y-4">
                    <!-- Approved -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs font-bold">
                            <span class="text-emerald-700 inline-flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Approved & Active</span>
                            <span class="text-slate-900">{{ $stats['approved'] ?? 0 }} ({{ $approvedPct }}%)</span>
                        </div>
                        <div class="h-2.5 w-full rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $approvedPct }}%"></div>
                        </div>
                    </div>

                    <!-- Pending -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs font-bold">
                            <span class="text-amber-700 inline-flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Pending Admin Authorization</span>
                            <span class="text-slate-900">{{ $stats['pending_jobs'] ?? 0 }} ({{ $pendingPct }}%)</span>
                        </div>
                        <div class="h-2.5 w-full rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-full" style="width: {{ $pendingPct }}%"></div>
                        </div>
                    </div>

                    <!-- Closed -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs font-bold">
                            <span class="text-slate-600 inline-flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg> Closed & Filled</span>
                            <span class="text-slate-900">{{ $stats['closed'] ?? 0 }} ({{ $closedPct }}%)</span>
                        </div>
                        <div class="h-2.5 w-full rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-slate-400 rounded-full" style="width: {{ $closedPct }}%"></div>
                        </div>
                    </div>

                    <!-- Rejected -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs font-bold">
                            <span class="text-rose-700 inline-flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg> Rejected / Returned</span>
                            <span class="text-slate-900">{{ $stats['rejected'] ?? 0 }} ({{ $rejectedPct }}%)</span>
                        </div>
                        <div class="h-2.5 w-full rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-rose-500 rounded-full" style="width: {{ $rejectedPct }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Platform User Population Distribution -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h2 class="text-lg font-black text-slate-900">User Population Distribution</h2>
                    <p class="text-xs text-slate-500">Breakdown of system accounts by role categories</p>
                </div>

                <div class="space-y-3">
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">Registered Jobseekers</h4>
                                <p class="text-[10px] text-slate-500">Active candidates seeking employment</p>
                            </div>
                        </div>
                        <span class="text-sm font-black text-slate-900">{{ $stats['total_jobseekers'] ?? 0 }}</span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center">
                                <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">Corporate Employers</h4>
                                <p class="text-[10px] text-slate-500">Accredited partner companies</p>
                            </div>
                        </div>
                        <span class="text-sm font-black text-slate-900">{{ $stats['total_employers'] ?? 0 }}</span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-xl bg-purple-100 text-purple-800 flex items-center justify-center">
                                <svg class="w-4 h-4 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">DMDP Staff Officers</h4>
                                <p class="text-[10px] text-slate-500">JPO, Trainers, Supervisor, and LMO</p>
                            </div>
                        </div>
                        <span class="text-sm font-black text-slate-900">{{ $stats['staff_count'] ?? 0 }}</span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-xl bg-rose-100 text-rose-800 flex items-center justify-center">
                                <svg class="w-4 h-4 text-rose-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">Super Administrators</h4>
                                <p class="text-[10px] text-slate-500">Full system access & oversight</p>
                            </div>
                        </div>
                        <span class="text-sm font-black text-slate-900">{{ $stats['admin_count'] ?? 0 }}</span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-emerald-50/60 border border-emerald-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center">
                                <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">Placement Reports Filed</h4>
                                <p class="text-[10px] text-slate-500">{{ $stats['approved_placements'] ?? 0 }} approved & archived into PESO records</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-black text-emerald-800">{{ $stats['total_placement_reports'] ?? 0 }}</span>
                            <a href="{{ route('admin.placement-reports.index') }}" class="text-[10px] text-emerald-700 hover:underline font-bold">View &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <section class="space-y-4" aria-label="Printable administrative reports">
            <div class="flex flex-col gap-2 border-b border-slate-200 pb-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-lg font-black text-slate-900">Printable Administrative Reports</h2>
                    <p class="text-xs text-slate-500">Generate filtered reports for workforce, employers, placements, and skills.</p>
                </div>
                <span class="text-[11px] font-semibold text-slate-500">Print dialog supports Save as PDF</span>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                @php
                    $printReports = [
                        ['type' => 'jobseekers', 'title' => 'Registered Jobseekers', 'description' => 'Employment status, barangay, skills, and registration date.'],
                        ['type' => 'employers', 'title' => 'Employer Registry', 'description' => 'Accreditation status, registration date, and posting activity.'],
                        ['type' => 'hired', 'title' => 'Hired Jobseekers', 'description' => 'Recorded hires, employer, position, and hiring date.'],
                        ['type' => 'specialized-skills', 'title' => 'Specialized Skills Inventory', 'description' => 'Technical and 21st-century skills across the workforce.'],
                        ['type' => 'barangay-skills', 'title' => 'Barangay Skills Distribution', 'description' => 'Skill category and candidate counts by barangay.'],
                        ['type' => 'training', 'title' => 'Training Enrollment & Completion', 'description' => 'Enrollment volume, completion status, pass counts, and certificates.'],
                        ['type' => 'application-funnel', 'title' => 'Application Pipeline', 'description' => 'Application counts and share by current workflow stage.'],
                        ['type' => 'hiring-trends', 'title' => 'Monthly Hiring Trends', 'description' => 'Recorded hires grouped by hiring month.'],
                    ];
                @endphp
                @foreach($printReports as $reportCard)
                    <article class="flex flex-col justify-between gap-4 border border-slate-200 bg-white p-4 shadow-sm">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">{{ $reportCard['title'] }}</h3>
                            <p class="mt-1 text-xs leading-relaxed text-slate-500">{{ $reportCard['description'] }}</p>
                        </div>
                        <a href="{{ route('admin.reports.print', ['type' => $reportCard['type']]) }}" class="inline-flex w-fit items-center gap-2 rounded-lg bg-slate-900 px-3.5 py-2 text-xs font-bold text-white hover:bg-emerald-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2m-12-4h12v8H6z"/></svg>
                            Open report
                        </a>
                    </article>
                @endforeach
            </div>
        </section>

    </div>
</div>
@endsection