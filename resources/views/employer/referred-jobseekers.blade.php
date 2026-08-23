@extends('layouts.employer')

@section('title', 'Referred Jobseekers - Employer Portal')

@section('content')
<div x-data="{ 
    interviewModal: false, 
    selectedAppId: null, 
    selectedName: '',
    openInterview(id, name) {
        this.selectedAppId = id;
        this.selectedName = name;
        this.interviewModal = true;
    }
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    JPO Verified Endorsements
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Referred Jobseekers</h1>
                <p class="text-sm text-slate-300">Review qualified candidates evaluated and referred by the DMDP Job Placement Officer (JPO). Schedule interviews or confirm hiring outcomes.</p>
            </div>

            <div class="shrink-0 bg-white/10 backdrop-blur rounded-2xl p-5 border border-white/10 text-center min-w-[150px]">
                <span class="text-xs font-bold text-emerald-300 uppercase tracking-wider">Referred Candidates</span>
                <p class="text-3xl font-black text-emerald-400 mt-0.5">{{ $referredApplicants->total() }}</p>
                <span class="text-[10px] text-slate-300">Verified by Placement Officer</span>
            </div>
        </div>

        <!-- Referred Applicants Grid -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-black text-slate-900">Candidates Awaiting Employer Action</h2>
                <span class="text-xs text-slate-500 font-semibold">{{ $referredApplicants->total() }} total endorsed</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($referredApplicants as $app)
                    @php
                        $jobseeker = $app->jobseeker;
                        $job = $app->jobPosting;
                    @endphp
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all flex flex-col justify-between gap-5">
                        
                        <div class="space-y-4">
                            <!-- Top Header -->
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="h-12 w-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white text-lg font-black shrink-0 shadow-sm">
                                        {{ strtoupper(substr($jobseeker->first_name ?? 'J', 0, 1)) }}
                                    </div>
                                    <div>
                                        <h3 class="font-black text-slate-900 text-base leading-tight">
                                            {{ $jobseeker->first_name }} {{ $jobseeker->last_name }}
                                        </h3>
                                        <p class="text-xs text-slate-400">{{ $jobseeker->email }} &bull; {{ $jobseeker->mobile_number ?? '09XX-XXX-XXXX' }}</p>
                                    </div>
                                </div>

                                <span class="rounded-full px-2.5 py-0.5 text-[11px] font-bold 
                                    {{ $app->status === 'hired' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($app->status === 'interview' ? 'bg-teal-100 text-teal-800 border border-teal-300' : 'bg-slate-100 text-slate-700') }}">
                                    {{ ucfirst($app->status) }}
                                </span>
                            </div>

                            <!-- Applied Position -->
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                                <span class="text-slate-500 font-semibold">Applied Role:</span>
                                <span class="font-bold text-slate-900">{{ $job->title ?? 'N/A' }}</span>
                            </div>

                            <!-- JPO Endorsement Remarks -->
                            <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200 text-xs space-y-1">
                                <span class="font-bold text-emerald-900 flex items-center gap-1.5">
                                    <svg class="h-3.5 w-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    JPO Referral Note:
                                </span>
                                <p class="text-emerald-800 text-[11px] leading-relaxed">
                                    {{ $app->jpo_notes ?: 'Evaluated and endorsed as a well-suited candidate by the Job Placement Officer.' }}
                                </p>
                            </div>

                            <!-- Skills Tag Pool -->
                            @if($jobseeker->skills->count() > 0)
                                <div class="space-y-1">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Verified Skills:</span>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($jobseeker->skills->take(4) as $skill)
                                            <span class="rounded-lg bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700">
                                                {{ $skill->skill_name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Attached Credentials & Resume -->
                            @php
                                $uploadedDocs = is_array($jobseeker->details?->training_certificates) 
                                    ? $jobseeker->details->training_certificates 
                                    : (json_decode($jobseeker->details?->training_certificates ?? '[]', true) ?: []);
                            @endphp
                            <div class="space-y-1.5 pt-1">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Attached Documents:</span>
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
                                                <a href="{{ $url }}" target="_blank" class="inline-flex items-center gap-1 rounded-xl bg-slate-100 hover:bg-emerald-50 hover:text-emerald-800 hover:border-emerald-300 border border-slate-200 px-2.5 py-1 text-[11px] font-bold text-slate-700 transition-colors" title="Click to preview {{ $doc['name'] ?? $label }}">
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
                                    <p class="text-[11px] text-slate-400 italic">No attached files</p>
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2">
                            
                            @if($app->status !== 'hired')
                                <button type="button" 
                                        @click="openInterview({{ $app->application_id }}, '{{ $jobseeker->first_name }} {{ $jobseeker->last_name }}')"
                                        class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold transition-colors">
                                    🗓️ Schedule Interview
                                </button>

                                <form action="{{ route('employer.applicants.update_status', $app->application_id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="hire">
                                    <button type="submit" onclick="return confirm('Confirm hiring this jobseeker?')"
                                            class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-md shadow-emerald-600/20">
                                        ✓ Mark as Hired
                                    </button>
                                </form>
                            @else
                                <span class="text-xs font-bold text-emerald-700 flex items-center gap-1">
                                    ✓ Hired on {{ $app->hired_date ? date('M d, Y', strtotime($app->hired_date)) : 'Today' }}
                                </span>
                            @endif

                        </div>

                    </div>
                @empty
                    <div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-400">
                        No jobseekers referred by the Placement Officer yet. As applicants apply, the JPO evaluates and endorses qualified candidates here.
                    </div>
                @endforelse
            </div>

            <div class="pt-4">
                {{ $referredApplicants->links() }}
            </div>
        </div>

    </div>

    <!-- Schedule Interview Modal -->
    <div x-show="interviewModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="interviewModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Candidate Interview</span>
                    <h3 class="text-xl font-black text-slate-900 mt-0.5">Schedule Interview with <span x-text="selectedName"></span></h3>
                </div>
                <button @click="interviewModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <form :action="'/employer/applicants/' + selectedAppId + '/status'" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="action" value="interview">

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Interview Date & Time *</label>
                    <input type="datetime-local" name="interview_schedule" required
                           class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Interview Format *</label>
                    <select name="interview_mode" required
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none">
                        <option value="online">Online (Video Meeting / Google Meet / Zoom)</option>
                        <option value="onsite">On-site (Company Office in Cebu)</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Meeting Link or Office Location *</label>
                    <input type="text" name="interview_location" required placeholder="e.g. https://meet.google.com/xyz-abc or 5th Flr IT Tower, Cebu IT Park"
                           class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="interviewModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-lg shadow-emerald-600/30">
                        Send Interview Invitation &rarr;
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection
