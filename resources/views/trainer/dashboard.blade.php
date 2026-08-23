@extends('layouts.trainer')

@section('title', 'Trainer Command Center - TrabaGo DMDP')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    DMDP Skills Training & Certification
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Trainer Command Center</h1>
                <p class="text-sm text-slate-300">Oversee jobseeker course enrollments, evaluate quiz and practical assessments, update progress statuses, and issue official completion certificates.</p>
            </div>
        </div>

        <!-- 4 Metric Cards (Figure 12) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Enrolled</span>
                    <p class="text-3xl font-black text-slate-900">{{ $totalEnrollments }}</p>
                    <span class="text-[11px] text-slate-500">Across {{ $coursesCount }} courses</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center justify-center text-xl font-black">
                    👥
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">In Progress</span>
                    <p class="text-3xl font-black text-amber-600">{{ $inProgressEnrollments }}</p>
                    <span class="text-[11px] text-amber-700">Active learners</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-800 border border-amber-200 flex items-center justify-center text-xl font-black">
                    ⏳
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Completed</span>
                    <p class="text-3xl font-black text-teal-700">{{ $completedEnrollments }}</p>
                    <span class="text-[11px] text-teal-800">Passed assessments</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-teal-50 text-teal-800 border border-teal-200 flex items-center justify-center text-xl font-black">
                    ✓
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Certs Generated</span>
                    <p class="text-3xl font-black text-emerald-700">{{ $certificatesIssued }}</p>
                    <span class="text-[11px] text-emerald-800">Skills certified</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center justify-center text-xl font-black">
                    🎓
                </div>
            </div>

        </div>

        <!-- 3 Primary Trainer Workflow Actions (Figure 12) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <a href="{{ route('trainer.enrollments.index') }}" 
               class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-xl hover:border-emerald-400 transition-all flex flex-col justify-between gap-4 group">
                <div class="space-y-3">
                    <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                        📝
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 group-hover:text-emerald-700 transition-colors">
                            1. Update Enrollment Status
                        </h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Track attendance, update statuses (Enrolled &rarr; In Progress &rarr; Completed), and record lab training notes.
                        </p>
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-emerald-800">
                    <span>Manage Statuses</span>
                    <span>&rarr;</span>
                </div>
            </a>

            <a href="{{ route('trainer.enrollments.index') }}" 
               class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-xl hover:border-emerald-400 transition-all flex flex-col justify-between gap-4 group">
                <div class="space-y-3">
                    <div class="h-12 w-12 rounded-2xl bg-teal-50 text-teal-800 border border-teal-200 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                        📋
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 group-hover:text-emerald-700 transition-colors">
                            2. Evaluate Course Answers
                        </h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Review jobseeker quiz answers, evaluate submission attempts, grade test performance, and provide trainer feedback.
                        </p>
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-teal-800">
                    <span>Evaluate Quizzes</span>
                    <span>&rarr;</span>
                </div>
            </a>

            <a href="{{ route('trainer.enrollments.index') }}" 
               class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-xl hover:border-emerald-400 transition-all flex flex-col justify-between gap-4 group">
                <div class="space-y-3">
                    <div class="h-12 w-12 rounded-2xl bg-blue-50 text-blue-800 border border-blue-200 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                        📜
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 group-hover:text-emerald-700 transition-colors">
                            3. Generate Certificate
                        </h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Issue official DMDP Certificates of Completion and automatically credit verified skills to jobseeker profiles.
                        </p>
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-blue-800">
                    <span>Generate Certificates</span>
                    <span>&rarr;</span>
                </div>
            </a>

        </div>

        <!-- Recent Enrollment Stream -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h2 class="text-lg font-black text-slate-900">Recent Learner Enrollments</h2>
                <a href="{{ route('trainer.enrollments.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900">View Full Registry &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($recentEnrollments as $item)
                    <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-black text-slate-900 text-sm">{{ $item->first_name }} {{ $item->last_name }}</span>
                                <span class="rounded-full px-2.5 py-0.5 text-[10px] font-bold 
                                    {{ $item->status === 'completed' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($item->status === 'in_progress' ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-slate-100 text-slate-700') }}">
                                    {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                </span>
                                @if($item->certificate_issued)
                                    <span class="rounded-full bg-emerald-600 text-white px-2 py-0.5 text-[10px] font-bold">
                                        🎓 Certified
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500">Course: <span class="font-bold text-slate-800">{{ $item->course_title }}</span> ({{ ucfirst($item->course_type) }})</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('trainer.enrollments.evaluate', $item->enrollment_id) }}" 
                               class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold transition-colors">
                                Evaluate &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="py-8 text-center text-xs text-slate-400 italic">No course enrollments registered yet.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
