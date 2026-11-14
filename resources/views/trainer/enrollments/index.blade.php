@extends('layouts.trainer')

@section('title', 'Manage Course Enrollments - Skills Trainer')

@section('content')
<div x-data="{
    statusModal: false,
    selectedId: null,
    selectedName: '',
    selectedStatus: 'enrolled',
    selectedRemarks: '',
    
    assessModal: false,
    assessId: null,
    assessName: '',
    assessCourse: '',
    assessType: '',
    selectedOutcome: null,
    labRemarks: '',
    trainerFeedback: '',

    openStatus(id, name, status, remarks) {
        this.selectedId = id;
        this.selectedName = name;
        this.selectedStatus = status;
        this.selectedRemarks = remarks || '';
        this.statusModal = true;
    },

    openAssess(id, name, course, type) {
        this.assessId = id;
        this.assessName = name;
        this.assessCourse = course;
        this.assessType = type;
        this.selectedOutcome = null;
        this.labRemarks = '';
        this.trainerFeedback = '';
        this.assessModal = true;
    }
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="w-full max-w-[1680px] mx-auto space-y-8">

        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Learner Progress & Certification
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Enrolled Job Seekers & Training Progress</h1>
                <p class="text-sm text-slate-300">Oversee active learners, update enrollment statuses, conduct assessments, mark completions, and generate official skills certificates.</p>
            </div>

            <div class="shrink-0 bg-white/10 backdrop-blur rounded-2xl p-5 border border-white/10 text-center min-w-[150px]">
                <span class="text-xs font-bold text-emerald-300 uppercase tracking-wider">Total Learners</span>
                <p class="text-3xl font-black text-emerald-400 mt-0.5">{{ $enrollments->total() }}</p>
                <span class="text-[10px] text-slate-300">Enrolled in Programs</span>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="GET" action="{{ route('trainer.enrollments.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                <div class="sm:col-span-6">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search learner name, course title, or certificate #..."
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>

                <div class="sm:col-span-4">
                    <select name="status" onchange="this.form.submit()"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none">
                        <option value="">All Enrollment Statuses</option>
                        <option value="enrolled" {{ request('status') === 'enrolled' ? 'selected' : '' }}>Enrolled (New)</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="pending_review" {{ request('status') === 'pending_review' ? 'selected' : '' }}>Pending Review</option>
                        <option value="needs_retake" {{ request('status') === 'needs_retake' ? 'selected' : '' }}>Needs Retake</option>
                        <option value="no_show" {{ request('status') === 'no_show' ? 'selected' : '' }}>No Show</option>
                        <option value="multiple_absences" {{ request('status') === 'multiple_absences' ? 'selected' : '' }}>Multiple Absences</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <a href="{{ route('trainer.enrollments.index') }}" class="w-full py-3 rounded-2xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-bold flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Enrollments Table -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h2 class="text-lg font-black text-slate-900">Enrolled Learners Registry</h2>
                <span class="text-xs font-bold text-slate-500">Figure 12: Manage &rarr; Update Status &rarr; Evaluate &rarr; Issue Certificate</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-400 uppercase tracking-wider font-bold">
                            <th class="pb-3 px-3">Learner Name</th>
                            <th class="pb-3 px-3">Course / Track</th>
                            <th class="pb-3 px-3">Score / Grade</th>
                            <th class="pb-3 px-3">Enrollment Status</th>
                            <th class="pb-3 px-3">Certificate</th>
                            <th class="pb-3 px-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($enrollments as $enr)
                        @php
                        $assessmentData = is_array($enr->answers) ? $enr->answers : (json_decode($enr->answers ?? '[]', true) ?: []);
                        $reviewStatus = $assessmentData['review_status'] ?? null;

                        // Determine the badge color and text
                        $badgeColor = 'bg-blue-100 text-blue-800 border border-blue-300';
                        $badgeText = ucfirst(str_replace('_', ' ', $enr->status));

                        if ($reviewStatus === 'pending') {
                        $badgeColor = 'bg-amber-100 text-amber-800 border border-amber-300';
                        $badgeText = 'Pending Review';
                        } elseif ($reviewStatus === 'needs_retake' || $enr->status === 'failed') {
                        $badgeColor = 'bg-rose-100 text-rose-800 border border-rose-300';
                        $badgeText = 'Needs Retake';
                        } elseif ($enr->status === 'completed') {
                        $badgeColor = 'bg-emerald-100 text-emerald-800 border border-emerald-300';
                        $badgeText = 'Completed';
                        } elseif (in_array($enr->status, ['no_show', 'multiple_absences'])) {
                        $badgeColor = 'bg-slate-200 text-slate-800 border border-slate-300';
                        $badgeText = ucfirst(str_replace('_', ' ', $enr->status));
                        }
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-3 font-bold text-slate-900">
                                <span class="text-sm block">{{ $enr->first_name }} {{ $enr->last_name }}</span>
                                <span class="text-[11px] text-slate-400">{{ $enr->jobseeker_email }}</span>
                            </td>
                            <td class="py-4 px-3">
                                <span class="font-bold text-slate-900 block">{{ $enr->course_title }}</span>
                                <span class="text-[11px] text-emerald-700 font-semibold">{{ ucfirst($enr->course_type) }}</span>
                            </td>
                            <td class="py-4 px-3 font-bold">
                                @if($reviewStatus === 'pending' && !empty($assessmentData['objective_score']['question_count']))
                                <span class="text-indigo-700">{{ $assessmentData['objective_score']['correct_count'] }}/{{ $assessmentData['objective_score']['question_count'] }} correct</span>
                                @elseif(!is_null($enr->score))
                                <span class="{{ $enr->score >= 80 ? 'text-emerald-700' : 'text-rose-600' }}">
                                    {{ $enr->score }}%
                                </span>
                                @else
                                <span class="text-slate-400 text-[11px]">Ungraded</span>
                                @endif
                            </td>
                            <td class="py-4 px-3">
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-bold {{ $badgeColor }}">
                                    {{ $badgeText }}
                                </span>
                            </td>
                            <td class="py-4 px-3">
                                @if($enr->certificate_issued)
                                <div class="space-y-0.5">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-600 text-white px-2.5 py-0.5 text-[10px] font-black">
                                        <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                            <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                                        </svg>
                                        <span>{{ $enr->certificate_no }}</span>
                                    </span>
                                    <a href="{{ route('trainer.certificates.preview', $enr->enrollment_id) }}" target="_blank" class="block text-[10px] font-bold text-emerald-700 hover:underline">
                                        Print Certificate &rarr;
                                    </a>
                                </div>
                                @else
                                <span class="text-slate-400 text-[11px]">{{ $reviewStatus === 'pending' ? 'Pending Review' : 'Not Issued' }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-3 text-right">

                                <!-- Hide buttons if completed, no_show, or multiple_absences -->
                                @if(!in_array($enr->status, ['completed', 'no_show', 'multiple_absences']))
                                <div class="ml-auto flex w-fit flex-col items-stretch gap-1.5 rounded-xl border border-slate-200 bg-white p-2">

                                    <!-- 1. Update Status -->
                                    <button type="button"
                                        @click="openStatus({{ $enr->enrollment_id }}, '{{ addslashes($enr->first_name) }} {{ addslashes($enr->last_name) }}', '{{ $enr->status }}', '{{ addslashes($enr->lab_remarks ?? '') }}')"
                                        class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition-colors"
                                        title="Update Enrollment Status">
                                        Status
                                    </button>

                                    <!-- 2. Assessment Button -->
                                    @if($reviewStatus === 'pending')
                                    <a href="{{ route('trainer.enrollments.evaluate', $enr->enrollment_id) }}"
                                        class="px-2.5 py-1.5 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-[11px] font-bold transition-colors text-center"
                                        title="Review submitted answers">
                                        Review Answers
                                    </a>
                                    @elseif($reviewStatus === 'needs_retake' || $enr->status === 'failed')
                                    <button type="button" @click="openAssess({{ $enr->enrollment_id }}, '{{ addslashes($enr->first_name) }} {{ addslashes($enr->last_name) }}', '{{ addslashes($enr->course_title) }}', '{{ $enr->course_type }}')"
                                        class="px-2.5 py-1.5 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-[11px] font-bold transition-colors text-center"
                                        title="Re-assess learner">
                                        Re-assess
                                    </button>
                                    @elseif(!$enr->certificate_issued)
                                    <button type="button" @click="openAssess({{ $enr->enrollment_id }}, '{{ addslashes($enr->first_name) }} {{ addslashes($enr->last_name) }}', '{{ addslashes($enr->course_title) }}', '{{ $enr->course_type }}')"
                                        class="px-2.5 py-1.5 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-[11px] font-bold transition-colors text-center"
                                        title="Conduct Assessment">
                                        Assess
                                    </button>
                                    @endif
                                </div>
                                @else
                                <span class="text-[11px] font-bold text-slate-400 italic px-3">No Actions</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 italic">
                                No enrollments found matching this filter.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-4 border-t border-slate-100">
                {{ $enrollments->links() }}
            </div>
        </div>

    </div>

    <!-- ========================================== -->
    <!-- UPDATE STATUS MODAL                        -->
    <!-- ========================================== -->
    <div x-show="statusModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="statusModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">

            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Update Enrollment Status</span>
                    <h3 class="text-xl font-black text-slate-900 mt-0.5">Learner: <span x-text="selectedName"></span></h3>
                </div>
                <button @click="statusModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <form :action="'/trainer/enrollments/' + selectedId + '/status'" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Enrollment Status *</label>
                    <select name="status" x-model="selectedStatus" required
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none">
                        <option value="enrolled">Enrolled (New)</option>
                        <option value="in_progress">In Progress (Active in lectures / lab)</option>
                        <option value="no_show">No Show (Never attended)</option>
                        <option value="multiple_absences">Multiple Absences (Dropped)</option>
                        <option value="completed">Completed (Successfully finished)</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Lab & Attendance Remarks</label>
                    <textarea name="lab_remarks" x-model="selectedRemarks" rows="3" placeholder="Notes on laboratory participation, practical exercises, or progress..."
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="statusModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-lg shadow-emerald-600/30">
                        Save Status &rarr;
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- ========================================== -->
    <!-- ASSESSMENT MODAL                           -->
    <!-- ========================================== -->
    <div x-show="assessModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">

            <div x-show="assessModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="assessModal = false"></div>

            <div x-show="assessModal" x-transition class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">

                <div class="bg-slate-900 px-6 py-5 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-white">Conduct Assessment</h3>
                        <p class="text-xs text-slate-400">Evaluate practical competencies for <span x-text="assessType === 'laboratory_onsite' ? 'Lab/Onsite' : 'Online'" class="font-bold text-emerald-400"></span>.</p>
                    </div>
                    <button @click="assessModal = false" type="button" class="text-slate-400 hover:text-white text-xl font-bold">&times;</button>
                </div>

                <form :action="'/trainer/enrollments/' + assessId + '/assessment'" method="POST"> @csrf
                    <div class="p-6 space-y-5">

                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Learner</p>
                            <p class="text-sm font-black text-slate-900" x-text="assessName"></p>
                            <p class="text-[11px] font-semibold text-emerald-700 mt-0.5" x-text="assessCourse"></p>
                        </div>

                        <!-- Pass/Fail Selection -->
                        <div class="space-y-2">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700">Final Outcome *</label>
                            <div class="grid grid-cols-2 gap-3">
                                <!-- Pass -->
                                <label class="relative flex cursor-pointer rounded-xl border p-3 shadow-sm transition-all text-center"
                                    :class="selectedOutcome === '100' ? 'border-emerald-600 bg-emerald-50 ring-2 ring-emerald-500' : 'border-slate-200 bg-white hover:border-emerald-300'">
                                    <input type="radio" name="score" value="100" x-model="selectedOutcome" class="sr-only" required>
                                    <div class="flex flex-col items-center gap-1.5 w-full">
                                        <div class="h-8 w-8 rounded-full flex items-center justify-center transition-colors"
                                            :class="selectedOutcome === '100' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-400'">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <span class="block text-xs font-black transition-colors" :class="selectedOutcome === '100' ? 'text-emerald-800' : 'text-slate-900'">Passed</span>
                                    </div>
                                </label>
                                <!-- Fail -->
                                <label class="relative flex cursor-pointer rounded-xl border p-3 shadow-sm transition-all text-center"
                                    :class="selectedOutcome === '0' ? 'border-rose-600 bg-rose-50 ring-2 ring-rose-500' : 'border-slate-200 bg-white hover:border-rose-300'">
                                    <input type="radio" name="score" value="0" x-model="selectedOutcome" class="sr-only" required>
                                    <div class="flex flex-col items-center gap-1.5 w-full">
                                        <div class="h-8 w-8 rounded-full flex items-center justify-center transition-colors"
                                            :class="selectedOutcome === '0' ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-400'">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </div>
                                        <span class="block text-xs font-black transition-colors" :class="selectedOutcome === '0' ? 'text-rose-800' : 'text-slate-900'">Retake</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-700">Lab Remarks</label>
                            <input type="text" name="lab_remarks" x-model="labRemarks" placeholder="e.g. Attended all sessions..."
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none">
                        </div>

                        <!-- Feedback -->
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-700">Trainer Feedback</label>
                            <textarea name="trainer_feedback" x-model="trainerFeedback" rows="2" placeholder="Detailed feedback..."
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none"></textarea>
                        </div>

                    </div>

                    <!-- Actions -->
                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button @click="assessModal = false" type="button" class="px-5 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-md shadow-emerald-600/20 transition-all">
                            Submit Assessment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection