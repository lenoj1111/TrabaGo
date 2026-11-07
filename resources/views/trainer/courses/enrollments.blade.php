@extends('layouts.trainer')

@section('title', 'Course Enrollments - Skills Trainer')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto w-full max-w-[1680px] space-y-8">
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 text-white shadow-xl sm:p-10">
            <div class="flex flex-col justify-between gap-6 sm:flex-row sm:items-center">
                <div class="space-y-2">
                    <a href="{{ route('trainer.courses') }}" class="text-xs font-bold text-emerald-300 hover:text-white">&larr; Courses Catalog</a>
                    <h1 class="text-3xl font-black tracking-tight sm:text-4xl">{{ $course->title }}</h1>
                    <p class="text-sm text-slate-300">Jobseekers enrolled in this course</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-5 text-center sm:min-w-[150px]">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-300">Matching Learners</span>
                    <p class="mt-0.5 text-3xl font-black text-emerald-400">{{ $enrollments->total() }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('trainer.courses.enrollments', $course->training_id) }}" class="grid grid-cols-1 gap-4 sm:grid-cols-12 sm:items-end">
                <div class="sm:col-span-5">
                    <label for="month" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Enrollment Month</label>
                    <input type="month" id="month" name="month" value="{{ request('month') }}" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs text-slate-700 focus:border-emerald-500 focus:outline-none">
                </div>
                <div class="sm:col-span-5">
                    <label for="status" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Progress</label>
                    <select id="status" name="status" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs text-slate-700 focus:border-emerald-500 focus:outline-none">
                        <option value="">All statuses</option>
                        <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="pending_review" {{ request('status') === 'pending_review' ? 'selected' : '' }}>Pending Review</option>
                        <option value="needs_retake" {{ request('status') === 'needs_retake' ? 'selected' : '' }}>Needs Retake</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="flex gap-2 sm:col-span-2">
                    @if(request()->hasAny(['month', 'status']))
                        <a href="{{ route('trainer.courses.enrollments', $course->training_id) }}" class="w-full rounded-xl bg-slate-100 px-4 py-2.5 text-center text-xs font-bold text-slate-600 transition-colors hover:bg-slate-200">Clear filters</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
            <div class="mb-5 flex items-center justify-between border-b border-slate-100 pb-4">
                <h2 class="text-lg font-black text-slate-900">Enrolled Jobseekers</h2>
                <span class="text-xs font-semibold text-slate-500">{{ $enrollments->total() }} total</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 font-bold uppercase tracking-wider text-slate-400">
                            <th class="px-3 pb-3">Jobseeker</th>
                            <th class="px-3 pb-3">Contact</th>
                            <th class="px-3 pb-3">Enrolled</th>
                            <th class="px-3 pb-3">Status</th>
                            <th class="px-3 pb-3">Score</th>
                            <th class="px-3 pb-3">Review</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($enrollments as $enrollment)
                            @php
                                $assessmentData = is_array($enrollment->answers) ? $enrollment->answers : (json_decode($enrollment->answers ?? '[]', true) ?: []);
                                $reviewStatus = $assessmentData['review_status'] ?? null;
                            @endphp
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-3 py-4 font-bold text-slate-900">{{ $enrollment->first_name }} {{ $enrollment->last_name }}</td>
                                <td class="px-3 py-4">
                                    <span class="block">{{ $enrollment->jobseeker_email ?: 'No email' }}</span>
                                    <span class="text-[11px] text-slate-400">{{ $enrollment->mobile_number ?: 'No phone number' }}</span>
                                </td>
                                <td class="px-3 py-4">{{ $enrollment->start_date ? \Carbon\Carbon::parse($enrollment->start_date)->format('M d, Y') : 'Not recorded' }}</td>
                                <td class="px-3 py-4">
                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-[11px] font-bold {{ $reviewStatus === 'pending' ? 'border-amber-300 bg-amber-100 text-amber-800' : ($enrollment->status === 'completed' ? 'border-emerald-300 bg-emerald-100 text-emerald-800' : ($reviewStatus === 'needs_retake' || $enrollment->status === 'failed' ? 'border-rose-300 bg-rose-100 text-rose-800' : 'border-slate-200 bg-slate-100 text-slate-700')) }}">
                                        {{ $reviewStatus === 'pending' ? 'Pending Review' : ($reviewStatus === 'needs_retake' || $enrollment->status === 'failed' ? 'Needs Retake' : ucfirst(str_replace('_', ' ', $enrollment->status))) }}
                                    </span>
                                </td>
                                <td class="px-3 py-4">{{ is_null($enrollment->score) ? '-' : $enrollment->score . '%' }}</td>
                                <td class="px-3 py-4">
                                    @if($reviewStatus === 'pending')
                                        <a href="{{ route('trainer.enrollments.evaluate', $enrollment->enrollment_id) }}" class="rounded-lg bg-indigo-600 px-3 py-2 text-[11px] font-bold text-white hover:bg-indigo-500">Review Answers</a>
                                    @elseif($reviewStatus === 'needs_retake')
                                        <span class="text-[11px] font-bold text-rose-700">Awaiting Retake</span>
                                    @elseif($reviewStatus === 'passed')
                                        <span class="text-[11px] font-bold text-emerald-700">{{ $enrollment->certificate_issued ? 'Certificate issued' : 'Passed' }}</span>
                                    @else
                                        <span class="text-[11px] text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-12 text-center text-slate-400">No enrolled jobseekers match these filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 border-t border-slate-100 pt-4">
                {{ $enrollments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
