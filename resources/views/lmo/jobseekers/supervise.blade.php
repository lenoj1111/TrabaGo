@extends('layouts.lmo')

@section('title', 'Supervise Jobseeker Workflow - Labor Market Info Officer')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Figure 13: Supervise Jobseeker Workflow
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Jobseeker Workflow Supervision</h1>
                <p class="text-sm text-slate-300">Monitor and supervise how jobseekers navigate through training programs, certifications, job vacancy applications, JPO evaluations, and employer hiring placement.</p>
            </div>

            <div class="shrink-0 bg-white/10 backdrop-blur rounded-2xl p-5 border border-white/10 text-center min-w-[150px]">
                <span class="text-xs font-bold text-emerald-300 uppercase tracking-wider">Supervised Pool</span>
                <p class="text-3xl font-black text-emerald-400 mt-0.5">{{ $jobseekers->total() }}</p>
                <span class="text-[10px] text-slate-300">Active Talent</span>
            </div>
        </div>

        <!-- Filter & Search Controls -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="GET" action="{{ route('lmo.jobseekers.supervise') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
                
                <div class="sm:col-span-5">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, or employment status..."
                           class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>

                <div class="sm:col-span-4">
                    <select name="stage" onchange="this.form.submit()"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none">
                        <option value="">All Workflow Stages</option>
                        <option value="in_training" {{ request('stage') === 'in_training' ? 'selected' : '' }}>📚 Currently in Training</option>
                        <option value="certified" {{ request('stage') === 'certified' ? 'selected' : '' }}>🎓 Skills Certified</option>
                        <option value="applied" {{ request('stage') === 'applied' ? 'selected' : '' }}>💼 Job Applications Filed</option>
                        <option value="jpo_referred" {{ request('stage') === 'jpo_referred' ? 'selected' : '' }}>📋 JPO Referred to Employer</option>
                        <option value="hired" {{ request('stage') === 'hired' ? 'selected' : '' }}>🏆 Placed / Hired</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700">
                        <input type="checkbox" name="pwd_only" value="1" {{ request('pwd_only') == 1 ? 'checked' : '' }} onchange="this.form.submit()"
                               class="rounded text-emerald-600 focus:ring-emerald-500">
                        <span>♿ PWD Only</span>
                    </label>
                </div>

                <div class="sm:col-span-1">
                    <a href="{{ route('lmo.jobseekers.supervise') }}" class="w-full py-3 rounded-2xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-bold flex items-center justify-center">
                        Reset
                    </a>
                </div>

            </form>
        </div>

        <!-- Supervised Jobseekers Directory Table -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h2 class="text-lg font-black text-slate-900">Jobseeker Workflow Supervision Registry</h2>
                <span class="text-xs font-bold text-slate-500">Figure 13: Complete End-to-End Oversight</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-400 uppercase tracking-wider font-bold">
                            <th class="pb-3 px-3">Jobseeker</th>
                            <th class="pb-3 px-3">Inclusivity / Status</th>
                            <th class="pb-3 px-3">Training Progress</th>
                            <th class="pb-3 px-3">Job Applications</th>
                            <th class="pb-3 px-3">JPO Evaluations</th>
                            <th class="pb-3 px-3 text-right">Placement Outcome</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($jobseekers as $js)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                
                                <!-- Jobseeker Identity -->
                                <td class="py-4 px-3 font-bold text-slate-900">
                                    <span class="text-sm block">{{ $js->first_name }} {{ $js->last_name }}</span>
                                    <span class="text-[11px] text-slate-400">{{ $js->user_email }}</span>
                                </td>

                                <!-- Demographics -->
                                <td class="py-4 px-3">
                                    <div class="space-y-1">
                                        <span class="rounded-lg bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-700 block max-w-fit">
                                            {{ $js->employment_status ?: 'Seeking' }}
                                        </span>
                                        <div class="flex flex-wrap gap-1">
                                            @if($js->is_pwd)
                                                <span class="rounded-md bg-purple-50 text-purple-700 border border-purple-200 px-1.5 py-0.5 text-[10px] font-bold">
                                                    ♿ {{ $js->pwd_type ?: 'PWD' }}
                                                </span>
                                            @endif
                                            @if($js->is_4ps)
                                                <span class="rounded-md bg-amber-50 text-amber-800 border border-amber-200 px-1.5 py-0.5 text-[10px] font-bold">
                                                    4Ps
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Training Progress -->
                                <td class="py-4 px-3">
                                    <div class="space-y-0.5">
                                        <span class="font-bold text-slate-900 block">{{ $js->trainings_count }} courses taken</span>
                                        @if($js->certs_count > 0)
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                                🎓 {{ $js->certs_count }} Certified
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-[11px]">No certs yet</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Job Applications -->
                                <td class="py-4 px-3">
                                    <span class="font-bold text-blue-700">{{ $js->applications_count }} positions</span>
                                </td>

                                <!-- JPO Evaluations -->
                                <td class="py-4 px-3">
                                    @if($js->jpo_referrals_count > 0)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 px-2.5 py-0.5 text-[11px] font-bold">
                                            ✓ {{ $js->jpo_referrals_count }} Referred to Employers
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">Pending JPO review</span>
                                    @endif
                                </td>

                                <!-- Placement Outcome -->
                                <td class="py-4 px-3 text-right">
                                    @if($js->hired_count > 0)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-600 text-white px-3 py-1 text-xs font-black shadow-sm">
                                            ✓ Hired & Placed
                                        </span>
                                    @else
                                        <span class="rounded-full bg-slate-100 text-slate-600 px-2.5 py-0.5 text-xs font-bold">
                                            Actively Navigating
                                        </span>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400 italic">
                                    No jobseekers found matching the selected filter criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-4 border-t border-slate-100">
                {{ $jobseekers->links() }}
            </div>
        </div>

    </div>
</div>
@endsection
