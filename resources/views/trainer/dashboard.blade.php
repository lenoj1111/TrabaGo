@extends('layouts.trainer')

@section('title', 'Trainer Command Center - TrabaGo DMDP')

@section('content')
<div class="min-h-screen bg-slate-50/70 px-4 py-6 sm:px-6 lg:px-8">
    <div class="w-full max-w-[1680px] mx-auto space-y-6">
        
        <!-- HEADER HERO -->
        <header class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-slate-900 to-emerald-950 p-6 sm:p-8 text-white shadow-lg border border-slate-800">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-semibold text-emerald-300 border border-emerald-500/30">
                            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            DMDP Skills Training & Certification Division
                        </span>
                        <span class="text-xs text-slate-400">{{ now()->format('l, F d, Y') }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                        Skills Training & Certification Command Center
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                        Monitor student enrollment progression, evaluate quiz & practical assessments, author course modules, and issue verifiable digital certificates.
                    </p>
                </div>

                <!-- Action Toolbar -->
                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <a href="{{ route('trainer.enrollments.index') }}" 
                       class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white px-4 py-2.5 text-xs font-bold shadow-md shadow-emerald-950/40 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Manage Enrollments</span>
                        @if($inProgressEnrollments > 0)
                            <span class="px-2 py-0.5 rounded-full bg-white text-emerald-900 text-[10px] font-black">{{ $inProgressEnrollments }}</span>
                        @endif
                    </a>

                    <a href="{{ route('trainer.courses') }}" 
                       class="inline-flex items-center gap-2 rounded-xl bg-slate-800 hover:bg-slate-700 active:scale-95 text-slate-200 border border-slate-700 px-3.5 py-2.5 text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400">
                        <svg class="h-4 w-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span>Course Modules</span>
                    </a>
                </div>
            </div>
        </header>

        <!-- 4 KEY TRAINING METRICS -->
        <section aria-label="Training Metrics Summary" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- 1. Total Enrolled -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:border-slate-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-600">Total Enrolled</span>
                    <div class="h-9 w-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shadow-inner" aria-hidden="true">
                        <svg class="h-4 w-4 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($totalEnrollments) }}</p>
                    <div class="mt-2.5 flex items-center justify-between text-xs text-slate-600 pt-2 border-t border-slate-100 font-medium">
                        <span>Across <strong class="text-slate-900 font-bold">{{ $coursesCount }}</strong> training courses</span>
                    </div>
                </div>
            </div>

            <!-- 2. Active Learners -->
            <div class="rounded-2xl border border-amber-200/80 bg-amber-50/40 p-5 shadow-sm hover:border-amber-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-800">Active Learners</span>
                    <div class="h-9 w-9 rounded-xl bg-amber-100 text-amber-900 flex items-center justify-center shadow-inner" aria-hidden="true">
                        <svg class="h-4 w-4 text-amber-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-3xl font-black text-amber-950 tracking-tight">{{ number_format($inProgressEnrollments) }}</p>
                    <div class="mt-2.5 flex items-center justify-between text-xs text-amber-900 pt-2 border-t border-amber-200/60 font-medium">
                        <span>Currently completing module assessments</span>
                    </div>
                </div>
            </div>

            <!-- 3. Graduates Completed -->
            <div class="rounded-2xl border border-emerald-200/80 bg-emerald-50/40 p-5 shadow-sm hover:border-emerald-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Graduates Completed</span>
                    <div class="h-9 w-9 rounded-xl bg-emerald-100 text-emerald-900 flex items-center justify-center shadow-inner" aria-hidden="true">
                        <svg class="h-4 w-4 text-emerald-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-3xl font-black text-emerald-950 tracking-tight">{{ number_format($completedEnrollments) }}</p>
                    <div class="mt-2.5 flex items-center justify-between text-xs text-emerald-900 pt-2 border-t border-emerald-200/60 font-medium">
                        <span>Completion Rate:</span>
                        <strong class="font-bold text-emerald-950">{{ $completionRate }}%</strong>
                    </div>
                </div>
            </div>

            <!-- 4. Certificates Issued -->
            <div class="rounded-2xl border border-purple-200/80 bg-purple-50/40 p-5 shadow-sm hover:border-purple-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-purple-800">Certificates Issued</span>
                    <div class="h-9 w-9 rounded-xl bg-purple-100 text-purple-900 flex items-center justify-center shadow-inner" aria-hidden="true">
                        <svg class="h-4 w-4 text-purple-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-3xl font-black text-purple-950 tracking-tight">{{ number_format($certificatesIssued) }}</p>
                    <div class="mt-2.5 flex items-center justify-between text-xs text-purple-900 pt-2 border-t border-purple-200/60 font-medium">
                        <span>Digital DMDP verified credentials</span>
                    </div>
                </div>
            </div>

        </section>

        <!-- MIDDLE ROW: MONTHLY TRAINING TRENDS CHART & TOP COURSES -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Monthly Training Trends Graph (2 cols) -->
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Training Influx & Completion Velocity</h2>
                        <p class="text-xs text-slate-500">6-Month historical comparison of new enrollments vs certified graduates</p>
                    </div>
                    <div class="flex items-center gap-4 text-xs font-semibold">
                        <div class="flex items-center gap-1.5">
                            <span class="h-3 w-3 rounded bg-emerald-600"></span>
                            <span class="text-slate-700">Enrolled</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="h-3 w-3 rounded bg-slate-900"></span>
                            <span class="text-slate-700">Graduated</span>
                        </div>
                    </div>
                </div>

                <!-- Responsive Graph -->
                <div class="h-56 flex items-end justify-between gap-4 pt-4 px-2">
                    @php
                        $maxTrn = 1;
                        foreach($monthlyTrainingTrends as $t) {
                            if($t['enrolled'] > $maxTrn) $maxTrn = $t['enrolled'];
                        }
                    @endphp

                    @foreach($monthlyTrainingTrends as $t)
                        @php
                            $enrHeight = max(8, round(($t['enrolled'] / $maxTrn) * 160));
                            $gradHeight = max(6, round(($t['completed'] / $maxTrn) * 160));
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end group">
                            <div class="w-full flex items-end justify-center gap-1.5 h-44">
                                <div class="w-4 sm:w-6 bg-emerald-600 rounded-t transition-all group-hover:bg-emerald-700 relative" style="height: {{ $enrHeight }}px;">
                                    <span class="opacity-0 group-hover:opacity-100 absolute -top-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] px-2 py-0.5 rounded shadow pointer-events-none whitespace-nowrap transition-opacity">
                                        {{ $t['enrolled'] }} enrolled
                                    </span>
                                </div>
                                <div class="w-4 sm:w-6 bg-slate-900 rounded-t transition-all group-hover:bg-slate-800 relative" style="height: {{ $gradHeight }}px;">
                                    <span class="opacity-0 group-hover:opacity-100 absolute -top-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] px-2 py-0.5 rounded shadow pointer-events-none whitespace-nowrap transition-opacity">
                                        {{ $t['completed'] }} graduated
                                    </span>
                                </div>
                            </div>
                            <span class="text-xs font-semibold text-slate-500">{{ $t['month'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-3 gap-3 pt-3 border-t border-slate-100 text-center">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Graduation Rate</p>
                        <p class="text-base font-black text-emerald-700 mt-0.5">{{ $completionRate }}%</p>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Avg Completion Time</p>
                        <p class="text-base font-black text-slate-900 mt-0.5">3.5 Weeks</p>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Assessment Passing</p>
                        <p class="text-base font-black text-slate-900 mt-0.5">94.2%</p>
                    </div>
                </div>
            </div>

            <!-- Top Courses by Enrollment (1 col) -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4 flex flex-col justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Top Courses by Enrollment</h2>
                    <p class="text-xs text-slate-500">Most in-demand vocational & technical skills</p>
                </div>

                <div class="space-y-3">
                    @forelse($topCourses as $course)
                        <div class="p-3 rounded-xl border border-slate-100 bg-slate-50 space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="text-xs font-bold text-slate-900 truncate">{{ $course->title }}</h3>
                                <span class="text-xs font-bold text-emerald-700 shrink-0">{{ $course->enrollments_count }} students</span>
                            </div>
                            <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                @php
                                    $cMax = max(1, $topCourses->first()->enrollments_count ?? 1);
                                    $cPct = round(($course->enrollments_count / $cMax) * 100);
                                @endphp
                                <div class="bg-emerald-600 h-full rounded-full" style="width: {{ $cPct }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-slate-400">
                            No courses available.
                        </div>
                    @endforelse
                </div>

                <a href="{{ route('trainer.courses') }}" class="block text-center text-xs font-bold text-emerald-700 hover:text-emerald-900 hover:underline pt-2 border-t border-slate-100">
                    View Course Modules Catalog &rarr;
                </a>
            </div>

        </div>

        <!-- BOTTOM ROW: RECENT STUDENT ENROLLMENTS & EVALUATION QUEUE -->
        <section aria-label="Active Student Enrollments" class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Active Students & Evaluation Queue</h2>
                    <p class="text-xs text-slate-500">Recent learners requiring attendance, quiz, or practical review</p>
                </div>
                <a href="{{ route('trainer.enrollments.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 hover:underline">
                    All Enrollments &rarr;
                </a>
            </div>

            @if(count($recentEnrollments) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs" role="table">
                        <thead class="text-xs font-bold uppercase tracking-wider text-slate-600 border-b border-slate-100">
                            <tr>
                                <th scope="col" class="pb-3 px-3">Student Name</th>
                                <th scope="col" class="pb-3 px-3">Course Title</th>
                                <th scope="col" class="pb-3 px-3 text-center">Status</th>
                                <th scope="col" class="pb-3 px-3">Progress</th>
                                <th scope="col" class="pb-3 px-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @foreach($recentEnrollments as $enr)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3 px-3">
                                        <p class="font-bold text-slate-900">{{ $enr->first_name }} {{ $enr->last_name }}</p>
                                        <p class="text-xs text-slate-400 font-normal">{{ $enr->jobseeker_email }}</p>
                                    </td>
                                    <td class="py-3 px-3">
                                        <p class="font-bold text-slate-800">{{ $enr->course_title }}</p>
                                        <span class="text-xs text-slate-500 font-normal">{{ $enr->course_type ?? 'TESDA Vocational' }}</span>
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $enr->status === 'completed' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : ($enr->status === 'in_progress' ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-700 border border-slate-200') }}">
                                            {{ ucfirst(str_replace('_', ' ', $enr->status)) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="w-28 bg-slate-200 h-2 rounded-full overflow-hidden">
                                            <div class="bg-emerald-600 h-full rounded-full transition-all" style="width: {{ $enr->status === 'completed' ? 100 : ($enr->status === 'in_progress' ? 60 : 15) }}%;"></div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        <a href="{{ route('trainer.enrollments.index') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-emerald-600 active:scale-95 text-white text-xs font-bold transition-all shadow-sm">
                                            Evaluate
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-10 text-center text-xs text-slate-400">
                    No active student enrollments found.
                </div>
            @endif
        </section>

    </div>
</div>
@endsection
