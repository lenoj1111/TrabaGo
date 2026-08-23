@extends('layouts.jobseeker')

@section('title', 'Dashboard & AI Match - TrabaGo')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- =================================================================== -->
        <!-- 1. FEATURED "BEST MATCH" HERO BANNER (GREEN/EMERALD PALETTE) -->
        <!-- =================================================================== -->
        @if ($bestMatch && isset($bestMatch['job']))
            @php
                $topJob = $bestMatch['job'];
                $topMatch = $bestMatch['match'];
                $topCompany = $topJob->employer->company_name ?? 'Partner Employer';
            @endphp
            <section class="relative isolate overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-emerald-950 to-slate-900 px-6 py-8 shadow-2xl sm:px-10 sm:py-10 border border-emerald-500/20">
                <!-- Background ambient emerald circles -->
                <div class="absolute -right-20 -top-20 -z-10 h-80 w-80 rounded-full bg-emerald-500/15 blur-3xl"></div>
                <div class="absolute -bottom-20 right-40 -z-10 h-80 w-80 rounded-full bg-teal-500/15 blur-3xl"></div>

                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
                    
                    <div class="max-w-2xl">
                        <div class="inline-flex items-center gap-2 rounded-full border border-emerald-400/40 bg-emerald-400/10 px-3.5 py-1.5 text-xs font-bold uppercase tracking-wider text-emerald-300 shadow-inner">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                            </span>
                            AI Skill-Match #1 Recommendation
                        </div>

                        <h1 class="mt-4 text-3xl font-black tracking-tight text-white sm:text-4xl lg:text-5xl">
                            {{ $topJob->title }}
                        </h1>
                        <p class="mt-2 text-base font-semibold text-emerald-300">
                            {{ $topCompany }} &bull; Cebu City &bull; ₱18,000 - ₱35,000 / mo
                        </p>
                        <p class="mt-4 text-sm leading-relaxed text-slate-300 line-clamp-2">
                            {{ $topJob->description ?: 'Explore this top-recommended position specifically matched to your verified skillset profile.' }}
                        </p>

                        <!-- Matched Skills Preview Badges -->
                        <div class="mt-5 flex flex-wrap items-center gap-2">
                            <span class="text-xs font-semibold text-slate-400">Skills snapshot:</span>
                            @foreach (array_slice($topMatch['matchedSkills'] ?? [], 0, 3) as $mSkill)
                                <span class="inline-flex items-center gap-1 rounded-lg bg-emerald-500/20 border border-emerald-400/40 px-2.5 py-1 text-xs font-bold text-emerald-200">
                                    <svg class="h-3 w-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    {{ $mSkill }}
                                </span>
                            @endforeach
                            @if(count($topMatch['matchedSkills'] ?? []) > 3)
                                <span class="text-xs text-slate-400">+{{ count($topMatch['matchedSkills']) - 3 }} more</span>
                            @endif
                        </div>
                    </div>

                    <!-- Right Match Percentage Card & Actions -->
                    <div class="flex flex-col sm:flex-row lg:flex-col items-center lg:items-end justify-between gap-6 shrink-0 bg-white/5 backdrop-blur-md rounded-2xl p-6 border border-emerald-500/20">
                        <div class="text-center lg:text-right">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Match Compatibility</span>
                            <div class="flex items-baseline justify-center lg:justify-end gap-1.5 mt-1">
                                <span class="text-5xl font-black text-emerald-400">{{ $topMatch['percentage'] ?? 0 }}%</span>
                            </div>
                            <span class="inline-block mt-1 text-xs font-bold text-emerald-300">
                                {{ $topMatch['tier'] ?? 'Match Calculated' }}
                            </span>
                        </div>

                        <div class="flex flex-col w-full sm:w-auto gap-2.5">
                            <a href="{{ route('jobseeker.jobs.show', $topJob->job_id) }}" 
                               class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 px-6 py-3.5 text-sm font-black text-white shadow-lg shadow-emerald-600/30 hover:scale-[1.02] transition-all">
                                View Match & Apply
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                            <a href="{{ route('jobseeker.jobs') }}" class="text-center text-xs font-semibold text-slate-300 hover:text-emerald-300 transition-colors">
                                Browse all matching jobs &rarr;
                            </a>
                        </div>
                    </div>

                </div>
            </section>
        @else
            <!-- Fallback Welcome Hero -->
            <section class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-8 text-white shadow-xl border border-emerald-500/20">
                <h1 class="text-3xl font-extrabold sm:text-4xl">Welcome to DMDP TrabaGo</h1>
                <p class="mt-2 text-slate-300">Start discovering jobs matched to your verified skills matrix.</p>
                <a href="{{ route('jobseeker.jobs') }}" class="mt-5 inline-flex rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-md">Explore Openings</a>
            </section>
        @endif

        <!-- =================================================================== -->
        <!-- 2. QUICK STATS METRICS -->
        <!-- =================================================================== -->
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <a href="{{ route('jobseeker.jobs') }}" class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Available Openings</p>
                    <span class="rounded-xl bg-emerald-50 p-2.5 text-emerald-700 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                </div>
                <p class="mt-4 text-3xl font-black text-slate-900">{{ $availableJobsCount }}</p>
                <p class="mt-1 text-xs font-bold text-emerald-600 flex items-center gap-1">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    Curated with AI Skill Matrix
                </p>
            </a>

            <a href="{{ route('jobseeker.applications') }}" class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Active Applications</p>
                    <span class="rounded-xl bg-teal-50 p-2.5 text-teal-700 group-hover:bg-teal-600 group-hover:text-white transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                </div>
                <p class="mt-4 text-3xl font-black text-slate-900">{{ $activeApplicationsCount }}</p>
                <p class="mt-1 text-xs font-medium text-slate-500">Track stage & interview dates</p>
            </a>

            <a href="{{ route('jobseeker.training') }}" class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Skills Training Courses</p>
                    <span class="rounded-xl bg-emerald-50 p-2.5 text-emerald-700 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </span>
                </div>
                <p class="mt-4 text-3xl font-black text-slate-900">{{ $availableTrainingsCount }}</p>
                <p class="mt-1 text-xs font-bold text-emerald-600">Earn certified skills & raise match score</p>
            </a>
        </section>

        <!-- =================================================================== -->
        <!-- 3. MAIN SECTION: RANKED JOB FEED + SIDEBAR PROGRESS -->
        <!-- =================================================================== -->
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            
            <!-- Left 2 Cols: AI Recommended Job Feed -->
            <div class="lg:col-span-2 space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-black text-slate-900">Ranked Opportunities</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Matched using your verified skill matrix</p>
                    </div>
                    <a href="{{ route('jobseeker.jobs') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                        View All Openings
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse ($rankedJobs as $item)
                        @php
                            $job = $item['job'];
                            $match = $item['match'];
                            $company = $job->employer->company_name ?? 'Partner Employer';
                        @endphp
                        <article class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-extrabold border {{ $match['badgeClass'] ?? 'bg-emerald-50 text-emerald-700 border-emerald-200' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $match['dotColor'] ?? 'bg-emerald-500' }}"></span>
                                        {{ $match['percentage'] ?? 0 }}% Match
                                    </span>
                                    @if ($job->accepts_disability)
                                        <span class="inline-flex items-center rounded-full bg-teal-50 px-2.5 py-0.5 text-xs font-bold text-teal-800 border border-teal-200">
                                            ♿ PWD Inclusive
                                        </span>
                                    @endif
                                </div>

                                <h3 class="text-lg font-bold text-slate-900 group-hover:text-emerald-700 transition-colors">
                                    <a href="{{ route('jobseeker.jobs.show', $job->job_id) }}">
                                        {{ $job->title }}
                                    </a>
                                </h3>

                                <p class="text-xs text-slate-500 font-medium">
                                    {{ $company }} &bull; Cebu City &bull; ₱18,000 - ₱35,000
                                </p>

                                <!-- Matched Skills Chips -->
                                <div class="flex flex-wrap gap-1.5 pt-1">
                                    @foreach (array_slice($match['matchedSkills'] ?? [], 0, 3) as $skill)
                                        <span class="rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 px-2 py-0.5 text-[11px] font-bold">
                                            ✓ {{ $skill }}
                                        </span>
                                    @endforeach
                                    @if(count($match['missingSkills'] ?? []) > 0)
                                        <span class="rounded-lg bg-slate-100 text-slate-600 px-2 py-0.5 text-[11px]">
                                            Needs {{ count($match['missingSkills']) }} skill(s)
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-3 shrink-0">
                                <a href="{{ route('jobseeker.jobs.show', $job->job_id) }}" 
                                   class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-2.5 text-xs font-bold text-white hover:bg-emerald-600 transition-colors shadow-sm">
                                    View Details
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-500">
                            No job openings found at the moment.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right Column: Profile Strength & Recommended Training -->
            <div class="space-y-6">
                
                <!-- Profile Strength Widget -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Profile Readiness</h3>
                        <span class="text-xs font-extrabold text-emerald-700">{{ $profileStrength }}%</span>
                    </div>

                    <div class="h-2.5 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-gradient-to-r from-emerald-600 to-teal-400 transition-all duration-500" style="width: {{ $profileStrength }}%"></div>
                    </div>

                    <p class="text-xs text-slate-500 leading-relaxed">
                        Add verified skills and upload your resume to maximize your match scores with Cebu employers.
                    </p>

                    <!-- Quick Skill Chips -->
                    <div class="pt-2 border-t border-slate-100">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-800">My Skills Matrix</span>
                            <a href="{{ route('jobseeker.profile') }}" class="text-[11px] font-bold text-emerald-600 hover:underline">Manage</a>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            @forelse($userSkills as $skill)
                                <span class="inline-flex items-center rounded-lg bg-emerald-50 border border-emerald-200 px-2.5 py-1 text-xs font-bold text-emerald-800">
                                    {{ $skill }}
                                </span>
                            @empty
                                <p class="text-xs text-slate-400 italic">No skills added yet. Go to Profile to add skills!</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Recommended Training Courses -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Skill Certifications</h3>
                        <a href="{{ route('jobseeker.training') }}" class="text-xs font-bold text-emerald-600 hover:underline">View All</a>
                    </div>

                    <div class="space-y-3">
                        @foreach($trainings as $training)
                            <div class="rounded-xl border border-emerald-100/80 bg-emerald-50/40 p-3.5 space-y-2">
                                <div class="flex items-start justify-between gap-2">
                                    <h4 class="text-xs font-bold text-slate-900">{{ $training->title }}</h4>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 shrink-0">Free</span>
                                </div>
                                <p class="text-[11px] text-slate-500 line-clamp-2">{{ $training->description }}</p>
                                <div class="flex items-center justify-between pt-1">
                                    <span class="text-[10px] text-slate-400">{{ $training->topics->count() }} Modules</span>
                                    <a href="{{ route('jobseeker.training.show', $training->training_id) }}" class="text-xs font-bold text-emerald-700 hover:underline">
                                        Start Learning &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
@endsection