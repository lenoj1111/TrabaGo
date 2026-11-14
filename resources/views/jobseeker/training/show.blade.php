@extends('layouts.jobseeker')

@section('title', $training->title . ' - Training Course')

@section('content')
<div x-data="{ activeTopic: 0 }" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-5xl space-y-8">

        <!-- Navigation -->
        <div class="flex items-center justify-between">
            <a href="{{ route('jobseeker.training') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-emerald-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to All Courses
            </a>
            <span class="text-xs text-slate-400 font-mono">Course ID: #{{ $training->training_id }}</span>
        </div>

        @php
        $isCompleted = $enrollment && $enrollment->status === 'completed';
        $isEnrolled = $enrollment && in_array($enrollment->status, ['enrolled', 'in_progress', 'completed', 'failed']);
        @endphp

        @if(session('success') || session('info') || session('error'))
        <div class="rounded-2xl border {{ session('error') ? 'border-rose-200 bg-rose-50 text-rose-800' : 'border-emerald-200 bg-emerald-50 text-emerald-900' }} p-4 text-sm font-semibold">
            {{ session('success') ?? session('info') ?? session('error') }}
        </div>
        @endif

        @if($assessmentReviewStatus === 'pending')
        <div class="rounded-2xl border border-amber-300 bg-amber-50 p-5 text-amber-950">
            <h2 class="text-sm font-black">Assessment Status: Pending Review</h2>
            <p class="mt-1 text-xs">Your answers are awaiting trainer review. You remain enrolled in this course.</p>
        </div>
        @elseif($assessmentReviewStatus === 'needs_retake')
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5 text-rose-900">
            <h2 class="text-sm font-black">Assessment Status: Needs Retake</h2>
            <p class="mt-1 text-xs">You are still enrolled in this course. Retake the quiz when you are ready.</p>
            @if($enrollment->trainer_feedback)
            <p class="mt-2 text-xs"><strong>Trainer feedback:</strong> {{ $enrollment->trainer_feedback }}</p>
            @endif
        </div>
        @elseif($assessmentReviewStatus === 'passed')
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-emerald-900">
            <h2 class="text-sm font-black">Assessment Status: Passed</h2>
            <p class="mt-1 text-xs">{{ $enrollment->certificate_issued ? 'Your certificate is ready to view.' : 'Your trainer approved your answers. Your certificate is awaiting issuance.' }}</p>
        </div>
        @endif

        <!-- Course Header Card in green Theme -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                        {{ ucfirst($training->training_type ?: 'Online') }}
                    </span>
                    @if($assessmentReviewStatus === 'passed')
                    <span class="rounded-full bg-emerald-500/30 px-3 py-1 text-xs font-bold text-emerald-200 border border-emerald-400/40 flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ $enrollment->certificate_issued ? 'Certificate Earned' : 'Assessment Passed' }}</span>
                    </span>
                    @elseif($assessmentReviewStatus === 'needs_retake')
                    <span class="rounded-full bg-rose-400/20 px-3 py-1 text-xs font-bold text-rose-200 border border-rose-400/30">
                        Assessment Needs Retake
                    </span>
                    @elseif($assessmentReviewStatus === 'pending')
                    <span class="rounded-full bg-amber-400/20 px-3 py-1 text-xs font-bold text-amber-200 border border-amber-400/30">
                        Assessment Pending Review
                    </span>
                    @elseif($isEnrolled)
                    <span class="rounded-full bg-amber-400/20 px-3 py-1 text-xs font-bold text-amber-300 border border-amber-400/30 flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5 text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <span>Enrolled &bull; {{ ucfirst(str_replace('_', ' ', $enrollment->status)) }}</span>
                    </span>
                    @else
                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-slate-300">
                        Available for Enrollment
                    </span>
                    @endif
                </div>

                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">{{ $training->title }}</h1>

                <!-- Location and Dates for Onsite Courses -->
                <div class="flex flex-wrap items-center gap-3 pt-2">
                    @if($training->training_type === 'laboratory_onsite')
                    <span class="inline-flex items-center gap-1.5 bg-white/10 border border-white/20 px-3 py-1 rounded-lg text-xs font-bold text-white">
                        <svg class="h-4 w-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ $training->location ?: 'Location TBA' }}
                    </span>
                    @endif

                    <span class="inline-flex items-center gap-1.5 bg-white/10 border border-white/20 px-3 py-1 rounded-lg text-xs font-bold text-white">
                        <svg class="h-4 w-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $training->training_start_date ? \Carbon\Carbon::parse($training->training_start_date)->format('M d, Y') : 'TBA' }}
                        to
                        {{ $training->training_end_date ? \Carbon\Carbon::parse($training->training_end_date)->format('M d, Y') : 'TBA' }}
                    </span>
                </div>

                <p class="text-sm text-slate-300 leading-relaxed mt-2">{{ $training->description ?: 'Complete all module topics and pass the quiz to earn your verified profile skill badge.' }}</p>
            </div>

            <div class="shrink-0 flex flex-col items-center sm:items-end gap-3">
                @if($enrollment && $enrollment->certificate_issued)
                <a href="{{ route('jobseeker.certificates.preview', $enrollment->enrollment_id) }}" target="_blank"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 px-5 py-2.5 text-xs font-black shadow-lg shadow-amber-500/20 transition-all hover:scale-105">
                    <svg class="h-4 w-4 text-slate-950" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                    </svg>
                    <span>View & Download Certificate</span>
                </a>
                @endif

                @if(!$isEnrolled)
                <form action="{{ route('jobseeker.training.enroll', $training->training_id) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 px-7 py-3.5 text-xs font-black text-white shadow-lg shadow-emerald-600/30 transition-all hover:scale-105 cursor-pointer">
                        <span>+</span> Enroll in Course
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </form>
                @elseif($assessmentReviewStatus === 'pending')
                <span class="max-w-sm rounded-xl border border-amber-300 bg-amber-50 px-5 py-3 text-center text-xs font-bold text-amber-900">Assessment pending trainer review</span>
                @elseif($assessmentReviewStatus === 'passed')
                <span class="max-w-sm rounded-xl border border-emerald-300 bg-emerald-50 px-5 py-3 text-center text-xs font-bold text-emerald-900">
                    {{ $enrollment->certificate_issued ? 'Course passed - certificate issued' : 'Course passed - certificate pending issuance' }}
                </span>
                @elseif($hasAssessmentQuestions)
                <a href="{{ route('jobseeker.training.quiz', $training->training_id) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 px-6 py-3 text-xs font-black text-white shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                    {{ $assessmentReviewStatus === 'needs_retake' ? 'Retake Quiz' : 'Take Skill Quiz' }}
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
                @else
                <div class="max-w-sm rounded-2xl border border-amber-300 bg-amber-50 p-4 text-center text-xs text-amber-900">
                    <strong class="block font-black">Assessment not available yet</strong>
                    <span class="mt-1 block">The trainer has not added questions yet. Please wait for the trainer to prepare the assessment.</span>
                </div>
                @endif
            </div>
        </div>

        @if(!$isEnrolled)
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-0.5">
                <h3 class="text-sm font-black text-emerald-950 flex items-center gap-2">
                    <svg class="h-4 w-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                    </svg>
                    <span>Not yet enrolled in this course</span>
                </h3>
                <p class="text-xs text-emerald-800">Enroll now to register your participation with DMDP, study the curriculum modules, and take the skill competency quiz.</p>
            </div>
            <form action="{{ route('jobseeker.training.enroll', $training->training_id) }}" method="POST" class="shrink-0">
                @csrf
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-md shadow-emerald-600/20 transition-all cursor-pointer">
                    Enroll Now &rarr;
                </button>
            </form>
        </div>
        @endif

        <!-- Topics & Modules Section -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- Topics Sidebar (4 cols) -->
            <div class="lg:col-span-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 px-2">Course Modules</h3>

                <div class="space-y-1">
                    @forelse($training->topics as $index => $topic)
                    <button @click="activeTopic = {{ $index }}"
                        class="w-full text-left p-3.5 rounded-2xl text-xs font-bold transition-all flex items-center justify-between"
                        :class="activeTopic === {{ $index }} ? 'bg-emerald-50 text-emerald-900 border border-emerald-300 shadow-sm' : 'text-slate-600 hover:bg-slate-50'">
                        <span class="flex items-center gap-2.5 truncate">
                            <span class="h-6 w-6 rounded-lg bg-slate-100 flex items-center justify-center text-[11px] font-bold text-slate-700 shrink-0"
                                :class="activeTopic === {{ $index }} ? 'bg-emerald-600 text-white' : ''">
                                {{ $index + 1 }}
                            </span>
                            <span class="truncate">{{ $topic->title }}</span>
                        </span>
                        <svg class="h-4 w-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    @empty
                    <p class="text-xs text-slate-400 italic p-2">No specific module topics listed. Read overview and proceed to quiz!</p>
                    @endforelse
                </div>

                <div class="pt-4 border-t border-slate-100">
                    @if($assessmentReviewStatus === 'pending')
                    <p class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-900">Your assessment is pending trainer review.</p>
                    @elseif($assessmentReviewStatus === 'passed')
                    <p class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-900">Your assessment has been approved.</p>
                    @elseif($assessmentReviewStatus === 'needs_retake' && $hasAssessmentQuestions)
                    <a href="{{ route('jobseeker.training.quiz', $training->training_id) }}"
                        class="w-full inline-flex items-center justify-center rounded-xl bg-rose-700 text-white px-4 py-3 text-xs font-bold hover:bg-rose-600 transition-colors">
                        Retake Quiz
                    </a>
                    @elseif($hasAssessmentQuestions)
                    <a href="{{ route('jobseeker.training.quiz', $training->training_id) }}"
                        class="w-full inline-flex items-center justify-center rounded-xl bg-slate-900 text-white px-4 py-3 text-xs font-bold hover:bg-emerald-600 transition-colors">
                        Ready for Assessment? Take Quiz
                    </a>
                    @else
                    <p class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-900">No assessment questions yet. Please wait for the trainer to add them.</p>
                    @endif
                </div>
            </div>

            <!-- Active Topic Content (8 cols) -->
            <div class="lg:col-span-8 rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                @if($training->topics->count() > 0)
                @foreach($training->topics as $index => $topic)
                <div x-show="activeTopic === {{ $index }}" class="space-y-6">
                    <div class="pb-4 border-b border-slate-100">
                        <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Module {{ $index + 1 }} of {{ $training->topics->count() }}</span>
                        <h2 class="text-2xl font-black text-slate-900 mt-1">{{ $topic->title }}</h2>
                    </div>

                    <!-- Video or Interactive Lesson Content -->
                    @if($topic->video_url)
                    @php
                    $vUrl = $topic->video_url;
                    $isYt = str_contains($vUrl, 'youtube.com') || str_contains($vUrl, 'youtu.be');
                    $embedYt = null;
                    if ($isYt) {
                    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $vUrl, $match)) {
                    $embedYt = "https://www.youtube.com/embed/" . $match[1];
                    }
                    }
                    @endphp
                    @if($embedYt)
                    <div class="rounded-2xl overflow-hidden aspect-video bg-slate-950 shadow-md">
                        <iframe class="w-full h-full" src="{{ $embedYt }}" frameborder="0" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                    </div>
                    @elseif(str_ends_with(strtolower($vUrl), '.mp4') || str_ends_with(strtolower($vUrl), '.webm'))
                    <div class="rounded-2xl overflow-hidden aspect-video bg-slate-950 shadow-md flex items-center justify-center">
                        <video class="w-full h-full" controls>
                            <source src="{{ $vUrl }}">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                    @else
                    <div class="rounded-2xl p-6 bg-slate-900 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-md">
                        <div class="flex items-center gap-3.5">
                            <div class="h-12 w-12 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shrink-0">
                                <svg class="h-6 w-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-emerald-300">Course Interactive Video Lecture</p>
                                <p class="text-[11px] text-slate-300 truncate max-w-sm mt-0.5">{{ $vUrl }}</p>
                            </div>
                        </div>
                        <a href="{{ $vUrl }}" target="_blank" rel="noopener noreferrer" class="rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white px-5 py-2.5 text-xs font-bold shrink-0 transition-all shadow-md">
                            Open Lecture Video &rarr;
                        </a>
                    </div>
                    @endif
                    @endif

                    <!-- Key Lesson Takeaways in green Theme -->
                    <div class="rounded-2xl bg-emerald-50/50 p-6 border border-emerald-100 space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-900 flex items-center gap-2">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Module Learning Objectives
                        </h4>
                        <ul class="space-y-2 text-xs text-slate-700 list-disc list-inside">
                            <li>Understand core principles and industry best practices for {{ $topic->title }}.</li>
                            <li>Apply technical requirements in real-world scenarios and employment tasks.</li>
                            <li>Prepare for the assessment questions required to verify this skill on your profile.</li>
                        </ul>
                    </div>

                    <!-- Bottom Action for Topic -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        @if($index > 0)
                        <button @click="activeTopic = {{ $index - 1 }}" class="text-xs font-bold text-slate-600 hover:text-slate-900">
                            &larr; Previous Module
                        </button>
                        @else
                        <span></span>
                        @endif

                        @if($index < $training->topics->count() - 1)
                            <button @click="activeTopic = {{ $index + 1 }}" class="rounded-xl bg-slate-900 text-white px-5 py-2 text-xs font-bold hover:bg-slate-800">
                                Next Module &rarr;
                            </button>
                            @else
                            @if($assessmentReviewStatus === 'pending')
                            <span class="text-xs font-semibold text-amber-800">Assessment pending trainer review.</span>
                            @elseif($assessmentReviewStatus === 'passed')
                            <span class="text-xs font-semibold text-emerald-800">Assessment approved.</span>
                            @elseif($assessmentReviewStatus === 'needs_retake' && $hasAssessmentQuestions)
                            <a href="{{ route('jobseeker.training.quiz', $training->training_id) }}" class="rounded-xl bg-rose-700 text-white px-6 py-2.5 text-xs font-black hover:bg-rose-600 shadow-md">
                                Retake Quiz &rarr;
                            </a>
                            @elseif($hasAssessmentQuestions)
                            <a href="{{ route('jobseeker.training.quiz', $training->training_id) }}" class="rounded-xl bg-emerald-600 text-white px-6 py-2.5 text-xs font-black hover:bg-emerald-500 shadow-md">
                                Finish & Take Skill Quiz &rarr;
                            </a>
                            @else
                            <span class="text-xs font-semibold text-amber-800">Assessment questions are not ready yet. Please wait for the trainer.</span>
                            @endif
                            @endif
                    </div>
                </div>
                @endforeach
                @else
                <div class="space-y-4">
                    <h2 class="text-xl font-bold text-slate-900">Course Overview & Study Guide</h2>
                    <p class="text-sm text-slate-700 leading-relaxed">{{ $training->description }}</p>
                    @if($assessmentReviewStatus === 'pending')
                    <p class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-900">Your assessment is pending trainer review.</p>
                    @elseif($assessmentReviewStatus === 'passed')
                    <p class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs text-emerald-900">Your assessment has been approved.</p>
                    @elseif($assessmentReviewStatus === 'needs_retake' && $hasAssessmentQuestions)
                    <a href="{{ route('jobseeker.training.quiz', $training->training_id) }}" class="inline-flex rounded-xl bg-rose-700 px-6 py-3 text-xs font-black text-white">
                        Retake Quiz
                    </a>
                    @elseif($hasAssessmentQuestions)
                    <a href="{{ route('jobseeker.training.quiz', $training->training_id) }}" class="inline-flex rounded-xl bg-emerald-600 px-6 py-3 text-xs font-black text-white">
                        Proceed to Certification Assessment
                    </a>
                    @else
                    <p class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-900">The trainer has not added assessment questions yet. Please wait for the trainer to prepare the assessment.</p>
                    @endif
                </div>
                @endif
            </div>

        </div>

    </div>
</div>
@endsection