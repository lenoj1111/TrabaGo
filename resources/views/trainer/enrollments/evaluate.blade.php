@extends('layouts.trainer')

@section('title', 'Evaluate Training Course Answers - Skills Trainer')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-4xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Figure 12: Evaluate Training Course Answer
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Assess Learner Submission</h1>
                <p class="text-sm text-slate-300">Grade quiz responses, review practical comprehension, provide trainer feedback, and determine passing qualification for certification.</p>
            </div>

            <a href="{{ route('trainer.enrollments.index') }}" class="px-5 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-colors">
                &larr; Back to Registry
            </a>
        </div>

        <!-- Learner Info & Course Banner -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="space-y-1">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Learner Candidate</span>
                <h3 class="text-xl font-black text-slate-900">{{ $enrollment->first_name }} {{ $enrollment->last_name }}</h3>
                <p class="text-xs text-slate-500">{{ $enrollment->jobseeker_email }}</p>
            </div>

            <div class="space-y-1">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Course Track</span>
                <h3 class="text-xl font-black text-emerald-800">{{ $enrollment->course_title }}</h3>
                <p class="text-xs text-slate-500 line-clamp-1">{{ $enrollment->course_desc }}</p>
            </div>
        </div>

        <!-- Submitted Answers & Quiz Evaluation -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-lg font-black text-slate-900">Submitted Course Answers & Assessment Review</h2>
                <p class="text-xs text-slate-500">Review student answers submitted during training.</p>
            </div>

            @if(is_array($answers) && count($answers) > 0)
                <div class="space-y-4">
                    @foreach($answers as $idx => $ans)
                        <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/60 space-y-2">
                            <div class="flex items-center justify-between text-xs font-bold">
                                <span class="text-slate-900">Question {{ is_numeric($idx) ? ($idx + 1) : $idx }}</span>
                                @if(is_array($ans) && isset($ans['is_correct']))
                                    <span class="{{ $ans['is_correct'] ? 'text-emerald-700' : 'text-rose-600' }}">
                                        {{ $ans['is_correct'] ? '✓ Auto-Validated Correct' : '✕ Incorrect' }}
                                    </span>
                                @endif
                            </div>
                            
                            @if(is_array($ans))
                                <p class="text-xs text-slate-700 font-semibold">{{ $ans['question'] ?? 'Answer detail:' }}</p>
                                <p class="text-xs text-slate-900 bg-white p-3 rounded-xl border border-slate-200">
                                    <strong class="text-emerald-800">Student Response:</strong> {{ $ans['selected_answer'] ?? ($ans['answer'] ?? json_encode($ans)) }}
                                </p>
                            @else
                                <p class="text-xs text-slate-900 bg-white p-3 rounded-xl border border-slate-200">
                                    {{ $ans }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 rounded-2xl bg-amber-50/60 border border-amber-200 text-center text-xs text-amber-900 space-y-1">
                    <p class="font-bold">No digital quiz breakdown recorded for this enrollment yet.</p>
                    <p class="text-amber-700">You can grade the student based on laboratory performance, attendance, and practical demonstrations below.</p>
                </div>
            @endif

            <!-- Trainer Grading Form -->
            <form action="{{ route('trainer.enrollments.evaluate.submit', $enrollment->enrollment_id) }}" method="POST" class="pt-6 border-t border-slate-100 space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Final Assessment Score (%) *</label>
                        <input type="number" name="score" min="0" max="100" step="0.1" value="{{ $enrollment->score ?? 85 }}" required
                               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-black text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        <p class="text-[11px] text-slate-400">Passing threshold is 80.0% or higher.</p>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Qualification Outcome</label>
                        <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs font-bold text-emerald-950 flex items-center gap-2">
                            <span>🎓 Scores &ge; 80% mark as Completed & qualify for Certificate.</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Trainer Evaluation Remarks & Practical Feedback</label>
                    <textarea name="trainer_feedback" rows="3" placeholder="Provide feedback on learner strengths, practical capabilities, and areas for professional growth..."
                              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">{{ $enrollment->trainer_feedback ?? '' }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('trainer.enrollments.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                        Save Evaluation & Grade Learner &rarr;
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
