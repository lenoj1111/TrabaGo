@extends('layouts.admin')

@section('title', 'Job Postings Management')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8" x-data="{ createModalOpen: false, acceptsDisability: false }">
    <div class="w-full max-w-[1680px] mx-auto space-y-8">

        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    DMDP Job Postings Hub
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Job Postings Management</h1>
                <p class="text-sm text-slate-300">
                    Create direct DMDP postings, review employer vacancy requests, manage expirations, and audit applicant submissions.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('admin.job-postings-list.export') }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-3 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/10 text-white text-xs font-bold transition-all">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Export CSV</span>
                </a>
                <button type="button" @click="createModalOpen = true" 
                        class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 px-5 py-3 text-xs font-black text-white shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                    <span>+ Create Job Posting</span>
                </button>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Postings</span>
                    <p class="text-3xl font-black text-slate-900">{{ $stats['total'] ?? 0 }}</p>
                    <span class="text-[11px] text-slate-500">All registered listings</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-slate-100 text-slate-800 flex items-center justify-center shadow-xs">
                    <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pending Review</span>
                    <p class="text-3xl font-black text-amber-600">{{ $stats['pending'] ?? 0 }}</p>
                    <span class="text-[11px] text-amber-700">Awaiting authorization</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-800 border border-amber-200 flex items-center justify-center shadow-xs">
                    <svg class="w-6 h-6 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Active Listings</span>
                    <p class="text-3xl font-black text-emerald-700">{{ $stats['approved'] ?? 0 }}</p>
                    <span class="text-[11px] text-emerald-800">Live on portal</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center justify-center shadow-xs">
                    <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Expired</span>
                    <p class="text-3xl font-black text-rose-700">{{ $stats['expired'] ?? 0 }}</p>
                    <span class="text-[11px] text-rose-800">Past deadline</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-rose-50 text-rose-800 border border-rose-200 flex items-center justify-center shadow-xs">
                    <svg class="w-6 h-6 text-rose-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="GET" action="{{ route('admin.job-postings-list.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
                <div class="lg:col-span-4 space-y-1">
                    <label class="text-xs font-bold text-slate-700">Search Title or Company</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Job title, employer..."
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                </div>

                <div class="lg:col-span-2 space-y-1">
                    <label class="text-xs font-bold text-slate-700">Status</label>
                    <select name="status" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                <div class="lg:col-span-2 space-y-1">
                    <label class="text-xs font-bold text-slate-700">Created By</label>
                    <select name="created_by" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        <option value="">All Creators</option>
                        <option value="employer" {{ request('created_by') == 'employer' ? 'selected' : '' }}>Employer</option>
                        <option value="admin" {{ request('created_by') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <div class="lg:col-span-2 space-y-1">
                    <label class="text-xs font-bold text-slate-700">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                </div>

                <div class="lg:col-span-2 flex items-center gap-2">
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('admin.job-postings-list.index') }}" class="px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Job Listings Table -->
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="p-6 sm:p-8 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-900">Job Postings Registry</h3>
                    <p class="text-xs text-slate-500">Live listings, vacancy status, and applicant application counters</p>
                </div>
                <span class="text-xs font-bold text-slate-400">{{ $jobPostings->total() ?? count($jobPostings) }} Listings</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-100 text-[11px] uppercase font-bold text-slate-500 tracking-wider">
                        <tr>
                            <th class="py-4 px-6">Job Title</th>
                            <th class="py-4 px-6">Employer / Entity</th>
                            <th class="py-4 px-6 text-center">Vacancies</th>
                            <th class="py-4 px-6 text-center">Status</th>
                            <th class="py-4 px-6 text-center">Source</th>
                            <th class="py-4 px-6">Valid Until</th>
                            <th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($jobPostings ?? [] as $job)
                            @php
                                $isExpired = $job->status === 'approved' && $job->valid_until < now()->toDateString();
                                $statusBadge = match($job->status) {
                                    'pending' => 'bg-amber-50 text-amber-800 border-amber-200',
                                    'approved' => $isExpired ? 'bg-slate-100 text-slate-700 border-slate-200' : 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                    'rejected' => 'bg-rose-50 text-rose-800 border-rose-200',
                                    'closed' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                                };
                                $statusText = $isExpired ? 'Expired' : ucfirst($job->status);
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6">
                                    <a href="{{ route('admin.job-postings-list.show', $job->job_id) }}" class="font-black text-slate-900 text-sm hover:text-emerald-700 transition-colors">
                                        {{ $job->title }}
                                    </a>
                                    <div class="text-[11px] text-slate-500">{{ $job->applications_count ?? 0 }} candidate applications</div>
                                </td>
                                <td class="py-4 px-6">
                                    @if($job->company_name === 'DMDP')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 font-bold text-[10px] border border-emerald-200">
                                            <svg class="w-3 h-3 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            <span>DMDP Direct</span>
                                        </span>
                                    @else
                                        <div class="font-bold text-slate-900">{{ $job->company_name ?? 'N/A' }}</div>
                                        <div class="text-[10px] text-slate-400 mb-1">{{ $job->employer_email ?? '' }}</div>
                                        <div>
                                            @if($job->is_accredited || ($job->accreditation_status ?? '') === 'admin_approved')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 text-[10px] font-bold border border-emerald-200">
                                                    <svg class="w-3 h-3 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                    <span>Accredited</span>
                                                </span>
                                            @elseif(in_array($job->accreditation_status ?? '', ['submitted_to_jpo', 'jpo_approved', 'supervisor_approved', 'manual_review', 'pending']))
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-amber-50 text-amber-800 text-[10px] font-bold border border-amber-200">
                                                    <svg class="w-3 h-3 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>Pending</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-slate-100 text-slate-600 text-[10px] font-bold border border-slate-200">
                                                    <svg class="w-3 h-3 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    <span>Not Accredited</span>
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 font-bold border border-slate-200">
                                        {{ $job->vacancy_count }} open
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-bold border {{ $statusBadge }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold border {{ $job->created_by === 'admin' ? 'bg-purple-50 text-purple-800 border-purple-200' : 'bg-blue-50 text-blue-800 border-blue-200' }}">
                                        {{ ucfirst($job->created_by ?? 'admin') }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-slate-600 font-medium">
                                    {{ $job->valid_until ? date('M d, Y', strtotime($job->valid_until)) : 'N/A' }}
                                    @if($isExpired)
                                        <span class="block text-[10px] text-rose-600 font-bold">Expired</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('admin.job-postings-list.show', $job->job_id) }}" 
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors" title="View Details">
                                            <svg class="w-3.5 h-3.5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>View</span>
                                        </a>
                                        <a href="{{ route('admin.job-postings-list.edit', $job->job_id) }}" 
                                           class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors" title="Edit Listing">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </a>

                                        @if($job->status === 'pending')
                                            <button onclick="approveJob({{ $job->job_id }})" 
                                                    class="p-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-colors shadow-sm" title="Approve">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                            <button onclick="rejectJob({{ $job->job_id }})" 
                                                    class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition-colors" title="Reject">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        @endif

                                        @if(in_array($job->status, ['approved', 'pending']))
                                            <button onclick="closeJob({{ $job->job_id }})" 
                                                    class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition-colors" title="Close Listing">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <div class="h-12 w-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <p class="font-bold text-slate-700">No job postings found</p>
                                    <p class="text-xs mt-0.5">Click "Create Job Posting" to add a new listing.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($jobPostings) && method_exists($jobPostings, 'hasPages') && $jobPostings->hasPages())
                <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                    {{ $jobPostings->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- ============================================ -->
    <!-- CREATE JOB POSTING MODAL (Tailwind + Alpine) -->
    <!-- ============================================ -->
    <div x-show="createModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div @click.away="createModalOpen = false" 
             x-show="createModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="w-full max-w-3xl rounded-3xl bg-white shadow-2xl border border-emerald-100 overflow-hidden">
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 text-white flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black tracking-tight">Create New Job Posting</h3>
                    <p class="text-xs text-slate-300">Admin-created job postings are automatically approved & live.</p>
                </div>
                <button @click="createModalOpen = false" class="text-slate-400 hover:text-white text-xl font-bold">&times;</button>
            </div>

            <!-- Modal Form -->
            <form method="POST" action="{{ route('admin.job-postings-list.store') }}" class="p-6 sm:p-8 space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Employer Entity</label>
                        <select name="employer_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                            <option value="">DMDP Direct (Default)</option>
                            @foreach($employers ?? [] as $emp)
                                <option value="{{ $emp->employer_id }}">{{ $emp->company_name }}</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-400">Leave blank for direct DMDP public posting.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Number of Vacancies <span class="text-rose-500">*</span></label>
                        <input type="number" name="vacancy_count" value="1" min="1" required 
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                    </div>

                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Job Title <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" required placeholder="e.g. Senior Software Engineer, Administrative Officer"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                    </div>

                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Job Description & Responsibilities <span class="text-rose-500">*</span></label>
                        <textarea name="description" rows="4" required placeholder="Detailed job summary..."
                                  class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none"></textarea>
                    </div>

                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Qualifications & Requirements</label>
                        <textarea name="qualifications" rows="3" placeholder="Education, years of experience, general qualifications..."
                                  class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none"></textarea>
                    </div>

                    <div class="sm:col-span-2 space-y-1.5">
                        <x-nsrp-skill-selector :selected="[]" name="skills[]" label="Required Skills (DOLE NSRP Form 1.REV 3 Compliant)" />
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Application Deadline (Valid Until) <span class="text-rose-500">*</span></label>
                        <input type="date" name="valid_until" required min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                    </div>

                    <div class="space-y-2 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700 select-none">
                            <input type="checkbox" name="accepts_disability" value="1" x-model="acceptsDisability"
                                   class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-600" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="4" r="2"/><path d="M15.5 13.5A4.5 4.5 0 0 1 11 18a4.5 4.5 0 0 1-4.5-4.5A4.5 4.5 0 0 1 11 9c.4 0 .78.06 1.14.16L13.5 6.5C12.7 6.18 11.87 6 11 6a7.5 7.5 0 0 0-7.5 7.5A7.5 7.5 0 0 0 11 21a7.5 7.5 0 0 0 7.42-6.5h-2.92z"/><path d="M19 10h-4.5l-1.3-2.6A2 2 0 0 0 11.4 6.2L8.5 7.6 9.4 9.4l2.1-1 2 4.1V17h2v-4.5l1.6-1.6H19v-2z"/></svg>
                                <span>Accepts PWD Applicants</span>
                            </span>
                        </label>
                        <div x-show="acceptsDisability" style="display: none;" class="pt-1">
                            <input type="text" name="disability_type" placeholder="Specify disability types (e.g. Visual, Hearing, Orthopedic)"
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <button type="button" @click="createModalOpen = false" 
                            class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="inline-flex items-center gap-1.5 px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-black text-xs shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Publish & Approve</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>

<script>
function approveJob(id) {
    Swal.fire({
        title: 'Approve Job Posting?',
        text: 'This will publish the job posting immediately to all registered jobseekers.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, approve listing'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/job-postings-list/${id}/approve`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Approved!', data.success, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.error || 'Something went wrong.', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Network error occurred.', 'error'));
        }
    });
}

function rejectJob(id) {
    Swal.fire({
        title: 'Reject Job Posting?',
        text: 'Are you sure you want to reject this job posting?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, reject listing'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/job-postings-list/${id}/reject`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Rejected!', data.success, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.error || 'Something went wrong.', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Network error occurred.', 'error'));
        }
    });
}

function closeJob(id) {
    Swal.fire({
        title: 'Close Job Posting?',
        text: 'This will close the listing and stop accepting new candidate applications.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#64748b',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Yes, close listing'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/job-postings-list/${id}/close`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Closed!', data.success, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.error || 'Something went wrong.', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Network error occurred.', 'error'));
        }
    });
}
</script>
@endsection