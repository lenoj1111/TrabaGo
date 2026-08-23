@extends('layouts.employer')

@section('title', 'Monthly Placement Reports - Employer Portal')

@section('content')
<div x-data="{ reportModal: false }" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    DMDP Placement Compliance
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Monthly Placement Reports</h1>
                <p class="text-sm text-slate-300">Generate and submit mandatory monthly placement reports on hired candidates to the Job Placement Officer (JPO) for evaluation and subsequent Admin approval.</p>
            </div>

            <button @click="reportModal = true" 
                    class="shrink-0 inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 px-6 py-3.5 text-xs font-black text-white shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                + Generate Placement Report
            </button>
        </div>

        <!-- Reports Table -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h2 class="text-lg font-black text-slate-900">Placement Submission History</h2>
                <span class="text-xs font-bold text-slate-500">Evaluated by JPO &rarr; Approved by Admin</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-400 uppercase tracking-wider font-bold">
                            <th class="pb-3 px-3">Report Month</th>
                            <th class="pb-3 px-3">Type</th>
                            <th class="pb-3 px-3">Hired Count</th>
                            <th class="pb-3 px-3">Workflow Status</th>
                            <th class="pb-3 px-3">JPO Remarks</th>
                            <th class="pb-3 px-3 text-right">Admin Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($reports as $rep)
                            @php
                                $rData = is_array($rep->report_data) ? $rep->report_data : json_decode($rep->report_data ?? '[]', true);
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-3 font-bold text-slate-900">
                                    {{ date('F Y', strtotime($rep->report_month)) }}
                                </td>
                                <td class="py-4 px-3">
                                    <span class="rounded-lg bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700 uppercase">
                                        {{ str_replace('_', ' ', $rep->report_type) }}
                                    </span>
                                </td>
                                <td class="py-4 px-3 font-bold text-emerald-700">
                                    {{ $rData['total_hired'] ?? 0 }} hired
                                </td>
                                <td class="py-4 px-3">
                                    @if($rep->status === 'approved')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 border border-emerald-300 px-2.5 py-0.5 text-xs font-bold text-emerald-800">
                                            ✓ Approved by Admin
                                        </span>
                                    @elseif($rep->status === 'jpo_evaluated')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 border border-blue-300 px-2.5 py-0.5 text-xs font-bold text-blue-800">
                                            📋 JPO Verified (With Admin)
                                        </span>
                                    @elseif($rep->status === 'submitted_to_jpo')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 border border-amber-300 px-2.5 py-0.5 text-xs font-bold text-amber-800">
                                            ⏳ Under JPO Evaluation
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-bold text-rose-800">
                                            {{ ucfirst($rep->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-3 text-slate-500 text-[11px]">
                                    {{ $rep->jpo_remarks ?: 'Pending evaluation' }}
                                </td>
                                <td class="py-4 px-3 text-right text-slate-500 text-[11px]">
                                    {{ $rep->admin_remarks ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400 italic">
                                    No monthly placement reports generated yet. Click "+ Generate Placement Report" to compile and submit.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-4 border-t border-slate-100">
                {{ $reports->links() }}
            </div>
        </div>

    </div>

    <!-- Generate Placement Report Modal -->
    <div x-show="reportModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="reportModal = false" class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Placement Compliance</span>
                    <h3 class="text-xl font-black text-slate-900 mt-0.5">Generate Monthly Placement Report</h3>
                </div>
                <button @click="reportModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <form action="{{ route('employer.placement-reports.generate') }}" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Report Month *</label>
                    <input type="month" name="report_month" value="{{ date('Y-m') }}" required
                           class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>

                <div class="rounded-2xl bg-emerald-50/70 border border-emerald-200 p-4 space-y-2 text-xs">
                    <span class="font-bold text-emerald-950">Hired Candidates Summary for this Report:</span>
                    <p class="text-emerald-800 font-semibold">{{ $hiredApplicants->count() }} candidates currently confirmed as Hired.</p>
                    <ul class="list-disc list-inside text-[11px] text-emerald-700 space-y-0.5">
                        @foreach($hiredApplicants->take(5) as $h)
                            <li>{{ $h->jobseeker->first_name ?? 'Candidate' }} {{ $h->jobseeker->last_name ?? '' }} ({{ $h->jobPosting->title ?? 'N/A' }})</li>
                        @endforeach
                    </ul>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Employer Comments / Notes</label>
                    <textarea name="notes" rows="2" placeholder="Any additional notes or comments regarding hiring and onboarding..."
                              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="reportModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-lg shadow-emerald-600/30">
                        Submit Report to JPO &rarr;
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection
