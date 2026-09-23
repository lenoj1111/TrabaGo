@extends('layouts.employer')

@section('title', 'Manage Job Postings - Employer Portal')

@section('content')
@php
    $editJob = null;
    if (request('edit_id')) {
        $editJob = $jobs->firstWhere('job_id', request('edit_id')) ?? \App\Models\JobPosting::where('employer_id', $employer->employer_id)->find(request('edit_id'));
    }
@endphp
<div x-data="{ 
    createModal: {{ request()->has('create') ? 'true' : 'false' }},
    editModal: {{ $editJob ? 'true' : 'false' }},
    viewModal: false,
    deleteModal: false,
    closeModal: false,
    selectedJob: {!! $editJob ? json_encode([
        'id' => $editJob->job_id,
        'title' => $editJob->title,
        'description' => $editJob->description,
        'qualifications' => $editJob->qualifications ?? '',
        'vacancy_count' => $editJob->vacancy_count,
        'valid_until' => $editJob->valid_until ? date('Y-m-d', strtotime($editJob->valid_until)) : '',
        'valid_until_formatted' => $editJob->valid_until ? date('M d, Y', strtotime($editJob->valid_until)) : 'Continuous',
        'accepts_disability' => (bool)$editJob->accepts_disability,
        'disability_type' => $editJob->disability_type ?? '',
        'status' => $editJob->status,
        'applications_count' => $editJob->applications_count ?? 0,
        'created_at' => $editJob->created_at ? date('M d, Y', strtotime($editJob->created_at)) : '',
    ]) : '{
        id: null,
        title: \'\',
        description: \'\',
        qualifications: \'\',
        vacancy_count: 1,
        valid_until: \'\',
        valid_until_formatted: \'\',
        accepts_disability: false,
        disability_type: \'\',
        status: \'\',
        applications_count: 0,
        created_at: \'\'
    }' !!},
    openView(job) {
        this.selectedJob = { ...job };
        this.viewModal = true;
    },
    openEdit(job) {
        this.selectedJob = { ...job };
        this.editModal = true;
    },
    openDelete(job) {
        this.selectedJob = { ...job };
        this.deleteModal = true;
    },
    openClose(job) {
        this.selectedJob = { ...job };
        this.closeModal = true;
    }
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Header in green Theme -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-green-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-green-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-400/20 px-3 py-1 text-xs font-bold text-green-300 border border-green-400/30">
                    <span class="h-2 w-2 rounded-full bg-green-400 animate-pulse"></span>
                    Job Postings Pipeline
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Job Postings Management</h1>
                <p class="text-sm text-slate-300">Create, edit, and manage company vacancies. Openings sent to DMDP Administration are reviewed and matched with jobseekers via AI.</p>
            </div>

            <button @click="createModal = true" 
                    class="shrink-0 inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 px-6 py-3.5 text-xs font-black text-white shadow-lg shadow-green-600/30 transition-all hover:scale-105">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                + Create New Job Opening
            </button>
        </div>

        <!-- Flash Messages & Validation Alerts -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-green-50 border border-green-200 text-green-800 text-xs font-bold flex items-center gap-2">
                <span>✓</span> {{ session('success') }}
            </div>
        @endif
        @if(session('info'))
            <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-xs font-bold flex items-center gap-2">
                <span>ℹ</span> {{ session('info') }}
            </div>
        @endif
        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1">
                <span class="block font-black">Please correct the following errors:</span>
                <ul class="list-disc list-inside font-medium">
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
            <div class="bg-white rounded-2xl p-4 border border-green-200 bg-green-50/20 shadow-sm">
                <span class="text-[11px] font-bold text-green-700 uppercase">Active / Live</span>
                <p class="text-2xl font-black text-green-700 mt-1">{{ $stats['approved'] ?? 0 }}</p>
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
                   class="px-3 py-1.5 rounded-xl transition-colors {{ (!request()->has('status') || request()->status === 'all') ? 'bg-green-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    All
                </a>
                <a href="{{ route('employer.job-postings', array_merge(request()->except('page'), ['status' => 'approved'])) }}"
                   class="px-3 py-1.5 rounded-xl transition-colors {{ request()->status === 'approved' ? 'bg-green-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
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
                           class="w-full rounded-xl border border-slate-200 pl-9 pr-4 py-2 text-xs text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-400">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-green-600 text-white text-xs font-bold transition-colors">
                    Search
                </button>
            </form>
        </div>

        <!-- Job Postings List Table -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h2 class="text-lg font-black text-slate-900">Your Company Vacancies ({{ $jobs->total() }})</h2>
                <span class="text-xs font-bold text-slate-500">Full CRUD & DMDP Pipeline Tracking</span>
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
                            @php
                                $jobData = [
                                    'id' => $job->job_id,
                                    'title' => $job->title,
                                    'description' => $job->description,
                                    'qualifications' => $job->qualifications ?? '',
                                    'vacancy_count' => $job->vacancy_count,
                                    'valid_until' => $job->valid_until ? date('Y-m-d', strtotime($job->valid_until)) : '',
                                    'valid_until_formatted' => $job->valid_until ? date('M d, Y', strtotime($job->valid_until)) : 'Continuous',
                                    'accepts_disability' => (bool)$job->accepts_disability,
                                    'disability_type' => $job->disability_type ?? '',
                                    'status' => $job->status,
                                    'applications_count' => $job->applications_count ?? 0,
                                    'created_at' => $job->created_at ? date('M d, Y', strtotime($job->created_at)) : 'Today'
                                ];
                                $jobJson = json_encode($jobData);
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-3">
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click='openView({!! $jobJson !!})' class="text-left group">
                                            <span class="font-bold text-slate-900 group-hover:text-green-700 transition-colors block text-sm">{{ $job->title }}</span>
                                            <span class="text-[11px] text-slate-400 line-clamp-1 max-w-sm">{{ $job->description }}</span>
                                        </button>
                                    </div>
                                </td>
                                <td class="py-4 px-3 font-bold text-slate-900">
                                    <span class="inline-flex items-center gap-1">
                                        {{ $job->vacancy_count }} open
                                        @if(($job->applications_count ?? 0) > 0)
                                            <a href="{{ route('employer.referred-jobseekers', ['job_id' => $job->job_id]) }}" class="text-[10px] text-green-700 font-bold bg-green-50 border border-green-200 px-1.5 py-0.5 rounded-md hover:underline" title="View applicants">
                                                ({{ $job->applications_count }} applicants)
                                            </a>
                                        @endif
                                    </span>
                                </td>
                                <td class="py-4 px-3">
                                    @if($job->accepts_disability)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-green-50 border border-green-200 px-2.5 py-0.5 text-[11px] font-bold text-green-800">
                                            ♿ {{ $job->disability_type ?: 'PWD Inclusive' }}
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
                                        Continuous
                                    @endif
                                </td>
                                <td class="py-4 px-3">
                                    @if($job->status === 'approved')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-green-100 border border-green-300 px-2.5 py-0.5 text-xs font-bold text-green-800">
                                            ✓ Live & Approved
                                        </span>
                                    @elseif($job->status === 'pending')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 border border-amber-300 px-2.5 py-0.5 text-xs font-bold text-amber-800">
                                            ⏳ Sent to Admin (Pending)
                                        </span>
                                    @elseif($job->status === 'rejected')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 border border-rose-300 px-2.5 py-0.5 text-xs font-bold text-rose-800">
                                            ✕ Needs Revision
                                        </span>
                                    @elseif($job->status === 'closed')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 border border-slate-300 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                                            📁 Closed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                                            {{ ucfirst($job->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-3 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- View Details -->
                                        <button type="button" @click='openView({!! $jobJson !!})'
                                                class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors"
                                                title="View Job Details">
                                            👁️ View
                                        </button>

                                        <!-- Edit Job -->
                                        <button type="button" @click='openEdit({!! $jobJson !!})'
                                                class="px-2.5 py-1 rounded-lg bg-green-50 hover:bg-green-100 text-green-800 font-bold text-xs border border-green-200 transition-colors"
                                                title="Edit Job Vacancy">
                                            ✏️ Edit
                                        </button>

                                        <!-- Close or Delete -->
                                        @if($job->status !== 'closed')
                                            <button type="button" @click='openClose({!! $jobJson !!})'
                                                    class="p-1 rounded-lg text-slate-400 hover:text-amber-700 hover:bg-amber-50 transition-colors"
                                                    title="Close Vacancy">
                                                🔒
                                            </button>
                                        @endif

                                        <button type="button" @click='openDelete({!! $jobJson !!})'
                                                class="p-1 rounded-lg text-slate-400 hover:text-rose-700 hover:bg-rose-50 transition-colors"
                                                title="Delete Job">
                                            🗑️
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400 italic">
                                    No job openings found matching the criteria. Click "+ Create New Job Opening" above to post your vacancy.
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

    <!-- 1. Create Job Posting Modal (Past Date Strictly Prevented) -->
    <div x-show="createModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="createModal = false" class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-green-700 uppercase tracking-wider">New Vacancy Submission</span>
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

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Job Title / Role <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" required value="{{ old('title') }}" placeholder="e.g. Senior PHP / Laravel Developer, Customer Service Associate"
                           class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Number of Vacancies <span class="text-rose-500">*</span></label>
                        <input type="number" name="vacancy_count" min="1" value="{{ old('vacancy_count', 1) }}" required
                               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Valid Until Date <span class="text-rose-500">*</span>
                            <span class="text-[10px] text-green-700 lowercase font-normal">(cannot be in the past)</span>
                        </label>
                        <input type="date" name="valid_until" 
                               min="{{ date('Y-m-d') }}" 
                               value="{{ old('valid_until', date('Y-m-d', strtotime('+2 months'))) }}" required
                               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Job Description & Responsibilities <span class="text-rose-500">*</span></label>
                    <textarea name="description" rows="3" required placeholder="Detail the core duties, daily tasks, and team environment..."
                              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-400">{{ old('description') }}</textarea>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Required Skills & Qualifications</label>
                    <textarea name="qualifications" rows="2" placeholder="e.g. Bachelor's or Vocational Graduate, PHP, SQL, Problem-Solving..."
                              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-400">{{ old('qualifications') }}</textarea>
                </div>

                <!-- PWD Inclusivity Tag -->
                <div class="rounded-2xl bg-green-50/70 border border-green-200 p-4 space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="accepts_disability" value="1" {{ old('accepts_disability') ? 'checked' : '' }} class="h-4 w-4 rounded text-green-600 focus:ring-green-500 border-slate-300">
                        <span class="text-xs font-bold text-green-950">This job opening accepts and accommodates Persons with Disabilities (PWDs)</span>
                    </label>

                    <div class="space-y-1">
                        <input type="text" name="disability_type" value="{{ old('disability_type') }}" placeholder="Accommodation details (e.g. Visual/Hearing impaired with ramp access, remote option)"
                               class="w-full rounded-xl border border-green-200 px-3 py-2 text-xs bg-white text-slate-900 focus:border-green-500 focus:outline-none">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="createModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-green-600 hover:bg-green-500 text-white text-xs font-black shadow-lg shadow-green-600/30">
                        Send to Admin for Approval &rarr;
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- 2. Edit Job Posting Modal (Past Date Strictly Prevented) -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="editModal = false" class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-green-700 uppercase tracking-wider">Update Vacancy Information</span>
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

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Job Title / Role <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" x-model="selectedJob.title" required
                           class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Number of Vacancies <span class="text-rose-500">*</span></label>
                        <input type="number" name="vacancy_count" min="1" x-model="selectedJob.vacancy_count" required
                               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Valid Until Date
                            <span class="text-[10px] text-green-700 lowercase font-normal">(cannot be in the past)</span>
                        </label>
                        <input type="date" name="valid_until" 
                               min="{{ date('Y-m-d') }}" 
                               x-model="selectedJob.valid_until"
                               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Job Description & Responsibilities <span class="text-rose-500">*</span></label>
                    <textarea name="description" rows="3" x-model="selectedJob.description" required
                              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-400"></textarea>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Required Skills & Qualifications</label>
                    <textarea name="qualifications" rows="2" x-model="selectedJob.qualifications"
                              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-400"></textarea>
                </div>

                <!-- PWD Inclusivity Tag -->
                <div class="rounded-2xl bg-green-50/70 border border-green-200 p-4 space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="accepts_disability" value="1" x-model="selectedJob.accepts_disability" class="h-4 w-4 rounded text-green-600 focus:ring-green-500 border-slate-300">
                        <span class="text-xs font-bold text-green-950">This job opening accepts and accommodates Persons with Disabilities (PWDs)</span>
                    </label>

                    <div class="space-y-1">
                        <input type="text" name="disability_type" x-model="selectedJob.disability_type" placeholder="Accommodation details"
                               class="w-full rounded-xl border border-green-200 px-3 py-2 text-xs bg-white text-slate-900 focus:border-green-500 focus:outline-none">
                    </div>
                </div>

                <template x-if="selectedJob.status === 'rejected'">
                    <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-800 space-y-1">
                        <span class="font-bold block">💡 Note on Revision:</span>
                        <p>Updating this rejected job posting will automatically resubmit it to DMDP Administration for approval review.</p>
                    </div>
                </template>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="editModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-green-600 hover:bg-green-500 text-white text-xs font-black shadow-lg shadow-green-600/30">
                        Save Changes
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- 3. View Job Posting Details Modal -->
    <div x-show="viewModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="viewModal = false" class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-green-700 uppercase tracking-wider">Job Specification</span>
                    <h3 class="text-2xl font-black text-slate-900" x-text="selectedJob.title"></h3>
                    <p class="text-xs text-slate-400">Created: <span x-text="selectedJob.created_at"></span> &bull; Opening Ref #<span x-text="selectedJob.id"></span></p>
                </div>
                <button @click="viewModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <!-- Badges Bar -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <div>
                    <span class="text-slate-400 text-[10px] font-bold uppercase block">Status</span>
                    <span class="font-bold text-slate-900 uppercase" x-text="selectedJob.status"></span>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] font-bold uppercase block">Vacancies</span>
                    <span class="font-bold text-green-700" x-text="selectedJob.vacancy_count + ' Openings'"></span>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] font-bold uppercase block">Valid Until</span>
                    <span class="font-bold text-slate-700" x-text="selectedJob.valid_until_formatted || 'Continuous'"></span>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] font-bold uppercase block">Applicants</span>
                    <a :href="'/employer/referred-jobseekers?job_id=' + selectedJob.id" class="font-bold text-green-700 hover:underline" x-text="selectedJob.applications_count + ' Applied'"></a>
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-2 text-xs">
                <h4 class="font-black uppercase tracking-wider text-slate-800">Job Description & Responsibilities</h4>
                <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-100 text-slate-700 leading-relaxed whitespace-pre-line" x-text="selectedJob.description"></div>
            </div>

            <!-- Qualifications -->
            <div class="space-y-2 text-xs" x-show="selectedJob.qualifications">
                <h4 class="font-black uppercase tracking-wider text-slate-800">Skills & Qualifications</h4>
                <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-100 text-slate-700 leading-relaxed whitespace-pre-line" x-text="selectedJob.qualifications"></div>
            </div>

            <!-- PWD Accommodation -->
            <div class="p-3.5 rounded-2xl border text-xs"
                 :class="selectedJob.accepts_disability ? 'bg-green-50 border-green-200 text-green-900' : 'bg-slate-50 border-slate-200 text-slate-600'">
                <span class="font-bold block">♿ PWD Inclusivity:</span>
                <p x-text="selectedJob.accepts_disability ? ('Accommodated: ' + (selectedJob.disability_type || 'Open to PWD candidates')) : 'Standard recruitment process.'"></p>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <button type="button" @click="viewModal = false; openEdit(selectedJob)" class="px-4 py-2 rounded-xl bg-green-600 text-white text-xs font-bold hover:bg-green-500 transition-colors">
                        ✏️ Edit Vacancy
                    </button>
                    <a :href="'/employer/referred-jobseekers?job_id=' + selectedJob.id" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200 transition-colors">
                        👥 View Applicants (<span x-text="selectedJob.applications_count"></span>)
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
            <div class="h-12 w-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl font-bold">
                🗑️
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
            <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                🔒
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
@endsection

