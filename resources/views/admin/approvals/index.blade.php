@extends('layouts.admin')

@section('title', 'Manage Approvals Center')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8" x-data="{
    tab: 'jobs',
    reportModal: false,
    selectedReport: null,
    viewReport(rep) {
        this.selectedReport = rep;
        this.reportModal = true;
    },
    docModalOpen: false,
    activeDocCompany: '',
    activeDocList: [],
    selectedDocKey: '',
    currentDoc: {},
    openDocInspection(company, docs) {
        this.activeDocCompany = company;
        this.activeDocList = [];
        
        for (let [key, val] of Object.entries(docs || {})) {
            let label = 'Attached Document';
            let icon = '📄';
            let issuer = 'Regulatory Authority';
            let filename = '';
            let url = '';

            if (typeof val === 'object' && val !== null) {
                filename = val.original_name || val.path || '';
                url = val.path ? ('/storage/' + val.path) : '';
            } else if (typeof val === 'string') {
                filename = val.split('/').pop() || val;
                url = (val.startsWith('http') || val.startsWith('/')) ? val : ('/storage/' + val);
            }

            let validity = 'Current / Valid';

            if (key.includes('bir') || key.includes('2303') || key.includes('tin')) {
                label = 'BIR Certificate of Registration (Form 2303)';
                icon = '📑';
                issuer = 'Bureau of Internal Revenue (BIR District 080)';
                validity = 'Current / Valid';
            } else if (key.includes('sec') || key.includes('dti')) {
                label = 'SEC Registration or DTI Registration';
                icon = '📜';
                issuer = 'Securities and Exchange Commission (SEC) / DTI';
                validity = 'Perpetual / Registered';
            } else if (key.includes('mayor') || key.includes('permit') || key.includes('business')) {
                label = 'Mayor’s Business Permit (current year)';
                icon = '🏢';
                issuer = 'City Government of Cebu - BPLO';
                validity = 'Calendar Year ' + new Date().getFullYear();
            } else if (key.includes('philjobnet')) {
                label = 'PhilJobNet Proof of Registration';
                icon = '🌐';
                issuer = 'PhilJobNet / DOLE Bureau of Local Employment';
                validity = 'Active Registration Certificate';
            } else if (key.includes('vacanc') || key.includes('job_vacanc')) {
                label = 'Updated Job Vacancies (prescribed form)';
                icon = '💼';
                issuer = 'Cebu City DMDP Prescribed Template';
                validity = 'Active Hiring Needs';
            } else if (key.includes('dole')) {
                label = 'DOLE License / DO 174';
                icon = '🛡️';
                issuer = 'Department of Labor and Employment (DOLE RO-7)';
                validity = 'Valid License (PRPA / Subcontractor)';
            } else if (key.includes('dmw_license') || (key.includes('dmw') && !key.includes('order'))) {
                label = 'DMW License (Overseas Agency)';
                icon = '✈️';
                issuer = 'Department of Migrant Workers (DMW / POEA)';
                validity = 'Valid DMW License';
            } else if (key.includes('order') || key.includes('job_order')) {
                label = 'DMW Approved and Validated Job Orders';
                icon = '📋';
                issuer = 'Department of Migrant Workers (DMW)';
                validity = 'Verified Job Orders Period';
            } else if (key.includes('intent') || key.includes('letter')) {
                label = 'Letter of Intent (Services & Assistance Details)';
                icon = '✉️';
                issuer = 'Corporate Executive / Authorized Signatory';
                validity = 'Official Signed Request';
            } else if (key.includes('profile')) {
                label = 'Company Overview Profile / Org Chart';
                icon = '📁';
                issuer = 'Corporate Executive Board';
                validity = 'Corporate Registry Reference';
            } else {
                label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                validity = 'Supporting Verification Document';
            }

            this.activeDocList.push({
                key: key,
                label: label,
                icon: icon,
                issuer: issuer,
                filename: filename,
                url: url,
                status: 'Verified Valid',
                validity: validity
            });
        }

        if (this.activeDocList.length > 0) {
            this.selectDocument(this.activeDocList[0]);
            this.docModalOpen = true;
        }
    },
    selectDocument(doc) {
        this.selectedDocKey = doc.key;
        this.currentDoc = doc;
    }
}">
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

            <button @click="tab = 'trainers'" 
                    :class="tab === 'trainers' ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-300 shadow-sm font-black' : 'text-slate-600 hover:text-slate-900 font-bold hover:bg-slate-50'"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs transition-all">
                <span>👥 Collaborator Trainers</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black" 
                      :class="tab === 'trainers' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700'">
                    {{ $pendingTrainers->count() }}
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
                                <th class="py-4 px-6 text-center">Employer Accreditation</th>
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
                                        @if(($job->employer_accreditation_state ?? '') === 'accredited')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-800 text-[10px] font-bold border border-emerald-200" title="Accredited with DMDP">
                                                🛡️ Accredited
                                            </span>
                                        @elseif(($job->employer_accreditation_state ?? '') === 'pending')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-amber-50 text-amber-800 text-[10px] font-bold border border-amber-200" title="Accreditation application in progress">
                                                ⏳ Pending
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-slate-100 text-slate-600 text-[10px] font-bold border border-slate-200" title="Not accredited">
                                                ✕ Not Accredited
                                            </span>
                                        @endif
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
                                    <td colspan="7" class="py-12 text-center text-slate-400">
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
                        <h3 class="text-lg font-black text-slate-900">Employer Accreditations (Recommended by JPO)</h3>
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
                                <th class="py-4 px-6">Document Status</th>
                                <th class="py-4 px-6">Workflow Status</th>
                                <th class="py-4 px-6">JPO Remarks</th>
                                <th class="py-4 px-6 text-right">Admin Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @forelse($pendingAccreditations as $acc)
                                @php
                                    $docs = is_array($acc->documents) ? $acc->documents : json_decode($acc->documents ?? '[]', true);
                                    $docStatus = $acc->document_status ?? 'pending';
                                    $isDocsComplete = ($docStatus === 'complete');
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
                                        @if($isDocsComplete)
                                            <span class="inline-flex items-center gap-1 rounded-xl bg-emerald-50 text-emerald-800 px-2.5 py-1 text-[11px] font-extrabold border border-emerald-200">
                                                ✓ Complete
                                            </span>
                                        @elseif($docStatus === 'incomplete')
                                            <div class="space-y-1">
                                                <span class="inline-flex items-center gap-1 rounded-xl bg-rose-50 text-rose-800 px-2.5 py-1 text-[11px] font-extrabold border border-rose-200">
                                                    ✕ Incomplete
                                                </span>
                                                @if($acc->document_incomplete_reason)
                                                    <p class="text-[10px] text-rose-700 italic max-w-xs leading-tight">
                                                        {{ $acc->document_incomplete_reason }}
                                                    </p>
                                                @endif
                                            </div>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-xl bg-amber-50 text-amber-800 px-2.5 py-1 text-[11px] font-extrabold border border-amber-200">
                                                ⏳ Pending JPO Review
                                            </span>
                                        @endif
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
                                            <a href="{{ route('admin.approvals.accreditations.print', $acc->accreditation_id) }}" target="_blank"
                                               class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition-colors flex items-center gap-1"
                                               title="Inspect Official Cebu City DMDP Establishment Registration Form">
                                                <span>📑 Form</span>
                                                <span class="text-[9px] text-slate-400">↗</span>
                                            </a>

                                            @if(is_array($docs) && count($docs) > 0)
                                                <button type="button" 
                                                        @click='openDocInspection("{{ addslashes($acc->company_name) }}", @json($docs))'
                                                        class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition-colors">
                                                    👁️ View Docs
                                                </button>
                                            @endif

                                            @if($isDocsComplete && in_array($acc->status, ['jpo_approved', 'supervisor_approved']))
                                                <form action="{{ route('admin.approvals.accreditations.approve', $acc->accreditation_id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-colors shadow-sm cursor-pointer">
                                                        🛡️ Officially Accredit
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" disabled 
                                                        class="px-3.5 py-2 rounded-xl bg-slate-100 text-slate-400 font-bold text-xs border border-dashed border-slate-300 cursor-not-allowed"
                                                        title="{{ !$isDocsComplete ? 'Cannot accredit: Required employer documents are incomplete or unverified' : 'Cannot accredit: Awaiting JPO recommending approval first' }}">
                                                    🛡️ {{ !$isDocsComplete ? 'Documents Incomplete' : 'Awaiting JPO' }}
                                                </button>
                                            @endif

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
                    <div class="flex items-center gap-4">
                        <a href="{{ route('admin.placement-reports.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 bg-emerald-50 border border-emerald-200 px-3.5 py-1.5 rounded-xl transition-colors">
                            Browse All Placement Reports Directory &rarr;
                        </a>
                        <span class="text-xs font-bold text-slate-400">{{ $pendingPlacementReports->count() }} Pending</span>
                    </div>
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
                                        'print_url' => route('admin.placement-reports.print', $rep->report_id),
                                        'approve_url' => route('admin.approvals.placement-reports.approve', $rep->report_id),
                                        'reject_url' => route('admin.approvals.placement-reports.reject', $rep->report_id),
                                    ]);
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="font-black text-slate-900 text-sm">{{ $rep->company_name }}</div>
                                        <div class="text-[11px] text-slate-500">Report #{{ $rep->report_id }} &bull; Employer #{{ $rep->employer_id }}</div>
                                    </td>
                                    <td class="py-4 px-6 font-bold text-slate-900">
                                        {{ date('F Y', strtotime($rep->report_month)) }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-xl bg-emerald-50 text-emerald-800 font-black border border-emerald-200">
                                            {{ $rData['total_hired'] ?? 0 }} hired
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-slate-500 max-w-xs truncate text-[11px]">
                                        {{ $rep->jpo_remarks ?: 'Audited and verified by JPO.' }}
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <button type="button" 
                                                    @click='viewReport({!! $repJson !!})'
                                                    class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-800 font-bold text-xs border border-slate-200 transition-colors flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                View
                                            </button>

                                            <a href="{{ route('admin.placement-reports.print', $rep->report_id) }}" 
                                               target="_blank"
                                               class="p-2 rounded-xl text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-colors" 
                                               title="Print Official Report">
                                                🖨️
                                            </a>

                                            <form action="{{ route('admin.approvals.placement-reports.approve', $rep->report_id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-colors shadow-sm">
                                                    ✓ Authorize
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

            <!-- Placement Report Details Modal -->
            <div x-show="reportModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                <div @click.away="reportModal = false" class="bg-white rounded-3xl max-w-3xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
                    <template x-if="selectedReport">
                        <div class="space-y-6">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                                <div>
                                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Placement Report Inspector</span>
                                    <h3 class="text-xl font-black text-slate-900 mt-0.5">
                                        <span x-text="selectedReport.company_name"></span> &bull; <span x-text="selectedReport.report_month"></span>
                                    </h3>
                                    <p class="text-xs text-slate-500">Report Ref #<span x-text="selectedReport.report_id"></span> &bull; Employer ID #<span x-text="selectedReport.employer_id"></span></p>
                                </div>
                                <button @click="reportModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs">
                                <div>
                                    <span class="text-slate-400 font-bold uppercase text-[10px]">Total Placements</span>
                                    <p class="text-lg font-black text-emerald-800"><span x-text="selectedReport.total_hired"></span> Candidates</p>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-bold uppercase text-[10px]">Status</span>
                                    <p class="mt-0.5">
                                        <span class="font-bold text-slate-900 uppercase" x-text="selectedReport.status"></span>
                                    </p>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-bold uppercase text-[10px]">Submission Date</span>
                                    <p class="font-semibold text-slate-700 mt-0.5" x-text="selectedReport.submitted_at ? new Date(selectedReport.submitted_at).toLocaleDateString() : 'N/A'"></p>
                                </div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-xs" x-show="selectedReport.notes">
                                <span class="font-bold text-slate-700 block">Employer Comments / Notes:</span>
                                <p class="text-slate-600 italic mt-0.5" x-text="selectedReport.notes"></p>
                            </div>

                            <div class="space-y-2">
                                <h4 class="text-xs font-black uppercase tracking-wider text-slate-900">
                                    Itemized Candidate Placements (<span x-text="selectedReport.total_hired"></span>)
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

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                <div class="p-3.5 rounded-2xl border border-blue-200 bg-blue-50/50 space-y-1">
                                    <span class="font-bold text-blue-900 block uppercase text-[10px]">JPO Verification Remarks</span>
                                    <p class="text-slate-700 italic" x-text="selectedReport.jpo_remarks ? '“' + selectedReport.jpo_remarks + '”' : 'Pending evaluation remarks.'"></p>
                                    <p class="text-[10px] text-slate-500 pt-1" x-show="selectedReport.jpo_evaluated_at">Audited on: <span x-text="new Date(selectedReport.jpo_evaluated_at).toLocaleDateString()"></span></p>
                                </div>
                                <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50 space-y-1">
                                    <span class="font-bold text-slate-800 block uppercase text-[10px]">Admin Decision Record</span>
                                    <p class="text-slate-700 italic" x-text="selectedReport.admin_remarks ? '“' + selectedReport.admin_remarks + '”' : 'Pending authorization.'"></p>
                                    <p class="text-[10px] text-slate-500 pt-1" x-show="selectedReport.approved_at">Authorized on: <span x-text="new Date(selectedReport.approved_at).toLocaleDateString()"></span></p>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                                <a :href="selectedReport.print_url" target="_blank"
                                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-xs font-bold text-slate-700 transition-colors">
                                    🖨️ View Official Printable Report &rarr;
                                </a>

                                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
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
                                    <button type="button" @click="reportModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- PILLAR 4: COLLABORATOR TRAINER APPROVALS -->
        <!-- =================================================================== -->
        <div x-show="tab === 'trainers'" class="space-y-4">
            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Collaborator Trainer Accounts Pending Approval</h3>
                        <p class="text-xs text-slate-500">Accounts created by existing trainers require Administrator authorization before access is enabled.</p>
                    </div>
                    <span class="text-xs font-bold text-slate-400">{{ $pendingTrainers->count() }} Accounts</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-100 text-[11px] uppercase font-bold text-slate-500 tracking-wider">
                            <tr>
                                <th class="py-4 px-6">Trainer Name</th>
                                <th class="py-4 px-6">Email Address</th>
                                <th class="py-4 px-6">Specialization</th>
                                <th class="py-4 px-6">Office / Partner Institution</th>
                                <th class="py-4 px-6">Registration Date</th>
                                <th class="py-4 px-6 text-right">Decision Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @forelse($pendingTrainers as $trainer)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="font-black text-slate-900 text-sm">{{ $trainer->full_name }}</div>
                                        <div class="text-[11px] text-amber-700 font-semibold">Pending Admin Approval</div>
                                    </td>
                                    <td class="py-4 px-6 text-slate-600 font-medium">
                                        {{ $trainer->email }}
                                    </td>
                                    <td class="py-4 px-6 font-bold text-slate-800">
                                        {{ $trainer->specialization ?: 'General Vocational' }}
                                    </td>
                                    <td class="py-4 px-6 text-slate-600">
                                        {{ $trainer->partner_institution ?: ($trainer->office ?: 'DMDP Center') }}
                                    </td>
                                    <td class="py-4 px-6 text-slate-400 font-medium">
                                        {{ \Carbon\Carbon::parse($trainer->created_at)->format('M d, Y') }}
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ route('admin.approvals.trainers.approve', $trainer->user_id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-sm">
                                                    ✓ Approve Trainer
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.approvals.trainers.reject', $trainer->user_id) }}" method="POST" onsubmit="return confirm('Reject this collaborator account?');" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400">
                                        <div class="text-3xl mb-2">✅</div>
                                        <div class="font-bold text-slate-700">All collaborator trainer accounts have been audited!</div>
                                        <div class="text-xs text-slate-400 mt-1">No pending trainer registrations in the authorization queue.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Reusable Employer Document Viewer Modal -->
        @include('partials.employer-document-viewer-modal')

    </div>
</div>
@endsection
