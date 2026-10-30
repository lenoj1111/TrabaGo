@extends('layouts.jobseeker')

@section('title', 'My Applications - TrabaGo Pipeline')

@section('content')
<div x-data="{
    viewModal: false,
    selectedApp: null,
    resignationModal: false,
    activeCompany: '',
    declineModal: false,
    declineAppId: null,
    declineJobTitle: '',
    declineCompany: '',
    openView(app) {
        this.selectedApp = app;
        this.viewModal = true;
    },
    openDecline(id, title, company) {
        this.declineAppId = id;
        this.declineJobTitle = title;
        this.declineCompany = company;
        this.declineModal = true;
    }
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="w-full max-w-[1680px] mx-auto space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800 border border-emerald-200">
                    <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                    Application Lifecycle Tracker
                </span>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight mt-1">My Job Applications</h1>
                <p class="text-sm text-slate-500">Track your recruitment pipeline — each application is a step forward.</p>
            </div>
            <a href="{{ route('jobseeker.jobs') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 px-5 py-2.5 text-xs font-bold text-white shadow-sm transition-all">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Find More Jobs
            </a>
        </div>

        @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center gap-2 shadow-xs">
            <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg> {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-bold flex items-center gap-2 shadow-xs">
            <svg class="w-4 h-4 text-rose-700 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg> {{ session('error') }}
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
                <span>Job Offers</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'offered' ? 'bg-blue-800 text-white' : 'bg-blue-100 text-blue-800' }} font-bold">
                    {{ $counts['offered'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('jobseeker.applications', ['status' => 'pending']) }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $filter === 'pending' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                <span>Pending</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'pending' ? 'bg-emerald-800 text-white' : 'bg-emerald-50 text-emerald-800' }} font-bold">
                    {{ $counts['pending'] }}
                </span>
            </a>

            <a href="{{ route('jobseeker.applications', ['status' => 'interview']) }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $filter === 'interview' ? 'bg-emerald-700 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                <span>Interview</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'interview' ? 'bg-emerald-900 text-white' : 'bg-emerald-50 text-emerald-800' }}">
                    {{ $counts['interview'] }}
                </span>
            </a>

            <a href="{{ route('jobseeker.applications', ['status' => 'hired']) }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $filter === 'hired' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                <span>Hired</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'hired' ? 'bg-slate-700 text-white' : 'bg-slate-100 text-slate-700' }}">
                    {{ $counts['hired'] }}
                </span>
            </a>

            <a href="{{ route('jobseeker.applications', ['status' => 'rejected']) }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $filter === 'rejected' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                <span>Not Qualified</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'rejected' ? 'bg-rose-800 text-white' : 'bg-rose-50 text-rose-800' }} font-bold">
                    {{ $counts['rejected'] }}
                </span>
            </a>
        </div>

        <!-- ======= APPLICATION SQUARE GRID ======= -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-5">
            @forelse ($applications as $app)
            @php
            $job = $app->jobPosting;
            $company = $job && $job->employer ? $job->employer->company_name : 'Partner Employer';
            $match = $app->match_details ?? ['percentage' => 85, 'tier' => 'High Match'];
            $appData = [
            // application
            'id' => $app->application_id,
            'status' => $app->status,
            'resignation_status' => $app->resignation_status,
            'applied_date' => $app->created_at ? $app->created_at->format('M d, Y') : 'Recently',
            'interview_schedule' => $app->interview_schedule,
            'interview_mode' => $app->interview_mode,
            'interview_location' => $app->interview_location,
            'offer_salary' => $app->offer_salary,
            'offer_start_date' => $app->offer_start_date,
            'offer_notes' => $app->offer_notes,
            'decline_reason' => $app->decline_reason,
            'jpo_notes' => $app->jpo_notes,
            'resignation_reason' => $app->resignation_reason,
            'resignation_remarks' => $app->resignation_remarks,
            'match' => $match['percentage'] ?? 0,
            // job posting details
            'title' => $job ? $job->title : 'Job Position',
            'company' => $company,
            'job_description' => $job->description ?? null,
            'job_requirements' => $job->requirements ?? null,
            'job_responsibilities' => $job->responsibilities ?? null,
            'job_location' => $job->location ?? null,
            'job_type' => $job->employment_type ?? $job->job_type ?? null,
            'job_category' => $job->category ?? null,
            'job_salary_min' => $job->salary_min ?? null,
            'job_salary_max' => $job->salary_max ?? null,
            'job_salary_currency' => $job->salary_currency ?? '₱',
            'job_vacancies' => $job->vacancies ?? null,
            'job_deadline' => $job->deadline ?? $job->application_deadline ?? null,
            'job_company_logo' => $job->employer->company_logo ?? null,
            'job_company_industry' => $job->employer->industry ?? null,
            'job_company_location' => $job->employer->company_address ?? null,
            'job_id' => $job->job_id ?? null,
            ];
            @endphp

            <div class="group relative aspect-square rounded-2xl bg-white border border-slate-200 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col p-4 overflow-hidden">

                @if($app->status === 'offered')
                <div class="absolute top-0 right-0 w-16 h-16 overflow-hidden">
                    <div class="absolute top-0 right-0 bg-blue-500 text-white text-[9px] font-black uppercase py-0.5 px-6 rotate-45 translate-x-4 -translate-y-2 shadow-sm">Offer</div>
                </div>
                @endif

                <div class="flex-1 flex flex-col min-h-0">
                    <h3 class="text-sm font-black text-slate-900 leading-tight line-clamp-2" title="{{ $job ? $job->title : 'Job Position' }}">
                        {{ $job ? $job->title : 'Job Position' }}
                    </h3>
                    <p class="text-[11px] font-semibold text-slate-500 mt-1 truncate">{{ $company }}</p>

                    <div class="mt-2 flex flex-wrap gap-1">
                        <span class="inline-flex items-center text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded-md border border-emerald-200">
                            {{ $match['percentage'] ?? 0 }}% Match
                        </span>
                        @if($job && ($job->location ?? null))
                        <span class="inline-flex items-center text-[10px] font-semibold text-slate-600 bg-slate-50 px-1.5 py-0.5 rounded-md border border-slate-200 truncate max-w-[100px]">
                            {{ $job->location }}
                        </span>
                        @endif
                    </div>

                    <p class="text-[10px] text-slate-400 mt-2 font-medium">
                        Applied {{ $app->created_at ? $app->created_at->format('M d, Y') : 'Recently' }}
                    </p>
                </div>

                <div class="mt-2">
                    @if($app->status === 'pending')
                    <span class="inline-flex items-center rounded-md bg-amber-50 text-amber-900 border border-amber-200 px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-wide">Pending Review</span>
                    @elseif($app->status === 'reviewed')
                    <span class="inline-flex items-center rounded-md bg-emerald-50 text-emerald-900 border border-emerald-200 px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-wide">Under Evaluation</span>
                    @elseif($app->status === 'interview')
                    <span class="inline-flex items-center rounded-md bg-emerald-100 text-emerald-900 border border-emerald-300 px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-wide">Interview Stage</span>
                    @elseif($app->status === 'offered')
                    <span class="inline-flex items-center rounded-md bg-blue-600 text-white px-2 py-0.5 text-[9px] font-black uppercase tracking-wide shadow-sm">Offer Received</span>
                    @elseif($app->status === 'declined')
                    <span class="inline-flex items-center rounded-md bg-slate-100 text-slate-700 border border-slate-300 px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-wide">Offer Declined</span>
                    @elseif($app->status === 'withdrawn')
                    <span class="inline-flex items-center rounded-md bg-slate-100 text-slate-500 border border-slate-200 px-2 py-0.5 text-[9px] font-semibold uppercase tracking-wide">Withdrawn</span>
                    @elseif($app->status === 'hired')
                    @if($app->resignation_status === 'requested')
                    <span class="inline-flex items-center rounded-md bg-amber-50 text-amber-900 border border-amber-300 px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-wide animate-pulse">Resignation Pending</span>
                    @elseif($app->resignation_status === 'approved')
                    <span class="inline-flex items-center rounded-md bg-slate-100 text-slate-700 border border-slate-300 px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-wide">Resigned / Released</span>
                    @elseif($app->resignation_status === 'rejected')
                    <span class="inline-flex items-center rounded-md bg-rose-50 text-rose-900 border border-rose-300 px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-wide">Resignation Declined</span>
                    @else
                    <span class="inline-flex items-center rounded-md bg-emerald-600 text-white px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-wide shadow-sm">Hired & Employed</span>
                    @endif
                    @elseif($app->status === 'training_recommended')
                    <span class="inline-flex items-center rounded-lg bg-blue-100 text-blue-900 border border-blue-300 px-2.5 py-1 text-[11px] font-extrabold">Training Recommended</span>
                    @elseif($app->status === 'rejected')
                    <span class="inline-flex items-center rounded-lg bg-rose-100 text-rose-900 border border-rose-200 px-2.5 py-1 text-[11px] font-extrabold">Not Qualified</span>
                    @endif
                </div>

                <div class="mt-3 flex items-center justify-between gap-1 pt-2 border-t border-slate-100">
                    <button type="button" @click="openView({{ json_encode($appData) }})"
                        class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-[10px] font-bold transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View
                    </button>

                    <div class="flex items-center gap-1">
                        @if($app->status === 'offered')
                        <form action="{{ route('jobseeker.applications.accept_offer', $app->application_id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" onclick="return confirm('Are you sure you want to ACCEPT this job offer?')" class="px-2 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-black shadow-sm transition-colors">
                                Accept
                            </button>
                        </form>
                        @elseif($app->status === 'hired' && $app->resignation_status !== 'requested' && $app->resignation_status !== 'approved')
                        <button type="button" @click="activeCompany = '{{ addslashes($company) }}'; resignationModal = true" class="px-2 py-1 rounded-lg bg-white border border-emerald-300 hover:bg-emerald-50 text-emerald-800 text-[10px] font-bold transition-colors">
                            Resign
                        </button>
                        @elseif($app->status === 'rejected' || ($app->status === 'hired' && $app->resignation_status === 'approved'))
                        <a href="{{ route('jobseeker.jobs.show', $appData['job_id']) }}"
                            class="px-2 py-1 rounded-lg bg-emerald-50 border border-emerald-300 hover:bg-emerald-100 text-emerald-800 text-[10px] font-bold transition-colors flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Re-apply
                        </a>
                        @elseif(!in_array($app->status, ['rejected', 'offered', 'declined', 'withdrawn', 'hired', 'training_recommended']))
                        <form action="{{ route('jobseeker.applications.withdraw', $app->application_id) }}" method="POST" onsubmit="return confirm('Withdraw this application?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-2 py-1 rounded-lg text-rose-600 hover:bg-rose-50 text-[10px] font-bold border border-slate-200 transition-colors">
                                Withdraw
                            </button>
                        </form>
                        @elseif($app->status === 'training_recommended')
                        <a href="{{ route('jobseeker.training') }}" class="px-2 py-1 rounded-lg bg-blue-50 border border-blue-300 hover:bg-blue-100 text-blue-800 text-[10px] font-bold transition-colors">
                            View Trainings
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full flex items-center justify-center py-20">
                <div class="text-center text-slate-400 italic text-sm">No applications found matching this filter.</div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="pt-4">
            {{ $applications->links() }}
        </div>

    </div>

    <!-- ======= VIEW MODAL (job posting details + status) ======= -->
    <div x-show="viewModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="viewModal = false" class="bg-white rounded-3xl max-w-3xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6 max-h-[90vh] overflow-y-auto custom-scroll">
            <template x-if="selectedApp">
                <div class="space-y-6">
                    <!-- Header -->
                    <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                        <div class="flex items-start gap-3">
                            <template x-if="selectedApp.job_company_logo">
                                <img :src="selectedApp.job_company_logo" alt="logo" class="h-12 w-12 rounded-xl object-cover border border-slate-200" />
                            </template>
                            <div>
                                <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider" x-text="selectedApp.company"></span>
                                <h3 class="text-2xl font-black text-slate-900 mt-1" x-text="selectedApp.title"></h3>
                                <p class="text-xs text-slate-500 mt-1">Applied on <span x-text="selectedApp.applied_date"></span></p>
                            </div>
                        </div>
                        <button @click="viewModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
                    </div>

                    <!-- Job meta pills -->
                    <div class="flex flex-wrap gap-2">
                        <template x-if="selectedApp.job_location">
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-700 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-200">
                                📍 <span x-text="selectedApp.job_location"></span>
                            </span>
                        </template>
                        <template x-if="selectedApp.job_type">
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-700 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-200">
                                💼 <span x-text="selectedApp.job_type"></span>
                            </span>
                        </template>
                        <template x-if="selectedApp.job_category">
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-700 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-200">
                                🏷️ <span x-text="selectedApp.job_category"></span>
                            </span>
                        </template>
                        <template x-if="selectedApp.job_salary_min || selectedApp.job_salary_max">
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                                💰 <span x-text="(selectedApp.job_salary_currency || '₱') + (selectedApp.job_salary_min ? parseInt(selectedApp.job_salary_min).toLocaleString() : '0') + ' – ' + (selectedApp.job_salary_max ? parseInt(selectedApp.job_salary_max).toLocaleString() : 'Negotiable')"></span>
                            </span>
                        </template>
                        <template x-if="selectedApp.job_vacancies">
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-700 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-200">
                                👥 <span x-text="selectedApp.job_vacancies + ' vacancy(ies)'"></span>
                            </span>
                        </template>
                        <template x-if="selectedApp.job_deadline">
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-700 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200">
                                ⏰ Deadline: <span x-text="new Date(selectedApp.job_deadline).toLocaleDateString()"></span>
                            </span>
                        </template>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                            ✓ <span x-text="selectedApp.match + '% Match'"></span>
                        </span>
                    </div>

                    <!-- Application status pipeline -->
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-2">Your Application Status</p>
                        <div class="flex items-center justify-between text-[10px] font-bold text-slate-400 mb-2">
                            <span :class="['pending', 'reviewed', 'interview', 'offered', 'hired', 'rejected', 'declined'].includes(selectedApp.status) ? 'text-emerald-600' : ''">Submitted</span>
                            <span :class="['reviewed', 'interview', 'offered', 'hired'].includes(selectedApp.status) ? 'text-emerald-600' : ''">Review</span>
                            <span :class="['interview', 'offered', 'hired'].includes(selectedApp.status) ? 'text-emerald-600' : ''">Interview</span>
                            <span :class="['offered', 'hired'].includes(selectedApp.status) ? 'text-blue-600' : ''">Offer</span>
                            <span :class="['hired'].includes(selectedApp.status) ? 'text-emerald-600' : (selectedApp.status === 'rejected' ? 'text-rose-600' : '')">
                                <span x-text="selectedApp.status === 'rejected' ? 'Not Qualified' : (selectedApp.status === 'hired' ? 'Hired' : 'Employed')"></span>
                            </span>
                        </div>
                        <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden flex">
                            <div class="h-full bg-emerald-500 transition-all duration-500" :style="`width: ${['pending', 'reviewed', 'interview', 'offered', 'hired', 'rejected'].includes(selectedApp.status) ? (selectedApp.status === 'pending' ? '20%' : selectedApp.status === 'reviewed' ? '40%' : selectedApp.status === 'interview' ? '60%' : selectedApp.status === 'offered' ? '80%' : '100%') : '0%'}`"></div>
                        </div>
                    </div>

                    <!-- Dynamic status blocks (interview, offer, hired, rejected, declined) -->
                    <template x-if="selectedApp.status === 'interview'">
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 space-y-2">
                            <p class="text-xs font-bold text-emerald-900 uppercase">Interview Scheduled</p>
                            <p class="text-sm font-bold text-slate-900" x-text="selectedApp.interview_schedule ? new Date(selectedApp.interview_schedule).toLocaleString() : 'To be announced'"></p>
                            <p class="text-xs text-slate-700">Mode: <span x-text="selectedApp.interview_mode || '—'"></span></p>
                            <p class="text-xs text-slate-700">Location: <span x-text="selectedApp.interview_location || '—'"></span></p>
                        </div>
                    </template>

                    <template x-if="selectedApp.status === 'offered'">
                        <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 space-y-3">
                            <p class="text-xs font-bold text-blue-900 uppercase">Formal Job Offer</p>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <span class="text-[10px] text-slate-500 uppercase">Salary</span>
                                    <p class="text-sm font-black text-slate-900" x-text="selectedApp.offer_salary ? '₱' + parseFloat(selectedApp.offer_salary).toLocaleString() : 'Negotiable'"></p>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-500 uppercase">Start Date</span>
                                    <p class="text-sm font-black text-slate-900" x-text="selectedApp.offer_start_date ? new Date(selectedApp.offer_start_date).toLocaleDateString() : 'To be arranged'"></p>
                                </div>
                            </div>
                            <div x-show="selectedApp.offer_notes" class="pt-2 border-t border-blue-200">
                                <span class="text-[10px] text-blue-700 font-bold uppercase">Message:</span>
                                <p class="text-xs text-slate-700 italic" x-text="'&quot;' + selectedApp.offer_notes + '&quot;'"></p>
                            </div>
                        </div>
                    </template>

                    <template x-if="selectedApp.status === 'hired'">
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 space-y-2">
                            <p class="text-xs font-bold text-emerald-900 uppercase">Employment Status</p>
                            <p class="text-sm text-slate-800">
                                <span class="font-bold">Current State:</span>
                                <span x-text="selectedApp.resignation_status === 'requested' ? 'Resignation Pending' : (selectedApp.resignation_status === 'approved' ? 'Resigned / Released' : (selectedApp.resignation_status === 'rejected' ? 'Resignation Declined' : 'Actively Employed'))"></span>
                            </p>
                            <template x-if="selectedApp.resignation_status === 'requested'">
                                <div class="mt-2 text-xs text-slate-600">
                                    <p><span class="font-bold">Reason:</span> <span x-text="selectedApp.resignation_reason"></span></p>
                                </div>
                            </template>
                            <template x-if="selectedApp.resignation_status === 'approved' && selectedApp.resignation_remarks">
                                <div class="mt-2 text-xs text-slate-600">
                                    <p><span class="font-bold">Employer Remarks:</span> <span x-text="selectedApp.resignation_remarks"></span></p>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="selectedApp.status === 'training_recommended'">
                        <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 space-y-2">
                            <p class="text-xs font-bold text-blue-900 uppercase">Training Recommended by JPO</p>
                            <p class="text-sm text-slate-800">The Job Placement Officer recommends you take upskilling / training courses before reapplying for this position.</p>
                            <div class="bg-white/90 p-2.5 rounded-lg border border-blue-200 text-xs text-slate-700 mt-2">
                                <span class="font-bold text-blue-900">JPO Remarks:</span>
                                <span x-text="selectedApp.jpo_notes || 'Please complete relevant training courses to be eligible for this role.'"></span>
                            </div>
                            <a href="{{ route('jobseeker.training') }}" class="inline-flex items-center gap-1.5 mt-2 text-xs font-bold text-blue-700 hover:underline">
                                View Available Training Courses &rarr;
                            </a>
                        </div>
                    </template>
                    <template x-if="selectedApp.status === 'rejected'">
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 space-y-2">
                            <p class="text-xs font-bold text-rose-900 uppercase">JPO Evaluation Feedback</p>
                            <p class="text-xs text-slate-800" x-text="selectedApp.jpo_notes || 'Does not meet the minimum required qualifications for this position.'"></p>
                        </div>
                    </template>

                    <template x-if="selectedApp.status === 'declined'">
                        <div class="p-4 rounded-2xl bg-slate-100 border border-slate-300 space-y-2">
                            <p class="text-xs font-bold text-slate-800 uppercase">Offer Declined</p>
                            <p class="text-xs text-slate-600" x-text="selectedApp.decline_reason ? 'Reason: ' + selectedApp.decline_reason : 'You previously declined this offer.'"></p>
                        </div>
                    </template>

                    <!-- ===== JOB POSTING DETAILS ===== -->
                    <div class="border-t border-slate-100 pt-5 space-y-5">
                        <p class="text-xs font-black uppercase tracking-wider text-slate-500">Job Posting Details</p>

                        <template x-if="selectedApp.job_description">
                            <div>
                                <p class="text-[11px] font-bold text-slate-700 uppercase mb-1">Description</p>
                                <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line" x-text="selectedApp.job_description"></p>
                            </div>
                        </template>

                        <template x-if="selectedApp.job_responsibilities">
                            <div>
                                <p class="text-[11px] font-bold text-slate-700 uppercase mb-1">Responsibilities</p>
                                <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line" x-text="selectedApp.job_responsibilities"></p>
                            </div>
                        </template>

                        <template x-if="selectedApp.job_requirements">
                            <div>
                                <p class="text-[11px] font-bold text-slate-700 uppercase mb-1">Requirements</p>
                                <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line" x-text="selectedApp.job_requirements"></p>
                            </div>
                        </template>

                        <template x-if="selectedApp.job_company_industry || selectedApp.job_company_location">
                            <div>
                                <p class="text-[11px] font-bold text-slate-700 uppercase mb-1">About the Company</p>
                                <p class="text-xs text-slate-600">
                                    <template x-if="selectedApp.job_company_industry">
                                        <span x-text="'Industry: ' + selectedApp.job_company_industry"></span>
                                    </template>
                                    <template x-if="selectedApp.job_company_location">
                                        <span x-text="' • Location: ' + selectedApp.job_company_location"></span>
                                    </template>
                                </p>
                            </div>
                        </template>
                    </div>

                    <!-- Footer -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="viewModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                            Close
                        </button>
                    </div>
                </div>
            </template>
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
                @csrf <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-950 space-y-1.5">
                    <p class="font-extrabold flex items-center gap-1.5 text-amber-900">
                        <svg class="w-4 h-4 text-amber-700 shrink-0 inline-block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                        </svg>
                        <span>Policy Notice on Resignation:</span>
                    </p>
                    <p class="leading-relaxed">
                        Once your employer confirms and approves your resignation, your status will automatically revert to <strong>Unemployed</strong>, enabling you to apply for new jobs.
                    </p>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Reason for Resignation *</label>
                    <textarea name="reason" rows="4" required placeholder="Please state the reason..." class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400"></textarea>
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

</div>
@endsection