@extends('layouts.admin')

@section('title', 'Job Postings Management')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8" x-data="{ createModalOpen: false, acceptsDisability: false }">
    <div class="mx-auto max-w-7xl space-y-8">

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
                    <span>📥 Export CSV</span>
                </a>
                <button type="button" @click="createModalOpen = true" 
                        class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 px-5 py-3 text-xs font-black text-white shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
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
                <div class="h-12 w-12 rounded-2xl bg-slate-100 text-slate-800 flex items-center justify-center text-xl font-black">
                    💼
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pending Review</span>
                    <p class="text-3xl font-black text-amber-600">{{ $stats['pending'] ?? 0 }}</p>
                    <span class="text-[11px] text-amber-700">Awaiting authorization</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-800 border border-amber-200 flex items-center justify-center text-xl font-black">
                    ⏳
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Active Listings</span>
                    <p class="text-3xl font-black text-emerald-700">{{ $stats['approved'] ?? 0 }}</p>
                    <span class="text-[11px] text-emerald-800">Live on portal</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center justify-center text-xl font-black">
                    ✓
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Expired</span>
                    <p class="text-3xl font-black text-rose-700">{{ $stats['expired'] ?? 0 }}</p>
                    <span class="text-[11px] text-rose-800">Past deadline</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-rose-50 text-rose-800 border border-rose-200 flex items-center justify-center text-xl font-black">
                    ⏱️
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
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 font-bold text-[10px] border border-emerald-200">
                                            🏛️ DMDP Direct
                                        </span>
                                    @else
                                        <div class="font-bold text-slate-900">{{ $job->company_name ?? 'N/A' }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $job->employer_email ?? '' }}</div>
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
                                           class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors" title="View Details">
                                            👁️ View
                                        </a>
                                        <a href="{{ route('admin.job-postings-list.edit', $job->job_id) }}" 
                                           class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors" title="Edit Listing">
                                            ✏️
                                        </a>

                                        @if($job->status === 'pending')
                                            <button onclick="approveJob({{ $job->job_id }})" 
                                                    class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-colors shadow-sm" title="Approve">
                                                ✓
                                            </button>
                                            <button onclick="rejectJob({{ $job->job_id }})" 
                                                    class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition-colors" title="Reject">
                                                ✕
                                            </button>
                                        @endif

                                        @if(in_array($job->status, ['approved', 'pending']))
                                            <button onclick="closeJob({{ $job->job_id }})" 
                                                    class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition-colors" title="Close Listing">
                                                🔒
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <div class="text-3xl mb-2">💼</div>
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
                        <label class="text-xs font-bold text-slate-700">Qualifications & Skills Required</label>
                        <textarea name="qualifications" rows="3" placeholder="Education, years of experience, skill certifications..."
                                  class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none"></textarea>
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
                            <span>♿ Accepts PWD Applicants</span>
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
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-black text-xs shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                        ✓ Publish & Approve
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