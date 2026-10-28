@extends('layouts.employer')

@section('title', 'Manage Job Postings - Employer Portal')

@section('content')
@php
$editJob = null;
if (request('edit_id')) {
$editJob = $jobs->firstWhere('job_id', request('edit_id')) ?? \App\Models\JobPosting::where('employer_id', $employer->employer_id)->find(request('edit_id'));
}

$jobsDataMap = [];
foreach ($jobs as $j) {
$jobsDataMap[$j->job_id] = [
'id' => $j->job_id,
'title' => $j->title,
'description' => $j->description ?? '',
'job_type' => $j->job_type ?? 'Full-time',
'location' => $j->location ?? 'Cebu City',
'salary_range' => $j->salary_range ?? '',
'benefits' => $j->benefits ?? '',
'other_instructions' => $j->other_instructions ?? '',
'qualifications' => $j->qualifications ?? '',
'skills' => $j->skills ?? '',
'skills_array' => $j->skills ? array_map('trim', explode(',', $j->skills)) : [],
'vacancy_count' => (int) $j->vacancy_count,
'valid_until' => $j->valid_until ? date('Y-m-d', strtotime($j->valid_until)) : '',
'valid_until_formatted' => $j->valid_until ? date('M d, Y', strtotime($j->valid_until)) : 'Continuous',
'accepts_disability' => (bool) $j->accepts_disability,
'disability_type' => $j->disability_type ?? '',
'status' => $j->status,
'applications_count' => (int) ($j->applications_count ?? 0),
'created_at' => $j->created_at ? date('M d, Y', strtotime($j->created_at)) : 'Today',
];
}
if ($editJob && !isset($jobsDataMap[$editJob->job_id])) {
$jobsDataMap[$editJob->job_id] = [
'id' => $editJob->job_id,
'title' => $editJob->title,
'description' => $editJob->description ?? '',
'job_type' => $editJob->job_type ?? 'Full-time',
'location' => $editJob->location ?? 'Cebu City',
'salary_range' => $editJob->salary_range ?? '',
'benefits' => $editJob->benefits ?? '',
'other_instructions' => $editJob->other_instructions ?? '',
'qualifications' => $editJob->qualifications ?? '',
'skills' => $editJob->skills ?? '',
'skills_array' => $editJob->skills ? array_map('trim', explode(',', $editJob->skills)) : [],
'vacancy_count' => (int) $editJob->vacancy_count,
'valid_until' => $editJob->valid_until ? date('Y-m-d', strtotime($editJob->valid_until)) : '',
'valid_until_formatted' => $editJob->valid_until ? date('M d, Y', strtotime($editJob->valid_until)) : 'Continuous',
'accepts_disability' => (bool) $editJob->accepts_disability,
'disability_type' => $editJob->disability_type ?? '',
'status' => $editJob->status,
'applications_count' => (int) ($editJob->applications_count ?? 0),
'created_at' => $editJob->created_at ? date('M d, Y', strtotime($editJob->created_at)) : 'Today',
];
}

// The exact same skill catalog used in Jobseeker Registration
$coreSkills = ['Innovation', 'Work Ethics', 'Problem Solving', 'Stress Tolerance', 'English Functional Skills', 'Team Work', 'Self Motivation', 'Critical Thinking', 'Planning and Organizing', 'English Comprehension', 'Multitasking', 'Creative Problem Solving', 'Decision Making', 'Social Perceptiveness', 'Math Functional Skill'];
$techSkills = ['Carpentry', 'Plumbing', 'Photography', 'Masonry', 'Driving', 'Hairdressing', 'Welding', 'Gardening', 'Cooking', 'Auto Mechanic', 'Tailoring', 'Baking'];
@endphp

<div x-data="jobPostingsManager()"
    data-jobs-map='@json($jobsDataMap)'
    data-edit-job-id="{{ $editJob ? $editJob->job_id : '' }}"
    data-has-create-param="{{ request()->has('create') && auth()->user()->is_approved ? '1' : '0' }}"
    class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="w-full max-w-[1680px] mx-auto space-y-8">

        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Job Postings Pipeline
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Job Postings Management</h1>
                <p class="text-sm text-slate-300">Create, edit, and manage company vacancies.</p>
            </div>

            @if($employer->is_accredited || ($accreditation && $accreditation->status === 'admin_approved'))
            <button @click="openCreate()"
                class="shrink-0 inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 px-6 py-3.5 text-xs font-black text-white shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Create New Job Posting
            </button>
            @else
            <a href="{{ route('employer.accreditation') }}"
                class="shrink-0 inline-flex items-center gap-2 rounded-2xl bg-amber-100 border border-amber-200 text-amber-800 px-6 py-3.5 text-xs font-black shadow-sm hover:bg-amber-200 transition-all">
                <span>⚠</span>
                Accreditation Required
            </a>
            @endif
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
                <span class="text-[11px] font-bold text-slate-400 uppercase">Total Vacancies</span>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total'] ?? $jobs->total() }}</p>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-emerald-200 bg-emerald-50/20 shadow-sm">
                <span class="text-[11px] font-bold text-emerald-700 uppercase">Active / Live</span>
                <p class="text-2xl font-black text-emerald-700 mt-1">{{ $stats['approved'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-amber-200 bg-amber-50/20 shadow-sm">
                <span class="text-[11px] font-bold text-amber-700 uppercase">Pending Review</span>
                <p class="text-2xl font-black text-amber-700 mt-1">{{ $stats['pending'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-rose-200 bg-rose-50/20 shadow-sm">
                <span class="text-[11px] font-bold text-rose-700 uppercase">Needs Revision</span>
                <p class="text-2xl font-black text-rose-700 mt-1">{{ $stats['rejected'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-slate-200 bg-slate-50/40 shadow-sm col-span-2 sm:col-span-1">
                <span class="text-[11px] font-bold text-slate-500 uppercase">Closed / Archived</span>
                <p class="text-2xl font-black text-slate-600 mt-1">{{ $stats['closed'] ?? 0 }}</p>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-1.5 bg-white p-1.5 rounded-2xl border border-slate-200 shadow-sm text-xs font-bold">
                <a href="{{ route('employer.job-postings', array_merge(request()->except('page', 'status'), ['status' => 'all'])) }}"
                    class="px-3 py-1.5 rounded-xl transition-colors {{ (!request()->has('status') || request()->status === 'all') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    All
                </a>
                <a href="{{ route('employer.job-postings', array_merge(request()->except('page'), ['status' => 'approved'])) }}"
                    class="px-3 py-1.5 rounded-xl transition-colors {{ request()->status === 'approved' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    Live ({{ $stats['approved'] ?? 0 }})
                </a>
                <a href="{{ route('employer.job-postings', array_merge(request()->except('page'), ['status' => 'pending'])) }}"
                    class="px-3 py-1.5 rounded-xl transition-colors {{ request()->status === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    Pending ({{ $stats['pending'] ?? 0 }})
                </a>
                <a href="{{ route('employer.job-postings', array_merge(request()->except('page'), ['status' => 'rejected'])) }}"
                    class="px-3 py-1.5 rounded-xl transition-colors {{ request()->status === 'rejected' ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    Revision ({{ $stats['rejected'] ?? 0 }})
                </a>
                <a href="{{ route('employer.job-postings', array_merge(request()->except('page'), ['status' => 'closed'])) }}"
                    class="px-3 py-1.5 rounded-xl transition-colors {{ request()->status === 'closed' ? 'bg-slate-700 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    Closed
                </a>
            </div>

            <form method="GET" action="{{ route('employer.job-postings') }}" class="flex items-center gap-2 max-w-sm w-full">
                @if(request()->filled('status'))
                <input type="hidden" name="status" value="{{ request()->status }}">
                @endif
                <div class="relative w-full">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search position or skills..."
                        class="w-full rounded-xl border border-slate-200 pl-9 pr-4 py-2 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold transition-colors">
                    Search
                </button>
            </form>
        </div>

        <!-- Job Postings Table -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <h2 class="text-lg font-black text-slate-900">Your Company Vacancies ({{ $jobs->total() }})</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-400 uppercase tracking-wider font-bold">
                            <th class="pb-3 px-3">Position / Title</th>
                            <th class="pb-3 px-3">Openings</th>
                            <th class="pb-3 px-3">Disability Inclusive</th>
                            <th class="pb-3 px-3">Valid Until</th>
                            <th class="pb-3 px-3">Approval Status</th>
                            <th class="pb-3 px-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($jobs as $job)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-3">
                                <button type="button" @click="openView({{ $job->job_id }})" class="text-left group focus:outline-none">
                                    <span class="font-bold text-slate-900 group-hover:text-emerald-700 transition-colors block text-sm">{{ $job->title }}</span>
                                    <span class="text-[11px] text-slate-400 line-clamp-1 max-w-sm">{{ $job->description }}</span>
                                </button>
                            </td>
                            <td class="py-4 px-3 font-bold text-slate-900">
                                <span class="inline-flex items-center gap-1.5">
                                    <span>{{ $job->vacancy_count }} open</span>
                                    @if(($job->applications_count ?? 0) > 0)
                                    <a href="{{ route('employer.referred-jobseekers', ['job_id' => $job->job_id]) }}" class="text-[10px] text-emerald-700 font-bold bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded-md hover:underline" title="View applicants">
                                        ({{ $job->applications_count }} applicants)
                                    </a>
                                    @endif
                                </span>
                            </td>
                            <td class="py-4 px-3">
                                @if($job->accepts_disability)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800">
                                    <svg class="w-3.5 h-3.5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    <span>{{ $job->disability_type ?: 'PWD Inclusive' }}</span>
                                </span>
                                @else
                                <span class="text-slate-400 text-[11px]">Standard</span>
                                @endif
                            </td>
                            <td class="py-4 px-3 text-slate-500 font-semibold">
                                @if($job->valid_until)
                                <span class="{{ strtotime($job->valid_until) < strtotime('today') ? 'text-rose-600 font-bold' : '' }}">
                                    {{ date('M d, Y', strtotime($job->valid_until)) }}
                                </span>
                                @else
                                <span>Continuous</span>
                                @endif
                            </td>
                            <td class="py-4 px-3">
                                @if($job->status === 'approved')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 border border-emerald-300 px-2.5 py-0.5 text-xs font-bold text-emerald-800">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                    <span>Live & Approved</span>
                                </span>
                                @elseif($job->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 border border-amber-300 px-2.5 py-0.5 text-xs font-bold text-amber-800">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-600 animate-pulse"></span>
                                    <span>Pending DMDP Review</span>
                                </span>
                                @elseif($job->status === 'rejected')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-100 border border-rose-300 px-2.5 py-0.5 text-xs font-bold text-rose-800">
                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-600"></span>
                                    <span>Needs Revision</span>
                                </span>
                                @elseif($job->status === 'closed')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 border border-slate-300 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                                    <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                    <span>Closed</span>
                                </span>
                                @endif
                            </td>
                            <td class="py-4 px-3 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" @click="openView({{ $job->job_id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>View</span>
                                    </button>

                                    <button type="button" @click="openEdit({{ $job->job_id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-200 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span>Edit</span>
                                    </button>

                                    @if($job->status !== 'closed')
                                    <button type="button" @click="openClose({{ $job->job_id }})"
                                        class="p-1.5 rounded-xl text-slate-400 hover:text-amber-700 hover:bg-amber-50 transition-colors"
                                        title="Close Vacancy">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </button>
                                    @endif

                                    <button type="button" @click="openDelete({{ $job->job_id }})"
                                        class="p-1.5 rounded-xl text-slate-400 hover:text-rose-700 hover:bg-rose-50 transition-colors"
                                        title="Delete Job">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 italic">
                                No job openings found matching the criteria. Click "Create New Job Posting" above to post your vacancy.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-4 border-t border-slate-100">
                {{ $jobs->links() }}
            </div>
        </div>

    </div>

    <!-- 1. Create Job Posting Modal -->
    <div x-show="createModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="createModal = false" class="bg-white rounded-3xl max-h-[90vh] overflow-y-auto overscroll-contain max-w-4xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">

            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">New Vacancy Submission</span>
                    <h3 class="text-xl font-black text-slate-900 mt-0.5">Post a Job Opening</h3>
                </div>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <form action="{{ route('employer.job-postings.store') }}" method="POST" class="space-y-6"
                x-data="skillPicker()"
                x-on:submit="validateCreateForm($event)">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2 space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Job Title / Role <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" required value="{{ old('title') }}" placeholder="e.g. Senior PHP / Laravel Developer"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Employment Type <span class="text-rose-500">*</span></label>
                        <select name="job_type" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900">
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract / Project-based</option>
                            <option value="Internship">Internship / OJT</option>
                            <option value="Freelance">Freelance</option>
                            <option value="Temporary">Temporary</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Location</label>
                        <input type="text" name="location" value="{{ old('location', 'Cebu City') }}"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Salary Range</label>
                        <input type="text" name="salary_range" value="{{ old('salary_range') }}" placeholder="e.g. ₱25,000 - ₱35,000"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Number of Vacancies <span class="text-rose-500">*</span></label>
                        <input type="number" name="vacancy_count" min="1" value="{{ old('vacancy_count', 1) }}" required
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Valid Until <span class="text-rose-500">*</span></label>
                        <input type="date" name="valid_until" min="{{ date('Y-m-d') }}" value="{{ old('valid_until', date('Y-m-d', strtotime('+2 months'))) }}" required
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Job Description <span class="text-rose-500">*</span></label>
                    <textarea name="description" rows="3" required placeholder="Detail the core duties..."
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900">{{ old('description') }}</textarea>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Required Qualifications <span class="text-[10px] text-slate-400 lowercase font-normal">(1 per line)</span>
                    </label>
                    <textarea name="qualifications" rows="2" placeholder="Bachelor's degree in Computer Science..."
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900">{{ old('qualifications') }}</textarea>
                </div>

                <!-- Categorized Skills Selection + Custom Input -->
                <div class="space-y-4 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-black text-slate-900">Required Skills & Competencies</h4>
                        <span class="text-xs font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full" x-text="skills.length + ' selected'"></span>
                    </div>

                    <input type="hidden" name="skills" id="create_skills_hidden" :value="skills.join(', ')">

                    <!-- Core Skills -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">1. 21st Century Core Skills</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach($coreSkills as $skill)
                            <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 bg-white cursor-pointer hover:bg-slate-50 transition-colors" :class="skills.includes('{{ $skill }}') ? 'border-emerald-500 bg-emerald-50/30' : ''">
                                <input type="checkbox" value="{{ $skill }}" x-model="skills" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="text-xs font-medium text-slate-800">{{ $skill }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Technical Skills -->
                    <div class="space-y-2 pt-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">2. Practical Technical Skills</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach($techSkills as $skill)
                            <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 bg-white cursor-pointer hover:bg-slate-50 transition-colors" :class="skills.includes('{{ $skill }}') ? 'border-emerald-500 bg-emerald-50/30' : ''">
                                <input type="checkbox" value="{{ $skill }}" x-model="skills" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="text-xs font-medium text-slate-800">{{ $skill }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- 3. Custom Skills Input -->
                    <div class="space-y-2 pt-4 border-t border-slate-200 mt-4">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">3. Specialized / Custom Skills</label>
                        <p class="text-[11px] text-slate-500 mb-2">Add specific software, tools, or skills not listed above (e.g. AutoCAD, QuickBooks, Laravel).</p>
                        <div class="flex gap-2">
                            <input type="text" x-model="newSkill" x-on:keydown.enter.prevent="addSkill()" placeholder="Type skill and press Enter or Add..."
                                class="flex-1 rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none">
                            <button type="button" x-on:click="addSkill()" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold transition-colors">
                                Add
                            </button>
                        </div>
                        <!-- Display added custom skills -->
                        <div class="flex flex-wrap gap-2 mt-2">
                            <template x-for="(skill, idx) in skills" :key="idx">
                                <!-- Only show if it's NOT in the predefined lists -->
                                <template x-if="!['Innovation', 'Work Ethics', 'Problem Solving', 'Stress Tolerance', 'English Functional Skills', 'Team Work', 'Self Motivation', 'Critical Thinking', 'Planning and Organizing', 'English Comprehension', 'Multitasking', 'Creative Problem Solving', 'Decision Making', 'Social Perceptiveness', 'Math Functional Skill', 'Carpentry', 'Plumbing', 'Photography', 'Masonry', 'Driving', 'Hairdressing', 'Welding', 'Gardening', 'Cooking', 'Auto Mechanic', 'Tailoring', 'Baking'].includes(skill)">
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 text-indigo-900 border border-indigo-200 px-3 py-1 text-xs font-bold shadow-sm">
                                        <span x-text="skill"></span>
                                        <button type="button" x-on:click="removeSkill(idx)" class="text-indigo-400 hover:text-indigo-700 font-black text-sm leading-none">&times;</button>
                                    </span>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Benefits <span class="text-[10px] text-slate-400 lowercase font-normal">(1 per line)</span>
                        </label>
                        <textarea name="benefits" rows="2" placeholder="HMO on day 1..."
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900">{{ old('benefits') }}</textarea>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Other Instructions</label>
                        <textarea name="other_instructions" rows="2" placeholder="Bring 2 copies of resume..."
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900">{{ old('other_instructions') }}</textarea>
                    </div>
                </div>

                <div class="rounded-2xl bg-emerald-50/70 border border-emerald-200 p-4 space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="accepts_disability" value="1" {{ old('accepts_disability') ? 'checked' : '' }} class="h-4 w-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                        <span class="text-xs font-bold text-emerald-950">This job opening accepts Persons with Disabilities (PWDs)</span>
                    </label>
                    <input type="text" name="disability_type" value="{{ old('disability_type') }}" placeholder="Accommodation details..."
                        class="w-full rounded-xl border border-emerald-200 px-3 py-2 text-xs bg-white text-slate-900">
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="createModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-lg">
                        Send to Admin &rarr;
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. Edit Job Posting Modal -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="editModal = false" class="bg-white rounded-3xl max-h-[90vh] overflow-y-auto overscroll-contain max-w-4xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">

            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Update Vacancy</span>
                    <h3 class="text-xl font-black text-slate-900 mt-0.5">Edit Job: <span x-text="selectedJob.title"></span></h3>
                </div>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <form :action="'/employer/job-postings/' + selectedJob.id" method="POST" class="space-y-6"
                x-data="editSkillPicker(selectedJob.skills_array || [])"
                x-on:submit="validateEditForm($event)">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2 space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Job Title <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" x-model="selectedJob.title" required
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Employment Type</label>
                        <select name="job_type" x-model="selectedJob.job_type" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900">
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract</option>
                            <option value="Internship">Internship</option>
                            <option value="Freelance">Freelance</option>
                            <option value="Temporary">Temporary</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Location</label>
                        <input type="text" name="location" x-model="selectedJob.location"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Salary Range</label>
                        <input type="text" name="salary_range" x-model="selectedJob.salary_range"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Vacancies <span class="text-rose-500">*</span></label>
                        <input type="number" name="vacancy_count" min="1" x-model="selectedJob.vacancy_count" required
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Valid Until</label>
                        <input type="date" name="valid_until" min="{{ date('Y-m-d') }}" x-model="selectedJob.valid_until"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Job Description <span class="text-rose-500">*</span></label>
                    <textarea name="description" rows="3" x-model="selectedJob.description" required
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900"></textarea>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Qualifications <span class="text-[10px] text-slate-400 lowercase font-normal">(1 per line)</span>
                    </label>
                    <textarea name="qualifications" rows="2" x-model="selectedJob.qualifications"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900"></textarea>
                </div>

                <!-- Categorized Skills Selection + Custom Input (Edit) -->
                <div class="space-y-4 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-black text-slate-900">Required Skills & Competencies</h4>
                        <span class="text-xs font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full" x-text="skills.length + ' selected'"></span>
                    </div>

                    <input type="hidden" name="skills" id="edit_skills_hidden" :value="skills.join(', ')">

                    <!-- Core Skills -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">1. 21st Century Core Skills</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach($coreSkills as $skill)
                            <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 bg-white cursor-pointer hover:bg-slate-50 transition-colors" :class="skills.includes('{{ $skill }}') ? 'border-emerald-500 bg-emerald-50/30' : ''">
                                <input type="checkbox" value="{{ $skill }}" x-model="skills" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="text-xs font-medium text-slate-800">{{ $skill }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Technical Skills -->
                    <div class="space-y-2 pt-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">2. Practical Technical Skills</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach($techSkills as $skill)
                            <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 bg-white cursor-pointer hover:bg-slate-50 transition-colors" :class="skills.includes('{{ $skill }}') ? 'border-emerald-500 bg-emerald-50/30' : ''">
                                <input type="checkbox" value="{{ $skill }}" x-model="skills" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="text-xs font-medium text-slate-800">{{ $skill }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- 3. Custom Skills Input -->
                    <div class="space-y-2 pt-4 border-t border-slate-200 mt-4">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">3. Specialized / Custom Skills</label>
                        <div class="flex gap-2">
                            <input type="text" x-model="newSkill" x-on:keydown.enter.prevent="addSkill()" placeholder="Type skill and press Enter or Add..."
                                class="flex-1 rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none">
                            <button type="button" x-on:click="addSkill()" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold transition-colors">
                                Add
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <template x-for="(skill, idx) in skills" :key="idx">
                                <template x-if="!['Innovation', 'Work Ethics', 'Problem Solving', 'Stress Tolerance', 'English Functional Skills', 'Team Work', 'Self Motivation', 'Critical Thinking', 'Planning and Organizing', 'English Comprehension', 'Multitasking', 'Creative Problem Solving', 'Decision Making', 'Social Perceptiveness', 'Math Functional Skill', 'Carpentry', 'Plumbing', 'Photography', 'Masonry', 'Driving', 'Hairdressing', 'Welding', 'Gardening', 'Cooking', 'Auto Mechanic', 'Tailoring', 'Baking'].includes(skill)">
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 text-indigo-900 border border-indigo-200 px-3 py-1 text-xs font-bold shadow-sm">
                                        <span x-text="skill"></span>
                                        <button type="button" x-on:click="removeSkill(idx)" class="text-indigo-400 hover:text-indigo-700 font-black text-sm leading-none">&times;</button>
                                    </span>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Benefits <span class="text-[10px] text-slate-400 lowercase font-normal">(1 per line)</span>
                        </label>
                        <textarea name="benefits" rows="2" x-model="selectedJob.benefits"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900"></textarea>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Other Instructions</label>
                        <textarea name="other_instructions" rows="2" x-model="selectedJob.other_instructions"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900"></textarea>
                    </div>
                </div>

                <div class="rounded-2xl bg-emerald-50/70 border border-emerald-200 p-4 space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="accepts_disability" value="1" x-model="selectedJob.accepts_disability" class="h-4 w-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                        <span class="text-xs font-bold text-emerald-950">This job opening accepts Persons with Disabilities (PWDs)</span>
                    </label>
                    <input type="text" name="disability_type" x-model="selectedJob.disability_type" placeholder="Accommodation details..."
                        class="w-full rounded-xl border border-emerald-200 px-3 py-2 text-xs bg-white text-slate-900">
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="editModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-lg">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. Delete Confirmation Modal -->
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="deleteModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-5">
            <div class="h-12 w-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <div class="space-y-1">
                <h3 class="text-lg font-black text-slate-900">Delete Job Posting?</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Remove <span class="font-bold text-slate-900" x-text="selectedJob.title"></span>? If active applications exist, it will be closed instead.
                </p>
            </div>
            <form :action="'/employer/job-postings/' + selectedJob.id" method="POST" class="pt-2 flex items-center justify-end gap-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-black">
                    Confirm Delete
                </button>
            </form>
        </div>
    </div>

    <!-- 4. Close Job Modal -->
    <div x-show="closeModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="closeModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-5">
            <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <div class="space-y-1">
                <h3 class="text-lg font-black text-slate-900">Close Vacancy?</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Close recruitment for <span class="font-bold text-slate-900" x-text="selectedJob.title"></span>? No new applicants will be accepted.
                </p>
            </div>
            <form :action="'/employer/job-postings/' + selectedJob.id + '/close'" method="POST" class="pt-2 flex items-center justify-end gap-3">
                @csrf
                <button type="button" @click="closeModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-black">
                    Close Opening
                </button>
            </form>
        </div>
    </div>

</div>

{{-- ============ SCRIPTS (NO Blade directives inside) ============ --}}
<script>
    function skillPicker() {
        return {
            skills: [],
            newSkill: '',
            addSkill() {
                const s = this.newSkill.trim();
                if (s && !this.skills.some(x => x.toLowerCase() === s.toLowerCase())) {
                    this.skills.push(s);
                }
                this.newSkill = '';
            },
            removeSkill(i) {
                this.skills.splice(i, 1);
            },
            validateCreateForm(e) {
                const vDate = e.target.querySelector('[name=valid_until]').value;
                if (vDate) {
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const chosen = new Date(vDate + 'T00:00:00');
                    if (chosen < today) {
                        alert('The valid until date cannot be in the past.');
                        e.preventDefault();
                        return false;
                    }
                }
                document.getElementById('create_skills_hidden').value = this.skills.join(', ');
            }
        };
    }

    function editSkillPicker(initial) {
        return {
            skills: initial || [],
            newSkill: '',
            addSkill() {
                const s = this.newSkill.trim();
                if (s && !this.skills.some(x => x.toLowerCase() === s.toLowerCase())) {
                    this.skills.push(s);
                }
                this.newSkill = '';
            },
            removeSkill(i) {
                this.skills.splice(i, 1);
            },
            validateEditForm(e) {
                const vDate = e.target.querySelector('[name=valid_until]').value;
                if (vDate) {
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const chosen = new Date(vDate + 'T00:00:00');
                    if (chosen < today) {
                        alert('The valid until date cannot be in the past.');
                        e.preventDefault();
                        return false;
                    }
                }
                document.getElementById('edit_skills_hidden').value = this.skills.join(', ');
            }
        };
    }

    function jobPostingsManager() {
        const root = document.querySelector('[data-jobs-map]');
        const jobsMap = JSON.parse(root.dataset.jobsMap || '{}');
        const editJobId = root.dataset.editJobId ? parseInt(root.dataset.editJobId, 10) : null;
        const hasCreateParam = root.dataset.hasCreateParam === '1';

        const initialJob = {
            id: null,
            title: '',
            description: '',
            job_type: 'Full-time',
            location: 'Cebu City',
            salary_range: '',
            benefits: '',
            other_instructions: '',
            qualifications: '',
            skills: '',
            skills_array: [],
            vacancy_count: 1,
            valid_until: '',
            valid_until_formatted: 'Continuous',
            accepts_disability: false,
            disability_type: '',
            status: '',
            applications_count: 0,
            created_at: ''
        };

        return {
            createModal: hasCreateParam,
            editModal: editJobId ? true : false,
            viewModal: false,
            deleteModal: false,
            closeModal: false,
            jobsMap: jobsMap,
            selectedJob: Object.assign({}, initialJob),
            init() {
                if (editJobId && this.jobsMap[editJobId]) {
                    this.selectedJob = Object.assign({}, initialJob, this.jobsMap[editJobId]);
                    this.editModal = true;
                }
            },
            openCreate() {
                this.createModal = true;
            },
            openView(id) {
                if (this.jobsMap[id]) {
                    this.selectedJob = Object.assign({}, initialJob, this.jobsMap[id]);
                    this.viewModal = true;
                }
            },
            openEdit(id) {
                if (this.jobsMap[id]) {
                    this.selectedJob = Object.assign({}, initialJob, this.jobsMap[id]);
                    this.editModal = true;
                }
            },
            openDelete(id) {
                if (this.jobsMap[id]) {
                    this.selectedJob = Object.assign({}, initialJob, this.jobsMap[id]);
                    this.deleteModal = true;
                }
            },
            openClose(id) {
                if (this.jobsMap[id]) {
                    this.selectedJob = Object.assign({}, initialJob, this.jobsMap[id]);
                    this.closeModal = true;
                }
            }
        };
    }
</script>
@endsection