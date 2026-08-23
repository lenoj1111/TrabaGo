@extends('layouts.trainer')

@section('title', 'Training Courses - Skills Trainer')

@section('content')
<div x-data="{ createModal: false, topics: [{ title: '', video_url: '' }] }" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Skills Curriculum Catalog
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Active Training Courses</h1>
                <p class="text-sm text-slate-300">Create and manage vocational curricula, configure learning modules, and track certified graduates.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button @click="createModal = true" type="button" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-extrabold transition-all shadow-lg shadow-emerald-500/20 hover:scale-105">
                    <span>➕</span> Create New Course
                </button>
                <a href="{{ route('trainer.enrollments.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-all border border-white/20 backdrop-blur-sm">
                    Manage Enrollments &rarr;
                </a>
            </div>
        </div>

        <!-- Courses Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($courses as $course)
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all flex flex-col justify-between gap-5">
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="rounded-full bg-emerald-50 border border-emerald-200 px-3 py-1 text-[11px] font-bold text-emerald-800 uppercase">
                                {{ $course->training_type === 'laboratory_onsite' ? 'Laboratory / Onsite' : 'Online Track' }}
                            </span>
                            <span class="text-xs text-slate-400 font-semibold">{{ $course->duration_months ?: '1' }} Month Track</span>
                        </div>

                        <div>
                            <h3 class="text-lg font-black text-slate-900 leading-snug">{{ $course->title }}</h3>
                            <p class="text-xs text-slate-500 mt-2 line-clamp-3 leading-relaxed">{{ $course->description }}</p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                        <div>
                            <span class="text-slate-400">Total Enrolled:</span>
                            <strong class="text-slate-900 ml-1">{{ $course->enrolled_count }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400">Certified:</span>
                            <strong class="text-emerald-700 ml-1">{{ $course->certs_count }}</strong>
                        </div>
                    </div>

                </div>
            @empty
                <div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-400 space-y-3">
                    <div class="text-4xl">📚</div>
                    <h3 class="text-sm font-bold text-slate-700">No training courses created yet</h3>
                    <p class="text-xs text-slate-400">Click the "Create New Course" button above to publish your first vocational curriculum.</p>
                </div>
            @endforelse
        </div>

        <div class="pt-4">
            {{ $courses->links() }}
        </div>

    </div>

    <!-- Create Course Modal -->
    <div x-show="createModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="createModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="createModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="createModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-emerald-100">
                <form action="{{ route('trainer.courses.store') }}" method="POST">
                    @csrf
                    <div class="bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 px-6 py-5 text-white flex items-center justify-between border-b border-emerald-500/20">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-2xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-lg">
                                🎓
                            </div>
                            <div>
                                <h3 class="text-base font-bold leading-6 text-white" id="modal-title">Create Training Program</h3>
                                <p class="text-xs text-emerald-300/80">Add a vocational certification course to the TrabaGo skills catalog.</p>
                            </div>
                        </div>
                        <button @click="createModal = false" type="button" class="text-slate-400 hover:text-white text-xl font-bold">&times;</button>
                    </div>

                    <div class="p-6 sm:p-8 space-y-6 max-h-[70vh] overflow-y-auto">
                        <!-- Title -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Course Title <span class="text-rose-500">*</span></label>
                            <input type="text" name="title" required placeholder="e.g. Workplace Communication & Professional Ethics" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Training Type -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Delivery Track <span class="text-rose-500">*</span></label>
                                <select name="training_type" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                                    <option value="online">Online Self-Paced Track</option>
                                    <option value="laboratory_onsite">Laboratory / Onsite Hands-on</option>
                                </select>
                            </div>

                            <!-- Duration -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Duration (Months) <span class="text-rose-500">*</span></label>
                                <input type="number" name="duration_months" value="1" min="1" max="24" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Course Description & Outcomes <span class="text-rose-500">*</span></label>
                            <textarea name="description" rows="3" required placeholder="Provide an overview of competencies, industry relevance, and learning objectives..." class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea>
                        </div>

                        <!-- Dynamic Modular Topics Section -->
                        <div class="pt-4 border-t border-slate-100 space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Initial Learning Topics / Modules</h4>
                                    <p class="text-[11px] text-slate-400">Add course chapters and video/resource URLs</p>
                                </div>
                                <button type="button" @click="topics.push({ title: '', video_url: '' })" class="px-3 py-1 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold transition-colors">
                                    + Add Topic
                                </button>
                            </div>

                            <template x-for="(topic, index) in topics" :key="index">
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3 relative">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider" x-text="'Topic #' + (index + 1)"></span>
                                        <button type="button" @click="topics.splice(index, 1)" x-show="topics.length > 1" class="text-rose-500 hover:text-rose-700 text-xs font-bold">Remove</button>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <input type="text" :name="'topics[' + index + '][title]'" x-model="topic.title" placeholder="Topic title (e.g. Module 1: Introduction)" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs bg-white text-slate-800 placeholder-slate-400 focus:border-emerald-500 outline-none">
                                        </div>
                                        <div>
                                            <input type="url" :name="'topics[' + index + '][video_url]'" x-model="topic.video_url" placeholder="Video / Resource URL (Optional)" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs bg-white text-slate-800 placeholder-slate-400 focus:border-emerald-500 outline-none">
                                        </div>
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
                            Save & Publish Course
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
