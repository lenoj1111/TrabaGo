@extends('layouts.jobseeker')

@section('title', 'Vocational Training Skills - TrabaGo')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Hero Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="max-w-2xl space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    DMDP Training Skills Pathway
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Vocational Training Skills</h1>
                <p class="text-sm text-slate-300 leading-relaxed">
                    Explore high-demand technical and vocational competencies offered through DMDP programs. Enroll directly in courses to train with accredited instructors and add verified skills to your profile.
                </p>
            </div>

            <div class="shrink-0 flex sm:flex-col items-center sm:items-end gap-3">
                <div class="bg-white/10 backdrop-blur rounded-2xl p-4 border border-white/10 text-center min-w-[140px]">
                    <span class="text-[11px] font-bold text-emerald-300 uppercase tracking-wider">Your Verified Skills</span>
                    <p class="text-3xl font-black text-emerald-400 mt-0.5">{{ count($userSkills) }}</p>
                </div>
                <a href="{{ route('jobseeker.training.enrollments') }}" 
                   class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2.5 text-xs font-black shadow-md shadow-emerald-600/30 transition-all">
                    <span>📋</span> My Training Enrollments &rarr;
                </a>
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

        <!-- Subnav & Search Bar -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            <!-- Nav Tabs -->
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('jobseeker.training.skills') }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-900 text-white shadow-sm flex items-center gap-2">
                    <span>🎯 View Training Skills</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-emerald-600 text-white font-black">
                        {{ count($skillsCatalog) }}
                    </span>
                </a>

                <a href="{{ route('jobseeker.training') }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 transition-colors">
                    <span>📚 All Courses Catalog</span>
                </a>

                <a href="{{ route('jobseeker.training.enrollments') }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 transition-colors flex items-center gap-1.5">
                    <span>📋 Training Enrollment</span>
                </a>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('jobseeker.training.skills') }}" class="flex items-center gap-2 max-w-sm w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search skills or course titles..."
                       class="w-full rounded-2xl border border-slate-200 px-4 py-2 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">
                    Filter
                </button>
            </form>
        </div>

        <!-- Skills Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($skillsCatalog as $item)
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all flex flex-col justify-between gap-5">
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="rounded-full px-2.5 py-0.5 text-[11px] font-bold uppercase {{ $item['course_type'] === 'laboratory_onsite' ? 'bg-purple-50 text-purple-800 border border-purple-200' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' }}">
                                {{ $item['course_type'] === 'laboratory_onsite' ? 'On-site Practical' : 'Online Learning' }}
                            </span>

                            @if($item['is_earned'])
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 text-emerald-900 border border-emerald-300 px-2.5 py-0.5 text-[11px] font-black">
                                    <span>✓</span> Verified Skill
                                </span>
                            @elseif(in_array($item['status'], ['enrolled', 'in_progress']))
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 text-amber-900 border border-amber-300 px-2.5 py-0.5 text-[11px] font-bold">
                                    <span>📚</span> In Training
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 text-slate-600 px-2.5 py-0.5 text-[11px] font-semibold">
                                    Available
                                </span>
                            @endif
                        </div>

                        <div>
                            <h3 class="text-base font-black text-slate-900 leading-snug">
                                {{ $item['skill_name'] }}
                            </h3>
                            <p class="text-xs text-slate-500 mt-1">
                                Taught in: <span class="font-bold text-slate-700">{{ $item['course_title'] }}</span>
                            </p>
                        </div>

                        <div class="bg-slate-50 rounded-2xl p-3 border border-slate-100 text-xs text-slate-600 space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Duration:</span>
                                <span class="font-bold text-slate-800">{{ $item['duration_months'] }} Month(s)</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Modules:</span>
                                <span class="font-bold text-slate-800">{{ $item['topics_count'] }} Topics</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Passing Score:</span>
                                <span class="font-bold text-emerald-700">{{ $item['passing_score'] }}%</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                        <a href="{{ route('jobseeker.training.show', $item['course_id']) }}" 
                           class="text-xs font-bold text-slate-500 hover:text-emerald-700">
                            Course Details &rarr;
                        </a>

                        @if($item['is_earned'] && $item['certificate_issued'] && $item['enrollment_id'])
                            <a href="{{ route('jobseeker.certificates.preview', $item['enrollment_id']) }}" target="_blank"
                               class="inline-flex items-center gap-1 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 px-3.5 py-2 text-xs font-black shadow-sm">
                                <span>🎓</span> View Certificate
                            </a>
                        @elseif(in_array($item['status'], ['enrolled', 'in_progress']))
                            <a href="{{ route('jobseeker.training.show', $item['course_id']) }}" 
                               class="inline-flex items-center gap-1 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white px-3.5 py-2 text-xs font-bold shadow-sm">
                                Continue &rarr;
                            </a>
                        @else
                            <form action="{{ route('jobseeker.training.enroll', $item['course_id']) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="inline-flex items-center gap-1 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 text-xs font-black shadow-md shadow-emerald-600/20 transition-all hover:scale-105 cursor-pointer">
                                    <span>+</span> Enroll in Training
                                </button>
                            </form>
                        @endif
                    </div>

                </div>
            @empty
                <div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-400 space-y-2">
                    <p class="text-sm font-bold text-slate-700">No training skills found matching your search.</p>
                    <a href="{{ route('jobseeker.training.skills') }}" class="inline-flex text-xs font-bold text-emerald-700 hover:underline">
                        Reset Filter
                    </a>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
