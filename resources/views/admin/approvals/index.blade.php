@extends('layouts.admin')

@section('title', 'Manage Approvals Center')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8" x-data="{ tab: 'jobs' }">
    <div class="mx-auto max-w-7xl space-y-8">

        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    DMDP 3-Pillar Authorization Queue
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Manage Approvals Center</h1>
                <p class="text-sm text-slate-300">
                    Administrative authorization hub for employer job postings, PESD-endorsed accreditations, and verified placement records.
                </p>
            </div>
        </div>

        <!-- Pill Tab Switcher -->
        <div class="flex flex-wrap gap-2 sm:gap-3 p-1.5 rounded-2xl bg-white border border-slate-200 shadow-sm max-w-fit">
            <button @click="tab = 'jobs'" 
                    :class="tab === 'jobs' ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-300 shadow-sm font-black' : 'text-slate-600 hover:text-slate-900 font-bold hover:bg-slate-50'"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs transition-all">
                <span>💼 Job Postings</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black" 
                      :class="tab === 'jobs' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700'">
                    {{ $pendingJobs->count() }}
                </span>
            </button>

            <button @click="tab = 'accreditations'" 
                    :class="tab === 'accreditations' ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-300 shadow-sm font-black' : 'text-slate-600 hover:text-slate-900 font-bold hover:bg-slate-50'"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs transition-all">
                <span>🏛️ Accreditations</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black" 
                      :class="tab === 'accreditations' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700'">
                    {{ $pendingAccreditations->count() }}
                </span>
            </button>

            <button @click="tab = 'reports'" 
                    :class="tab === 'reports' ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-300 shadow-sm font-black' : 'text-slate-600 hover:text-slate-900 font-bold hover:bg-slate-50'"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs transition-all">
                <span>📊 Placement Reports</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black" 
                      :class="tab === 'reports' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700'">
                    {{ $pendingPlacementReports->count() }}
                </span>
            </button>
        </div>

        <!-- =================================================================== -->
        <!-- PILLAR 1: JOB POSTING APPROVALS -->
        <!-- =================================================================== -->
        <div x-show="tab === 'jobs'" class="space-y-4">
            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Employer Job Postings Pending Review</h3>
                        <p class="text-xs text-slate-500">Authorize listings for publication to all registered jobseekers</p>
                    </div>
                    <span class="text-xs font-bold text-slate-400">{{ $pendingJobs->count() }} Listings</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-100 text-[11px] uppercase font-bold text-slate-500 tracking-wider">
                            <tr>
                                <th class="py-4 px-6">Job Title</th>
                                <th class="py-4 px-6">Employer</th>
                                <th class="py-4 px-6 text-center">Vacancies</th>
                                <th class="py-4 px-6">Inclusivity (PWD)</th>
                                <th class="py-4 px-6">Submitted Date</th>
                                <th class="py-4 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @forelse($pendingJobs as $job)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="font-black text-slate-900 text-sm">{{ $job->title }}</div>
                                        <div class="text-[11px] text-slate-500">Listing #{{ $job->job_id }}</div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-slate-900">{{ $job->employer->company_name ?? 'DMDP Portal' }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $job->employer->email ?? 'N/A' }}</div>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 font-bold border border-slate-200">
                                            {{ $job->vacancy_count }} open
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($job->accepts_disability)
                                            <span class="inline-flex items-center gap-1 rounded-xl bg-emerald-50 text-emerald-800 px-2.5 py-1 font-bold border border-emerald-200">
                                                ♿ {{ $job->disability_type ?: 'PWD Inclusive' }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 font-medium">Standard</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-slate-500">
                                        {{ $job->created_at ? date('M d, Y', strtotime($job->created_at)) : 'Today' }}
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <form action="{{ route('admin.approvals.job-postings.approve', $job->job_id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-colors shadow-sm">
                                                    ✓ Approve
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.approvals.job-postings.reject', $job->job_id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-rose-700 hover:text-rose-800 font-bold text-xs border border-slate-200 transition-colors">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400">
                                        <div class="text-3xl mb-2">🎉</div>
                                        <p class="font-bold text-slate-700">All employer job postings reviewed!</p>
                                        <p class="text-xs mt-0.5">No pending job postings waiting in the approval queue.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- PILLAR 2: EMPLOYER ACCREDITATION FINAL APPROVAL -->
        <!-- =================================================================== -->
        <div x-show="tab === 'accreditations'" class="space-y-4" style="display: none;">
            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Employer Accreditations (Endorsed by PESD Supervisor)</h3>
                        <p class="text-xs text-slate-500">Official accreditation grant allowing employers to recruit DMDP candidates</p>
                    </div>
                    <span class="text-xs font-bold text-slate-400">{{ $pendingAccreditations->count() }} Applications</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-100 text-[11px] uppercase font-bold text-slate-500 tracking-wider">
                            <tr>
                                <th class="py-4 px-6">Company Name</th>
                                <th class="py-4 px-6">Documents</th>
                                <th class="py-4 px-6">Workflow Status</th>
                                <th class="py-4 px-6">Supervisor Remarks</th>
                                <th class="py-4 px-6 text-right">Admin Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @forelse($pendingAccreditations as $acc)
                                @php
                                    $docs = is_array($acc->documents) ? $acc->documents : json_decode($acc->documents ?? '[]', true);
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="font-black text-slate-900 text-sm">{{ $acc->company_name }}</div>
                                        <div class="text-[11px] text-slate-500">Employer ID #{{ $acc->employer_id }}</div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="space-y-1.5">
                                            <div class="flex flex-wrap gap-1">
                                                @if(is_array($docs) && count($docs) > 0)
                                                    @foreach($docs as $k => $d)
                                                        <button type="button" 
                                                                @click='openDocInspection("{{ addslashes($acc->company_name) }}", @json($docs))'
                                                                class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-emerald-100 hover:text-emerald-900 text-slate-700 text-[10px] font-bold border border-slate-200 transition-colors cursor-pointer"
                                                                title="Click to inspect this document">
                                                            <span>📄 {{ ucfirst(str_replace('_', ' ', $k)) }}</span>
                                                            <span class="text-[9px] text-emerald-600">↗</span>
                                                        </button>
                                                    @endforeach
                                                @else
                                                    <span class="text-slate-400">Attached Papers</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($acc->status === 'supervisor_approved')
                                            <span class="inline-flex items-center gap-1 rounded-xl bg-emerald-50 text-emerald-800 px-2.5 py-1 font-bold border border-emerald-200">
                                                🏛️ PESD Endorsed
                                            </span>
                                        @elseif($acc->status === 'jpo_approved')
                                            <span class="inline-flex items-center gap-1 rounded-xl bg-blue-50 text-blue-800 px-2.5 py-1 font-bold border border-blue-200">
                                                📋 JPO Recommended
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-xl bg-amber-50 text-amber-800 px-2.5 py-1 font-bold border border-amber-200">
                                                ⏳ In Review
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-slate-500 max-w-xs truncate">
                                        {{ $acc->supervisor_remarks ?: ($acc->jpo_remarks ?: 'Credentials verified.') }}
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            @if(is_array($docs) && count($docs) > 0)
                                                <button type="button" 
                                                        @click='openDocInspection("{{ addslashes($acc->company_name) }}", @json($docs))'
                                                        class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition-colors">
                                                    👁️ View Docs
                                                </button>
                                            @endif
                                            <form action="{{ route('admin.approvals.accreditations.approve', $acc->accreditation_id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-colors shadow-sm">
                                                    🛡️ Officially Accredit
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.approvals.accreditations.reject', $acc->accreditation_id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-rose-700 font-bold text-xs border border-slate-200 transition-colors">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400">
                                        <div class="text-3xl mb-2">🏛️</div>
                                        <p class="font-bold text-slate-700">No accreditations pending admin authorization</p>
                                        <p class="text-xs mt-0.5">All endorsed partner company files are up to date.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- PILLAR 3: PLACEMENT REPORT APPROVAL -->
        <!-- =================================================================== -->
        <div x-show="tab === 'reports'" class="space-y-4" style="display: none;">
            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Monthly Placement Reports (Evaluated by JPO)</h3>
                        <p class="text-xs text-slate-500">Authorize official placement figures and archive into City PESO archives</p>
                    </div>
                    <span class="text-xs font-bold text-slate-400">{{ $pendingPlacementReports->count() }} Reports</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-100 text-[11px] uppercase font-bold text-slate-500 tracking-wider">
                            <tr>
                                <th class="py-4 px-6">Company</th>
                                <th class="py-4 px-6">Report Month</th>
                                <th class="py-4 px-6 text-center">Hired Candidates</th>
                                <th class="py-4 px-6">JPO Audit Remarks</th>
                                <th class="py-4 px-6 text-right">Final Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @forelse($pendingPlacementReports as $rep)
                                @php
                                    $rData = is_array($rep->report_data) ? $rep->report_data : json_decode($rep->report_data ?? '[]', true);
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="font-black text-slate-900 text-sm">{{ $rep->company_name }}</div>
                                        <div class="text-[11px] text-slate-500">Report #{{ $rep->report_id }}</div>
                                    </td>
                                    <td class="py-4 px-6 font-bold text-slate-900">
                                        {{ date('F Y', strtotime($rep->report_month)) }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-xl bg-emerald-50 text-emerald-800 font-black border border-emerald-200">
                                            {{ $rData['total_hired'] ?? 0 }} hired
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-slate-500 max-w-xs truncate">
                                        {{ $rep->jpo_remarks ?: 'Audited and verified by JPO.' }}
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <form action="{{ route('admin.approvals.placement-reports.approve', $rep->report_id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-colors shadow-sm">
                                                    ✓ Authorize & Archive
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.approvals.placement-reports.reject', $rep->report_id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-rose-700 font-bold text-xs border border-slate-200 transition-colors">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400">
                                        <div class="text-3xl mb-2">📊</div>
                                        <p class="font-bold text-slate-700">No monthly placement reports pending authorization</p>
                                        <p class="text-xs mt-0.5">All placement records have been archived.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        <!-- Reusable Employer Document Viewer Modal -->
        @include('partials.employer-document-viewer-modal')

    </div>
</div>
@endsection
