@extends('layouts.admin')

@section('title', 'Placement Reports Directory - Admin')

@section('content')
<div x-data="{
    detailModal: false,
    selectedReport: null,
    viewReport(report) {
        this.selectedReport = report;
        this.detailModal = true;
    }
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">

        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    DMDP City Employment Compliance
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Placement Reports Directory</h1>
                <p class="text-sm text-slate-300">
                    Comprehensive statutory repository of monthly employer placement reports, JPO audit verifications, and City Hall PESO/DMDP archive records.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('admin.approvals.index') }}" 
                   class="inline-flex items-center gap-2 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/20 px-5 py-3 text-xs font-bold text-white transition-all">
                    <span>📋 Manage Approvals</span>
                </a>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <a href="{{ route('admin.placement-reports.index') }}" 
               class="rounded-3xl border {{ $statusFilter === 'all' ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/50' : 'border-slate-200 bg-white' }} p-5 shadow-sm transition-all hover:border-emerald-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Filings</span>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total'] ?? 0 }}</p>
                <span class="text-[10px] text-slate-500">All submissions</span>
            </a>

            <a href="{{ route('admin.placement-reports.index', ['status' => 'submitted_to_jpo']) }}" 
               class="rounded-3xl border {{ $statusFilter === 'submitted_to_jpo' ? 'border-amber-500 ring-2 ring-amber-500/20 bg-amber-50/50' : 'border-slate-200 bg-white' }} p-5 shadow-sm transition-all hover:border-amber-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Under JPO Audit</span>
                <p class="text-2xl font-black text-amber-800 mt-1">{{ $stats['submitted_to_jpo'] ?? 0 }}</p>
                <span class="text-[10px] text-amber-700">Awaiting officer</span>
            </a>

            <a href="{{ route('admin.placement-reports.index', ['status' => 'jpo_evaluated']) }}" 
               class="rounded-3xl border {{ $statusFilter === 'jpo_evaluated' ? 'border-blue-500 ring-2 ring-blue-500/20 bg-blue-50/50' : 'border-slate-200 bg-white' }} p-5 shadow-sm transition-all hover:border-blue-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-700">Pending Admin</span>
                <p class="text-2xl font-black text-blue-800 mt-1">{{ $stats['pending_admin'] ?? 0 }}</p>
                <span class="text-[10px] text-blue-700">Ready for authorization</span>
            </a>

            <a href="{{ route('admin.placement-reports.index', ['status' => 'approved']) }}" 
               class="rounded-3xl border {{ $statusFilter === 'approved' ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/50' : 'border-slate-200 bg-white' }} p-5 shadow-sm transition-all hover:border-emerald-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Approved & Archived</span>
                <p class="text-2xl font-black text-emerald-800 mt-1">{{ $stats['approved'] ?? 0 }}</p>
                <span class="text-[10px] text-emerald-700">City records</span>
            </a>

            <a href="{{ route('admin.placement-reports.index', ['status' => 'rejected']) }}" 
               class="rounded-3xl border {{ $statusFilter === 'rejected' ? 'border-rose-500 ring-2 ring-rose-500/20 bg-rose-50/50' : 'border-slate-200 bg-white' }} p-5 shadow-sm transition-all hover:border-rose-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-700">Rejected</span>
                <p class="text-2xl font-black text-rose-800 mt-1">{{ $stats['rejected'] ?? 0 }}</p>
                <span class="text-[10px] text-rose-700">Discrepancies flagged</span>
            </a>
        </div>

        <!-- Filter & Search Controls -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
            <form method="GET" action="{{ route('admin.placement-reports.index') }}" class="flex flex-col md:flex-row items-center gap-4 justify-between">
                <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-2 md:pb-0">
                    @php
                        $filterOptions = [
                            'all' => 'All Reports',
                            'jpo_evaluated' => 'Pending Admin (' . ($stats['pending_admin'] ?? 0) . ')',
                            'submitted_to_jpo' => 'Under JPO Review',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ];
                    @endphp
                    @foreach($filterOptions as $key => $label)
                        <a href="{{ route('admin.placement-reports.index', array_merge(request()->query(), ['status' => $key, 'page' => 1])) }}"
                           class="px-4 py-2 rounded-2xl text-xs font-bold whitespace-nowrap transition-all {{ $statusFilter === $key ? 'bg-slate-950 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto">
                    <input type="hidden" name="status" value="{{ $statusFilter }}">
                    <div class="relative w-full md:w-72">
                        <input type="text" name="search" value="{{ $searchQuery }}" placeholder="Search company or report #..."
                               class="w-full rounded-2xl border border-slate-200 pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        <span class="absolute left-3 top-2.5 text-slate-400">🔍</span>
                    </div>
                    @if($searchQuery || $statusFilter !== 'all')
                        <a href="{{ route('admin.placement-reports.index') }}" class="text-xs text-slate-500 hover:text-slate-900 font-bold whitespace-nowrap">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Reports Data Table -->
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-bold text-[11px]">
                        <tr>
                            <th class="py-4 px-6">Company / Employer</th>
                            <th class="py-4 px-4">Period</th>
                            <th class="py-4 px-4 text-center">Hired Count</th>
                            <th class="py-4 px-4">Workflow Status</th>
                            <th class="py-4 px-4">JPO Remarks</th>
                            <th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($reports as $rep)
                            @php
                                $rData = is_array($rep->report_data) ? $rep->report_data : json_decode($rep->report_data ?? '[]', true);
                                $repJson = json_encode([
                                    'report_id' => $rep->report_id,
                                    'employer_id' => $rep->employer_id,
                                    'company_name' => $rep->company_name,
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
                                    'print_url' => route('admin.placement-reports.print', $rep->report_id),
                                    'approve_url' => route('admin.approvals.placement-reports.approve', $rep->report_id),
                                    'reject_url' => route('admin.approvals.placement-reports.reject', $rep->report_id),
                                ]);
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="font-black text-slate-900 text-sm">{{ $rep->company_name }}</div>
                                    <div class="text-[11px] text-slate-400">Report #{{ $rep->report_id }} &bull; Employer #{{ $rep->employer_id }}</div>
                                </td>
                                <td class="py-4 px-4 font-bold text-slate-900">
                                    {{ date('F Y', strtotime($rep->report_month)) }}
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-xl bg-emerald-50 text-emerald-800 font-black border border-emerald-200">
                                        {{ $rData['total_hired'] ?? count($rData['hired_list'] ?? []) }} hired
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    @if($rep->status === 'approved')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 border border-emerald-300 px-2.5 py-0.5 text-xs font-bold text-emerald-800">
                                            ✓ Approved & Archived
                                        </span>
                                    @elseif($rep->status === 'jpo_evaluated')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 border border-blue-300 px-2.5 py-0.5 text-xs font-bold text-blue-800">
                                            📋 JPO Evaluated (Action Needed)
                                        </span>
                                    @elseif($rep->status === 'submitted_to_jpo')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 border border-amber-300 px-2.5 py-0.5 text-xs font-bold text-amber-800">
                                            ⏳ Under JPO Audit
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-bold text-rose-800">
                                            {{ ucfirst($rep->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-slate-500 max-w-xs truncate text-[11px]">
                                    {{ $rep->jpo_remarks ?: 'Pending JPO audit' }}
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <button type="button" 
                                                @click='viewReport({!! $repJson !!})'
                                                class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-800 font-bold text-xs border border-slate-200 transition-colors flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            View Report
                                        </button>
                                        
                                        <a href="{{ route('admin.placement-reports.print', $rep->report_id) }}" 
                                           target="_blank"
                                           class="p-1.5 rounded-xl text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-colors" 
                                           title="Print Official Report">
                                            🖨️
                                        </a>

                                        @if($rep->status === 'jpo_evaluated' || $rep->status === 'submitted_to_jpo')
                                            <form action="{{ route('admin.approvals.placement-reports.approve', $rep->report_id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-colors shadow-sm">
                                                    ✓ Approve
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <div class="text-3xl mb-2">📊</div>
                                    <p class="font-bold text-slate-700">No placement reports found</p>
                                    <p class="text-xs mt-0.5">Adjust your filters or wait for employers to submit monthly hiring reports.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $reports->links() }}
            </div>
        </div>

    </div>

    <!-- Interactive Report Details Modal -->
    <div x-show="detailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="detailModal = false" class="bg-white rounded-3xl max-w-3xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <template x-if="selectedReport">
                <div class="space-y-6">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <span class="text-[11px] font-extrabold text-emerald-700 uppercase tracking-wider">Placement Compliance Details</span>
                            <h3 class="text-xl font-black text-slate-900 mt-0.5">
                                <span x-text="selectedReport.company_name"></span> &bull; <span x-text="selectedReport.report_month"></span>
                            </h3>
                            <p class="text-xs text-slate-500">Report Reference #<span x-text="selectedReport.report_id"></span> &bull; Employer ID #<span x-text="selectedReport.employer_id"></span></p>
                        </div>
                        <button @click="detailModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
                    </div>

                    <!-- Meta Summary Banner -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs">
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px]">Total Placements</span>
                            <p class="text-lg font-black text-emerald-800"><span x-text="selectedReport.total_hired"></span> Hired Candidates</p>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px]">Current Workflow Status</span>
                            <p class="mt-0.5">
                                <span class="font-bold text-slate-900 uppercase" x-text="selectedReport.status"></span>
                            </p>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px]">Submission Date</span>
                            <p class="font-semibold text-slate-700 mt-0.5" x-text="selectedReport.submitted_at ? new Date(selectedReport.submitted_at).toLocaleDateString() : 'N/A'"></p>
                        </div>
                    </div>

                    <!-- Employer Notes -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-xs" x-show="selectedReport.notes">
                        <span class="font-bold text-slate-700 block">Employer Comments / Notes:</span>
                        <p class="text-slate-600 italic mt-0.5" x-text="selectedReport.notes"></p>
                    </div>

                    <!-- Hired Candidates Roster -->
                    <div class="space-y-2">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-900">
                            Itemized Candidate Placements (<span x-text="selectedReport.total_hired"></span>)
                        </h4>
                        <div class="max-h-60 overflow-y-auto border border-slate-200 rounded-2xl">
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

                    <!-- Audit Trail Blocks -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="p-3.5 rounded-2xl border border-blue-200 bg-blue-50/50 space-y-1">
                            <span class="font-bold text-blue-900 block uppercase text-[10px]">JPO Verification Audit</span>
                            <p class="text-slate-700 italic" x-text="selectedReport.jpo_remarks ? '“' + selectedReport.jpo_remarks + '”' : 'Pending JPO evaluation remarks.'"></p>
                            <p class="text-[10px] text-slate-500 pt-1" x-show="selectedReport.jpo_evaluated_at">Evaluated on: <span x-text="new Date(selectedReport.jpo_evaluated_at).toLocaleDateString()"></span></p>
                        </div>

                        <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50 space-y-1">
                            <span class="font-bold text-slate-800 block uppercase text-[10px]">Admin Final Authorization</span>
                            <p class="text-slate-700 italic" x-text="selectedReport.admin_remarks ? '“' + selectedReport.admin_remarks + '”' : 'Pending Administrative decision.'"></p>
                            <p class="text-[10px] text-slate-500 pt-1" x-show="selectedReport.approved_at">Authorized on: <span x-text="new Date(selectedReport.approved_at).toLocaleDateString()"></span></p>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <a :href="selectedReport.print_url" target="_blank"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-xs font-bold text-slate-700 transition-colors">
                            🖨️ Open Official Printable Report &rarr;
                        </a>

                        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                            <template x-if="selectedReport.status === 'jpo_evaluated' || selectedReport.status === 'submitted_to_jpo'">
                                <div class="flex items-center gap-2">
                                    <form :action="selectedReport.approve_url" method="POST">
                                        @csrf
                                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md">
                                            ✓ Authorize & Archive
                                        </button>
                                    </form>
                                    <form :action="selectedReport.reject_url" method="POST">
                                        @csrf
                                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </template>

                            <button type="button" @click="detailModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                                Close
                            </button>
                        </div>
                    </div>

                </div>
            </template>

        </div>
    </div>

</div>
@endsection
