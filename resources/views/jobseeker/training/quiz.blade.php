@extends('layouts.jobseeker')

@section('title', 'Skill Assessment: ' . $training->title . ' - TrabaGo')

@section('content')
@if(empty($questions))
    <div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl space-y-6">
            <a href="{{ route('jobseeker.training.show', $training->training_id) }}" class="text-xs font-bold text-slate-600 hover:text-emerald-700">&larr; Back to Course</a>
            <div class="rounded-3xl border border-amber-300 bg-amber-50 p-8 text-center shadow-sm">
                <h1 class="text-xl font-black text-amber-950">Assessment not available yet</h1>
                <p class="mt-2 text-sm text-amber-900">The trainer has not added questions to this course yet. Please wait for the trainer to prepare the assessment.</p>
            </div>
        </div>
    </div>
@else
    <div x-data="{
        questions: @js($questions),
        answers: {},
        currentQuestion: 0,
        answered(question) {
            const response = this.answers[question.id];
            return question.type === 'essay'
                ? typeof response === 'string' && response.trim().length > 0
                : response !== undefined && response !== null;
        },
        get answeredCount() {
            return this.questions.filter(question => this.answered(question)).length;
        },
        get allAnswered() {
            return this.answeredCount === this.questions.length;
        }
    }" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl space-y-8">
            <div class="flex items-center justify-between">
                <a href="{{ route('jobseeker.training.show', $training->training_id) }}" class="text-xs font-bold text-slate-600 hover:text-emerald-700">&larr; Exit Assessment</a>
                <span class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-bold uppercase text-amber-800">Trainer review required</span>
            </div>

            @if($errors->has('responses'))
                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm font-semibold text-rose-800">{{ $errors->first('responses') }}</div>
            @endif

            <form action="{{ route('jobseeker.training.quiz.submit', $training->training_id) }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="responses" :value="JSON.stringify(answers)">

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-10">
                    <div class="space-y-5 border-b border-slate-100 pb-6">
                        <div>
                            <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-bold uppercase text-emerald-800">Skill Assessment</span>
                            <h1 class="mt-2 text-2xl font-black text-slate-900 sm:text-3xl">{{ $training->title }}</h1>
                        </div>
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <p class="text-xs font-semibold text-slate-500">
                                Question <span x-text="currentQuestion + 1"></span> of {{ count($questions) }}
                            </p>
                            <p class="text-xs font-semibold text-slate-500">
                                <span x-text="answeredCount"></span> of {{ count($questions) }} answered
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="(question, index) in questions" :key="question.id">
                                <button type="button" @click="currentQuestion = index" class="h-9 w-9 rounded-xl border text-xs font-bold"
                                    :class="currentQuestion === index ? 'border-emerald-600 bg-emerald-600 text-white' : (answered(question) ? 'border-emerald-300 bg-emerald-50 text-emerald-800' : 'border-slate-200 bg-slate-50 text-slate-600')"
                                    x-text="index + 1"></button>
                            </template>
                        </div>
                    </div>

                    <div class="py-8">
                        <template x-for="(question, index) in questions" :key="question.id">
                            <section x-show="currentQuestion === index" class="space-y-5">
                                <div class="flex items-start gap-3">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-sm font-black text-emerald-800" x-text="index + 1"></span>
                                    <div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-700" x-text="question.type === 'essay' ? 'Essay response' : 'Multiple choice'"></span>
                                        <h2 class="mt-1 text-base font-bold leading-relaxed text-slate-900 sm:text-lg" x-text="question.question"></h2>
                                    </div>
                                </div>

                                <template x-if="question.type === 'essay'">
                                    <div class="space-y-2 pl-11">
                                        <textarea :name="'essay_' + question.id" x-model="answers[question.id]" rows="8" maxlength="5000"
                                            placeholder="Write your response here..."
                                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400"></textarea>
                                        <p class="text-[11px] text-slate-400">Your written response will be reviewed by the trainer.</p>
                                    </div>
                                </template>

                                <template x-if="question.type !== 'essay'">
                                    <div class="space-y-3 pl-11">
                                        <template x-for="(choice, choiceIndex) in question.choices" :key="choiceIndex">
                                            <button type="button" @click="answers[question.id] = choiceIndex"
                                                class="flex w-full items-center gap-3 rounded-2xl border p-4 text-left text-sm font-semibold transition-colors"
                                                :class="answers[question.id] === choiceIndex ? 'border-emerald-500 bg-emerald-50 text-emerald-950' : 'border-slate-200 text-slate-700 hover:border-emerald-300 hover:bg-slate-50'">
                                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-xs font-black text-slate-600" x-text="String.fromCharCode(65 + choiceIndex)"></span>
                                                <span x-text="choice"></span>
                                            </button>
                                        </template>
                                    </div>
                                </template>
                            </section>
                        </template>
                    </div>

                    <div class="flex flex-col justify-between gap-3 border-t border-slate-100 pt-5 sm:flex-row">
                        <button type="button" @click="currentQuestion = Math.max(0, currentQuestion - 1)" :disabled="currentQuestion === 0"
                            class="rounded-xl border border-slate-200 px-5 py-2.5 text-xs font-bold text-slate-700 disabled:opacity-40">Previous</button>
                        <button type="button" x-show="currentQuestion < questions.length - 1" @click="currentQuestion++"
                            class="rounded-xl bg-slate-900 px-5 py-2.5 text-xs font-bold text-white hover:bg-slate-800">Next Question</button>
                        <div x-show="currentQuestion === questions.length - 1" class="space-y-2 sm:text-right">
                            <p x-show="!allAnswered" class="text-xs font-semibold text-amber-700">Answer every question before submitting.</p>
                            <button type="submit" :disabled="!allAnswered"
                                class="rounded-xl bg-emerald-600 px-7 py-3 text-xs font-black text-white shadow-lg shadow-emerald-600/20 hover:bg-emerald-500 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:shadow-none">
                                Submit for Trainer Review
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <p class="text-center text-xs text-slate-500">Your certificate remains pending until the trainer reviews your answers and approves the result.</p>
        </div>
    </div>
@endif
@endsection
