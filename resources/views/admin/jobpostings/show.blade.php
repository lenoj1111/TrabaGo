@extends('layouts.admin')

@section('title', 'Job Posting Details')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="w-full max-w-[1680px] mx-auto space-y-8">

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
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    <span>Edit Listing</span>
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

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                            <span class="text-[10px] font-bold uppercase text-slate-400">Employer Entity</span>
                            <p class="font-black text-slate-900 text-sm">{{ $jobPosting->company_name ?? 'DMDP Direct' }}</p>
                            <p class="text-[11px] text-slate-500">{{ $jobPosting->employer_email ?? 'DMDP Public Portal' }}</p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                            <span class="text-[10px] font-bold uppercase text-slate-400">Job Type & Location</span>
                            <p class="font-black text-slate-900 text-sm">{{ $jobPosting->job_type ?? 'Full-time' }}</p>
                            <p class="text-[11px] text-slate-500 inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>{{ $jobPosting->location ?? 'Cebu City' }}</span>
                            </p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                            <span class="text-[10px] font-bold uppercase text-slate-400">Compensation</span>
                            <p class="font-black text-emerald-700 text-sm">{{ $jobPosting->salary_range ?: 'Competitive / Negotiable' }}</p>
                            <p class="text-[11px] text-slate-500">Salary & benefits package</p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                            <span class="text-[10px] font-bold uppercase text-slate-400">Open Vacancies</span>
                            <p class="font-black text-emerald-700 text-sm">{{ $jobPosting->vacancy_count }} positions</p>
                            <p class="text-[11px] text-slate-500">Available quota</p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                            <span class="text-[10px] font-bold uppercase text-slate-400">Application Deadline</span>
                            <p class="font-black text-slate-900 text-sm">{{ $jobPosting->valid_until ? \Carbon\Carbon::parse($jobPosting->valid_until)->format('F d, Y') : 'Open Continuous' }}</p>
                            @if($isExpired)
                                <span class="text-[10px] font-bold text-rose-600">Past Deadline</span>
                            @else
                                <span class="text-[10px] font-bold text-emerald-700">Currently Active</span>
                            @endif
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                            <span class="text-[10px] font-bold uppercase text-slate-400">PWD Inclusivity</span>
                            @if($jobPosting->accepts_disability)
                                <p class="font-bold text-emerald-800 text-xs inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="4" r="2"/><path d="M15.5 13.5A4.5 4.5 0 0 1 11 18a4.5 4.5 0 0 1-4.5-4.5A4.5 4.5 0 0 1 11 9c.4 0 .78.06 1.14.16L13.5 6.5C12.7 6.18 11.87 6 11 6a7.5 7.5 0 0 0-7.5 7.5A7.5 7.5 0 0 0 11 21a7.5 7.5 0 0 0 7.42-6.5h-2.92z"/><path d="M19 10h-4.5l-1.3-2.6A2 2 0 0 0 11.4 6.2L8.5 7.6 9.4 9.4l2.1-1 2 4.1V17h2v-4.5l1.6-1.6H19v-2z"/></svg>
                                    <span>{{ $jobPosting->disability_type ?: 'All PWD Applicants' }}</span>
                                </p>
                            @else
                                <p class="text-slate-500 font-medium">Standard Listing</p>
                            @endif
                        </div>
                    </div>

                    <!-- Job Description -->
                    <div class="space-y-2 pt-2">
                        <h3 class="text-xs font-bold uppercase text-slate-400 tracking-wider">Job Description & Responsibilities</h3>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                            {{ $jobPosting->description }}
                        </div>
                    </div>

                    <!-- Qualifications -->
                    @if(!empty($jobPosting->qualifications_list))
                        <div class="space-y-2">
                            <h3 class="text-xs font-bold uppercase text-slate-400 tracking-wider">Qualifications & Requirements</h3>
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-700 leading-relaxed">
                                <ul class="list-disc list-inside space-y-1.5">
                                    @foreach($jobPosting->qualifications_list as $qual)
                                        <li class="pl-1">{{ $qual }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @elseif($jobPosting->qualifications)
                        <div class="space-y-2">
                            <h3 class="text-xs font-bold uppercase text-slate-400 tracking-wider">Qualifications & Requirements</h3>
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                                {{ $jobPosting->qualifications }}
                            </div>
                        </div>
                    @endif

                    <!-- Skills -->
                    @if(!empty($jobPosting->skills_list))
                        <div class="space-y-2">
                            <h3 class="text-xs font-bold uppercase text-slate-400 tracking-wider">Required Skills</h3>
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-700 leading-relaxed">
                                <ul class="list-disc list-inside space-y-1.5">
                                    @foreach($jobPosting->skills_list as $skill)
                                        <li class="pl-1 font-semibold text-slate-900">{{ $skill }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <!-- Benefits & Perks -->
                    @if(!empty($jobPosting->benefits_list))
                        <div class="space-y-2">
                            <h3 class="text-xs font-bold uppercase text-emerald-800 tracking-wider">Benefits & Perks</h3>
                            <div class="p-4 rounded-2xl bg-emerald-50/50 border border-emerald-200 text-xs text-emerald-950 leading-relaxed">
                                <ul class="list-disc list-inside space-y-1.5">
                                    @foreach($jobPosting->benefits_list as $benefit)
                                        <li class="pl-1 font-medium">{{ $benefit }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <!-- Other Instructions -->
                    @if($jobPosting->other_instructions)
                        <div class="space-y-2">
                            <h3 class="text-xs font-bold uppercase text-slate-400 tracking-wider">Application Instructions</h3>
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                                {{ $jobPosting->other_instructions }}
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
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 text-[10px] font-bold border border-emerald-200">
                                                    <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    <span>{{ ucfirst($app->recommendation) }}</span>
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
                                            <div class="h-12 w-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </div>
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
                                    class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Approve & Publish Listing</span>
                            </button>
                            <button onclick="rejectJob({{ $jobPosting->job_id }})" 
                                    class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Reject Job Posting</span>
                            </button>
                        @endif

                        @if(in_array($jobPosting->status, ['approved', 'pending']))
                            <button onclick="closeJob({{ $jobPosting->job_id }})" 
                                    class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors border border-slate-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>Close Job Listing</span>
                            </button>
                        @endif

                        <a href="{{ route('admin.job-postings.edit', $jobPosting->job_id) }}" 
                           class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            <span>Edit Listing Details</span>
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