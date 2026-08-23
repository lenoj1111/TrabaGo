@extends('layouts.employer')

@section('title', 'Manage Job Postings - Employer Portal')

@section('content')
<div x-data="{ openModal: false }" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Header in Emerald Theme -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Job Postings Pipeline
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Job Postings Management</h1>
                <p class="text-sm text-slate-300">Create vacancies and send them to DMDP Administration for approval. Approved openings are automatically matched with jobseekers via AI.</p>
            </div>

            <button @click="openModal = true" 
                    class="shrink-0 inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 px-6 py-3.5 text-xs font-black text-white shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                + Create New Job Opening
            </button>
        </div>

        <!-- Job Postings List Table -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h2 class="text-lg font-black text-slate-900">Your Company Vacancies ({{ $jobs->total() }})</h2>
                <span class="text-xs font-bold text-slate-500">Sent to Admin for Authorization</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-400 uppercase tracking-wider font-bold">
                            <th class="pb-3 px-3">Position / Title</th>
                            <th class="pb-3 px-3">Vacancies</th>
                            <th class="pb-3 px-3">Disability Inclusive</th>
                            <th class="pb-3 px-3">Valid Until</th>
                            <th class="pb-3 px-3">Approval Status</th>
                            <th class="pb-3 px-3 text-right">Date Posted</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($jobs as $job)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-3">
                                    <span class="font-bold text-slate-900 block text-sm">{{ $job->title }}</span>
                                    <span class="text-[11px] text-slate-400 line-clamp-1 max-w-sm">{{ $job->description }}</span>
                                </td>
                                <td class="py-4 px-3 font-bold text-slate-900">
                                    {{ $job->vacancy_count }} open
                                </td>
                                <td class="py-4 px-3">
                                    @if($job->accepts_disability)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800">
                                            ♿ {{ $job->disability_type ?: 'PWD Inclusive' }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">Standard</span>
                                    @endif
                                </td>
                                <td class="py-4 px-3 text-slate-500">
                                    {{ $job->valid_until ? date('M d, Y', strtotime($job->valid_until)) : 'Continuous' }}
                                </td>
                                <td class="py-4 px-3">
                                    @if($job->status === 'approved')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 border border-emerald-300 px-3 py-0.5 text-xs font-bold text-emerald-800">
                                            ✓ Live & Approved
                                        </span>
                                    @elseif($job->status === 'pending')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 border border-amber-300 px-3 py-0.5 text-xs font-bold text-amber-800">
                                            ⏳ Sent to Admin (Pending)
                                        </span>
                                    @elseif($job->status === 'rejected')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 border border-rose-300 px-3 py-0.5 text-xs font-bold text-rose-800">
                                            ✕ Needs Revision
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-0.5 text-xs font-bold text-slate-600">
                                            {{ ucfirst($job->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-3 text-right text-slate-400">
                                    {{ $job->created_at ? date('M d, Y', strtotime($job->created_at)) : 'Today' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400 italic">
                                    No job openings posted yet. Click "+ Create New Job Opening" above to post your first vacancy.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-4 border-t border-slate-100">
                {{ $jobs->links() }}
            </div>
        </div>

    </div>

    <!-- Create Job Posting Modal -->
    <div x-show="openModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="openModal = false" class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">New Vacancy Submission</span>
                    <h3 class="text-xl font-black text-slate-900 mt-0.5">Post a Job Opening</h3>
                </div>
                <button @click="openModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <form action="{{ route('employer.job-postings.store') }}" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Job Title / Role *</label>
                    <input type="text" name="title" required placeholder="e.g. Senior PHP / Laravel Developer, Customer Service Associate"
                           class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Number of Vacancies *</label>
                        <input type="number" name="vacancy_count" min="1" value="1" required
                               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Valid Until</label>
                        <input type="date" name="valid_until" value="{{ date('Y-m-d', strtotime('+2 months')) }}"
                               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Job Description & Responsibilities *</label>
                    <textarea name="description" rows="3" required placeholder="Detail the core duties, daily tasks, and team environment..."
                              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400"></textarea>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Required Skills & Qualifications</label>
                    <textarea name="qualifications" rows="2" placeholder="e.g. Bachelor's or Vocational Graduate, PHP, SQL, Problem-Solving..."
                              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400"></textarea>
                </div>

                <!-- PWD Inclusivity Tag -->
                <div class="rounded-2xl bg-emerald-50/70 border border-emerald-200 p-4 space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="accepts_disability" value="1" class="h-4 w-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                        <span class="text-xs font-bold text-emerald-950">This job opening accepts and accommodates Persons with Disabilities (PWDs)</span>
                    </label>

                    <div class="space-y-1">
                        <input type="text" name="disability_type" placeholder="Accommodation details (e.g. Visual/Hearing impaired with ramp access, remote option)"
                               class="w-full rounded-xl border border-emerald-200 px-3 py-2 text-xs bg-white text-slate-900 focus:border-emerald-500 focus:outline-none">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="openModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-lg shadow-emerald-600/30">
                        Send to Admin for Approval &rarr;
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection
