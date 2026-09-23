@extends('layouts.employer')

@section('title', 'Referred Candidates & Applicants - Employer Portal')

@section('content')
<div x-data="{ 
    interviewModal: false, 
    notQualifiedModal: false,
    resignationModal: false,
    offerModal: false,
    resignationAction: 'approve',
    selectedAppId: null, 
    selectedName: '',
    offerSalary: '',
    offerStartDate: '',
    offerNotes: '',
    openInterview(id, name) {
        this.selectedAppId = id;
        this.selectedName = name;
        this.interviewModal = true;
    },
    openNotQualified(id, name) {
        this.selectedAppId = id;
        this.selectedName = name;
        this.notQualifiedModal = true;
    },
    openResignation(id, name, action) {
        this.selectedAppId = id;
        this.selectedName = name;
        this.resignationAction = action;
        this.resignationModal = true;
    },
    openOffer(id, name, salary = '', startDate = '', notes = '') {
        this.selectedAppId = id;
        this.selectedName = name;
        this.offerSalary = salary;
        this.offerStartDate = startDate;
        this.offerNotes = notes;
        this.offerModal = true;
    }
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-green-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-green-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-400/20 px-3 py-1 text-xs font-bold text-green-300 border border-green-400/30">
                    <span class="h-2 w-2 rounded-full bg-green-400 animate-pulse"></span>
                    DMDP Talent Evaluation Hub
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Candidate Evaluations</h1>
                <p class="text-sm text-slate-300">Review certified jobseekers endorsed by DMDP Placement Officers or direct applicants. Schedule interviews, mark candidates as Not Qualified, or confirm hiring.</p>
            </div>

            <div class="shrink-0 bg-white/10 backdrop-blur rounded-2xl p-5 border border-white/10 text-center min-w-[160px]">
                <span class="text-xs font-bold text-green-300 uppercase tracking-wider">Total Candidates</span>
                <p class="text-3xl font-black text-green-400 mt-0.5">{{ $stats['total'] ?? $referredApplicants->total() }}</p>
                <span class="text-[10px] text-slate-300">{{ $stats['hired'] ?? 0 }} Hired &bull; {{ $stats['not_qualified'] ?? 0 }} Not Qualified</span>
            </div>
        </div>

        <!-- Flash Messages -->
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

        <!-- Metric Summary Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-6 gap-3">
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
                <span class="text-[11px] font-bold text-slate-400 uppercase">Total Roster</span>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-amber-200 bg-amber-50/20 shadow-sm">
                <span class="text-[11px] font-bold text-amber-700 uppercase">Awaiting Action</span>
                <p class="text-2xl font-black text-amber-700 mt-1">{{ $stats['pending'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-green-200 bg-green-50/20 shadow-sm">
                <span class="text-[11px] font-bold text-green-700 uppercase">Interviewing</span>
                <p class="text-2xl font-black text-green-700 mt-1">{{ $stats['interview'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-blue-200 bg-blue-50/20 shadow-sm">
                <span class="text-[11px] font-bold text-blue-700 uppercase">Offers Extended</span>
                <p class="text-2xl font-black text-blue-700 mt-1">{{ $stats['offered'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-green-200 bg-green-50/20 shadow-sm">
                <span class="text-[11px] font-bold text-green-700 uppercase">Hired Candidates</span>
                <p class="text-2xl font-black text-green-700 mt-1">{{ $stats['hired'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-rose-200 bg-rose-50/20 shadow-sm col-span-2 sm:col-span-1">
                <span class="text-[11px] font-bold text-rose-700 uppercase">Not Qualified</span>
                <p class="text-2xl font-black text-rose-700 mt-1">{{ $stats['not_qualified'] ?? 0 }}</p>
            </div>
        </div>

        <!-- Filter Tabs & Controls -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <!-- Filter Pills -->
            <div class="flex flex-wrap items-center gap-1.5 bg-white p-1.5 rounded-2xl border border-slate-200 shadow-sm text-xs font-bold">
                <a href="{{ route('employer.referred-jobseekers', array_merge(request()->except('page', 'status'), ['status' => 'all'])) }}"
                   class="px-3 py-1.5 rounded-xl transition-colors {{ (!request()->has('status') || request()->status === 'all') ? 'bg-green-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    All Candidates
                </a>
                <a href="{{ route('employer.referred-jobseekers', array_merge(request()->except('page'), ['status' => 'pending'])) }}"
                   class="px-3 py-1.5 rounded-xl transition-colors {{ request()->status === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    Pending ({{ $stats['pending'] ?? 0 }})
                </a>
                <a href="{{ route('employer.referred-jobseekers', array_merge(request()->except('page'), ['status' => 'interview'])) }}"
                   class="px-3 py-1.5 rounded-xl transition-colors {{ request()->status === 'interview' ? 'bg-green-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    Interview ({{ $stats['interview'] ?? 0 }})
                </a>
                <a href="{{ route('employer.referred-jobseekers', array_merge(request()->except('page'), ['status' => 'offered'])) }}"
                   class="px-3 py-1.5 rounded-xl transition-colors {{ request()->status === 'offered' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    🎁 Offered ({{ $stats['offered'] ?? 0 }})
                </a>
                <a href="{{ route('employer.referred-jobseekers', array_merge(request()->except('page'), ['status' => 'hired'])) }}"
                   class="px-3 py-1.5 rounded-xl transition-colors {{ request()->status === 'hired' ? 'bg-green-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    Hired ({{ $stats['hired'] ?? 0 }})
                </a>
                <a href="{{ route('employer.referred-jobseekers', array_merge(request()->except('page'), ['status' => 'not_qualified'])) }}"
                   class="px-3 py-1.5 rounded-xl transition-colors {{ request()->status === 'not_qualified' ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    Not Qualified ({{ $stats['not_qualified'] ?? 0 }})
                </a>
                @if(($stats['resignation_requested'] ?? 0) > 0)
                    <a href="{{ route('employer.referred-jobseekers', array_merge(request()->except('page'), ['status' => 'resignation_requested'])) }}"
                       class="px-3 py-1.5 rounded-xl transition-colors {{ request()->status === 'resignation_requested' ? 'bg-amber-600 text-white shadow-sm' : 'text-amber-800 bg-amber-50 border border-amber-300 hover:bg-amber-100 animate-pulse' }}">
                        ⚠️ Resignations ({{ $stats['resignation_requested'] }})
                    </a>
                @endif
            </div>

            <!-- Filter By Vacancy Dropdown & Search -->
            <form method="GET" action="{{ route('employer.referred-jobseekers') }}" class="flex flex-wrap items-center gap-2 max-w-lg w-full">
                @if(request()->filled('status'))
                    <input type="hidden" name="status" value="{{ request()->status }}">
                @endif
                
                @if(isset($employerJobs) && count($employerJobs) > 0)
                    <select name="job_id" onchange="this.form.submit()" class="rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-800 bg-white focus:border-green-500 focus:outline-none">
                        <option value="">All Job Vacancies</option>
                        @foreach($employerJobs as $ej)
                            <option value="{{ $ej->job_id }}" {{ request('job_id') == $ej->job_id ? 'selected' : '' }}>
                                {{ $ej->title }}
                            </option>
                        @endforeach
                    </select>
                @endif

                <div class="relative flex-1 min-w-[160px]">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search candidate name..."
                           class="w-full rounded-xl border border-slate-200 pl-8 pr-3 py-2 text-xs text-slate-900 focus:border-green-500 focus:outline-none">
                    <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-green-600 text-white text-xs font-bold transition-colors">
                    Filter
                </button>
            </form>
        </div>

        <!-- Referred Applicants Grid -->
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($referredApplicants as $app)
                    @php
                        $jobseeker = $app->jobseeker;
                        $job = $app->jobPosting;
                        $fullName = trim(($jobseeker->first_name ?? '') . ' ' . ($jobseeker->last_name ?? ''));
                    @endphp
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:border-green-300 transition-all flex flex-col justify-between gap-5">
                        
                        <div class="space-y-4">
                            <!-- Top Header -->
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="h-12 w-12 rounded-2xl bg-gradient-to-tr from-green-600 to-green-400 flex items-center justify-center text-white text-lg font-black shrink-0 shadow-sm">
                                        {{ strtoupper(substr($jobseeker->first_name ?? 'C', 0, 1)) }}
                                    </div>
                                    <div>
                                        <h3 class="font-black text-slate-900 text-base leading-tight">
                                            {{ $fullName ?: 'Candidate #' . $app->application_id }}
                                        </h3>
                                        <p class="text-xs text-slate-400">{{ $jobseeker->email ?? 'N/A' }} &bull; {{ $jobseeker->mobile_number ?? '09XX-XXX-XXXX' }}</p>
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                <div>
                                    @if($app->status === 'hired')
                                        @if($app->resignation_status === 'requested')
                                            <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-extrabold bg-amber-100 text-amber-900 border border-amber-300 shadow-sm animate-pulse">
                                                ⚠️ Resignation Requested
                                            </span>
                                        @elseif($app->resignation_status === 'approved')
                                            <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-extrabold bg-slate-100 text-slate-700 border border-slate-300 shadow-sm">
                                                💼 Resigned (Unemployed)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-extrabold bg-green-100 text-green-800 border border-green-300 shadow-sm">
                                                ✓ Hired
                                            </span>
                                        @endif
                                    @elseif($app->status === 'offered')
                                        <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-extrabold bg-blue-100 text-blue-800 border border-blue-300 shadow-sm animate-pulse">
                                            🎁 Offer Extended
                                        </span>
                                    @elseif($app->status === 'declined')
                                        <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-extrabold bg-slate-100 text-slate-700 border border-slate-300 shadow-sm">
                                            ✕ Offer Declined
                                        </span>
                                    @elseif($app->status === 'rejected')
                                        <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-extrabold bg-rose-100 text-rose-800 border border-rose-300 shadow-sm">
                                            ✕ Not Qualified
                                        </span>
                                    @elseif($app->status === 'interview')
                                        <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-extrabold bg-green-100 text-green-800 border border-green-300 shadow-sm">
                                            🗓️ Interview Scheduled
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                                            ⏳ Pending Review
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Applied Position -->
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                                <span class="text-slate-500 font-semibold">Opening Applied:</span>
                                <span class="font-bold text-slate-900">{{ $job->title ?? 'General Vacancy' }}</span>
                            </div>

                            <!-- Offer Extended Info Box -->
                            @if($app->status === 'offered')
                                <div class="p-4 rounded-2xl bg-blue-50/90 border border-blue-200 text-xs space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="font-extrabold text-blue-900 flex items-center gap-1.5">
                                            <span>🎁</span> Formal Job Offer Extended
                                        </span>
                                        <span class="text-[10px] text-blue-700 font-semibold">
                                            {{ $app->offered_at ? \Carbon\Carbon::parse($app->offered_at)->diffForHumans() : 'Recently' }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-[11px] bg-white/80 p-2.5 rounded-xl border border-blue-100">
                                        <div>
                                            <span class="text-slate-500 font-semibold">Proposed Salary:</span>
                                            <p class="font-bold text-slate-900">{{ $app->offer_salary ? '₱' . number_format($app->offer_salary, 2) . ' / mo' : 'Negotiable' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-slate-500 font-semibold">Target Start Date:</span>
                                            <p class="font-bold text-slate-900">{{ $app->offer_start_date ? date('M d, Y', strtotime($app->offer_start_date)) : 'To be arranged' }}</p>
                                        </div>
                                    </div>
                                    @if($app->offer_notes)
                                        <p class="text-[11px] text-blue-900 italic bg-white/60 p-2 rounded-lg border border-blue-100">
                                            "{{ $app->offer_notes }}"
                                        </p>
                                    @endif
                                    <p class="text-[10px] text-blue-700">
                                        ⏳ Waiting for candidate to accept or decline in their portal.
                                    </p>
                                </div>
                            @elseif($app->status === 'declined')
                                <div class="p-3.5 rounded-2xl bg-slate-100 border border-slate-300 text-xs space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-slate-800 flex items-center gap-1">
                                            <span>✕</span> Candidate Declined Offer
                                        </span>
                                        <span class="text-[10px] text-slate-500">
                                            {{ $app->declined_at ? \Carbon\Carbon::parse($app->declined_at)->diffForHumans() : '' }}
                                        </span>
                                    </div>
                                    @if($app->decline_reason)
                                        <p class="text-slate-600 text-[11px]">
                                            <span class="font-semibold">Reason:</span> {{ $app->decline_reason }}
                                        </p>
                                    @endif
                                </div>
                            @endif

                            <!-- Resignation Request Alert -->
                            @if($app->resignation_status === 'requested')
                                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-300 text-xs space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="font-black text-amber-950 flex items-center gap-1.5">
                                            <span>⚠️</span> Resignation Request from Employee
                                        </span>
                                        <span class="text-[10px] text-amber-800 font-semibold">
                                            {{ $app->resignation_requested_at ? \Carbon\Carbon::parse($app->resignation_requested_at)->diffForHumans() : 'Recent' }}
                                        </span>
                                    </div>
                                    <div class="bg-white/95 p-3 rounded-xl border border-amber-200 shadow-xs">
                                        <p class="text-slate-800 text-[11px] leading-relaxed">
                                            <span class="font-bold text-amber-900">Reason:</span> "{{ $app->resignation_reason }}"
                                        </p>
                                    </div>
                                    <p class="text-[10px] text-amber-800 font-medium">
                                        Approving this resignation will update their status back to <strong>Unemployed</strong> and allow them to apply to other jobs.
                                    </p>
                                </div>
                            @elseif($app->resignation_status === 'approved')
                                <div class="p-3 rounded-xl bg-slate-100 border border-slate-200 text-xs text-slate-600 flex items-center justify-between">
                                    <span class="font-semibold text-slate-700">Resignation approved on {{ $app->resignation_approved_at ? \Carbon\Carbon::parse($app->resignation_approved_at)->format('M d, Y') : 'N/A' }}</span>
                                    <span class="text-[11px] text-green-700 font-bold">Profile: Unemployed</span>
                                </div>
                            @elseif($app->resignation_status === 'rejected')
                                <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800">
                                    <span class="font-bold">Resignation request declined.</span>
                                    @if($app->resignation_remarks)
                                        <span class="text-[11px] italic">Remarks: {{ $app->resignation_remarks }}</span>
                                    @endif
                                </div>
                            @endif

                            <!-- JPO Endorsement Remarks or Rejection Notes -->
                            @if($app->status === 'rejected' && $app->jpo_notes)
                                <div class="p-3.5 rounded-2xl bg-rose-50/80 border border-rose-200 text-xs space-y-1">
                                    <span class="font-bold text-rose-900 flex items-center gap-1.5">
                                        <span>✕</span> Evaluation Feedback / Reason:
                                    </span>
                                    <p class="text-rose-800 text-[11px] leading-relaxed">
                                        {{ $app->jpo_notes }}
                                    </p>
                                </div>
                            @elseif($app->jpo_notes)
                                <div class="p-3.5 rounded-2xl bg-green-50/70 border border-green-200 text-xs space-y-1">
                                    <span class="font-bold text-green-900 flex items-center gap-1.5">
                                        <svg class="h-3.5 w-3.5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Endorsement Note:
                                    </span>
                                    <p class="text-green-800 text-[11px] leading-relaxed">
                                        {{ $app->jpo_notes }}
                                    </p>
                                </div>
                            @endif

                            <!-- Skills Tag Pool -->
                            @if($jobseeker->skills && $jobseeker->skills->count() > 0)
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
                                                <a href="{{ $url }}" target="_blank" class="inline-flex items-center gap-1 rounded-xl bg-slate-100 hover:bg-green-50 hover:text-green-800 hover:border-green-300 border border-slate-200 px-2.5 py-1 text-[11px] font-bold text-slate-700 transition-colors" title="Click to preview {{ $doc['name'] ?? $label }}">
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
                            
                            @if($app->status === 'hired')
                                <div class="flex items-center justify-between w-full gap-2 flex-wrap">
                                    @if($app->resignation_status === 'requested')
                                        <div class="flex items-center gap-2">
                                            <button type="button" 
                                                    @click="openResignation({{ $app->application_id }}, '{{ addslashes($fullName) }}', 'approve')"
                                                    class="px-4 py-2 rounded-xl bg-green-600 hover:bg-green-500 text-white text-xs font-black shadow-md shadow-green-600/20 transition-all">
                                                ✓ Approve Resignation
                                            </button>
                                            <button type="button" 
                                                    @click="openResignation({{ $app->application_id }}, '{{ addslashes($fullName) }}', 'reject')"
                                                    class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition-colors">
                                                ✕ Decline Request
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-xs font-bold text-green-700 flex items-center gap-1">
                                            ✓ Confirmed Hired on {{ $app->hired_date ? date('M d, Y', strtotime($app->hired_date)) : 'Today' }}
                                        </span>
                                    @endif

                                    <a href="{{ route('employer.placement-reports') }}" class="text-xs font-bold text-green-700 hover:underline">
                                        Include in Placement Report &rarr;
                                    </a>
                                </div>
                            @elseif($app->status === 'rejected')
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-bold text-rose-700 flex items-center gap-1">
                                        ✕ Marked as Not Qualified
                                    </span>
                                    @if($app->jpo_notes)
                                        <span class="text-[11px] text-slate-500 italic max-w-sm truncate" title="{{ $app->jpo_notes }}">
                                            &bull; "{{ $app->jpo_notes }}"
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2">
                                    <!-- Allow reconsidering / hiring -->
                                    <form action="{{ route('employer.applicants.update_status', $app->application_id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="action" value="hire">
                                        <button type="submit" onclick="return confirm('Reconsider and confirm that you are officially hiring {{ addslashes($fullName) }}?')"
                                                class="px-3.5 py-1.5 rounded-xl bg-green-50 hover:bg-green-100 text-green-800 text-xs font-bold border border-green-300 transition-colors">
                                            Reconsider & Mark as Hired &rarr;
                                        </button>
                                    </form>
                                </div>
                            @elseif($app->status === 'offered')
                                <div class="flex items-center justify-between w-full gap-2 flex-wrap">
                                    <span class="text-xs font-semibold text-blue-700 flex items-center gap-1">
                                        ⏳ Offer Sent &bull; Awaiting Candidate Response
                                    </span>
                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                                @click="openOffer({{ $app->application_id }}, '{{ addslashes($fullName) }}', '{{ $app->offer_salary }}', '{{ $app->offer_start_date ? date('Y-m-d', strtotime($app->offer_start_date)) : '' }}', '{{ addslashes($app->offer_notes ?? '') }}')"
                                                class="px-3.5 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold border border-blue-200 transition-colors">
                                            ✏️ Revise Offer
                                        </button>
                                        <form action="{{ route('employer.applicants.update_status', $app->application_id) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="action" value="hire">
                                            <button type="submit" onclick="return confirm('Directly mark as hired (e.g. candidate verbally accepted or signed on-site)?')"
                                                    class="px-3.5 py-1.5 rounded-xl bg-green-600 hover:bg-green-500 text-white text-xs font-bold shadow-sm transition-colors">
                                                ✓ Direct Hire
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @elseif($app->status === 'declined')
                                <div class="flex items-center justify-between w-full gap-2 flex-wrap">
                                    <span class="text-xs font-semibold text-slate-500">
                                        Offer declined by candidate.
                                    </span>
                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                                @click="openOffer({{ $app->application_id }}, '{{ addslashes($fullName) }}')"
                                                class="px-3.5 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold border border-blue-200 transition-colors">
                                            🎁 Send Revised Offer
                                        </button>
                                        <button type="button" 
                                                @click="openNotQualified({{ $app->application_id }}, '{{ addslashes($fullName) }}')"
                                                class="px-3.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition-colors">
                                            ✕ Close Application
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center gap-2 flex-wrap">
                                    <!-- Schedule Interview -->
                                    <button type="button" 
                                            @click="openInterview({{ $app->application_id }}, '{{ addslashes($fullName) }}')"
                                            class="px-3 py-2 rounded-xl bg-slate-900 hover:bg-green-600 text-white text-xs font-bold transition-colors">
                                        🗓️ Interview
                                    </button>

                                    <!-- Send Formal Job Offer (Option C) -->
                                    <button type="button" 
                                            @click="openOffer({{ $app->application_id }}, '{{ addslashes($fullName) }}')"
                                            class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-black shadow-md shadow-blue-600/20 transition-all hover:scale-105">
                                        🎁 Extend Offer
                                    </button>

                                    <!-- Mark as Not Qualified Button -->
                                    <button type="button" 
                                            @click="openNotQualified({{ $app->application_id }}, '{{ addslashes($fullName) }}')"
                                            class="px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition-colors">
                                        ✕ Not Qualified
                                    </button>
                                </div>

                                <!-- Direct Hire Button -->
                                <form action="{{ route('employer.applicants.update_status', $app->application_id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="hire">
                                    <button type="submit" onclick="return confirm('Directly confirm that you are officially hiring {{ addslashes($fullName) }}?')"
                                            class="px-3.5 py-2 rounded-xl bg-green-50 hover:bg-green-100 text-green-800 border border-green-300 text-xs font-bold transition-colors">
                                        ✓ Direct Hire
                                    </button>
                                </form>
                            @endif

                        </div>

                    </div>
                @empty
                    <div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-400">
                        No candidates found matching the selected criteria. When jobseekers apply or are endorsed by the DMDP Placement Officer, they appear here.
                    </div>
                @endforelse
            </div>

            <div class="pt-4">
                {{ $referredApplicants->links() }}
            </div>
        </div>

    </div>

    <!-- 1. Schedule Interview Modal -->
    <div x-show="interviewModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="interviewModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-green-700 uppercase tracking-wider">Candidate Interview</span>
                    <h3 class="text-xl font-black text-slate-900 mt-0.5">Schedule Interview with <span x-text="selectedName"></span></h3>
                </div>
                <button @click="interviewModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <form :action="'/employer/applicants/' + selectedAppId + '/status'" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="action" value="interview">

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Interview Date & Time *</label>
                    <input type="datetime-local" name="interview_schedule" min="{{ date('Y-m-d\TH:i') }}" required
                           class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Interview Format *</label>
                    <select name="interview_mode" required
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-green-500 focus:outline-none">
                        <option value="online">Online (Video Meeting / Google Meet / Zoom)</option>
                        <option value="onsite">On-site (Company Office in Cebu)</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Meeting Link or Office Location *</label>
                    <input type="text" name="interview_location" required placeholder="e.g. https://meet.google.com/xyz-abc or 5th Flr IT Tower, Cebu IT Park"
                           class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="interviewModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-green-600 hover:bg-green-500 text-white text-xs font-black shadow-lg shadow-green-600/30">
                        Send Interview Invitation &rarr;
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- 2. Mark as Not Qualified Modal -->
    <div x-show="notQualifiedModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="notQualifiedModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-rose-600 uppercase tracking-wider">Evaluation Outcome</span>
                    <h3 class="text-xl font-black text-slate-900 mt-0.5">Mark <span x-text="selectedName"></span> as Not Qualified</h3>
                </div>
                <button @click="notQualifiedModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <form :action="'/employer/applicants/' + selectedAppId + '/status'" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="action" value="not_qualified">

                <p class="text-xs text-slate-600 leading-relaxed">
                    This candidate will be marked as <span class="font-bold text-rose-700">Not Qualified</span> for this role. You can optionally specify the reasoning below so constructive feedback is shared with the applicant.
                </p>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Evaluation Reason / Remarks</label>
                    <textarea name="remarks" rows="3" placeholder="e.g. Does not meet minimum specialized years of experience; position filled by another candidate..."
                              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-400">Candidate does not meet the specified qualifications for this role.</textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="notQualifiedModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-black shadow-lg shadow-rose-600/30">
                        Confirm: Not Qualified &rarr;
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- 3. Resignation Response Modal -->
    <div x-show="resignationModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="resignationModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider" :class="resignationAction === 'approve' ? 'text-green-700' : 'text-rose-700'" x-text="resignationAction === 'approve' ? 'Approve Employee Resignation' : 'Decline Resignation Request'"></span>
                    <h3 class="text-xl font-black text-slate-900 mt-0.5">
                        <span x-text="resignationAction === 'approve' ? 'Release ' : 'Decline Request from '"></span>
                        <span x-text="selectedName"></span>
                    </h3>
                </div>
                <button @click="resignationModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <form :action="'/employer/applicants/' + selectedAppId + '/resignation'" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="action" :value="resignationAction">

                <div x-show="resignationAction === 'approve'" class="p-4 rounded-2xl bg-green-50 border border-green-200 text-xs text-green-950 space-y-1">
                    <p class="font-extrabold flex items-center gap-1">
                        <span>✓</span> Resignation Clearance Impact:
                    </p>
                    <p class="leading-relaxed">
                        Approving this resignation will officially conclude their employment at your company. Their profile status will automatically be updated back to <strong>Unemployed</strong>, allowing them to browse and apply for other employment opportunities on TrabaGo.
                    </p>
                </div>

                <div x-show="resignationAction === 'reject'" class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-950 space-y-1">
                    <p class="font-extrabold flex items-center gap-1">
                        <span>✕</span> Request Declined:
                    </p>
                    <p class="leading-relaxed">
                        The candidate will remain marked as <strong>Employed</strong> at your company and will continue to be restricted from applying to other jobs.
                    </p>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Remarks / Clearance Notes (Optional)</label>
                    <textarea name="remarks" rows="3" placeholder="Provide any clearance notes, handover status, or reasons for your decision..."
                              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-400"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="resignationModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            :class="resignationAction === 'approve' ? 'bg-green-600 hover:bg-green-500 text-white shadow-green-600/30' : 'bg-rose-600 hover:bg-rose-500 text-white shadow-rose-600/30'"
                            class="px-7 py-2.5 rounded-xl text-xs font-black shadow-lg transition-all">
                        <span x-text="resignationAction === 'approve' ? 'Confirm Resignation Approval &rarr;' : 'Confirm Decline &rarr;'"></span>
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- 4. Extend Job Offer Modal (Option C) -->
    <div x-show="offerModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="offerModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">Formal Employment Offer</span>
                    <h3 class="text-xl font-black text-slate-900 mt-0.5">Extend Job Offer to <span x-text="selectedName"></span></h3>
                </div>
                <button @click="offerModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <form :action="'/employer/applicants/' + selectedAppId + '/status'" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="action" value="offer">

                <div class="p-3.5 rounded-2xl bg-blue-50/80 border border-blue-200 text-xs text-blue-950 space-y-1">
                    <p class="font-bold flex items-center gap-1">
                        <span>💡</span> How Offer & Acceptance Works:
                    </p>
                    <p class="leading-relaxed text-[11px] text-blue-900">
                        Sending this offer notifies <strong x-text="selectedName"></strong> with your terms. They can review and click <strong>Accept</strong> or <strong>Decline</strong> in their applications dashboard. Once accepted, their profile is automatically tagged as Employed at your company!
                    </p>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Offered Monthly Salary (₱) (Optional)</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-3 text-xs font-bold text-slate-400">₱</span>
                        <input type="number" step="0.01" min="0" name="offer_salary" x-model="offerSalary" placeholder="e.g. 25000"
                               class="w-full rounded-2xl border border-slate-200 pl-8 pr-4 py-3 text-xs text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <span class="text-[10px] text-slate-400">Leave blank if salary will be finalized in contract.</span>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Target Start Date (Optional)</label>
                    <input type="date" name="offer_start_date" x-model="offerStartDate" min="{{ date('Y-m-d') }}"
                           class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Offer Message & Benefits Note (Optional)</label>
                    <textarea name="offer_notes" x-model="offerNotes" rows="3" placeholder="e.g. Welcome to the team! Includes HMO upon Day 1, 13th month pay, 14 days annual leave. First day reporting time: 9:00 AM at Cebu IT Park..."
                              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="offerModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-black shadow-lg shadow-blue-600/30 transition-all hover:scale-105">
                        Send Official Job Offer &rarr;
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection

