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
                    status: 'Verified Valid'
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
    <div class="mx-auto max-w-5xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-green-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-green-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-400/20 px-3 py-1 text-xs font-bold text-green-300 border border-green-400/30">
                    <span class="h-2 w-2 rounded-full bg-green-400 animate-pulse"></span>
                    Cebu City DMDP Official Accreditation
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Accreditation Papers</h1>
                <p class="text-sm text-slate-300">Submit legal verification documents to the Job Placement Officer (JPO) for initial evaluation, followed by PESD Supervisor endorsement and final Admin authorization.</p>
            </div>

            <div class="shrink-0 bg-white/10 backdrop-blur rounded-2xl p-5 border border-white/10 text-center min-w-[150px]">
                <span class="text-xs font-bold text-green-300 uppercase tracking-wider">Accreditation Status</span>
                <div class="mt-1">
                    @if($employer->is_accredited || ($accreditation && $accreditation->status === 'admin_approved'))
                        <span class="inline-flex items-center gap-1 rounded-full bg-green-500 text-white px-3 py-1 text-xs font-black">
                            ✓ Accredited
                        </span>
                    @elseif($accreditation && $accreditation->status === 'supervisor_approved')
                        <span class="inline-flex items-center gap-1 rounded-full bg-green-500/80 text-white px-3 py-1 text-xs font-bold">
                            🏛️ With Admin
                        </span>
                    @elseif($accreditation && $accreditation->status === 'jpo_approved')
                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-500/80 text-white px-3 py-1 text-xs font-bold">
                            📋 With Supervisor
                        </span>
                    @elseif($accreditation && $accreditation->status === 'submitted_to_jpo')
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-500/80 text-white px-3 py-1 text-xs font-bold">
                            ⏳ Under JPO Review
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-500/60 text-white px-3 py-1 text-xs font-bold">
                            Not Submitted
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3-Stage Progress Pipeline (Figure 9, 8, 11, 10) -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Accreditation Routing Stages (Figure 9 &rarr; 8 &rarr; 11 &rarr; 10)</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-2">
                <!-- Step 1: Employer Submission -->
                <div class="p-4 rounded-2xl border {{ $accreditation ? 'bg-green-50 border-green-300 text-green-950' : 'bg-slate-50 border-slate-200 text-slate-600' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black">1. Employer</span>
                        <span class="text-lg">🏢</span>
                    </div>
                    <p class="text-xs font-bold mt-1">Pass Papers to JPO</p>
                    <span class="text-[11px] block mt-0.5 {{ $accreditation ? 'text-green-700 font-semibold' : 'text-slate-400' }}">
                        {{ $accreditation ? 'Submitted' : 'Pending Upload' }}
                    </span>
                </div>

                <!-- Step 2: JPO Evaluation -->
                <div class="p-4 rounded-2xl border {{ ($accreditation && in_array($accreditation->status, ['jpo_approved', 'supervisor_approved', 'admin_approved'])) ? 'bg-green-50 border-green-300 text-green-950' : (($accreditation && $accreditation->status === 'submitted_to_jpo') ? 'bg-amber-50 border-amber-300 text-amber-950' : 'bg-slate-50 border-slate-200 text-slate-400') }}">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black">2. JPO</span>
                        <span class="text-lg">📋</span>
                    </div>
                    <p class="text-xs font-bold mt-1">Evaluate & Forward</p>
                    <span class="text-[11px] block mt-0.5 {{ ($accreditation && in_array($accreditation->status, ['jpo_approved', 'supervisor_approved', 'admin_approved'])) ? 'text-green-700 font-semibold' : 'text-slate-400' }}">
                        {{ ($accreditation && in_array($accreditation->status, ['jpo_approved', 'supervisor_approved', 'admin_approved'])) ? 'Recommended' : 'Pending JPO' }}
                    </span>
                </div>

                <!-- Step 3: PESD Supervisor Endorsement -->
                <div class="p-4 rounded-2xl border {{ ($accreditation && in_array($accreditation->status, ['supervisor_approved', 'admin_approved'])) ? 'bg-green-50 border-green-300 text-green-950' : (($accreditation && $accreditation->status === 'jpo_approved') ? 'bg-amber-50 border-amber-300 text-amber-950' : 'bg-slate-50 border-slate-200 text-slate-400') }}">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black">3. Supervisor</span>
                        <span class="text-lg">🏛️</span>
                    </div>
                    <p class="text-xs font-bold mt-1">PESD Endorsement</p>
                    <span class="text-[11px] block mt-0.5 {{ ($accreditation && in_array($accreditation->status, ['supervisor_approved', 'admin_approved'])) ? 'text-green-700 font-semibold' : 'text-slate-400' }}">
                        {{ ($accreditation && in_array($accreditation->status, ['supervisor_approved', 'admin_approved'])) ? 'Endorsed' : 'Pending Supervisor' }}
                    </span>
                </div>

                <!-- Step 4: Admin Final Authorization -->
                <div class="p-4 rounded-2xl border {{ ($employer->is_accredited || ($accreditation && $accreditation->status === 'admin_approved')) ? 'bg-green-600 border-green-600 text-white shadow-md' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black">4. Admin</span>
                        <span class="text-lg">👑</span>
                    </div>
                    <p class="text-xs font-bold mt-1">Official Accreditation</p>
                    <span class="text-[11px] block mt-0.5 {{ ($employer->is_accredited || ($accreditation && $accreditation->status === 'admin_approved')) ? 'text-green-100 font-bold' : 'text-slate-400' }}">
                        {{ ($employer->is_accredited || ($accreditation && $accreditation->status === 'admin_approved')) ? 'Authorized' : 'Pending Final' }}
                    </span>
                </div>
            </div>
        </div>

        @php
            $uploadedDocs = $accreditation ? (is_array($accreditation->documents) ? $accreditation->documents : json_decode($accreditation->documents ?? '[]', true)) : [];
            
            $officialDocMeta = [
                'bir_2303' => ['title' => 'BIR Certificate of Registration (Form 2303)', 'category' => 'Core Mandatory', 'validity' => 'Current / Valid', 'icon' => '📑', 'issuer' => 'Bureau of Internal Revenue (BIR District 080)'],
                'sec_dti' => ['title' => 'SEC Registration or DTI Registration', 'category' => 'Core Mandatory', 'validity' => 'Perpetual / Registered', 'icon' => '📜', 'issuer' => 'Securities and Exchange Commission (SEC) / DTI'],
                'mayors_permit' => ['title' => 'Mayor’s Business Permit (current year)', 'category' => 'Core Mandatory', 'validity' => 'Calendar Year ' . date('Y'), 'icon' => '🏢', 'issuer' => 'City Government of Cebu - BPLO'],
                'business_permit' => ['title' => 'Mayor’s Business Permit (current year)', 'category' => 'Core Mandatory', 'validity' => 'Calendar Year ' . date('Y'), 'icon' => '🏢', 'issuer' => 'City Government of Cebu - BPLO'],
                'philjobnet_proof' => ['title' => 'PhilJobNet Proof of Registration', 'category' => 'Core Mandatory', 'validity' => 'Active Registration Certificate', 'icon' => '🌐', 'issuer' => 'PhilJobNet / DOLE Bureau of Local Employment'],
                'job_vacancies_form' => ['title' => 'Updated Job Vacancies (prescribed form)', 'category' => 'Core Mandatory', 'validity' => 'Active Hiring Needs / Attached in Section B', 'icon' => '💼', 'issuer' => 'Cebu City DMDP Prescribed Template'],
                'letter_of_intent' => ['title' => 'Letter of Intent (Services & Assistance Details)', 'category' => 'Core Mandatory', 'validity' => 'Official Request on Letterhead', 'icon' => '✉️', 'issuer' => 'Corporate Executive / Authorized Signatory'],
                'dole_license' => ['title' => 'DOLE License / DO 174', 'category' => 'When Applicable', 'validity' => 'Valid License Period', 'icon' => '🛡️', 'issuer' => 'Department of Labor and Employment (DOLE RO-7)'],
                'dmw_license' => ['title' => 'DMW License', 'category' => 'When Applicable', 'validity' => 'Valid DMW / POEA License', 'icon' => '✈️', 'issuer' => 'Department of Migrant Workers (Overseas Agency)'],
                'dmw_job_orders' => ['title' => 'DMW Approved and Validated Job Orders', 'category' => 'When Applicable', 'validity' => 'Verified Job Orders Period', 'icon' => '📋', 'issuer' => 'Department of Migrant Workers (DMW)'],
                'company_profile' => ['title' => 'Company Overview Profile / Org Chart', 'category' => 'Supplemental', 'validity' => 'Current Operations Overview', 'icon' => '📁', 'issuer' => 'Corporate Executive Board'],
            ];
        @endphp

        <!-- Currently Uploaded Documents Vault -->
        @if(is_array($uploadedDocs) && count($uploadedDocs) > 0)
            <div class="rounded-3xl border border-green-200 bg-green-50/40 p-6 sm:p-8 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-green-100 pb-4">
                    <div>
                        <span class="text-[10px] font-bold text-green-800 uppercase tracking-wider block">Enterprise Vault</span>
                        <h3 class="text-lg font-black text-slate-900">Your Uploaded Legal Accreditation Documents</h3>
                        <p class="text-xs text-slate-500">Original and other documents, when applicable, should be presented for validation.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('employer.accreditation.print') }}" target="_blank"
                           class="px-3.5 py-2 rounded-xl bg-white border border-green-300 hover:bg-green-50 text-green-800 text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5">
                            <span>📑</span> Official Form (PDF)
                            <span class="text-[9px] text-green-600">↗</span>
                        </a>
                        <button type="button" 
                                @click='openDocInspection("{{ addslashes($employer->company_name) }}", @json($uploadedDocs))'
                                class="px-4 py-2 rounded-xl bg-green-600 hover:bg-green-700 text-white text-xs font-bold shadow-sm transition-colors flex items-center gap-1.5 self-start sm:self-auto">
                            <span>👁️</span> Inspect Document Hub ({{ count($uploadedDocs) }} Files)
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
                                'icon' => '📄',
                                'issuer' => 'Authorized Entity'
                            ];
                        @endphp
                        <div class="p-4 rounded-2xl bg-white border border-green-100 shadow-2xs space-y-3 flex flex-col justify-between hover:border-green-300 transition-colors">
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-2xl">{{ $meta['icon'] }}</span>
                                    <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-green-100 text-green-800">
                                        ✓ Uploaded
                                    </span>
                                </div>
                                <div>
                                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block">{{ $meta['category'] }}</span>
                                    <h4 class="text-xs font-black text-slate-900 leading-snug">
                                        {{ $meta['title'] }}
                                    </h4>
                                    <p class="text-[10px] text-green-700 font-semibold mt-0.5 flex items-center gap-1">
                                        <span>⏱️</span>
                                        <span>{{ $meta['validity'] }}</span>
                                    </p>
                                </div>
                            </div>
                            <button type="button" 
                                    @click='openDocInspection("{{ addslashes($employer->company_name) }}", @json($uploadedDocs))'
                                    class="w-full py-2 rounded-xl bg-slate-50 hover:bg-green-50 hover:text-green-900 text-slate-700 text-[11px] font-bold border border-slate-200 transition-colors text-center">
                                Preview / Inspect &rarr;
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Official Documentary Requirements & Validity Period Guidelines -->
        <div class="rounded-3xl border-2 border-green-300 bg-gradient-to-r from-green-950 via-green-950 to-slate-900 p-6 sm:p-8 text-white shadow-xl space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 border-b border-white/10 pb-5">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-green-600 text-white uppercase tracking-wider">
                            Official DOLE / DMDP Notice
                        </span>
                        <span class="text-xs text-green-300 font-bold">City Government of Cebu</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-white">Employer Documentary Requirements</h2>
                    <p class="text-xs sm:text-sm text-green-200/90 font-medium italic">
                        "Original and other documents, when applicable, should be presented for validation."
                    </p>
                </div>
                <a href="{{ route('employer.accreditation.print') }}" target="_blank"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-white hover:bg-slate-100 text-slate-900 text-xs font-black shadow-lg transition-all hover:scale-105 shrink-0">
                    <span>🖨️ Official Pre-filled Form (2 Pages)</span>
                </a>
            </div>

            <!-- Required Documents and Validity Period Matrix -->
            <div class="space-y-2">
                <h3 class="text-xs font-black uppercase tracking-wider text-green-300">
                    Required Documents and Validity Period
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
                    
                    <!-- 1. BIR 2303 -->
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white flex items-center gap-1.5"><span>📑</span> BIR Certificate (Form 2303)</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-green-500/20 text-green-300">Mandatory</span>
                        </div>
                        <p class="text-[11px] text-slate-300">Bureau of Internal Revenue Certificate of Registration.</p>
                        <p class="text-[10px] text-green-400 font-semibold">&bull; Validity: Current / Valid</p>
                    </div>

                    <!-- 2. SEC / DTI -->
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white flex items-center gap-1.5"><span>📜</span> SEC or DTI Registration</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-green-500/20 text-green-300">Mandatory</span>
                        </div>
                        <p class="text-[11px] text-slate-300">Certificate of Incorporation or Business Name Registration.</p>
                        <p class="text-[10px] text-green-400 font-semibold">&bull; Validity: Perpetual / Registered</p>
                    </div>

                    <!-- 3. Mayor's Permit -->
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white flex items-center gap-1.5"><span>🏢</span> Mayor’s Business Permit</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-green-500/20 text-green-300">Mandatory</span>
                        </div>
                        <p class="text-[11px] text-slate-300">Current year business license from Cebu City Hall.</p>
                        <p class="text-[10px] text-green-400 font-semibold">&bull; Validity: Calendar Year {{ date('Y') }}</p>
                    </div>

                    <!-- 4. PhilJobNet -->
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white flex items-center gap-1.5"><span>🌐</span> PhilJobNet Proof of Registration</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-green-500/20 text-green-300">Mandatory</span>
                        </div>
                        <p class="text-[11px] text-slate-300">Active national employment database account / certification.</p>
                        <p class="text-[10px] text-green-400 font-semibold">&bull; Validity: Active Registration</p>
                    </div>

                    <!-- 5. Updated Job Vacancies -->
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white flex items-center gap-1.5"><span>💼</span> Updated Job Vacancies</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-green-500/20 text-green-300">Mandatory</span>
                        </div>
                        <p class="text-[11px] text-slate-300">Prescribed qualification and vacancy declaration form.</p>
                        <p class="text-[10px] text-green-400 font-semibold">&bull; Validity: Active Hiring Needs</p>
                    </div>

                    <!-- 6. Letter of Intent -->
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white flex items-center gap-1.5"><span>✉️</span> Letter of Intent</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-green-500/20 text-green-300">Mandatory</span>
                        </div>
                        <p class="text-[11px] text-slate-300">For specific services and assistance needed with details.</p>
                        <p class="text-[10px] text-green-400 font-semibold">&bull; Validity: Official Request</p>
                    </div>

                    <!-- 7. DOLE License / DO 174 -->
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white flex items-center gap-1.5"><span>🛡️</span> DOLE License / DO 174</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300">When Applicable</span>
                        </div>
                        <p class="text-[11px] text-slate-300">For Licensed Private Recruitment & Placement Agencies (PRPA) / Subcontractors.</p>
                        <p class="text-[10px] text-amber-400 font-semibold">&bull; Validity: Valid License Period</p>
                    </div>

                    <!-- 8. DMW License -->
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white flex items-center gap-1.5"><span>✈️</span> DMW License</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300">When Applicable</span>
                        </div>
                        <p class="text-[11px] text-slate-300">For Overseas Recruitment & Placement Agencies.</p>
                        <p class="text-[10px] text-amber-400 font-semibold">&bull; Validity: Valid DMW License</p>
                    </div>

                    <!-- 9. DMW Approved Job Orders -->
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white flex items-center gap-1.5"><span>📋</span> DMW Job Orders</span>
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
                        <span class="w-2.5 h-2.5 rounded-full bg-green-600"></span>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-900">1. Core Mandatory Requirements</h3>
                        <span class="text-[10px] font-bold text-green-800 bg-green-50 px-2 py-0.5 rounded-full">Required for All Establishments</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        
                        <!-- 1. BIR 2303 -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-800">BIR Certificate (Form 2303) *</label>
                                <span class="text-[10px] text-green-700 font-bold">Current / Valid</span>
                            </div>
                            <input type="file" name="bir_2303" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-green-600 file:text-white hover:file:bg-green-500">
                            <p class="text-[11px] text-slate-400">Bureau of Internal Revenue Certificate of Registration.</p>
                        </div>

                        <!-- 2. SEC / DTI -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-800">SEC or DTI Registration *</label>
                                <span class="text-[10px] text-green-700 font-bold">Perpetual</span>
                            </div>
                            <input type="file" name="sec_dti" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-green-600 file:text-white hover:file:bg-green-500">
                            <p class="text-[11px] text-slate-400">Certificate of incorporation or DTI business name certificate.</p>
                        </div>

                        <!-- 3. Mayor's Business Permit -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-800">Mayor’s Business Permit *</label>
                                <span class="text-[10px] text-green-700 font-bold">Year {{ date('Y') }}</span>
                            </div>
                            <input type="file" name="mayors_permit" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-green-600 file:text-white hover:file:bg-green-500">
                            <p class="text-[11px] text-slate-400">Current year business permit issued by Cebu City Hall.</p>
                        </div>

                        <!-- 4. PhilJobNet Proof of Registration -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-800">PhilJobNet Proof of Registration *</label>
                                <span class="text-[10px] text-green-700 font-bold">Active</span>
                            </div>
                            <input type="file" name="philjobnet_proof" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-green-600 file:text-white hover:file:bg-green-500">
                            <p class="text-[11px] text-slate-400">Verified registration certificate or portal account proof.</p>
                        </div>

                        <!-- 5. Updated Job Vacancies -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-800">Updated Job Vacancies *</label>
                                <span class="text-[10px] text-green-700 font-bold">Prescribed Form</span>
                            </div>
                            <input type="file" name="job_vacancies_form" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-green-600 file:text-white hover:file:bg-green-500">
                            <p class="text-[11px] text-slate-400">Duly filled qualification and vacancy declaration.</p>
                        </div>

                        <!-- 6. Letter of Intent -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-800">Letter of Intent *</label>
                                <span class="text-[10px] text-green-700 font-bold">On Letterhead</span>
                            </div>
                            <input type="file" name="letter_of_intent" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-green-600 file:text-white hover:file:bg-green-500">
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
                    <button type="submit" class="rounded-xl bg-green-600 hover:bg-green-500 px-8 py-3 text-xs font-black text-white shadow-lg shadow-green-600/30 transition-all hover:scale-105 shrink-0">
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

