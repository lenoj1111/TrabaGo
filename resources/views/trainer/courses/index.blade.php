@extends('layouts.trainer')

@section('title', 'Training Courses Catalog - Skills Trainer')

@section('content')
<div x-data="{ 
    createModal: false, 
    editModal: false, 
    activeCourse: {},
    topics: [{ title: '', video_url: '' }] 
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-green-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-green-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-400/20 px-3 py-1 text-xs font-bold text-green-300 border border-green-400/30">
                    <span class="h-2 w-2 rounded-full bg-green-400 animate-pulse"></span>
                    Skills Curriculum Catalog
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Active Training Courses</h1>
                <p class="text-sm text-slate-300">Create vocational courses, author custom evaluation assessments, and configure automated certification.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button @click="createModal = true" type="button" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-green-500 hover:bg-green-400 text-slate-950 text-xs font-extrabold transition-all shadow-lg shadow-green-500/20 hover:scale-105">
                    <span>➕</span> Create New Course
                </button>
                <a href="{{ route('trainer.skills.enrollment') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-all border border-white/20 backdrop-blur-sm">
                    <span>🎯</span> Skills Enrollment
                </a>
            </div>
        </div>

        <!-- Search and Filter Bar -->
        <div class="rounded-2xl bg-white p-4 border border-slate-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <form action="{{ route('trainer.courses') }}" method="GET" class="w-full flex flex-col sm:flex-row items-center gap-3">
                <div class="relative w-full sm:w-80">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search course title, skills, description..." class="w-full rounded-xl border border-slate-200 pl-9 pr-4 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
                </div>

                <select name="type" onchange="this.form.submit()" class="w-full sm:w-56 rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-700 focus:border-green-500 outline-none">
                    <option value="">All Delivery Tracks</option>
                    <option value="online" {{ request('type') == 'online' ? 'selected' : '' }}>Online Self-Paced Track</option>
                    <option value="laboratory_onsite" {{ request('type') == 'laboratory_onsite' ? 'selected' : '' }}>Laboratory / Onsite Hands-on</option>
                </select>

                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-colors">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'type']))
                        <a href="{{ route('trainer.courses') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-colors">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
            
            <div class="text-xs text-slate-500 font-semibold shrink-0">
                Total Courses: <strong class="text-slate-900">{{ $courses->total() }}</strong>
            </div>
        </div>

        <!-- Courses Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($courses as $course)
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:border-green-300 transition-all flex flex-col justify-between gap-5 relative group">
                    
                    <div class="space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <span class="rounded-full bg-green-50 border border-green-200 px-3 py-1 text-[11px] font-bold text-green-800 uppercase">
                                {{ $course->training_type === 'laboratory_onsite' ? 'Laboratory / Onsite' : 'Online Track' }}
                            </span>

                            @if($course->auto_generate_certificate ?? true)
                                <span class="rounded-full bg-green-50 border border-green-200 px-2.5 py-0.5 text-[10px] font-bold text-green-800" title="Certificate automatically issued when passing score is reached">
                                    🎓 Auto-Certificate
                                </span>
                            @else
                                <span class="rounded-full bg-amber-50 border border-amber-200 px-2.5 py-0.5 text-[10px] font-bold text-amber-800" title="Requires manual trainer evaluation before certificate release">
                                    📋 Manual Evaluation
                                </span>
                            @endif
                        </div>

                        <div>
                            <h3 class="text-lg font-black text-slate-900 leading-snug">
                                <a href="{{ route('trainer.courses.show', $course->training_id) }}" class="hover:text-green-700 transition-colors">
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

                        <!-- Metrics & Assessment Status -->
                        <div class="grid grid-cols-3 gap-2 pt-3 border-t border-slate-100 text-center">
                            <div class="rounded-xl bg-slate-50 p-2">
                                <div class="text-[10px] text-slate-400 font-bold uppercase">Trainees</div>
                                <div class="text-xs font-black text-slate-900">{{ $course->enrolled_count }}</div>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-2">
                                <div class="text-[10px] text-slate-400 font-bold uppercase">Certified</div>
                                <div class="text-xs font-black text-green-700">{{ $course->certs_count }}</div>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-2">
                                <div class="text-[10px] text-slate-400 font-bold uppercase">Assessment</div>
                                <div class="text-xs font-black {{ ($course->assessments_count ?? 0) > 0 ? 'text-indigo-700' : 'text-rose-600' }}">
                                    {{ $course->assessments_count ?? 0 }} Qs
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                        <a href="{{ route('trainer.courses.show', $course->training_id) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-green-50 hover:bg-green-100 text-green-800 text-xs font-extrabold transition-colors">
                            Manage & Assessments &rarr;
                        </a>

                        <div class="flex items-center gap-1">
                            <!-- Edit Button -->
                            <button @click="activeCourse = {
                                id: {{ $course->training_id }},
                                title: '{{ addslashes($course->title) }}',
                                training_type: '{{ $course->training_type }}',
                                duration_months: {{ $course->duration_months ?: 1 }},
                                description: '{{ addslashes($course->description ?? '') }}',
                                skills: '{{ addslashes($course->skills ?? '') }}',
                                passing_score: {{ $course->passing_score ?: 80 }},
                                auto_generate_certificate: {{ ($course->auto_generate_certificate ?? true) ? 1 : 0 }}
                            }; editModal = true;" type="button" class="p-2 rounded-xl text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-colors" title="Edit Course">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>

                            <!-- Delete Form -->
                            <form action="{{ route('trainer.courses.destroy', $course->training_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this course? All modules and trainer assessments will be deleted.');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Delete Course">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            @empty
                <div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-400 space-y-3">
                    <div class="text-4xl">📚</div>
                    <h3 class="text-sm font-bold text-slate-700">No training courses found</h3>
                    <p class="text-xs text-slate-400">Click the "Create New Course" button above to publish your first vocational curriculum and configure its assessments.</p>
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

            <div x-show="createModal" x-transition class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-green-100">
                <form action="{{ route('trainer.courses.store') }}" method="POST">
                    @csrf
                    <div class="bg-gradient-to-r from-slate-950 via-green-950 to-slate-900 px-6 py-5 text-white flex items-center justify-between border-b border-green-500/20">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-2xl bg-green-500/20 border border-green-400/30 flex items-center justify-center text-lg">
                                🎓
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-white">Create Vocational Training Course</h3>
                                <p class="text-xs text-green-300/80">Author curriculum, skills competencies, and configure evaluation options.</p>
                            </div>
                        </div>
                        <button @click="createModal = false" type="button" class="text-slate-400 hover:text-white text-xl font-bold">&times;</button>
                    </div>

                    <div class="p-6 sm:p-8 space-y-5 max-h-[70vh] overflow-y-auto">
                        <!-- Title -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Course Title <span class="text-rose-500">*</span></label>
                            <input type="text" name="title" required placeholder="e.g. Shielded Metal Arc Welding (SMAW NC-II)" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 placeholder-slate-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Training Type -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Delivery Track <span class="text-rose-500">*</span></label>
                                <select name="training_type" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none">
                                    <option value="online">Online Self-Paced Track</option>
                                    <option value="laboratory_onsite">Laboratory / Onsite Hands-on</option>
                                </select>
                            </div>

                            <!-- Duration -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Duration (Months) <span class="text-rose-500">*</span></label>
                                <input type="number" name="duration_months" value="1" min="1" max="24" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none">
                            </div>
                        </div>

                        <!-- Skills Competencies -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Target Skills (Comma-Separated)</label>
                            <input type="text" name="skills" placeholder="e.g. Arc Welding, Blueprint Reading, Safety Compliance" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 placeholder-slate-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none">
                            <p class="text-[11px] text-slate-400 mt-1">These skills will be tagged to enrolled jobseekers and credited upon course certification.</p>
                        </div>

                        <!-- Passing Score -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Assessment Passing Threshold (%)</label>
                            <input type="number" name="passing_score" value="80" min="50" max="100" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none">
                        </div>

                        <!-- Certificate Generation Mode Toggle -->
                        <div class="rounded-2xl border border-green-100 bg-green-50/50 p-4 space-y-2">
                            <div class="flex items-start gap-3">
                                <input type="checkbox" id="auto_generate_certificate_create" name="auto_generate_certificate" value="1" checked class="mt-1 h-4 w-4 rounded text-green-600 focus:ring-green-500 border-slate-300">
                                <div>
                                    <label for="auto_generate_certificate_create" class="text-xs font-bold text-slate-900 cursor-pointer">
                                        Auto-Generate Certificate upon Passing Assessment
                                    </label>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                                        When checked, the system automatically generates and uploads the official Certificate of Completion to the jobseeker's Document Hub immediately upon passing your assessment. If unchecked, completion will require your manual review and approval before certificate release.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Course Description & Outcomes <span class="text-rose-500">*</span></label>
                            <textarea name="description" rows="3" required placeholder="Provide an overview of competencies, industry relevance, and learning objectives..." class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 placeholder-slate-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none"></textarea>
                        </div>

                        <!-- Initial Learning Topics Section -->
                        <div class="pt-3 border-t border-slate-100 space-y-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Course Syllabus / Topics</h4>
                                    <p class="text-[11px] text-slate-400">Chapters and resources (Assessments will be authored separately)</p>
                                </div>
                                <button type="button" @click="topics.push({ title: '', video_url: '' })" class="px-3 py-1 rounded-xl bg-green-50 hover:bg-green-100 text-green-800 text-xs font-bold transition-colors">
                                    + Add Topic
                                </button>
                            </div>

                            <template x-for="(topic, index) in topics" :key="index">
                                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2 relative">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold text-green-700 uppercase tracking-wider" x-text="'Topic #' + (index + 1)"></span>
                                        <button type="button" @click="topics.splice(index, 1)" x-show="topics.length > 1" class="text-rose-500 hover:text-rose-700 text-xs font-bold">Remove</button>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <input type="text" :name="'topics[' + index + '][title]'" x-model="topic.title" placeholder="Topic title (e.g. Module 1: Workshop Safety)" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs bg-white text-slate-800 placeholder-slate-400 focus:border-green-500 outline-none">
                                        <input type="url" :name="'topics[' + index + '][video_url]'" x-model="topic.video_url" placeholder="Resource / Video URL (Optional)" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs bg-white text-slate-800 placeholder-slate-400 focus:border-green-500 outline-none">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-3xl">
                        <button @click="createModal = false" type="button" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-green-600 hover:bg-green-500 text-white text-xs font-extrabold transition-all shadow-md shadow-green-600/20">
                            Save & Continue to Assessments &rarr;
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
                            <span class="text-xl">✏️</span>
                            <div>
                                <h3 class="text-base font-bold text-white">Edit Training Course</h3>
                                <p class="text-xs text-slate-400">Update course details and certification logic.</p>
                            </div>
                        </div>
                        <button @click="editModal = false" type="button" class="text-slate-400 hover:text-white text-xl font-bold">&times;</button>
                    </div>

                    <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Course Title <span class="text-rose-500">*</span></label>
                            <input type="text" name="title" x-model="activeCourse.title" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-green-500 outline-none">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Delivery Track <span class="text-rose-500">*</span></label>
                                <select name="training_type" x-model="activeCourse.training_type" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-green-500 outline-none">
                                    <option value="online">Online Self-Paced Track</option>
                                    <option value="laboratory_onsite">Laboratory / Onsite Hands-on</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Duration (Months) <span class="text-rose-500">*</span></label>
                                <input type="number" name="duration_months" x-model="activeCourse.duration_months" min="1" max="24" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-green-500 outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Target Skills (Comma-Separated)</label>
                            <input type="text" name="skills" x-model="activeCourse.skills" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-green-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Passing Threshold (%)</label>
                            <input type="number" name="passing_score" x-model="activeCourse.passing_score" min="50" max="100" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-green-500 outline-none">
                        </div>

                        <!-- Certificate Generation Toggle -->
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 space-y-1">
                            <div class="flex items-start gap-3">
                                <input type="checkbox" id="auto_generate_certificate_edit" name="auto_generate_certificate" value="1" :checked="activeCourse.auto_generate_certificate == 1" class="mt-1 h-4 w-4 rounded text-green-600 focus:ring-green-500 border-slate-300">
                                <div>
                                    <label for="auto_generate_certificate_edit" class="text-xs font-bold text-slate-900 cursor-pointer">
                                        Auto-Generate Certificate upon Passing Assessment
                                    </label>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                                        Enable for immediate automated certificate issuance upon passing score. Uncheck if you wish to manually assess and release certificates.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Course Description <span class="text-rose-500">*</span></label>
                            <textarea name="description" x-model="activeCourse.description" rows="3" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-green-500 outline-none"></textarea>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-3xl">
                        <button @click="editModal = false" type="button" class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-green-600 hover:bg-green-500 text-white text-xs font-bold transition-all shadow-md shadow-green-600/20">
                            Update Course
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
