@extends('layouts.trainer')

@section('title', 'Conduct Assessment - Skills Trainer')

@section('content')
<div x-data="{
    theoryScore: {{ isset($answers['theory_score']) ? $answers['theory_score'] : 85 }},
    practicalScore: {{ isset($answers['practical_score']) ? $answers['practical_score'] : 90 }},
    passingScore: {{ $enrollment->passing_score ?: 80 }},
    get finalScore() {
        return Math.round(((parseFloat(this.theoryScore) || 0) * 0.4) + ((parseFloat(this.practicalScore) || 0) * 0.6));
    }
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-4xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Figure 12: Conduct Competency Assessment
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Conduct Learner Assessment</h1>
                <p class="text-sm text-slate-300">Evaluate theoretical understanding and practical laboratory competencies to qualify learner for course completion and skills certification.</p>
            </div>

            <a href="{{ route('trainer.enrollments.index') }}" class="px-5 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-colors">
                &larr; Back to Registry
            </a>
        </div>

        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2">
                <span>✕</span> {{ session('error') }}
            </div>
        @endif

        <!-- Learner Info & Course Banner -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="space-y-1">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Candidate Information</span>
                <h3 class="text-xl font-black text-slate-900">{{ $enrollment->first_name }} {{ $enrollment->last_name }}</h3>
                <p class="text-xs text-slate-500">{{ $enrollment->jobseeker_email }}</p>
                @if($enrollment->jobseeker_phone)
                    <p class="text-xs text-slate-400 font-mono">{{ $enrollment->jobseeker_phone }}</p>
                @endif
                <div class="pt-2">
                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-bold bg-slate-100 text-slate-700">
                        Current Status: {{ ucfirst(str_replace('_', ' ', $enrollment->status)) }}
                    </span>
                </div>
            </div>

            <div class="space-y-1">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Course & Competency Track</span>
                <h3 class="text-xl font-black text-emerald-800">{{ $enrollment->course_title }}</h3>
                <p class="text-xs text-slate-500 line-clamp-2">{{ $enrollment->course_desc }}</p>
                <div class="pt-2 flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-600">Passing Requirement:</span>
                    <span class="px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 font-black text-xs border border-emerald-200">
                        {{ $enrollment->passing_score ?: 80 }}% Score
                    </span>
                </div>
            </div>
        </div>

        <!-- Trainer-Authored Assessment Items Review -->
        @if($assessments->count() > 0)
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-black text-slate-900">Course Assessment Questions</h2>
                        <p class="text-xs text-slate-500">Trainer-authored questions assigned to this course curriculum.</p>
                    </div>
                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-xl border border-emerald-200">
                        {{ $assessments->count() }} Questions
                    </span>
                </div>

                <div class="space-y-3">
                    @foreach($assessments as $qIndex => $q)
                        @php
                            $opts = is_array($q->options) ? $q->options : (json_decode($q->options ?? '[]', true) ?: []);
                        @endphp
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900">Item {{ $qIndex + 1 }}: {{ $q->question }}</span>
                                <span class="text-[10px] font-bold text-slate-400">Correct: Choice {{ is_numeric($q->correct_answer) ? (chr(65 + (int)$q->correct_answer)) : strtoupper($q->correct_answer) }}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-slate-600">
                                @foreach($opts as $optIdx => $optVal)
                                    <div><strong class="text-slate-800">{{ chr(65 + $optIdx) }}:</strong> {{ $optVal }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Assessment Grading Form -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-lg font-black text-slate-900">Competency Assessment Grading Rubric</h2>
                <p class="text-xs text-slate-500">Record theoretical test marks and practical hands-on demonstration evaluation.</p>
            </div>

            <form action="{{ route('trainer.enrollments.assessment.submit', $enrollment->enrollment_id) }}" method="POST" class="space-y-6">
                @csrf

                <!-- Score Rubric Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    
                    <!-- 1. Theoretical Exam Score (40% Weight) -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Theory / Written Exam (40%)
                        </label>
                        <input type="number" name="theory_score" x-model="theoryScore" min="0" max="100" step="1" required
                               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-black text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        <p class="text-[11px] text-slate-400">Knowledge & module comprehension</p>
                    </div>

                    <!-- 2. Practical Skills Demonstration (60% Weight) -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Practical Demonstration (60%)
                        </label>
                        <input type="number" name="practical_score" x-model="practicalScore" min="0" max="100" step="1" required
                               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-black text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        <p class="text-[11px] text-slate-400">Hands-on laboratory execution</p>
                    </div>

                    <!-- 3. Final Calculated Score -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Final Composite Score (%) *
                        </label>
                        <input type="number" name="score" :value="finalScore" min="0" max="100" step="0.1" required
                               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-black text-slate-900 bg-slate-50 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        <p class="text-[11px] text-slate-400">Combined grade</p>
                    </div>

                </div>

                <!-- Outcome Preview Box -->
                <div class="p-4 rounded-2xl border transition-all"
                     :class="finalScore >= passingScore ? 'bg-emerald-50 border-emerald-200 text-emerald-950' : 'bg-rose-50 border-rose-200 text-rose-950'">
                    <div class="flex items-center justify-between text-xs font-bold">
                        <span class="flex items-center gap-2">
                            <span class="text-base" x-text="finalScore >= passingScore ? '🎓' : '⚠️'"></span>
                            <span>Evaluation Assessment Outcome:</span>
                            <span class="uppercase font-black" x-text="finalScore >= passingScore ? 'PASSED & COMPLETED' : 'FAILED / NEEDS RETAKE'"></span>
                        </span>
                        <span x-text="'Required: ' + passingScore + '%'"></span>
                    </div>
                </div>

                <!-- Lab Attendance Remarks -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Laboratory Observation & Attendance Remarks
                    </label>
                    <input type="text" name="lab_remarks" value="{{ $enrollment->lab_remarks ?? '' }}" placeholder="e.g. Attended all workshop sessions, demonstrated safety protocols..."
                           class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>

                <!-- Trainer Practical Feedback -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Trainer Assessment Feedback & Recommendations
                    </label>
                    <textarea name="trainer_feedback" rows="3" placeholder="Detailed feedback regarding candidate competencies, technical proficiencies, and readiness for employment..."
                              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">{{ $enrollment->trainer_feedback ?? '' }}</textarea>
                </div>

                <!-- Actions -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('trainer.enrollments.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                        Submit Assessment & Record Grade &rarr;
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
