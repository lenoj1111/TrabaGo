@extends('layouts.jpo')

@section('title', 'Evaluate Jobseekers - JPO Portal')

@section('content')
<div x-data="{
    referModal: false,
    selectedAppId: null,
    selectedName: '',
    selectedJob: '',
    
    detailModal: false,
    selectedApp: null,
    
    openRefer(id, name, job) {
        this.selectedAppId = id;
        this.selectedName = name;
        this.selectedJob = job;
        this.referModal = true;
    },
    
    openDetail(app) {
        this.selectedApp = app;
        this.detailModal = true;
    }
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="w-full max-w-[1680px] mx-auto space-y-6">

        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Candidate Qualification & Referral
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Evaluate Jobseekers</h1>
                <p class="text-sm text-slate-300">Review applicant qualifications, verify skills match against job criteria, and officially endorse qualified candidates to the Employer.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('jpo.evaluations.jobseekers', ['status' => 'pending']) }}"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'pending' ? 'bg-emerald-600 text-white shadow-md' : 'bg-white/10 text-slate-300 hover:bg-white/20' }}">
                    Pending JPO
                </a>
                <a href="{{ route('jpo.evaluations.jobseekers', ['status' => 'referred']) }}"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'referred' ? 'bg-emerald-600 text-white shadow-md' : 'bg-white/10 text-slate-300 hover:bg-white/20' }}">
                    Referred to Employer
                </a>
            </div>
        </div>

        <!-- Applicants Table -->
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-black uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="py-4 px-6">Candidate</th>
                            <th class="py-4 px-6">Target Role</th>
                            <th class="py-4 px-6">Skills & Docs</th>
                            <th class="py-4 px-6 text-center">Status</th>
                            <th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($applications as $app)
                        @php
                        $jobseeker = $app->jobseeker;
                        $job = $app->jobPosting;
                        $employer = $job->employer ?? null;
                        $isReferred = $app->referred_by_jpo;

                        // Prepare data for the detail modal
                        $uploadedDocs = is_array($jobseeker->details?->training_certificates)
                        ? $jobseeker->details->training_certificates
                        : (json_decode($jobseeker->details?->training_certificates ?? '[]', true) ?: []);
                        $docCount = count($uploadedDocs);

                        // Prepare documents with URLs for Alpine
                        $modalDocs = [];
                        foreach ($uploadedDocs as $idx => $doc) {
                        if (is_array($doc)) {
                        $modalDocs[] = [
                        'name' => $doc['name'] ?? 'Document',
                        'category' => ucfirst(str_replace('_', ' ', $doc['category'] ?? 'File')),
                        'url' => route('jpo.evaluations.jobseekers.documents.show', [$app->application_id, $idx])
                        ];
                        }
                        }

                        $appData = [
                        'id' => $app->application_id,
                        'name' => $jobseeker->first_name . ' ' . $jobseeker->last_name,
                        'email' => $jobseeker->email,
                        'phone' => $jobseeker->mobile_number ?? 'N/A',
                        'job_title' => $job->title,
                        'company' => $employer->company_name ?? 'Company',
                        'skills' => $jobseeker->skills->pluck('skill_name')->toArray(),
                        'docs' => $modalDocs,
                        'is_verified' => $jobseeker->isVerified(),
                        'nsrp_url' => route('jpo.evaluations.jobseekers.nsrp', $app->application_id)
                        ];
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <!-- Candidate Info -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-emerald-400 flex items-center justify-center text-white text-sm font-black shrink-0 shadow-sm">
                                        {{ strtoupper(substr($jobseeker->first_name ?? 'J', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 text-sm">{{ $jobseeker->first_name }} {{ $jobseeker->last_name }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $jobseeker->email }}</p>
                                        <div class="flex items-center gap-1 mt-0.5">
                                            @if($jobseeker->isVerified())
                                            <span class="inline-flex items-center gap-1 text-[9px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded-md">
                                                <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                </svg>
                                                Verified
                                            </span>
                                            @else
                                            <span class="inline-flex items-center gap-1 text-[9px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded-md">
                                                Incomplete Docs
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Target Role -->
                            <td class="py-4 px-6">
                                <p class="font-bold text-slate-900 text-xs">{{ $job->title ?? 'N/A' }}</p>
                                <p class="text-[11px] text-emerald-700 font-semibold mt-0.5">{{ $employer->company_name ?? 'Company' }}</p>
                            </td>

                            <!-- Skills & Docs -->
                            <td class="py-4 px-6">
                                <div class="space-y-2">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($jobseeker->skills->take(2) as $s)
                                        <span class="rounded-md bg-slate-100 border border-slate-200 px-1.5 py-0.5 text-[10px] font-semibold text-slate-700">
                                            {{ $s->skill_name }}
                                        </span>
                                        @empty
                                        <span class="text-[10px] text-slate-400 italic">No skills</span>
                                        @endforelse
                                        @if($jobseeker->skills->count() > 2)
                                        <span class="text-[10px] text-slate-500 font-medium self-center">+{{ $jobseeker->skills->count() - 2 }}</span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] text-slate-500 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        {{ $docCount }} Attached Documents
                                    </div>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-6 text-center">
                                @if($app->status === 'training_recommended')
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[10px] font-extrabold uppercase bg-blue-100 text-blue-800 border border-blue-300">
                                    Training Recommended
                                </span>
                                @elseif($app->status === 'rejected')
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[10px] font-extrabold uppercase bg-rose-100 text-rose-800 border border-rose-300">
                                    Rejected / Closed
                                </span>
                                @elseif($isReferred || $app->status === 'pending')
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    <svg class="w-3 h-3 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Referred
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800 border border-amber-300">
                                    Pending JPO
                                </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-6 text-right">
                                <div class="ml-auto flex w-fit flex-col items-stretch gap-1.5 rounded-xl border border-slate-200 bg-white p-2">
                                    <!-- View Button (Triggers Modal) -->
                                    <button type="button" @click="openDetail({{ json_encode($appData) }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition-colors">
                                        <svg class="w-3.5 h-3.5 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>View</span>
                                    </button>

                                    <!-- Evaluate Button -->
                                    @if($app->status === 'jpo_pending')
                                    <button type="button"
                                        @click="openRefer({{ $app->application_id }}, '{{ $jobseeker->first_name }} {{ $jobseeker->last_name }}', '{{ $job->title }}')"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-sm transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Evaluate</span>
                                    </button>
                                    @elseif($app->status === 'training_recommended')
                                    <span class="text-xs font-bold text-blue-600 flex items-center justify-end gap-1 px-2">
                                        Training Assigned
                                    </span>
                                    @elseif($app->status === 'rejected')
                                    <span class="text-xs font-bold text-rose-600 flex items-center justify-end gap-1 px-2">
                                        Closed
                                    </span>
                                    @else
                                    <span class="text-xs font-bold text-emerald-700 flex items-center justify-end gap-1 px-2">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Endorsed</span>
                                    </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 text-sm italic">
                                No jobseeker applications found matching the filter.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $applications->links() }}
            </div>
        </div>

    </div>

    <!-- ========================================== -->
    <!-- 1. VIEW DETAILS MODAL -->
    <!-- ========================================== -->
    <div x-show="detailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="detailModal = false" class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6 max-h-[90vh] overflow-y-auto">

            <template x-if="selectedApp">
                <div class="space-y-6">
                    <!-- Header -->
                    <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                        <div>
                            <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider" x-text="selectedApp.company"></span>
                            <h3 class="text-2xl font-black text-slate-900 mt-1" x-text="selectedApp.name"></h3>
                            <p class="text-xs text-slate-500 mt-0.5">Target Role: <span class="font-bold text-slate-800" x-text="selectedApp.job_title"></span></p>
                        </div>
                        <button @click="detailModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
                    </div>

                    <!-- Basic Info -->
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Email</span>
                            <span class="font-semibold text-slate-800" x-text="selectedApp.email"></span>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Mobile</span>
                            <span class="font-semibold text-slate-800" x-text="selectedApp.phone"></span>
                        </div>
                    </div>

                    <!-- Skills -->
                    <div class="space-y-2">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-900">Verified Skills</h4>
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="skill in selectedApp.skills" :key="skill">
                                <span class="rounded-lg bg-emerald-50 border border-emerald-200 px-2.5 py-1 text-xs font-bold text-emerald-800" x-text="skill"></span>
                            </template>
                            <template x-if="selectedApp.skills.length === 0">
                                <span class="text-xs text-slate-400 italic">No skills listed.</span>
                            </template>
                        </div>
                    </div>

                    <!-- Documents -->
                    <div class="space-y-2">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-900">Uploaded Documents</h4>
                        <div class="space-y-2">
                            <template x-for="(doc, idx) in selectedApp.docs" :key="idx">
                                <a :href="doc.url" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50 transition-colors group">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <div>
                                            <p class="text-xs font-bold text-slate-800" x-text="doc.name"></p>
                                            <p class="text-[10px] text-slate-500" x-text="doc.category"></p>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-bold text-emerald-700 bg-white border border-emerald-200 px-2 py-1 rounded-md group-hover:bg-emerald-600 group-hover:text-white transition-colors">Open File &rarr;</span>
                                </a>
                            </template>
                            <template x-if="selectedApp.docs.length === 0">
                                <p class="text-xs text-slate-400 italic">No documents uploaded.</p>
                            </template>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                        <a :href="selectedApp.nsrp_url" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-colors">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>View NSRP Form 1 (Official)</span>
                        </a>
                        <button @click="detailModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                            Close
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 2. REFERRAL & EVALUATION MODAL -->
    <!-- ========================================== -->
    <div x-show="referModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="referModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">

            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">JPO Placement Endorsement</span>
                    <h3 class="text-xl font-black text-slate-900 mt-0.5">Evaluate Candidate</h3>
                </div>
                <button @click="referModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <form :action="'/jpo/evaluations/jobseekers/' + selectedAppId + '/refer'" method="POST" class="space-y-4">
                @csrf

                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs space-y-1.5">
                    <p class="font-bold text-emerald-950">Candidate: <span x-text="selectedName" class="text-slate-900 font-extrabold"></span></p>
                    <div class="flex items-center justify-between">
                        <p class="text-emerald-800">Target Role: <span x-text="selectedJob" class="font-semibold"></span></p>
                        <a :href="'/jpo/evaluations/jobseekers/' + selectedAppId + '/nsrp'" target="_blank" class="text-xs font-bold text-emerald-700 hover:underline flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Inspect NSRP Form 1</span>
                        </a>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Evaluation Decision *</label>
                    <select name="recommendation" required
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none">
                        <option value="refer">Officially Refer & Endorse to Employer</option>
                        <option value="training">Recommend Upskilling / Training Course</option>
                        <option value="reject">Mark as Not Qualified for this Role</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">JPO Endorsement Remarks</label>
                    <textarea name="remarks" rows="3" placeholder="State candidate qualifications, strengths, and endorsement justification..."
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="referModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-lg shadow-emerald-600/30">
                        Confirm Endorsement & Send to Employer &rarr;
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection