@extends('layouts.admin')

@section('title', 'Jobseeker Status & Workflow Supervision - Admin')

@section('content')
<div x-data="{ viewModal: false, selectedJobseeker: null, openView(js) { this.selectedJobseeker = js; this.viewModal = true; } }" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="w-full max-w-[1680px] mx-auto space-y-8">

        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    DMDP Jobseeker Workflow Supervision & Intelligence
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Jobseeker Status Directory</h1>
                <p class="text-sm text-slate-300">
                    Comprehensive oversight of candidate training progression, certifications, JPO evaluations, and employer placement statuses.
                </p>
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
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Hired Placements</span>
                <p class="text-2xl font-black text-emerald-800 mt-1">{{ $employedJobseekers }}</p>
                <span class="text-[10px] text-emerald-700">Employed candidates</span>
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
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                </div>

                <div class="lg:col-span-3 space-y-1">
                    <label class="text-xs font-bold text-slate-700">Employment Status</label>
                    <select name="employment_status" onchange="this.form.submit()"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        <option value="">All Statuses</option>
                        <option value="Looking for job" {{ request('employment_status') == 'Looking for job' ? 'selected' : '' }}>Actively Seeking</option>
                        <option value="Employed" {{ request('employment_status') == 'Employed' ? 'selected' : '' }}>Employed</option>
                        <option value="Wage Employed" {{ request('employment_status') == 'Wage Employed' ? 'selected' : '' }}>Wage Employed</option>
                    </select>
                </div>

                <div class="lg:col-span-2 flex items-center gap-2 pb-2">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700 select-none">
                        <input type="checkbox" name="pwd_only" value="1" {{ request('pwd_only') == 1 ? 'checked' : '' }} onchange="this.form.submit()"
                                class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-600" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="4" r="2"/><path d="M15.5 13.5A4.5 4.5 0 0 1 11 18a4.5 4.5 0 0 1-4.5-4.5A4.5 4.5 0 0 1 11 9c.4 0 .78.06 1.14.16L13.5 6.5C12.7 6.18 11.87 6 11 6a7.5 7.5 0 0 0-7.5 7.5A7.5 7.5 0 0 0 11 21a7.5 7.5 0 0 0 7.42-6.5h-2.92z"/><path d="M19 10h-4.5l-1.3-2.6A2 2 0 0 0 11.4 6.2L8.5 7.6 9.4 9.4l2.1-1 2 4.1V17h2v-4.5l1.6-1.6H19v-2z"/></svg>
                            <span>PWD Only</span>
                        </span>
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
                            <th class="py-4 px-6">Account Created</th>
                            <th class="py-4 px-6 text-center">Trainings & Certs</th>
                            <th class="py-4 px-6 text-center">Applications</th>
                            <th class="py-4 px-6 text-center">JPO Referrals</th>
                            <th class="py-4 px-6 text-center">Placement Status</th>
                            <th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($jobseekers as $js)
                            @php
                                $jsName = trim(($js->first_name ?? '') . ' ' . ($js->last_name ?? ''));
                                $jsData = [
                                    'id' => $js->jobseeker_id,
                                    'user_id' => $js->user_id,
                                    'name' => $jsName,
                                    'email' => $js->user_email ?? 'N/A',
                                    'mobile' => $js->mobile_number ?: 'Not provided',
                                    'gender' => !empty($js->sex) ? ucfirst($js->sex) : (!empty($js->gender) ? ucfirst($js->gender) : 'N/A'),
                                    'civil_status' => !empty($js->civil_status) ? ucfirst($js->civil_status) : 'N/A',
                                    'birth_date' => !empty($js->birth_date) ? date('M d, Y', strtotime($js->birth_date)) : 'N/A',
                                    'location' => $js->location_text ?? (trim(($js->current_barangay ?? '') . ', ' . ($js->current_city ?? '') . ', ' . ($js->current_province ?? 'Cebu')) ?: 'Cebu City'),
                                    'education' => $js->education_text ?? (!empty($js->educational_level) ? ($js->educational_level . (!empty($js->course_degree) ? ' (' . $js->course_degree . ')' : '')) : 'N/A'),
                                    'employment_status' => $js->employment_status ?: 'Seeking Opportunity',
                                    'hired_company' => $js->hired_company ?? '',
                                    'is_pwd' => (bool)($js->is_pwd ?? false),
                                    'pwd_type' => $js->pwd_type ?? '',
                                    'is_4ps' => (bool)($js->is_4ps ?? false),
                                    'skills' => $js->skills_text ?: '',
                                    'skills_count' => $js->skills_count ?? 0,
                                    'trainings_count' => $js->trainings_count ?? 0,
                                    'certs_count' => $js->certs_count ?? 0,
                                    'applications_count' => $js->applications_count ?? 0,
                                    'jpo_referrals_count' => $js->jpo_referrals_count ?? 0,
                                    'hired_count' => $js->hired_count ?? 0,
                                    'created_at' => !empty($js->registered_at) ? date('M d, Y', strtotime($js->registered_at)) : 'N/A',
                                    'is_verified' => (bool)($js->is_verified ?? false),
                                    'is_approved' => (bool)($js->user_is_approved ?? false),
                                    'valid_id_url' => $js->valid_id_url,
                                    'valid_id_name' => $js->valid_id_name,
                                    'valid_id_is_image' => (bool) $js->valid_id_is_image,
                                    'missing_requirements' => $js->missing_requirements ?? []
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-xl bg-slate-900 text-white font-bold text-xs flex items-center justify-center ring-2 ring-emerald-500/30 shrink-0">
                                            {{ strtoupper(substr($js->first_name ?? 'J', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-black text-slate-900 text-sm flex items-center gap-1.5 flex-wrap">
                                                <span>{{ $js->first_name }} {{ $js->last_name }}</span>
                                                @if(!empty($js->user_is_approved))
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black border border-emerald-300">
                                                        <svg class="w-3 h-3 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                        <span>Verified</span>
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold border border-amber-300" title="Incomplete Profile or Documents">
                                                        <svg class="w-3 h-3 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                        <span>Incomplete</span>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-[11px] text-slate-500">Candidate ID #{{ $js->jobseeker_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-900">{{ $js->user_email }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $js->mobile_number ?: 'No phone provided' }}</div>
                                </td>
                                <td class="py-4 px-6 text-slate-600 text-[11px]">
                                    {{ $js->registered_at ? date('M d, Y', strtotime($js->registered_at)) : 'N/A' }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="space-y-0.5">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-amber-50 text-amber-800 text-[10px] font-bold border border-amber-200">
                                            <svg class="w-3 h-3 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                                            <span>{{ $js->trainings_count ?? 0 }} Enrolled</span>
                                        </span>
                                        @if(($js->certs_count ?? 0) > 0)
                                            <div>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 text-[10px] font-bold border border-emerald-200">
                                                    <svg class="w-3 h-3 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                                    <span>{{ $js->certs_count }} Certified</span>
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
                                <td class="py-4 px-6 text-center">
                                    @if(($js->hired_count ?? 0) > 0)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-600 text-white font-bold text-xs shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            <span>Hired</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold border border-slate-200">
                                            {{ $js->employment_status ?: 'Seeking' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button"
                                                data-action="approve-user"
                                                data-approve-url="{{ route('admin.approvals.jobseekers.approve', $js->jobseeker_id) }}"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition-all" title="Approve Candidate">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            <span>Approve</span>
                                        </button>
                                        <button type="button"
                                                data-action="disapprove-user"
                                                data-approve-url="{{ route('admin.approvals.jobseekers.disapprove', $js->jobseeker_id) }}"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition-all" title="Disapprove Candidate">
                                            <span>Disapprove</span>
                                        </button>
                                        <button type="button" @click='openView(@json($jsData))'
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition-colors" title="View Full Profile">
                                            <svg class="w-3.5 h-3.5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>View</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400">
                                    <div class="h-12 w-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </div>
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

        <!-- View Jobseeker Details Modal -->
        <div x-show="viewModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="viewModal = false" class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6 max-h-[90vh] overflow-y-auto" x-show="selectedJobseeker">
                
                <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Jobseeker Dossier</span>
                        <h3 class="text-2xl font-black text-slate-900" x-text="selectedJobseeker.name"></h3>
                        <p class="text-xs text-slate-400">Candidate ID #<span x-text="selectedJobseeker.id"></span> &bull; <span x-text="selectedJobseeker.email"></span></p>
                        <div class="pt-1 flex items-center gap-2">
                            <template x-if="selectedJobseeker.is_verified">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-black border border-emerald-300">
                                    <svg class="w-3.5 h-3.5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    <span>Verified Candidate (Complete Profile & Docs)</span>
                                </span>
                            </template>
                            <template x-if="!selectedJobseeker.is_verified">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[11px] font-bold border border-amber-300">
                                    <svg class="w-3.5 h-3.5 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <span>Unverified (Profile/Documents Incomplete)</span>
                                </span>
                            </template>
                        </div>
                    </div>
                    <button @click="viewModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
                </div>

                <!-- Badges -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <div>
                        <span class="text-slate-400 text-[10px] font-bold uppercase block">Placement</span>
                        <span class="font-bold text-slate-900" x-text="selectedJobseeker.hired_count > 0 ? 'Hired' : selectedJobseeker.employment_status"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] font-bold uppercase block">Trainings</span>
                        <span class="font-bold text-amber-800" x-text="selectedJobseeker.trainings_count + ' Enrolled'"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] font-bold uppercase block">Certs Issued</span>
                        <span class="font-bold text-emerald-700" x-text="selectedJobseeker.certs_count + ' Certified'"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] font-bold uppercase block">Applications</span>
                        <span class="font-bold text-blue-700" x-text="selectedJobseeker.applications_count + ' Submitted'"></span>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="space-y-2 text-xs">
                    <h4 class="font-black uppercase tracking-wider text-slate-800">Personal & Contact Details</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <div>
                            <span class="text-slate-400 font-semibold block text-[10px] uppercase">Contact Phone</span>
                            <span class="font-bold text-slate-900" x-text="selectedJobseeker.mobile"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block text-[10px] uppercase">Registered Email</span>
                            <span class="font-bold text-slate-900" x-text="selectedJobseeker.email"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block text-[10px] uppercase">Gender / Civil Status</span>
                            <span class="font-bold text-slate-900" x-text="selectedJobseeker.gender + ' &bull; ' + selectedJobseeker.civil_status"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block text-[10px] uppercase">Birth Date</span>
                            <span class="font-bold text-slate-900" x-text="selectedJobseeker.birth_date"></span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-slate-400 font-semibold block text-[10px] uppercase">Residential Address</span>
                            <span class="font-bold text-slate-900" x-text="selectedJobseeker.location || 'Cebu City'"></span>
                        </div>
                    </div>
                </div>

                <!-- Education & Skills -->
                <div class="space-y-2 text-xs">
                    <h4 class="font-black uppercase tracking-wider text-slate-800">Education & Qualifications</h4>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                        <div>
                            <span class="text-slate-400 font-semibold block text-[10px] uppercase">Attainment</span>
                            <span class="font-bold text-slate-900" x-text="selectedJobseeker.education"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block text-[10px] uppercase">Registered Skills</span>
                            <span class="font-bold text-emerald-900" x-text="selectedJobseeker.skills || 'No skill tags registered yet.'"></span>
                        </div>
                    </div>
                </div>

                <template x-if="selectedJobseeker.valid_id_url">
                    <div class="space-y-2 text-xs">
                        <h4 class="font-black uppercase tracking-wider text-slate-800">Uploaded Valid ID</h4>
                        <div class="space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="font-semibold text-slate-600" x-text="selectedJobseeker.valid_id_name || 'Government-issued ID'"></p>
                            <template x-if="selectedJobseeker.valid_id_is_image">
                                <img :src="selectedJobseeker.valid_id_url" :alt="selectedJobseeker.valid_id_name || 'Uploaded valid ID'" class="max-h-[60vh] w-full rounded-xl border border-slate-200 bg-white object-contain">
                            </template>
                            <template x-if="!selectedJobseeker.valid_id_is_image">
                                <iframe :src="selectedJobseeker.valid_id_url" :title="selectedJobseeker.valid_id_name || 'Uploaded valid ID'" class="h-[60vh] w-full rounded-xl border border-slate-200 bg-white"></iframe>
                            </template>
                            <a :href="selectedJobseeker.valid_id_url" target="_blank" rel="noopener" class="inline-flex items-center gap-1 font-bold text-emerald-700 hover:text-emerald-900">
                                Open ID in a new tab
                            </a>
                        </div>
                    </div>
                </template>

                <!-- Inclusivity & Demographics -->
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="p-3.5 rounded-2xl border"
                         :class="selectedJobseeker.is_pwd ? 'bg-blue-50 border-blue-200 text-blue-900' : 'bg-slate-50 border-slate-200 text-slate-600'">
                        <span class="font-bold flex items-center gap-1.5 mb-0.5">
                            <svg class="w-3.5 h-3.5 text-blue-700 shrink-0" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="4" r="2"/><path d="M15.5 13.5A4.5 4.5 0 0 1 11 18a4.5 4.5 0 0 1-4.5-4.5A4.5 4.5 0 0 1 11 9c.4 0 .78.06 1.14.16L13.5 6.5C12.7 6.18 11.87 6 11 6a7.5 7.5 0 0 0-7.5 7.5A7.5 7.5 0 0 0 11 21a7.5 7.5 0 0 0 7.42-6.5h-2.92z"/><path d="M19 10h-4.5l-1.3-2.6A2 2 0 0 0 11.4 6.2L8.5 7.6 9.4 9.4l2.1-1 2 4.1V17h2v-4.5l1.6-1.6H19v-2z"/></svg>
                            <span>PWD Status:</span>
                        </span>
                        <p x-text="selectedJobseeker.is_pwd ? ('Registered PWD (' + (selectedJobseeker.pwd_type || 'General') + ')') : 'Non-PWD'"></p>
                    </div>
                    <div class="p-3.5 rounded-2xl border"
                         :class="selectedJobseeker.is_4ps ? 'bg-amber-50 border-amber-200 text-amber-900' : 'bg-slate-50 border-slate-200 text-slate-600'">
                        <span class="font-bold flex items-center gap-1.5 mb-0.5">
                            <svg class="w-3.5 h-3.5 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <span>4Ps Beneficiary:</span>
                        </span>
                        <p x-text="selectedJobseeker.is_4ps ? 'Active 4Ps Beneficiary' : 'Not 4Ps'"></p>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button"
                                    data-action="approve-user"
                                    :data-approve-url="'{{ url('/admin/approvals/jobseekers') }}/' + selectedJobseeker.id + '/approve'"
                                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-md transition-all hover:bg-emerald-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Approve Candidate</span>
                            </button>
                            <button type="button"
                                    data-action="disapprove-user"
                                    :data-approve-url="'{{ url('/admin/approvals/jobseekers') }}/' + selectedJobseeker.id + '/disapprove'"
                                    class="inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-xs font-bold text-rose-700 transition-all hover:bg-rose-100">
                                <span>Disapprove Candidate</span>
                            </button>
                        </div>
                    </div>
                    <button type="button" @click="viewModal = false" class="px-5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Close
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('click', function (event) {
            const button = event.target.closest('[data-action="approve-user"], [data-action="disapprove-user"]');
            if (!button) return;
            const isDisapproval = button.dataset.action === 'disapprove-user';

            const confirmation = {
                title: isDisapproval ? 'Disapprove Jobseeker Account?' : 'Approve User Account?',
                text: isDisapproval
                    ? 'This will revoke application access and return the account to pending review.'
                    : 'This will authorize the user account to log in and access system functions.',
                icon: isDisapproval ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: isDisapproval ? '#e11d48' : '#059669',
                cancelButtonColor: '#64748b',
                confirmButtonText: isDisapproval ? 'Yes, disapprove' : 'Yes, approve'
            };

            if (isDisapproval) {
                confirmation.input = 'textarea';
                confirmation.inputLabel = 'Remarks for the jobseeker';
                confirmation.inputPlaceholder = 'Explain why the account is being disapproved...';
                confirmation.inputAttributes = { 'aria-label': 'Disapproval remarks' };
                confirmation.inputValidator = (value) => {
                    if (!value || !value.trim()) return 'Please enter remarks for the jobseeker.';
                    if (value.trim().length > 1000) return 'Remarks must be 1000 characters or fewer.';
                };
            }

            Swal.fire(confirmation).then((result) => {
                if (!result.isConfirmed) return;

                const headers = {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                };
                const request = { method: 'POST', headers };
                if (isDisapproval) {
                    headers['Content-Type'] = 'application/json';
                    request.body = JSON.stringify({ remarks: result.value });
                }

                fetch(button.dataset.approveUrl, request)
                .then(async response => {
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(data.message || data.error || 'Unable to update this account.');
                    }
                    return data;
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire(isDisapproval ? 'Disapproved!' : 'Approved!', data.success, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Action Failed', data.error || 'Unable to update this account.', 'error');
                    }
                })
                .catch(error => Swal.fire('Action Failed', error.message || 'Network error occurred.', 'error'));
            });
        });
    });
</script>
@endsection
