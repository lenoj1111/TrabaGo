@extends('layouts.trainer')

@section('title', 'Conduct Assessment - Skills Trainer')

@section('content')
<div x-data="{ selectedOutcome: null }" class="min-h-screen bg-slate-50/80 px-4 py-6 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-5xl space-y-5">

        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:px-8 sm:py-6 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-[10px] font-bold text-emerald-300 border border-emerald-400/30 uppercase tracking-wider">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Laboratory / Onsite Evaluation
                </span>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Update Learner Status</h1>
            </div>

            <a href="{{ route('trainer.enrollments.index') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-colors shrink-0">
                &larr; Back to Registry
            </a>
        </div>

        @if(session('error'))
        <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2">
            <svg class="h-4 w-4 text-rose-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        <!-- 2-Column Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

            <!-- LEFT COL: Candidate Info -->
            <div class="lg:col-span-4 space-y-5">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Candidate Information</span>
                        <h3 class="text-xl font-black text-slate-900 mt-1">{{ $enrollment->first_name }} {{ $enrollment->last_name }}</h3>
                        <p class="text-xs text-slate-500">{{ $enrollment->jobseeker_email }}</p>
                        @if($enrollment->jobseeker_phone)
                        <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $enrollment->jobseeker_phone }}</p>
                        @endif
                    </div>

                    <div class="pt-3 border-t border-slate-100">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">Current Status</span>
                        <span class="inline-flex items-center gap-1 rounded-lg px-3 py-1 text-xs font-bold bg-slate-100 text-slate-700">
                            {{ ucfirst(str_replace('_', ' ', $enrollment->status)) }}
                        </span>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Course Track</span>
                    <h3 class="text-base font-black text-emerald-800 leading-tight">{{ $enrollment->course_title }}</h3>
                    <p class="text-xs text-slate-500 leading-relaxed line-clamp-4">{{ $enrollment->course_desc }}</p>
                </div>
            </div>

            <!-- RIGHT COL: Evaluation Form -->
            <div class="lg:col-span-8">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm">
                    <div class="border-b border-slate-100 pb-3 mb-5">
                        <h2 class="text-lg font-black text-slate-900">Final Status Evaluation</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Because this is a laboratory course, there is no written exam. Evaluate the practical output below.</p>
                    </div>

                    <form action="{{ route('trainer.enrollments.assessment.submit', $enrollment->enrollment_id) }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- Pass / Fail Selection (Large Cards) -->
                        <div class="space-y-2">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700">Final Evaluation Outcome *</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                                <!-- PASS -->
                                <label class="relative flex cursor-pointer rounded-2xl border p-4 shadow-sm transition-all text-center"
                                    :class="selectedOutcome === '100' ? 'border-emerald-600 bg-emerald-50 ring-2 ring-emerald-500' : 'border-slate-200 bg-white hover:border-emerald-300'">
                                    <input type="radio" name="score" value="100" x-model="selectedOutcome" class="sr-only" required>
                                    <div class="flex flex-col items-center gap-2 w-full">
                                        <div class="h-10 w-10 rounded-full flex items-center justify-center transition-colors"
                                            :class="selectedOutcome === '100' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-400'">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="block text-sm font-black transition-colors" :class="selectedOutcome === '100' ? 'text-emerald-800' : 'text-slate-900'">Passed</span>
                                            <span class="block text-[10px] text-slate-500 mt-0.5">Demonstrated required skills</span>
                                        </div>
                                    </div>
                                </label>

                                <!-- FAIL -->
                                <label class="relative flex cursor-pointer rounded-2xl border p-4 shadow-sm transition-all text-center"
                                    :class="selectedOutcome === '0' ? 'border-rose-600 bg-rose-50 ring-2 ring-rose-500' : 'border-slate-200 bg-white hover:border-rose-300'">
                                    <input type="radio" name="score" value="0" x-model="selectedOutcome" class="sr-only" required>
                                    <div class="flex flex-col items-center gap-2 w-full">
                                        <div class="h-10 w-10 rounded-full flex items-center justify-center transition-colors"
                                            :class="selectedOutcome === '0' ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-400'">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="block text-sm font-black transition-colors" :class="selectedOutcome === '0' ? 'text-rose-800' : 'text-slate-900'">Needs Retake</span>
                                            <span class="block text-[10px] text-slate-500 mt-0.5">Requires more practice</span>
                                        </div>
                                    </div>
                                </label>

                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <!-- Lab Attendance Remarks -->
                            <div class="space-y-1">
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-700">Laboratory Remarks</label>
                                <input type="text" name="lab_remarks" value="{{ $enrollment->lab_remarks ?? '' }}" placeholder="e.g. Attended all workshop sessions, demonstrated safety protocols..."
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-400">
                            </div>

                            <!-- Trainer Practical Feedback -->
                            <div class="space-y-1">
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-700">Trainer Feedback & Recommendations</label>
                                <textarea name="trainer_feedback" rows="3" placeholder="Detailed feedback regarding technical proficiencies and readiness..."
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-400">{{ $enrollment->trainer_feedback ?? '' }}</textarea>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                            <a href="{{ route('trainer.enrollments.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-md shadow-emerald-600/20 transition-all hover:scale-105">
                                Submit Final Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection