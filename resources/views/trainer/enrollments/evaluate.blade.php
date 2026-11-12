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

            @if(!empty($answers['responses']))
                @php($objectiveScore = $answers['objective_score'] ?? [])
                <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4 text-sm font-bold text-indigo-950">
                    @if(($objectiveScore['question_count'] ?? 0) > 0)
                        Auto-scored: {{ $objectiveScore['correct_count'] }}/{{ $objectiveScore['question_count'] }} multiple-choice questions
                        ({{ $objectiveScore['earned_points'] }}/{{ $objectiveScore['total_points'] }} points, {{ $objectiveScore['percentage'] }}%).
                    @else
                        This assessment contains essay responses only; review them below and choose the final outcome.
                    @endif
                </div>
                <div class="space-y-4">
                    @foreach($answers['responses'] as $idx => $answer)
                        <article class="space-y-3 rounded-2xl border border-slate-200 bg-slate-50/60 p-5">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="text-xs font-black text-slate-900">Question {{ $idx + 1 }}: {{ $answer['question'] }}</span>
                                <span class="rounded-full bg-white px-2.5 py-1 text-[10px] font-bold uppercase text-slate-600">{{ $answer['question_type'] === 'essay' ? 'Essay' : 'Multiple choice' }} · {{ $answer['points'] }} pt(s)</span>
                            </div>
                            @if($answer['question_type'] === 'essay')
                                <div class="rounded-xl border border-indigo-200 bg-white p-4">
                                    <p class="mb-1 text-[10px] font-black uppercase tracking-wider text-indigo-700">Jobseeker essay response</p>
                                    <p class="whitespace-pre-wrap text-sm leading-relaxed text-slate-800">{{ $answer['response'] }}</p>
                                </div>
                            @else
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <p class="rounded-xl border border-slate-200 bg-white p-3 text-xs text-slate-800">
                                        <strong>Your answer:</strong> {{ $answer['response'] }}
                                    </p>
                                    <p class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-900">
                                        <strong>Correct answer:</strong> {{ $answer['correct_answer'] }}
                                    </p>
                                </div>
                                <p class="text-xs font-bold {{ $answer['is_correct'] ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ $answer['is_correct'] ? 'Correct' : 'Incorrect' }}
                                    @if($answer['is_correct']) · {{ $answer['awarded_points'] }}/{{ $answer['points'] }} points @endif
                                </p>
                            @endif
                            @if(!empty($answer['explanation']))
                                <p class="text-xs text-slate-600"><strong>Trainer rationale:</strong> {{ $answer['explanation'] }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            @else
                <div class="p-8 rounded-2xl bg-amber-50/60 border border-amber-200 text-center text-xs text-amber-900 space-y-1">
                    <p class="font-bold">No submitted quiz answers are recorded for this enrollment.</p>
                </div>
            @endif

            <!-- Trainer Grading Form -->
            <form action="{{ route('trainer.enrollments.evaluate.submit', $enrollment->enrollment_id) }}" method="POST" class="pt-6 border-t border-slate-100 space-y-6">
                @csrf

                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Trainer Decision *</label>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                            <input type="radio" name="outcome" value="passed" required class="mt-0.5 text-emerald-600">
                            <span><strong class="block text-sm text-emerald-900">Passed</strong><small class="text-xs text-emerald-800">Approve course completion and certificate eligibility.</small></span>
                        </label>
                        <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4">
                            <input type="radio" name="outcome" value="needs_retake" required class="mt-0.5 text-amber-600">
                            <span><strong class="block text-sm text-amber-900">Needs Retake</strong><small class="text-xs text-amber-800">Keep the certificate pending and allow a new quiz submission.</small></span>
                        </label>
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
