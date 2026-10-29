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
    <div class="w-full max-w-[1680px] mx-auto space-y-8">
        
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

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-bold flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-rose-700 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg> {{ session('error') }}
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
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H4.5a1.5 1.5 0 0 1-1.5-1.5v-8.25M21 11.25H3m18 0a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 11.25m18 0V7.5a2.25 2.25 0 0 0-2.25-2.25H16.5m-9 0H5.25A2.25 2.25 0 0 0 3 7.5v3.75m13.5-3.75a2.25 2.25 0 0 0-2.25-2.25H12m0 0H9.75A2.25 2.25 0 0 0 7.5 7.5m4.5-2.25V21"/></svg>
                <span>Job Offers</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'offered' ? 'bg-blue-800 text-white' : 'bg-blue-100 text-blue-800' }} font-bold">
                    {{ $counts['offered'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('jobseeker.applications', ['status' => 'pending']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $filter === 'pending' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span>Pending</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'pending' ? 'bg-emerald-800 text-white' : 'bg-emerald-50 text-emerald-800' }} font-bold">
                    {{ $counts['pending'] }}
                </span>
            </a>

            <a href="{{ route('jobseeker.applications', ['status' => 'reviewed']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $filter === 'reviewed' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                <span>Under Review</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'reviewed' ? 'bg-emerald-800 text-white' : 'bg-emerald-50 text-emerald-800' }}">
                    {{ $counts['reviewed'] }}
                </span>
            </a>

            <a href="{{ route('jobseeker.applications', ['status' => 'interview']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $filter === 'interview' ? 'bg-emerald-700 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                <span>Interview</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'interview' ? 'bg-emerald-900 text-white' : 'bg-emerald-50 text-emerald-800' }}">
                    {{ $counts['interview'] }}
                </span>
            </a>

            <a href="{{ route('jobseeker.applications', ['status' => 'hired']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $filter === 'hired' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.004 0H9.496m5.004 0a3 3 0 0 0 2.25-2.906l.75-5.25a2.25 2.25 0 0 0-2.25-2.569h-6.5a2.25 2.25 0 0 0-2.25 2.569l.75 5.25a3 3 0 0 0 2.25 2.906"/></svg>
                <span>Hired</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'hired' ? 'bg-slate-700 text-white' : 'bg-slate-100 text-slate-700' }}">
                    {{ $counts['hired'] }}
                </span>
            </a>

            <a href="{{ route('jobseeker.applications', ['status' => 'rejected']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $filter === 'rejected' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                <svg class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <span>Not Qualified</span>
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
                                <svg class="w-3.5 h-3.5 text-emerald-600 mr-1 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"/></svg> {{ $match['percentage'] ?? 0 }}% Skill Match
                            </span>

                            @if($app->status === 'pending')
                                <span class="inline-flex items-center rounded-xl bg-amber-50 text-amber-900 border border-amber-200 px-3.5 py-1.5 text-xs font-extrabold">
                                    <svg class="w-3.5 h-3.5 text-amber-600 mr-1.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Pending Review
                                </span>
                            @elseif($app->status === 'reviewed')
                                <span class="inline-flex items-center rounded-xl bg-emerald-50 text-emerald-900 border border-emerald-200 px-3.5 py-1.5 text-xs font-extrabold">
                                    <svg class="w-3.5 h-3.5 text-blue-600 mr-1.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg> Under Evaluation
                                </span>
                            @elseif($app->status === 'interview')
                                <span class="inline-flex items-center rounded-xl bg-emerald-100 text-emerald-900 border border-emerald-300 px-3.5 py-1.5 text-xs font-extrabold">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 mr-1.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg> Interview Stage
                                </span>
                            @elseif($app->status === 'offered')
                                <span class="inline-flex items-center rounded-xl bg-blue-600 text-white px-3.5 py-1.5 text-xs font-black shadow-md shadow-blue-500/30 animate-pulse">
                                    <svg class="w-4 h-4 mr-1.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H4.5a1.5 1.5 0 0 1-1.5-1.5v-8.25M21 11.25H3m18 0a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 11.25m18 0V7.5a2.25 2.25 0 0 0-2.25-2.25H16.5m-9 0H5.25A2.25 2.25 0 0 0 3 7.5v3.75m13.5-3.75a2.25 2.25 0 0 0-2.25-2.25H12m0 0H9.75A2.25 2.25 0 0 0 7.5 7.5m4.5-2.25V21"/></svg> Job Offer Received!
                                </span>
                            @elseif($app->status === 'declined')
                                <span class="inline-flex items-center rounded-xl bg-slate-100 text-slate-700 border border-slate-300 px-3.5 py-1.5 text-xs font-extrabold">
                                    <svg class="w-3.5 h-3.5 text-slate-500 mr-1.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg> Offer Declined
                                </span>
                            @elseif($app->status === 'withdrawn')
                                <span class="inline-flex items-center rounded-xl bg-slate-100 text-slate-500 border border-slate-200 px-3.5 py-1.5 text-xs font-semibold">
                                    <svg class="w-3.5 h-3.5 text-slate-400 mr-1.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/></svg> Withdrawn
                                </span>
                            @elseif($app->status === 'hired')
                                @if($app->resignation_status === 'requested')
                                    <span class="inline-flex items-center rounded-xl bg-amber-50 text-amber-900 border border-amber-300 px-3.5 py-1.5 text-xs font-extrabold animate-pulse">
                                        <svg class="w-3.5 h-3.5 text-amber-600 mr-1.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg> Resignation Pending Approval
                                    </span>
                                @elseif($app->resignation_status === 'approved')
                                    <span class="inline-flex items-center rounded-xl bg-slate-100 text-slate-700 border border-slate-300 px-3.5 py-1.5 text-xs font-extrabold">
                                        <svg class="w-3.5 h-3.5 text-slate-600 mr-1.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z"/></svg> Resigned / Released
                                    </span>
                                @elseif($app->resignation_status === 'rejected')
                                    <span class="inline-flex items-center rounded-xl bg-rose-50 text-rose-900 border border-rose-300 px-3.5 py-1.5 text-xs font-extrabold">
                                        <svg class="w-3.5 h-3.5 text-rose-600 mr-1.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.004 0H9.496m5.004 0a3 3 0 0 0 2.25-2.906l.75-5.25a2.25 2.25 0 0 0-2.25-2.569h-6.5a2.25 2.25 0 0 0-2.25 2.569l.75 5.25a3 3 0 0 0 2.25 2.906"/></svg> Hired (Resignation Declined)
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-xl bg-emerald-600 text-white px-3.5 py-1.5 text-xs font-extrabold shadow-sm">
                                        <svg class="w-3.5 h-3.5 text-white mr-1.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.004 0H9.496m5.004 0a3 3 0 0 0 2.25-2.906l.75-5.25a2.25 2.25 0 0 0-2.25-2.569h-6.5a2.25 2.25 0 0 0-2.25 2.569l.75 5.25a3 3 0 0 0 2.25 2.906"/></svg> Hired & Employed
                                    </span>
                                @endif
                            @elseif($app->status === 'rejected')
                                <span class="inline-flex items-center rounded-xl bg-rose-100 text-rose-900 border border-rose-200 px-3.5 py-1.5 text-xs font-extrabold">
                                    <svg class="w-3.5 h-3.5 text-rose-600 mr-1.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg> Not Qualified
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Progress Stepper Pipeline in green & Indigo Theme -->
                    <div class="py-2">
                        <div class="relative">
                            <div class="overflow-hidden h-2.5 mb-4 text-xs flex rounded-full bg-slate-100">
                                <div style="width: {{ $step === 1 ? '20%' : ($step === 2 ? '40%' : ($step === 3 ? '60%' : ($step === 4 ? '80%' : '100%'))) }}" 
                                     class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $app->status === 'rejected' ? 'bg-rose-500' : ($app->status === 'offered' ? 'bg-gradient-to-r from-blue-500 to-indigo-600' : 'bg-gradient-to-r from-emerald-500 via-emerald-500 to-emerald-600') }} transition-all duration-500">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-5 text-center text-xs font-bold text-slate-500">
                                <div class="{{ $step >= 1 ? 'text-emerald-700' : '' }}">
                                    1. Submitted
                                </div>
                                <div class="{{ $step >= 2 ? 'text-emerald-700' : '' }}">
                                    2. Review
                                </div>
                                <div class="{{ $step >= 3 ? 'text-emerald-700' : '' }}">
                                    3. Interview
                                </div>
                                <div class="{{ $step >= 4 ? ($app->status === 'offered' ? 'text-blue-700 font-black' : 'text-emerald-700') : '' }}">
                                    4. Job Offer
                                </div>
                                <div class="{{ $step >= 5 ? ($app->status === 'rejected' ? 'text-rose-600 font-bold' : ($app->status === 'declined' ? 'text-slate-600 font-bold' : 'text-emerald-700 font-black')) : '' }}">
                                    {{ $app->status === 'rejected' ? '5. Not Qualified' : ($app->status === 'hired' ? '5. Hired!' : ($app->status === 'declined' ? '5. Declined' : '5. Employed')) }}
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

                    <!-- Job Offer Banner (Option C) -->
                    @if($app->status === 'offered')
                        <div class="rounded-3xl bg-gradient-to-br from-blue-500 via-indigo-600 to-emerald-600 p-1 shadow-xl animate-in fade-in">
                            <div class="rounded-[22px] bg-white p-6 sm:p-7 space-y-5">
                                
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                                    <div class="flex items-center gap-3.5">
                                        <div class="h-12 w-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center text-white shadow-md shadow-blue-500/30 shrink-0">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H4.5a1.5 1.5 0 0 1-1.5-1.5v-8.25M21 11.25H3m18 0a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 11.25m18 0V7.5a2.25 2.25 0 0 0-2.25-2.25H16.5m-9 0H5.25A2.25 2.25 0 0 0 3 7.5v3.75m13.5-3.75a2.25 2.25 0 0 0-2.25-2.25H12m0 0H9.75A2.25 2.25 0 0 0 7.5 7.5m4.5-2.25V21"/></svg>
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
                                    <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-100 space-y-1">
                                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Target Start Date</span>
                                        <p class="text-2xl font-black text-emerald-950">
                                            {{ $app->offer_start_date ? date('M d, Y', strtotime($app->offer_start_date)) : 'To Be Arranged' }}
                                        </p>
                                    </div>
                                </div>

                                @if($app->offer_notes)
                                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                                        <span class="font-bold text-slate-900 flex items-center gap-1">
                                            <svg class="w-4 h-4 text-slate-600 shrink-0 inline-block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.502 49.188 49.188 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.081-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v5.018Z"/></svg> Employer Message & Benefits:
                                        </span>
                                        <p class="leading-relaxed whitespace-pre-line text-[11px] text-slate-800 italic">
                                            "{{ $app->offer_notes }}"
                                        </p>
                                    </div>
                                @endif

                                <!-- Decision Advisory Notice -->
                                <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-950 flex items-start gap-2">
                                    <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.516 0c.85.493 1.508 1.333 1.508 2.316V18"/></svg>
                                    <p class="leading-relaxed text-[11px]">
                                        <strong>Offer & Acceptance Policy:</strong> Clicking <strong>Accept Job Offer</strong> will confirm your employment with <strong>{{ $company }}</strong>. Your profile will automatically be tagged as <strong>Employed</strong> and any other active job applications will be automatically withdrawn. If you choose to <strong>Decline</strong>, this application will close and your other job applications will remain active.
                                    </p>
                                </div>

                                <!-- Accept & Decline Action Buttons -->
                                <div class="pt-2 flex flex-col sm:flex-row sm:items-center justify-end gap-3">
                                    <button type="button" 
                                            @click="openDecline({{ $app->application_id }}, '{{ addslashes($job ? $job->title : 'Job') }}', '{{ addslashes($company) }}')"
                                            class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-white hover:bg-rose-50 text-rose-700 font-bold text-xs border border-slate-200 hover:border-rose-300 transition-colors text-center inline-flex items-center justify-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg> Decline Offer
                                    </button>

                                    <form action="{{ route('jobseeker.applications.accept_offer', $app->application_id) }}" method="POST" class="w-full sm:w-auto">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('Congratulations! Are you sure you want to ACCEPT this job offer from {{ addslashes($company) }}?\n\nThis will confirm your hiring for this position while keeping your profile flexible for future opportunities.')"
                                                class="w-full sm:w-auto px-8 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs shadow-xl shadow-emerald-600/30 transition-all hover:scale-105 flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
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
                                    <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg> Job Offer Declined
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
                                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg> Resignation Request Submitted to Employer
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
                            <div class="rounded-2xl bg-emerald-50 border border-emerald-300 p-5 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-black uppercase tracking-wider text-emerald-950 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg> Resignation Approved by {{ $company }}
                                    </span>
                                    <span class="text-[11px] text-emerald-800 font-semibold">
                                        Approved {{ $app->resignation_approved_at ? \Carbon\Carbon::parse($app->resignation_approved_at)->format('M d, Y') : '' }}
                                    </span>
                                </div>
                                <p class="text-xs text-emerald-900 leading-relaxed">
                                    Your resignation was officially approved. Your platform profile is now set to <strong>Unemployed</strong> and you are fully authorized to apply for new vacancies!
                                </p>
                                @if($app->resignation_remarks)
                                    <div class="bg-white/90 p-2.5 rounded-xl border border-emerald-200 text-xs text-slate-700">
                                        <span class="font-bold">Employer Clearance Remarks:</span> {{ $app->resignation_remarks }}
                                    </div>
                                @endif
                            </div>
                        @elseif($app->resignation_status === 'rejected')
                            <div class="rounded-2xl bg-rose-50 border border-rose-300 p-5 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-black uppercase tracking-wider text-rose-900 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-rose-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg> Resignation Request Declined by {{ $company }}
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
                            <div class="rounded-2xl bg-emerald-50/80 border border-emerald-200 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <p class="text-xs font-bold text-emerald-950 uppercase tracking-wider flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z"/></svg> Active Employment Record
                                    </p>
                                    <p class="text-xs text-emerald-800 leading-relaxed">You are actively employed at <strong>{{ $company }}</strong>. You cannot apply for other jobs until you request and receive resignation approval from this employer.</p>
                                </div>
                                <button type="button" @click="activeCompany = '{{ addslashes($company) }}'; resignationModal = true"
                                        class="shrink-0 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-md shadow-emerald-600/20 transition-all">
                                    Request Resignation &rarr;
                                </button>
                            </div>
                        @endif
                    @endif

                    <!-- Bottom Actions -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
                        @if($job)
                            <a href="{{ route('jobseeker.jobs.show', $job->job_id) }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">
                                View Job Posting &rarr;
                            </a>
                        @else
                            <span></span>
                        @endif

                        <div class="flex items-center gap-3">
                            @if($app->status === 'hired')
                                @if($app->resignation_status === 'requested')
                                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-amber-100 text-amber-900 border border-amber-300 text-xs font-bold">
                                        <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg> Resignation Pending
                                    </span>
                                @elseif($app->resignation_status !== 'approved')
                                    <button type="button" @click="activeCompany = '{{ addslashes($company) }}'; resignationModal = true"
                                            class="px-4 py-2 rounded-xl border border-emerald-300 bg-emerald-50 hover:bg-emerald-100 text-emerald-900 text-xs font-bold transition-colors">
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
                    <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 mx-auto flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/></svg>
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
                    <p class="font-extrabold flex items-center gap-1.5 text-amber-900">
                        <svg class="w-4 h-4 text-amber-700 shrink-0 inline-block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>
                        <span>Policy Notice on Resignation:</span>
                    </p>
                    <p class="leading-relaxed">
                        Under TrabaGo policy, employed candidates cannot apply to other jobs until their current employer approves resignation. Submitting this request sends a formal resignation notice to your employer. Once your employer confirms and approves your resignation, your status will automatically revert to <strong>Unemployed</strong>, enabling you to apply for new jobs.
                    </p>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Reason for Resignation *</label>
                    <textarea name="reason" rows="4" required placeholder="Please state the reason for requesting resignation (e.g., career transition, relocated, personal reasons)..."
                              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400"></textarea>
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
                    <p class="font-extrabold flex items-center gap-1.5 text-rose-900">
                        <svg class="w-4 h-4 text-rose-700 shrink-0 inline-block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                        <span>Are you sure you want to decline this offer?</span>
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
