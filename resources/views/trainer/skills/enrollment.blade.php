@extends('layouts.trainer')

@section('title', 'Training Skills Enrollment - Skills Trainer Portal')

@section('content')
<div x-data="{ 
    enrollModal: false, 
    selectedJobseeker: { id: null, name: '', email: '' },
    selectedCourseId: '',
    targetSkills: '',
    courses: {{ json_encode($courses) }},
    onCourseChange() {
        const found = this.courses.find(c => c.training_id == this.selectedCourseId);
        if (found && found.skills) {
            this.targetSkills = found.skills;
        }
    }
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Workforce Upskilling & Competencies
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Training Skills Enrollment</h1>
                <p class="text-sm text-slate-300 max-w-2xl">
                    Enroll jobseekers into vocational training courses targeting specialized skills and industry competencies.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('trainer.courses') }}" class="px-5 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-all border border-white/20 backdrop-blur-sm">
                    View Courses Catalog &rarr;
                </a>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="rounded-2xl bg-white p-4 border border-slate-200 shadow-sm flex items-center justify-between gap-4">
            <form action="{{ route('trainer.skills.enrollment') }}" method="GET" class="w-full flex items-center gap-3">
                <div class="relative w-full sm:w-96">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search candidate name or email..." class="w-full rounded-xl border border-slate-200 pl-9 pr-4 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 focus:border-emerald-500 outline-none">
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-colors">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('trainer.skills.enrollment') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-colors">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left 2 Cols: Candidate Pool to Enroll -->
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h2 class="text-base font-black text-slate-900">Jobseeker Talent Pool</h2>
                            <p class="text-xs text-slate-500">Select candidates needing upskilling to enroll into specialized tracks.</p>
                        </div>
                        <span class="text-xs font-bold text-slate-600">
                            Page {{ $jobseekers->currentPage() }} of {{ $jobseekers->lastPage() }}
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-100">
                                <tr>
                                    <th class="py-3 px-3">Candidate</th>
                                    <th class="py-3 px-3">Contact</th>
                                    <th class="py-3 px-3">Status</th>
                                    <th class="py-3 px-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($jobseekers as $js)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="py-3 px-3">
                                            <div class="font-bold text-slate-900">{{ $js->first_name }} {{ $js->last_name }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $js->user_email }}</div>
                                        </td>
                                        <td class="py-3 px-3 text-slate-600 font-medium">
                                            {{ $js->mobile_number ?: 'N/A' }}
                                        </td>
                                        <td class="py-3 px-3 text-slate-600">
                                            {{ ucwords(str_replace('_', ' ', $js->employment_status ?? 'Seeking Work')) }}
                                        </td>
                                        <td class="py-3 px-3 text-right">
                                            <button @click="selectedJobseeker = { id: {{ $js->jobseeker_id }}, name: '{{ addslashes($js->first_name . ' ' . $js->last_name) }}', email: '{{ addslashes($js->user_email) }}' }; enrollModal = true;" type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-extrabold transition-colors">
                                                <span>🎯</span> Enroll in Skills Track
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-slate-400 text-xs">
                                            No jobseekers found matching your search.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="pt-3">
                        {{ $jobseekers->links() }}
                    </div>
                </div>
            </div>

            <!-- Right Col: Recent Skills Enrollments Activity -->
            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                    <div class="pb-3 border-b border-slate-100">
                        <h3 class="text-sm font-black text-slate-900">Recent Skills Enrollments</h3>
                        <p class="text-[11px] text-slate-400">Trainees active in skill development tracks</p>
                    </div>

                    <div class="space-y-3">
                        @forelse($recentEnrollments as $renr)
                            <div class="p-3.5 rounded-2xl border border-slate-100 bg-slate-50 space-y-2 text-xs">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <span class="font-bold text-slate-900 block">{{ $renr->first_name }} {{ $renr->last_name }}</span>
                                        <span class="text-[11px] text-emerald-700 font-semibold">{{ $renr->course_title }}</span>
                                    </div>
                                    <span class="rounded-full px-2 py-0.5 text-[9px] font-black uppercase {{ $renr->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $renr->status }}
                                    </span>
                                </div>

                                @if(!empty($renr->enrolled_skills))
                                    <div class="flex flex-wrap gap-1 pt-1">
                                        @foreach(array_map('trim', explode(',', $renr->enrolled_skills)) as $sk)
                                            @if(!empty($sk))
                                                <span class="rounded bg-white text-slate-700 px-1.5 py-0.5 text-[10px] font-semibold border border-slate-200">
                                                    {{ $sk }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="py-6 text-center text-slate-400 text-xs italic">
                                No skill enrollments recorded yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Enroll Modal -->
    <div x-show="enrollModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="enrollModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="enrollModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="enrollModal" x-transition class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-emerald-100">
                <form action="{{ route('trainer.skills.enrollment.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="jobseeker_id" :value="selectedJobseeker.id">

                    <div class="bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 px-6 py-5 text-white flex items-center justify-between border-b border-emerald-500/20">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">🎯</span>
                            <div>
                                <h3 class="text-base font-bold text-white">Enroll in Skills Training Track</h3>
                                <p class="text-xs text-emerald-300/80">Target specific skills for candidate development.</p>
                            </div>
                        </div>
                        <button @click="enrollModal = false" type="button" class="text-slate-400 hover:text-white text-xl font-bold">&times;</button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="rounded-2xl bg-emerald-50/80 border border-emerald-100 p-3.5 space-y-1">
                            <span class="text-[10px] uppercase font-bold text-emerald-700 tracking-wider">Candidate</span>
                            <div class="text-sm font-black text-slate-900" x-text="selectedJobseeker.name"></div>
                            <div class="text-xs text-slate-500" x-text="selectedJobseeker.email"></div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Select Training Course <span class="text-rose-500">*</span></label>
                            <select name="training_id" x-model="selectedCourseId" @change="onCourseChange()" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none">
                                <option value="">-- Choose Course --</option>
                                <template x-for="c in courses" :key="c.training_id">
                                    <option :value="c.training_id" x-text="c.title + ' (' + c.training_type + ')'"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Target Skills to Develop <span class="text-rose-500">*</span></label>
                            <input type="text" name="enrolled_skills" x-model="targetSkills" required placeholder="e.g. Electrical Safety, Circuit Diagnostics, Wiring" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none">
                            <p class="text-[11px] text-slate-400 mt-1">Comma-separated competencies being developed in this enrollment.</p>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-3xl">
                        <button @click="enrollModal = false" type="button" class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-extrabold shadow-md shadow-emerald-600/20 transition-all">
                            Confirm Enrollment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
