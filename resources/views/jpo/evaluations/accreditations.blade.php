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

    documentRouteTemplate: '{{ route('accreditation.documents', ['accreditationId' => '__ACCREDITATION_ID__', 'documentKey' => '__DOCUMENT_KEY__', 'action' => '__ACTION__']) }}',

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

    openDocInspection(company, docs, accreditationId = null, initialKey = null) {
        this.activeDocCompany = company || '';
        this.activeDocList = [];
        this.selectedDocKey = '';
        this.currentDoc = {};

        const entries = Object.entries(docs || {});
        const seenPaths = new Set();
        const aliases = {
            sec_dti_registration: 'sec_dti',
            updated_job_vacancies: 'job_vacancies_form',
            business_permit: 'mayors_permit'
        };
        const versionGroups = new Map();
        entries.forEach(([key, val]) => {
            if (!val || key.includes('_previous_')) return;
            const canonicalKey = aliases[key] || key;
            const path = typeof val === 'object' && val !== null ? val.path : val;
            if (!versionGroups.has(canonicalKey)) versionGroups.set(canonicalKey, []);
            versionGroups.get(canonicalKey).push({ key, val, path });
        });
        const inferredPreviousKeys = new Set();
        for (const candidates of versionGroups.values()) {
            const distinctPaths = new Set(candidates.map(item => item.path).filter(Boolean));
            if (distinctPaths.size < 2) continue;
            const explicitCurrent = candidates.find(item => item.val?.revision_status === 'new');
            const current = explicitCurrent || [...candidates].sort((a, b) => {
                const aDate = Date.parse(a.val?.uploaded_at || '') || 0;
                const bDate = Date.parse(b.val?.uploaded_at || '') || 0;
                return bDate - aDate;
            })[0];
            candidates.forEach(candidate => {
                if (candidate !== current) inferredPreviousKeys.add(candidate.key);
            });
        }

        entries.forEach(([key, val]) => {
            if (!val) return;
            const documentPath = typeof val === 'object' && val !== null ? val.path : val;
            const normalizedPath = typeof documentPath === 'string' ? documentPath.replace(/\\/g, '/') : '';
            if (normalizedPath && seenPaths.has(normalizedPath)) return;
            if (normalizedPath) seenPaths.add(normalizedPath);

            const meta = this.describeDocument(key, val);
            const fileRoute = this.documentRouteTemplate
                .replace('__ACCREDITATION_ID__', encodeURIComponent(accreditationId ?? this.selectedAccId ?? ''))
                .replace('__DOCUMENT_KEY__', encodeURIComponent(key));

            const previewUrl = meta.isImage ? fileRoute.replace('__ACTION__', 'preview') : '';
            const downloadUrl = fileRoute.replace('__ACTION__', 'download');

            this.activeDocList.push({
                key: key,
                label: meta.label,
                issuer: meta.issuer,
                validity: meta.validity,
                filename: meta.filename,
                isImage: meta.isImage,
                previewUrl: previewUrl,
                downloadUrl: downloadUrl,
                status: meta.revisionStatus === 'new'
                    ? 'New upload'
                    : (meta.revisionStatus === 'previous' || inferredPreviousKeys.has(key) ? 'Previous version' : 'Attached for Review'),
                revisionStatus: meta.revisionStatus || (inferredPreviousKeys.has(key) ? 'previous' : ''),
                url: previewUrl || downloadUrl || ''
            });
        });

        if (this.activeDocList.length > 0) {
            let targetDoc = this.activeDocList[0];
            if (initialKey) {
                let found = this.activeDocList.find(d => d.key === initialKey);
                if (found) targetDoc = found;
            }
            this.selectDocument(targetDoc);
            this.docModalOpen = true;
        }
    },

    selectDocument(doc) {
        this.selectedDocKey = doc.key;
        this.currentDoc = {
            ...doc,
            url: doc.previewUrl || doc.downloadUrl || doc.url || ''
        };
    },

    describeDocument(key, val) {
        let label = 'Attached Document';
        let issuer = 'Regulatory Authority';
        let filename = '';

        if (typeof val === 'object' && val !== null) {
            filename = val.original_name || (val.path ? val.path.split('/').pop() : '');
        } else if (typeof val === 'string') {
            filename = val.split('/').pop() || val;
        }

        let validity = 'Current / Valid';
        const normalizedKey = String(key || '').toLowerCase();
        const revisionStatus = (typeof val === 'object' && val !== null && val.revision_status)
            || (normalizedKey.includes('_previous_') ? 'previous' : '');

        if (normalizedKey.includes('bir') || normalizedKey.includes('2303') || normalizedKey.includes('tin')) {
            label = 'BIR Certificate of Registration (Form 2303)';
            issuer = 'Bureau of Internal Revenue (BIR District 080)';
            validity = 'Current / Valid';
        } else if (normalizedKey.includes('sec') || normalizedKey.includes('dti')) {
            label = 'SEC Registration or DTI Registration';
            issuer = 'Securities and Exchange Commission (SEC) / DTI';
            validity = 'Perpetual / Registered';
        } else if (normalizedKey.includes('mayor') || normalizedKey.includes('permit') || normalizedKey.includes('business')) {
            label = 'Mayor’s Business Permit (current year)';
            issuer = 'City Government of Cebu - BPLO';
            validity = 'Calendar Year ' + new Date().getFullYear();
        } else if (normalizedKey.includes('philjobnet')) {
            label = 'PhilJobNet Proof of Registration';
            issuer = 'PhilJobNet / DOLE Bureau of Local Employment';
            validity = 'Active Registration Certificate';
        } else if (normalizedKey.includes('vacanc') || normalizedKey.includes('job_vacanc')) {
            label = 'Updated Job Vacancies (prescribed form)';
            issuer = 'Cebu City DMDP Prescribed Template';
            validity = 'Active Hiring Needs';
        } else if (normalizedKey.includes('dole')) {
            label = 'DOLE License / DO 174';
            issuer = 'Department of Labor and Employment (DOLE RO-7)';
            validity = 'Valid License (PRPA / Subcontractor)';
        } else if (normalizedKey.includes('dmw_license') || (normalizedKey.includes('dmw') && !normalizedKey.includes('order'))) {
            label = 'DMW License (Overseas Agency)';
            issuer = 'Department of Migrant Workers (DMW / POEA)';
            validity = 'Valid DMW License';
        } else if (normalizedKey.includes('order') || normalizedKey.includes('job_order')) {
            label = 'DMW Approved and Validated Job Orders';
            issuer = 'Department of Migrant Workers (DMW)';
            validity = 'Verified Job Orders Period';
        } else if (normalizedKey.includes('intent') || normalizedKey.includes('letter')) {
            label = 'Letter of Intent (Services & Assistance Details)';
            issuer = 'Corporate Executive / Authorized Signatory';
            validity = 'Official Signed Request';
        } else if (normalizedKey.includes('profile')) {
            label = 'Company Overview Profile / Org Chart';
            issuer = 'Corporate Executive Board';
            validity = 'Current Operations Overview';
        } else {
            label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            issuer = 'Government Regulatory Agency';
            validity = 'On File';
        }

        const extensionSource = typeof val === 'object' && val !== null ? (val.path || filename) : val;
        const extension = String(extensionSource || '').split('.').pop().toLowerCase();
        const isImage = ['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(extension);

        return { label, issuer, validity, filename, isImage, revisionStatus };
    }
}" class="min-h-screen bg-slate-50/80 px-4 py-6 sm:px-6 lg:px-8">
    <div class="space-y-6 max-w-[1680px] mx-auto">
        
        <!-- Header -->
        <div class="rounded-2xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-7 text-white shadow-lg border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-1.5">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-0.5 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Accreditation Verification Pipeline
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Evaluate Accreditation Papers</h1>
                <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                    Inspect employer legal credentials, verify document completeness, specify reasons if incomplete, and recommend fully verified submissions directly to the City Administrator.
                </p>
            </div>

            <div class="shrink-0 bg-white/10 backdrop-blur rounded-xl p-4 border border-white/10 text-center min-w-[140px]">
                <span class="text-[11px] font-bold text-emerald-300 uppercase tracking-wider">Total Submissions</span>
                <p class="text-3xl font-black text-emerald-400 mt-0.5">{{ $accreditations->total() }}</p>
                <span class="text-[10px] text-slate-300">Employer Applications</span>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="rounded-2xl border border-slate-200 bg-white p-3.5 sm:p-4 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                <span class="text-xs font-bold uppercase text-slate-400 mr-1">Document Status:</span>
                <a href="{{ route('jpo.evaluations.accreditations') }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors {{ !request('doc_status') && !request('status') ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    All
                </a>
                <a href="{{ route('jpo.evaluations.accreditations', ['doc_status' => 'complete']) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors inline-flex items-center gap-1.5 {{ request('doc_status') === 'complete' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100' }}">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Complete</span>
                </a>
                <a href="{{ route('jpo.evaluations.accreditations', ['doc_status' => 'incomplete']) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors inline-flex items-center gap-1.5 {{ request('doc_status') === 'incomplete' ? 'bg-rose-600 text-white shadow-sm' : 'bg-rose-50 text-rose-800 hover:bg-rose-100' }}">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <span>Incomplete</span>
                </a>
                <a href="{{ route('jpo.evaluations.accreditations', ['doc_status' => 'pending']) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors inline-flex items-center gap-1.5 {{ request('doc_status') === 'pending' ? 'bg-amber-600 text-white shadow-sm' : 'bg-amber-50 text-amber-800 hover:bg-amber-100' }}">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>Pending Review</span>
                </a>
            </div>

            <form action="{{ route('jpo.evaluations.accreditations') }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
                @if(request('doc_status'))
                    <input type="hidden" name="doc_status" value="{{ request('doc_status') }}">
                @endif
                <div class="relative w-full md:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search company..."
                           class="w-full rounded-xl border border-slate-200 pl-8 pr-3 py-1.5 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none">
                    <svg class="w-3.5 h-3.5 absolute left-2.5 top-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <button type="submit" class="px-4 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-sm">
                    Filter
                </button>
            </form>
        </div>

        <!-- Accreditations Table Card -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="p-5 sm:px-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 bg-white">
                <div>
                    <h2 class="text-base font-black text-slate-900">Employer Accreditation Records</h2>
                    <p class="text-xs text-slate-500 mt-0.5">JPO document verification determines whether an employer is eligible for official recommendation to Admin.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-xl self-start sm:self-auto">
                        Workflow: JPO Verification &rarr; Admin Official Accreditation
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[980px] divide-y divide-slate-100">
                    <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-bold text-[11px]">
                        <tr>
                            <th class="py-3.5 px-5 w-[22%] min-w-[200px]">Company Name</th>
                            <th class="py-3.5 px-4 w-[28%] min-w-[250px]">Submitted Documents</th>
                            <th class="py-3.5 px-4 w-[20%] min-w-[190px]">Document Status</th>
                            <th class="py-3.5 px-4 w-[12%] min-w-[130px]">Workflow Status</th>
                            <th class="py-3.5 px-4 w-[8%] min-w-[100px] whitespace-nowrap">Date Submitted</th>
                            <th class="py-3.5 px-5 w-[10%] min-w-[140px] text-right whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($accreditations as $acc)
                            @php
                                $docs = is_array($acc->documents) ? $acc->documents : json_decode($acc->documents ?? '[]', true);
                                $displayDocs = [];
                                $seenDocumentPaths = [];
                                foreach ($docs ?: [] as $docKey => $document) {
                                    $documentPath = is_array($document) ? ($document['path'] ?? null) : $document;
                                    $normalizedPath = is_string($documentPath) ? str_replace('\\', '/', $documentPath) : '';
                                    if ($normalizedPath !== '' && isset($seenDocumentPaths[$normalizedPath])) {
                                        continue;
                                    }
                                    if ($normalizedPath !== '') {
                                        $seenDocumentPaths[$normalizedPath] = true;
                                    }
                                    $displayDocs[$docKey] = $document;
                                }
                                $docStatus = $acc->document_status ?? 'pending';
                                $isComplete = ($docStatus === 'complete');
                                $isIncomplete = ($docStatus === 'incomplete');
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- Company Name -->
                                <td class="py-4 px-5 align-top">
                                    <div class="flex items-start gap-3">
                                        <div class="h-9 w-9 rounded-xl bg-slate-900 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                                            {{ strtoupper(substr($acc->company_name ?? 'C', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <span class="text-sm font-bold text-slate-900 block leading-snug" title="{{ $acc->company_name }}">{{ $acc->company_name }}</span>
                                            <div class="flex items-center gap-1.5 text-[11px] text-slate-500 mt-1 font-medium">
                                                <span>Employer #{{ $acc->employer_id }}</span>
                                                <span>&bull;</span>
                                                <span>Ref #{{ $acc->accreditation_id }}</span>
                                            </div>

                                            @if(!empty($acc->admin_rejection_remarks))
                                                <div class="mt-2 p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-[11px] text-rose-900 leading-snug">
                                                    <span class="font-bold flex items-center gap-1 text-rose-800">
                                                        <svg class="w-3.5 h-3.5 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                        Returned by Admin for Re-evaluation:
                                                    </span>
                                                    <p class="mt-1 font-medium italic">"{{ $acc->admin_rejection_remarks }}"</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Submitted Documents -->
                                <td class="py-4 px-4 align-top">
                                    <div class="space-y-1.5">
                                        <div class="flex flex-wrap gap-1.5">
                                            @if(is_array($docs) && count($docs) > 0)
                                                @foreach($displayDocs as $key => $doc)
                                                    <button type="button" 
                                                            @click='openDocInspection("{{ addslashes($acc->company_name) }}", @json($docs), {{ $acc->accreditation_id }}, "{{ $key }}")'
                                                            class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 hover:bg-emerald-50 hover:text-emerald-900 hover:border-emerald-300 border border-slate-200/80 px-2.5 py-1 text-[11px] font-semibold text-slate-700 transition-colors cursor-pointer group"
                                                            title="Click to inspect this document">
                                                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-emerald-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                        <span>{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                                                    </button>
                                                @endforeach
                                            @else
                                                <span class="text-slate-400 italic text-[11px]">No digital files attached</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Document Verification Status Column -->
                                <td class="py-4 px-4 align-top">
                                    <div class="space-y-1.5">
                                        <div class="flex items-center gap-2">
                                            @if($isComplete)
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 border border-emerald-300 px-2.5 py-0.5 text-xs font-bold text-emerald-800">
                                                    <svg class="w-3 h-3 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Complete</span>
                                                </span>
                                            @elseif($isIncomplete)
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-100 border border-rose-300 px-2.5 py-0.5 text-xs font-bold text-rose-800">
                                                    <svg class="w-3 h-3 text-rose-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    <span>Incomplete</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 border border-amber-300 px-2.5 py-0.5 text-xs font-bold text-amber-800">
                                                    <svg class="w-3 h-3 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>Pending Review</span>
                                                </span>
                                            @endif

                                            <button type="button" 
                                                    @click='openDocStatusModal({{ $acc->accreditation_id }}, "{{ addslashes($acc->company_name) }}", "{{ $docStatus }}", "{{ addslashes($acc->document_incomplete_reason ?? '') }}")'
                                                    class="p-1 rounded-lg text-slate-400 hover:text-emerald-700 hover:bg-emerald-50 border border-transparent hover:border-emerald-200 transition-colors"
                                                    title="Update Status & Reason">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </button>
                                        </div>

                                        @if($isIncomplete && $acc->document_incomplete_reason)
                                            <div class="p-2 rounded-xl bg-rose-50 border border-rose-200 text-[11px] text-rose-900 leading-snug">
                                                <span class="font-bold">Reason:</span> {{ $acc->document_incomplete_reason }}
                                            </div>
                                        @elseif($isComplete)
                                            <p class="text-[11px] text-emerald-700 font-medium">Eligible for Admin recommendation</p>
                                        @else
                                            <p class="text-[11px] text-slate-500 font-medium">Documents await verification</p>
                                        @endif
                                    </div>
                                </td>

                                <!-- Workflow Pipeline Status -->
                                <td class="py-4 px-4 align-top whitespace-nowrap">
                                    @if($acc->status === 'admin_approved')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 border border-emerald-300 px-2.5 py-0.5 text-xs font-bold text-emerald-800">
                                            <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            <span>Officially Accredited</span>
                                        </span>
                                    @elseif($acc->status === 'jpo_approved')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 border border-blue-300 px-2.5 py-0.5 text-xs font-bold text-blue-800">
                                            <svg class="w-3.5 h-3.5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                            <span>Recommended to Admin</span>
                                        </span>
                                    @elseif($acc->status === 'submitted_to_jpo')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 border border-amber-300 px-2.5 py-0.5 text-xs font-bold text-amber-800">
                                            <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Awaiting JPO Action</span>
                                        </span>
                                    @elseif($acc->status === 'rejected')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-100 border border-rose-300 px-2.5 py-0.5 text-xs font-bold text-rose-800">
                                            <svg class="w-3.5 h-3.5 text-rose-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                            <span>Rejected</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                                            {{ ucfirst($acc->status) }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Date Submitted -->
                                <td class="py-4 px-4 align-top text-slate-500 font-medium whitespace-nowrap text-xs">
                                    {{ $acc->submitted_at ? date('M d, Y', strtotime($acc->submitted_at)) : 'Today' }}
                                </td>

                                <!-- Actions Column -->
                                <td class="py-4 px-5 align-top text-right whitespace-nowrap">
                                    <div class="ml-auto flex w-fit flex-col items-stretch gap-1.5 rounded-xl border border-slate-200 bg-white p-2">
                                        
                                        <!-- Official Establishment Registration PDF View -->
                                        <a href="{{ route('jpo.evaluations.accreditations.print', $acc->accreditation_id) }}" target="_blank"
                                           class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold border border-slate-200 transition-colors inline-flex items-center gap-1.5 shrink-0"
                                           title="Inspect Official Cebu City DMDP Establishment Registration Form">
                                            <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            <span>PDF</span>
                                            <svg class="w-2.5 h-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>

                                        @if(is_array($docs) && count($docs) > 0)
                                            <button type="button"
                                                    @click='openDocInspection("{{ addslashes($acc->company_name) }}", @json($docs), {{ $acc->accreditation_id }})'
                                                    class="px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold border border-emerald-200 transition-colors inline-flex items-center gap-1.5 shrink-0"
                                                    title="Review uploaded accreditation papers">
                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>View Docs</span>
                                            </button>
                                        @endif

                                        <!-- Action Buttons: Same Function as Admin (Accredit, Reject, Disaccredit, Certificate) -->
                                        @if($acc->status === 'admin_approved')
                                            <!-- Official Accreditation Certificate -->
                                            <a href="{{ route('jpo.evaluations.accreditations.certificate', $acc->accreditation_id) }}" target="_blank"
                                               class="px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 text-xs font-bold transition-colors inline-flex items-center gap-1 shrink-0"
                                               title="View Official Certificate of Accreditation">
                                                <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                <span>Certificate</span>
                                            </a>

                                            <!-- Disaccredit Employer -->
                                            <form action="{{ route('jpo.evaluations.accreditations.disaccredit', $acc->accreditation_id) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Revoke official accreditation for {{ addslashes($acc->company_name) }}?');">
                                                @csrf
                                                <button type="submit" 
                                                        class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition-colors">
                                                    Disaccredit
                                                </button>
                                            </form>
                                        @else
                                            <!-- Direct Accreditation (JPO & Admin Shared Authority) -->
                                            <form action="{{ route('jpo.evaluations.accreditations.accredit', $acc->accreditation_id) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Grant official DMDP Accreditation & generate Certificate for {{ addslashes($acc->company_name) }}?');">
                                                @csrf
                                                <button type="submit" 
                                                        class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1 shrink-0"
                                                        title="Directly Accredit and Issue Certificate">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                    <span>Accredit</span>
                                                </button>
                                            </form>

                                            <!-- Recommend to Admin Button (Conditioned on Documents Complete) -->
                                            @if($acc->status === 'submitted_to_jpo')
                                                @if($isComplete)
                                                <button type="button" 
                                                        @click='openRecommend({{ $acc->accreditation_id }}, "{{ addslashes($acc->company_name) }}", "{{ $docStatus }}", "")'
                                                        class="px-2.5 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-bold transition-all inline-flex items-center gap-1 shrink-0"
                                                        title="Forward Recommendation to Administrator">
                                                    <span>Recommend</span>
                                                    <span>&rarr;</span>
                                                </button>
                                            @endif

                                            <!-- Reject Accreditation Papers -->
                                            <form action="{{ route('jpo.evaluations.accreditations.reject', $acc->accreditation_id) }}" method="POST" class="inline"
                                                  onsubmit="var r = prompt('Enter mandatory remarks for rejecting this accreditation:'); if(!r || r.trim().length < 5){ alert('Remarks are mandatory (min 5 chars).'); return false; } this.remarks.value = r; return true;">
                                                @csrf
                                                <input type="hidden" name="remarks" value="">
                                                <button type="submit" 
                                                        class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition-colors">
                                                    Reject
                                                </button>
                                            </form>
                                        @endif

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
                <div class="p-4 sm:px-6 border-t border-slate-100 bg-slate-50/50">
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
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Document Verification Assessment</span>
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
                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-xs font-bold text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        <option value="pending">Pending Review / In Progress</option>
                        <option value="complete">Complete - All Required Documents Validated & In Order</option>
                        <option value="incomplete">Incomplete - Missing or Deficient Documents</option>
                    </select>
                    <p class="text-[11px] text-slate-500">
                        <template x-if="editDocStatus === 'complete'">
                            <span class="text-emerald-700 font-bold inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Complete documents permit JPO to officially recommend the employer to Admin.</span>
                            </span>
                        </template>
                        <template x-if="editDocStatus === 'incomplete'">
                            <span class="text-rose-600 font-bold inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>You must provide the reason below explaining what documents are missing or invalid.</span>
                            </span>
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
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-lg shadow-emerald-600/30 transition-all">
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
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Recommend Accreditation to Admin</span>
                    <h3 class="text-xl font-black text-slate-900 mt-0.5">Endorse <span x-text="selectedCompany"></span></h3>
                </div>
                <button @click="recModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <!-- Pre-check Guard Notice -->
            <template x-if="selectedDocStatus !== 'complete'">
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-300 text-xs text-rose-900 space-y-1">
                    <p class="font-black flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Cannot Recommend Employer to Admin</span>
                    </p>
                    <p>Required legal documents are currently marked as <strong class="uppercase" x-text="selectedDocStatus"></strong>.</p>
                    <p class="text-[11px] text-rose-700">JPO and Admin are not permitted to recommend or accredit any employer whose documents are incomplete. Please update the document status to Complete first.</p>
                </div>
            </template>

            <template x-if="selectedDocStatus === 'complete'">
                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-300 text-xs text-emerald-950 space-y-0.5">
                    <p class="font-black flex items-center gap-1.5 text-emerald-900">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Documents Verified Complete</span>
                    </p>
                    <p class="text-[11px] text-emerald-800">All required legal credentials have been reviewed and validated. You may submit your recommendation to the City Administrator.</p>
                </div>
            </template>

            <form :action="'/jpo/evaluations/accreditations/' + selectedAccId + '/recommend'" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Recommendation Action *</label>
                    <select name="action" required
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none">
                        <option value="recommend">Recommend Approval to Admin (Forward for Official Accreditation)</option>
                        <option value="reject">Reject Accreditation</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">JPO Verification Remarks</label>
                    <textarea name="remarks" rows="3" placeholder="State document verification notes and endorsement remarks for the City Administrator..."
                              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="recModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            :disabled="selectedDocStatus !== 'complete'"
                            :class="selectedDocStatus === 'complete' ? 'bg-emerald-600 hover:bg-emerald-500 text-white cursor-pointer shadow-lg shadow-emerald-600/30' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
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
