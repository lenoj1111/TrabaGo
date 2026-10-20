@extends('layouts.employer')

@section('title', $job->title . ' - Job Details')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-5xl space-y-8">

        <!-- Top Navigation -->
        <div class="flex items-center justify-between">
            <a href="{{ route('employer.job-postings') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-700 hover:text-emerald-700 bg-white px-4 py-2 rounded-xl shadow-sm border border-slate-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back to All Job Postings</span>
            </a>

            <div class="flex items-center gap-2">
                <a href="{{ route('employer.job-postings', ['edit_id' => $job->job_id]) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-500 shadow-md transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit Vacancy</span>
                </a>
            </div>
        </div>

        <!-- Job Header Banner -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Opening Reference #{{ $job->job_id }}
                </span>
                
                <div>
                    @if($job->status === 'approved')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 border border-emerald-300 px-3 py-1 text-xs font-black text-emerald-900">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                            <span>Live & Approved</span>
                        </span>
                    @elseif($job->status === 'pending')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 border border-amber-300 px-3 py-1 text-xs font-black text-amber-900">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-600 animate-pulse"></span>
                            <span>Under DMDP Admin Review</span>
                        </span>
                    @elseif($job->status === 'rejected')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-100 border border-rose-300 px-3 py-1 text-xs font-black text-rose-900">
                            <span class="h-1.5 w-1.5 rounded-full bg-rose-600"></span>
                            <span>Needs Revision</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-900">
                            {{ ucfirst($job->status) }}
                        </span>
                    @endif
                </div>
            </div>

            <h1 class="text-3xl sm:text-4xl font-black tracking-tight">{{ $job->title }}</h1>
            <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-slate-300">
                <span class="inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>{{ $employer->company_name }}</span>
                </span>
                <span>&bull;</span>
                <span class="inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>{{ $job->job_type ?? 'Full-time' }}</span>
                </span>
                <span>&bull;</span>
                <span class="inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>{{ $job->location ?? 'Cebu City' }}</span>
                </span>
                @if($job->salary_range)
                    <span>&bull;</span>
                    <span class="inline-flex items-center gap-1.5 text-emerald-300 font-bold">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $job->salary_range }}</span>
                    </span>
                @endif
                <span>&bull;</span>
                <span class="inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>{{ $job->vacancy_count }} Available {{ Str::plural('Position', $job->vacancy_count) }}</span>
                </span>
                <span>&bull;</span>
                <span>Valid until {{ $job->valid_until ? date('F d, Y', strtotime($job->valid_until)) : 'Continuous' }}</span>
            </div>
        </div>

        <!-- Specifications & Accommodation Details -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left: Description, Qualifications, Skills, Benefits -->
            <div class="md:col-span-2 space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
                    <h2 class="text-base font-black text-slate-900 uppercase tracking-wider">Job Description & Duties</h2>
                    <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                        {{ $job->description }}
                    </div>
                </div>

                @if(!empty($job->qualifications_list))
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
                        <h2 class="text-base font-black text-slate-900 uppercase tracking-wider">Required Qualifications</h2>
                        <ul class="list-disc list-inside space-y-1.5 text-xs text-slate-700 leading-relaxed">
                            @foreach($job->qualifications_list as $qual)
                                <li class="pl-1">{{ $qual }}</li>
                            @endforeach
                        </ul>
                    </div>
                @elseif($job->qualifications)
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
                        <h2 class="text-base font-black text-slate-900 uppercase tracking-wider">Required Qualifications</h2>
                        <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                            {{ $job->qualifications }}
                        </div>
                    </div>
                @endif

                @if(!empty($job->skills_list))
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
                        <h2 class="text-base font-black text-slate-900 uppercase tracking-wider">Required Skills</h2>
                        <ul class="list-disc list-inside space-y-1.5 text-xs text-slate-700 leading-relaxed">
                            @foreach($job->skills_list as $skill)
                                <li class="pl-1">{{ $skill }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(!empty($job->benefits_list))
                    <div class="rounded-3xl border border-emerald-200 bg-emerald-50/40 p-6 sm:p-8 shadow-sm space-y-4">
                        <h2 class="text-base font-black text-emerald-950 uppercase tracking-wider">Benefits & Perks</h2>
                        <ul class="list-disc list-inside space-y-1.5 text-xs text-emerald-900 leading-relaxed">
                            @foreach($job->benefits_list as $benefit)
                                <li class="pl-1">{{ $benefit }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($job->other_instructions)
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
                        <h2 class="text-base font-black text-slate-900 uppercase tracking-wider">Application Instructions</h2>
                        <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                            {{ $job->other_instructions }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right: Meta Cards -->
            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4 text-xs">
                    <h3 class="font-black text-slate-900 uppercase tracking-wider">Opening Metadata</h3>
                    <div class="space-y-3 divide-y divide-slate-100">
                        <div class="pt-2 flex justify-between">
                            <span class="text-slate-400 font-semibold">Job Type:</span>
                            <span class="font-bold text-slate-900">{{ $job->job_type ?? 'Full-time' }}</span>
                        </div>
                        <div class="pt-2 flex justify-between">
                            <span class="text-slate-400 font-semibold">Location:</span>
                            <span class="font-bold text-slate-900">{{ $job->location ?? 'Cebu City' }}</span>
                        </div>
                        @if($job->salary_range)
                            <div class="pt-2 flex justify-between">
                                <span class="text-slate-400 font-semibold">Compensation:</span>
                                <span class="font-bold text-emerald-700">{{ $job->salary_range }}</span>
                            </div>
                        @endif
                        <div class="pt-2 flex justify-between">
                            <span class="text-slate-400 font-semibold">Vacancies:</span>
                            <span class="font-bold text-slate-900">{{ $job->vacancy_count }}</span>
                        </div>
                        <div class="pt-2 flex justify-between">
                            <span class="text-slate-400 font-semibold">Date Created:</span>
                            <span class="font-bold text-slate-900">{{ $job->created_at ? date('M d, Y', strtotime($job->created_at)) : 'Today' }}</span>
                        </div>
                        <div class="pt-2 flex justify-between">
                            <span class="text-slate-400 font-semibold">Application Deadline:</span>
                            <span class="font-bold text-slate-900">{{ $job->valid_until ? date('M d, Y', strtotime($job->valid_until)) : 'Open' }}</span>
                        </div>
                        <div class="pt-2 flex justify-between">
                            <span class="text-slate-400 font-semibold">Total Applicants:</span>
                            <span class="font-bold text-emerald-700">{{ $job->applications->count() }} candidates</span>
                        </div>
                    </div>
                </div>

                <!-- PWD Inclusivity Card -->
                <div class="rounded-3xl border {{ $job->accepts_disability ? 'border-emerald-200 bg-emerald-50/50' : 'border-slate-200 bg-white' }} p-6 shadow-sm space-y-2 text-xs">
                    <div class="flex items-center gap-2">
                        <div class="h-8 w-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <h3 class="font-black text-slate-900">PWD Inclusivity</h3>
                    </div>
                    @if($job->accepts_disability)
                        <p class="text-emerald-900 font-bold">Accommodating Persons with Disabilities</p>
                        <p class="text-[11px] text-emerald-800">{{ $job->disability_type ?: 'Workplace accommodations provided.' }}</p>
                    @else
                        <p class="text-slate-500">Standard workplace opening.</p>
                    @endif
                </div>

                <!-- Quick Action -->
                <a href="{{ route('employer.referred-jobseekers', ['job_id' => $job->job_id]) }}" class="inline-flex items-center justify-center gap-2 w-full text-center rounded-2xl bg-slate-900 hover:bg-emerald-600 text-white p-4 font-bold text-xs shadow-md transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>View All in Candidates Portal &rarr;</span>
                </a>
            </div>
        </div>

        <!-- Candidate Applications Roster Section -->
        <div x-data="{ 
            notQualifiedModal: false, 
            selectedAppId: null, 
            selectedName: '',
            openNotQualified(id, name) {
                this.selectedAppId = id;
                this.selectedName = name;
                this.notQualifiedModal = true;
            }
        }" class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Candidate Pipeline</span>
                    <h2 class="text-lg font-black text-slate-900 mt-0.5">Applicants for this Opening ({{ $job->applications->count() }})</h2>
                </div>
                <a href="{{ route('employer.referred-jobseekers', ['job_id' => $job->job_id]) }}" class="text-xs font-bold text-emerald-700 hover:underline inline-flex items-center gap-1">
                    <span>Manage in Full Portal</span> &rarr;
                </a>
            </div>

            @if($job->applications->isEmpty())
                <div class="py-10 text-center text-slate-400 text-xs italic">
                    No candidates have applied to this job opening yet. When jobseekers apply or are endorsed by DMDP JPO officers, they will be listed here.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-[10px] font-black uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="py-3 px-4">Applicant Name</th>
                                <th class="py-3 px-4">Contact</th>
                                <th class="py-3 px-4 text-center">Applied Date</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @foreach($job->applications as $app)
                                @php
                                    $candName = trim(($app->jobseeker->first_name ?? '') . ' ' . ($app->jobseeker->last_name ?? ''));
                                    if (empty($candName)) {
                                        $candName = 'Candidate #' . $app->jobseeker_id;
                                    }
                                @endphp
                                <tr class="hover:bg-slate-50/70">
                                    <td class="py-3 px-4">
                                        <p class="font-bold text-slate-900">{{ $candName }}</p>
                                        @if($app->referred_by_jpo)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 mt-0.5">
                                                DMDP JPO Referred
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 text-[11px]">
                                        <p>{{ $app->jobseeker->email ?? 'N/A' }}</p>
                                        <p>{{ $app->jobseeker->mobile_number ?? '' }}</p>
                                    </td>
                                    <td class="py-3 px-4 text-center text-slate-500">
                                        {{ $app->created_at ? date('M d, Y', strtotime($app->created_at)) : 'Recent' }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($app->status === 'hired')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                                <span>Hired</span>
                                            </span>
                                        @elseif($app->status === 'rejected')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-black bg-rose-100 text-rose-800 border border-rose-300" title="{{ $app->jpo_notes }}">
                                                <span class="h-1.5 w-1.5 rounded-full bg-rose-600"></span>
                                                <span>Not Qualified</span>
                                            </span>
                                        @elseif($app->status === 'interview')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-black bg-blue-100 text-blue-800 border border-blue-300">
                                                <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <span>Interview</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-black bg-amber-100 text-amber-800 border border-amber-300">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-600 animate-pulse"></span>
                                                <span>Under Review</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <div class="inline-flex items-center gap-1.5 justify-end">
                                            @if($app->status === 'hired')
                                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Hired</span>
                                                </span>
                                            @elseif($app->status === 'rejected')
                                                <form action="{{ route('employer.applicants.update_status', $app->application_id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="action" value="hire">
                                                    <button type="submit" onclick="return confirm('Confirm reconsidering and hiring {{ addslashes($candName) }}?')"
                                                            class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-[11px] font-bold border border-emerald-200">
                                                        Reconsider & Hire
                                                    </button>
                                                </form>
                                            @else
                                                <!-- Mark as Not Qualified Button -->
                                                <button type="button" 
                                                        @click="openNotQualified({{ $app->application_id }}, '{{ addslashes($candName) }}')"
                                                        class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] border border-rose-200 transition-colors">
                                                    Not Qualified
                                                </button>

                                                <!-- Mark as Hired Button -->
                                                <form action="{{ route('employer.applicants.update_status', $app->application_id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="action" value="hire">
                                                    <button type="submit" onclick="return confirm('Confirm officially hiring {{ addslashes($candName) }} for this position?')"
                                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-black text-[11px] shadow-sm transition-transform hover:scale-105">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                        <span>Mark as Hired</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Not Qualified Modal -->
            <div x-show="notQualifiedModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                <div @click.away="notQualifiedModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <span class="text-xs font-bold text-rose-600 uppercase tracking-wider">Candidate Evaluation</span>
                            <h3 class="text-xl font-black text-slate-900 mt-0.5">Mark as Not Qualified</h3>
                        </div>
                        <button @click="notQualifiedModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
                    </div>

                    <p class="text-xs text-slate-600">
                        You are marking <strong class="text-slate-900" x-text="selectedName"></strong> as not meeting the required qualifications for this position.
                    </p>

                    <form :action="'/employer/applicants/' + selectedAppId + '/status'" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="action" value="not_qualified">

                        <div class="space-y-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Reason / Evaluation Notes (Optional)</label>
                            <textarea name="remarks" rows="3" placeholder="e.g. Lacks specific technical certification, minimum years of experience not met..."
                                      class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-400"></textarea>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                            <button type="button" @click="notQualifiedModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-7 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-black shadow-lg shadow-rose-600/30">
                                Confirm & Mark Not Qualified
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
