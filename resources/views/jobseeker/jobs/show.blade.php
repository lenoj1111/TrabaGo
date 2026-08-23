@extends('layouts.jobseeker')

@section('title', $job->title . ' - TrabaGo AI Match')

@section('content')
<div x-data="{ applyModalOpen: false, accredModalOpen: false }" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-4xl space-y-8">
        
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
                                <span>🛡️</span>
                                <span>DMDP Verified Employer</span>
                                <span class="text-[10px] text-emerald-600">↗</span>
                            </button>
                        @endif
                        @if ($job->accepts_disability)
                            <span class="rounded-full bg-teal-50 text-teal-800 border border-teal-200 px-2.5 py-0.5 text-xs font-bold">
                                ♿ PWD Inclusive ({{ $job->disability_type ?: 'All eligible' }})
                            </span>
                        @endif
                    </div>

                    <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">{{ $job->title }}</h1>
                    
                    <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-slate-500 pt-1">
                        <span class="flex items-center gap-1">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            Cebu City, Philippines
                        </span>
                        <span>&bull;</span>
                        <span class="font-bold text-slate-900">₱18,000 - ₱35,000 / month</span>
                        <span>&bull;</span>
                        <span>Full-time Position</span>
                    </div>
                </div>

                <!-- Match Score Badge in Emerald Theme -->
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
                    <!-- Matched Skills -->
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

                    <!-- Missing Skills -->
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

            <!-- Qualifications -->
            @if ($job->qualifications)
                <div class="space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Qualifications & Requirements</h3>
                    <div class="rounded-2xl bg-slate-50 p-5 text-xs text-slate-800 leading-relaxed font-mono whitespace-pre-line border border-slate-200">
                        {{ $job->qualifications }}
                    </div>
                </div>
            @endif

            <!-- Suggested Training Modules -->
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
                @else
                    <button @click="applyModalOpen = true" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 px-10 py-3.5 text-sm font-black text-white shadow-xl shadow-emerald-600/30 transition-all hover:scale-105">
                        Submit Application
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                @endif
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
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Attach Updated Resume (Optional)</label>
                    <input type="file" name="resume" accept=".pdf,.doc,.docx" 
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-800 hover:file:bg-emerald-100">
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
                    <div class="h-10 w-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-xl font-bold">
                        🛡️
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
                    <span class="font-bold text-slate-700">📜 SEC / DTI Registration</span>
                    <span class="font-bold text-emerald-700">✓ Verified Legal Entity</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <span class="font-bold text-slate-700">🏢 Mayor's Business Permit</span>
                    <span class="font-bold text-emerald-700">✓ Cebu City BPLO Authorized</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <span class="font-bold text-slate-700">📑 BIR 2303 Tax Compliance</span>
                    <span class="font-bold text-emerald-700">✓ Active Taxpayer (District 080)</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <span class="font-bold text-slate-700">🛡️ PESD Supervisor Audit</span>
                    <span class="font-bold text-emerald-700">✓ Endorsed & Approved</span>
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
