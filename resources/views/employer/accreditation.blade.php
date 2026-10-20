@extends('layouts.employer')

@section('title', 'Employer Accreditation Papers - TrabaGo')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8"
     x-data="{
        docModalOpen: false,
        activeDocCompany: '',
        activeDocList: [],
        selectedDocKey: '',
        currentDoc: {},
        openDocInspection(company, docs, initialKey = null) {
            this.activeDocCompany = company;
            this.activeDocList = [];
            const documentRouteTemplate = '{{ route('accreditation.documents', ['accreditationId' => '__ACCREDITATION_ID__', 'documentKey' => '__DOCUMENT_KEY__', 'action' => '__ACTION__']) }}';
            
            for (let [key, val] of Object.entries(docs || {})) {
                if (!val) continue;

                let label = 'Attached Document';
                let icon = 'document';
                let issuer = 'Regulatory Authority';
                let filename = '';
                let url = '';
                let pathVal = '';

                if (typeof val === 'object' && val !== null) {
                    filename = val.original_name || (val.path ? val.path.split('/').pop() : '');
                    pathVal = val.file_url || val.url || val.path || '';
                } else if (typeof val === 'string') {
                    filename = val.split('/').pop() || val;
                    pathVal = val;
                }

                if (pathVal) {
                    if (pathVal.startsWith('http://') || pathVal.startsWith('https://') || pathVal.startsWith('/storage/')) {
                        url = pathVal;
                    } else if (pathVal.startsWith('storage/')) {
                        url = '/' + pathVal;
                    } else if (pathVal.startsWith('/')) {
                        url = pathVal;
                    } else {
                        url = '/storage/' + pathVal;
                    }
                }

                let validity = 'Current / Valid';
                const extensionSource = typeof val === 'object' && val !== null ? (val.path || filename) : val;
                const extension = String(extensionSource || '').split('.').pop().toLowerCase();
                const isImage = ['jpg', 'jpeg', 'png'].includes(extension);
                const fileRoute = documentRouteTemplate
                    .replace('__ACCREDITATION_ID__', encodeURIComponent(accreditationId))
                    .replace('__DOCUMENT_KEY__', encodeURIComponent(key));

                if (key.includes('bir') || key.includes('2303') || key.includes('tin')) {
                    label = 'BIR Certificate of Registration (Form 2303)';
                    icon = 'tax';
                    issuer = 'Bureau of Internal Revenue (BIR District 080)';
                    validity = 'Current / Valid';
                } else if (key.includes('sec') || key.includes('dti')) {
                    label = 'SEC Registration or DTI Registration';
                    icon = 'sec';
                    issuer = 'Securities and Exchange Commission (SEC) / DTI';
                    validity = 'Perpetual / Registered';
                } else if (key.includes('mayor') || key.includes('permit') || key.includes('business')) {
                    label = 'Mayor’s Business Permit (current year)';
                    icon = 'permit';
                    issuer = 'City Government of Cebu - BPLO';
                    validity = 'Calendar Year ' + new Date().getFullYear();
                } else if (key.includes('philjobnet')) {
                    label = 'PhilJobNet Proof of Registration';
                    icon = 'philjobnet';
                    issuer = 'PhilJobNet / DOLE Bureau of Local Employment';
                    validity = 'Active Registration Certificate';
                } else if (key.includes('vacanc') || key.includes('job_vacanc')) {
                    label = 'Updated Job Vacancies (use prescribed form)';
                    icon = 'vacancies';
                    issuer = 'Cebu City DMDP Prescribed Template';
                    validity = 'Active Hiring Needs';
                } else if (key.includes('dole')) {
                    label = 'DOLE License/DO 174';
                    icon = 'dole';
                    issuer = 'Department of Labor and Employment (DOLE RO-7)';
                    validity = 'Valid License (PRPA / Subcontractor)';
                } else if (key.includes('dmw_license') || (key.includes('dmw') && !key.includes('order'))) {
                    label = 'DMW License (Overseas Agency)';
                    icon = 'dmw';
                    issuer = 'Department of Migrant Workers (DMW / POEA)';
                    validity = 'Valid DMW License';
                } else if (key.includes('order') || key.includes('job_order')) {
                    label = 'DMW Approved and Validated Job Orders';
                    icon = 'orders';
                    issuer = 'Department of Migrant Workers (DMW)';
                    validity = 'Verified Job Orders Period';
                } else if (key.includes('intent') || key.includes('letter')) {
                    label = 'Letter of Intent (Services & Assistance Details)';
                    icon = 'intent';
                    issuer = 'Corporate Executive / Authorized Signatory';
                    validity = 'Official Signed Request';
                } else if (key.includes('profile')) {
                    label = 'Company Overview Profile / Org Chart';
                    icon = 'profile';
                    issuer = 'Corporate Executive Board';
                    validity = 'Current Operations Overview';
                } else {
                    label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    icon = 'document';
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
                    isImage: isImage,
                    previewUrl: isImage ? fileRoute.replace('__ACTION__', 'preview') : '',
                    downloadUrl: fileRoute.replace('__ACTION__', 'download'),
                    status: 'Verified Valid'
                });
            }

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
            this.currentDoc = doc;
        }
     }">
    <div class="mx-auto max-w-5xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Cebu City DMDP Official Accreditation
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Accreditation Papers</h1>
                <p class="text-sm text-slate-300">Submit legal verification documents to the Job Placement Officer (JPO) for initial evaluation, followed by PESD Supervisor endorsement and final Admin authorization.</p>
            </div>

            <div class="shrink-0 bg-white/10 backdrop-blur rounded-2xl p-5 border border-white/10 text-center min-w-[150px]">
                <span class="text-xs font-bold text-emerald-300 uppercase tracking-wider">Accreditation Status</span>
                <div class="mt-1">
                    @if($employer->is_accredited || ($accreditation && $accreditation->status === 'admin_approved'))
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500 text-white px-3 py-1 text-xs font-black">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Accredited</span>
                        </span>
                    @elseif($accreditation && ($accreditation->document_status === 'incomplete' || !empty($accreditation->document_incomplete_reason)))
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-600 text-white px-3 py-1 text-xs font-black">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Incomplete Papers</span>
                        </span>
                    @elseif($accreditation && $accreditation->status === 'supervisor_approved')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/80 text-white px-3 py-1 text-xs font-bold">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                            <span>With Admin</span>
                        </span>
                    @elseif($accreditation && $accreditation->status === 'jpo_approved')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-500/80 text-white px-3 py-1 text-xs font-bold">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <span>With Supervisor</span>
                        </span>
                    @elseif($accreditation && $accreditation->status === 'submitted_to_jpo')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/80 text-white px-3 py-1 text-xs font-bold">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Under JPO Review</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-500/60 text-white px-3 py-1 text-xs font-bold">
                            Not Submitted
                        </span>
                    @endif

                    @php
                        $jpoReason = trim((string) ($accreditation->document_incomplete_reason ?? $accreditation->jpo_remarks ?? ''));
                    @endphp
                    @if($jpoReason !== '')
                        <div class="mt-3 rounded-2xl border border-amber-200 bg-amber-50/90 px-3 py-2 text-left text-slate-800 shadow-sm">
                            <p class="text-[9px] font-black uppercase tracking-[0.2em] text-amber-700">JPO Reason</p>
                            <p class="mt-1 text-[11px] leading-relaxed text-amber-900">{{ $jpoReason }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Incomplete Documents Alert Notice (Requirement 3) -->
        @if($accreditation && ($accreditation->document_status === 'incomplete' || !empty($accreditation->document_incomplete_reason)))
            <div class="rounded-3xl border-2 border-rose-300 bg-rose-50/90 p-6 sm:p-8 text-rose-950 shadow-md space-y-3">
                <div class="flex items-start gap-4">
                    <div class="h-12 w-12 rounded-2xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div class="space-y-2 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-rose-600 text-white uppercase tracking-wider">
                                Documents Marked Incomplete by JPO
                            </span>
                            <span class="text-xs text-rose-700 font-bold">Action Required: Re-upload deficient credentials below</span>
                        </div>
                        <h3 class="text-lg font-black text-rose-950">Evaluation Findings & Reason:</h3>
                        <div class="p-4 rounded-2xl bg-white border border-rose-200 text-rose-900 text-sm font-semibold shadow-2xs leading-relaxed">
                            {{ $accreditation->document_incomplete_reason ?: 'Some attached documents are missing, unreadable, or expired. Please upload updated credentials below.' }}
                        </div>
                        <p class="text-xs text-rose-800">
                            Please use the upload section below to attach the requested or missing documents. Once submitted, your papers will automatically return to the Job Placement Officer (JPO) queue for immediate re-evaluation.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Session Status Notifications -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-rose-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1 shadow-xs">
                <span class="block font-black">Upload Failed - Please address the following issues:</span>
                <ul class="list-disc list-inside font-medium space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- 3-Stage Progress Pipeline -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Accreditation Routing Stages (Employer &rarr; JPO &rarr; Admin)</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                <!-- Step 1: Employer Submission -->
                <div class="p-4 rounded-2xl border {{ $accreditation ? 'bg-emerald-50 border-emerald-300 text-emerald-950' : 'bg-slate-50 border-slate-200 text-slate-600' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black">1. Employer</span>
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <p class="text-xs font-bold mt-1">Submit Legal Documents</p>
                    <span class="text-[11px] block mt-0.5 {{ $accreditation ? 'text-emerald-700 font-semibold' : 'text-slate-400' }}">
                        {{ $accreditation ? 'Submitted' : 'Pending Upload' }}
                    </span>
                </div>

                <!-- Step 2: JPO Evaluation -->
                <div class="p-4 rounded-2xl border {{ ($accreditation && in_array($accreditation->status, ['jpo_approved', 'supervisor_approved', 'admin_approved'])) ? 'bg-emerald-50 border-emerald-300 text-emerald-950' : (($accreditation && $accreditation->status === 'submitted_to_jpo') ? 'bg-amber-50 border-amber-300 text-amber-950' : 'bg-slate-50 border-slate-200 text-slate-400') }}">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black">2. JPO Evaluation</span>
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <p class="text-xs font-bold mt-1">Verify & Recommend</p>
                    <span class="text-[11px] block mt-0.5 {{ ($accreditation && in_array($accreditation->status, ['jpo_approved', 'supervisor_approved', 'admin_approved'])) ? 'text-emerald-700 font-semibold' : 'text-slate-400' }}">
                        {{ ($accreditation && in_array($accreditation->status, ['jpo_approved', 'supervisor_approved', 'admin_approved'])) ? 'Recommended to Admin' : 'Pending JPO' }}
                    </span>
                </div>

                <!-- Step 3: Admin Final Authorization -->
                <div class="p-4 rounded-2xl border {{ ($employer->is_accredited || ($accreditation && $accreditation->status === 'admin_approved')) ? 'bg-emerald-600 border-emerald-600 text-white shadow-md' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black">3. City Administrator</span>
                        <svg class="w-5 h-5 {{ ($employer->is_accredited || ($accreditation && $accreditation->status === 'admin_approved')) ? 'text-emerald-200' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <p class="text-xs font-bold mt-1">Official Accreditation</p>
                    <span class="text-[11px] block mt-0.5 {{ ($employer->is_accredited || ($accreditation && $accreditation->status === 'admin_approved')) ? 'text-emerald-100 font-bold' : 'text-slate-400' }}">
                        {{ ($employer->is_accredited || ($accreditation && $accreditation->status === 'admin_approved')) ? 'Officially Accredited' : 'Pending Authorization' }}
                    </span>
                </div>
            </div>
        </div>

        @php
            $uploadedDocs = $accreditation ? (is_array($accreditation->documents) ? $accreditation->documents : json_decode($accreditation->documents ?? '[]', true)) : [];
            
            $officialDocMeta = [
                'bir_2303' => ['title' => 'BIR Certificate of Registration (Form 2303)', 'category' => 'Core Mandatory', 'validity' => 'Current / Valid', 'icon' => 'tax', 'issuer' => 'Bureau of Internal Revenue (BIR District 080)'],
                'sec_dti' => ['title' => 'SEC Registration or DTI Registration', 'category' => 'Core Mandatory', 'validity' => 'Perpetual / Registered', 'icon' => 'sec', 'issuer' => 'Securities and Exchange Commission (SEC) / DTI'],
                'mayors_permit' => ['title' => 'Mayor’s Business Permit (current year)', 'category' => 'Core Mandatory', 'validity' => 'Calendar Year ' . date('Y'), 'icon' => 'permit', 'issuer' => 'City Government of Cebu - BPLO'],
                'business_permit' => ['title' => 'Mayor’s Business Permit (current year)', 'category' => 'Core Mandatory', 'validity' => 'Calendar Year ' . date('Y'), 'icon' => 'permit', 'issuer' => 'City Government of Cebu - BPLO'],
                'philjobnet_proof' => ['title' => 'PhilJobNet Proof of Registration', 'category' => 'Core Mandatory', 'validity' => 'Active Registration Certificate', 'icon' => 'philjobnet', 'issuer' => 'PhilJobNet / DOLE Bureau of Local Employment'],
                'job_vacancies_form' => ['title' => 'Updated Job Vacancies (use prescribed form)', 'category' => 'Core Mandatory', 'validity' => 'Active Hiring Needs / Attached in Section B', 'icon' => 'vacancies', 'issuer' => 'Cebu City DMDP Prescribed Template'],
                'letter_of_intent' => ['title' => 'Letter of Intent', 'category' => 'Core Mandatory', 'validity' => 'Official Request on Letterhead', 'icon' => 'intent', 'issuer' => 'Corporate Executive / Authorized Signatory'],
                'dole_license' => ['title' => 'DOLE License/DO 174', 'category' => 'When Applicable', 'validity' => 'Valid License Period', 'icon' => 'dole', 'issuer' => 'Department of Labor and Employment (DOLE RO-7)'],
                'dmw_license' => ['title' => 'DMW License', 'category' => 'When Applicable', 'validity' => 'Valid DMW / POEA License', 'icon' => 'dmw', 'issuer' => 'Department of Migrant Workers (Overseas Agency)'],
                'dmw_job_orders' => ['title' => 'DMW Approved and Validated Job Orders', 'category' => 'When Applicable', 'validity' => 'Verified Job Orders Period', 'icon' => 'orders', 'issuer' => 'Department of Migrant Workers (DMW)'],
                'company_profile' => ['title' => 'Company Overview Profile / Org Chart', 'category' => 'Supplemental', 'validity' => 'Current Operations Overview', 'icon' => 'profile', 'issuer' => 'Corporate Executive Board'],
            ];
        @endphp

        <!-- Currently Uploaded Documents Vault -->
        @if(is_array($uploadedDocs) && count($uploadedDocs) > 0)
            <div class="rounded-3xl border border-emerald-200 bg-emerald-50/40 p-6 sm:p-8 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-emerald-100 pb-4">
                    <div>
                        <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Enterprise Vault</span>
                        <h3 class="text-lg font-black text-slate-900">Your Uploaded Legal Accreditation Documents</h3>
                        <p class="text-xs text-slate-500">Original and other documents, when applicable, should be presented for validation.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('employer.accreditation.print') }}" target="_blank"
                           class="px-3.5 py-2 rounded-xl bg-white border border-emerald-300 hover:bg-emerald-50 text-emerald-800 text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Official Form (PDF)</span>
                            <svg class="w-2.5 h-2.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                        <button type="button" 
                                @click='openDocInspection("{{ addslashes($employer->company_name) }}", @json($uploadedDocs), {{ $accreditation->accreditation_id }})'
                                class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition-colors flex items-center gap-1.5 self-start sm:self-auto">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Inspect Document Hub ({{ count($uploadedDocs) }} Files)</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                    @foreach($uploadedDocs as $key => $doc)
                        @php
                            $meta = $officialDocMeta[$key] ?? [
                                'title' => ucfirst(str_replace('_', ' ', $key)),
                                'category' => 'Uploaded Document',
                                'validity' => 'Submitted on file',
                                'icon' => 'document',
                                'issuer' => 'Authorized Entity'
                            ];
                        @endphp
                        <div class="p-4 rounded-2xl bg-white border border-emerald-100 shadow-2xs space-y-3 flex flex-col justify-between hover:border-emerald-300 transition-colors">
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <div class="h-9 w-9 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 flex items-center gap-1">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Uploaded</span>
                                    </span>
                                </div>
                                <div>
                                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block">{{ $meta['category'] }}</span>
                                    <h4 class="text-xs font-black text-slate-900 leading-snug">
                                        {{ $meta['title'] }}
                                    </h4>
                                    <p class="text-[10px] text-emerald-700 font-semibold mt-0.5 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-emerald-600 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>{{ $meta['validity'] }}</span>
                                    </p>
                                </div>
                            </div>
                            <button type="button" 
                                    @click='openDocInspection("{{ addslashes($employer->company_name) }}", @json($uploadedDocs), "{{ $key }}")'
                                    class="w-full py-2 rounded-xl bg-slate-50 hover:bg-emerald-50 hover:text-emerald-900 text-slate-700 text-[11px] font-bold border border-slate-200 transition-colors text-center">
                                Preview / Inspect &rarr;
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Official Documentary Requirements & Validity Period Guidelines -->
        <div class="rounded-3xl border-2 border-emerald-300 bg-gradient-to-r from-emerald-950 via-emerald-950 to-slate-900 p-6 sm:p-8 text-white shadow-xl space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 border-b border-white/10 pb-5">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-600 text-white uppercase tracking-wider">
                            Official DOLE / DMDP Notice
                        </span>
                        <span class="text-xs text-emerald-300 font-bold">City Government of Cebu</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-white">Employer Documentary Requirements</h2>
                    <p class="text-xs sm:text-sm text-emerald-200/90 font-medium italic">
                        "Original and other documents, when applicable, should be presented for validation."
                    </p>
                </div>
                <a href="{{ route('employer.accreditation.print') }}" target="_blank"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-white hover:bg-slate-100 text-slate-900 text-xs font-black shadow-lg transition-all hover:scale-105 shrink-0">
                    <svg class="w-4 h-4 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Official Pre-filled Form</span>
                </a>
            </div>

            <!-- Required Documents and Validity Period Matrix -->
            <div class="space-y-2">
                <h3 class="text-xs font-black uppercase tracking-wider text-emerald-300">
                    Required Documents and Validity Period
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
                    
                    <!-- 1. BIR 2303 -->
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><span>BIR Certificate (Form 2303)</span></span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300">Mandatory</span>
                        </div>
                        <p class="text-[11px] text-slate-300">Bureau of Internal Revenue Certificate of Registration.</p>
                        <p class="text-[10px] text-emerald-400 font-semibold">&bull; Validity: Current / Valid</p>
                    </div>

                    <!-- 2. SEC / DTI -->
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><span>SEC or DTI Registration</span></span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300">Mandatory</span>
                        </div>
                        <p class="text-[11px] text-slate-300">Certificate of Incorporation or Business Name Registration.</p>
                        <p class="text-[10px] text-emerald-400 font-semibold">&bull; Validity: Perpetual / Registered</p>
                    </div>

                    <!-- 3. Mayor's Permit -->
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg><span>Mayor’s Business Permit</span></span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300">Mandatory</span>
                        </div>
                        <p class="text-[11px] text-slate-300">Current year business license from Cebu City Hall.</p>
                        <p class="text-[10px] text-emerald-400 font-semibold">&bull; Validity: Calendar Year {{ date('Y') }}</p>
                    </div>

                    <!-- 4. PhilJobNet -->
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg><span>PhilJobNet Proof of Registration</span></span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300">Mandatory</span>
                        </div>
                        <p class="text-[11px] text-slate-300">Active national employment database account / certification.</p>
                        <p class="text-[10px] text-emerald-400 font-semibold">&bull; Validity: Active Registration</p>
                    </div>

                    <!-- 5. Updated Job Vacancies -->
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg><span>Updated Job Vacancies</span></span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300">Mandatory</span>
                        </div>
                        <p class="text-[11px] text-slate-300">Prescribed qualification and vacancy declaration form.</p>
                        <p class="text-[10px] text-emerald-400 font-semibold">&bull; Validity: Active Hiring Needs</p>
                    </div>

                    <!-- 6. Letter of Intent -->
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg><span>Letter of Intent</span></span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300">Mandatory</span>
                        </div>
                        <p class="text-[11px] text-slate-300">For specific services and assistance needed with details.</p>
                        <p class="text-[10px] text-emerald-400 font-semibold">&bull; Validity: Official Request</p>
                    </div>

                    <!-- 7. DOLE License / DO 174 -->
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg><span>DOLE License / DO 174</span></span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300">When Applicable</span>
                        </div>
                        <p class="text-[11px] text-slate-300">For Licensed Private Recruitment & Placement Agencies (PRPA) / Subcontractors.</p>
                        <p class="text-[10px] text-amber-400 font-semibold">&bull; Validity: Valid License Period</p>
                    </div>

                    <!-- 8. DMW License -->
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg><span>DMW License</span></span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300">When Applicable</span>
                        </div>
                        <p class="text-[11px] text-slate-300">For Overseas Recruitment & Placement Agencies.</p>
                        <p class="text-[10px] text-amber-400 font-semibold">&bull; Validity: Valid DMW License</p>
                    </div>

                    <!-- 9. DMW Approved Job Orders -->
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg><span>DMW Job Orders</span></span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300">When Applicable</span>
                        </div>
                        <p class="text-[11px] text-slate-300">DMW Approved and Validated Job Orders for overseas deployment.</p>
                        <p class="text-[10px] text-amber-400 font-semibold">&bull; Validity: Verified Job Orders</p>
                    </div>

                </div>
            </div>
        </div>

        <!-- Document Upload Form -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-8">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-lg font-black text-slate-900">Upload / Update Legal Credentials</h2>
                <p class="text-xs text-slate-500 mt-0.5">Submit PDF, Word, or image copies of your official credentials. Original and other documents, when applicable, should be presented for validation.</p>
            </div>

            <form action="{{ route('employer.accreditation.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <!-- Section 1: Core Mandatory Requirements -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-900">1. Core Mandatory Requirements</h3>
                        <span class="text-[10px] font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full">Required for All Establishments</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        
                        <!-- 1. BIR 2303 -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-800">BIR Certificate (Form 2303) *</label>
                                <span class="text-[10px] text-emerald-700 font-bold">Current / Valid</span>
                            </div>
                            <input type="file" name="bir_2303" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-500">
                            <p class="text-[11px] text-slate-400">Bureau of Internal Revenue Certificate of Registration.</p>
                        </div>

                        <!-- 2. SEC / DTI -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-800">SEC or DTI Registration *</label>
                                <span class="text-[10px] text-emerald-700 font-bold">Perpetual</span>
                            </div>
                            <input type="file" name="sec_dti" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-500">
                            <p class="text-[11px] text-slate-400">Certificate of incorporation or DTI business name certificate.</p>
                        </div>

                        <!-- 3. Mayor's Business Permit -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-800">Mayor’s Business Permit *</label>
                                <span class="text-[10px] text-emerald-700 font-bold">Year {{ date('Y') }}</span>
                            </div>
                            <input type="file" name="mayors_permit" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-500">
                            <p class="text-[11px] text-slate-400">Current year business permit issued by Cebu City Hall.</p>
                        </div>

                        <!-- 4. PhilJobNet Proof of Registration -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-800">PhilJobNet Proof of Registration *</label>
                                <span class="text-[10px] text-emerald-700 font-bold">Active</span>
                            </div>
                            <input type="file" name="philjobnet_proof" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-500">
                            <p class="text-[11px] text-slate-400">Verified registration certificate or portal account proof.</p>
                        </div>

                        <!-- 5. Updated Job Vacancies -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-800">Updated Job Vacancies *</label>
                                <span class="text-[10px] text-emerald-700 font-bold">Prescribed Form</span>
                            </div>
                            <input type="file" name="job_vacancies_form" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-500">
                            <p class="text-[11px] text-slate-400">Duly filled qualification and vacancy declaration.</p>
                        </div>

                        <!-- 6. Letter of Intent -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-800">Letter of Intent *</label>
                                <span class="text-[10px] text-emerald-700 font-bold">On Letterhead</span>
                            </div>
                            <input type="file" name="letter_of_intent" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-500">
                            <p class="text-[11px] text-slate-400">Specifying services and assistance needed with details.</p>
                        </div>

                    </div>
                </div>

                <!-- Section 2: Specialized Agency Requirements (When Applicable) -->
                <div class="space-y-4 pt-4 border-t border-slate-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-900">2. Specialized Agency Requirements</h3>
                            <span class="text-[10px] font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded-full">When Applicable</span>
                        </div>
                        <span class="text-xs text-slate-400">For PRPA, Subcontractors, & Overseas Agencies</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        
                        <!-- 7. DOLE License / DO 174 -->
                        <div class="p-4 rounded-2xl border border-amber-200 bg-amber-50/30 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-800">DOLE License / DO 174</label>
                                <span class="text-[10px] text-amber-700 font-bold">PRPA / Subcon</span>
                            </div>
                            <input type="file" name="dole_license" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-600 file:text-white hover:file:bg-amber-500">
                            <p class="text-[11px] text-slate-400">For Licensed Private Recruitment & Placement Agency or Subcontractor.</p>
                        </div>

                        <!-- 8. DMW License -->
                        <div class="p-4 rounded-2xl border border-amber-200 bg-amber-50/30 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-800">DMW License</label>
                                <span class="text-[10px] text-amber-700 font-bold">Overseas</span>
                            </div>
                            <input type="file" name="dmw_license" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-600 file:text-white hover:file:bg-amber-500">
                            <p class="text-[11px] text-slate-400">For Overseas Recruitment & Placement Agency (DMW / POEA).</p>
                        </div>

                        <!-- 9. DMW Approved Job Orders -->
                        <div class="p-4 rounded-2xl border border-amber-200 bg-amber-50/30 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-800">DMW Approved Job Orders</label>
                                <span class="text-[10px] text-amber-700 font-bold">Validated</span>
                            </div>
                            <input type="file" name="dmw_job_orders" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-600 file:text-white hover:file:bg-amber-500">
                            <p class="text-[11px] text-slate-400">DMW Approved and Validated Job Orders for deployment.</p>
                        </div>

                    </div>
                </div>

                <!-- Section 3: Supplemental Information -->
                <div class="space-y-4 pt-4 border-t border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-900">3. Supplemental Information</h3>
                    </div>

                    <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2 max-w-md">
                        <label class="block text-xs font-bold text-slate-800">Company Profile / Organizational Chart</label>
                        <input type="file" name="company_profile" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-700 file:text-white hover:file:bg-slate-600">
                        <p class="text-[11px] text-slate-400">Overview of corporate structure, workforce size, and operations.</p>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <p class="text-[11px] text-slate-500">
                        Original and other documents, when applicable, should be presented for validation. Submitted files are routed to the JPO queue.
                    </p>
                    <button type="submit" class="rounded-xl bg-emerald-600 hover:bg-emerald-500 px-8 py-3 text-xs font-black text-white shadow-lg shadow-emerald-600/30 transition-all hover:scale-105 shrink-0">
                        Submit Accreditation Documents to JPO &rarr;
                    </button>
                </div>
            </form>
        </div>

    </div>

    <!-- Reusable Employer Document Viewer Modal -->
    @include('partials.employer-document-viewer-modal')

</div>
@endsection

