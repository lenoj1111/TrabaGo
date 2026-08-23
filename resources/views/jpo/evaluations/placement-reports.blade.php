@extends('layouts.jpo')

@section('title', 'Evaluate Placement Reports - JPO Portal')

@section('content')
<div x-data="{
    forwardModal: false,
    selectedRepId: null,
    selectedCompany: '',
    selectedMonth: '',
    openForward(id, company, month) {
        this.selectedRepId = id;
        this.selectedCompany = company;
        this.selectedMonth = month;
        this.forwardModal = true;
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
                            <th class="pb-3 px-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($reports as $rep)
                            @php
                                $rData = is_array($rep->report_data) ? $rep->report_data : json_decode($rep->report_data ?? '[]', true);
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-3 font-bold text-slate-900">
                                    <span class="text-sm block">{{ $rep->company_name }}</span>
                                    <span class="text-[11px] text-slate-400">Employer ID #{{ $rep->employer_id }}</span>
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
                                <td class="py-4 px-3 text-slate-500 text-[11px]">
                                    {{ $rep->jpo_remarks ?: 'Pending JPO remarks' }}
                                </td>
                                <td class="py-4 px-3 text-right">
                                    @if($rep->status === 'submitted_to_jpo')
                                        <button type="button" 
                                                @click="openForward({{ $rep->report_id }}, '{{ $rep->company_name }}', '{{ date('F Y', strtotime($rep->report_month)) }}')"
                                                class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold transition-colors">
                                            Evaluate & Send to Admin &rarr;
                                        </button>
                                    @else
                                        <span class="text-[11px] text-slate-400 font-semibold">Processed</span>
                                    @endif
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

</div>
@endsection
