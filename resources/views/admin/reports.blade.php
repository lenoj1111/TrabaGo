@extends('layouts.admin')

@section('title', 'Platform Reports & Analytics')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">

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
                <button onclick="window.print()" 
                        class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 px-5 py-3 text-xs font-black text-white shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                    <span>🖨️ Print / Export Report</span>
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
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center justify-center text-xl font-black">
                    💼
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Corporate Employers</span>
                    <p class="text-3xl font-black text-teal-700">{{ $stats['total_employers'] ?? 0 }}</p>
                    <span class="text-[11px] text-teal-800">Partner companies</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-teal-50 text-teal-800 border border-teal-200 flex items-center justify-center text-xl font-black">
                    🏢
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Jobseekers</span>
                    <p class="text-3xl font-black text-blue-700">{{ $stats['total_jobseekers'] ?? 0 }}</p>
                    <span class="text-[11px] text-blue-800">Registered candidates</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-blue-50 text-blue-800 border border-blue-200 flex items-center justify-center text-xl font-black">
                    👥
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Candidate Applications</span>
                    <p class="text-3xl font-black text-purple-700">{{ $stats['total_applications'] ?? 0 }}</p>
                    <span class="text-[11px] text-purple-800">Application pipeline</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-purple-50 text-purple-800 border border-purple-200 flex items-center justify-center text-xl font-black">
                    📄
                </div>
            </div>
        </div>

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
                            <span class="text-emerald-700">✓ Approved & Active</span>
                            <span class="text-slate-900">{{ $stats['approved'] ?? 0 }} ({{ $approvedPct }}%)</span>
                        </div>
                        <div class="h-2.5 w-full rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $approvedPct }}%"></div>
                        </div>
                    </div>

                    <!-- Pending -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs font-bold">
                            <span class="text-amber-700">⏳ Pending Admin Authorization</span>
                            <span class="text-slate-900">{{ $stats['pending_jobs'] ?? 0 }} ({{ $pendingPct }}%)</span>
                        </div>
                        <div class="h-2.5 w-full rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-full" style="width: {{ $pendingPct }}%"></div>
                        </div>
                    </div>

                    <!-- Closed -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs font-bold">
                            <span class="text-slate-600">🔒 Closed & Filled</span>
                            <span class="text-slate-900">{{ $stats['closed'] ?? 0 }} ({{ $closedPct }}%)</span>
                        </div>
                        <div class="h-2.5 w-full rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-slate-400 rounded-full" style="width: {{ $closedPct }}%"></div>
                        </div>
                    </div>

                    <!-- Rejected -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs font-bold">
                            <span class="text-rose-700">✕ Rejected / Returned</span>
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
                            <div class="h-8 w-8 rounded-xl bg-blue-100 text-blue-800 font-bold text-xs flex items-center justify-center">
                                👥
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
                            <div class="h-8 w-8 rounded-xl bg-teal-100 text-teal-800 font-bold text-xs flex items-center justify-center">
                                🏢
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
                            <div class="h-8 w-8 rounded-xl bg-purple-100 text-purple-800 font-bold text-xs flex items-center justify-center">
                                🏛️
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
                            <div class="h-8 w-8 rounded-xl bg-rose-100 text-rose-800 font-bold text-xs flex items-center justify-center">
                                🛡️
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">Super Administrators</h4>
                                <p class="text-[10px] text-slate-500">Full system access & oversight</p>
                            </div>
                        </div>
                        <span class="text-sm font-black text-slate-900">{{ $stats['admin_count'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection