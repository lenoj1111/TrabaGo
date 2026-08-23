@extends('layouts.lmo')

@section('title', 'Labor Market Information Dashboard - TrabaGo DMDP')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Labor Market Intelligence & Supervision (Figure 13)
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Labor Market Oversight</h1>
                <p class="text-sm text-slate-300">Supervise the overall jobseeker workflow (training, job applications, evaluations), monitor labor market supply vs demand, and ensure employment readiness and placement.</p>
            </div>
        </div>

        <!-- Jobseeker Workflow Supervision Funnel (Figure 13) -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-lg font-black text-slate-900">Jobseeker Workflow Supervision Funnel</h2>
                    <p class="text-xs text-slate-500">Figure 13: Monitoring jobseekers navigating through Training &rarr; Applications &rarr; JPO Evaluations &rarr; Placement</p>
                </div>
                <a href="{{ route('lmo.jobseekers.supervise') }}" class="text-xs font-bold text-emerald-700 hover:underline">Monitor All &rarr;</a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-center space-y-1">
                    <span class="text-xs font-bold text-slate-400">1. Registered</span>
                    <p class="text-2xl font-black text-slate-900">{{ $totalJobseekers }}</p>
                    <span class="text-[10px] text-slate-500">Talent Pool</span>
                </div>

                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-center space-y-1">
                    <span class="text-xs font-bold text-amber-700">2. In Training</span>
                    <p class="text-2xl font-black text-amber-900">{{ $inTraining }}</p>
                    <span class="text-[10px] text-amber-600">Active Learners</span>
                </div>

                <div class="p-4 rounded-2xl bg-teal-50 border border-teal-200 text-center space-y-1">
                    <span class="text-xs font-bold text-teal-700">3. Certified</span>
                    <p class="text-2xl font-black text-teal-900">{{ $certifiedSkillsCount }}</p>
                    <span class="text-[10px] text-teal-600">Skills Verified</span>
                </div>

                <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-center space-y-1">
                    <span class="text-xs font-bold text-blue-700">4. Applied</span>
                    <p class="text-2xl font-black text-blue-900">{{ $totalApplications }}</p>
                    <span class="text-[10px] text-blue-600">Job Submissions</span>
                </div>

                <div class="p-4 rounded-2xl bg-purple-50 border border-purple-200 text-center space-y-1">
                    <span class="text-xs font-bold text-purple-700">5. JPO Referred</span>
                    <p class="text-2xl font-black text-purple-900">{{ $jpoEvaluated }}</p>
                    <span class="text-[10px] text-purple-600">Endorsed</span>
                </div>

                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-center space-y-1 shadow-sm">
                    <span class="text-xs font-bold text-emerald-800">6. Hired Placed</span>
                    <p class="text-2xl font-black text-emerald-700">{{ $hiredPlacements }}</p>
                    <span class="text-[10px] text-emerald-800 font-bold">Employed</span>
                </div>

            </div>
        </div>

        <!-- Labor Market Intelligence & Inclusion Metrics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Left: Top Verified Skills Supply -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <h2 class="text-base font-black text-slate-900">Labor Market Skills Supply</h2>
                    <span class="text-xs text-slate-400 font-semibold">Verified Jobseeker Skills</span>
                </div>

                <div class="space-y-3">
                    @forelse($topSupplySkills as $skill)
                        <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="text-xs font-bold text-slate-800">{{ $skill->skill_name }}</span>
                            <span class="rounded-full bg-emerald-100 text-emerald-800 px-3 py-0.5 text-xs font-black">
                                {{ $skill->total_holders }} jobseekers
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic text-center py-6">No skills recorded yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- Right: Inclusion & Diversity Demographics -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <h2 class="text-base font-black text-slate-900">Inclusivity & Social Demographics</h2>
                    <span class="text-xs text-slate-400 font-semibold">Cebu City DMDP</span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-purple-50 border border-purple-200 space-y-1">
                        <span class="text-xs font-bold text-purple-900">♿ PWD Jobseekers</span>
                        <p class="text-3xl font-black text-purple-950">{{ $pwdJobseekers }}</p>
                        <span class="text-[10px] text-purple-700">Persons with Disabilities</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 space-y-1">
                        <span class="text-xs font-bold text-amber-900">📋 4Ps Beneficiaries</span>
                        <p class="text-3xl font-black text-amber-950">{{ $fourPsJobseekers }}</p>
                        <span class="text-[10px] text-amber-700">Pantawid Pamilya</span>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 text-xs text-emerald-950 space-y-1">
                    <strong class="block">Market Demand Summary:</strong>
                    <p class="text-emerald-800">
                        {{ $totalActiveJobs }} live vacancies across {{ $totalEmployers }} registered enterprises in Cebu City.
                    </p>
                </div>
            </div>

        </div>

        <!-- Recent Jobseekers Monitored -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h2 class="text-lg font-black text-slate-900">Recent Jobseeker Workflow Activities</h2>
                <a href="{{ route('lmo.jobseekers.supervise') }}" class="text-xs font-bold text-emerald-700 hover:underline">View All &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-400 uppercase tracking-wider font-bold">
                            <th class="pb-3 px-3">Jobseeker</th>
                            <th class="pb-3 px-3">Status</th>
                            <th class="pb-3 px-3">Skills</th>
                            <th class="pb-3 px-3">Trainings</th>
                            <th class="pb-3 px-3">Applications</th>
                            <th class="pb-3 px-3 text-right">Outcome</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($recentWorkflows as $w)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-3 font-bold text-slate-900">
                                    {{ $w->first_name }} {{ $w->last_name }}
                                    <span class="text-[11px] text-slate-400 block font-normal">{{ $w->user_email }}</span>
                                </td>
                                <td class="py-4 px-3">
                                    <span class="badge rounded-lg bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700">
                                        {{ $w->employment_status ?: 'Active Seeking' }}
                                    </span>
                                </td>
                                <td class="py-4 px-3 font-bold text-emerald-700">
                                    {{ $w->skills_count }} verified
                                </td>
                                <td class="py-4 px-3 font-bold text-teal-700">
                                    {{ $w->trainings_count }} enrolled
                                </td>
                                <td class="py-4 px-3 font-bold text-blue-700">
                                    {{ $w->apps_count }} applied
                                </td>
                                <td class="py-4 px-3 text-right">
                                    @if($w->hired_count > 0)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 px-2.5 py-0.5 text-xs font-black">
                                            ✓ Hired
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs">In Progress</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
