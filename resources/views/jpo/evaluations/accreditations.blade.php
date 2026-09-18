@extends('layouts.jpo')

@section('title', 'Evaluate Accreditation Papers - JPO Portal')

@section('content')
<div x-data="{
    recModal: false,
    selectedAccId: null,
    selectedCompany: '',
    selectedDocStatus: 'pending',
    selectedIncompleteReason: '',

    docStatusModal: false,
    editAccId: null,
    editCompany: '',
    editDocStatus: 'pending',
    editIncompleteReason: '',

    docModalOpen: false,
    activeDocCompany: '',
    activeDocList: [],
    selectedDocKey: '',
    currentDoc: {},

    openRecommend(id, company, docStatus, reason) {
        this.selectedAccId = id;
        this.selectedCompany = company;
        this.selectedDocStatus = docStatus || 'pending';
        this.selectedIncompleteReason = reason || '';
        this.recModal = true;
    },

    openDocStatusModal(id, company, docStatus, reason) {
        this.editAccId = id;
        this.editCompany = company;
        this.editDocStatus = docStatus || 'pending';
        this.editIncompleteReason = reason || '';
        this.docStatusModal = true;
    },

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
                validity = 'Current Operations Overview';
            } else {
                label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                icon = '📄';
                issuer = 'Government Regulatory Agency';
                validity = 'On File';
            }

            this.activeDocList.push({
                key: key,
                label: label,
                icon: icon,
                issuer: issuer,
                validity: validity,
                filename: filename,
                url: url,
                status: 'Attached for Review'
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
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-green-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-green-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-400/20 px-3 py-1 text-xs font-bold text-green-300 border border-green-400/30">
                    <span class="h-2 w-2 rounded-full bg-green-400 animate-pulse"></span>
                    Accreditation Verification Pipeline
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Evaluate Accreditation Papers</h1>
                <p class="text-sm text-slate-300 max-w-3xl">
                    Inspect employer legal credentials, verify document completeness, specify reasons if incomplete, and recommend fully verified complete submissions directly to the City Administrator.
                </p>
            </div>

            <div class="shrink-0 bg-white/10 backdrop-blur rounded-2xl p-5 border border-white/10 text-center min-w-[150px]">
                <span class="text-xs font-bold text-green-300 uppercase tracking-wider">Total Submissions</span>
                <p class="text-3xl font-black text-green-400 mt-0.5">{{ $accreditations->total() }}</p>
                <span class="text-[10px] text-slate-300">Employer Applications</span>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                <span class="text-xs font-bold uppercase text-slate-400 mr-1">Document Status:</span>
                <a href="{{ route('jpo.evaluations.accreditations') }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors {{ !request('doc_status') && !request('status') ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    All
                </a>
                <a href="{{ route('jpo.evaluations.accreditations', ['doc_status' => 'complete']) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors {{ request('doc_status') === 'complete' ? 'bg-green-600 text-white shadow-sm' : 'bg-green-50 text-green-800 hover:bg-green-100' }}">
                    🟢 Complete
                </a>
                <a href="{{ route('jpo.evaluations.accreditations', ['doc_status' => 'incomplete']) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors {{ request('doc_status') === 'incomplete' ? 'bg-rose-600 text-white shadow-sm' : 'bg-rose-50 text-rose-800 hover:bg-rose-100' }}">
                    🔴 Incomplete
                </a>
                <a href="{{ route('jpo.evaluations.accreditations', ['doc_status' => 'pending']) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors {{ request('doc_status') === 'pending' ? 'bg-amber-600 text-white shadow-sm' : 'bg-amber-50 text-amber-800 hover:bg-amber-100' }}">
                    🟡 Pending Review
                </a>
            </div>

            <form action="{{ route('jpo.evaluations.accreditations') }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
                @if(request('doc_status'))
                    <input type="hidden" name="doc_status" value="{{ request('doc_status') }}">
                @endif
                <div class="relative w-full md:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search company..."
                           class="w-full rounded-xl border border-slate-200 pl-8 pr-3 py-1.5 text-xs text-slate-900 focus:border-green-500 focus:outline-none">
                    <span class="absolute left-2.5 top-2 text-slate-400 text-xs">🔍</span>
                </div>
                <button type="submit" class="px-4 py-1.5 rounded-xl bg-green-600 hover:bg-green-500 text-white text-xs font-bold shadow-sm">
                    Filter
                </button>
            </form>
        </div>

        <!-- Accreditations Table -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-lg font-black text-slate-900">Employer Accreditation Records</h2>
                    <p class="text-xs text-slate-500 mt-0.5">JPO document verification determines whether an employer is eligible for official recommendation to Admin.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-green-800 bg-green-50 border border-green-200 px-3 py-1.5 rounded-xl hidden sm:inline">
                        Workflow: JPO Verification &rarr; Admin Official Accreditation
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-400 uppercase tracking-wider font-bold text-[11px]">
                            <th class="pb-3 px-3">Company Name</th>
                            <th class="pb-3 px-3">Submitted Documents</th>
                            <th class="pb-3 px-3">Document Status</th>
                            <th class="pb-3 px-3">Workflow Status</th>
                            <th class="pb-3 px-3">Date Submitted</th>
                            <th class="pb-3 px-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($accreditations as $acc)
                            @php
                                $docs = is_array($acc->documents) ? $acc->documents : json_decode($acc->documents ?? '[]', true);
                                $docStatus = $acc->document_status ?? 'pending';
                                $isComplete = ($docStatus === 'complete');
                                $isIncomplete = ($docStatus === 'incomplete');
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-3 font-bold text-slate-900">
                                    <span class="text-sm block font-black">{{ $acc->company_name }}</span>
                                    <span class="text-[11px] text-slate-400">Employer ID #{{ $acc->employer_id }} &bull; Ref #{{ $acc->accreditation_id }}</span>
                                </td>

                                <td class="py-4 px-3">
                                    <div class="space-y-1.5">
                                        <div class="flex flex-wrap gap-1">
                                            @if(is_array($docs) && count($docs) > 0)
                                                @foreach($docs as $key => $doc)
                                                    <button type="button" 
                                                            @click='openDocInspection("{{ addslashes($acc->company_name) }}", @json($docs))'
                                                            class="inline-flex items-center gap-1 rounded-xl bg-slate-100 hover:bg-green-100 hover:text-green-900 border border-slate-200 px-2 py-1 text-[10px] font-bold text-slate-700 transition-colors cursor-pointer"
                                                            title="Click to inspect this document">
                                                        <span>📄</span>
                                                        <span>{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                                                        <span class="text-[9px] text-green-600">↗</span>
                                                    </button>
                                                @endforeach
                                            @else
                                                <span class="text-slate-400 italic">No digital files attached</span>
                                            @endif
                                        </div>
                                        @if(is_array($docs) && count($docs) > 0)
                                            <button type="button" 
                                                    @click='openDocInspection("{{ addslashes($acc->company_name) }}", @json($docs))'
                                                    class="inline-flex items-center gap-1 text-[11px] font-bold text-green-700 hover:text-green-900 transition-colors">
                                                <span>👁️</span> Inspect Files ({{ count($docs) }})
                                            </button>
                                        @endif
                                    </div>
                                </td>

                                <!-- Document Verification Status Column -->
                                <td class="py-4 px-3">
                                    <div class="space-y-1">
                                        @if($isComplete)
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 border border-green-300 px-3 py-1 text-xs font-extrabold text-green-800">
                                                <span>✓</span> Complete
                                            </span>
                                            <span class="block text-[10px] text-green-700 font-semibold">Eligible for Admin recommendation</span>
                                        @elseif($isIncomplete)
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-100 border border-rose-300 px-3 py-1 text-xs font-extrabold text-rose-800">
                                                <span>✕</span> Incomplete
                                            </span>
                                            @if($acc->document_incomplete_reason)
                                                <div class="p-2 rounded-xl bg-rose-50 border border-rose-200 text-[10px] text-rose-900 max-w-xs mt-1">
                                                    <span class="font-bold block">Reason:</span>
                                                    <span>{{ $acc->document_incomplete_reason }}</span>
                                                </div>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 border border-amber-300 px-3 py-1 text-xs font-extrabold text-amber-800">
                                                <span>⏳</span> Pending Review
                                            </span>
                                            <span class="block text-[10px] text-amber-700 font-medium">Documents await verification</span>
                                        @endif

                                        <div>
                                            <button type="button" 
                                                    @click='openDocStatusModal({{ $acc->accreditation_id }}, "{{ addslashes($acc->company_name) }}", "{{ $docStatus }}", "{{ addslashes($acc->document_incomplete_reason ?? '') }}")'
                                                    class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-600 hover:text-green-700 transition-colors underline decoration-dotted">
                                                <span>⚙️</span> Update Status & Reason
                                            </button>
                                        </div>
                                    </div>
                                </td>

                                <!-- Workflow Pipeline Status -->
                                <td class="py-4 px-3">
                                    @if($acc->status === 'admin_approved')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-green-100 border border-green-300 px-2.5 py-0.5 text-xs font-bold text-green-800">
                                            🛡️ Officially Accredited
                                        </span>
                                    @elseif($acc->status === 'jpo_approved')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 border border-blue-300 px-2.5 py-0.5 text-xs font-bold text-blue-800">
                                            📋 Recommended to Admin
                                        </span>
                                    @elseif($acc->status === 'submitted_to_jpo')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 border border-amber-300 px-2.5 py-0.5 text-xs font-bold text-amber-800">
                                            ⏳ Awaiting JPO Action
                                        </span>
                                    @elseif($acc->status === 'rejected')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 border border-rose-300 px-2.5 py-0.5 text-xs font-bold text-rose-800">
                                            ✕ Rejected
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                                            {{ ucfirst($acc->status) }}
                                        </span>
                                    @endif
                                </td>

                                <td class="py-4 px-3 text-slate-500 font-medium">
                                    {{ $acc->submitted_at ? date('M d, Y', strtotime($acc->submitted_at)) : 'Today' }}
                                </td>

                                <!-- Actions Column -->
                                <td class="py-4 px-3 text-right">
                                    <div class="flex flex-col sm:flex-row items-end sm:items-center justify-end gap-2">
                                        
                                        <!-- Official Establishment Registration PDF View -->
                                        <a href="{{ route('jpo.evaluations.accreditations.print', $acc->accreditation_id) }}" target="_blank"
                                           class="px-2.5 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200 transition-colors flex items-center gap-1"
                                           title="Inspect Official Cebu City DMDP Establishment Registration Form">
                                            <span>📑 PDF</span>
                                            <span class="text-[10px] text-slate-400">↗</span>
                                        </a>

                                        <!-- Update Status Dropdown Trigger -->
                                        <button type="button" 
                                                @click='openDocStatusModal({{ $acc->accreditation_id }}, "{{ addslashes($acc->company_name) }}", "{{ $docStatus }}", "{{ addslashes($acc->document_incomplete_reason ?? '') }}")'
                                                class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition-colors">
                                            Document Status &rarr;
                                        </button>

                                        <!-- Recommend to Admin Button (Conditioned on Documents Complete) -->
                                        @if($acc->status === 'submitted_to_jpo')
                                            @if($isComplete)
                                                <button type="button" 
                                                        @click='openRecommend({{ $acc->accreditation_id }}, "{{ addslashes($acc->company_name) }}", "{{ $docStatus }}", "")'
                                                        class="px-3.5 py-1.5 rounded-xl bg-green-600 hover:bg-green-500 text-white text-xs font-black shadow-md shadow-green-600/20 transition-all flex items-center gap-1">
                                                    <span>Recommend to Admin</span>
                                                    <span>&rarr;</span>
                                                </button>
                                            @else
                                                <button type="button" 
                                                        @click='openDocStatusModal({{ $acc->accreditation_id }}, "{{ addslashes($acc->company_name) }}", "{{ $docStatus }}", "{{ addslashes($acc->document_incomplete_reason ?? '') }}")'
                                                        class="px-3.5 py-1.5 rounded-xl bg-slate-100 text-slate-400 hover:text-slate-600 text-xs font-bold border border-dashed border-slate-300 transition-colors flex items-center gap-1"
                                                        title="Cannot recommend: Documents must be marked as Complete first">
                                                    <span>🔒 Needs Complete Docs</span>
                                                </button>
                                            @endif
                                        @else
                                            <span class="text-[11px] text-slate-400 font-semibold px-2">Processed</span>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400 italic">
                                    No employer accreditation papers found matching your criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($accreditations, 'links'))
                <div class="pt-4 border-t border-slate-100">
                    {{ $accreditations->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- 1. Document Status Dropdown & Reason Modal -->
    <div x-show="docStatusModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="docStatusModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-green-700 uppercase tracking-wider">Document Verification Assessment</span>
                    <h3 class="text-xl font-black text-slate-900 mt-0.5">Evaluate <span x-text="editCompany"></span></h3>
                </div>
                <button @click="docStatusModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <form :action="'/jpo/evaluations/accreditations/' + editAccId + '/document-status'" method="POST" class="space-y-4">
                @csrf

                <!-- Dropdown Status -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Employer Documents Status <span class="text-rose-500">*</span>
                    </label>
                    <select name="document_status" x-model="editDocStatus" required
                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-xs font-bold text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-400">
                        <option value="pending">⏳ Pending Review / In Progress</option>
                        <option value="complete">🟢 Complete - All Required Documents Validated & In Order</option>
                        <option value="incomplete">🔴 Incomplete - Missing or Deficient Documents</option>
                    </select>
                    <p class="text-[11px] text-slate-500">
                        <template x-if="editDocStatus === 'complete'">
                            <span class="text-green-700 font-bold">✓ Complete documents permit JPO to officially recommend the employer to Admin.</span>
                        </template>
                        <template x-if="editDocStatus === 'incomplete'">
                            <span class="text-rose-600 font-bold">✕ You must provide the reason below explaining what documents are missing or invalid.</span>
                        </template>
                        <template x-if="editDocStatus === 'pending'">
                            <span class="text-amber-700">Documents remain under evaluation. Recommendation will stay locked.</span>
                        </template>
                    </p>
                </div>

                <!-- Reason Textarea (Required if Incomplete) -->
                <div x-show="editDocStatus === 'incomplete'" x-transition class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-rose-700">
                        Reason Why Documents Are Incomplete <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="document_incomplete_reason" x-model="editIncompleteReason" rows="3"
                              :required="editDocStatus === 'incomplete'"
                              placeholder="Specify missing documents, expired permits, or deficiencies (e.g. Missing BIR Form 2303, Mayor's Permit expired, unreadable SEC copy)..."
                              class="w-full rounded-2xl border border-rose-300 bg-rose-50/40 px-4 py-3 text-xs text-slate-900 focus:border-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-400 font-medium"></textarea>
                    <p class="text-[11px] text-rose-600 font-medium">This reason will be recorded on file and dispatched as a notification to the employer.</p>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="docStatusModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-green-600 hover:bg-green-500 text-white text-xs font-black shadow-lg shadow-green-600/30 transition-all">
                        Save Document Status &rarr;
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- 2. Official Recommendation to Admin Modal -->
    <div x-show="recModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="recModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-green-700 uppercase tracking-wider">Recommend Accreditation to Admin</span>
                    <h3 class="text-xl font-black text-slate-900 mt-0.5">Endorse <span x-text="selectedCompany"></span></h3>
                </div>
                <button @click="recModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <!-- Pre-check Guard Notice -->
            <template x-if="selectedDocStatus !== 'complete'">
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-300 text-xs text-rose-900 space-y-1">
                    <p class="font-black flex items-center gap-1.5">
                        <span>⛔</span> Cannot Recommend Employer to Admin
                    </p>
                    <p>Required legal documents are currently marked as <strong class="uppercase" x-text="selectedDocStatus"></strong>.</p>
                    <p class="text-[11px] text-rose-700">JPO and Admin are not permitted to recommend or accredit any employer whose documents are incomplete. Please update the document status to Complete first.</p>
                </div>
            </template>

            <template x-if="selectedDocStatus === 'complete'">
                <div class="p-3.5 rounded-2xl bg-green-50 border border-green-300 text-xs text-green-950 space-y-0.5">
                    <p class="font-black flex items-center gap-1.5 text-green-900">
                        <span>✓</span> Documents Verified Complete
                    </p>
                    <p class="text-[11px] text-green-800">All required legal credentials have been reviewed and validated. You may submit your recommendation to the City Administrator.</p>
                </div>
            </template>

            <form :action="'/jpo/evaluations/accreditations/' + selectedAccId + '/recommend'" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Recommendation Action *</label>
                    <select name="action" required
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-green-500 focus:outline-none">
                        <option value="recommend">✓ Recommend Approval to Admin (Forward for Official Accreditation)</option>
                        <option value="reject">✕ Reject Accreditation</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">JPO Verification Remarks</label>
                    <textarea name="remarks" rows="3" placeholder="State document verification notes and endorsement remarks for the City Administrator..."
                              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-400"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="recModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            :disabled="selectedDocStatus !== 'complete'"
                            :class="selectedDocStatus === 'complete' ? 'bg-green-600 hover:bg-green-500 text-white cursor-pointer shadow-lg shadow-green-600/30' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                            class="px-7 py-2.5 rounded-xl text-xs font-black transition-all">
                        Forward to Admin &rarr;
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Reusable Employer Document Viewer Modal -->
    @include('partials.employer-document-viewer-modal')

</div>
@endsection
