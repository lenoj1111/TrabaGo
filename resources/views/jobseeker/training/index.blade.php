@extends('layouts.jobseeker')

@section('title', 'Training & Skill Certification - TrabaGo')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Hero Header in Emerald Theme -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="max-w-2xl space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    DMDP Skill Enhancement & Certification
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Upskill & Certify Your Profile</h1>
                <p class="text-sm text-slate-300">Take interactive course modules, pass the assessment quiz with &ge; 80%, and automatically add verified skills to your profile to unlock higher job match ratings!</p>
            </div>

            <div class="shrink-0 bg-white/10 backdrop-blur rounded-2xl p-5 border border-white/10 text-center">
                <span class="text-xs font-bold text-emerald-300 uppercase tracking-wider">Your Verified Skills</span>
                <p class="text-4xl font-black text-emerald-400 mt-1">{{ count($userSkills) }}</p>
                <a href="{{ route('jobseeker.profile') }}" class="text-[11px] text-slate-300 hover:text-emerald-300 underline mt-1 block">View Matrix &rarr;</a>
            </div>
        </div>

        <!-- Course Catalog Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($trainings as $training)
                @php
                    $enrollment = $training->enrollments->first();
                    $isCompleted = $enrollment && $enrollment->status === 'completed';
                    $isInProgress = $enrollment && $enrollment->status === 'in_progress';
                @endphp
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all flex flex-col justify-between gap-6">
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 px-3 py-0.5 text-xs font-bold">
                                {{ ucfirst($training->training_type ?: 'Online') }}
                            </span>
                            @if($isCompleted)
                                <span class="rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 px-3 py-0.5 text-xs font-extrabold flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    Certified
                                </span>
                            @elseif($isInProgress)
                                <span class="rounded-full bg-teal-100 text-teal-800 px-3 py-0.5 text-xs font-bold">
                                    In Progress
                                </span>
                            @endif
                        </div>

                        <h3 class="text-lg font-bold text-slate-900 leading-snug">
                            {{ $training->title }}
                        </h3>

                        <p class="text-xs text-slate-500 leading-relaxed line-clamp-3">
                            {{ $training->description ?: 'Comprehensive course training module designed to prepare you for industry-level expectations.' }}
                        </p>

                        <!-- Certified Skill Granted Badge -->
                        <div class="pt-2 border-t border-slate-100">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Certified Skill Granted:</span>
                            <div class="mt-1 flex flex-wrap gap-1.5">
                                <span class="rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 px-2.5 py-1 text-xs font-bold">
                                    🎓 {{ $training->title }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-4">
                        <span class="text-xs font-semibold text-slate-500">
                            {{ $training->topics->count() }} Modules &bull; {{ $training->duration_months ?? 1 }} Mo
                        </span>

                        <a href="{{ route('jobseeker.training.show', $training->training_id) }}" 
                           class="inline-flex items-center justify-center rounded-xl {{ $isCompleted ? 'bg-emerald-700 text-white' : 'bg-slate-900 hover:bg-emerald-600 text-white' }} px-4 py-2.5 text-xs font-bold transition-colors shadow-sm">
                            {{ $isCompleted ? 'Review Course' : ($isInProgress ? 'Continue' : 'Start Course') }}
                        </a>
                    </div>

                </div>
            @empty
                <div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-400">
                    No training courses available at the moment.
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
