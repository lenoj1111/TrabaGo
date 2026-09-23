@extends('layouts.jobseeker')

@section('title', 'My Applications - TrabaGo Pipeline')

@section('content')
<div x-data="{ 
    resignationModal: false, 
    activeCompany: '',
    declineModal: false,
    declineAppId: null,
    declineJobTitle: '',
    declineCompany: '',
    openDecline(id, title, company) {
        this.declineAppId = id;
        this.declineJobTitle = title;
        this.declineCompany = company;
        this.declineModal = true;
    }
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-800 border border-green-200">
                    <span class="h-2 w-2 rounded-full bg-green-600"></span>
                    Application Lifecycle Tracker
                </span>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight mt-1">My Job Applications</h1>
                <p class="text-sm text-slate-500">Track your recruitment pipeline from initial review to interview schedules.</p>
            </div>
            <a href="{{ route('jobseeker.jobs') }}" class="inline-flex items-center gap-2 rounded-xl bg-green-600 hover:bg-green-500 px-5 py-2.5 text-xs font-bold text-white shadow-sm transition-all">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Find More Jobs
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-green-50 border border-green-200 text-green-900 text-xs font-bold flex items-center gap-2 shadow-xs">
                <span class="text-green-700">✓</span> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-bold flex items-center gap-2 shadow-xs">
                <span class="text-rose-700">✕</span> {{ session('error') }}
            </div>
        @endif

        <!-- Filter Status Tabs -->
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-4">
            <a href="{{ route('jobseeker.applications') }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $filter === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                <span>All Applications</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'all' ? 'bg-slate-700 text-slate-200' : 'bg-slate-100 text-slate-700' }}">
                    {{ $counts['all'] }}
                </span>
            </a>

            <a href="{{ route('jobseeker.applications', ['status' => 'offered']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $filter === 'offered' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-blue-700 border border-blue-200 hover:bg-blue-50' }} {{ ($counts['offered'] ?? 0) > 0 ? 'ring-2 ring-blue-400 ring-offset-1 font-black animate-pulse' : '' }}">
                <span>🎁 Job Offers</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'offered' ? 'bg-blue-800 text-white' : 'bg-blue-100 text-blue-800' }} font-bold">
                    {{ $counts['offered'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('jobseeker.applications', ['status' => 'pending']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $filter === 'pending' ? 'bg-green-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                <span>🟡 Pending</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'pending' ? 'bg-green-800 text-white' : 'bg-green-50 text-green-800' }} font-bold">
                    {{ $counts['pending'] }}
                </span>
            </a>

            <a href="{{ route('jobseeker.applications', ['status' => 'reviewed']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $filter === 'reviewed' ? 'bg-green-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                <span>🔵 Under Review</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'reviewed' ? 'bg-green-800 text-white' : 'bg-green-50 text-green-800' }}">
                    {{ $counts['reviewed'] }}
                </span>
            </a>

            <a href="{{ route('jobseeker.applications', ['status' => 'interview']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $filter === 'interview' ? 'bg-green-700 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                <span>🟢 Interview</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'interview' ? 'bg-green-900 text-white' : 'bg-green-50 text-green-800' }}">
                    {{ $counts['interview'] }}
                </span>
            </a>

            <a href="{{ route('jobseeker.applications', ['status' => 'hired']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $filter === 'hired' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                <span>🏆 Hired</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'hired' ? 'bg-slate-700 text-white' : 'bg-slate-100 text-slate-700' }}">
                    {{ $counts['hired'] }}
                </span>
            </a>

            <a href="{{ route('jobseeker.applications', ['status' => 'rejected']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $filter === 'rejected' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                <span>🔴 Not Qualified</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'rejected' ? 'bg-rose-800 text-white' : 'bg-rose-50 text-rose-800' }} font-bold">
                    {{ $counts['rejected'] }}
                </span>
            </a>
        </div>

        <!-- Application Cards List -->
        <div class="space-y-6">
            @forelse ($applications as $app)
                @php
                    $job = $app->jobPosting;
                    $company = $job && $job->employer ? $job->employer->company_name : 'Partner Employer';
                    $match = $app->match_details ?? ['percentage' => 85, 'tier' => 'High Match'];
                    
                    // Pipeline step index: 1 = pending, 2 = reviewed, 3 = interview, 4 = offered, 5 = hired/rejected/declined
                    $step = 1;
                    if ($app->status === 'reviewed') $step = 2;
                    elseif ($app->status === 'interview') $step = 3;
                    elseif ($app->status === 'offered') $step = 4;
                    elseif ($app->status === 'hired' || $app->status === 'rejected' || $app->status === 'declined') $step = 5;
                @endphp

                <div class="rounded-3xl border {{ $app->status === 'offered' ? 'border-blue-400 ring-2 ring-blue-100 shadow-md' : 'border-slate-200 shadow-sm' }} bg-white p-6 sm:p-8 space-y-6">
                    
                    <!-- Top Info Bar -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-green-700">{{ $company }}</span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="text-xs text-slate-500">Applied on {{ $app->created_at ? $app->created_at->format('M d, Y') : 'Recently' }}</span>
                            </div>
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">
                                {{ $job ? $job->title : 'Job Position' }}
                            </h2>
                        </div>

                        <!-- Status Badge & Cosine Rating -->
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center rounded-xl bg-green-50 border border-green-200 px-3 py-1.5 text-xs font-bold text-green-800">
                                ⚡ {{ $match['percentage'] ?? 0 }}% Skill Match
                            </span>

                            @if($app->status === 'pending')
                                <span class="inline-flex items-center rounded-xl bg-amber-50 text-amber-900 border border-amber-200 px-3.5 py-1.5 text-xs font-extrabold">
                                    🟡 Pending Review
                                </span>
                            @elseif($app->status === 'reviewed')
                                <span class="inline-flex items-center rounded-xl bg-green-50 text-green-900 border border-green-200 px-3.5 py-1.5 text-xs font-extrabold">
                                    🔵 Under Evaluation
                                </span>
                            @elseif($app->status === 'interview')
                                <span class="inline-flex items-center rounded-xl bg-green-100 text-green-900 border border-green-300 px-3.5 py-1.5 text-xs font-extrabold">
                                    🟢 Interview Stage
                                </span>
                            @elseif($app->status === 'offered')
                                <span class="inline-flex items-center rounded-xl bg-blue-600 text-white px-3.5 py-1.5 text-xs font-black shadow-md shadow-blue-500/30 animate-pulse">
                                    🎁 Job Offer Received!
                                </span>
                            @elseif($app->status === 'declined')
                                <span class="inline-flex items-center rounded-xl bg-slate-100 text-slate-700 border border-slate-300 px-3.5 py-1.5 text-xs font-extrabold">
                                    ✕ Offer Declined
                                </span>
                            @elseif($app->status === 'withdrawn')
                                <span class="inline-flex items-center rounded-xl bg-slate-100 text-slate-500 border border-slate-200 px-3.5 py-1.5 text-xs font-semibold">
                                    ⚪ Withdrawn
                                </span>
                            @elseif($app->status === 'hired')
                                @if($app->resignation_status === 'requested')
                                    <span class="inline-flex items-center rounded-xl bg-amber-50 text-amber-900 border border-amber-300 px-3.5 py-1.5 text-xs font-extrabold animate-pulse">
                                        ⏳ Resignation Pending Approval
                                    </span>
                                @elseif($app->resignation_status === 'approved')
                                    <span class="inline-flex items-center rounded-xl bg-slate-100 text-slate-700 border border-slate-300 px-3.5 py-1.5 text-xs font-extrabold">
                                        💼 Resigned / Released
                                    </span>
                                @elseif($app->resignation_status === 'rejected')
                                    <span class="inline-flex items-center rounded-xl bg-rose-50 text-rose-900 border border-rose-300 px-3.5 py-1.5 text-xs font-extrabold">
                                        🏆 Hired (Resignation Declined)
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-xl bg-green-600 text-white px-3.5 py-1.5 text-xs font-extrabold shadow-sm">
                                        🏆 Hired & Employed
                                    </span>
                                @endif
                            @elseif($app->status === 'rejected')
                                <span class="inline-flex items-center rounded-xl bg-rose-100 text-rose-900 border border-rose-200 px-3.5 py-1.5 text-xs font-extrabold">
                                    🔴 Not Qualified
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Progress Stepper Pipeline in green & Indigo Theme -->
                    <div class="py-2">
                        <div class="relative">
                            <div class="overflow-hidden h-2.5 mb-4 text-xs flex rounded-full bg-slate-100">
                                <div style="width: {{ $step === 1 ? '20%' : ($step === 2 ? '40%' : ($step === 3 ? '60%' : ($step === 4 ? '80%' : '100%'))) }}" 
                                     class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $app->status === 'rejected' ? 'bg-rose-500' : ($app->status === 'offered' ? 'bg-gradient-to-r from-blue-500 to-indigo-600' : 'bg-gradient-to-r from-green-500 via-green-500 to-green-600') }} transition-all duration-500">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-5 text-center text-xs font-bold text-slate-500">
                                <div class="{{ $step >= 1 ? 'text-green-700' : '' }}">
                                    1. Submitted
                                </div>
                                <div class="{{ $step >= 2 ? 'text-green-700' : '' }}">
                                    2. Review
                                </div>
                                <div class="{{ $step >= 3 ? 'text-green-700' : '' }}">
                                    3. Interview
                                </div>
                                <div class="{{ $step >= 4 ? ($app->status === 'offered' ? 'text-blue-700 font-black' : 'text-green-700') : '' }}">
                                    4. Job Offer
                                </div>
                                <div class="{{ $step >= 5 ? ($app->status === 'rejected' ? 'text-rose-600 font-bold' : ($app->status === 'declined' ? 'text-slate-600 font-bold' : 'text-green-700 font-black')) : '' }}">
                                    {{ $app->status === 'rejected' ? '5. Not Qualified' : ($app->status === 'hired' ? '5. Hired!' : ($app->status === 'declined' ? '5. Declined' : '5. Employed')) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Interview Details Alert (if status === interview) -->
                    @if($app->status === 'interview' && $app->interview_schedule)
                        <div class="rounded-2xl bg-green-50 border border-green-200 p-5 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-green-800 flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Interview Scheduled
                                </span>
                                <span class="text-xs font-bold text-green-700 uppercase">{{ $app->interview_mode ?: 'Online' }}</span>
                            </div>
                            <p class="text-sm font-bold text-slate-900">
                                Date & Time: {{ \Carbon\Carbon::parse($app->interview_schedule)->format('M d, Y @ h:i A') }}
                            </p>
                            @if($app->interview_location)
                                <p class="text-xs text-slate-600">Location / Meeting Link: {{ $app->interview_location }}</p>
                            @endif
                        </div>
                    @endif

                    <!-- Job Offer Banner (Option C) -->
                    @if($app->status === 'offered')
                        <div class="rounded-3xl bg-gradient-to-br from-blue-500 via-indigo-600 to-green-600 p-1 shadow-xl animate-in fade-in">
                            <div class="rounded-[22px] bg-white p-6 sm:p-7 space-y-5">
                                
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                                    <div class="flex items-center gap-3.5">
                                        <div class="h-12 w-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center text-2xl shadow-md shadow-blue-500/30 shrink-0">
                                            🎁
                                        </div>
                                        <div>
                                            <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2.5 py-0.5 text-[10px] font-extrabold text-blue-800 uppercase tracking-wider">
                                                Action Required
                                            </span>
                                            <h3 class="text-xl font-black text-slate-900 mt-0.5">Formal Job Offer from {{ $company }}</h3>
                                        </div>
                                    </div>
                                    <span class="text-xs font-semibold text-slate-500">
                                        Extended {{ $app->offered_at ? \Carbon\Carbon::parse($app->offered_at)->diffForHumans() : 'Recently' }}
                                    </span>
                                </div>

                                <!-- Offer Terms Grid -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-100 space-y-1">
                                        <span class="text-xs font-bold uppercase tracking-wider text-blue-700">Offered Monthly Salary</span>
                                        <p class="text-2xl font-black text-blue-950">
                                            {{ $app->offer_salary ? '₱' . number_format($app->offer_salary, 2) : 'Negotiable / Per Contract' }}
                                        </p>
                                    </div>
                                    <div class="p-4 rounded-2xl bg-green-50/70 border border-green-100 space-y-1">
                                        <span class="text-xs font-bold uppercase tracking-wider text-green-700">Target Start Date</span>
                                        <p class="text-2xl font-black text-green-950">
                                            {{ $app->offer_start_date ? date('M d, Y', strtotime($app->offer_start_date)) : 'To Be Arranged' }}
                                        </p>
                                    </div>
                                </div>

                                @if($app->offer_notes)
                                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                                        <span class="font-bold text-slate-900 flex items-center gap-1">
                                            <span>💬</span> Employer Message & Benefits:
                                        </span>
                                        <p class="leading-relaxed whitespace-pre-line text-[11px] text-slate-800 italic">
                                            "{{ $app->offer_notes }}"
                                        </p>
                                    </div>
                                @endif

                                <!-- Decision Advisory Notice -->
                                <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-950 flex items-start gap-2">
                                    <span class="text-base leading-none">💡</span>
                                    <p class="leading-relaxed text-[11px]">
                                        <strong>Offer & Acceptance Policy:</strong> Clicking <strong>Accept Job Offer</strong> will confirm your employment with <strong>{{ $company }}</strong>. Your profile will automatically be tagged as <strong>Employed</strong> and any other active job applications will be automatically withdrawn. If you choose to <strong>Decline</strong>, this application will close and your other job applications will remain active.
                                    </p>
                                </div>

                                <!-- Accept & Decline Action Buttons -->
                                <div class="pt-2 flex flex-col sm:flex-row sm:items-center justify-end gap-3">
                                    <button type="button" 
                                            @click="openDecline({{ $app->application_id }}, '{{ addslashes($job ? $job->title : 'Job') }}', '{{ addslashes($company) }}')"
                                            class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-white hover:bg-rose-50 text-rose-700 font-bold text-xs border border-slate-200 hover:border-rose-300 transition-colors text-center">
                                        ✕ Decline Offer
                                    </button>

                                    <form action="{{ route('jobseeker.applications.accept_offer', $app->application_id) }}" method="POST" class="w-full sm:w-auto">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('🎉 Congratulations! Are you sure you want to ACCEPT this job offer from {{ addslashes($company) }}?\n\nThis will tag your profile as Employed and automatically withdraw your other active applications.')"
                                                class="w-full sm:w-auto px-8 py-3 rounded-2xl bg-green-600 hover:bg-green-500 text-white font-black text-xs shadow-xl shadow-green-600/30 transition-all hover:scale-105 flex items-center justify-center gap-2">
                                            <span>✓</span>
                                            <span>Accept Job Offer</span>
                                            <span>&rarr;</span>
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    @endif

                    <!-- Offer Declined Notice -->
                    @if($app->status === 'declined')
                        <div class="rounded-2xl bg-slate-100 border border-slate-200 p-5 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                                    <span>✕</span> Job Offer Declined
                                </span>
                                <span class="text-xs text-slate-500">
                                    {{ $app->declined_at ? \Carbon\Carbon::parse($app->declined_at)->format('M d, Y') : '' }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                You previously declined the job offer for this position.
                                @if($app->decline_reason)
                                    Reason provided: <em>"{{ $app->decline_reason }}"</em>
                                @endif
                            </p>
                        </div>
                    @endif

                    <!-- Not Qualified JPO Evaluation Reason Alert -->
                    @if($app->status === 'rejected')
                        <div class="rounded-2xl bg-rose-50 border border-rose-200 p-5 space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-rose-900 flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    JPO Evaluation Feedback
                                </span>
                                @if($app->jpo_evaluated_at)
                                    <span class="text-[11px] text-rose-700 font-medium">Evaluated {{ \Carbon\Carbon::parse($app->jpo_evaluated_at)->format('M d, Y') }}</span>
                                @endif
                            </div>
                            <div class="bg-white/80 rounded-xl p-3 border border-rose-100">
                                <p class="text-xs text-rose-950 leading-relaxed">
                                    <span class="font-bold text-rose-900">Reason Given by JPO:</span>
                                    <span class="font-medium">{{ $app->jpo_notes ?: 'Does not meet the minimum required qualifications or experience criteria for this position.' }}</span>
                                </p>
                            </div>
                            <div class="pt-2 flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-t border-rose-200/60">
                                <p class="text-[11px] text-rose-700">Upskill with DMDP-certified courses to meet criteria for similar roles.</p>
                                <a href="{{ route('jobseeker.training') }}" class="inline-flex items-center gap-1 text-xs font-bold text-rose-900 hover:text-rose-950 underline">
                                    Browse Available Training Skills &rarr;
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Hired & Resignation Management Alert -->
                    @if($app->status === 'hired')
                        @if($app->resignation_status === 'requested')
                            <div class="rounded-2xl bg-amber-50 border border-amber-300 p-5 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-black uppercase tracking-wider text-amber-950 flex items-center gap-1.5">
                                        <span>⏳</span> Resignation Request Submitted to Employer
                                    </span>
                                    <span class="text-[11px] text-amber-800 font-semibold">
                                        Submitted {{ $app->resignation_requested_at ? \Carbon\Carbon::parse($app->resignation_requested_at)->diffForHumans() : 'Recently' }}
                                    </span>
                                </div>
                                <p class="text-xs text-amber-900 leading-relaxed">
                                    Your resignation request has been forwarded to <strong>{{ $company }}</strong> for formal review. As soon as the employer approves your request, your profile will be tagged as <strong>Unemployed</strong> and you will be eligible to apply for other jobs.
                                </p>
                                <div class="bg-white/95 p-3 rounded-xl border border-amber-200 text-xs text-slate-800 shadow-xs">
                                    <span class="font-bold text-amber-950">Reason Submitted:</span> "{{ $app->resignation_reason }}"
                                </div>
                            </div>
                        @elseif($app->resignation_status === 'approved')
                            <div class="rounded-2xl bg-green-50 border border-green-300 p-5 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-black uppercase tracking-wider text-green-950 flex items-center gap-1.5">
                                        <span>✓</span> Resignation Approved by {{ $company }}
                                    </span>
                                    <span class="text-[11px] text-green-800 font-semibold">
                                        Approved {{ $app->resignation_approved_at ? \Carbon\Carbon::parse($app->resignation_approved_at)->format('M d, Y') : '' }}
                                    </span>
                                </div>
                                <p class="text-xs text-green-900 leading-relaxed">
                                    Your resignation was officially approved. Your platform profile is now set to <strong>Unemployed</strong> and you are fully authorized to apply for new vacancies!
                                </p>
                                @if($app->resignation_remarks)
                                    <div class="bg-white/90 p-2.5 rounded-xl border border-green-200 text-xs text-slate-700">
                                        <span class="font-bold">Employer Clearance Remarks:</span> {{ $app->resignation_remarks }}
                                    </div>
                                @endif
                            </div>
                        @elseif($app->resignation_status === 'rejected')
                            <div class="rounded-2xl bg-rose-50 border border-rose-300 p-5 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-black uppercase tracking-wider text-rose-900 flex items-center gap-1.5">
                                        <span>✕</span> Resignation Request Declined by {{ $company }}
                                    </span>
                                </div>
                                <p class="text-xs text-rose-950 leading-relaxed">
                                    {{ $company }} declined your resignation request. You remain registered as actively employed at this company.
                                </p>
                                @if($app->resignation_remarks)
                                    <div class="bg-white/95 p-2.5 rounded-xl border border-rose-200 text-xs text-slate-800 shadow-xs">
                                        <span class="font-bold text-rose-900">Employer Reason:</span> {{ $app->resignation_remarks }}
                                    </div>
                                @endif
                                <div class="pt-1">
                                    <button type="button" @click="activeCompany = '{{ addslashes($company) }}'; resignationModal = true"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-xs transition-colors">
                                        Submit Revised Resignation Request &rarr;
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="rounded-2xl bg-green-50/80 border border-green-200 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <p class="text-xs font-bold text-green-950 uppercase tracking-wider flex items-center gap-1.5">
                                        <span>💼</span> Active Employment Record
                                    </p>
                                    <p class="text-xs text-green-800 leading-relaxed">You are actively employed at <strong>{{ $company }}</strong>. You cannot apply for other jobs until you request and receive resignation approval from this employer.</p>
                                </div>
                                <button type="button" @click="activeCompany = '{{ addslashes($company) }}'; resignationModal = true"
                                        class="shrink-0 px-5 py-2.5 rounded-xl bg-green-600 hover:bg-green-500 text-white text-xs font-black shadow-md shadow-green-600/20 transition-all">
                                    Request Resignation &rarr;
                                </button>
                            </div>
                        @endif
                    @endif

                    <!-- Bottom Actions -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
                        @if($job)
                            <a href="{{ route('jobseeker.jobs.show', $job->job_id) }}" class="text-xs font-bold text-green-700 hover:text-green-800">
                                View Job Posting &rarr;
                            </a>
                        @else
                            <span></span>
                        @endif

                        <div class="flex items-center gap-3">
                            @if($app->status === 'hired')
                                @if($app->resignation_status === 'requested')
                                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-amber-100 text-amber-900 border border-amber-300 text-xs font-bold">
                                        ⏳ Resignation Pending
                                    </span>
                                @elseif($app->resignation_status !== 'approved')
                                    <button type="button" @click="activeCompany = '{{ addslashes($company) }}'; resignationModal = true"
                                            class="px-4 py-2 rounded-xl border border-green-300 bg-green-50 hover:bg-green-100 text-green-900 text-xs font-bold transition-colors">
                                        Request Resignation
                                    </button>
                                @endif
                            @elseif(!in_array($app->status, ['rejected', 'offered', 'declined', 'withdrawn']))
                                <form action="{{ route('jobseeker.applications.withdraw', $app->application_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to withdraw this application?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors">
                                        Withdraw Application
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                </div>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center space-y-3">
                    <div class="h-12 w-12 rounded-2xl bg-green-50 text-green-600 mx-auto flex items-center justify-center font-bold text-xl">
                        📋
                    </div>
                    <h3 class="text-base font-bold text-slate-900">No applications found in this status</h3>
                    <p class="text-xs text-slate-500">Explore open job positions and apply to track them here.</p>
                    <a href="{{ route('jobseeker.jobs') }}" class="inline-flex rounded-xl bg-green-600 px-5 py-2.5 text-xs font-bold text-white shadow-sm">
                        Browse Openings
                    </a>
                </div>
            @endforelse
        </div>

    </div>

    <!-- Resignation Request Modal -->
    <div x-show="resignationModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="resignationModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-amber-700 uppercase tracking-wider">Formal Request</span>
                    <h3 class="text-xl font-black text-slate-900 mt-0.5">Request Resignation</h3>
                    <p class="text-xs text-slate-500" x-text="'Employer: ' + activeCompany"></p>
                </div>
                <button @click="resignationModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <form action="{{ route('jobseeker.resignation.request') }}" method="POST" class="space-y-4">
                @csrf
                
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-950 space-y-1.5">
                    <p class="font-extrabold flex items-center gap-1 text-amber-900">
                        <span>ℹ️</span> Policy Notice on Resignation:
                    </p>
                    <p class="leading-relaxed">
                        Under TrabaGo policy, employed candidates cannot apply to other jobs until their current employer approves resignation. Submitting this request sends a formal resignation notice to your employer. Once your employer confirms and approves your resignation, your status will automatically revert to <strong>Unemployed</strong>, enabling you to apply for new jobs.
                    </p>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Reason for Resignation *</label>
                    <textarea name="reason" rows="4" required placeholder="Please state the reason for requesting resignation (e.g., career transition, relocated, personal reasons)..."
                              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-400"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="resignationModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-black shadow-lg shadow-amber-600/30 transition-all">
                        Submit Resignation Request &rarr;
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Decline Job Offer Modal (Option C) -->
    <div x-show="declineModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="declineModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-rose-700 uppercase tracking-wider">Decline Offer</span>
                    <h3 class="text-xl font-black text-slate-900 mt-0.5">Decline Job Offer</h3>
                    <p class="text-xs text-slate-500" x-text="declineJobTitle + ' at ' + declineCompany"></p>
                </div>
                <button @click="declineModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <form :action="'/jobseeker/applications/' + declineAppId + '/decline-offer'" method="POST" class="space-y-4">
                @csrf
                
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-950 space-y-1">
                    <p class="font-extrabold flex items-center gap-1 text-rose-900">
                        <span>⚠️</span> Are you sure you want to decline this offer?
                    </p>
                    <p class="leading-relaxed">
                        Declining will close this specific application and notify <strong x-text="declineCompany"></strong>. Your profile will remain <strong>Unemployed</strong> and all your other active applications will stay safe and intact.
                    </p>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Reason for Declining (Optional)</label>
                    <textarea name="decline_reason" rows="3" placeholder="e.g., Accepted an offer elsewhere, salary expectations, schedule conflict, location..."
                              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-400"></textarea>
                    <span class="text-[10px] text-slate-400">Giving constructive feedback helps employers understand your decision.</span>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="declineModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Keep Offer
                    </button>
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-black shadow-lg shadow-rose-600/30 transition-all">
                        Confirm: Decline Offer &rarr;
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection
