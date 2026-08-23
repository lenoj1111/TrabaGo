@extends('layouts.jobseeker')

@section('title', $training->title . ' - Training Course')

@section('content')
<div x-data="{ activeTopic: 0 }" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-5xl space-y-8">
        
        <!-- Navigation -->
        <div class="flex items-center justify-between">
            <a href="{{ route('jobseeker.training') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-emerald-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to All Courses
            </a>
            <span class="text-xs text-slate-400 font-mono">Course ID: #{{ $training->training_id }}</span>
        </div>

        @php
            $isCompleted = $enrollment && $enrollment->status === 'completed';
        @endphp

        <!-- Course Header Card in Emerald Theme -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="flex items-center gap-2">
                    <span class="rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                        {{ ucfirst($training->training_type ?: 'Online') }}
                    </span>
                    @if($isCompleted)
                        <span class="rounded-full bg-emerald-500/30 px-3 py-1 text-xs font-bold text-emerald-200 border border-emerald-400/40 flex items-center gap-1">
                            ✓ Certificate Earned
                        </span>
                    @endif
                </div>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">{{ $training->title }}</h1>
                <p class="text-sm text-slate-300 leading-relaxed">{{ $training->description ?: 'Complete all module topics and pass the quiz to earn your verified profile skill badge.' }}</p>
            </div>

            <div class="shrink-0 flex flex-col items-center sm:items-end gap-3">
                @if($enrollment && $enrollment->certificate_issued)
                    <a href="{{ route('jobseeker.certificates.preview', $enrollment->enrollment_id) }}" target="_blank"
                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 px-5 py-2.5 text-xs font-black shadow-lg shadow-amber-500/20 transition-all hover:scale-105">
                        <span>🎓</span> View & Download Certificate
                    </a>
                @endif
                <a href="{{ route('jobseeker.training.quiz', $training->training_id) }}" 
                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 px-6 py-3 text-xs font-black text-white shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                    {{ $isCompleted ? 'Retake Quiz' : 'Take Skill Quiz' }}
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>

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
                            <svg class="h-4 w-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    @empty
                        <p class="text-xs text-slate-400 italic p-2">No specific module topics listed. Read overview and proceed to quiz!</p>
                    @endforelse
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <a href="{{ route('jobseeker.training.quiz', $training->training_id) }}" 
                       class="w-full inline-flex items-center justify-center rounded-xl bg-slate-900 text-white px-4 py-3 text-xs font-bold hover:bg-emerald-600 transition-colors">
                        Ready for Assessment? Take Quiz
                    </a>
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
                                            <div class="h-12 w-12 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-xl shrink-0">
                                                🎬
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

                            <!-- Key Lesson Takeaways in Emerald Theme -->
                            <div class="rounded-2xl bg-emerald-50/50 p-6 border border-emerald-100 space-y-3">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-900 flex items-center gap-2">
                                    <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
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
                                    <a href="{{ route('jobseeker.training.quiz', $training->training_id) }}" class="rounded-xl bg-emerald-600 text-white px-6 py-2.5 text-xs font-black hover:bg-emerald-500 shadow-md">
                                        Finish & Take Skill Quiz &rarr;
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="space-y-4">
                        <h2 class="text-xl font-bold text-slate-900">Course Overview & Study Guide</h2>
                        <p class="text-sm text-slate-700 leading-relaxed">{{ $training->description }}</p>
                        <a href="{{ route('jobseeker.training.quiz', $training->training_id) }}" class="inline-flex rounded-xl bg-emerald-600 px-6 py-3 text-xs font-black text-white">
                            Proceed to Certification Assessment
                        </a>
                    </div>
                @endif
            </div>

        </div>

    </div>
</div>
@endsection
