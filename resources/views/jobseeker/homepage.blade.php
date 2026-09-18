@extends('layouts.jobseeker')

@section('title', 'Dashboard & AI Match - TrabaGo')

@section('content')
<div class="min-h-screen bg-gray-50 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-6">
        
        @if($jobseeker->isEmployed())
            <!-- Employment Placement Success Banner -->
            <div class="rounded-2xl bg-white p-6 text-gray-900 border border-green-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="h-10 w-10 rounded-xl bg-green-50 text-green-700 flex items-center justify-center font-bold text-sm shrink-0">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-full bg-green-100 text-green-800 font-bold text-[11px] px-2.5 py-0.5">
                                Verified Employed
                            </span>
                            <span class="text-xs text-gray-500 font-medium">Official DMDP Placement</span>
                        </div>
                        <h2 class="text-base font-bold text-gray-900 mt-0.5">Career Record Tagged as Employed</h2>
                        @if($jobseeker->hired_company)
                            <p class="text-xs text-gray-600 mt-0.5">
                                Hired by: <strong class="text-gray-900 font-bold">{{ $jobseeker->hired_company }}</strong>
                            </p>
                        @endif
                    </div>
                </div>
                <a href="{{ route('jobseeker.profile') }}" class="shrink-0 px-4 py-2 rounded-lg bg-gray-900 hover:bg-green-600 text-white text-xs font-semibold transition-colors">
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
            <div class="rounded-2xl bg-white p-6 sm:p-8 border border-gray-200 space-y-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    
                    <div class="max-w-2xl space-y-2">
                        <div class="inline-flex items-center gap-1.5 rounded-full border border-green-200 bg-green-50 px-2.5 py-0.5 text-xs font-semibold text-green-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                            AI Skill-Match #1 Recommendation
                        </div>

                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">
                            {{ $topJob->title }}
                        </h1>
                        <p class="text-xs text-gray-500 flex flex-wrap items-center gap-2">
                            <span class="font-medium text-gray-700">{{ $topCompany }}</span>
                            <span>&bull;</span>
                            <span>Cebu City</span>
                            <span>&bull;</span>
                            <span class="text-green-700 font-semibold">₱18,000 - ₱35,000 / mo</span>
                            @if ($topJob->valid_until)
                                <span>&bull;</span>
                                <span class="text-xs text-gray-500">
                                    Expires {{ $topJob->valid_until->format('M d, Y') }}
                                </span>
                            @endif
                        </p>
                        <p class="text-xs text-gray-600 leading-relaxed pt-1">
                            {{ $topJob->description ?: 'Explore this top-recommended position specifically matched to your verified skillset profile.' }}
                        </p>

                        <!-- Matched Skills Preview Badges -->
                        <div class="pt-2 flex flex-wrap items-center gap-1.5">
                            <span class="text-xs text-gray-400 font-medium">Matched skills:</span>
                            @foreach (array_slice($topMatch['matchedSkills'] ?? [], 0, 3) as $mSkill)
                                <span class="inline-flex items-center gap-1 rounded-md bg-green-50 border border-green-200 px-2 py-0.5 text-xs font-semibold text-green-800">
                                    ✓ {{ $mSkill }}
                                </span>
                            @endforeach
                            @if(count($topMatch['matchedSkills'] ?? []) > 3)
                                <span class="text-xs text-gray-400 font-medium">+{{ count($topMatch['matchedSkills']) - 3 }} more</span>
                            @endif
                        </div>
                    </div>

                    <!-- Right Match Percentage Card & Actions -->
                    <div class="flex flex-col sm:flex-row lg:flex-col items-center lg:items-end justify-between gap-4 shrink-0 bg-gray-50 rounded-xl p-5 border border-gray-200">
                        <div class="text-center lg:text-right">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Compatibility Score</span>
                            <div class="flex items-baseline justify-center lg:justify-end gap-1 mt-0.5">
                                <span class="text-4xl font-bold text-green-700">{{ $topMatch['percentage'] ?? 0 }}%</span>
                            </div>
                            <span class="text-xs font-medium text-gray-600">
                                {{ $topMatch['tier'] ?? 'High Match' }}
                            </span>
                        </div>

                        <div class="flex flex-col w-full sm:w-auto gap-2">
                            <a href="{{ route('jobseeker.jobs.show', $topJob->job_id) }}" 
                               class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-green-600 hover:bg-green-700 px-4 py-2 text-xs font-semibold text-white transition-colors">
                                <span>View & Apply</span>
                                <span>&rarr;</span>
                            </a>
                            <a href="{{ route('jobseeker.jobs') }}" class="text-center text-xs text-gray-500 hover:text-green-700 font-medium transition-colors">
                                Browse all matches &rarr;
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        @endif

        <!-- 3 Statistical Key Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <a href="{{ route('jobseeker.jobs') }}" class="rounded-xl border border-gray-200 bg-white p-5 hover:border-green-600 transition-colors">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Available Openings</p>
                    <span class="rounded-lg bg-green-50 p-2 text-green-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($availableJobsCount) }}</p>
                <p class="mt-1 text-xs text-green-700 font-medium flex items-center gap-1">
                    AI Cosine similarity ranked
                </p>
            </a>

            <a href="{{ route('jobseeker.applications') }}" class="rounded-xl border border-gray-200 bg-white p-5 hover:border-green-600 transition-colors">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Active Applications</p>
                    <span class="rounded-lg bg-blue-50 p-2 text-blue-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $activeApplicationsCount }}</p>
                <p class="mt-1 text-xs text-gray-500">Under review & interview stages</p>
            </a>

            <a href="{{ route('jobseeker.training') }}" class="rounded-xl border border-gray-200 bg-white p-5 hover:border-green-600 transition-colors">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Skills Training Courses</p>
                    <span class="rounded-lg bg-purple-50 p-2 text-purple-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $availableTrainingsCount }}</p>
                <p class="mt-1 text-xs text-green-700 font-medium">Earn certificates to raise match tier</p>
            </a>
        </div>

        <!-- Main Content Area: Ranked Job Feed + Profile Readiness -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            
            <!-- Left 2 Cols: AI Recommended Job Feed -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Ranked Vacancies For You</h2>
                        <p class="text-xs text-gray-500">Sorted by cosine similarity with your skillset</p>
                    </div>
                    <a href="{{ route('jobseeker.jobs') }}" class="text-xs font-semibold text-green-700 hover:text-green-800">
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
                        <article class="rounded-xl border border-gray-200 bg-white p-5 hover:border-gray-300 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-bold bg-green-50 text-green-700 border border-green-200">
                                        {{ $match['percentage'] ?? 0 }}% Match
                                    </span>
                                    @if ($job->accepts_disability)
                                        <span class="inline-flex items-center rounded-md bg-purple-50 px-2 py-0.5 text-xs font-semibold text-purple-700 border border-purple-200">
                                            PWD Inclusive
                                        </span>
                                    @endif
                                </div>

                                <h3 class="text-base font-bold text-gray-900">
                                    <a href="{{ route('jobseeker.jobs.show', $job->job_id) }}" class="hover:text-green-700 transition-colors">
                                        {{ $job->title }}
                                    </a>
                                </h3>

                                <p class="text-xs text-gray-500 font-medium">
                                    {{ $company }} &bull; Cebu City &bull; ₱18,000 - ₱35,000
                                </p>

                                <!-- Matched Skills Chips -->
                                <div class="flex flex-wrap gap-1 pt-1">
                                    @foreach (array_slice($match['matchedSkills'] ?? [], 0, 3) as $skill)
                                        <span class="rounded bg-gray-100 text-gray-700 px-2 py-0.5 text-[11px] font-medium">
                                            ✓ {{ $skill }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="shrink-0">
                                <a href="{{ route('jobseeker.jobs.show', $job->job_id) }}" 
                                   class="inline-flex items-center justify-center rounded-lg bg-gray-900 hover:bg-green-600 px-4 py-2 text-xs font-semibold text-white transition-colors">
                                    View Details
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-xs text-gray-500">
                            No job openings found at the moment.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right Column: Profile Strength & Recommended Training -->
            <div class="space-y-6">
                
                <!-- Profile Strength Meter -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">Profile Readiness</h3>
                        <span class="text-xs font-bold text-green-700">{{ $profileStrength }}% Complete</span>
                    </div>

                    <div class="h-2 w-full overflow-hidden rounded-full bg-gray-100">
                        <div class="h-full rounded-full bg-green-600 transition-all duration-300" style="width: {{ $profileStrength }}%"></div>
                    </div>

                    <p class="text-xs text-gray-500 leading-relaxed">
                        Add certified skills and complete your profile to maximize matching accuracy with employers.
                    </p>

                    <!-- Skills Matrix -->
                    <div class="pt-3 border-t border-gray-100 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-900">Verified Skills Matrix</span>
                            <a href="{{ route('jobseeker.profile') }}" class="text-[11px] font-semibold text-green-700 hover:underline">Manage</a>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            @forelse($userSkills as $skill)
                                <span class="inline-flex items-center rounded-md bg-green-50 border border-green-200 px-2 py-0.5 text-xs font-medium text-green-800">
                                    {{ $skill }}
                                </span>
                            @empty
                                <p class="text-xs text-gray-400 italic">No skills listed yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Skill Certifications -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">Skill Certifications</h3>
                        <a href="{{ route('jobseeker.training') }}" class="text-xs font-semibold text-green-700 hover:underline">View Catalog</a>
                    </div>

                    <div class="space-y-2.5">
                        @foreach($trainings as $training)
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 space-y-1">
                                <div class="flex items-start justify-between gap-2">
                                    <h4 class="text-xs font-bold text-gray-900 truncate">{{ $training->title }}</h4>
                                    <span class="text-[10px] font-semibold px-2 py-0.2 rounded-full bg-green-100 text-green-800 shrink-0">Free</span>
                                </div>
                                <p class="text-[11px] text-gray-500 line-clamp-1">{{ $training->description }}</p>
                                <div class="flex items-center justify-between pt-1">
                                    <span class="text-[10px] text-gray-400">{{ $training->topics->count() }} Modules</span>
                                    <a href="{{ route('jobseeker.training.show', $training->training_id) }}" class="text-xs font-semibold text-green-700 hover:underline">
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