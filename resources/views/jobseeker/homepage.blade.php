@extends('layouts.jobseeker')

@section('title', 'Dashboard & AI Match - TrabaGo')

@section('content')
<div class="min-h-screen bg-slate-50/70 px-4 py-8 sm:px-6 lg:px-8">
    <div class="w-full max-w-[1680px] mx-auto space-y-6">
        
        @if($jobseeker->isEmployed())
            <!-- Employment Placement Success Banner -->
            <div class="rounded-3xl bg-white p-6 text-slate-900 border border-emerald-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-sm shrink-0 border border-emerald-200/60 shadow-xs">
                        <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-full bg-emerald-100 text-emerald-800 font-bold text-[11px] px-3 py-0.5 border border-emerald-200/60">
                                Verified Employed
                            </span>
                            <span class="text-xs text-slate-500 font-medium">Official DMDP Placement</span>
                        </div>
                        <h2 class="text-base font-bold text-slate-900 mt-1">Profile Tagged as Employed</h2>
                        @if($jobseeker->hired_company)
                            <p class="text-xs text-slate-600 mt-0.5">
                                Hired by: <strong class="text-slate-900 font-bold">{{ $jobseeker->hired_company }}</strong>
                            </p>
                        @endif
                    </div>
                </div>
                <a href="{{ route('jobseeker.profile') }}" class="shrink-0 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold transition-all shadow-sm active:scale-95">
                    View Employment Record &rarr;
                </a>
            </div>
        @endif

        <!-- AI Top Match Hero -->
        @if ($bestMatch && isset($bestMatch['job']))
            @php
                $topJob = $bestMatch['job'];
                $topMatch = $bestMatch['match'];
                $topCompany = $topJob->employer->company_name ?? 'Partner Employer';
            @endphp
            <div class="rounded-3xl bg-white p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    
                    <div class="max-w-2xl space-y-2.5">
                        <div class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            AI Skill-Match #1 Recommendation
                        </div>

                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                            {{ $topJob->title }}
                        </h1>
                        <p class="text-xs text-slate-500 flex flex-wrap items-center gap-2">
                            <span class="font-bold text-slate-800">{{ $topCompany }}</span>
                            <span>&bull;</span>
                            <span>Cebu City</span>
                            <span>&bull;</span>
                            <span class="text-emerald-700 font-bold">₱18,000 - ₱35,000 / mo</span>
                            @if ($topJob->valid_until)
                                <span>&bull;</span>
                                <span class="text-xs text-slate-400">
                                    Available until {{ $topJob->valid_until->format('M d, Y') }}
                                </span>
                            @endif
                        </p>
                        <p class="text-xs text-slate-600 leading-relaxed pt-1">
                            {{ $topJob->description ?: 'Explore this top-recommended position specifically matched to your verified skillset profile.' }}
                        </p>

                        <!-- Matched Skills Preview Badges -->
                        <div class="pt-2 flex flex-wrap items-center gap-1.5">
                            <span class="text-xs text-slate-400 font-medium">Matched skills:</span>
                            @foreach (array_slice($topMatch['matchedSkills'] ?? [], 0, 3) as $mSkill)
                                <span class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 border border-emerald-200 px-2.5 py-1 text-xs font-semibold text-emerald-800">
                                    <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>{{ $mSkill }}</span>
                                </span>
                            @endforeach
                            @if(count($topMatch['matchedSkills'] ?? []) > 3)
                                <span class="text-xs text-slate-400 font-medium">+{{ count($topMatch['matchedSkills']) - 3 }} more</span>
                            @endif
                        </div>
                    </div>

                    <!-- Right Match Percentage Card & Actions -->
                    <div class="flex flex-col sm:flex-row lg:flex-col items-center lg:items-end justify-between gap-4 shrink-0 bg-slate-50 rounded-2xl p-6 border border-slate-200 shadow-inner">
                        <div class="text-center lg:text-right">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Compatibility Score</span>
                            <div class="flex items-baseline justify-center lg:justify-end gap-1 mt-0.5">
                                <span class="text-4xl font-black text-emerald-700 tracking-tight">{{ $topMatch['percentage'] ?? 0 }}%</span>
                            </div>
                            <span class="text-xs font-bold text-slate-700">
                                {{ $topMatch['tier'] ?? 'High Match' }}
                            </span>
                        </div>

                        <div class="flex flex-col w-full sm:w-auto gap-2">
                            <a href="{{ route('jobseeker.jobs.show', $topJob->job_id) }}" 
                               class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 px-5 py-2.5 text-xs font-bold text-white transition-all shadow-md shadow-emerald-950/20 active:scale-95">
                                <span>View & Apply</span>
                                <span>&rarr;</span>
                            </a>
                            <a href="{{ route('jobseeker.jobs') }}" class="text-center text-xs text-slate-500 hover:text-emerald-700 font-semibold transition-colors">
                                Browse all matches &rarr;
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        @endif

        <!-- 3 Statistical Key Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <a href="{{ route('jobseeker.jobs') }}" class="rounded-2xl border border-slate-200 bg-white p-6 hover:border-emerald-400 hover:shadow-md transition-all group">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Available Openings</p>
                    <span class="rounded-xl bg-emerald-50 p-2.5 text-emerald-700 group-hover:scale-105 transition-transform border border-emerald-100">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                </div>
                <p class="mt-3 text-3xl font-black text-slate-900 tracking-tight">{{ number_format($availableJobsCount) }}</p>
                <p class="mt-1.5 text-xs text-emerald-700 font-semibold flex items-center gap-1">
                    AI Cosine similarity ranked
                </p>
            </a>

            <a href="{{ route('jobseeker.applications') }}" class="rounded-2xl border border-slate-200 bg-white p-6 hover:border-sky-400 hover:shadow-md transition-all group">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Active Applications</p>
                    <span class="rounded-xl bg-sky-50 p-2.5 text-sky-700 group-hover:scale-105 transition-transform border border-sky-100">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                </div>
                <p class="mt-3 text-3xl font-black text-slate-900 tracking-tight">{{ $activeApplicationsCount }}</p>
                <p class="mt-1.5 text-xs text-slate-500 font-medium">Under review & interview stages</p>
            </a>

            <a href="{{ route('jobseeker.training') }}" class="rounded-2xl border border-slate-200 bg-white p-6 hover:border-purple-400 hover:shadow-md transition-all group">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Skills Training Courses</p>
                    <span class="rounded-xl bg-purple-50 p-2.5 text-purple-700 group-hover:scale-105 transition-transform border border-purple-100">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </span>
                </div>
                <p class="mt-3 text-3xl font-black text-slate-900 tracking-tight">{{ $availableTrainingsCount }}</p>
                <p class="mt-1.5 text-xs text-purple-700 font-semibold">Earn certificates to raise match tier</p>
            </a>
        </div>

        <!-- Main Content Area: Ranked Job Feed + Profile Readiness -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            
            <!-- Left 2 Cols: AI Recommended Job Feed -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900">Ranked Vacancies For You</h2>
                        <p class="text-xs text-slate-500">Sorted by cosine similarity with your skillset</p>
                    </div>
                    <a href="{{ route('jobseeker.jobs') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 transition-colors">
                        View All &rarr;
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse ($rankedJobs as $item)
                        @php
                            $job = $item['job'];
                            $match = $item['match'];
                            $company = $job->employer->company_name ?? 'Partner Employer';
                        @endphp
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 hover:border-slate-300 hover:shadow-xs transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ $match['percentage'] ?? 0 }}% Match
                                    </span>
                                    @if ($job->accepts_disability)
                                        <span class="inline-flex items-center rounded-full bg-purple-50 px-2.5 py-0.5 text-xs font-bold text-purple-700 border border-purple-200">
                                            PWD Inclusive
                                        </span>
                                    @endif
                                </div>

                                <h3 class="text-base font-bold text-slate-900">
                                    <a href="{{ route('jobseeker.jobs.show', $job->job_id) }}" class="hover:text-emerald-700 transition-colors">
                                        {{ $job->title }}
                                    </a>
                                </h3>

                                <p class="text-xs text-slate-500 font-medium">
                                    {{ $company }} &bull; Cebu City &bull; ₱18,000 - ₱35,000
                                    @if ($job->valid_until)
                                        &bull; <span>Available until {{ $job->valid_until->format('M d, Y') }}</span>
                                    @endif
                                </p>

                                <!-- Matched Skills Chips -->
                                <div class="flex flex-wrap gap-1 pt-1">
                                    @foreach (array_slice($match['matchedSkills'] ?? [], 0, 3) as $skill)
                                        <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 text-slate-700 px-2 py-0.5 text-[11px] font-medium border border-slate-200/50">
                                            <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            <span>{{ $skill }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="shrink-0">
                                <a href="{{ route('jobseeker.jobs.show', $job->job_id) }}" 
                                   class="inline-flex items-center justify-center rounded-xl bg-slate-900 hover:bg-emerald-600 px-4 py-2 text-xs font-bold text-white transition-all shadow-xs active:scale-95">
                                    View Details
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-xs text-slate-400">
                            No job openings found at the moment.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right Column: Profile Strength & Recommended Training -->
            <div class="space-y-6">
                
                <!-- Profile Strength Meter -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Profile Readiness</h3>
                        <span class="text-xs font-black text-emerald-700">{{ $profileStrength }}% Complete</span>
                    </div>

                    <div class="h-2.5 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-emerald-600 transition-all duration-300" style="width: {{ $profileStrength }}%"></div>
                    </div>

                    <p class="text-xs text-slate-500 leading-relaxed">
                        Add certified skills and complete your profile to maximize matching accuracy with employers.
                    </p>

                    <!-- Skills Matrix -->
                    <div class="pt-4 border-t border-slate-100 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900">Verified Skills Matrix</span>
                            <a href="{{ route('jobseeker.profile') }}" class="text-[11px] font-bold text-emerald-700 hover:underline">Manage</a>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            @forelse($userSkills as $skill)
                                <span class="inline-flex items-center rounded-lg bg-emerald-50 border border-emerald-200 px-2.5 py-1 text-xs font-semibold text-emerald-800">
                                    {{ $skill }}
                                </span>
                            @empty
                                <p class="text-xs text-slate-400 italic">No skills listed yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Skill Certifications -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Skill Certifications</h3>
                        <a href="{{ route('jobseeker.training') }}" class="text-xs font-bold text-emerald-700 hover:underline">View Catalog</a>
                    </div>

                    <div class="space-y-3">
                        @foreach($trainings as $training)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-3.5 space-y-1.5 hover:border-slate-300 transition-all">
                                <div class="flex items-start justify-between gap-2">
                                    <h4 class="text-xs font-bold text-slate-900 truncate">{{ $training->title }}</h4>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200/60 shrink-0">Free</span>
                                </div>
                                <p class="text-[11px] text-slate-500 line-clamp-1">{{ $training->description }}</p>
                                <div class="flex items-center justify-between pt-1">
                                    <span class="text-[10px] text-slate-400 font-medium">{{ $training->topics->count() }} Modules</span>
                                    <a href="{{ route('jobseeker.training.show', $training->training_id) }}" class="text-xs font-bold text-emerald-700 hover:underline">
                                        Enroll &rarr;
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