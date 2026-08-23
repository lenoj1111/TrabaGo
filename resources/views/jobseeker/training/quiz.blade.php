@extends('layouts.jobseeker')

@section('title', 'Skill Assessment: ' . $training->title . ' - TrabaGo')

@section('content')
<div x-data="{
    questions: {{ json_encode($questions) }},
    answers: {},
    currentQuestion: 0,
    submitted: false,
    score: 0,
    passed: false,
    correctCount: 0,
    showReview: false,
    
    get totalQuestions() {
        return this.questions.length;
    },

    get answeredCount() {
        return Object.keys(this.answers).length;
    },

    get isAllAnswered() {
        return this.answeredCount === this.totalQuestions && this.totalQuestions > 0;
    },

    selectAnswer(qIndex, choiceIndex) {
        if (this.submitted) return;
        this.answers[qIndex] = choiceIndex;
    },

    goToQuestion(idx) {
        if (idx >= 0 && idx < this.totalQuestions) {
            this.currentQuestion = idx;
        }
    },

    isQuestionAnswered(idx) {
        return this.answers[idx] !== undefined && this.answers[idx] !== null;
    },

    calculateScore() {
        let correct = 0;
        this.questions.forEach((q, idx) => {
            let selected = this.answers[idx];
            if (selected !== undefined && selected !== null) {
                if (typeof q.answer === 'number') {
                    if (selected === q.answer) correct++;
                } else if (typeof q.answer === 'string') {
                    if (String(selected) === q.answer || (q.choices && String(q.choices[selected]).trim().toLowerCase() === q.answer.trim().toLowerCase())) {
                        correct++;
                    }
                } else if (selected == q.answer) {
                    correct++;
                }
            }
        });
        this.correctCount = correct;
        this.score = this.totalQuestions > 0 ? Math.round((correct / this.totalQuestions) * 100) : 100;
        this.passed = this.score >= 80;
        this.submitted = true;
    },

    resetQuiz() {
        this.answers = {};
        this.currentQuestion = 0;
        this.submitted = false;
        this.score = 0;
        this.passed = false;
        this.correctCount = 0;
        this.showReview = false;
    }
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-4xl space-y-8">
        
        <!-- Header -->
        <div class="flex items-center justify-between">
            <a href="{{ route('jobseeker.training.show', $training->training_id) }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-emerald-700 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Exit Assessment
            </a>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-3.5 py-1 rounded-full uppercase tracking-wider">
                    Passing Threshold: 80%
                </span>
                <span class="text-xs font-bold text-slate-500 bg-white border border-slate-200 px-3 py-1 rounded-full" x-text="totalQuestions + ' Total Questions'"></span>
            </div>
        </div>

        <!-- Quiz Container Card in Emerald Theme -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-10 shadow-sm space-y-8">
            
            <!-- Quiz Title & Progress Bar -->
            <div class="space-y-4 pb-6 border-b border-slate-100">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800 border border-emerald-200">
                            <span class="h-2 w-2 rounded-full bg-emerald-600 animate-pulse"></span>
                            Official Skill Certification Assessment
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ $training->title }}</h1>
                    </div>

                    <div class="text-left sm:text-right" x-show="!submitted">
                        <span class="text-xs font-bold text-slate-400 block uppercase tracking-wider">Progress</span>
                        <span class="text-sm font-black text-emerald-800" x-text="'Question ' + (currentQuestion + 1) + ' of ' + totalQuestions"></span>
                    </div>
                </div>
                
                <!-- Linear Progress Bar -->
                <div class="h-2.5 w-full overflow-hidden rounded-full bg-slate-100" x-show="!submitted">
                    <div class="h-full rounded-full bg-gradient-to-r from-emerald-600 to-teal-400 transition-all duration-300 shadow-sm"
                         :style="'width: ' + ((answeredCount / totalQuestions) * 100) + '%'"></div>
                </div>

                <!-- Interactive Question Navigator Pills -->
                <div class="flex flex-wrap items-center gap-2 pt-2" x-show="!submitted">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-1">Questions:</span>
                    <template x-for="(q, idx) in questions" :key="idx">
                        <button type="button" 
                                @click="goToQuestion(idx)"
                                class="h-9 w-9 rounded-xl text-xs font-bold transition-all flex items-center justify-center relative"
                                :class="{
                                    'bg-emerald-600 text-white shadow-md ring-2 ring-emerald-500/40': currentQuestion === idx,
                                    'bg-emerald-100 text-emerald-900 border border-emerald-300': currentQuestion !== idx && isQuestionAnswered(idx),
                                    'bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200': currentQuestion !== idx && !isQuestionAnswered(idx)
                                }">
                            <span x-text="idx + 1"></span>
                            <!-- Answered Dot Indicator -->
                            <span x-show="isQuestionAnswered(idx) && currentQuestion !== idx" 
                                  class="absolute -top-1 -right-1 h-3 w-3 rounded-full bg-emerald-500 border-2 border-white"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Active Question & Choices (When NOT submitted) -->
            <div x-show="!submitted" class="space-y-6">
                <template x-for="(q, qIdx) in questions" :key="qIdx">
                    <div x-show="currentQuestion === qIdx" class="space-y-5">
                        
                        <div class="flex items-start gap-3">
                            <span class="h-8 w-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-black shrink-0" x-text="qIdx + 1"></span>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 leading-snug pt-0.5" x-text="q.question"></h3>
                        </div>

                        <!-- Choices List -->
                        <div class="space-y-3 pt-2">
                            <template x-for="(choice, cIdx) in q.choices" :key="cIdx">
                                <button type="button" 
                                        @click="selectAnswer(qIdx, cIdx)"
                                        class="w-full p-4 rounded-2xl border text-left text-xs sm:text-sm font-semibold transition-all flex items-center justify-between group"
                                        :class="answers[qIdx] === cIdx ? 'bg-emerald-50 border-emerald-500 ring-2 ring-emerald-400/30 text-emerald-950 font-bold shadow-sm' : 'border-slate-200 hover:border-emerald-300 hover:bg-slate-50/80 text-slate-700'">
                                    <span class="flex items-center gap-3.5">
                                        <span class="h-7 w-7 rounded-lg flex items-center justify-center text-xs font-black shrink-0 transition-colors"
                                              :class="answers[qIdx] === cIdx ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 group-hover:bg-emerald-100 group-hover:text-emerald-800'"
                                              x-text="['A', 'B', 'C', 'D', 'E'][cIdx] || (cIdx + 1)"></span>
                                        <span x-text="choice" class="leading-relaxed"></span>
                                    </span>
                                    <div class="h-5 w-5 rounded-full border flex items-center justify-center shrink-0 ml-2"
                                         :class="answers[qIdx] === cIdx ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-300'">
                                        <svg x-show="answers[qIdx] === cIdx" class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Navigation Controls -->
                <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <button type="button" 
                                x-show="currentQuestion > 0" 
                                @click="currentQuestion--" 
                                class="flex-1 sm:flex-none px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                            &larr; Previous
                        </button>
                        
                        <button type="button" 
                                x-show="currentQuestion < totalQuestions - 1" 
                                @click="currentQuestion++" 
                                class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-colors">
                            Next Question &rarr;
                        </button>
                    </div>

                    <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                        <span class="text-xs text-slate-400 font-semibold" x-text="answeredCount + '/' + totalQuestions + ' Answered'"></span>

                        <button type="button" 
                                @click="calculateScore()" 
                                :disabled="!isAllAnswered"
                                class="w-full sm:w-auto px-8 py-3 rounded-xl font-black text-xs transition-all shadow-md"
                                :class="isAllAnswered ? 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-emerald-600/30 hover:scale-105 cursor-pointer' : 'bg-slate-200 text-slate-400 cursor-not-allowed'">
                            Submit Assessment ({{ count($questions) }} Questions)
                        </button>
                    </div>
                </div>

                <div x-show="!isAllAnswered" class="text-right">
                    <p class="text-[11px] text-amber-600 font-medium">Please answer all <span x-text="totalQuestions"></span> questions to unlock the assessment submission.</p>
                </div>
            </div>

            <!-- Quiz Results Screen (When submitted) -->
            <div x-show="submitted" x-cloak class="space-y-8 text-center py-4">
                
                <!-- Status Badge Icon -->
                <div class="mx-auto h-24 w-24 rounded-3xl flex items-center justify-center text-4xl shadow-xl transition-all"
                     :class="passed ? 'bg-emerald-100 text-emerald-800 border-2 border-emerald-400 ring-4 ring-emerald-500/20' : 'bg-amber-100 text-amber-800 border-2 border-amber-400 ring-4 ring-amber-500/20'">
                    <span x-text="passed ? '🏆' : '📖'"></span>
                </div>

                <div class="space-y-2">
                    <h2 class="text-3xl font-black text-slate-900" x-text="passed ? 'Congratulations! You Passed!' : 'Assessment Completed'"></h2>
                    <p class="text-sm text-slate-600 max-w-md mx-auto" 
                       x-text="passed ? 'You answered ' + correctCount + ' of ' + totalQuestions + ' questions correctly (' + score + '%). The certified skill has been permanently verified on your profile!' : 'You answered ' + correctCount + ' of ' + totalQuestions + ' questions correctly (' + score + '%). A minimum of 80% is required to earn the certificate. Review the questions and retake when ready!'"></p>
                </div>

                <!-- Final Score Metric Card -->
                <div class="inline-flex flex-col items-center p-6 rounded-3xl bg-emerald-50/60 border border-emerald-200 shadow-sm min-w-[260px]">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Final Assessment Score</span>
                    <span class="text-5xl font-black text-emerald-800 mt-1.5" x-text="score + '%'"></span>
                    <span class="mt-3 text-xs font-black px-4 py-1.5 rounded-full"
                          :class="passed ? 'bg-emerald-600 text-white shadow-xs' : 'bg-rose-100 text-rose-800 border border-rose-200'"
                          x-text="passed ? 'PASSED (>= 80%)' : 'NEEDS IMPROVEMENT (< 80%)'"></span>
                </div>

                <!-- Form to save score & automatically grant skill -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                    
                    <button type="button" 
                            @click="showReview = !showReview" 
                            class="w-full sm:w-auto px-6 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition-all flex items-center justify-center gap-2">
                        <span x-text="showReview ? 'Hide Answer Review' : 'Review Questions & Answers (' + totalQuestions + ')'"></span>
                    </button>

                    <button type="button" 
                            x-show="!passed" 
                            @click="resetQuiz()" 
                            class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition-all">
                        Retake Assessment
                    </button>

                    <form action="{{ route('jobseeker.training.quiz.submit', $training->training_id) }}" method="POST" class="w-full sm:w-auto">
                        @csrf
                        <input type="hidden" name="score" :value="score">
                        <input type="hidden" name="passed" :value="passed ? 1 : 0">
                        
                        <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs shadow-xl shadow-emerald-600/30 transition-all hover:scale-105">
                            <span x-text="passed ? 'Save Certification & Return to Course &rarr;' : 'Save Score & Return to Course &rarr;'"></span>
                        </button>
                    </form>
                </div>

                <!-- Review Breakdown Table -->
                <div x-show="showReview" x-cloak class="mt-8 pt-8 border-t border-slate-200 text-left space-y-4">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Assessment Question Review:</h3>
                    
                    <div class="space-y-4">
                        <template x-for="(q, idx) in questions" :key="idx">
                            @php
                                // Alpine handles review dynamic rendering
                            @endphp
                            <div class="p-5 rounded-2xl border transition-all"
                                 :class="answers[idx] === q.answer ? 'bg-emerald-50/50 border-emerald-200' : 'bg-rose-50/40 border-rose-200'">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-black px-2.5 py-0.5 rounded-md"
                                                  :class="answers[idx] === q.answer ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white'"
                                                  x-text="answers[idx] === q.answer ? 'Correct' : 'Incorrect'"></span>
                                            <span class="text-xs font-bold text-slate-500" x-text="'Question ' + (idx + 1)"></span>
                                        </div>
                                        <p class="text-sm font-bold text-slate-900" x-text="q.question"></p>
                                    </div>
                                </div>

                                <div class="mt-3 pt-3 border-t border-slate-200/60 space-y-1.5 text-xs">
                                    <p class="text-slate-600">
                                        <span class="font-bold text-slate-700">Your Selection:</span> 
                                        <span :class="answers[idx] === q.answer ? 'text-emerald-800 font-bold' : 'text-rose-700 font-bold'"
                                              x-text="q.choices[answers[idx]] || 'Not answered'"></span>
                                    </p>
                                    <p class="text-slate-600" x-show="answers[idx] !== q.answer">
                                        <span class="font-bold text-emerald-800">Correct Answer:</span> 
                                        <span class="text-emerald-800 font-semibold" x-text="q.choices[q.answer] || q.answer"></span>
                                    </p>
                                    <p class="text-[11px] text-slate-500 italic pt-1" x-show="q.explanation" x-text="'Note: ' + q.explanation"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
@endsection
