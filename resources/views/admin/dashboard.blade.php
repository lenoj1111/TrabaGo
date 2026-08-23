@extends('layouts.admin')

@section('title', 'Admin Command Center')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Header / Hero Section -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                        <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        DMDP Central Administration
                    </span>
                    <span class="text-xs text-slate-400 font-medium">{{ now()->format('F d, Y') }}</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Admin Command Center</h1>
                <p class="text-sm text-slate-300">
                    Welcome back, <span class="text-emerald-400 font-bold">{{ Auth::user()->email }}</span>. Direct administrative oversight over job postings, employer accreditations, employee accounts, and manpower placement workflows.
                </p>
            </div>
        </div>

        <!-- 4 Metric Cards (Figure 10 Metrics) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Jobs</span>
                    <p class="text-3xl font-black text-slate-900">{{ $stats['total_jobs'] ?? 0 }}</p>
                    <span class="text-[11px] text-slate-500 font-medium">All listings on portal</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center justify-center text-xl font-black">
                    💼
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pending Review</span>
                    <p class="text-3xl font-black text-amber-600">{{ $stats['pending_jobs'] ?? 0 }}</p>
                    <span class="text-[11px] text-amber-700 font-medium">Awaiting authorization</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-800 border border-amber-200 flex items-center justify-center text-xl font-black">
                    ⏳
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Employers</span>
                    <p class="text-3xl font-black text-teal-700">{{ $stats['total_employers'] ?? 0 }}</p>
                    <span class="text-[11px] text-teal-800 font-medium">Partner companies</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-teal-50 text-teal-800 border border-teal-200 flex items-center justify-center text-xl font-black">
                    🏢
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Applications</span>
                    <p class="text-3xl font-black text-slate-900">{{ $stats['total_applications'] ?? 0 }}</p>
                    <span class="text-[11px] text-slate-500 font-medium">Submitted candidates</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-blue-50 text-blue-800 border border-blue-200 flex items-center justify-center text-xl font-black">
                    📄
                </div>
            </div>

        </div>

        <!-- Primary Admin Workflow Hub (Figure 10) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <a href="{{ route('admin.approvals.index') }}" 
               class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-7 shadow-sm hover:shadow-xl hover:border-emerald-400 transition-all flex flex-col justify-between gap-4 group">
                <div class="space-y-3">
                    <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                        ✅
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-black text-slate-900 group-hover:text-emerald-700 transition-colors">
                                1. Manage Approvals
                            </h3>
                            @if(($stats['pending_jobs'] ?? 0) > 0)
                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold border border-amber-200">
                                    {{ $stats['pending_jobs'] }} Pending
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            Final 3-pillar authorization for Job Postings, PESD Endorsed Accreditations, and Monthly Placement Reports.
                        </p>
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-emerald-800">
                    <span>Open Approvals Center</span>
                    <span>&rarr;</span>
                </div>
            </a>

            <a href="{{ route('admin.users.index') }}" 
               class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-7 shadow-sm hover:shadow-xl hover:border-emerald-400 transition-all flex flex-col justify-between gap-4 group">
                <div class="space-y-3">
                    <div class="h-12 w-12 rounded-2xl bg-blue-50 text-blue-800 border border-blue-200 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                        👥
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 group-hover:text-emerald-700 transition-colors">
                            2. Employee Accounts
                        </h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Create, activate, assign roles (JPO, Trainer, Supervisor, LMO), and manage access permissions for all staff.
                        </p>
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-blue-800">
                    <span>Manage User Accounts</span>
                    <span>&rarr;</span>
                </div>
            </a>

            <a href="{{ route('admin.jobseekers.index') }}" 
               class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-7 shadow-sm hover:shadow-xl hover:border-emerald-400 transition-all flex flex-col justify-between gap-4 group">
                <div class="space-y-3">
                    <div class="h-12 w-12 rounded-2xl bg-purple-50 text-purple-800 border border-purple-200 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                        📊
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 group-hover:text-emerald-700 transition-colors">
                            3. Jobseeker Status
                        </h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Monitor registered candidate demographics, PWD inclusivity, verified skill matrices, and employment placement rate.
                        </p>
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-purple-800">
                    <span>View Status Directory</span>
                    <span>&rarr;</span>
                </div>
            </a>

        </div>

        <!-- Platform Overview & Quick Access Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Quick Actions -->
            <div class="lg:col-span-2 rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="text-lg font-black text-slate-900">System Modules & Operations</h2>
                        <p class="text-xs text-slate-500">Fast access to key operational tasks and administrative modules</p>
                    </div>
                    <span class="text-xs font-bold text-emerald-700">Operational Hub</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="{{ route('admin.job-postings') }}" class="p-4 rounded-2xl border border-slate-100 bg-slate-50 hover:bg-emerald-50/60 hover:border-emerald-200 transition-all group flex items-start gap-3">
                        <div class="h-10 w-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-lg shrink-0 group-hover:scale-105 transition-transform">
                            💼
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900 group-hover:text-emerald-800">Manage Job Postings</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">Publish jobs on behalf of DMDP or review employer listings</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.employers') }}" class="p-4 rounded-2xl border border-slate-100 bg-slate-50 hover:bg-emerald-50/60 hover:border-emerald-200 transition-all group flex items-start gap-3">
                        <div class="h-10 w-10 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center text-lg shrink-0 group-hover:scale-105 transition-transform">
                            🏢
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900 group-hover:text-emerald-800">Employers Registry</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">View accredited companies and accreditation records</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.users.create') }}" class="p-4 rounded-2xl border border-slate-100 bg-slate-50 hover:bg-emerald-50/60 hover:border-emerald-200 transition-all group flex items-start gap-3">
                        <div class="h-10 w-10 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center text-lg shrink-0 group-hover:scale-105 transition-transform">
                            👤
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900 group-hover:text-emerald-800">Create Staff User</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">Provision JPO, Trainer, Supervisor, or LMO staff accounts</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.reports') }}" class="p-4 rounded-2xl border border-slate-100 bg-slate-50 hover:bg-emerald-50/60 hover:border-emerald-200 transition-all group flex items-start gap-3">
                        <div class="h-10 w-10 rounded-xl bg-purple-100 text-purple-800 flex items-center justify-center text-lg shrink-0 group-hover:scale-105 transition-transform">
                            📈
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900 group-hover:text-emerald-800">Analytics & Reports</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">Export placement metrics, user counts, and job trends</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Platform Distribution Overview -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h2 class="text-lg font-black text-slate-900">Platform Overview</h2>
                    <p class="text-xs text-slate-500">Live platform totals across modules</p>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <span class="text-base">👥</span>
                            <span class="text-xs font-bold text-slate-700">Total Jobseekers</span>
                        </div>
                        <span class="text-xs font-black text-slate-900">{{ $stats['total_jobseekers'] ?? 0 }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <span class="text-base">🏢</span>
                            <span class="text-xs font-bold text-slate-700">Registered Employers</span>
                        </div>
                        <span class="text-xs font-black text-slate-900">{{ $stats['total_employers'] ?? 0 }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <span class="text-base">💼</span>
                            <span class="text-xs font-bold text-slate-700">Active Job Postings</span>
                        </div>
                        <span class="text-xs font-black text-slate-900">{{ $stats['total_jobs'] ?? 0 }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-2xl bg-amber-50/70 border border-amber-200/60">
                        <div class="flex items-center gap-2.5">
                            <span class="text-base">⏳</span>
                            <span class="text-xs font-bold text-amber-900">Pending Review</span>
                        </div>
                        <span class="text-xs font-black text-amber-700">{{ $stats['pending_jobs'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection