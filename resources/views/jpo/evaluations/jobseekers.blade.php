@extends('layouts.jpo')

@section('title', 'Evaluate Jobseekers - JPO Portal')

@section('content')
<div x-data="{
    referModal: false,
    selectedAppId: null,
    selectedName: '',
    selectedJob: '',
    openRefer(id, name, job) {
        this.selectedAppId = id;
        this.selectedName = name;
        this.selectedJob = job;
        this.referModal = true;
    }
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="w-full max-w-[1680px] mx-auto space-y-8">
        
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
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'pending' ? 'bg-emerald-600 text-white' : 'bg-white/10 text-slate-300 hover:bg-white/20' }}">
                    Pending JPO
                </a>
                <a href="{{ route('jpo.evaluations.jobseekers', ['status' => 'referred']) }}" 
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'referred' ? 'bg-emerald-600 text-white' : 'bg-white/10 text-slate-300 hover:bg-white/20' }}">
                    Referred to Employer
                </a>
            </div>
        </div>

        <!-- Applicant Evaluation Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($applications as $app)
                @php
                    $jobseeker = $app->jobseeker;
                    $job = $app->jobPosting;
                    $employer = $job->employer ?? null;
                    $isReferred = $app->referred_by_jpo;
                @endphp
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all flex flex-col justify-between gap-5">
                    
                    <div class="space-y-4">
                        <!-- Top Header -->
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-3">
                                <div class="h-11 w-11 rounded-2xl bg-gradient-to-tr from-emerald-600 to-emerald-400 flex items-center justify-center text-white text-base font-black shrink-0 shadow-sm">
                                    {{ strtoupper(substr($jobseeker->first_name ?? 'J', 0, 1)) }}
                                </div>
                                <div>
                                    <h3 class="font-black text-slate-900 text-sm leading-tight">
                                        {{ $jobseeker->first_name }} {{ $jobseeker->last_name }}
                                    </h3>
                                    <p class="text-[11px] text-slate-400">{{ $jobseeker->email }}</p>
                                     <div class="flex items-center gap-1.5 mt-1">
                                        @if($jobseeker->isVerified())
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                <span>Verified Candidate</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full" title="Incomplete Profile or Documents">
                                                <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                <span>Incomplete Profile/Docs</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[10px] font-extrabold uppercase {{ $isReferred ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                                @if($isReferred)
                                    <svg class="w-3 h-3 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Referred</span>
                                @else
                                    <span>Pending JPO</span>
                                @endif
                            </span>
                        </div>

                        <!-- Target Vacancy -->
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 space-y-0.5 text-xs">
                            <p class="text-slate-500 font-semibold">Target Opening:</p>
                            <p class="font-bold text-slate-900">{{ $job->title ?? 'N/A' }}</p>
                            <p class="text-[11px] text-emerald-700 font-semibold">{{ $employer->company_name ?? 'Company' }}</p>
                        </div>

                        <!-- Verified Skills Matrix -->
                        @if($jobseeker->skills->count() > 0)
                            <div class="space-y-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Verified Skills:</span>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($jobseeker->skills->take(4) as $s)
                                        <span class="rounded-lg bg-emerald-50 border border-emerald-200 px-2 py-0.5 text-[10px] font-bold text-emerald-800">
                                            {{ $s->skill_name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Attached Credentials & Documents from Document Hub -->
                        @php
                            $uploadedDocs = is_array($jobseeker->details?->training_certificates) 
                                ? $jobseeker->details->training_certificates 
                                : (json_decode($jobseeker->details?->training_certificates ?? '[]', true) ?: []);
                        @endphp
                        <div class="space-y-1.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Applicant Documents:</span>
                            @if(count($uploadedDocs) > 0)
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($uploadedDocs as $doc)
                                        @php
                                            $cat = $doc['category'] ?? 'document';
                                            $label = ucfirst(str_replace('_', ' ', $cat));
                                            $url = $doc['file_url'] ?? null;
                                            $dStatus = $doc['status'] ?? 'under_review';
                                            $statusDot = match($dStatus) {
                                                'approved', 'verified' => 'bg-emerald-500',
                                                'rejected' => 'bg-rose-500',
                                                default => 'bg-amber-500'
                                            };
                                        @endphp
                                        @if($url)
                                            <a href="{{ $url }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-100 hover:bg-emerald-50 hover:text-emerald-800 hover:border-emerald-300 border border-slate-200/80 px-2.5 py-1 text-[11px] font-bold text-slate-700 transition-colors shadow-2xs" title="Click to view/download {{ $doc['name'] ?? $label }} ({{ ucfirst(str_replace('_', ' ', $dStatus)) }})">
                                                <span class="h-1.5 w-1.5 rounded-full {{ $statusDot }}"></span>
                                                @if($cat === 'resume')
                                                    <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                @elseif($cat === 'valid_id')
                                                    <svg class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                                @elseif(in_array($cat, ['certificate', 'certs']))
                                                    <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                                @elseif($cat === 'pwd_id')
                                                    <svg class="w-3.5 h-3.5 text-purple-600 shrink-0" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="4" r="2"/><path d="M15.5 13.5A4.5 4.5 0 0 1 11 18a4.5 4.5 0 0 1-4.5-4.5A4.5 4.5 0 0 1 11 9c.4 0 .78.06 1.14.16L13.5 6.5C12.7 6.18 11.87 6 11 6a7.5 7.5 0 0 0-7.5 7.5A7.5 7.5 0 0 0 11 21a7.5 7.5 0 0 0 7.42-6.5h-2.92z"/><path d="M19 10h-4.5l-1.3-2.6A2 2 0 0 0 11.4 6.2L8.5 7.6 9.4 9.4l2.1-1 2 4.1V17h2v-4.5l1.6-1.6H19v-2z"/></svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                @endif
                                                <span>{{ $label }}</span>
                                                <svg class="w-2.5 h-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-xl bg-slate-100 border border-slate-200 px-2 py-0.5 text-[10px] font-semibold text-slate-600">
                                                <span class="h-1.5 w-1.5 rounded-full {{ $statusDot }}"></span>
                                                <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                <span>{{ $label }}</span>
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <p class="text-[11px] text-slate-400 italic">No documents uploaded to vault yet.</p>
                            @endif
                        </div>

                        <!-- Social Status Indicators -->
                        <div class="flex flex-wrap gap-1 text-[10px]">
                            @if($jobseeker->socialStatus && $jobseeker->socialStatus->is_pwd)
                                <span class="inline-flex items-center gap-1 rounded-md bg-purple-50 text-purple-700 px-2 py-0.5 font-bold border border-purple-200">
                                    <svg class="w-3 h-3 text-purple-700 shrink-0" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="4" r="2"/><path d="M15.5 13.5A4.5 4.5 0 0 1 11 18a4.5 4.5 0 0 1-4.5-4.5A4.5 4.5 0 0 1 11 9c.4 0 .78.06 1.14.16L13.5 6.5C12.7 6.18 11.87 6 11 6a7.5 7.5 0 0 0-7.5 7.5A7.5 7.5 0 0 0 11 21a7.5 7.5 0 0 0 7.42-6.5h-2.92z"/><path d="M19 10h-4.5l-1.3-2.6A2 2 0 0 0 11.4 6.2L8.5 7.6 9.4 9.4l2.1-1 2 4.1V17h2v-4.5l1.6-1.6H19v-2z"/></svg>
                                    <span>{{ $jobseeker->socialStatus->pwd_type ?: 'PWD' }}</span>
                                </span>
                            @endif
                            @if($jobseeker->socialStatus && $jobseeker->socialStatus->is_4ps)
                                <span class="rounded-md bg-amber-50 text-amber-800 px-2 py-0.5 font-bold border border-amber-200">
                                    4Ps Beneficiary
                                </span>
                            @endif
                        </div>

                        <!-- Previous Notes if any -->
                        @if($app->jpo_notes)
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-600">
                                <span class="font-bold text-slate-800">JPO Note:</span> {{ $app->jpo_notes }}
                            </div>
                        @endif
                    </div>

                    <!-- Evaluation Action -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                        <a href="{{ route('jpo.evaluations.jobseekers.nsrp', $app->application_id) }}" 
                           target="_blank"
                           class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition-colors flex items-center gap-1.5"
                           title="Inspect official DOLE NSRP Form 1.REV 3 for this candidate">
                            <svg class="w-3.5 h-3.5 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>NSRP Form 1</span>
                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>

                        @if(!$isReferred)
                            <button type="button" 
                                    @click="openRefer({{ $app->application_id }}, '{{ $jobseeker->first_name }} {{ $jobseeker->last_name }}', '{{ $job->title }}')"
                                    class="flex-1 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-md shadow-emerald-600/30 transition-all text-center">
                                Evaluate & Refer &rarr;
                            </button>
                        @else
                            <span class="text-xs font-bold text-emerald-700 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Endorsed</span>
                            </span>
                        @endif
                    </div>

                </div>
            @empty
                <div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-400">
                    No jobseeker applications found matching the filter.
                </div>
            @endforelse
        </div>

        <div class="pt-4">
            {{ $applications->links() }}
        </div>

    </div>

    <!-- Referral & Evaluation Modal -->
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
                            <svg class="w-3.5 h-3.5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Inspect NSRP Form 1</span>
                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
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
