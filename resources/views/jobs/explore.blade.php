@extends('layouts.public')

@section('title', 'Explore Jobs - DMDP Cebu City | TrabaGo')

@section('content')
<div x-data="{
    detailsModalOpen: false,
    selectedJob: null,
    authModalOpen: false,
    authJobTitle: '',
    authJobCompany: '',
    authJobId: '',
    applyModalOpen: false,
    applyJobId: '',
    applyJobTitle: '',
    applyJobCompany: '',
    getBulletList(text) {
        if (!text) return [];
        return text.split(/\r?\n/).map(s => s.trim()).filter(s => s.length > 0);
    },
    openDetails(job) {
        this.selectedJob = job;
        this.detailsModalOpen = true;
    },
    closeDetails() {
        this.detailsModalOpen = false;
        this.selectedJob = null;
    },
    handleApply(job) {
        @guest
            this.authJobTitle = job.title;
            this.authJobCompany = job.employer ? job.employer.company_name : 'Partner Employer';
            this.authJobId = job.job_id;
            this.authModalOpen = true;
            this.detailsModalOpen = false;
        @else
            @if(Auth::user()->role === 'jobseeker')
                @if(!Auth::user()->is_approved)
                    alert('Your jobseeker account is pending approval by DMDP. You cannot apply for job openings until your account is approved.');
                    return;
                @endif
                this.applyJobId = job.job_id;
                this.applyJobTitle = job.title;
                this.applyJobCompany = job.employer ? job.employer.company_name : 'Partner Employer';
                this.applyModalOpen = true;
                this.detailsModalOpen = false;
            @else
                alert('You are currently signed in as an {{ ucfirst(Auth::user()->role) }}. Please sign in with a Jobseeker account to apply.');
            @endif
        @endguest
    },
    closeAuthModal() { this.authModalOpen = false; },
    closeApplyModal() { this.applyModalOpen = false; },
    init() {
        @if(!empty($selectedJobId))
            const targetId = {{ $selectedJobId }};
            const jobsList = @json($jobs->items());
            const found = jobsList.find(j => j.job_id === targetId);
            if (found) {
                this.openDetails(found);
            }
        @endif
    }
}" 
@keydown.escape.window="closeDetails(); closeAuthModal(); closeApplyModal();"
class="min-h-screen bg-gradient-to-b from-slate-50 via-emerald-50/20 to-slate-50 py-8 md:py-14">

    <div class="max-w-5xl mx-auto px-4 sm:px-6">

        <!-- Hero Search Header Card -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-950 via-emerald-900 to-slate-950 text-white shadow-xl shadow-emerald-950/20 p-6 sm:p-8 md:p-10 mb-8 border border-emerald-800/40">
            <!-- Decorative Glow Accents -->
            <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>
            <div class="absolute left-1/2 -bottom-16 w-56 h-56 rounded-full bg-emerald-400/10 blur-3xl pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-400/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        DOLE & DMDP Cebu City Verified
                    </span>
                    <span class="text-xs font-medium text-emerald-200/70 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Official Public Job Directory
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight text-white">
                    Explore Verified Jobs in <span class="text-emerald-400">Cebu City</span>
                </h1>
                <p class="text-emerald-100/80 text-sm sm:text-base mt-2 max-w-2xl leading-relaxed">
                    Search openings submitted by accredited Cebu City employers, pre-screened by the Public Employment Service Office.
                </p>

                <!-- Search & Filter Form -->
                <form action="{{ route('jobs.index') }}" method="GET" class="mt-7 pt-5 border-t border-emerald-800/60 space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5">
                        <!-- Keyword input -->
                        <div class="sm:col-span-5 relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-300/70">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Job title, technical skill, company..." 
                                   class="w-full pl-10 pr-3 py-2.5 rounded-xl bg-emerald-950/70 border border-emerald-700/70 text-white placeholder-emerald-300/50 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 transition" />
                        </div>

                        <!-- Location input -->
                        <div class="sm:col-span-4 relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-300/70">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <input type="text" name="location" value="{{ request('location') }}" placeholder="Location (e.g. IT Park, Lahug)" 
                                   class="w-full pl-10 pr-3 py-2.5 rounded-xl bg-emerald-950/70 border border-emerald-700/70 text-white placeholder-emerald-300/50 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 transition" />
                        </div>

                        <!-- Buttons -->
                        <div class="sm:col-span-3 flex items-center gap-2">
                            <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs sm:text-sm transition-all shadow-md shadow-emerald-500/25 active:scale-95 flex items-center justify-center gap-1.5">
                                <span>Find Jobs</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                            @if(request()->hasAny(['q', 'location', 'pwd_only', 'filter', 'sort']))
                                <a href="{{ route('jobs.index') }}" title="Reset filters" class="px-3 py-2.5 rounded-xl bg-emerald-900/60 hover:bg-emerald-800 text-emerald-200 text-xs font-semibold flex items-center justify-center transition shrink-0">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Filter Chips -->
                    <div class="flex flex-wrap items-center gap-2 pt-2 text-xs">
                        <span class="text-emerald-300/70 font-semibold text-[11px] uppercase tracking-wider">Quick Filters:</span>
                        <a href="{{ route('jobs.index', array_filter(['q' => request('q'), 'location' => request('location'), 'sort' => request('sort')])) }}" 
                           class="px-3 py-1 rounded-full font-semibold transition-all duration-150 {{ !request('pwd_only') && !request('filter') ? 'bg-white text-emerald-950 shadow-xs' : 'bg-emerald-900/50 text-emerald-200 hover:bg-emerald-800/60' }}">
                            All Openings
                        </a>
                        <a href="{{ route('jobs.index', array_merge(request()->query(), ['pwd_only' => 1])) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-semibold transition-all duration-150 {{ request('pwd_only') ? 'bg-emerald-400 text-slate-950 shadow-xs font-bold' : 'bg-emerald-900/50 text-emerald-200 hover:bg-emerald-800/60' }}">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="4" r="2"/><path d="M15.5 13.5A4.5 4.5 0 0 1 11 18a4.5 4.5 0 0 1-4.5-4.5A4.5 4.5 0 0 1 11 9c.4 0 .78.06 1.14.16L13.5 6.5C12.7 6.18 11.87 6 11 6a7.5 7.5 0 0 0-7.5 7.5A7.5 7.5 0 0 0 11 21a7.5 7.5 0 0 0 7.42-6.5h-2.92z"/><path d="M19 10h-4.5l-1.3-2.6A2 2 0 0 0 11.4 6.2L8.5 7.6 9.4 9.4l2.1-1 2 4.1V17h2v-4.5l1.6-1.6H19v-2z"/></svg>
                            <span>PWD-Inclusive</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Toolbar (Count & Sorting) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-sm font-bold text-slate-900">
                    {{ $jobs->total() }} Verified {{ Str::plural('Opening', $jobs->total()) }} Available
                </span>
            </div>
            
            <div class="flex items-center gap-2 text-xs">
                <span class="text-slate-400 font-medium">Sort by:</span>
                <a href="{{ route('jobs.index', array_merge(request()->query(), ['sort' => 'latest'])) }}" 
                   class="px-2.5 py-1 rounded-lg font-semibold transition {{ request('sort', 'latest') === 'latest' ? 'bg-emerald-100 text-emerald-800' : 'text-slate-600 hover:text-slate-900' }}">
                    Most Recent
                </a>
                <span class="text-slate-300">&bull;</span>
                <a href="{{ route('jobs.index', array_merge(request()->query(), ['sort' => 'deadline'])) }}" 
                   class="px-2.5 py-1 rounded-lg font-semibold transition {{ request('sort') === 'deadline' ? 'bg-emerald-100 text-emerald-800' : 'text-slate-600 hover:text-slate-900' }}">
                    Application Deadline
                </a>
            </div>
        </div>

        <!-- Global Alerts -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm flex items-start gap-3 shadow-xs">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <p class="font-bold">Application Submitted</p>
                    <p class="text-emerald-700 text-xs mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-sm flex items-start gap-3 shadow-xs">
                <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <p class="font-bold">Notice</p>
                    <p class="text-rose-700 text-xs mt-0.5">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Job Grid -->
        @if($jobs->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-10">
                @foreach($jobs as $job)
                    <div class="bg-white border border-slate-200/90 hover:border-emerald-500 rounded-3xl p-6 transition-all duration-200 hover:shadow-xl hover:shadow-emerald-950/5 hover:-translate-y-0.5 flex flex-col justify-between group">
                        <div>
                            <!-- Header Bar -->
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-emerald-100 to-emerald-200 text-emerald-900 border border-emerald-200/70 flex items-center justify-center font-black text-base shrink-0 shadow-xs group-hover:scale-105 transition-transform">
                                        {{ substr($job->employer->company_name ?? 'P', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-700 truncate max-w-[210px]">
                                            {{ $job->employer->company_name ?? 'Partner Employer' }}
                                        </p>
                                        @if($job->employer && $job->employer->is_accredited)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100/80">
                                                <svg class="w-3 h-3 text-emerald-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                DMDP Accredited
                                            </span>
                                        @else
                                            <span class="text-[10px] text-slate-400">Registered Partner</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-col items-end gap-1.5 shrink-0">
                                    <span class="text-[11px] font-bold text-slate-700 bg-slate-100 px-2.5 py-0.5 rounded-full">
                                        {{ $job->vacancy_count }} {{ Str::plural('Slot', $job->vacancy_count) }}
                                    </span>
                                    @if($job->accepts_disability)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-teal-800 bg-teal-50 border border-teal-200/70 px-2.5 py-0.5 rounded-full" title="{{ $job->disability_type }}">
                                            <svg class="w-3 h-3 text-teal-700 shrink-0" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="4" r="2"/><path d="M15.5 13.5A4.5 4.5 0 0 1 11 18a4.5 4.5 0 0 1-4.5-4.5A4.5 4.5 0 0 1 11 9c.4 0 .78.06 1.14.16L13.5 6.5C12.7 6.18 11.87 6 11 6a7.5 7.5 0 0 0-7.5 7.5A7.5 7.5 0 0 0 11 21a7.5 7.5 0 0 0 7.42-6.5h-2.92z"/><path d="M19 10h-4.5l-1.3-2.6A2 2 0 0 0 11.4 6.2L8.5 7.6 9.4 9.4l2.1-1 2 4.1V17h2v-4.5l1.6-1.6H19v-2z"/></svg>
                                            <span>PWD-Inclusive</span>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Title -->
                            <h3 class="text-lg font-black text-slate-900 group-hover:text-emerald-700 transition-colors mt-2">
                                {{ $job->title }}
                            </h3>

                            <!-- Description Snippet -->
                            <p class="text-slate-500 text-xs sm:text-sm mt-2 leading-relaxed line-clamp-2">
                                {{ $job->description }}
                            </p>

                            <!-- Skills Tags -->
                            @if(!empty($job->skill_tags))
                                <div class="flex flex-wrap gap-1.5 mt-3.5">
                                    @foreach(array_slice($job->skill_tags, 0, 3) as $skill)
                                        <span class="text-[11px] font-semibold text-emerald-800 bg-emerald-50 px-2.5 py-0.5 rounded-lg border border-emerald-100/80">
                                            {{ $skill }}
                                        </span>
                                    @endforeach
                                    @if(count($job->skill_tags) > 3)
                                        <span class="text-[11px] font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded-lg">
                                            +{{ count($job->skill_tags) - 3 }} more
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <div class="border-t border-slate-100 my-4"></div>
                        </div>

                        <!-- Footer Meta & Actions -->
                        <div>
                            <div class="flex items-center justify-between text-xs text-slate-400 mb-4">
                                <span class="flex items-center gap-1 font-medium text-slate-600">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    Cebu City
                                </span>
                                @if($job->valid_until)
                                    <span class="text-[11px]">Valid until: <strong class="text-slate-700">{{ $job->valid_until->format('M d, Y') }}</strong></span>
                                @else
                                    <span class="text-[11px] text-emerald-700 font-semibold">● Actively Hiring</span>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 gap-2.5">
                                <button type="button" 
                                        @click="openDetails({{ json_encode($job) }})"
                                        class="w-full py-2.5 px-3 rounded-xl border border-slate-200 hover:border-slate-300 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs sm:text-sm transition-all text-center">
                                    View Details
                                </button>
                                <button type="button" 
                                        @click="handleApply({{ json_encode($job) }})"
                                        class="w-full py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm transition-all shadow-md shadow-emerald-600/20 active:scale-95 text-center flex items-center justify-center gap-1">
                                    <span>Apply Now</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mb-12 flex justify-center">
                {{ $jobs->links() }}
            </div>
        @else
            <!-- Empty state -->
            <div class="bg-white border border-slate-200/90 rounded-3xl p-10 sm:p-14 text-center max-w-lg mx-auto my-10 shadow-xs">
                <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-3xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="text-xl font-black text-slate-900">No Vacancies Match Your Search</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-2 leading-relaxed">
                    Try adjusting your keyword, clearing location criteria, or exploring all available positions.
                </p>
                <div class="mt-6">
                    <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm transition shadow-md shadow-emerald-600/25">
                        <span>Clear All Filters</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </a>
                </div>
            </div>
        @endif

    </div>

    <!-- ======= MODAL 1: JOB DETAILS ======= -->
    <div x-show="detailsModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div x-show="detailsModalOpen" 
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="closeDetails()" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity"></div>

        <div class="flex min-h-screen items-center justify-center p-4">
            <div x-show="detailsModalOpen"
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative bg-white rounded-3xl shadow-2xl w-full max-w-2xl border border-slate-200 overflow-hidden">
                
                <template x-if="selectedJob">
                    <div>
                        <!-- Header -->
                        <div class="bg-gradient-to-br from-emerald-950 via-emerald-900 to-slate-950 p-6 sm:p-8 text-white relative">
                            <button @click="closeDetails()" type="button" class="absolute top-5 right-5 text-white/70 hover:text-white p-2 rounded-xl hover:bg-white/10 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            <div class="pr-10">
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-300 uppercase tracking-wider bg-emerald-500/20 px-2.5 py-0.5 rounded-full border border-emerald-400/20 mb-2">
                                    Job Reference #<span x-text="selectedJob.job_id"></span>
                                </span>
                                <h3 class="text-xl sm:text-2xl font-black text-white leading-tight" x-text="selectedJob.title"></h3>
                                <p class="text-emerald-200/80 text-xs sm:text-sm mt-1" x-text="selectedJob.employer ? selectedJob.employer.company_name : 'Partner Employer'"></p>
                            </div>
                        </div>

                        <!-- Body Content -->
                        <div class="p-6 sm:p-8 space-y-6 max-h-[65vh] overflow-y-auto">
                            <!-- Key Metrics Grid -->
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-3.5">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Vacancies</p>
                                    <p class="text-xs font-black text-slate-900 mt-1" x-text="selectedJob.vacancy_count + ' Slots'"></p>
                                </div>
                                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-3.5">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Location</p>
                                    <p class="text-xs font-black text-slate-900 mt-1" x-text="selectedJob.location || 'Cebu City'"></p>
                                </div>
                                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-3.5">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Employment</p>
                                    <p class="text-xs font-black text-slate-900 mt-1" x-text="selectedJob.job_type || 'Full-time'"></p>
                                </div>
                                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-3.5">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Compensation</p>
                                    <p class="text-xs font-black text-emerald-700 mt-1" x-text="selectedJob.salary_range || 'Competitive'"></p>
                                </div>
                            </div>

                            <!-- PWD Banner -->
                            <template x-if="selectedJob.accepts_disability">
                                <div class="bg-teal-50 border border-teal-200 rounded-2xl p-4 text-xs text-teal-900 flex items-start gap-3">
                                    <div class="h-8 w-8 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-teal-800" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="4" r="2"/><path d="M15.5 13.5A4.5 4.5 0 0 1 11 18a4.5 4.5 0 0 1-4.5-4.5A4.5 4.5 0 0 1 11 9c.4 0 .78.06 1.14.16L13.5 6.5C12.7 6.18 11.87 6 11 6a7.5 7.5 0 0 0-7.5 7.5A7.5 7.5 0 0 0 11 21a7.5 7.5 0 0 0 7.42-6.5h-2.92z"/><path d="M19 10h-4.5l-1.3-2.6A2 2 0 0 0 11.4 6.2L8.5 7.6 9.4 9.4l2.1-1 2 4.1V17h2v-4.5l1.6-1.6H19v-2z"/></svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-teal-950">Person with Disability (PWD) Friendly Opening</p>
                                        <p class="text-[11px] mt-0.5 text-teal-800" x-text="selectedJob.disability_type || 'Workplace accommodations and accessible facilities available.'"></p>
                                    </div>
                                </div>
                            </template>

                            <!-- Description -->
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Job Description & Responsibilities</h4>
                                <div class="bg-slate-50 rounded-2xl p-4 text-xs text-slate-700 leading-relaxed whitespace-pre-line border border-slate-100" x-text="selectedJob.description"></div>
                            </div>

                            <!-- Qualifications -->
                            <template x-if="selectedJob.qualifications">
                                <div>
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Qualifications & Minimum Criteria</h4>
                                    <div class="bg-slate-50 rounded-2xl p-4 text-xs text-slate-700 leading-relaxed border border-slate-100">
                                        <ul class="list-disc list-inside space-y-1">
                                            <template x-for="(qual, idx) in getBulletList(selectedJob.qualifications)" :key="idx">
                                                <li class="pl-1"><span x-text="qual"></span></li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            </template>

                            <!-- Skills -->
                            <template x-if="selectedJob.skills">
                                <div>
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Required Core & Technical Skills</h4>
                                    <div class="bg-slate-50 rounded-2xl p-4 text-xs text-slate-700 leading-relaxed border border-slate-100">
                                        <ul class="list-disc list-inside space-y-1">
                                            <template x-for="(skill, idx) in getBulletList(selectedJob.skills)" :key="idx">
                                                <li class="pl-1 font-semibold text-emerald-900"><span x-text="skill"></span></li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            </template>

                            <!-- Benefits -->
                            <template x-if="selectedJob.benefits">
                                <div>
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-800 mb-2">Benefits & Perks</h4>
                                    <div class="bg-emerald-50/60 border border-emerald-100 rounded-2xl p-4 text-xs text-emerald-950 leading-relaxed">
                                        <ul class="list-disc list-inside space-y-1">
                                            <template x-for="(ben, idx) in getBulletList(selectedJob.benefits)" :key="idx">
                                                <li class="pl-1"><span x-text="ben"></span></li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            </template>

                            <!-- Instructions -->
                            <template x-if="selectedJob.other_instructions">
                                <div>
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Employer Application Instructions</h4>
                                    <div class="bg-slate-50 rounded-2xl p-4 text-xs text-slate-700 leading-relaxed whitespace-pre-line border border-slate-100" x-text="selectedJob.other_instructions"></div>
                                </div>
                            </template>
                        </div>

                        <!-- Footer -->
                        <div class="bg-slate-50 border-t border-slate-200/80 p-5 sm:p-6 flex flex-col sm:flex-row items-center justify-between gap-3">
                            <button @click="closeDetails()" type="button" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-100 transition">
                                Close
                            </button>
                            <button @click="handleApply(selectedJob)" type="button" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold transition-all shadow-md shadow-emerald-600/25 active:scale-95 flex items-center justify-center gap-1.5">
                                <span>Apply for this Opening</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- ======= MODAL 2: AUTH REQUIRED ======= -->
    <div x-show="authModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div x-show="authModalOpen" 
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="closeAuthModal()" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity"></div>

        <div class="flex min-h-screen items-center justify-center p-4">
            <div x-show="authModalOpen"
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md border border-slate-200 overflow-hidden">
                
                <!-- Header -->
                <div class="bg-gradient-to-br from-emerald-950 via-emerald-900 to-slate-950 p-6 sm:p-7 text-white text-center relative">
                    <button @click="closeAuthModal()" type="button" class="absolute top-4 right-4 text-white/70 hover:text-white p-1.5 rounded-xl hover:bg-white/10 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <div class="w-14 h-14 bg-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-3 text-white font-bold text-xl shadow-lg shadow-emerald-600/30">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-white">Sign In to Submit Application</h3>
                    <p class="text-emerald-200/80 text-xs mt-1.5 max-w-xs mx-auto">
                        Applying for <strong class="text-emerald-300 font-semibold" x-text="authJobTitle"></strong> at <span x-text="authJobCompany"></span>
                    </p>
                </div>

                <!-- Body -->
                <div class="p-6 sm:p-7 space-y-4">
                    <p class="text-xs sm:text-sm text-slate-600 text-center leading-relaxed">
                        Sign in to your verified jobseeker account or register to submit your skills profile to this employer.
                    </p>
                    <div class="space-y-2.5 pt-1">
                        <a href="{{ route('login') }}" 
                           class="flex items-center justify-center gap-2 w-full py-3 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm transition shadow-md shadow-emerald-600/25 active:scale-95">
                            <span>Sign In to Account</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <a href="{{ route('jobseeker.register') }}" 
                           class="flex items-center justify-center gap-2 w-full py-3 px-5 rounded-xl border border-emerald-600 text-emerald-700 hover:bg-emerald-50 font-bold text-sm transition active:scale-95">
                            <span>Create Free Jobseeker Profile</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                    <div class="border-t border-slate-100 pt-3 text-center">
                        <p class="text-[11px] text-slate-400">100% Free Public Employment Service &bull; DMDP Cebu City</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ======= MODAL 3: JOBSEEKER APPLY ======= -->
    @auth
        @if(Auth::user()->role === 'jobseeker')
            <div x-show="applyModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                <div x-show="applyModalOpen" @click="closeApplyModal()" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity"></div>
                <div class="flex min-h-screen items-center justify-center p-4">
                    <div x-show="applyModalOpen" class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md border border-slate-200 overflow-hidden">
                        <form :action="'/jobseeker/jobs/' + applyJobId + '/apply'" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="bg-gradient-to-br from-emerald-950 via-emerald-900 to-slate-950 p-6 text-white relative">
                                <button @click="closeApplyModal()" type="button" class="absolute top-4 right-4 text-white/70 hover:text-white p-1.5 rounded-xl hover:bg-white/10 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                                <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider">Fast-Track Application</p>
                                <h3 class="text-xl font-black text-white mt-1 leading-tight" x-text="applyJobTitle"></h3>
                                <p class="text-emerald-200/80 text-xs mt-1" x-text="applyJobCompany"></p>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-100 text-xs text-emerald-900 leading-relaxed">
                                    Your verified DMDP skills profile, training certificates, and contact details will be automatically attached to this referral.
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Optional Resume Attachment</label>
                                    <input type="file" name="resume" accept=".pdf,.doc,.docx" 
                                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 file:cursor-pointer transition">
                                </div>
                                <button type="submit" class="w-full py-3 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm transition-all shadow-md shadow-emerald-600/25 active:scale-95">
                                    Confirm & Submit Application &rarr;
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endauth

</div>
@endsection
