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

                if (key.includes('sec') || key.includes('dti')) {
                    label = 'SEC / DTI Registration';
                    icon = '📜';
                    issuer = 'Securities and Exchange Commission (SEC) / DTI';
                } else if (key.includes('permit') || key.includes('mayor')) {
                    label = 'Mayor\'s Business Permit';
                    icon = '🏢';
                    issuer = 'City Government of Cebu - BPLO';
                } else if (key.includes('bir') || key.includes('tin')) {
                    label = 'BIR Form 2303 Certificate';
                    icon = '📑';
                    issuer = 'Bureau of Internal Revenue (BIR District 080)';
                } else if (key.includes('dole')) {
                    label = 'DOLE Certificate of Registration';
                    icon = '🛡️';
                    issuer = 'Department of Labor and Employment (DOLE RO-7)';
                } else if (key.includes('profile')) {
                    label = 'Company Overview Profile';
                    icon = '📁';
                    issuer = 'Corporate Executive Board';
                } else {
                    label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                }

                this.activeDocList.push({
                    key: key,
                    label: label,
                    icon: icon,
                    issuer: issuer,
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
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500 text-white px-3 py-1 text-xs font-black">
                            ✓ Accredited
                        </span>
                    @elseif($accreditation && $accreditation->status === 'supervisor_approved')
                        <span class="inline-flex items-center gap-1 rounded-full bg-teal-500/80 text-white px-3 py-1 text-xs font-bold">
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
                <div class="p-4 rounded-2xl border {{ $accreditation ? 'bg-emerald-50 border-emerald-300 text-emerald-950' : 'bg-slate-50 border-slate-200 text-slate-600' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black">1. Employer</span>
                        <span class="text-lg">🏢</span>
                    </div>
                    <p class="text-xs font-bold mt-1">Pass Papers to JPO</p>
                    <span class="text-[11px] block mt-0.5 {{ $accreditation ? 'text-emerald-700 font-semibold' : 'text-slate-400' }}">
                        {{ $accreditation ? 'Submitted' : 'Pending Upload' }}
                    </span>
                </div>

                <!-- Step 2: JPO Evaluation -->
                <div class="p-4 rounded-2xl border {{ ($accreditation && in_array($accreditation->status, ['jpo_approved', 'supervisor_approved', 'admin_approved'])) ? 'bg-emerald-50 border-emerald-300 text-emerald-950' : (($accreditation && $accreditation->status === 'submitted_to_jpo') ? 'bg-amber-50 border-amber-300 text-amber-950' : 'bg-slate-50 border-slate-200 text-slate-400') }}">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black">2. JPO</span>
                        <span class="text-lg">📋</span>
                    </div>
                    <p class="text-xs font-bold mt-1">Evaluate & Forward</p>
                    <span class="text-[11px] block mt-0.5 {{ ($accreditation && in_array($accreditation->status, ['jpo_approved', 'supervisor_approved', 'admin_approved'])) ? 'text-emerald-700 font-semibold' : 'text-slate-400' }}">
                        {{ ($accreditation && in_array($accreditation->status, ['jpo_approved', 'supervisor_approved', 'admin_approved'])) ? 'Recommended' : 'Pending JPO' }}
                    </span>
                </div>

                <!-- Step 3: PESD Supervisor Endorsement -->
                <div class="p-4 rounded-2xl border {{ ($accreditation && in_array($accreditation->status, ['supervisor_approved', 'admin_approved'])) ? 'bg-emerald-50 border-emerald-300 text-emerald-950' : (($accreditation && $accreditation->status === 'jpo_approved') ? 'bg-amber-50 border-amber-300 text-amber-950' : 'bg-slate-50 border-slate-200 text-slate-400') }}">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black">3. Supervisor</span>
                        <span class="text-lg">🏛️</span>
                    </div>
                    <p class="text-xs font-bold mt-1">PESD Endorsement</p>
                    <span class="text-[11px] block mt-0.5 {{ ($accreditation && in_array($accreditation->status, ['supervisor_approved', 'admin_approved'])) ? 'text-emerald-700 font-semibold' : 'text-slate-400' }}">
                        {{ ($accreditation && in_array($accreditation->status, ['supervisor_approved', 'admin_approved'])) ? 'Endorsed' : 'Pending Supervisor' }}
                    </span>
                </div>

                <!-- Step 4: Admin Final Authorization -->
                <div class="p-4 rounded-2xl border {{ ($employer->is_accredited || ($accreditation && $accreditation->status === 'admin_approved')) ? 'bg-emerald-600 border-emerald-600 text-white shadow-md' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black">4. Admin</span>
                        <span class="text-lg">👑</span>
                    </div>
                    <p class="text-xs font-bold mt-1">Official Accreditation</p>
                    <span class="text-[11px] block mt-0.5 {{ ($employer->is_accredited || ($accreditation && $accreditation->status === 'admin_approved')) ? 'text-emerald-100 font-bold' : 'text-slate-400' }}">
                        {{ ($employer->is_accredited || ($accreditation && $accreditation->status === 'admin_approved')) ? 'Authorized' : 'Pending Final' }}
                    </span>
                </div>
            </div>
        </div>

        @php
            $uploadedDocs = $accreditation ? (is_array($accreditation->documents) ? $accreditation->documents : json_decode($accreditation->documents ?? '[]', true)) : [];
        @endphp

        <!-- Currently Uploaded Documents Vault -->
        @if(is_array($uploadedDocs) && count($uploadedDocs) > 0)
            <div class="rounded-3xl border border-emerald-200 bg-emerald-50/40 p-6 sm:p-8 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-emerald-100 pb-4">
                    <div>
                        <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Enterprise Vault</span>
                        <h3 class="text-lg font-black text-slate-900">Your Uploaded Legal Accreditation Documents</h3>
                    </div>
                    <button type="button" 
                            @click='openDocInspection("{{ addslashes($employer->company_name) }}", @json($uploadedDocs))'
                            class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition-colors flex items-center gap-1.5 self-start sm:self-auto">
                        <span>👁️</span> Inspect Document Hub ({{ count($uploadedDocs) }} Files)
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    @foreach($uploadedDocs as $key => $doc)
                        <div class="p-4 rounded-2xl bg-white border border-emerald-100 shadow-2xs space-y-2 flex flex-col justify-between">
                            <div class="space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-xl">📄</span>
                                    <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                                        ✓ Uploaded
                                    </span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-900 leading-snug">
                                    {{ ucfirst(str_replace('_', ' ', $key)) }}
                                </h4>
                            </div>
                            <button type="button" 
                                    @click='openDocInspection("{{ addslashes($employer->company_name) }}", @json($uploadedDocs))'
                                    class="w-full py-1.5 rounded-xl bg-slate-50 hover:bg-emerald-50 hover:text-emerald-900 text-slate-700 text-[11px] font-bold border border-slate-200 transition-colors text-center">
                                Preview / View &rarr;
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Document Upload Form -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-lg font-black text-slate-900">Upload / Update Legal Credentials</h2>
                <p class="text-xs text-slate-500">Provide PDF or image copies of your company credentials for evaluation by the Job Placement Officer.</p>
            </div>

            <form action="{{ route('employer.accreditation.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- 1. Mayor's / Business Permit -->
                    <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Mayor's / Business Permit *</label>
                        <input type="file" name="business_permit" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-500">
                        <p class="text-[11px] text-slate-400">Current year business permit issued by Cebu City Hall.</p>
                    </div>

                    <!-- 2. SEC / DTI Registration -->
                    <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">SEC / DTI Registration Certificate *</label>
                        <input type="file" name="sec_dti" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-500">
                        <p class="text-[11px] text-slate-400">Certificate of incorporation or DTI business name registration.</p>
                    </div>

                    <!-- 3. BIR 2303 Certificate -->
                    <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">BIR Form 2303 (Tax Certificate)</label>
                        <input type="file" name="bir_2303" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-500">
                        <p class="text-[11px] text-slate-400">Bureau of Internal Revenue Certificate of Registration.</p>
                    </div>

                    <!-- 4. Company Profile -->
                    <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Company Profile / Organizational Chart</label>
                        <input type="file" name="company_profile" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-500">
                        <p class="text-[11px] text-slate-400">Overview of operations, contact channels, and office location.</p>
                    </div>

                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <p class="text-[11px] text-slate-400">Submitted papers are immediately routed to the JPO queue.</p>
                    <button type="submit" class="rounded-xl bg-emerald-600 hover:bg-emerald-500 px-8 py-3 text-xs font-black text-white shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                        Submit Accreditation Papers to JPO &rarr;
                    </button>
                </div>
            </form>
        </div>

    </div>

    <!-- Reusable Employer Document Viewer Modal -->
    @include('partials.employer-document-viewer-modal')

</div>
@endsection

