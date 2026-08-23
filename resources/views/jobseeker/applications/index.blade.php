@extends('layouts.jobseeker')

@section('title', 'My Applications - TrabaGo Pipeline')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800 border border-emerald-200">
                    <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                    Application Lifecycle Tracker
                </span>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight mt-1">My Job Applications</h1>
                <p class="text-sm text-slate-500">Track your recruitment pipeline from initial review to interview schedules.</p>
            </div>
            <a href="{{ route('jobseeker.jobs') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 px-5 py-2.5 text-xs font-bold text-white shadow-sm transition-all">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Find More Jobs
            </a>
        </div>

        <!-- Filter Status Tabs -->
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-4">
            <a href="{{ route('jobseeker.applications') }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $filter === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                <span>All Applications</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'all' ? 'bg-slate-700 text-slate-200' : 'bg-slate-100 text-slate-700' }}">
                    {{ $counts['all'] }}
                </span>
            </a>

            <a href="{{ route('jobseeker.applications', ['status' => 'pending']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $filter === 'pending' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                <span>🟡 Pending</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'pending' ? 'bg-emerald-800 text-white' : 'bg-emerald-50 text-emerald-800' }} font-bold">
                    {{ $counts['pending'] }}
                </span>
            </a>

            <a href="{{ route('jobseeker.applications', ['status' => 'reviewed']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $filter === 'reviewed' ? 'bg-teal-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                <span>🔵 Under Review</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'reviewed' ? 'bg-teal-800 text-white' : 'bg-teal-50 text-teal-800' }}">
                    {{ $counts['reviewed'] }}
                </span>
            </a>

            <a href="{{ route('jobseeker.applications', ['status' => 'interview']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $filter === 'interview' ? 'bg-emerald-700 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                <span>🟢 Interview</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'interview' ? 'bg-emerald-900 text-white' : 'bg-emerald-50 text-emerald-800' }}">
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
        </div>

        <!-- Application Cards List -->
        <div class="space-y-6">
            @forelse ($applications as $app)
                @php
                    $job = $app->jobPosting;
                    $company = $job && $job->employer ? $job->employer->company_name : 'Partner Employer';
                    $match = $app->match_details ?? ['percentage' => 85, 'tier' => 'High Match'];
                    
                    // Pipeline step index: 1 = pending, 2 = reviewed, 3 = interview, 4 = hired/rejected
                    $step = 1;
                    if ($app->status === 'reviewed') $step = 2;
                    elseif ($app->status === 'interview') $step = 3;
                    elseif ($app->status === 'hired' || $app->status === 'rejected') $step = 4;
                @endphp

                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                    
                    <!-- Top Info Bar -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">{{ $company }}</span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="text-xs text-slate-500">Applied on {{ $app->created_at ? $app->created_at->format('M d, Y') : 'Recently' }}</span>
                            </div>
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">
                                {{ $job ? $job->title : 'Job Position' }}
                            </h2>
                        </div>

                        <!-- Status Badge & Cosine Rating -->
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center rounded-xl bg-emerald-50 border border-emerald-200 px-3 py-1.5 text-xs font-bold text-emerald-800">
                                ⚡ {{ $match['percentage'] ?? 0 }}% Skill Match
                            </span>

                            @if($app->status === 'pending')
                                <span class="inline-flex items-center rounded-xl bg-amber-50 text-amber-900 border border-amber-200 px-3.5 py-1.5 text-xs font-extrabold">
                                    🟡 Pending Review
                                </span>
                            @elseif($app->status === 'reviewed')
                                <span class="inline-flex items-center rounded-xl bg-teal-50 text-teal-900 border border-teal-200 px-3.5 py-1.5 text-xs font-extrabold">
                                    🔵 Under Evaluation
                                </span>
                            @elseif($app->status === 'interview')
                                <span class="inline-flex items-center rounded-xl bg-emerald-100 text-emerald-900 border border-emerald-300 px-3.5 py-1.5 text-xs font-extrabold">
                                    🟢 Interview Stage
                                </span>
                            @elseif($app->status === 'hired')
                                <span class="inline-flex items-center rounded-xl bg-slate-900 text-white px-3.5 py-1.5 text-xs font-extrabold">
                                    🏆 Hired
                                </span>
                            @elseif($app->status === 'rejected')
                                <span class="inline-flex items-center rounded-xl bg-rose-100 text-rose-900 px-3.5 py-1.5 text-xs font-extrabold">
                                    🔴 Not Selected
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Progress Stepper Pipeline in Emerald Theme -->
                    <div class="py-2">
                        <div class="relative">
                            <div class="overflow-hidden h-2.5 mb-4 text-xs flex rounded-full bg-slate-100">
                                <div style="width: {{ $step === 1 ? '25%' : ($step === 2 ? '50%' : ($step === 3 ? '75%' : '100%')) }}" 
                                     class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $app->status === 'rejected' ? 'bg-rose-500' : 'bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600' }} transition-all duration-500">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-4 text-center text-xs font-bold text-slate-500">
                                <div class="{{ $step >= 1 ? 'text-emerald-700' : '' }}">
                                    1. Submitted
                                </div>
                                <div class="{{ $step >= 2 ? 'text-emerald-700' : '' }}">
                                    2. Under Review
                                </div>
                                <div class="{{ $step >= 3 ? 'text-emerald-700' : '' }}">
                                    3. Interview
                                </div>
                                <div class="{{ $step >= 4 ? ($app->status === 'rejected' ? 'text-rose-600' : 'text-emerald-700 font-black') : '' }}">
                                    4. Final Decision
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Interview Details Alert (if status === interview) -->
                    @if($app->status === 'interview' && $app->interview_schedule)
                        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-5 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Interview Scheduled
                                </span>
                                <span class="text-xs font-bold text-emerald-700 uppercase">{{ $app->interview_mode ?: 'Online' }}</span>
                            </div>
                            <p class="text-sm font-bold text-slate-900">
                                Date & Time: {{ \Carbon\Carbon::parse($app->interview_schedule)->format('M d, Y @ h:i A') }}
                            </p>
                            @if($app->interview_location)
                                <p class="text-xs text-slate-600">Location / Meeting Link: {{ $app->interview_location }}</p>
                            @endif
                        </div>
                    @endif

                    <!-- Bottom Actions -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-4">
                        @if($job)
                            <a href="{{ route('jobseeker.jobs.show', $job->job_id) }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">
                                View Job Posting &rarr;
                            </a>
                        @else
                            <span></span>
                        @endif

                        <div class="flex items-center gap-3">
                            <form action="{{ route('jobseeker.applications.withdraw', $app->application_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to withdraw this application?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors">
                                    Withdraw Application
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center space-y-3">
                    <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 mx-auto flex items-center justify-center font-bold text-xl">
                        📋
                    </div>
                    <h3 class="text-base font-bold text-slate-900">No applications found in this status</h3>
                    <p class="text-xs text-slate-500">Explore open job positions and apply to track them here.</p>
                    <a href="{{ route('jobseeker.jobs') }}" class="inline-flex rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-bold text-white shadow-sm">
                        Browse Openings
                    </a>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
