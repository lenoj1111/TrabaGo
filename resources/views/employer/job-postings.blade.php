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
@endphp

<div x-data="jobPostingsManager()" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="w-full max-w-[1680px] mx-auto space-y-8">
        
        <!-- Header in green Theme -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Job Postings Pipeline
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Job Postings Management</h1>
                <p class="text-sm text-slate-300">Create, edit, and manage company vacancies. Openings sent to DMDP Administration are reviewed and matched with jobseekers via AI.</p>
            </div>

            @if(auth()->user()->isAccountVerified())
                <button @click="createModal = true" 
                        class="shrink-0 inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 px-6 py-3.5 text-xs font-black text-white shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Create New Job Opening</span>
                </button>
            @elseif(auth()->user()->account_status === 'unverified')
                <button type="button" 
                        onclick="if(confirm('Your company is not yet accredited. Would you like to submit your required business documents now?')) { window.location.href='{{ route('employer.accreditation') }}'; }"
                        class="shrink-0 inline-flex items-center gap-2 rounded-2xl bg-slate-800 hover:bg-slate-700 px-6 py-3.5 text-xs font-black text-amber-300 shadow-md transition-all cursor-pointer border border-amber-500/40"
                        title="Submit documents to unlock job posting">
                    <svg class="h-4 w-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Create Job (Accreditation Required)</span>
                </button>
            @else
                <button type="button" 
                        onclick="alert('Your accreditation documents have been submitted and are under review by DMDP / JPO officers. You will be able to post jobs once approved.')"
                        class="shrink-0 inline-flex items-center gap-2 rounded-2xl bg-slate-800 hover:bg-slate-700 px-6 py-3.5 text-xs font-black text-slate-300 shadow-md transition-all cursor-not-allowed border border-slate-700"
                        title="Accreditation evaluation in progress">
                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Create Job (Accreditation Pending)</span>
                </button>
            @endif
        </div>

        <!-- Approval / Accreditation Required Notice -->
        @if(!auth()->user()->isAccountVerified())
            @if(auth()->user()->account_status === 'unverified')
                <div class="rounded-2xl bg-amber-50 border border-amber-200 p-5 text-amber-950 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-xs">
                    <div class="flex items-start gap-3">
                        <div class="h-10 w-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full bg-amber-600 text-white text-[10px] font-black uppercase tracking-wider">Accreditation Required</span>
                                <span class="text-xs text-amber-800 font-bold">Job Posting Locked</span>
                            </div>
                            <p class="text-xs font-semibold text-amber-900 mt-1">
                                You can configure your company profile and explore the portal freely. To publish job openings, please submit your company accreditation documents (BIR 2303, Mayor's Permit, SEC/DTI) for DMDP/JPO evaluation.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('employer.accreditation') }}" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shrink-0 transition-colors shadow-sm">
                        Submit Accreditation Documents &rarr;
                    </a>
                </div>
            @else
                <div class="rounded-2xl bg-blue-50 border border-blue-200 p-5 text-blue-950 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-xs">
                    <div class="flex items-start gap-3">
                        <div class="h-10 w-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full bg-blue-600 text-white text-[10px] font-black uppercase tracking-wider">Under Evaluation</span>
                                <span class="text-xs text-blue-800 font-bold">Review in Progress</span>
                            </div>
                            <p class="text-xs font-semibold text-blue-900 mt-1">
                                Your accreditation documents have been submitted and are currently being reviewed by DMDP and JPO officers. Job posting will be unlocked immediately upon approval.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('employer.accreditation') }}" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shrink-0 transition-colors shadow-sm">
                        Check Status &rarr;
                    </a>
                </div>
            @endif
        @endif

        <!-- Flash Messages & Validation Alerts -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2.5">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('info'))
            <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-xs font-bold flex items-center gap-2.5">
                <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="block font-black">Please correct the following errors:</span>
                </div>
                <ul class="list-disc list-inside font-medium pl-6">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Summary Metric Cards -->
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

        <!-- Filter & Search Controls -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <!-- Filter Pills -->
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

            <!-- Search Field -->
            <form method="GET" action="{{ route('employer.job-postings') }}" class="flex items-center gap-2 max-w-sm w-full">
                @if(request()->filled('status'))
                    <input type="hidden" name="status" value="{{ request()->status }}">
                @endif
                <div class="relative w-full">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search position or skills..."
                           class="w-full rounded-xl border border-slate-200 pl-9 pr-4 py-2 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold transition-colors">
                    Search
                </button>
            </form>
        </div>

        <!-- Job Postings List Table -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h2 class="text-lg font-black text-slate-900">Your Company Vacancies ({{ $jobs->total() }})</h2>
                <span class="text-xs font-bold text-slate-500">Pipeline Tracking & Administration</span>
            </div>

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
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="openView({{ $job->job_id }})" class="text-left group focus:outline-none">
                                            <span class="font-bold text-slate-900 group-hover:text-emerald-700 transition-colors block text-sm">{{ $job->title }}</span>
                                            <span class="text-[11px] text-slate-400 line-clamp-1 max-w-sm">{{ $job->description }}</span>
                                        </button>
                                    </div>
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
                                            <svg class="w-3.5 h-3.5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
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
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                                            {{ ucfirst($job->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-3 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- View Details Button -->
                                        <button type="button" @click="openView({{ $job->job_id }})"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors shadow-xs"
                                                title="View Job Details">
                                            <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>View</span>
                                        </button>

                                        <!-- Edit Job Button -->
                                        <button type="button" @click="openEdit({{ $job->job_id }})"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-200 transition-colors shadow-xs"
                                                title="Edit Job Vacancy">
                                            <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </button>

                                        <!-- Close or Delete -->
                                        @if($job->status !== 'closed')
                                            <button type="button" @click="openClose({{ $job->job_id }})"
                                                    class="p-1.5 rounded-xl text-slate-400 hover:text-amber-700 hover:bg-amber-50 transition-colors"
                                                    title="Close Vacancy">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            </button>
                                        @endif

                                        <button type="button" @click="openDelete({{ $job->job_id }})"
                                                class="p-1.5 rounded-xl text-slate-400 hover:text-rose-700 hover:bg-rose-50 transition-colors"
                                                title="Delete Job">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400 italic">
                                    No job openings found matching the criteria. Click "Create New Job Opening" above to post your vacancy.
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
        <div @click.away="createModal = false" class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">New Vacancy Submission</span>
                    <h3 class="text-xl font-black text-slate-900 mt-0.5">Post a Job Opening</h3>
                </div>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <form action="{{ route('employer.job-postings.store') }}" method="POST" class="space-y-4"
                  @submit="
                      const vDate = $el.querySelector('[name=valid_until]').value;
                      if (vDate) {
                          const today = new Date();
                          today.setHours(0,0,0,0);
                          const chosen = new Date(vDate + 'T00:00:00');
                          if (chosen < today) {
                              alert('The valid until date cannot be in the past.');
                              $event.preventDefault();
                              return false;
                          }
                      }
                  ">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2 space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Job Title / Role <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" required value="{{ old('title') }}" placeholder="e.g. Senior PHP / Laravel Developer, Customer Service Associate"
                               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Employment Type <span class="text-rose-500">*</span></label>
                        <select name="job_type" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            <option value="Full-time" {{ old('job_type') == 'Full-time' ? 'selected' : '' }}>Full-time</option>
                            <option value="Part-time" {{ old('job_type') == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                            <option value="Contract" {{ old('job_type') == 'Contract' ? 'selected' : '' }}>Contract / Project-based</option>
                            <option value="Internship" {{ old('job_type') == 'Internship' ? 'selected' : '' }}>Internship / OJT</option>
                            <option value="Freelance" {{ old('job_type') == 'Freelance' ? 'selected' : '' }}>Freelance</option>
                            <option value="Temporary" {{ old('job_type') == 'Temporary' ? 'selected' : '' }}>Temporary</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Location / Work Arrangement</label>
                        <input type="text" name="location" value="{{ old('location', 'Cebu City') }}" placeholder="e.g. Cebu City / Hybrid / On-site"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Salary & Compensation</label>
                        <input type="text" name="salary_range" value="{{ old('salary_range') }}" placeholder="e.g. ₱25,000 - ₱35,000 / month, Negotiable"
                               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Number of Vacancies <span class="text-rose-500">*</span></label>
                        <input type="number" name="vacancy_count" min="1" value="{{ old('vacancy_count', 1) }}" required
                               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Valid Until Date <span class="text-rose-500">*</span>
                            <span class="text-[10px] text-emerald-700 lowercase font-normal">(cannot be in the past)</span>
                        </label>
                        <input type="date" name="valid_until" 
                               min="{{ date('Y-m-d') }}" 
                               value="{{ old('valid_until', date('Y-m-d', strtotime('+2 months'))) }}" required
                               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Job Description & Responsibilities <span class="text-rose-500">*</span></label>
                    <textarea name="description" rows="3" required placeholder="Detail the core duties, daily tasks, and team environment..."
                              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Required Qualifications
                            <span class="text-[10px] text-slate-400 lowercase font-normal">(enter 1 per line)</span>
                        </label>
                        <textarea name="qualifications" rows="3" placeholder="Bachelor's degree in Computer Science&#10;At least 2 years work experience&#10;Strong communication skills"
                                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">{{ old('qualifications') }}</textarea>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Required Skills
                            <span class="text-[10px] text-slate-400 lowercase font-normal">(enter 1 per line)</span>
                        </label>
                        <textarea name="skills" rows="3" placeholder="PHP / Laravel&#10;MySQL Database Design&#10;RESTful API Development&#10;Git Version Control"
                                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">{{ old('skills') }}</textarea>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Benefits & Perks
                            <span class="text-[10px] text-slate-400 lowercase font-normal">(enter 1 per line)</span>
                        </label>
                        <textarea name="benefits" rows="2" placeholder="HMO health coverage on day 1&#10;13th & 14th month pay&#10;Flexible working hours"
                                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">{{ old('benefits') }}</textarea>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Other Application Instructions
                        </label>
                        <textarea name="other_instructions" rows="2" placeholder="Bring 2 copies of resume and 1 valid ID during initial interview."
                                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">{{ old('other_instructions') }}</textarea>
                    </div>
                </div>

                <!-- PWD Inclusivity Tag -->
                <div class="rounded-2xl bg-emerald-50/70 border border-emerald-200 p-4 space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="accepts_disability" value="1" {{ old('accepts_disability') ? 'checked' : '' }} class="h-4 w-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                        <span class="text-xs font-bold text-emerald-950">This job opening accepts and accommodates Persons with Disabilities (PWDs)</span>
                    </label>

                    <div class="space-y-1">
                        <input type="text" name="disability_type" value="{{ old('disability_type') }}" placeholder="Accommodation details (e.g. Visual/Hearing impaired with ramp access, remote option)"
                               class="w-full rounded-xl border border-emerald-200 px-3 py-2 text-xs bg-white text-slate-900 focus:border-emerald-500 focus:outline-none">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="createModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-lg shadow-emerald-600/30">
                        Send to Admin for Approval &rarr;
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- 2. Edit Job Posting Modal -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="editModal = false" class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Update Vacancy Information</span>
                    <h3 class="text-xl font-black text-slate-900 mt-0.5">Edit Job: <span x-text="selectedJob.title"></span></h3>
                </div>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <form :action="'/employer/job-postings/' + selectedJob.id" method="POST" class="space-y-4"
                  @submit="
                      const vDate = $el.querySelector('[name=valid_until]').value;
                      if (vDate) {
                          const today = new Date();
                          today.setHours(0,0,0,0);
                          const chosen = new Date(vDate + 'T00:00:00');
                          if (chosen < today) {
                              alert('The valid until date cannot be in the past.');
                              $event.preventDefault();
                              return false;
                          }
                      }
                  ">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2 space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Job Title / Role <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" x-model="selectedJob.title" required
                               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Employment Type <span class="text-rose-500">*</span></label>
                        <select name="job_type" x-model="selectedJob.job_type" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
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
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Location / Work Arrangement</label>
                        <input type="text" name="location" x-model="selectedJob.location" placeholder="e.g. Cebu City / Hybrid / On-site"
                               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Salary & Compensation</label>
                        <input type="text" name="salary_range" x-model="selectedJob.salary_range" placeholder="e.g. ₱25,000 - ₱35,000 / month, Negotiable"
                               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Number of Vacancies <span class="text-rose-500">*</span></label>
                        <input type="number" name="vacancy_count" min="1" x-model="selectedJob.vacancy_count" required
                               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Valid Until Date
                            <span class="text-[10px] text-emerald-700 lowercase font-normal">(cannot be in the past)</span>
                        </label>
                        <input type="date" name="valid_until" 
                               min="{{ date('Y-m-d') }}" 
                               x-model="selectedJob.valid_until"
                               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Job Description & Responsibilities <span class="text-rose-500">*</span></label>
                    <textarea name="description" rows="3" x-model="selectedJob.description" required
                              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Required Qualifications
                            <span class="text-[10px] text-slate-400 lowercase font-normal">(enter 1 per line)</span>
                        </label>
                        <textarea name="qualifications" rows="3" x-model="selectedJob.qualifications" placeholder="One qualification per line..."
                                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400"></textarea>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Required Skills
                            <span class="text-[10px] text-slate-400 lowercase font-normal">(enter 1 per line)</span>
                        </label>
                        <textarea name="skills" rows="3" x-model="selectedJob.skills" placeholder="One skill per line..."
                                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400"></textarea>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Benefits & Perks
                            <span class="text-[10px] text-slate-400 lowercase font-normal">(enter 1 per line)</span>
                        </label>
                        <textarea name="benefits" rows="2" x-model="selectedJob.benefits" placeholder="One benefit per line..."
                                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400"></textarea>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Other Application Instructions
                        </label>
                        <textarea name="other_instructions" rows="2" x-model="selectedJob.other_instructions" placeholder="Special interview or document instructions..."
                                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400"></textarea>
                    </div>
                </div>

                <!-- PWD Inclusivity Tag -->
                <div class="rounded-2xl bg-emerald-50/70 border border-emerald-200 p-4 space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="accepts_disability" value="1" x-model="selectedJob.accepts_disability" class="h-4 w-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                        <span class="text-xs font-bold text-emerald-950">This job opening accepts and accommodates Persons with Disabilities (PWDs)</span>
                    </label>

                    <div class="space-y-1">
                        <input type="text" name="disability_type" x-model="selectedJob.disability_type" placeholder="Accommodation details"
                               class="w-full rounded-xl border border-emerald-200 px-3 py-2 text-xs bg-white text-slate-900 focus:border-emerald-500 focus:outline-none">
                    </div>
                </div>

                <template x-if="selectedJob.status === 'rejected'">
                    <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-800 space-y-1">
                        <div class="flex items-center gap-1.5 font-bold">
                            <svg class="w-4 h-4 text-rose-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Note on Revision:</span>
                        </div>
                        <p>Updating this rejected job posting will automatically resubmit it to DMDP Administration for approval review.</p>
                    </div>
                </template>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="editModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-lg shadow-emerald-600/30">
                        Save Changes
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- 3. View Job Posting Details Modal -->
    <div x-show="viewModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="viewModal = false" class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6 max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Job Specification</span>
                    <h3 class="text-2xl font-black text-slate-900" x-text="selectedJob.title"></h3>
                    <p class="text-xs text-slate-400">Created: <span x-text="selectedJob.created_at"></span> &bull; Opening Ref #<span x-text="selectedJob.id"></span></p>
                </div>
                <button @click="viewModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <!-- Badges Bar -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <div>
                    <span class="text-slate-400 text-[10px] font-bold uppercase block">Status</span>
                    <span class="font-bold text-slate-900 uppercase" x-text="selectedJob.status"></span>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] font-bold uppercase block">Vacancies</span>
                    <span class="font-bold text-emerald-700" x-text="selectedJob.vacancy_count + ' Openings'"></span>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] font-bold uppercase block">Valid Until</span>
                    <span class="font-bold text-slate-700" x-text="selectedJob.valid_until_formatted || 'Continuous'"></span>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] font-bold uppercase block">Job Type</span>
                    <span class="font-bold text-slate-900" x-text="selectedJob.job_type || 'Full-time'"></span>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] font-bold uppercase block">Location</span>
                    <span class="font-bold text-slate-900" x-text="selectedJob.location || 'Cebu City'"></span>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] font-bold uppercase block">Salary & Comp</span>
                    <span class="font-bold text-emerald-700" x-text="selectedJob.salary_range || 'Competitive / Neg.'"></span>
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-2 text-xs">
                <h4 class="font-black uppercase tracking-wider text-slate-800">Job Description & Responsibilities</h4>
                <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-100 text-slate-700 leading-relaxed whitespace-pre-line" x-text="selectedJob.description"></div>
            </div>

            <!-- Required Qualifications in Bullets -->
            <div class="space-y-2 text-xs" x-show="selectedJob.qualifications">
                <h4 class="font-black uppercase tracking-wider text-slate-800">Qualifications & Requirements</h4>
                <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-100 text-slate-700">
                    <ul class="list-disc list-inside space-y-1.5 leading-relaxed">
                        <template x-for="(qual, idx) in getBulletList(selectedJob.qualifications)" :key="idx">
                            <li class="pl-1"><span x-text="qual"></span></li>
                        </template>
                    </ul>
                </div>
            </div>

            <!-- Required Skills in Bullets -->
            <div class="space-y-2 text-xs" x-show="selectedJob.skills">
                <h4 class="font-black uppercase tracking-wider text-slate-800">Required Skills</h4>
                <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-100 text-slate-700">
                    <ul class="list-disc list-inside space-y-1.5 leading-relaxed">
                        <template x-for="(skill, idx) in getBulletList(selectedJob.skills)" :key="idx">
                            <li class="pl-1"><span x-text="skill"></span></li>
                        </template>
                    </ul>
                </div>
            </div>

            <!-- Benefits & Perks in Bullets -->
            <div class="space-y-2 text-xs" x-show="selectedJob.benefits">
                <h4 class="font-black uppercase tracking-wider text-slate-800">Benefits & Perks</h4>
                <div class="p-4 rounded-2xl bg-emerald-50/50 border border-emerald-100 text-slate-700">
                    <ul class="list-disc list-inside space-y-1.5 leading-relaxed">
                        <template x-for="(benefit, idx) in getBulletList(selectedJob.benefits)" :key="idx">
                            <li class="pl-1"><span class="text-emerald-950 font-medium" x-text="benefit"></span></li>
                        </template>
                    </ul>
                </div>
            </div>

            <!-- Other Instructions -->
            <div class="space-y-2 text-xs" x-show="selectedJob.other_instructions">
                <h4 class="font-black uppercase tracking-wider text-slate-800">Application Instructions</h4>
                <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-100 text-slate-700 leading-relaxed whitespace-pre-line" x-text="selectedJob.other_instructions"></div>
            </div>

            <!-- PWD Accommodation -->
            <div class="p-3.5 rounded-2xl border text-xs"
                 :class="selectedJob.accepts_disability ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-slate-50 border-slate-200 text-slate-600'">
                <div class="flex items-center gap-1.5 font-bold">
                    <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>PWD Inclusivity:</span>
                </div>
                <p class="mt-1" x-text="selectedJob.accepts_disability ? ('Accommodated: ' + (selectedJob.disability_type || 'Open to PWD candidates')) : 'Standard recruitment process.'"></p>
            </div>

            <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="viewModal = false; openEdit(selectedJob.id)" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-500 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Edit Vacancy</span>
                    </button>
                    <a :href="'/employer/referred-jobseekers?job_id=' + selectedJob.id" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>View Applicants (<span x-text="selectedJob.applications_count"></span>)</span>
                    </a>
                    <a :href="'/employer/job-postings/' + selectedJob.id" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>Full Details Page</span>
                    </a>
                </div>
                <button type="button" @click="viewModal = false" class="px-5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                    Close
                </button>
            </div>

        </div>
    </div>

    <!-- 4. Delete Confirmation Modal -->
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="deleteModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-5">
            <div class="h-12 w-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <div class="space-y-1">
                <h3 class="text-lg font-black text-slate-900">Delete Job Posting?</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Are you sure you want to remove <span class="font-bold text-slate-900" x-text="selectedJob.title"></span>? If this opening has active jobseeker applications, it will be safely archived/closed instead.
                </p>
            </div>
            <form :action="'/employer/job-postings/' + selectedJob.id" method="POST" class="pt-2 flex items-center justify-end gap-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-black shadow-lg shadow-rose-600/30">
                    Confirm Delete
                </button>
            </form>
        </div>
    </div>

    <!-- 5. Close Job Confirmation Modal -->
    <div x-show="closeModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="closeModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-5">
            <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div class="space-y-1">
                <h3 class="text-lg font-black text-slate-900">Close Vacancy?</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Are you sure you want to close recruitment for <span class="font-bold text-slate-900" x-text="selectedJob.title"></span>? No new applicants will be accepted, but existing candidate records will be preserved.
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

<script>
function jobPostingsManager() {
    const jobsMap = @json($jobsDataMap);
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
        createModal: {{ (request()->has('create') && auth()->user()->is_approved) ? 'true' : 'false' }},
        editModal: {{ $editJob ? 'true' : 'false' }},
        viewModal: false,
        deleteModal: false,
        closeModal: false,
        isApproved: {{ auth()->user()->is_approved ? 'true' : 'false' }},
        jobsMap: jobsMap,
        selectedJob: Object.assign({}, initialJob),
        init() {
            @if($editJob)
                if (this.jobsMap[{{ $editJob->job_id }}]) {
                    this.selectedJob = Object.assign({}, initialJob, this.jobsMap[{{ $editJob->job_id }}]);
                    this.editModal = true;
                }
            @endif
        },
        getBulletList(text) {
            if (!text) return [];
            return text.split(/\r?\n/).map(s => s.trim().replace(/^[\u2022\-\*]\s*/, '')).filter(s => s.length > 0);
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
