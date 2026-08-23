@extends('layouts.jobseeker')

@section('title', 'Explore Jobs & AI Matching - TrabaGo')

@section('content')
<div x-data="{ 
    selectedJobId: '{{ $selectedItem ? $selectedItem['job_id'] : '' }}',
    applyModalOpen: false,
    selectedJobTitle: '{{ $selectedItem ? addslashes($selectedItem['job']->title) : '' }}',
    selectedJobCompany: '{{ $selectedItem ? addslashes($selectedItem['job']->employer->company_name ?? 'Partner Employer') : '' }}'
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-6">
        
        <!-- Header & Search Controls -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-8 text-white shadow-xl border border-emerald-500/20">
            <div class="max-w-2xl mb-6">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    AI Cosine-Similarity Job Explorer
                </span>
                <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold tracking-tight">Find Your Next Career Move</h1>
                <p class="mt-1 text-sm text-slate-300">Jobs ranked automatically based on your verified skills profile.</p>
            </div>

            <!-- Search Form -->
            <form action="{{ route('jobseeker.jobs') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-5 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Job title, keywords, or company..." 
                           class="w-full pl-10 pr-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white/15">
                </div>

                <div class="sm:col-span-4 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    </div>
                    <input type="text" name="location" value="{{ request('location') }}" placeholder="Location (e.g. Cebu City)" 
                           class="w-full pl-10 pr-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white/15">
                </div>

                <div class="sm:col-span-3 flex gap-2">
                    <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-bold text-sm shadow-md transition-all">
                        Search Jobs
                    </button>
                    @if(request()->hasAny(['q', 'location', 'pwd_only', 'sort']))
                        <a href="{{ route('jobseeker.jobs') }}" class="px-4 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            <!-- Quick Filter Badges -->
            <div class="mt-4 flex flex-wrap items-center gap-2 pt-4 border-t border-white/10">
                <span class="text-xs text-slate-400 font-semibold">Quick Filters:</span>
                <a href="{{ route('jobseeker.jobs') }}" 
                   class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all {{ !request('pwd_only') && !request('sort') ? 'bg-emerald-500 text-white shadow-sm' : 'bg-white/10 text-slate-300 hover:bg-white/20' }}">
                    All Matches
                </a>
                <a href="{{ route('jobseeker.jobs', array_merge(request()->query(), ['pwd_only' => 1])) }}" 
                   class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all {{ request('pwd_only') ? 'bg-teal-500 text-white shadow-sm' : 'bg-white/10 text-slate-300 hover:bg-white/20' }}">
                    ♿ PWD Inclusive Only
                </a>
                <a href="{{ route('jobseeker.jobs', array_merge(request()->query(), ['sort' => 'latest'])) }}" 
                   class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all {{ request('sort') === 'latest' ? 'bg-emerald-500 text-white shadow-sm' : 'bg-white/10 text-slate-300 hover:bg-white/20' }}">
                    Latest Openings
                </a>
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- 2-COLUMN JOB EXPLORER -->
        <!-- =================================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Column: Job Cards List (5 Cols on desktop) -->
            <div class="lg:col-span-5 space-y-4">
                <div class="flex items-center justify-between px-1">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                        {{ count($rankedJobs) }} Positions Found
                    </p>
                    <span class="text-xs text-emerald-700 font-bold">Sorted by Skill Match %</span>
                </div>

                <div class="space-y-3">
                    @forelse ($rankedJobs as $item)
                        @php
                            $job = $item['job'];
                            $match = $item['match'];
                            $company = $job->employer->company_name ?? 'Partner Employer';
                            $hasApplied = in_array($job->job_id, $appliedJobIds);
                        @endphp
                        <div @click="selectedJobId = '{{ $job->job_id }}'; selectedJobTitle = '{{ addslashes($job->title) }}'; selectedJobCompany = '{{ addslashes($company) }}'"
                             class="cursor-pointer rounded-2xl border bg-white p-5 shadow-sm transition-all relative overflow-hidden"
                             :class="selectedJobId === '{{ $job->job_id }}' ? 'border-emerald-500 ring-2 ring-emerald-400/30 bg-emerald-50/20 shadow-md' : 'border-slate-200 hover:border-emerald-200 hover:shadow-md'">
                            
                            @if($hasApplied)
                                <span class="absolute top-0 right-0 rounded-bl-xl bg-emerald-700 text-white text-[10px] font-black px-2.5 py-0.5 shadow-xs">
                                    APPLIED
                                </span>
                            @endif

                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-base font-bold text-slate-900 leading-snug group-hover:text-emerald-700">
                                        {{ $job->title }}
                                    </h3>
                                    <p class="text-xs text-slate-500 font-medium mt-0.5">
                                        {{ $company }}
                                    </p>
                                </div>
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-extrabold border shrink-0 {{ $match['badgeClass'] ?? 'bg-emerald-50 text-emerald-700 border-emerald-200' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $match['dotColor'] ?? 'bg-emerald-500' }}"></span>
                                    {{ $match['percentage'] ?? 0 }}%
                                </span>
                            </div>

                            <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-slate-600">
                                <span class="inline-flex items-center gap-1 text-slate-500">
                                    <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    Cebu City
                                </span>
                                <span>&bull;</span>
                                <span class="font-bold text-slate-800">₱18k - ₱35k</span>
                                @if ($job->accepts_disability)
                                    <span>&bull;</span>
                                    <span class="text-teal-700 font-bold">♿ PWD</span>
                                @endif
                            </div>

                            <!-- Skills preview pills -->
                            <div class="mt-3 flex flex-wrap gap-1">
                                @foreach (array_slice($match['matchedSkills'] ?? [], 0, 2) as $s)
                                    <span class="rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 px-2 py-0.5 text-[10px] font-bold">
                                        ✓ {{ $s }}
                                    </span>
                                @endforeach
                                @if(count($match['missingSkills'] ?? []) > 0)
                                    <span class="rounded-lg bg-slate-100 text-slate-600 px-2 py-0.5 text-[10px]">
                                        +{{ count($match['missingSkills']) }} missing
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-500">
                            No jobs matched your filter criteria.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right Column: Sticky Selected Job Details Preview (7 Cols on desktop) -->
            <div class="lg:col-span-7 sticky top-20">
                @if ($selectedItem)
                    @foreach ($rankedJobs as $item)
                        @php
                            $job = $item['job'];
                            $match = $item['match'];
                            $company = $job->employer->company_name ?? 'Partner Employer';
                            $hasApplied = in_array($job->job_id, $appliedJobIds);
                        @endphp
                        <div x-show="selectedJobId === '{{ $job->job_id }}'" class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                            
                            <!-- Header & Match Meter -->
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-6 border-b border-slate-100">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">{{ $company }}</span>
                                        @if ($job->accepts_disability)
                                            <span class="rounded-full bg-teal-50 text-teal-800 border border-teal-200 px-2 py-0.5 text-[10px] font-bold">
                                                ♿ PWD Inclusive
                                            </span>
                                        @endif
                                    </div>
                                    <h2 class="text-2xl font-black text-slate-900 mt-1">{{ $job->title }}</h2>
                                    <p class="text-xs text-slate-500 mt-1">Cebu City, Philippines &bull; Full-time Position &bull; ₱18,000 - ₱35,000 / month</p>
                                </div>

                                <div class="shrink-0 flex sm:flex-col items-center sm:items-end justify-between bg-emerald-50/70 rounded-2xl p-4 border border-emerald-100">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">AI Skill Match</span>
                                    <div class="flex items-baseline gap-1 mt-0.5">
                                        <span class="text-3xl font-black text-emerald-700">{{ $match['percentage'] ?? 0 }}%</span>
                                    </div>
                                    <span class="text-xs font-extrabold text-emerald-800">
                                        {{ $match['tier'] ?? 'Match Calculated' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Skills Match Breakdown -->
                            <div class="space-y-3 bg-emerald-50/40 rounded-2xl p-5 border border-emerald-100">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Skill Compatibility Analysis</h4>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- Matched Skills -->
                                    <div class="space-y-2">
                                        <span class="text-xs font-bold text-emerald-800 flex items-center gap-1">
                                            <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Skills You Have ({{ count($match['matchedSkills'] ?? []) }})
                                        </span>
                                        <div class="flex flex-wrap gap-1.5">
                                            @forelse($match['matchedSkills'] ?? [] as $ms)
                                                <span class="rounded-lg bg-emerald-100 text-emerald-900 border border-emerald-300 px-2.5 py-1 text-xs font-bold">
                                                    {{ $ms }}
                                                </span>
                                            @empty
                                                <span class="text-xs text-slate-400 italic">No matching skills detected.</span>
                                            @endforelse
                                        </div>
                                    </div>

                                    <!-- Missing Skills -->
                                    <div class="space-y-2">
                                        <span class="text-xs font-bold text-slate-700 flex items-center gap-1">
                                            <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            Missing Requirements ({{ count($match['missingSkills'] ?? []) }})
                                        </span>
                                        <div class="flex flex-wrap gap-1.5">
                                            @forelse($match['missingSkills'] ?? [] as $miss)
                                                <span class="rounded-lg bg-slate-100 text-slate-800 border border-slate-200 px-2.5 py-1 text-xs font-bold">
                                                    {{ $miss }}
                                                </span>
                                            @empty
                                                <span class="text-xs text-emerald-700 font-extrabold">You meet 100% of the requirements!</span>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                @if(count($match['missingSkills'] ?? []) > 0)
                                    <div class="pt-2 flex items-center justify-between border-t border-emerald-100">
                                        <p class="text-[11px] text-slate-500">Want to improve your match score for this job?</p>
                                        <a href="{{ route('jobseeker.training') }}" class="text-xs font-bold text-emerald-700 hover:underline">
                                            Take Skill Courses &rarr;
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- Description -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Position Overview</h4>
                                <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                                    {{ $job->description ?: 'No detailed description provided for this opening.' }}
                                </p>
                            </div>

                            <!-- Qualifications / Requirements -->
                            @if($job->qualifications)
                                <div class="space-y-3">
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Required Qualifications</h4>
                                    <div class="rounded-2xl bg-slate-50 p-4 text-xs text-slate-700 leading-relaxed font-mono whitespace-pre-line border border-slate-100">
                                        {{ $job->qualifications }}
                                    </div>
                                </div>
                            @endif

                            <!-- Action Bar -->
                            <div class="pt-6 border-t border-slate-100 flex items-center justify-between gap-4">
                                <a href="{{ route('jobseeker.jobs.show', $job->job_id) }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">
                                    Full Page View &rarr;
                                </a>

                                @if($hasApplied)
                                    <button disabled class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-6 py-3 text-sm font-bold text-white opacity-90 cursor-not-allowed">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Application Submitted
                                    </button>
                                @else
                                    <button @click="applyModalOpen = true" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 px-8 py-3 text-sm font-black text-white shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                                        Apply for Position
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </button>
                                @endif
                            </div>

                        </div>
                    @endforeach
                @else
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-400">
                        Select a job from the list to preview details and skill match analysis.
                    </div>
                @endif
            </div>

        </div>

    </div>

    <!-- =================================================================== -->
    <!-- APPLICATION SUBMISSION MODAL -->
    <!-- =================================================================== -->
    <div x-show="applyModalOpen" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        
        <div @click.away="applyModalOpen = false" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             class="w-full max-w-lg rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Submit Application</span>
                    <h3 class="text-xl font-extrabold text-slate-900 mt-0.5" x-text="selectedJobTitle"></h3>
                    <p class="text-xs text-slate-500" x-text="selectedJobCompany"></p>
                </div>
                <button @click="applyModalOpen = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <!-- Application Form -->
            <form :action="'/jobseeker/jobs/' + selectedJobId + '/apply'" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <div class="rounded-2xl bg-emerald-50/50 p-4 border border-emerald-100 space-y-2 text-xs">
                    <p class="font-bold text-slate-800">Applicant Details (Auto-filled from Profile):</p>
                    <p class="text-slate-600"><span class="font-semibold">Name:</span> {{ Auth::user()->full_name }}</p>
                    <p class="text-slate-600"><span class="font-semibold">Email:</span> {{ Auth::user()->email }}</p>
                    <p class="text-slate-600"><span class="font-semibold">Phone:</span> {{ $jobseeker->mobile_number ?? 'Not specified' }}</p>
                </div>

                <!-- Resume Attachment -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Attach Resume / CV (Optional)</label>
                    <input type="file" name="resume" accept=".pdf,.doc,.docx" 
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-800 hover:file:bg-emerald-100">
                    <p class="text-[11px] text-slate-400">Accepted formats: PDF, DOC, DOCX (Max 5MB)</p>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="applyModalOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-md transition-colors">
                        Confirm & Submit Application
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection
