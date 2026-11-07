@extends('layouts.trainer')

@section('title', $course->title . ' - Enrolled Learners')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto w-full max-w-[1680px] space-y-8">
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 text-white shadow-xl sm:p-10">
            <div class="flex flex-col justify-between gap-6 sm:flex-row sm:items-center">
                <div class="space-y-2">
                    <a href="{{ route('trainer.courses.show', $course->training_id) }}" class="text-xs font-bold text-emerald-300 hover:text-white">
                        &larr; Back to Course
                    </a>
                    <h1 class="text-3xl font-black tracking-tight sm:text-4xl">{{ $course->title }}</h1>
                    <p class="text-sm text-slate-300">Review learner progress, submitted assessments, and certificate status.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-5 text-center sm:min-w-[150px]">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-300">Enrolled Learners</span>
                    <p class="mt-0.5 text-3xl font-black text-emerald-400">{{ $enrollments->total() }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('trainer.courses.enrollments', $course->training_id) }}" class="grid grid-cols-1 gap-4 sm:grid-cols-12">
                <label class="text-xs font-bold text-slate-600 sm:col-span-5">
                    Enrollment month
                    <input type="month" name="month" value="{{ request('month') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs text-slate-800 focus:border-emerald-500 focus:outline-none">
                </label>
                <label class="text-xs font-bold text-slate-600 sm:col-span-5">
                    Progress status
                    <select name="status" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs text-slate-800 focus:border-emerald-500 focus:outline-none">
                        <option value="">All statuses</option>
                        <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="pending_review" {{ request('status') === 'pending_review' ? 'selected' : '' }}>Pending review</option>
                        <option value="needs_retake" {{ request('status') === 'needs_retake' ? 'selected' : '' }}>Needs retake</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </label>
                <div class="flex items-end gap-2 sm:col-span-2">
                    <button type="submit" class="flex-1 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white hover:bg-slate-800">Filter</button>
                    <a href="{{ route('trainer.courses.enrollments', $course->training_id) }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50">Reset</a>
                </div>
            </form>
            @if(request('month'))
                <p class="mt-3 text-xs font-semibold text-slate-500">{{ \Carbon\Carbon::createFromFormat('!Y-m', request('month'))->format('F Y') }}</p>
            @endif
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
            <div class="mb-5 border-b border-slate-100 pb-4">
                <h2 class="text-lg font-black text-slate-900">Enrolled Learners</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 font-bold uppercase tracking-wider text-slate-400">
                            <th class="px-3 pb-3">Learner</th>
                            <th class="px-3 pb-3">Started</th>
                            <th class="px-3 pb-3">Score</th>
                            <th class="px-3 pb-3">Status</th>
                            <th class="px-3 pb-3">Certificate</th>
                            <th class="px-3 pb-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($enrollments as $enrollment)
                            @php
                                $answers = is_array($enrollment->answers) ? $enrollment->answers : (json_decode($enrollment->answers ?? '[]', true) ?: []);
                                $reviewStatus = $answers['review_status'] ?? null;
                                $objectiveScore = $answers['objective_score'] ?? [];
                            @endphp
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-3 py-4">
                                    <span class="block text-sm font-bold text-slate-900">{{ $enrollment->first_name }} {{ $enrollment->last_name }}</span>
                                    <span class="text-[11px] text-slate-400">{{ $enrollment->jobseeker_email }}</span>
                                </td>
                                <td class="px-3 py-4">{{ $enrollment->start_date ? \Carbon\Carbon::parse($enrollment->start_date)->format('M j, Y') : '—' }}</td>
                                <td class="px-3 py-4 font-bold">
                                    @if($reviewStatus === 'pending' && !empty($objectiveScore['question_count']))
                                        <span class="text-indigo-700">{{ $objectiveScore['correct_count'] ?? 0 }}/{{ $objectiveScore['question_count'] }} correct</span>
                                    @elseif(!is_null($enrollment->score))
                                        {{ $enrollment->score }}%
                                    @else
                                        <span class="text-slate-400">Ungraded</span>
                                    @endif
                                </td>
                                <td class="px-3 py-4">
                                    @php
                                        $statusLabel = $reviewStatus === 'pending' ? 'Pending Review' : ($reviewStatus === 'needs_retake' ? 'Needs Retake' : ($reviewStatus === 'passed' ? 'Passed' : ucfirst(str_replace('_', ' ', $enrollment->status))));
                                    @endphp
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-bold {{ $reviewStatus === 'pending' ? 'border border-amber-200 bg-amber-50 text-amber-800' : ($reviewStatus === 'needs_retake' ? 'border border-rose-200 bg-rose-50 text-rose-800' : ($enrollment->status === 'completed' ? 'border border-emerald-200 bg-emerald-50 text-emerald-800' : 'bg-slate-100 text-slate-700')) }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-3 py-4">
                                    @if($enrollment->certificate_issued)
                                        <a href="{{ route('trainer.certificates.preview', $enrollment->enrollment_id) }}" target="_blank" class="font-bold text-emerald-700 hover:underline">{{ $enrollment->certificate_no ?: 'Issued' }}</a>
                                    @elseif($reviewStatus === 'pending')
                                        <span class="text-amber-700">Pending review</span>
                                    @else
                                        <span class="text-slate-400">Not issued</span>
                                    @endif
                                </td>
                                <td class="px-3 py-4 text-right">
                                    @if($reviewStatus === 'pending')
                                        <a href="{{ route('trainer.enrollments.evaluate', $enrollment->enrollment_id) }}" class="inline-flex rounded-xl bg-slate-900 px-3 py-2 font-bold text-white hover:bg-emerald-600">Review Answers</a>
                                    @elseif($enrollment->status === 'completed' && $enrollment->passed && !$enrollment->certificate_issued)
                                        <form action="{{ route('trainer.enrollments.certificate', $enrollment->enrollment_id) }}" method="POST" onsubmit="return confirm('Generate and issue this learner’s certificate?');">
                                            @csrf
                                            <button type="submit" class="rounded-xl bg-emerald-600 px-3 py-2 font-bold text-white hover:bg-emerald-500">Issue Certificate</button>
                                        </form>
                                    @elseif(!in_array($reviewStatus, ['passed', 'needs_retake'], true))
                                        <a href="{{ route('trainer.enrollments.assessment', $enrollment->enrollment_id) }}" class="inline-flex rounded-xl bg-slate-900 px-3 py-2 font-bold text-white hover:bg-emerald-600">Assess</a>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-12 text-center text-sm text-slate-400">No learners match this course and filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-5">{{ $enrollments->links() }}</div>
        </div>
    </div>
</div>
@endsection
