@extends('layouts.trainer')

@section('title', 'Training Courses Catalog - Skills Trainer')

@section('content')
<div x-data="{ 
    createModal: false, 
    editModal: false, 
    deleteModal: false,
    archiveModal: false,      
    deleteUrl: '',
    archiveUrl: '',           
    activeCourse: {},
    formType: 'online',
    editFormType: 'online',
    topics: [{ title: '', video_url: '' }] 
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="w-full max-w-[1680px] mx-auto space-y-8">

        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Skills Curriculum Catalog
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Active Training Courses</h1>
                <p class="text-sm text-slate-300">Create vocational courses, author assessment questions, and review learner completions before issuing certificates.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button @click="createModal = true; formType = 'online'" type="button" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-extrabold transition-all shadow-lg shadow-emerald-500/20 hover:scale-105">
                    <svg class="h-4 w-4 text-slate-950" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Create New Course</span>
                </button>
                <a href="{{ route('trainer.skills.enrollment') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-all border border-white/20 backdrop-blur-sm">
                    <svg class="h-4 w-4 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>Skills Enrollment</span>
                </a>
            </div>
        </div>

        <!-- Search and Filter Bar -->
        <div class="rounded-2xl bg-white p-4 border border-slate-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <form action="{{ route('trainer.courses') }}" method="GET" class="w-full flex flex-col sm:flex-row items-center gap-3">

                <!-- Search Input -->
                <div class="relative w-full sm:w-80">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search course title, skills, description..." class="w-full rounded-xl border border-slate-200 pl-9 pr-4 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                </div>

                <!-- Delivery Track Dropdown -->
                <select name="type" onchange="this.form.submit()" class="w-full sm:w-56 rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-700 focus:border-emerald-500 outline-none">
                    <option value="">All Delivery Tracks</option>
                    <option value="online" {{ request('type') == 'online' ? 'selected' : '' }}>Online Self-Paced Track</option>
                    <option value="laboratory_onsite" {{ request('type') == 'laboratory_onsite' ? 'selected' : '' }}>Laboratory / Onsite Hands-on</option>
                </select>

                <!-- Buttons -->
                <div class="flex items-center gap-2">

                    <!-- Show Archived Toggle -->
                    <label class="flex items-center gap-1.5 px-3 py-2 rounded-xl border {{ request('show_archived') ? 'bg-amber-50 border-amber-300 text-amber-800' : 'bg-white border-slate-200 text-slate-600' }} text-xs font-bold transition-colors cursor-pointer hover:bg-slate-50" title="Show Archived Courses">
                        <input type="checkbox" name="show_archived" value="1" onchange="this.form.submit()" {{ request('show_archived') ? 'checked' : '' }} class="hidden">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                        <span>Archived</span>
                    </label>

                    <!-- Filter Button -->
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-colors">
                        Filter
                    </button>

                    <!-- Clear Button -->
                    @if(request()->hasAny(['search', 'type', 'show_archived']))
                    <a href="{{ route('trainer.courses') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-colors">
                        Clear
                    </a>
                    @endif
                </div>
            </form>

            <!-- Total Counter -->
            <div class="text-xs text-slate-500 font-semibold shrink-0">
                Total Courses: <strong class="text-slate-900">{{ $courses->total() }}</strong>
            </div>
        </div>

        <!-- Courses Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($courses as $course)
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all flex flex-col justify-between gap-5 relative group">

                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <span class="rounded-full bg-emerald-50 border border-emerald-200 px-3 py-1 text-[11px] font-bold text-emerald-800 uppercase">
                            {{ $course->training_type === 'laboratory_onsite' ? 'Laboratory / Onsite' : 'Online Track' }}
                        </span>

                        <!-- Essay assessments require trainer review before certificate issuance -->
                        @if($course->training_type !== 'laboratory_onsite' && ($course->essay_assessments_count ?? 0) > 0)
                        <span class="rounded-full bg-amber-50 border border-amber-200 px-2.5 py-0.5 text-[10px] font-bold text-amber-800 inline-flex items-center gap-1" title="Trainer review is required before certificate issuance">
                            <svg class="h-3 w-3 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span>Trainer Review Required</span>
                        </span>
                        @elseif($course->training_type !== 'laboratory_onsite' && ($course->assessments_count ?? 0) > 0)
                        <span class="rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-[10px] font-bold text-emerald-800 inline-flex items-center gap-1" title="Certificate is generated automatically after the learner passes the assessment">
                            <svg class="h-3 w-3 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Auto-Certificate on Pass</span>
                        </span>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-lg font-black text-slate-900 leading-snug">
                            <a href="{{ route('trainer.courses.show', $course->training_id) }}" class="hover:text-emerald-700 transition-colors">
                                {{ $course->title }}
                            </a>
                        </h3>
                        <p class="text-xs text-slate-500 mt-2 line-clamp-3 leading-relaxed">{{ $course->description }}</p>
                    </div>

                    <!-- Skills Competencies -->
                    @if(!empty($course->skills))
                    <div class="pt-2">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Target Skills:</span>
                        <div class="flex flex-wrap gap-1">
                            @foreach(array_map('trim', explode(',', $course->skills)) as $skill)
                            @if(!empty($skill))
                            <span class="rounded-lg bg-slate-100 text-slate-700 px-2 py-0.5 text-[10px] font-semibold">
                                {{ $skill }}
                            </span>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Metrics -->
                    <div class="grid grid-cols-3 gap-2 pt-3 border-t border-slate-100 text-center">
                        <div class="rounded-xl bg-slate-50 p-2">
                            <div class="text-[10px] text-slate-400 font-bold uppercase">Trainees</div>
                            <div class="text-xs font-black text-slate-900">{{ $course->enrolled_count }} / {{ $course->trainee_capacity ?: 'Unlimited' }}</div>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-2">
                            <div class="text-[10px] text-slate-400 font-bold uppercase">Certified</div>
                            <div class="text-xs font-black text-emerald-700">{{ $course->certs_count }}</div>
                        </div>

                        @if($course->training_type === 'laboratory_onsite')
                        <div class="rounded-xl bg-slate-50 p-2">
                            <div class="text-[10px] text-slate-400 font-bold uppercase">Type</div>
                            <div class="text-xs font-black text-slate-700">Onsite</div>
                        </div>
                        @else
                        <div class="rounded-xl bg-slate-50 p-2">
                            <div class="text-[10px] text-slate-400 font-bold uppercase">Assessment</div>
                            <div class="text-xs font-black {{ ($course->assessments_count ?? 0) > 0 ? 'text-indigo-700' : 'text-rose-600' }}">
                                {{ $course->assessments_count ?? 0 }} Qs
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Date and Location -->
                    <div class="space-y-2 pt-2">
                        <div class="text-[11px] font-semibold text-slate-500">
                            {{ $course->training_start_date ? \Carbon\Carbon::parse($course->training_start_date)->format('M j, Y') : 'Start date unset' }}
                            <span class="px-1 text-slate-300">to</span>
                            {{ $course->training_end_date ? \Carbon\Carbon::parse($course->training_end_date)->format('M j, Y') : 'End date unset' }}
                        </div>

                        <!-- Full-width Location Badge -->
                        @if($course->training_type === 'laboratory_onsite')
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-100 text-emerald-800 w-full">
                            <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-[11px] font-bold truncate" title="{{ $course->location }}">
                                {{ $course->location ?: 'Location TBA' }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('trainer.courses.show', $course->training_id) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-extrabold transition-colors">
                            Manage & Assessments &rarr;
                        </a>
                        <a href="{{ route('trainer.courses.enrollments', $course->training_id) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-800 text-xs font-extrabold transition-colors">
                            View Enrolled ({{ $course->enrolled_count }})
                        </a>
                    </div>

                    <div class="flex w-fit items-center gap-1.5 rounded-xl border border-slate-200 bg-white p-2">
                        <!-- Edit Button -->
                        <button @click="activeCourse = {
                                id: {{ $course->training_id }},
                                title: '{{ addslashes($course->title) }}',
                                training_type: '{{ $course->training_type }}',
                                location: '{{ addslashes($course->location ?? '') }}',
                                training_start_date: '{{ $course->training_start_date }}',
                                training_end_date: '{{ $course->training_end_date }}',
                                trainee_capacity: {{ $course->trainee_capacity ?: 1 }},
                                description: '{{ addslashes($course->description ?? '') }}',
                                skills: '{{ addslashes($course->skills ?? '') }}',
                                passing_score: {{ $course->passing_score ?: 80 }},
                                demographics: @js($course->demographic_requirements ?? ['education_levels' => []]),
                            }; editFormType = '{{ $course->training_type }}'; editModal = true;" type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-bold text-slate-700 transition-colors hover:bg-slate-200" title="Edit Course">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            <span>Edit</span>
                        </button>

                        <!-- Delete OR Archive Button -->
                        @if($course->certs_count > 0)
                        <button @click="archiveUrl = '{{ route('trainer.courses.archive', $course->training_id) }}'; archiveModal = true;" type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 px-2.5 py-1.5 text-xs font-bold text-amber-700 transition-colors hover:bg-amber-100" title="Archive Course">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                            <span>Archive</span>
                        </button>
                        @else
                        <button @click="deleteUrl = '{{ route('trainer.courses.destroy', $course->training_id) }}'; deleteModal = true;" type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-rose-50 px-2.5 py-1.5 text-xs font-bold text-rose-700 transition-colors hover:bg-rose-100" title="Delete Course">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Delete</span>
                        </button>
                        @endif


                        <!-- Manage Certificate Button -->
                        <a href="{{ route('trainer.courses.certificate-content', $course->training_id) }}"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 text-xs font-bold transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Manage Certificate</span>
                        </a>
                    </div>
                </div>

            </div>
            @empty
            <div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-400 space-y-3">
                <div class="h-14 w-14 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                    <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-700">No training courses found</h3>
                <p class="text-xs text-slate-400">Click the "Create New Course" button above to publish your first vocational curriculum.</p>
            </div>
            @endforelse
        </div>

        <div class="pt-4">
            {{ $courses->links() }}
        </div>

    </div>

    <!-- Create Course Modal -->
    <div x-show="createModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="createModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="createModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="createModal" x-transition class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-emerald-100">
                <form action="{{ route('trainer.courses.store') }}" method="POST">
                    @csrf
                    <div class="bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 px-6 py-5 text-white flex items-center justify-between border-b border-emerald-500/20">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-2xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-emerald-300">
                                <svg class="h-5 w-5 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-white">Create Vocational Training Course</h3>
                            </div>
                        </div>
                        <button @click="createModal = false" type="button" class="text-slate-400 hover:text-white text-xl font-bold">&times;</button>
                    </div>

                    <div class="p-6 sm:p-8 space-y-5 max-h-[70vh] overflow-y-auto">
                        <!-- Title -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Course Title <span class="text-rose-500">*</span></label>
                            <input type="text" name="title" required placeholder="e.g. Shielded Metal Arc Welding (SMAW NC-II)" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 placeholder-slate-400 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Delivery Track -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Delivery Track <span class="text-rose-500">*</span></label>
                                <select name="training_type" x-model="formType" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none">
                                    <option value="online">Online Self-Paced Track</option>
                                    <option value="laboratory_onsite">Laboratory / Onsite Hands-on</option>
                                </select>
                            </div>

                            <!-- Location (Only for Onsite) -->
                            <div x-show="formType === 'laboratory_onsite'" x-cloak>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Workshop Location <span class="text-rose-500">*</span></label>
                                <input type="text" name="location" placeholder="e.g. DMDP Training Center, Lab 2" :required="formType === 'laboratory_onsite'" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Training Start Date <span class="text-rose-500">*</span></label>
                                <input type="date" name="training_start_date" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Training End Date <span class="text-rose-500">*</span></label>
                                <input type="date" name="training_end_date" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Trainee Capacity <span class="text-rose-500">*</span></label>
                                <input type="number" name="trainee_capacity" min="1" max="10000" required placeholder="Maximum number of trainees" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none">
                            </div>

                            <!-- Assessment Passing Threshold (Only for Online) -->
                            <div x-show="formType === 'online'" x-cloak>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Assessment Passing Threshold (%)</label>
                                <input type="number" name="passing_score" value="80" min="50" max="100" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none">
                            </div>
                        </div>

                        <!-- Target Skills -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Target Skills (Comma-Separated)</label>
                            <input type="text" name="skills" placeholder="e.g. Arc Welding, Blueprint Reading, Safety Compliance" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 placeholder-slate-400 focus:border-emerald-500 outline-none">
                            <p class="text-[11px] text-slate-400 mt-1">These skills will be tagged to enrolled jobseekers and credited upon course certification.</p>
                        </div>

                        <!-- Demographics -->
                        <div class="pt-3 border-t border-slate-100 space-y-3">
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Optional Demographic Eligibility</h4>
                                <p class="text-[11px] text-slate-400">Leave every field blank to accept all applicants.</p>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Gender</label>
                                    <select name="demographics[gender]" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-800 focus:border-emerald-500 outline-none">
                                        <option value="">Open to all</option>
                                        <option value="Male">Men only</option>
                                        <option value="Female">Women only</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Minimum Age</label>
                                    <input type="number" name="demographics[min_age]" min="15" max="100" placeholder="No minimum" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-800 focus:border-emerald-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Maximum Age</label>
                                    <input type="number" name="demographics[max_age]" min="15" max="100" placeholder="No maximum" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-800 focus:border-emerald-500 outline-none">
                                </div>
                            </div>
                            <fieldset>
                                <legend class="block text-xs font-bold text-slate-700 mb-2">Educational Attainment</legend>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs text-slate-700">
                                    @foreach(['Elementary', 'High School', 'Senior High School', 'Vocational', 'College', 'Post Graduate'] as $level)
                                    <label class="inline-flex items-center gap-2"><input type="checkbox" name="demographics[education_levels][]" value="{{ $level }}" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">{{ $level }}</label>
                                    @endforeach
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">Applicants matching any selected level can enroll.</p>
                            </fieldset>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Course Description & Outcomes <span class="text-rose-500">*</span></label>
                            <textarea name="description" rows="3" required placeholder="Provide an overview of competencies, industry relevance, and learning objectives..." class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 placeholder-slate-400 focus:border-emerald-500 outline-none"></textarea>
                        </div>

                        <!-- Initial Learning Topics Section (Only for Online) -->
                        <div x-show="formType === 'online'" x-cloak class="pt-3 border-t border-slate-100 space-y-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Course Syllabus / Topics</h4>
                                    <p class="text-[11px] text-slate-400">Chapters and resources (Assessments will be authored separately)</p>
                                </div>
                                <button type="button" @click="topics.push({ title: '', video_url: '' })" class="px-3 py-1 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold transition-colors">
                                    + Add Topic
                                </button>
                            </div>

                            <template x-for="(topic, index) in topics" :key="index">
                                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2 relative">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider" x-text="'Topic #' + (index + 1)"></span>
                                        <button type="button" @click="topics.splice(index, 1)" x-show="topics.length > 1" class="text-rose-500 hover:text-rose-700 text-xs font-bold">Remove</button>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <input type="text" :name="'topics[' + index + '][title]'" x-model="topic.title" placeholder="Topic title (e.g. Module 1: Workshop Safety)" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs bg-white text-slate-800 placeholder-slate-400 focus:border-emerald-500 outline-none">
                                        <input type="url" :name="'topics[' + index + '][video_url]'" x-model="topic.video_url" placeholder="Resource / Video URL (Optional)" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs bg-white text-slate-800 placeholder-slate-400 focus:border-emerald-500 outline-none">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-3xl">
                        <button @click="createModal = false" type="button" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-extrabold transition-all shadow-md shadow-emerald-600/20">
                            Save Course
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Course Modal -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="editModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="editModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="editModal" x-transition class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-200">
                <form :action="'/trainer/courses/' + activeCourse.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-slate-900 px-6 py-5 text-white flex items-center justify-between border-b border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-xl bg-slate-800 text-slate-300 flex items-center justify-center">
                                <svg class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-white">Edit Training Course</h3>
                            </div>
                        </div>
                        <button @click="editModal = false" type="button" class="text-slate-400 hover:text-white text-xl font-bold">&times;</button>
                    </div>

                    <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Course Title <span class="text-rose-500">*</span></label>
                            <input type="text" name="title" x-model="activeCourse.title" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Delivery Track <span class="text-rose-500">*</span></label>
                                <select name="training_type" x-model="editFormType" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none">
                                    <option value="online">Online Self-Paced Track</option>
                                    <option value="laboratory_onsite">Laboratory / Onsite Hands-on</option>
                                </select>
                            </div>

                            <!-- Location (Only for Onsite) -->
                            <div x-show="editFormType === 'laboratory_onsite'" x-cloak>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Workshop Location <span class="text-rose-500">*</span></label>
                                <input type="text" name="location" x-model="activeCourse.location" placeholder="e.g. DMDP Training Center, Lab 2" :required="editFormType === 'laboratory_onsite'" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Training Start Date <span class="text-rose-500">*</span></label>
                                <input type="date" name="training_start_date" x-model="activeCourse.training_start_date" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Training End Date <span class="text-rose-500">*</span></label>
                                <input type="date" name="training_end_date" x-model="activeCourse.training_end_date" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Trainee Capacity <span class="text-rose-500">*</span></label>
                                <input type="number" name="trainee_capacity" x-model="activeCourse.trainee_capacity" min="1" max="10000" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none">
                            </div>

                            <!-- Passing Score (Only for Online) -->
                            <div x-show="editFormType === 'online'" x-cloak>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Passing Threshold (%)</label>
                                <input type="number" name="passing_score" x-model="activeCourse.passing_score" min="50" max="100" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Target Skills (Comma-Separated)</label>
                            <input type="text" name="skills" x-model="activeCourse.skills" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Course Description <span class="text-rose-500">*</span></label>
                            <textarea name="description" x-model="activeCourse.description" rows="3" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none"></textarea>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-3xl">
                        <button @click="editModal = false" type="button" class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-md shadow-emerald-600/20">
                            Update Course
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- CUSTOM DELETE WARNING MODAL -->
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="deleteModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="deleteModal = false"></div>

            <div x-show="deleteModal" x-transition class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-rose-100">
                <div class="bg-rose-50 px-6 py-6 flex items-start gap-4">
                    <div class="h-12 w-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-rose-950">Delete Training Course?</h3>
                        <p class="text-sm text-rose-800 mt-1">
                            Are you absolutely sure you want to delete this course? This action will permanently remove all associated assessments, modules, and enrollment records. <strong>This cannot be undone.</strong>
                        </p>
                    </div>
                </div>

                <div class="bg-white px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button @click="deleteModal = false" type="button" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 transition-colors">
                        Cancel
                    </button>

                    <form :action="deleteUrl" method="POST" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-black shadow-lg shadow-rose-600/30 transition-all">
                            Yes, Delete Permanently
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- CUSTOM ARCHIVE WARNING MODAL -->
    <div x-show="archiveModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="archiveModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="archiveModal = false"></div>

            <div x-show="archiveModal" x-transition class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-amber-100">
                <div class="bg-amber-50 px-6 py-6 flex items-start gap-4">
                    <div class="h-12 w-12 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-amber-950">Archive Training Course?</h3>
                        <p class="text-sm text-amber-800 mt-1">
                            Because learners have already received official certificates for this course, it cannot be deleted. Instead, it will be **Archived**.
                        </p>
                        <p class="text-xs text-amber-700 mt-2 font-semibold">
                            Archived courses are hidden from the public catalog, but learner certificates remain valid.
                        </p>
                    </div>
                </div>

                <div class="bg-white px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button @click="archiveModal = false" type="button" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 transition-colors">
                        Cancel
                    </button>

                    <form :action="archiveUrl" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-black shadow-lg shadow-amber-600/30 transition-all">
                            Yes, Archive Course
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection