@extends('layouts.jobseeker')

@section('title', $job->title . ' - TrabaGo AI Match')

@section('content')
@php
    $currentUser = Auth::user();
    $isApproved = (bool) ($currentUser->is_approved ?? false);
@endphp
<div x-data="{ applyModalOpen: false, approvalWarningOpen: false, accredModalOpen: false }" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-4xl space-y-8">

        @if(!$isApproved)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 sm:p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="h-10 w-10 rounded-xl bg-amber-100 border border-amber-200 text-amber-800 flex items-center justify-center text-lg shrink-0">⚠️</div>
                    <div>
                        <h2 class="text-sm font-black text-amber-900">Approval Required Before Applying</h2>
                        <p class="text-xs text-amber-800 mt-0.5">Your jobseeker account is pending admin approval. You may browse the job listing, but you must be approved before you can apply for this job posting.</p>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Back Navigation -->
        <div class="flex items-center justify-between">
            <a href="{{ route('jobseeker.jobs') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-emerald-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to All Jobs
            </a>
            <span class="text-xs text-slate-400 font-mono">Position ID: #{{ $job->job_id }}</span>
        </div>

        @php
            $company = $job->employer->company_name ?? 'Partner Employer';
        @endphp

        <!-- Main Job Card -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-10 shadow-sm space-y-8">
            
            <!-- Header & Compatibility -->
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 pb-8 border-b border-slate-100">
                <div class="space-y-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">{{ $company }}</span>
                        @if ($job->employer && $job->employer->is_accredited)
                            <button type="button" 
                                    @click="accredModalOpen = true"
                                    class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-300 px-3 py-0.5 text-xs font-black hover:bg-emerald-100 transition-colors shadow-2xs cursor-pointer"
                                    title="Click to view verified employer accreditation details">
                                <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span>DMDP Verified Employer</span>
                                <svg class="w-2.5 h-2.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </button>
                        @endif
                        @if ($job->accepts_disability)
                            <span class="rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 px-2.5 py-0.5 text-xs font-bold inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-700" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="4" r="2"/><path d="M15.5 13.5A4.5 4.5 0 0 1 11 18a4.5 4.5 0 0 1-4.5-4.5A4.5 4.5 0 0 1 11 9c.4 0 .78.06 1.14.16L13.5 6.5C12.7 6.18 11.87 6 11 6a7.5 7.5 0 0 0-7.5 7.5A7.5 7.5 0 0 0 11 21a7.5 7.5 0 0 0 7.42-6.5h-2.92z"/><path d="M19 10h-4.5l-1.3-2.6A2 2 0 0 0 11.4 6.2L8.5 7.6 9.4 9.4l2.1-1 2 4.1V17h2v-4.5l1.6-1.6H19v-2z"/></svg>
                                <span>PWD Inclusive ({{ $job->disability_type ?: 'All eligible' }})</span>
                            </span>
                        @endif
                    </div>

                    <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">{{ $job->title }}</h1>
                    
                    <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-slate-500 pt-1">
                        @if($job->location)
                            <span class="flex items-center gap-1">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            {{ $job->location ?? 'Cebu City, Philippines' }}
                        </span>
                        @endif
                        <span>&bull;</span>
                        <span class="font-bold text-slate-900">{{ $job->salary_range ?: 'Salary not specified' }}</span>
                        <span>&bull;</span>
                        <span>{{ $job->job_type ?: 'Job type not specified' }}</span>
                        @if ($job->valid_until)
                            <span>&bull;</span>
                            <span class="inline-flex items-center gap-1 font-bold {{ $job->valid_until->isPast() ? 'text-rose-600' : 'text-emerald-700' }}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Application Deadline: Available until {{ $job->valid_until->format('F d, Y') }} ({{ $job->valid_until->isPast() ? 'Expired' : $job->valid_until->diffForHumans() }})
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Match Score Badge -->
                <div class="shrink-0 rounded-2xl bg-gradient-to-br from-slate-950 via-emerald-950 to-slate-900 text-white p-5 text-center min-w-[140px] shadow-lg border border-emerald-500/30">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-300">AI Skill Match</span>
                    <p class="text-4xl font-black text-emerald-400 mt-1">{{ $match['percentage'] ?? 0 }}%</p>
                    <span class="inline-block mt-1 text-xs font-semibold text-emerald-300">
                        {{ $match['tier'] ?? 'Calculated' }}
                    </span>
                </div>
            </div>

            <!-- AI Skill Matrix Deep-dive -->
            <div class="rounded-2xl bg-emerald-50/40 p-6 border border-emerald-100 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Skill Compatibility Breakdown</h3>
                    <span class="text-xs font-semibold text-emerald-800">{{ $match['matchedCount'] ?? 0 }} of {{ $match['totalRequired'] ?? 0 }} requirements met</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                    <div class="space-y-2">
                        <span class="text-xs font-bold text-emerald-800 flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Your Matching Skills
                        </span>
                        <div class="flex flex-wrap gap-1.5">
                            @forelse($match['matchedSkills'] ?? [] as $skill)
                                <span class="rounded-lg bg-emerald-100 text-emerald-900 border border-emerald-300 px-3 py-1 text-xs font-bold">
                                    {{ $skill }}
                                </span>
                            @empty
                                <p class="text-xs text-slate-400 italic">No direct matches. Add more verified skills in your profile.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="space-y-2">
                        <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Missing Requirements
                        </span>
                        <div class="flex flex-wrap gap-1.5">
                            @forelse($match['missingSkills'] ?? [] as $missing)
                                <span class="rounded-lg bg-slate-100 text-slate-800 border border-slate-200 px-3 py-1 text-xs font-bold">
                                    {{ $missing }}
                                </span>
                            @empty
                                <p class="text-xs text-emerald-700 font-extrabold">Congratulations! You meet all requirements for this role.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Job Description -->
            <div class="space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Position Overview</h3>
                <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                    {{ $job->description ?: 'No overview description provided.' }}
                </p>
            </div>

            @if(!empty($job->qualifications_list))
                <div class="space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Qualifications & Requirements</h3>
                    <div class="rounded-2xl bg-slate-50 p-5 text-xs text-slate-800 leading-relaxed border border-slate-200">
                        <ul class="list-disc list-inside space-y-1.5">
                            @foreach($job->qualifications_list as $qual)
                                <li class="pl-1">{{ $qual }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @elseif($job->qualifications)
                <div class="space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Qualifications & Requirements</h3>
                    <div class="rounded-2xl bg-slate-50 p-5 text-xs text-slate-800 leading-relaxed font-mono whitespace-pre-line border border-slate-200">
                        {{ $job->qualifications }}
                    </div>
                </div>
            @endif

            @if(!empty($job->skills_list))
                <div class="space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Required Skills</h3>
                    <div class="rounded-2xl bg-slate-50 p-5 text-xs text-slate-800 leading-relaxed border border-slate-200">
                        <ul class="list-disc list-inside space-y-1.5">
                            @foreach($job->skills_list as $skill)
                                <li class="pl-1 font-semibold text-slate-900">{{ $skill }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if(!empty($job->benefits_list))
                <div class="space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-900">Benefits & Perks</h3>
                    <div class="rounded-2xl bg-emerald-50/50 p-5 text-xs text-emerald-950 leading-relaxed border border-emerald-200">
                        <ul class="list-disc list-inside space-y-1.5">
                            @foreach($job->benefits_list as $benefit)
                                <li class="pl-1 font-medium">{{ $benefit }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if($job->other_instructions)
                <div class="space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Application Instructions</h3>
                    <div class="rounded-2xl bg-slate-50 p-5 text-xs text-slate-700 leading-relaxed whitespace-pre-line border border-slate-200">
                        {{ $job->other_instructions }}
                    </div>
                </div>
            @endif

            @if(!empty($recommendedTrainings) && count($recommendedTrainings) > 0)
                <div class="rounded-2xl bg-emerald-50/70 p-5 border border-emerald-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-900">Recommended Skill Certifications</h4>
                        <span class="text-[11px] text-emerald-700 font-semibold">Boost your match rating</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($recommendedTrainings as $recT)
                            <div class="rounded-xl bg-white p-3.5 border border-emerald-200 shadow-sm flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-bold text-slate-900">{{ $recT->title }}</p>
                                    <p class="text-[10px] text-slate-500">Free Online Assessment</p>
                                </div>
                                <a href="{{ route('jobseeker.training.show', $recT->training_id) }}" class="text-xs font-bold text-emerald-700 hover:underline">
                                    Start &rarr;
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Bottom Application Actions -->
            <div class="pt-8 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-slate-500">Applications are reviewed directly by verified Cebu employers.</p>

                @if($hasApplied)
                    <div class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-8 py-3.5 text-sm font-bold text-white shadow-md">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        You Have Already Applied
                    </div>
                @elseif($job->isExpired())
                    <div class="inline-flex items-center gap-2 rounded-xl bg-slate-200 px-8 py-3.5 text-sm font-bold text-slate-600">
                        <svg class="h-5 w-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Job Posting Expired
                    </div>
                @elseif(!$isApproved)
                    <button type="button" @click="approvalWarningOpen = true" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-amber-100 border border-amber-300 hover:bg-amber-200 px-10 py-3.5 text-sm font-black text-amber-900 shadow-sm transition-all">
                        <span>⚠️</span>
                        Approval Required to Apply
                    </button>
                @elseif(auth()->user()->account_status !== 'verified' || !auth()->user()->is_approved)
                    @if(auth()->user()->account_status === 'unverified' || !$jobseeker->isProfileAndDocumentsComplete())
                        @php
                            $missing = $jobseeker->getMissingRequirements();
                        @endphp
                        <div class="space-y-2 text-right max-w-md ml-auto">
                            <button type="button" 
                                    onclick="alert('You cannot apply for this job yet. Please complete your profile and submit the required documents in the Document Hub, then wait for Admin/JPO verification before applying.\n\nMissing requirements:\n- {{ implode('\n- ', $missing) }}')"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-amber-100 hover:bg-amber-200 text-amber-900 border border-amber-300 px-8 py-3.5 text-sm font-bold shadow-xs transition-colors">
                                <svg class="w-4 h-4 text-amber-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>Verification Required to Apply</span>
                            </button>
                            <div class="text-[11px] text-amber-900 bg-amber-50 border border-amber-200 rounded-xl p-3 text-left space-y-1.5 shadow-2xs">
                                <p class="font-extrabold text-amber-950 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <span>Documents & Verification Required:</span>
                                </p>
                                <p class="text-xs text-amber-800 leading-normal">
                                    You can browse job vacancies freely, but to apply you must first submit your valid documents in your Document Hub. Once verified by DMDP/JPO, job applications will be unlocked.
                                </p>
                                <div class="pt-1.5 flex items-center gap-3 font-black text-emerald-800 text-xs border-t border-amber-200">
                                    <a href="{{ route('jobseeker.documents') }}" class="underline hover:text-emerald-950">Upload in Document Hub &rarr;</a>
                                    <a href="{{ route('jobseeker.profile', ['tab' => 'edit']) }}" class="underline hover:text-emerald-950">Update Profile &rarr;</a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="space-y-1.5 text-right">
                            <button type="button" 
                                    onclick="alert('Your documents have been submitted and are currently under review by DMDP / JPO officers. You will be able to apply as soon as your account is verified.')"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-slate-200 cursor-not-allowed px-8 py-3.5 text-sm font-bold text-slate-600 border border-slate-300 shadow-sm">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>Application Locked (Verification Under Review)</span>
                            </button>
                            <p class="text-[11px] text-amber-700 font-semibold">Your documents are currently under evaluation by DMDP JPO officers.</p>
                        </div>
                    @endif
                @else
                    <button @click="applyModalOpen = true" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 px-10 py-3.5 text-sm font-black text-white shadow-xl shadow-emerald-600/30 transition-all hover:scale-105">
                        Submit Application
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                @endif
            </div>

        </div>

    </div>

    <div x-show="approvalWarningOpen" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="approvalWarningOpen = false" class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl border border-amber-200 space-y-5">
            <div class="flex items-start gap-3">
                <div class="h-12 w-12 rounded-2xl bg-amber-100 border border-amber-200 text-amber-800 flex items-center justify-center text-2xl shrink-0">⚠️</div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-amber-700">Action required</p>
                    <h3 class="text-xl font-black text-slate-900 mt-1">Approval Needed Before You Can Apply</h3>
                </div>
            </div>

            <p class="text-sm text-slate-600 leading-relaxed">
                Your jobseeker account is still pending admin approval. You need approval before you can apply for a job posting.
            </p>

            <div class="flex justify-end">
                <button type="button" @click="approvalWarningOpen = false" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-white text-xs font-black">
                    OK
                </button>
            </div>
        </div>
    </div>

    <!-- Application Modal -->
    <div x-show="applyModalOpen" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        
        <div @click.away="applyModalOpen = false" 
             class="w-full max-w-lg rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Confirm Application</span>
                    <h3 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $job->title }}</h3>
                    <p class="text-xs text-slate-500">{{ $company }}</p>
                </div>
                <button @click="applyModalOpen = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form action="{{ route('jobseeker.jobs.apply', $job->job_id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <div class="rounded-2xl bg-emerald-50/50 p-4 border border-emerald-100 space-y-2 text-xs">
                    <p class="font-bold text-slate-800">Your Applicant Profile:</p>
                    <p class="text-slate-600"><span class="font-semibold">Name:</span> {{ Auth::user()->full_name }}</p>
                    <p class="text-slate-600"><span class="font-semibold">Email:</span> {{ Auth::user()->email }}</p>
                    <p class="text-slate-600"><span class="font-semibold">Phone:</span> {{ $jobseeker->mobile_number ?? 'N/A' }}</p>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Attach Updated Resume (Optional)</label>
                        <span class="text-[11px] text-emerald-700 font-bold">Profile auto-attached</span>
                    </div>
                    <input type="file" name="resume" accept=".pdf,.doc,.docx" 
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-800 hover:file:bg-emerald-100">
                    <p class="text-[11px] text-slate-400">Accepted formats: PDF, DOC, DOCX (Max 5MB). If omitted, the employer will evaluate your application using your verified TrabaGo profile details and skills matrix.</p>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="applyModalOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-md">
                        Confirm Application
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Employer Verified Accreditation Details Modal -->
    <div x-show="accredModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="accredModalOpen = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-black text-emerald-800 uppercase tracking-widest block">DMDP Verified Enterprise</span>
                        <h3 class="text-lg font-black text-slate-900 leading-tight">{{ $company }}</h3>
                    </div>
                </div>
                <button @click="accredModalOpen = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 space-y-2 text-xs">
                <p class="font-bold text-emerald-950">Official Cebu City DMDP Accreditation Certificate</p>
                <p class="text-emerald-800 leading-relaxed">
                    This employer has undergone rigorous 4-stage legal credential verification by the City Government of Cebu Department of Manpower Development and Placement.
                </p>
            </div>

            <div class="space-y-2.5 text-xs">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <span class="font-bold text-slate-700 inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>SEC / DTI Registration</span>
                    </span>
                    <span class="font-bold text-emerald-700 inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Verified Legal Entity</span>
                    </span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <span class="font-bold text-slate-700 inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>Mayor's Business Permit</span>
                    </span>
                    <span class="font-bold text-emerald-700 inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Cebu City BPLO Authorized</span>
                    </span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <span class="font-bold text-slate-700 inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>BIR 2303 Tax Compliance</span>
                    </span>
                    <span class="font-bold text-emerald-700 inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Active Taxpayer (District 080)</span>
                    </span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <span class="font-bold text-slate-700 inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>PESD Supervisor Audit</span>
                    </span>
                    <span class="font-bold text-emerald-700 inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Endorsed & Approved</span>
                    </span>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                <button type="button" @click="accredModalOpen = false" class="px-5 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition-colors">
                    Close Verification
                </button>
            </div>

        </div>
    </div>

</div>
@endsection