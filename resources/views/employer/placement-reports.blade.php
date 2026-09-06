@extends('layouts.employer')

@section('title', 'Monthly Placement Reports - Employer Portal')

@section('content')
<div x-data="{
    reportModal: false,
    detailModal: false,
    selectedReport: null,
    viewReport(rep) {
        this.selectedReport = rep;
        this.detailModal = true;
    }
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
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
                            <th class="pb-3 px-3">Admin Remarks</th>
                            <th class="pb-3 px-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($reports as $rep)
                            @php
                                $rData = is_array($rep->report_data) ? $rep->report_data : json_decode($rep->report_data ?? '[]', true);
                                $repJson = json_encode([
                                    'report_id' => $rep->report_id,
                                    'employer_id' => $rep->employer_id,
                                    'company_name' => $employer->company_name ?? 'Company',
                                    'report_month' => date('F Y', strtotime($rep->report_month)),
                                    'status' => $rep->status,
                                    'total_hired' => $rData['total_hired'] ?? count($rData['hired_list'] ?? []),
                                    'hired_list' => $rData['hired_list'] ?? [],
                                    'notes' => $rData['notes'] ?? '',
                                    'submitted_at' => $rData['submitted_at'] ?? '',
                                    'jpo_evaluated' => $rep->jpo_evaluated,
                                    'jpo_evaluated_at' => $rep->jpo_evaluated_at,
                                    'jpo_remarks' => $rep->jpo_remarks,
                                    'admin_remarks' => $rep->admin_remarks,
                                    'approved_at' => $rep->approved_at,
                                    'print_url' => route('employer.placement-reports.print', $rep->report_id),
                                ]);
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-3 font-bold text-slate-900">
                                    {{ date('F Y', strtotime($rep->report_month)) }}
                                    <span class="block text-[10px] text-slate-400 font-normal">ID #{{ $rep->report_id }}</span>
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
                                <td class="py-4 px-3 text-slate-500 text-[11px] max-w-xs truncate">
                                    {{ $rep->jpo_remarks ?: 'Pending evaluation' }}
                                </td>
                                <td class="py-4 px-3 text-slate-500 text-[11px] max-w-xs truncate">
                                    {{ $rep->admin_remarks ?: '-' }}
                                </td>
                                <td class="py-4 px-3 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <button type="button" 
                                                @click='viewReport({!! $repJson !!})'
                                                class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-800 font-bold text-xs border border-slate-200 transition-colors flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            View
                                        </button>
                                        
                                        <a href="{{ route('employer.placement-reports.print', $rep->report_id) }}" 
                                           target="_blank"
                                           class="p-1.5 rounded-xl text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-colors" 
                                           title="Print Official Report">
                                            🖨️
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400 italic">
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

    <!-- Report Details Modal -->
    <div x-show="detailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="detailModal = false" class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <template x-if="selectedReport">
                <div class="space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Placement Report Details</span>
                            <h3 class="text-xl font-black text-slate-900 mt-0.5">
                                Month: <span x-text="selectedReport.report_month"></span>
                            </h3>
                            <p class="text-xs text-slate-500">Report Ref #<span x-text="selectedReport.report_id"></span></p>
                        </div>
                        <button @click="detailModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
                    </div>

                    <!-- Meta Summary Banner -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs">
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px]">Total Hired</span>
                            <p class="text-lg font-black text-emerald-800"><span x-text="selectedReport.total_hired"></span> Candidates</p>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px]">Current Status</span>
                            <p class="mt-0.5">
                                <span class="font-bold text-slate-900 uppercase" x-text="selectedReport.status"></span>
                            </p>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px]">Submitted At</span>
                            <p class="font-semibold text-slate-700 mt-0.5" x-text="selectedReport.submitted_at ? new Date(selectedReport.submitted_at).toLocaleDateString() : 'N/A'"></p>
                        </div>
                    </div>

                    <!-- Employer Notes -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-xs" x-show="selectedReport.notes">
                        <span class="font-bold text-slate-700 block">Submitted Notes:</span>
                        <p class="text-slate-600 italic mt-0.5" x-text="selectedReport.notes"></p>
                    </div>

                    <!-- Hired Candidates Roster -->
                    <div class="space-y-2">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-900">
                            Hired Candidate Roster (<span x-text="selectedReport.total_hired"></span>)
                        </h4>
                        <div class="max-h-56 overflow-y-auto border border-slate-200 rounded-2xl">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-100 border-b border-slate-200 text-slate-600 font-bold text-[10px] uppercase">
                                    <tr>
                                        <th class="py-2.5 px-3">#</th>
                                        <th class="py-2.5 px-3">Candidate Name</th>
                                        <th class="py-2.5 px-3">Position</th>
                                        <th class="py-2.5 px-3">Date Hired</th>
                                        <th class="py-2.5 px-3 text-right">Referral Type</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    <template x-for="(item, idx) in selectedReport.hired_list" :key="idx">
                                        <tr class="hover:bg-slate-50">
                                            <td class="py-2.5 px-3 text-slate-400" x-text="idx + 1"></td>
                                            <td class="py-2.5 px-3 font-bold text-slate-900" x-text="item.jobseeker_name"></td>
                                            <td class="py-2.5 px-3 text-slate-700" x-text="item.position"></td>
                                            <td class="py-2.5 px-3 text-slate-600" x-text="item.hired_date"></td>
                                            <td class="py-2.5 px-3 text-right">
                                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold"
                                                      :class="item.referred_by_jpo === 'Yes' || item.referred_by_jpo === true ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700'"
                                                      x-text="item.referred_by_jpo === 'Yes' || item.referred_by_jpo === true ? 'JPO Referred' : 'Direct'">
                                                </span>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="!selectedReport.hired_list || selectedReport.hired_list.length === 0">
                                        <tr>
                                            <td colspan="5" class="py-6 text-center text-slate-400 italic">No candidates itemized in this report.</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Workflow Trail (JPO Remarks & Admin Remarks) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="p-3.5 rounded-2xl border border-blue-200 bg-blue-50/50 space-y-1">
                            <span class="font-bold text-blue-900 block uppercase text-[10px]">JPO Verification Audit</span>
                            <p class="text-slate-700 italic" x-text="selectedReport.jpo_remarks ? '“' + selectedReport.jpo_remarks + '”' : 'Pending JPO evaluation remarks.'"></p>
                            <p class="text-[10px] text-slate-500 pt-1" x-show="selectedReport.jpo_evaluated_at">Audited on: <span x-text="new Date(selectedReport.jpo_evaluated_at).toLocaleDateString()"></span></p>
                        </div>

                        <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50 space-y-1">
                            <span class="font-bold text-slate-800 block uppercase text-[10px]">Admin Final Authorization</span>
                            <p class="text-slate-700 italic" x-text="selectedReport.admin_remarks ? '“' + selectedReport.admin_remarks + '”' : 'Pending Administrative review.'"></p>
                            <p class="text-[10px] text-slate-500 pt-1" x-show="selectedReport.approved_at">Approved on: <span x-text="new Date(selectedReport.approved_at).toLocaleDateString()"></span></p>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                        <a :href="selectedReport.print_url" target="_blank"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-xs font-bold text-slate-700 transition-colors">
                            🖨️ View Official Printable Form &rarr;
                        </a>
                        <button type="button" @click="detailModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                            Close
                        </button>
                    </div>

                </div>
            </template>

        </div>
    </div>

</div>
@endsection
