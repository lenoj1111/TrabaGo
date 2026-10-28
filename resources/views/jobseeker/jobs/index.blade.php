@extends('layouts.jobseeker')

@section('title', 'Explore Jobs & AI Matching - TrabaGo')

@section('content')
@php
$currentUser = Auth::user();
$isApproved = (bool) ($currentUser->is_approved ?? false);
@endphp
<div x-data="{ 
    selectedJobId: null,
    previewOpen: false,
    applyModalOpen: false,
    approvalWarningOpen: false,
    selectedJobTitle: '',
    selectedJobCompany: ''
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="w-full max-w-[1680px] mx-auto space-y-6">

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
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Job title, keywords, or company..."
                        class="w-full pl-10 pr-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white/15">
                </div>

                <div class="sm:col-span-4 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                    </div>
                    <input type="text" name="location" value="{{ request('location') }}" placeholder="Location (e.g. Cebu City)"
                        class="w-full pl-10 pr-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white/15">
                </div>

                <div class="sm:col-span-3 flex gap-2">
                    <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-bold text-sm shadow-md transition-all">
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
                    class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all inline-flex items-center gap-1.5 {{ request('pwd_only') ? 'bg-emerald-500 text-white shadow-sm' : 'bg-white/10 text-slate-300 hover:bg-white/20' }}">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="4" r="2" />
                        <path d="M15.5 13.5A4.5 4.5 0 0 1 11 18a4.5 4.5 0 0 1-4.5-4.5A4.5 4.5 0 0 1 11 9c.4 0 .78.06 1.14.16L13.5 6.5C12.7 6.18 11.87 6 11 6a7.5 7.5 0 0 0-7.5 7.5A7.5 7.5 0 0 0 11 21a7.5 7.5 0 0 0 7.42-6.5h-2.92z" />
                        <path d="M19 10h-4.5l-1.3-2.6A2 2 0 0 0 11.4 6.2L8.5 7.6 9.4 9.4l2.1-1 2 4.1V17h2v-4.5l1.6-1.6H19v-2z" />
                    </svg>
                    <span>PWD Inclusive Only</span>
                </a>
                <a href="{{ route('jobseeker.jobs', array_merge(request()->query(), ['sort' => 'latest'])) }}"
                    class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all {{ request('sort') === 'latest' ? 'bg-emerald-500 text-white shadow-sm' : 'bg-white/10 text-slate-300 hover:bg-white/20' }}">
                    Latest Openings
                </a>
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- FULL-SCREEN GRID JOB LISTINGS -->
        <!-- =================================================================== -->
        <div class="space-y-4">
            <div class="flex items-center justify-between px-1">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                    {{ count($rankedJobs) }} Positions Available
                </p>
                <span class="text-xs text-emerald-700 font-bold">Click any box to inspect details & apply</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($rankedJobs as $item)
                @php
                $job = $item['job'];
                $match = $item['match'];
                $company = $job->employer->company_name ?? 'Partner Employer';
                $hasApplied = in_array($job->job_id, $appliedJobIds);
                @endphp
                <div @click="selectedJobId = '{{ $job->job_id }}'; selectedJobTitle = '{{ addslashes($job->title) }}'; selectedJobCompany = '{{ addslashes($company) }}'; previewOpen = true;"
                    class="cursor-pointer rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md hover:border-emerald-400 transition-all relative overflow-hidden flex flex-col justify-between group">

                    @if($hasApplied)
                    <span class="absolute top-0 right-0 rounded-bl-xl bg-emerald-700 text-white text-[9px] font-black px-2.5 py-1 shadow-xs">
                        APPLIED
                    </span>
                    @endif

                    <div>
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 pr-2">
                                <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider">{{ $company }}</span>
                                <h3 class="text-base font-black text-slate-900 group-hover:text-emerald-700 transition-colors leading-snug mt-0.5">
                                    {{ $job->title }}
                                </h3>
                            </div>
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-black border shrink-0 {{ $match['badgeClass'] ?? 'bg-emerald-50 text-emerald-700 border-emerald-200' }}">
                                {{ $match['percentage'] ?? 0 }}% Match
                            </span>
                        </div>

                        <p class="text-xs text-slate-500 mt-2 flex items-center gap-2">
                            <span>{{ $job->location ?: 'Location not specified' }}</span>
                            <span>&bull;</span>
                            <span>{{ $job->salary_range ?: 'Salary not specified' }}</span>
                        </p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        @if ($job->accepts_disability)
                        <span class="text-emerald-600 font-bold inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="4" r="2" />
                                <path d="M15.5 13.5A4.5 4.5 0 0 1 11 18a4.5 4.5 0 0 1-4.5-4.5A4.5 4.5 0 0 1 11 9c.4 0 .78.06 1.14.16L13.5 6.5C12.7 6.18 11.87 6 11 6a7.5 7.5 0 0 0-7.5 7.5A7.5 7.5 0 0 0 11 21a7.5 7.5 0 0 0 7.42-6.5h-2.92z" />
                                <path d="M19 10h-4.5l-1.3-2.6A2 2 0 0 0 11.4 6.2L8.5 7.6 9.4 9.4l2.1-1 2 4.1V17h2v-4.5l1.6-1.6H19v-2z" />
                            </svg>
                            PWD Inclusive
                        </span>
                        @else
                        <span class="text-slate-400">Standard Opening</span>
                        @endif
                        <span class="font-bold text-emerald-600 group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                            View Details &rarr;
                        </span>
                    </div>
                </div>
                @empty
                <div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-500">
                    No jobs matched your filter criteria.
                </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- =================================================================== -->
    <!-- SLIDE-OVER DRAWER WITH SMOOTH TRANSITIONS & FADE SWITCHING -->
    <!-- =================================================================== -->
    <div x-show="previewOpen" x-cloak
        class="fixed inset-0 z-50 overflow-hidden bg-slate-950/40 backdrop-blur-xs"
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        <div class="absolute inset-y-0 right-0 max-w-full flex pl-10">
            <div @click.away="previewOpen = false"
                class="w-screen max-w-xl bg-white shadow-2xl flex flex-col h-full border-l border-slate-200"
                x-transition:enter="transform transition ease-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full">

                @if ($rankedJobs && count($rankedJobs) > 0)
                @foreach ($rankedJobs as $item)
                @php
                $job = $item['job'];
                $match = $item['match'];
                $company = $job->employer->company_name ?? 'Partner Employer';
                $hasApplied = in_array($job->job_id, $appliedJobIds);
                @endphp

                <!-- Individual Job View inside Drawer with Snappy Fade/Scale Transition -->
                <div x-show="selectedJobId === '{{ $job->job_id }}'"
                    x-transition:enter="transition ease-out duration-200 delay-75"
                    x-transition:enter-start="opacity-0 scale-98"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="flex flex-col h-full overflow-hidden">

                    <!-- Scrollable Content Body -->
                    <div class="p-6 sm:p-7 space-y-6 flex-1 overflow-y-auto">

                        <!-- Drawer Header -->
                        <div class="flex items-start justify-between pb-5 border-b border-slate-100">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">{{ $company }}</span>
                                    @if ($job->accepts_disability)
                                    <span class="rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 px-2 py-0.5 text-[10px] font-bold inline-flex items-center gap-1">
                                        <span>PWD Inclusive</span>
                                    </span>
                                    @endif
                                </div>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">{{ $job->title }}</h2>
                                <p class="text-xs text-slate-500 mt-1 flex flex-wrap items-center gap-2">
                                    <span>{{ $job->location ?: 'Location not specified' }} &bull; {{ $job->job_type ?: 'Job type not specified' }} &bull; {{ $job->salary_range ?: 'Salary not specified' }}</span>
                                </p>
                            </div>
                            <button @click="previewOpen = false" class="rounded-full bg-slate-100 p-2 text-slate-500 hover:bg-slate-200 font-bold text-lg leading-none">&times;</button>
                        </div>

                        <!-- Match Score Banner -->
                        <div class="bg-emerald-50/70 rounded-2xl p-4 border border-emerald-100 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">AI Skill Match Rating</span>
                                <p class="text-xs font-extrabold text-emerald-800">{{ $match['tier'] ?? 'Match Calculated' }}</p>
                            </div>
                            <span class="text-3xl font-black text-emerald-700">{{ $match['percentage'] ?? 0 }}%</span>
                        </div>

                        <!-- Skills Compatibility Analysis -->
                        <div class="space-y-3 bg-emerald-50/40 rounded-2xl p-4 border border-emerald-100">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Skill Compatibility Analysis</h4>
                            <div class="space-y-2">
                                <span class="text-xs font-bold text-slate-800">Required Skills</span>
                                <div class="flex flex-wrap gap-2">
                                    @php
                                    $requiredSkillsList = array_filter(array_map('trim', explode(',', $job->skills ?? '')));
                                    if (empty($requiredSkillsList)) {
                                    $requiredSkillsList = array_merge($match['matchedSkills'] ?? [], $match['missingSkills'] ?? []);
                                    }
                                    @endphp

                                    @forelse($requiredSkillsList as $reqSkill)
                                    @php
                                    $hasSkill = in_array(strtolower($reqSkill), array_map('strtolower', $userSkills));
                                    @endphp
                                    @if($hasSkill)
                                    <span class="inline-flex items-center gap-1 rounded-lg bg-emerald-100 text-emerald-900 border border-emerald-300 px-2.5 py-1 text-xs font-bold shadow-sm">
                                        ✓ {{ $reqSkill }}
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 px-2.5 py-1 text-xs font-bold shadow-sm">
                                        ✕ {{ $reqSkill }}
                                    </span>
                                    @endif
                                    @empty
                                    <span class="text-xs text-slate-400 italic">No specific skills required.</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Position Overview -->
                        <div class="space-y-2">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Position Overview</h4>
                            <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                                {{ $job->description ?: 'No detailed description provided.' }}
                            </p>
                        </div>

                        <!-- Qualifications -->
                        @if($job->qualifications)
                        <div class="space-y-2">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Required Qualifications</h4>
                            <ul class="list-disc pl-5 rounded-2xl bg-slate-50 p-4 text-xs text-slate-700 leading-relaxed border border-slate-100 space-y-1">
                                @foreach(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $job->qualifications))) as $qualification)
                                <li>{{ $qualification }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if($job->benefits_perks)
                        <div class="space-y-2">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Benefits & Perks</h4>
                            <ul class="list-disc pl-5 rounded-2xl bg-emerald-50/50 p-4 text-xs text-slate-700 leading-relaxed border border-emerald-100 space-y-1">
                                @foreach(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $job->benefits_perks))) as $benefit)
                                <li>{{ $benefit }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if($job->other_instructions)
                        <div class="space-y-2">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Other Instructions</h4>
                            <p class="rounded-2xl bg-slate-50 p-4 text-xs text-slate-700 leading-relaxed whitespace-pre-line border border-slate-100">{{ $job->other_instructions }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Pinned Sticky Footer Action Bar -->
                    <div class="px-6 py-4 border-t border-slate-200 bg-white/95 backdrop-blur-sm flex items-center justify-between gap-4 shrink-0 shadow-lg">
                        <a href="{{ route('jobseeker.jobs.show', $job->job_id) }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">
                            Full Page &rarr;
                        </a>

                        @if($hasApplied)
                        <button disabled class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-5 py-2.5 text-xs font-bold text-white opacity-90 cursor-not-allowed">
                            Applied
                        </button>
                        @elseif($job->isExpired())
                        <button disabled class="inline-flex items-center gap-2 rounded-xl bg-slate-200 px-5 py-2.5 text-xs font-bold text-slate-600 cursor-not-allowed">
                            Expired
                        </button>
                        @elseif(!$isApproved)
                        <button type="button" @click="approvalWarningOpen = true" class="inline-flex items-center gap-2 rounded-xl bg-amber-100 border border-amber-300 px-5 py-2.5 text-xs font-black text-amber-900">
                            <span>⚠️</span> Approval Required
                        </button>
                        @elseif(!auth()->user()->is_approved)
                        <button type="button" onclick="alert('Your jobseeker account is pending approval by DMDP.')"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-slate-200 px-4 py-2 text-xs font-bold text-slate-600 cursor-not-allowed">
                            Pending Approval
                        </button>
                        @elseif(!$jobseeker->isProfileAndDocumentsComplete())
                        <button type="button" onclick="alert('Please complete your profile and upload required documents.')"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-amber-100 px-4 py-2 text-xs font-bold text-amber-900">
                            Incomplete
                        </button>
                        @else
                        <button @click="applyModalOpen = true" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 px-6 py-3 text-xs font-black text-white shadow-md transition-all">
                            Apply for Position
                        </button>
                        @endif
                    </div>

                </div>
                @endforeach
                @endif

            </div>
        </div>
    </div>

    <!-- APPLICATION SUBMISSION MODAL -->
    <div x-show="applyModalOpen" x-cloak
        class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">

        <div @click.away="applyModalOpen = false"
            class="w-full max-w-lg rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">

            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Submit Application</span>
                    <h3 class="text-xl font-extrabold text-slate-900 mt-0.5" x-text="selectedJobTitle"></h3>
                    <p class="text-xs text-slate-500" x-text="selectedJobCompany"></p>
                </div>
                <button @click="applyModalOpen = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form :action="'/jobseeker/jobs/' + selectedJobId + '/apply'" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div class="rounded-2xl bg-emerald-50/50 p-4 border border-emerald-100 space-y-2 text-xs">
                    <p class="font-bold text-slate-800">Applicant Details (Auto-filled from Profile):</p>
                    <p class="text-slate-600"><span class="font-semibold">Name:</span> {{ Auth::user()->full_name }}</p>
                    <p class="text-slate-600"><span class="font-semibold">Email:</span> {{ Auth::user()->email }}</p>
                    <p class="text-slate-600"><span class="font-semibold">Phone:</span> {{ $jobseeker->mobile_number ?? 'Not specified' }}</p>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Attach Resume / CV (Optional)</label>
                        <span class="text-[11px] text-emerald-700 font-bold">Profile auto-attached</span>
                    </div>
                    <input type="file" name="resume" accept=".pdf,.doc,.docx"
                        class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-800 hover:file:bg-emerald-100">
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="applyModalOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-md">
                        Confirm & Submit Application
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- APPROVAL WARNING MODAL -->
    <div x-show="approvalWarningOpen" x-cloak class="fixed inset-0 z-[80] flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="approvalWarningOpen = false" class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl border border-amber-200 space-y-5">
            <div class="flex items-start gap-3">
                <div class="h-12 w-12 rounded-2xl bg-amber-100 border border-amber-200 text-amber-800 flex items-center justify-center text-2xl shrink-0">⚠️</div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-amber-700">Action required</p>
                    <h3 class="text-xl font-black text-slate-900 mt-1">Approval Needed Before You Can Apply</h3>
                </div>
            </div>
            <p class="text-sm text-slate-600 leading-relaxed">
                Your jobseeker account is still pending admin approval. Please wait for approval before you can submit any job application.
            </p>
            <div class="flex justify-end">
                <button type="button" @click="approvalWarningOpen = false" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-white text-xs font-black">
                    OK
                </button>
            </div>
        </div>
    </div>

</div>
@endsection