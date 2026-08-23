@extends('layouts.admin')

@section('title', 'Job Posting Details')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <a href="{{ route('admin.job-postings') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 inline-flex items-center gap-1">
                    &larr; Back to Job Postings Registry
                </a>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ $jobPosting->title }}</h1>
                <p class="text-xs text-slate-500">Posting ID #{{ $jobPosting->job_id }} &bull; Posted by {{ $jobPosting->company_name ?? 'DMDP Direct' }}</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.job-postings.edit', $jobPosting->job_id) }}" 
                   class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                    ✏️ Edit Listing
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left 2 Cols: Main Job Details & Applications -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Job Overview Card -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                        <h2 class="text-lg font-black text-slate-900">Listing Information</h2>
                        @php
                            $isExpired = $jobPosting->status === 'approved' && $jobPosting->valid_until < now()->toDateString();
                            $statusBadge = match($jobPosting->status) {
                                'pending' => 'bg-amber-50 text-amber-800 border-amber-200',
                                'approved' => $isExpired ? 'bg-slate-100 text-slate-700 border-slate-200' : 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                'rejected' => 'bg-rose-50 text-rose-800 border-rose-200',
                                'closed' => 'bg-slate-100 text-slate-700 border-slate-200',
                                default => 'bg-slate-100 text-slate-700 border-slate-200',
                            };
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-bold border {{ $statusBadge }}">
                            {{ $isExpired ? 'Expired' : ucfirst($jobPosting->status) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                            <span class="text-[10px] font-bold uppercase text-slate-400">Employer Entity</span>
                            <p class="font-black text-slate-900 text-sm">{{ $jobPosting->company_name ?? 'DMDP Direct' }}</p>
                            <p class="text-[11px] text-slate-500">{{ $jobPosting->employer_email ?? 'DMDP Public Portal' }}</p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                            <span class="text-[10px] font-bold uppercase text-slate-400">Open Vacancies</span>
                            <p class="font-black text-emerald-700 text-sm">{{ $jobPosting->vacancy_count }} positions</p>
                            <p class="text-[11px] text-slate-500">Available quota</p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                            <span class="text-[10px] font-bold uppercase text-slate-400">Application Deadline</span>
                            <p class="font-black text-slate-900 text-sm">{{ \Carbon\Carbon::parse($jobPosting->valid_until)->format('F d, Y') }}</p>
                            @if($isExpired)
                                <span class="text-[10px] font-bold text-rose-600">Past Deadline</span>
                            @else
                                <span class="text-[10px] font-bold text-emerald-700">Currently Active</span>
                            @endif
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                            <span class="text-[10px] font-bold uppercase text-slate-400">PWD Inclusivity</span>
                            @if($jobPosting->accepts_disability)
                                <p class="font-bold text-emerald-800 text-xs">♿ {{ $jobPosting->disability_type ?: 'All PWD Applicants' }}</p>
                            @else
                                <p class="text-slate-500 font-medium">Standard Listing</p>
                            @endif
                        </div>
                    </div>

                    <!-- Job Description -->
                    <div class="space-y-2 pt-2">
                        <h3 class="text-xs font-bold uppercase text-slate-400 tracking-wider">Job Description</h3>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                            {{ $jobPosting->description }}
                        </div>
                    </div>

                    <!-- Qualifications -->
                    @if($jobPosting->qualifications)
                        <div class="space-y-2">
                            <h3 class="text-xs font-bold uppercase text-slate-400 tracking-wider">Qualifications & Requirements</h3>
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                                {{ $jobPosting->qualifications }}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Candidate Applications Table -->
                <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="p-6 sm:p-8 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-black text-slate-900">Submitted Applications</h3>
                            <p class="text-xs text-slate-500">Candidates who applied for this vacancy</p>
                        </div>
                        <span class="text-xs font-bold text-slate-400">{{ $applications->count() }} Candidates</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 border-b border-slate-100 text-[11px] uppercase font-bold text-slate-500 tracking-wider">
                                <tr>
                                    <th class="py-4 px-6">Candidate</th>
                                    <th class="py-4 px-6">Contact</th>
                                    <th class="py-4 px-6 text-center">Status</th>
                                    <th class="py-4 px-6">JPO Assessment</th>
                                    <th class="py-4 px-6">Applied Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                                @forelse($applications as $app)
                                    @php
                                        $appBadge = match($app->status) {
                                            'pending' => 'bg-amber-50 text-amber-800 border-amber-200',
                                            'interview' => 'bg-blue-50 text-blue-800 border-blue-200',
                                            'hired' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                            'rejected' => 'bg-rose-50 text-rose-800 border-rose-200',
                                            default => 'bg-slate-100 text-slate-700 border-slate-200',
                                        };
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="py-4 px-6">
                                            <div class="font-black text-slate-900 text-sm">{{ $app->first_name }} {{ $app->last_name }}</div>
                                            <div class="text-[11px] text-slate-500">Candidate ID #{{ $app->jobseeker_id }}</div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="font-bold text-slate-900">{{ $app->email ?? $app->jobseeker_email }}</div>
                                            <div class="text-[11px] text-slate-500">{{ $app->mobile_number ?: 'N/A' }}</div>
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-bold border {{ $appBadge }}">
                                                {{ ucfirst($app->status) }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6">
                                            @if(isset($app->recommendation) && $app->recommendation)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 text-[10px] font-bold border border-emerald-200">
                                                    ✓ {{ ucfirst($app->recommendation) }}
                                                </span>
                                            @else
                                                <span class="text-slate-400 font-medium">Under Review</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-slate-500">
                                            {{ \Carbon\Carbon::parse($app->created_at)->format('M d, Y') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-12 text-center text-slate-400">
                                            <div class="text-3xl mb-2">📄</div>
                                            <p class="font-bold text-slate-700">No applications received yet</p>
                                            <p class="text-xs mt-0.5">Candidates who apply will appear here.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Right Col: Actions & Statistics -->
            <div class="space-y-6">
                
                <!-- Quick Actions Card -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100">
                        Admin Controls
                    </h3>

                    <div class="space-y-2">
                        @if($jobPosting->status === 'pending')
                            <button onclick="approveJob({{ $jobPosting->job_id }})" 
                                    class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors shadow-sm">
                                ✓ Approve & Publish Listing
                            </button>
                            <button onclick="rejectJob({{ $jobPosting->job_id }})" 
                                    class="w-full py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200 transition-colors">
                                ✕ Reject Job Posting
                            </button>
                        @endif

                        @if(in_array($jobPosting->status, ['approved', 'pending']))
                            <button onclick="closeJob({{ $jobPosting->job_id }})" 
                                    class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors border border-slate-200">
                                🔒 Close Job Listing
                            </button>
                        @endif

                        <a href="{{ route('admin.job-postings.edit', $jobPosting->job_id) }}" 
                           class="w-full block text-center py-2.5 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold transition-colors">
                            ✏️ Edit Listing Details
                        </a>
                    </div>
                </div>

                <!-- Pipeline Statistics Card -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100">
                        Candidate Pipeline
                    </h3>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                            <span class="text-slate-600 font-bold">Total Applicants</span>
                            <span class="font-black text-slate-900">{{ $applications->count() }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-2xl bg-amber-50/60 border border-amber-200/60 text-xs">
                            <span class="text-amber-800 font-bold">Pending Screening</span>
                            <span class="font-black text-amber-800">{{ $applications->where('status', 'pending')->count() }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-2xl bg-blue-50/60 border border-blue-200/60 text-xs">
                            <span class="text-blue-800 font-bold">In Interview</span>
                            <span class="font-black text-blue-800">{{ $applications->where('status', 'interview')->count() }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-2xl bg-emerald-50/60 border border-emerald-200/60 text-xs">
                            <span class="text-emerald-800 font-bold">Hired Placements</span>
                            <span class="font-black text-emerald-800">{{ $applications->where('status', 'hired')->count() }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-2xl bg-rose-50/60 border border-rose-200/60 text-xs">
                            <span class="text-rose-800 font-bold">Rejected</span>
                            <span class="font-black text-rose-800">{{ $applications->where('status', 'rejected')->count() }}</span>
                        </div>
                    </div>
                </div>

            </div>

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