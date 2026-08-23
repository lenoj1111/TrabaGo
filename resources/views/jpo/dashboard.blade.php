@extends('layouts.jpo')

@section('title', 'JPO Command Center - TrabaGo DMDP')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    DMDP Job Placement Officer Division
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Placement Evaluation Dashboard</h1>
                <p class="text-sm text-slate-300">Evaluate applicant jobseeker qualifications (refer to employers), verify accreditation papers (send to PESD Supervisor), and validate monthly placement reports (send to Admin).</p>
            </div>

            <div class="shrink-0 bg-white/10 backdrop-blur rounded-2xl p-5 border border-white/10 text-center min-w-[150px]">
                <span class="text-xs font-bold text-emerald-300 uppercase tracking-wider">Total Endorsed</span>
                <p class="text-4xl font-black text-emerald-400 mt-0.5">{{ $totalReferredJobseekers }}</p>
                <span class="text-[10px] text-slate-300">{{ $totalHiredReferred }} Hired to Date</span>
            </div>
        </div>

        <!-- 3 Primary Evaluation Pillars (Figure 8) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Pillar 1: Evaluate Jobseekers -> Send to Employer -->
            <a href="{{ route('jpo.evaluations.jobseekers') }}" 
               class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-xl hover:border-emerald-400 transition-all flex flex-col justify-between gap-4 group">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                            👥
                        </div>
                        <span class="rounded-full bg-emerald-100 text-emerald-900 px-3 py-1 text-xs font-black">
                            {{ $pendingJobseekers }} Pending
                        </span>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 group-hover:text-emerald-700 transition-colors">
                            1. Evaluate Jobseekers
                        </h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Assess candidate credentials, verified skills, and match compatibility, then officially refer to Employers.
                        </p>
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-emerald-800">
                    <span>Review Applicants</span>
                    <span>&rarr;</span>
                </div>
            </a>

            <!-- Pillar 2: Evaluate Accreditation Papers -> Send to PESD Supervisor -->
            <a href="{{ route('jpo.evaluations.accreditations') }}" 
               class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-xl hover:border-emerald-400 transition-all flex flex-col justify-between gap-4 group">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="h-12 w-12 rounded-2xl bg-teal-50 text-teal-800 border border-teal-200 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                            📑
                        </div>
                        <span class="rounded-full bg-teal-100 text-teal-900 px-3 py-1 text-xs font-black">
                            {{ $pendingAccreditations }} Pending
                        </span>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 group-hover:text-emerald-700 transition-colors">
                            2. Accreditation Papers
                        </h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Inspect business permits, tax certificates, and legal compliance, then recommend and forward to PESD Supervisor.
                        </p>
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-teal-800">
                    <span>Evaluate Papers</span>
                    <span>&rarr;</span>
                </div>
            </a>

            <!-- Pillar 3: Evaluate Placement Reports -> Send to Admin -->
            <a href="{{ route('jpo.evaluations.placement-reports') }}" 
               class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-xl hover:border-emerald-400 transition-all flex flex-col justify-between gap-4 group">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="h-12 w-12 rounded-2xl bg-blue-50 text-blue-800 border border-blue-200 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                            📊
                        </div>
                        <span class="rounded-full bg-blue-100 text-blue-900 px-3 py-1 text-xs font-black">
                            {{ $pendingPlacementReports }} Pending
                        </span>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 group-hover:text-emerald-700 transition-colors">
                            3. Placement Reports
                        </h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Validate monthly employer hiring compliance reports against DMDP databases and forward to Admin for approval.
                        </p>
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-blue-800">
                    <span>Audit Reports</span>
                    <span>&rarr;</span>
                </div>
            </a>

        </div>

        <!-- Recent Applicants Awaiting JPO Endorsement -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h2 class="text-base font-black text-slate-900">Recent Applications Awaiting JPO Review</h2>
                <a href="{{ route('jpo.evaluations.jobseekers') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900">View All Applications &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($recentApplicants as $app)
                    <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-slate-900 text-white font-bold text-xs flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($app->jobseeker->first_name ?? 'J', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900">{{ $app->jobseeker->first_name }} {{ $app->jobseeker->last_name }}</p>
                                <p class="text-[11px] text-slate-400">Applied for {{ $app->jobPosting->title ?? 'N/A' }} at {{ $app->jobPosting->employer->company_name ?? 'Company' }}</p>
                            </div>
                        </div>

                        <a href="{{ route('jpo.evaluations.jobseekers') }}" class="px-4 py-2 rounded-xl bg-slate-900 text-white hover:bg-emerald-600 text-xs font-bold transition-colors">
                            Evaluate & Refer &rarr;
                        </a>
                    </div>
                @empty
                    <p class="py-8 text-center text-xs text-slate-400 italic">No pending jobseeker applications requiring evaluation right now.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
