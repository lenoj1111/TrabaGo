@extends('layouts.trainer')

@section('title', 'Manage Course Enrollments - Skills Trainer')

@section('content')
<div x-data="{
    statusModal: false,
    selectedId: null,
    selectedName: '',
    selectedStatus: 'enrolled',
    selectedRemarks: '',
    openStatus(id, name, status, remarks) {
        this.selectedId = id;
        this.selectedName = name;
        this.selectedStatus = status;
        this.selectedRemarks = remarks || '';
        this.statusModal = true;
    }
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Learner Progress & Certification
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Manage Course Enrollments</h1>
                <p class="text-sm text-slate-300">Oversee active students, update training milestone statuses, evaluate course answers, and issue completion certificates.</p>
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
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed / Needs Retake</option>
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
                                    @if(!is_null($enr->score))
                                        <span class="{{ $enr->score >= 80 ? 'text-emerald-700' : 'text-rose-600' }}">
                                            {{ $enr->score }}% {{ $enr->score >= 80 ? '(Passed)' : '(Failed)' }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">Ungraded</span>
                                    @endif
                                </td>
                                <td class="py-4 px-3">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-bold 
                                        {{ $enr->status === 'completed' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($enr->status === 'in_progress' ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-slate-100 text-slate-700') }}">
                                        {{ ucfirst(str_replace('_', ' ', $enr->status)) }}
                                    </span>
                                </td>
                                <td class="py-4 px-3">
                                    @if($enr->certificate_issued)
                                        <div class="space-y-0.5">
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-600 text-white px-2.5 py-0.5 text-[10px] font-black">
                                                🎓 {{ $enr->certificate_no }}
                                            </span>
                                            <a href="{{ route('trainer.certificates.preview', $enr->enrollment_id) }}" target="_blank" class="block text-[10px] font-bold text-emerald-700 hover:underline">
                                                Print Certificate &rarr;
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-[11px]">Not Issued</span>
                                    @endif
                                </td>
                                <td class="py-4 px-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- 1. Update Status Button -->
                                        <button type="button" 
                                                @click="openStatus({{ $enr->enrollment_id }}, '{{ $enr->first_name }} {{ $enr->last_name }}', '{{ $enr->status }}', '{{ addslashes($enr->lab_remarks ?? '') }}')"
                                                class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition-colors">
                                            Status
                                        </button>

                                        <!-- 2. Evaluate Course Answers -->
                                        <a href="{{ route('trainer.enrollments.evaluate', $enr->enrollment_id) }}" 
                                           class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-[11px] font-bold transition-colors">
                                            Evaluate
                                        </a>

                                        <!-- 3. Generate Certificate -->
                                        @if(!$enr->certificate_issued)
                                            <form action="{{ route('trainer.enrollments.certificate', $enr->enrollment_id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Issue official completion certificate and award verified skill?')"
                                                        class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-black shadow-md shadow-emerald-600/20">
                                                    🎓 Issue Cert
                                                </button>
                                            </form>
                                        @endif
                                    </div>
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

    <!-- Update Enrollment Status Modal -->
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
                        <option value="completed">Completed (Successfully finished)</option>
                        <option value="failed">Failed / Dropped</option>
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

</div>
@endsection
