@extends('layouts.jpo')

@section('title', 'Evaluate Placement Reports - JPO Portal')

@section('content')
<div x-data="{
    forwardModal: false,
    detailModal: false,
    selectedRepId: null,
    selectedCompany: '',
    selectedMonth: '',
    selectedReport: null,
    openForward(id, company, month) {
        this.selectedRepId = id;
        this.selectedCompany = company;
        this.selectedMonth = month;
        this.forwardModal = true;
    },
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
                    Monthly Placement Compliance
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Evaluate Placement Reports</h1>
                <p class="text-sm text-slate-300">Inspect monthly employer hiring submissions, audit hired candidate records against DMDP referrals, and forward verified reports to Admin for final authorization.</p>
            </div>

            <div class="shrink-0 bg-white/10 backdrop-blur rounded-2xl p-5 border border-white/10 text-center min-w-[150px]">
                <span class="text-xs font-bold text-emerald-300 uppercase tracking-wider">Reports Log</span>
                <p class="text-3xl font-black text-emerald-400 mt-0.5">{{ $reports->total() }}</p>
                <span class="text-[10px] text-slate-300">Employer Submissions</span>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                <span class="text-xs font-bold uppercase text-slate-400 mr-1">Filter:</span>
                <a href="{{ route('jpo.evaluations.placement-reports') }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors {{ !request('status') ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    All Reports
                </a>
                <a href="{{ route('jpo.evaluations.placement-reports', ['status' => 'submitted_to_jpo']) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors {{ request('status') === 'submitted_to_jpo' ? 'bg-amber-600 text-white shadow-sm' : 'bg-amber-50 text-amber-800 hover:bg-amber-100' }}">
                    ⏳ Awaiting JPO
                </a>
                <a href="{{ route('jpo.evaluations.placement-reports', ['status' => 'jpo_evaluated']) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors {{ request('status') === 'jpo_evaluated' ? 'bg-blue-600 text-white shadow-sm' : 'bg-blue-50 text-blue-800 hover:bg-blue-100' }}">
                    📋 Forwarded to Admin
                </a>
                <a href="{{ route('jpo.evaluations.placement-reports', ['status' => 'approved']) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors {{ request('status') === 'approved' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100' }}">
                    ✓ Approved
                </a>
            </div>

            <form action="{{ route('jpo.evaluations.placement-reports') }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="relative w-full md:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search company..."
                           class="w-full rounded-xl border border-slate-200 pl-8 pr-3 py-1.5 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none">
                    <span class="absolute left-2.5 top-2 text-slate-400 text-xs">🔍</span>
                </div>
                <button type="submit" class="px-4 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-sm">
                    Search
                </button>
            </form>
        </div>

        <!-- Reports Table -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h2 class="text-lg font-black text-slate-900">Submitted Employer Placement Reports</h2>
                <span class="text-xs font-bold text-slate-500">Step 2: JPO Evaluation &rarr; Send to Admin</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-400 uppercase tracking-wider font-bold">
                            <th class="pb-3 px-3">Company</th>
                            <th class="pb-3 px-3">Report Month</th>
                            <th class="pb-3 px-3">Hired Count</th>
                            <th class="pb-3 px-3">Status</th>
                            <th class="pb-3 px-3">JPO Remarks</th>
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
                                    'company_name' => $rep->company_name ?? 'Company',
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
                                    'print_url' => route('jpo.evaluations.placement-reports.print', $rep->report_id),
                                ]);
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-3 font-bold text-slate-900">
                                    <span class="text-sm block">{{ $rep->company_name }}</span>
                                    <span class="text-[11px] text-slate-400">Employer ID #{{ $rep->employer_id }} &bull; Report #{{ $rep->report_id }}</span>
                                </td>
                                <td class="py-4 px-3 font-bold text-slate-900">
                                    {{ date('F Y', strtotime($rep->report_month)) }}
                                </td>
                                <td class="py-4 px-3 font-bold text-emerald-700">
                                    {{ $rData['total_hired'] ?? 0 }} candidates
                                </td>
                                <td class="py-4 px-3">
                                    @if($rep->status === 'approved')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 border border-emerald-300 px-2.5 py-0.5 text-xs font-bold text-emerald-800">
                                            ✓ Approved by Admin
                                        </span>
                                    @elseif($rep->status === 'jpo_evaluated')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 border border-blue-300 px-2.5 py-0.5 text-xs font-bold text-blue-800">
                                            📋 Forwarded to Admin
                                        </span>
                                    @elseif($rep->status === 'submitted_to_jpo')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 border border-amber-300 px-2.5 py-0.5 text-xs font-bold text-amber-800">
                                            ⏳ Awaiting JPO Evaluation
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                                            {{ ucfirst($rep->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-3 text-slate-500 text-[11px] max-w-xs truncate">
                                    {{ $rep->jpo_remarks ?: 'Pending JPO remarks' }}
                                </td>
                                <td class="py-4 px-3 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <button type="button" 
                                                @click='viewReport({!! $repJson !!})'
                                                class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-800 font-bold text-xs border border-slate-200 transition-colors flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            View Report
                                        </button>

                                        <a href="{{ route('jpo.evaluations.placement-reports.print', $rep->report_id) }}" 
                                           target="_blank"
                                           class="p-1.5 rounded-xl text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-colors" 
                                           title="Print Official Report">
                                            🖨️
                                        </a>

                                        @if($rep->status === 'submitted_to_jpo')
                                            <button type="button" 
                                                    @click="openForward({{ $rep->report_id }}, '{{ $rep->company_name }}', '{{ date('F Y', strtotime($rep->report_month)) }}')"
                                                    class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold transition-colors">
                                                Evaluate &rarr;
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400 italic">
                                    No employer placement reports submitted for evaluation.
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

    <!-- Forward to Admin Modal -->
    <div x-show="forwardModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="forwardModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Placement Report Verification</span>
                    <h3 class="text-xl font-black text-slate-900 mt-0.5">Evaluate Report: <span x-text="selectedCompany"></span></h3>
                </div>
                <button @click="forwardModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <form :action="'/jpo/evaluations/placement-reports/' + selectedRepId + '/forward'" method="POST" class="space-y-4">
                @csrf

                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs space-y-1">
                    <p class="font-bold text-emerald-950">Company: <span x-text="selectedCompany" class="text-slate-900 font-extrabold"></span></p>
                    <p class="text-emerald-800">Reporting Period: <span x-text="selectedMonth" class="font-semibold"></span></p>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">JPO Evaluation Remarks & Audit Notes *</label>
                    <textarea name="remarks" rows="3" required placeholder="State audit observations, verification of hired list, and endorsement for Admin..."
                              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="forwardModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-lg shadow-emerald-600/30">
                        Verify & Send to Admin &rarr;
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Interactive Report Details Modal -->
    <div x-show="detailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="detailModal = false" class="bg-white rounded-3xl max-w-3xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <template x-if="selectedReport">
                <div class="space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">JPO Placement Audit Inspector</span>
                            <h3 class="text-xl font-black text-slate-900 mt-0.5">
                                <span x-text="selectedReport.company_name"></span> &bull; <span x-text="selectedReport.report_month"></span>
                            </h3>
                            <p class="text-xs text-slate-500">Report Ref #<span x-text="selectedReport.report_id"></span> &bull; Employer ID #<span x-text="selectedReport.employer_id"></span></p>
                        </div>
                        <button @click="detailModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
                    </div>

                    <!-- Summary Banner -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs">
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px]">Total Placements</span>
                            <p class="text-lg font-black text-emerald-800"><span x-text="selectedReport.total_hired"></span> Candidates</p>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px]">Audit Status</span>
                            <p class="mt-0.5">
                                <span class="font-bold text-slate-900 uppercase" x-text="selectedReport.status"></span>
                            </p>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px]">Filing Timestamp</span>
                            <p class="font-semibold text-slate-700 mt-0.5" x-text="selectedReport.submitted_at ? new Date(selectedReport.submitted_at).toLocaleDateString() : 'N/A'"></p>
                        </div>
                    </div>

                    <!-- Employer Notes -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-xs" x-show="selectedReport.notes">
                        <span class="font-bold text-slate-700 block">Employer Comments / Notes:</span>
                        <p class="text-slate-600 italic mt-0.5" x-text="selectedReport.notes"></p>
                    </div>

                    <!-- Hired Candidate List -->
                    <div class="space-y-2">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-900">
                            Candidate Placements for Audit (<span x-text="selectedReport.total_hired"></span>)
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
                                                      x-text="item.referred_by_jpo === 'Yes' || item.referred_by_jpo === true ? 'DMDP Referred' : 'Direct'">
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

                    <!-- Audit Trail -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="p-3.5 rounded-2xl border border-blue-200 bg-blue-50/50 space-y-1">
                            <span class="font-bold text-blue-900 block uppercase text-[10px]">Your JPO Audit Remarks</span>
                            <p class="text-slate-700 italic" x-text="selectedReport.jpo_remarks ? '“' + selectedReport.jpo_remarks + '”' : 'Not yet evaluated.'"></p>
                            <p class="text-[10px] text-slate-500 pt-1" x-show="selectedReport.jpo_evaluated_at">Evaluated on: <span x-text="new Date(selectedReport.jpo_evaluated_at).toLocaleDateString()"></span></p>
                        </div>

                        <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50 space-y-1">
                            <span class="font-bold text-slate-800 block uppercase text-[10px]">Admin Final Authorization</span>
                            <p class="text-slate-700 italic" x-text="selectedReport.admin_remarks ? '“' + selectedReport.admin_remarks + '”' : 'Pending City Hall Administrative authorization.'"></p>
                            <p class="text-[10px] text-slate-500 pt-1" x-show="selectedReport.approved_at">Authorized on: <span x-text="new Date(selectedReport.approved_at).toLocaleDateString()"></span></p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <a :href="selectedReport.print_url" target="_blank"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-xs font-bold text-slate-700 transition-colors">
                            🖨️ View Official Printable Report &rarr;
                        </a>

                        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                            <template x-if="selectedReport.status === 'submitted_to_jpo'">
                                <button type="button" 
                                        @click="detailModal = false; openForward(selectedReport.report_id, selectedReport.company_name, selectedReport.report_month)"
                                        class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white font-bold text-xs transition-colors">
                                    Evaluate & Forward &rarr;
                                </button>
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
