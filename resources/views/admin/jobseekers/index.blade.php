@extends('layouts.admin')

@section('title', 'Jobseeker Status & Workflow Supervision - Admin')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">

        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-green-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-green-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-400/20 px-3 py-1 text-xs font-bold text-green-300 border border-green-400/30">
                    <span class="h-2 w-2 rounded-full bg-green-400 animate-pulse"></span>
                    DMDP Jobseeker Workflow Supervision & Intelligence
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Jobseeker Status Directory</h1>
                <p class="text-sm text-slate-300">
                    Comprehensive oversight of candidate training progression, certifications, JPO evaluations, and employer placement statuses.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('admin.analytics.index') }}" 
                   class="inline-flex items-center gap-2 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/20 px-4 py-2.5 text-xs font-bold text-white transition-all">
                    <span>📈</span> Market Analytics
                </a>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Registered</span>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $totalJobseekers }}</p>
                <span class="text-[10px] text-slate-500">Active candidates</span>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700">In Training</span>
                <p class="text-2xl font-black text-amber-800 mt-1">{{ $inTrainingJobseekers ?? 0 }}</p>
                <span class="text-[10px] text-amber-700">Enrolled trainees</span>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-700">Certified Skills</span>
                <p class="text-2xl font-black text-blue-800 mt-1">{{ $certifiedJobseekers ?? 0 }}</p>
                <span class="text-[10px] text-blue-700">Graduated trainees</span>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-700">PWD Registered</span>
                <p class="text-2xl font-black text-indigo-800 mt-1">{{ $pwdJobseekers }}</p>
                <span class="text-[10px] text-indigo-700">Inclusive placement</span>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <span class="text-[11px] font-bold uppercase tracking-wider text-green-700">Hired Placements</span>
                <p class="text-2xl font-black text-green-800 mt-1">{{ $employedJobseekers }}</p>
                <span class="text-[10px] text-green-700">Employed candidates</span>
            </div>
        </div>

        <!-- Stage Filter Tabs & Search Bar -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
            <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-100">
                @php
                    $stages = [
                        '' => 'All Candidates',
                        'in_training' => 'In Training',
                        'certified' => 'Certified Skills',
                        'applied' => 'Active Applicants',
                        'jpo_referred' => 'JPO Referred',
                        'hired' => 'Hired / Placed',
                    ];
                @endphp
                @foreach($stages as $key => $label)
                    <a href="{{ route('admin.jobseekers.index', array_merge(request()->query(), ['stage' => $key, 'page' => 1])) }}"
                       class="px-4 py-2 rounded-2xl text-xs font-bold whitespace-nowrap transition-all {{ request('stage', '') == $key ? 'bg-slate-950 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('admin.jobseekers.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
                <input type="hidden" name="stage" value="{{ request('stage') }}">

                <div class="lg:col-span-5 space-y-1">
                    <label class="text-xs font-bold text-slate-700">Search Jobseeker</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search by name, email, or keywords..."
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                </div>

                <div class="lg:col-span-3 space-y-1">
                    <label class="text-xs font-bold text-slate-700">Employment Status</label>
                    <select name="employment_status" onchange="this.form.submit()"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                        <option value="">All Statuses</option>
                        <option value="Looking for job" {{ request('employment_status') == 'Looking for job' ? 'selected' : '' }}>Actively Seeking</option>
                        <option value="Employed" {{ request('employment_status') == 'Employed' ? 'selected' : '' }}>Employed</option>
                        <option value="Wage Employed" {{ request('employment_status') == 'Wage Employed' ? 'selected' : '' }}>Wage Employed</option>
                    </select>
                </div>

                <div class="lg:col-span-2 flex items-center gap-2 pb-2">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700 select-none">
                        <input type="checkbox" name="pwd_only" value="1" {{ request('pwd_only') == 1 ? 'checked' : '' }} onchange="this.form.submit()"
                                class="rounded border-slate-300 text-green-600 focus:ring-green-500 h-4 w-4">
                        <span>♿ PWD Only</span>
                    </label>
                </div>

                <div class="lg:col-span-2">
                    <a href="{{ route('admin.jobseekers.index') }}" class="block text-center w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Jobseekers Directory Table -->
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="p-6 sm:p-8 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-900">Jobseeker Profiles & Workflow Status</h3>
                    <p class="text-xs text-slate-500">Supervise training enrollment, skill certifications, and hiring pipelines</p>
                </div>
                <span class="text-xs font-bold text-slate-400">{{ $jobseekers->total() }} Candidates</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-100 text-[11px] uppercase font-bold text-slate-500 tracking-wider">
                        <tr>
                            <th class="py-4 px-6">Candidate Name</th>
                            <th class="py-4 px-6">Contact & Email</th>
                            <th class="py-4 px-6">Demographics</th>
                            <th class="py-4 px-6 text-center">Trainings & Certs</th>
                            <th class="py-4 px-6 text-center">Applications</th>
                            <th class="py-4 px-6 text-center">JPO Referrals</th>
                            <th class="py-4 px-6 text-right">Placement Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($jobseekers as $js)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-xl bg-slate-900 text-white font-bold text-xs flex items-center justify-center ring-2 ring-green-500/30 shrink-0">
                                            {{ strtoupper(substr($js->first_name ?? 'J', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-black text-slate-900 text-sm">{{ $js->first_name }} {{ $js->last_name }}</div>
                                            <div class="text-[11px] text-slate-500">Candidate ID #{{ $js->jobseeker_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-900">{{ $js->user_email }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $js->mobile_number ?: 'No phone provided' }}</div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex flex-wrap gap-1">
                                        @if($js->is_pwd)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-blue-50 text-blue-800 text-[10px] font-bold border border-blue-200">
                                                ♿ {{ $js->pwd_type ?: 'PWD' }}
                                            </span>
                                        @endif
                                        @if($js->is_4ps)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-amber-50 text-amber-800 text-[10px] font-bold border border-amber-200">
                                                4Ps
                                            </span>
                                        @endif
                                        @if(!$js->is_pwd && !$js->is_4ps)
                                            <span class="text-slate-400 font-medium">General</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="space-y-0.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-amber-50 text-amber-800 text-[10px] font-bold border border-amber-200">
                                            📚 {{ $js->trainings_count ?? 0 }} Enrolled
                                        </span>
                                        @if(($js->certs_count ?? 0) > 0)
                                            <div>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-green-50 text-green-800 text-[10px] font-bold border border-green-200">
                                                    🏅 {{ $js->certs_count }} Certified
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-center text-slate-600 font-bold">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 text-[10px] font-bold border border-slate-200">
                                        {{ $js->applications_count ?? 0 }} applied
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl bg-blue-50 text-blue-800 text-[10px] font-bold border border-blue-200">
                                        {{ $js->jpo_referrals_count ?? 0 }} referred
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    @if(($js->hired_count ?? 0) > 0)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl bg-green-600 text-white font-bold text-xs shadow-sm">
                                            ✓ Hired
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold border border-slate-200">
                                            {{ $js->employment_status ?: 'Seeking' }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <div class="text-3xl mb-2">👥</div>
                                    <p class="font-bold text-slate-700">No jobseeker records found</p>
                                    <p class="text-xs mt-0.5">Try adjusting your search criteria or filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($jobseekers->hasPages())
                <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                    {{ $jobseekers->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
