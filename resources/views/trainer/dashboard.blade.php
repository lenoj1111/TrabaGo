@extends('layouts.trainer')

@section('title', 'Trainer Command Center - TrabaGo DMDP')

@section('content')
<div class="min-h-screen bg-gray-50 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-semibold text-green-700 border border-green-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                        DMDP Skills Training & Certification Division
                    </span>
                    <span class="text-xs text-gray-500">{{ now()->format('l, F d, Y') }}</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Skills Training & Certification Command Center</h1>
                <p class="text-xs text-gray-500">
                    Monitor student enrollment progression, evaluate quiz & practical assessments, and issue verifiable digital certificates.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('trainer.enrollments.index') }}" class="px-3.5 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-xs font-semibold transition-colors flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Manage Enrollments</span>
                    @if($inProgressEnrollments > 0)
                        <span class="px-1.5 py-0.2 rounded-full bg-white text-green-800 text-[10px] font-bold">{{ $inProgressEnrollments }}</span>
                    @endif
                </a>
                <a href="{{ route('trainer.courses') }}" class="px-3.5 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold transition-colors flex items-center gap-1.5">
                    <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Course Modules</span>
                </a>
            </div>
        </div>

        <!-- 4 Key Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <div class="bg-white rounded-xl p-5 border border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Enrolled</span>
                    <span class="p-2 rounded-lg bg-blue-50 text-blue-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </span>
                </div>
                <div class="mt-2">
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalEnrollments) }}</p>
                    <div class="flex items-center gap-1.5 mt-1 text-xs text-gray-500">
                        <span>Across {{ $coursesCount }} training courses</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 border border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Active Learners</span>
                    <span class="p-2 rounded-lg bg-amber-50 text-amber-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <div class="mt-2">
                    <p class="text-2xl font-bold text-amber-600">{{ $inProgressEnrollments }}</p>
                    <div class="flex items-center gap-1.5 mt-1 text-xs text-gray-500">
                        <span>Currently completing modules</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 border border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Graduates Completed</span>
                    <span class="p-2 rounded-lg bg-green-50 text-green-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <div class="mt-2">
                    <p class="text-2xl font-bold text-green-700">{{ $completedEnrollments }}</p>
                    <div class="flex items-center gap-1.5 mt-1 text-xs text-gray-500">
                        <span class="text-green-700 font-semibold">{{ $completionRate }}% completion rate</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 border border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Certificates Issued</span>
                    <span class="p-2 rounded-lg bg-purple-50 text-purple-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z"/></svg>
                    </span>
                </div>
                <div class="mt-2">
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($certificatesIssued) }}</p>
                    <div class="flex items-center gap-1.5 mt-1 text-xs text-gray-500">
                        <span>Digital DMDP verified credentials</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Middle Row: Monthly Training Trends Chart & Top Courses -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Monthly Training Trends Graph (2 cols) -->
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-gray-200 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-100 pb-4">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Training Influx & Completion Velocity</h2>
                        <p class="text-xs text-gray-500">6-Month historical comparison of new enrollments vs certified graduates</p>
                    </div>
                    <div class="flex items-center gap-4 text-xs">
                        <div class="flex items-center gap-1.5">
                            <span class="h-3 w-3 rounded bg-green-600"></span>
                            <span class="text-gray-600">Enrolled</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="h-3 w-3 rounded bg-gray-900"></span>
                            <span class="text-gray-600">Graduated</span>
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
                                <div class="w-4 sm:w-6 bg-green-600 rounded-t transition-all group-hover:bg-green-700 relative" style="height: {{ $enrHeight }}px;">
                                    <span class="opacity-0 group-hover:opacity-100 absolute -top-6 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] px-1.5 py-0.5 rounded transition-opacity pointer-events-none whitespace-nowrap">
                                        {{ $t['enrolled'] }} enrolled
                                    </span>
                                </div>
                                <div class="w-4 sm:w-6 bg-gray-900 rounded-t transition-all group-hover:bg-gray-800 relative" style="height: {{ $gradHeight }}px;">
                                    <span class="opacity-0 group-hover:opacity-100 absolute -top-6 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] px-1.5 py-0.5 rounded transition-opacity pointer-events-none whitespace-nowrap">
                                        {{ $t['completed'] }} graduated
                                    </span>
                                </div>
                            </div>
                            <span class="text-[11px] font-medium text-gray-500">{{ $t['month'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-3 gap-3 pt-3 border-t border-gray-100 text-center">
                    <div class="p-2.5 rounded-lg bg-gray-50">
                        <p class="text-[10px] font-semibold text-gray-500 uppercase">Graduation Rate</p>
                        <p class="text-sm font-bold text-green-700 mt-0.5">{{ $completionRate }}%</p>
                    </div>
                    <div class="p-2.5 rounded-lg bg-gray-50">
                        <p class="text-[10px] font-semibold text-gray-500 uppercase">Avg Completion Time</p>
                        <p class="text-sm font-bold text-gray-900 mt-0.5">3.5 Weeks</p>
                    </div>
                    <div class="p-2.5 rounded-lg bg-gray-50">
                        <p class="text-[10px] font-semibold text-gray-500 uppercase">Assessment Passing</p>
                        <p class="text-sm font-bold text-gray-900 mt-0.5">94.2%</p>
                    </div>
                </div>
            </div>

            <!-- Top Courses by Enrollment (1 col) -->
            <div class="bg-white rounded-2xl p-6 border border-gray-200 space-y-4 flex flex-col justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Top Courses by Enrollment</h2>
                    <p class="text-xs text-gray-500">Most in-demand vocational & technical skills</p>
                </div>

                <div class="space-y-3">
                    @forelse($topCourses as $course)
                        <div class="p-3 rounded-xl border border-gray-100 bg-gray-50 space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="text-xs font-bold text-gray-900 truncate">{{ $course->title }}</h3>
                                <span class="text-xs font-bold text-green-700 shrink-0">{{ $course->enrollments_count }} students</span>
                            </div>
                            <div class="w-full bg-gray-200 h-1.5 rounded-full overflow-hidden">
                                @php
                                    $cMax = max(1, $topCourses->first()->enrollments_count ?? 1);
                                    $cPct = round(($course->enrollments_count / $cMax) * 100);
                                @endphp
                                <div class="bg-green-600 h-full rounded-full" style="width: {{ $cPct }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-gray-500">
                            No courses available.
                        </div>
                    @endforelse
                </div>

                <a href="{{ route('trainer.courses') }}" class="block text-center text-xs font-semibold text-green-700 hover:text-green-800 pt-2 border-t border-gray-100">
                    View Course Modules Catalog &rarr;
                </a>
            </div>

        </div>

        <!-- Bottom Row: Recent Student Enrollments & Assessments -->
        <div class="bg-white rounded-2xl p-6 border border-gray-200 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Active Students & Evaluation Queue</h2>
                    <p class="text-xs text-gray-500">Recent learners requiring attendance, quiz, or practical review</p>
                </div>
                <a href="{{ route('trainer.enrollments.index') }}" class="text-xs font-semibold text-green-700 hover:text-green-800">
                    All Enrollments &rarr;
                </a>
            </div>

            @if(count($recentEnrollments) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-gray-200 text-gray-500 uppercase tracking-wider text-[10px]">
                                <th class="pb-2">Student Name</th>
                                <th class="pb-2">Course Title</th>
                                <th class="pb-2">Status</th>
                                <th class="pb-2">Progress</th>
                                <th class="pb-2 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($recentEnrollments as $enr)
                                <tr>
                                    <td class="py-3">
                                        <p class="font-bold text-gray-900">{{ $enr->first_name }} {{ $enr->last_name }}</p>
                                        <p class="text-[11px] text-gray-500">{{ $enr->jobseeker_email }}</p>
                                    </td>
                                    <td class="py-3">
                                        <p class="font-medium text-gray-900">{{ $enr->course_title }}</p>
                                        <span class="text-[10px] text-gray-500">{{ $enr->course_type ?? 'TESDA Vocational' }}</span>
                                    </td>
                                    <td class="py-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $enr->status === 'completed' ? 'bg-green-50 text-green-700' : ($enr->status === 'in_progress' ? 'bg-amber-50 text-amber-700' : 'bg-gray-100 text-gray-700') }}">
                                            {{ ucfirst(str_replace('_', ' ', $enr->status)) }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <div class="w-24 bg-gray-200 h-1.5 rounded-full overflow-hidden">
                                            <div class="bg-green-600 h-full rounded-full" style="width: {{ $enr->status === 'completed' ? 100 : ($enr->status === 'in_progress' ? 60 : 15) }}%;"></div>
                                        </div>
                                    </td>
                                    <td class="py-3 text-right">
                                        <a href="{{ route('trainer.enrollments.index') }}" class="px-3 py-1 rounded-lg bg-gray-900 hover:bg-green-600 text-white text-[11px] font-semibold transition-colors">
                                            Evaluate
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-8 text-center text-xs text-gray-500">
                    No active student enrollments found.
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
