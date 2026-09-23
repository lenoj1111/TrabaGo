@extends('layouts.jobseeker')

@section('title', 'Training & Skill Certification - TrabaGo')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Hero Header in green Theme -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-green-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-green-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="max-w-2xl space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-400/20 px-3 py-1 text-xs font-bold text-green-300 border border-green-400/30">
                    <span class="h-2 w-2 rounded-full bg-green-400"></span>
                    DMDP Skill Enhancement & Certification
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Upskill & Certify Your Profile</h1>
                <p class="text-sm text-slate-300">Enroll in vocational skill courses, complete interactive lesson modules, pass competency quiz assessments, and automatically earn verified skills on your TrabaGo profile!</p>
            </div>

            <div class="shrink-0 bg-white/10 backdrop-blur rounded-2xl p-5 border border-white/10 text-center">
                <span class="text-xs font-bold text-green-300 uppercase tracking-wider">Your Verified Skills</span>
                <p class="text-4xl font-black text-green-400 mt-1">{{ count($userSkills) }}</p>
                <a href="{{ route('jobseeker.profile', ['tab' => 'skills']) }}" class="text-[11px] text-slate-300 hover:text-green-300 underline mt-1 block">View Matrix &rarr;</a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-green-50 border border-green-200 text-green-800 text-xs font-bold flex items-center gap-2">
                <span>✓</span> {{ session('success') }}
            </div>
        @endif

        @if(session('info'))
            <div class="p-4 rounded-2xl bg-green-50 border border-green-200 text-green-800 text-xs font-bold flex items-center gap-2">
                <span>ℹ️</span> {{ session('info') }}
            </div>
        @endif

        <!-- Filter Status Tabs (Combined Training & Skills Enrollment) -->
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200 pb-4">
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('jobseeker.training') }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ ($filter ?? 'all') === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                    <span>All Courses & Skills</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ ($filter ?? 'all') === 'all' ? 'bg-slate-700 text-slate-200' : 'bg-slate-100 text-slate-700' }}">
                        {{ $counts['all'] ?? count($trainings) }}
                    </span>
                </a>

                <a href="{{ route('jobseeker.training', ['filter' => 'enrolled']) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ ($filter ?? '') === 'enrolled' ? 'bg-green-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                    <span>📚 Enrolled Courses</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ ($filter ?? '') === 'enrolled' ? 'bg-green-800 text-white' : 'bg-green-50 text-green-800' }} font-bold">
                        {{ $counts['enrolled'] ?? 0 }}
                    </span>
                </a>

                <a href="{{ route('jobseeker.training', ['filter' => 'completed']) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ ($filter ?? '') === 'completed' ? 'bg-green-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                    <span>🎓 Completed & Certified</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ ($filter ?? '') === 'completed' ? 'bg-green-800 text-white' : 'bg-green-50 text-green-800' }}">
                        {{ $counts['completed'] ?? 0 }}
                    </span>
                </a>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('jobseeker.documents') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 shadow-xs">
                    <span>📜</span> View Earned Certificates
                </a>
            </div>
        </div>

        <!-- Course Catalog Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($trainings as $training)
                @php
                    $enrollment = $training->enrollments->first();
                    $isCompleted = $enrollment && $enrollment->status === 'completed';
                    $isInProgress = $enrollment && $enrollment->status === 'in_progress';
                    $isEnrolled = $enrollment && in_array($enrollment->status, ['enrolled', 'in_progress', 'completed']);
                @endphp
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:border-green-300 transition-all flex flex-col justify-between gap-6">
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="rounded-full bg-green-50 text-green-800 border border-green-200 px-3 py-0.5 text-xs font-bold">
                                {{ ucfirst($training->training_type ?: 'Online') }}
                            </span>
                            @if($isCompleted)
                                <span class="rounded-full bg-green-100 text-green-800 border border-green-300 px-3 py-0.5 text-xs font-extrabold flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    Certified
                                </span>
                            @elseif($isInProgress)
                                <span class="rounded-full bg-green-100 text-green-800 px-3 py-0.5 text-xs font-bold">
                                    In Progress
                                </span>
                            @elseif($isEnrolled)
                                <span class="rounded-full bg-amber-100 text-amber-800 px-3 py-0.5 text-xs font-bold">
                                    Enrolled
                                </span>
                            @else
                                <span class="rounded-full bg-slate-100 text-slate-600 px-3 py-0.5 text-xs font-semibold">
                                    Open for Enrollment
                                </span>
                            @endif
                        </div>

                        <h3 class="text-lg font-bold text-slate-900 leading-snug">
                            <a href="{{ route('jobseeker.training.show', $training->training_id) }}" class="hover:text-green-700 transition-colors">
                                {{ $training->title }}
                            </a>
                        </h3>

                        <p class="text-xs text-slate-500 leading-relaxed line-clamp-3">
                            {{ $training->description ?: 'Comprehensive course training module designed to prepare you for industry-level expectations.' }}
                        </p>

                        <!-- Certified Skill Granted Badge -->
                        <div class="pt-2 border-t border-slate-100">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Skill Credential:</span>
                            <div class="mt-1 flex flex-wrap gap-1.5">
                                <span class="rounded-lg bg-green-50 text-green-800 border border-green-200 px-2.5 py-1 text-xs font-bold">
                                    🎓 {{ $training->title }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                        <span class="text-xs font-semibold text-slate-500">
                            {{ $training->topics->count() }} Modules &bull; {{ $training->duration_months ?? 1 }} Mo
                        </span>

                        <div class="flex items-center gap-2">
                            @if($isCompleted)
                                <a href="{{ route('jobseeker.training.show', $training->training_id) }}" 
                                   class="inline-flex items-center justify-center rounded-xl bg-green-700 hover:bg-green-800 text-white px-4 py-2.5 text-xs font-bold transition-colors shadow-sm">
                                    Review Course
                                </a>
                            @elseif($isEnrolled)
                                <a href="{{ route('jobseeker.training.show', $training->training_id) }}" 
                                   class="inline-flex items-center justify-center rounded-xl bg-green-600 hover:bg-green-500 text-white px-4 py-2.5 text-xs font-black transition-colors shadow-sm">
                                    Continue &rarr;
                                </a>
                            @else
                                <form action="{{ route('jobseeker.training.enroll', $training->training_id) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-green-600 hover:bg-green-500 text-white px-4 py-2.5 text-xs font-black shadow-md shadow-green-600/25 transition-all hover:scale-105 cursor-pointer">
                                        <span>+</span> Enroll Now
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                </div>
            @empty
                <div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-400 space-y-2">
                    <p class="text-sm font-bold text-slate-700">No courses found matching this category.</p>
                    <a href="{{ route('jobseeker.training') }}" class="inline-flex text-xs font-bold text-green-700 hover:underline">
                        View All Available Courses
                    </a>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
