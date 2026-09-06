@extends('layouts.jobseeker')

@section('title', 'My Training Enrollments - TrabaGo')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Hero Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="max-w-2xl space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Learner Progress Dashboard
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">My Training Enrollments</h1>
                <p class="text-sm text-slate-300 leading-relaxed">
                    Track your registered vocational courses, study lecture topics, review trainer assessment feedback, and download your earned certificates of completion.
                </p>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-3 gap-3 bg-white/10 backdrop-blur rounded-2xl p-4 border border-white/10 text-center shrink-0">
                <div>
                    <span class="text-[10px] font-bold text-slate-300 uppercase tracking-wider block">Total</span>
                    <p class="text-2xl font-black text-white mt-0.5">{{ $stats['total'] }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-amber-300 uppercase tracking-wider block">Active</span>
                    <p class="text-2xl font-black text-amber-400 mt-0.5">{{ $stats['in_progress'] }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-emerald-300 uppercase tracking-wider block">Certified</span>
                    <p class="text-2xl font-black text-emerald-400 mt-0.5">{{ $stats['certificates'] }}</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
                <span>✓</span> {{ session('success') }}
            </div>
        @endif

        @if(session('info'))
            <div class="p-4 rounded-2xl bg-teal-50 border border-teal-200 text-teal-800 text-xs font-bold flex items-center gap-2">
                <span>ℹ️</span> {{ session('info') }}
            </div>
        @endif

        <!-- Subnav -->
        <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm flex items-center justify-between gap-4">
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('jobseeker.training.enrollments') }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-900 text-white shadow-sm flex items-center gap-2">
                    <span>📋 My Training Enrollments</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-emerald-600 text-white font-black">
                        {{ $stats['total'] }}
                    </span>
                </a>

                <a href="{{ route('jobseeker.training.skills') }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 transition-colors">
                    <span>🎯 View Training Skills</span>
                </a>

                <a href="{{ route('jobseeker.training') }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 transition-colors">
                    <span>📚 All Courses Catalog</span>
                </a>
            </div>

            <a href="{{ route('jobseeker.training.skills') }}" 
               class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 hover:underline">
                <span>+ Explore More Skills</span> &rarr;
            </a>
        </div>

        <!-- Enrollments List -->
        <div class="space-y-6">
            @forelse($enrollments as $enrollment)
                @php
                    $course = $enrollment->trainingProgram;
                    $isCompleted = $enrollment->status === 'completed';
                    $isInProgress = $enrollment->status === 'in_progress';
                    $isFailed = $enrollment->status === 'failed';
                @endphp
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm hover:border-emerald-300 transition-all space-y-6">
                    
                    <!-- Header of Enrollment Card -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="rounded-full px-2.5 py-0.5 text-[11px] font-bold uppercase {{ $course && $course->training_type === 'laboratory_onsite' ? 'bg-purple-100 text-purple-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    {{ $course ? ucfirst($course->training_type ?: 'Online') : 'Online' }}
                                </span>

                                @if($isCompleted)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 px-3 py-0.5 text-xs font-extrabold">
                                        <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                                        Completed & Qualified
                                    </span>
                                @elseif($isInProgress)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-teal-100 text-teal-800 border border-teal-300 px-3 py-0.5 text-xs font-bold">
                                        <span class="h-2 w-2 rounded-full bg-teal-600 animate-pulse"></span>
                                        In Progress (Active Training)
                                    </span>
                                @elseif($isFailed)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 text-rose-800 border border-rose-300 px-3 py-0.5 text-xs font-bold">
                                        Needs Retake
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 text-amber-900 border border-amber-300 px-3 py-0.5 text-xs font-bold">
                                        <span class="h-2 w-2 rounded-full bg-amber-600"></span>
                                        Enrolled (Awaiting Assessment)
                                    </span>
                                @endif
                            </div>

                            <h2 class="text-xl font-black text-slate-900 mt-1">
                                {{ $course ? $course->title : 'Vocational Training Course' }}
                            </h2>
                            <p class="text-xs text-slate-500">
                                Enrolled On: {{ $enrollment->start_date ? \Carbon\Carbon::parse($enrollment->start_date)->format('M d, Y') : 'Recent' }}
                                @if($enrollment->end_date)
                                    &bull; Completed On: {{ \Carbon\Carbon::parse($enrollment->end_date)->format('M d, Y') }}
                                @endif
                            </p>
                        </div>

                        <!-- Certificate Action -->
                        <div class="shrink-0 flex items-center gap-2">
                            @if($enrollment->certificate_issued)
                                <a href="{{ route('jobseeker.certificates.preview', $enrollment->enrollment_id) }}" target="_blank"
                                   class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 px-5 py-2.5 text-xs font-black shadow-lg shadow-amber-500/20 transition-all hover:scale-105">
                                    <span>🎓</span> View Skills Certificate
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Progress Stepper -->
                    <div class="py-2">
                        <div class="grid grid-cols-4 gap-2 text-center text-xs font-bold">
                            <!-- Step 1 -->
                            <div class="space-y-1">
                                <div class="h-2 rounded-full bg-emerald-600"></div>
                                <span class="text-emerald-800 text-[11px]">1. Enrolled</span>
                            </div>
                            <!-- Step 2 -->
                            <div class="space-y-1">
                                <div class="h-2 rounded-full {{ in_array($enrollment->status, ['in_progress', 'completed']) ? 'bg-emerald-600' : 'bg-slate-200' }}"></div>
                                <span class="{{ in_array($enrollment->status, ['in_progress', 'completed']) ? 'text-emerald-800' : 'text-slate-400' }} text-[11px]">2. In Training</span>
                            </div>
                            <!-- Step 3 -->
                            <div class="space-y-1">
                                <div class="h-2 rounded-full {{ !is_null($enrollment->score) || $isCompleted ? 'bg-emerald-600' : 'bg-slate-200' }}"></div>
                                <span class="{{ !is_null($enrollment->score) || $isCompleted ? 'text-emerald-800' : 'text-slate-400' }} text-[11px]">3. Assessed</span>
                            </div>
                            <!-- Step 4 -->
                            <div class="space-y-1">
                                <div class="h-2 rounded-full {{ $enrollment->certificate_issued ? 'bg-emerald-600' : 'bg-slate-200' }}"></div>
                                <span class="{{ $enrollment->certificate_issued ? 'text-emerald-800' : 'text-slate-400' }} text-[11px]">4. Certified</span>
                            </div>
                        </div>
                    </div>

                    <!-- Evaluation & Remarks Details -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-slate-50 rounded-2xl p-4 border border-slate-100 text-xs">
                        <div>
                            <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px]">Assessment Score</span>
                            @if(!is_null($enrollment->score))
                                <p class="text-base font-black {{ $enrollment->passed ? 'text-emerald-700' : 'text-rose-600' }} mt-0.5">
                                    {{ $enrollment->score }}% &bull; {{ $enrollment->passed ? 'PASSED' : 'FAILED' }}
                                </p>
                            @else
                                <p class="text-slate-500 italic mt-0.5">Pending Assessment</p>
                            @endif
                        </div>

                        <div>
                            <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px]">Target Skills</span>
                            <p class="font-bold text-slate-800 mt-0.5">
                                {{ $enrollment->enrolled_skills ?: ($course && $course->skills ? $course->skills : ($course ? $course->title : 'Vocational Competency')) }}
                            </p>
                        </div>

                        <div>
                            <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px]">Certificate Status</span>
                            @if($enrollment->certificate_issued)
                                <p class="font-bold text-emerald-800 mt-0.5">
                                    ✓ Issued (#{{ $enrollment->certificate_no }})
                                </p>
                            @else
                                <p class="text-slate-500 italic mt-0.5">
                                    {{ $isCompleted ? 'Awaiting Trainer Issuance' : 'Requires Course Completion' }}
                                </p>
                            @endif
                        </div>
                    </div>

                    @if($enrollment->lab_remarks || $enrollment->trainer_feedback)
                        <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/80 text-xs space-y-1">
                            <span class="font-bold text-amber-900 block text-[11px]">Trainer Feedback & Remarks:</span>
                            @if($enrollment->lab_remarks)
                                <p class="text-slate-700"><strong class="text-slate-900">Lab/Attendance:</strong> {{ $enrollment->lab_remarks }}</p>
                            @endif
                            @if($enrollment->trainer_feedback)
                                <p class="text-slate-700"><strong class="text-slate-900">Evaluation:</strong> {{ $enrollment->trainer_feedback }}</p>
                            @endif
                        </div>
                    @endif

                    <!-- Action Footer -->
                    <div class="flex items-center justify-between gap-3 pt-2">
                        <div class="text-xs text-slate-500">
                            @if($course && $course->topics)
                                {{ $course->topics->count() }} Learning Modules
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            @if($course)
                                <a href="{{ route('jobseeker.training.show', $course->training_id) }}" 
                                   class="inline-flex items-center gap-1 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 text-xs font-bold transition-colors">
                                    <span>📖</span> Course Lessons
                                </a>

                                <a href="{{ route('jobseeker.training.quiz', $course->training_id) }}" 
                                   class="inline-flex items-center gap-1 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 text-xs font-black shadow-md shadow-emerald-600/20 transition-all">
                                    <span>📝</span> {{ !is_null($enrollment->score) ? 'Retake Quiz' : 'Take Assessment Quiz' }} &rarr;
                                </a>
                            @endif
                        </div>
                    </div>

                </div>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-400 space-y-3">
                    <div class="text-4xl">📚</div>
                    <h3 class="text-base font-bold text-slate-800">You have not enrolled in any training courses yet</h3>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">
                        Browse our vocational skills catalog to select in-demand competencies, register with accredited trainers, and earn verified certificate credentials.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('jobseeker.training.skills') }}" 
                           class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-3 text-xs font-black shadow-lg shadow-emerald-600/25">
                            <span>🎯</span> Browse Training Skills &rarr;
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
