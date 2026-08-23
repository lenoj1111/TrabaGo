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
    <div class="mx-auto max-w-7xl space-y-8">
        
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
                                <div class="h-11 w-11 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white text-base font-black shrink-0 shadow-sm">
                                    {{ strtoupper(substr($jobseeker->first_name ?? 'J', 0, 1)) }}
                                </div>
                                <div>
                                    <h3 class="font-black text-slate-900 text-sm leading-tight">
                                        {{ $jobseeker->first_name }} {{ $jobseeker->last_name }}
                                    </h3>
                                    <p class="text-[11px] text-slate-400">{{ $jobseeker->email }}</p>
                                </div>
                            </div>

                            <span class="rounded-full px-2.5 py-0.5 text-[10px] font-extrabold uppercase {{ $isReferred ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                                {{ $isReferred ? '✓ Referred' : 'Pending JPO' }}
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
                                            $icon = match($cat) {
                                                'resume' => '📄',
                                                'valid_id' => '🪪',
                                                'certificate', 'certs' => '🎖️',
                                                'pwd_id' => '♿',
                                                default => '📎'
                                            };
                                            $label = ucfirst(str_replace('_', ' ', $cat));
                                            $url = $doc['file_url'] ?? null;
                                        @endphp
                                        @if($url)
                                            <a href="{{ $url }}" target="_blank" class="inline-flex items-center gap-1 rounded-xl bg-slate-100 hover:bg-emerald-50 hover:text-emerald-800 hover:border-emerald-300 border border-slate-200/80 px-2.5 py-1 text-[11px] font-bold text-slate-700 transition-colors shadow-2xs" title="Click to view/download {{ $doc['name'] ?? $label }}">
                                                <span>{{ $icon }}</span>
                                                <span>{{ $label }}</span>
                                                <span class="text-[9px] text-slate-400">↗</span>
                                            </a>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-xl bg-slate-100 border border-slate-200 px-2 py-0.5 text-[10px] font-semibold text-slate-600">
                                                <span>{{ $icon }}</span> {{ $label }}
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
                                <span class="rounded-md bg-purple-50 text-purple-700 px-2 py-0.5 font-bold border border-purple-200">
                                    ♿ {{ $jobseeker->socialStatus->pwd_type ?: 'PWD' }}
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
                        @if(!$isReferred)
                            <button type="button" 
                                    @click="openRefer({{ $app->application_id }}, '{{ $jobseeker->first_name }} {{ $jobseeker->last_name }}', '{{ $job->title }}')"
                                    class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-md shadow-emerald-600/30 transition-all">
                                Evaluate & Refer to Employer &rarr;
                            </button>
                        @else
                            <span class="text-xs font-bold text-emerald-700 flex items-center gap-1">
                                ✓ Endorsed to Employer on {{ $app->jpo_evaluated_at ? date('M d, Y', strtotime($app->jpo_evaluated_at)) : 'Today' }}
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

                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs space-y-1">
                    <p class="font-bold text-emerald-950">Candidate: <span x-text="selectedName" class="text-slate-900 font-extrabold"></span></p>
                    <p class="text-emerald-800">Target Role: <span x-text="selectedJob" class="font-semibold"></span></p>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Evaluation Decision *</label>
                    <select name="recommendation" required
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none">
                        <option value="refer">✓ Officially Refer & Endorse to Employer</option>
                        <option value="training">📚 Recommend Upskilling / Training Course</option>
                        <option value="reject">✕ Mark as Not Qualified for this Role</option>
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
