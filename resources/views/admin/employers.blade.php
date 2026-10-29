@extends('layouts.admin')

@section('title', 'Employers Registry')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8"
     x-data="{
        viewModal: false,
        selectedEmployer: null,
        openView(emp) {
            this.selectedEmployer = emp;
            this.viewModal = true;
        },
        docModalOpen: false,
        activeDocCompany: '',
        activeDocList: [],
        selectedDocKey: '',
        currentDoc: {},
        openDocInspection(company, docs, initialKey = null) {
            this.activeDocCompany = company;
            this.activeDocList = [];
            
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

                if (key.includes('bir') || key.includes('2303') || key.includes('tin')) {
                    label = 'BIR Certificate of Registration (Form 2303)';
                    icon = 'form';
                    issuer = 'Bureau of Internal Revenue (BIR District 080)';
                    validity = 'Current / Valid';
                } else if (key.includes('sec') || key.includes('dti')) {
                    label = 'SEC Registration or DTI Registration';
                    icon = 'scroll';
                    issuer = 'Securities and Exchange Commission (SEC) / DTI';
                    validity = 'Perpetual / Registered';
                } else if (key.includes('mayor') || key.includes('permit') || key.includes('business')) {
                    label = 'Mayor’s Business Permit (current year)';
                    icon = 'permit';
                    issuer = 'City Government of Cebu - BPLO';
                    validity = 'Calendar Year ' + new Date().getFullYear();
                } else if (key.includes('philjobnet')) {
                    label = 'PhilJobNet Proof of Registration';
                    icon = 'network';
                    issuer = 'PhilJobNet / DOLE Bureau of Local BLE';
                    validity = 'Active Registration Certificate';
                } else if (key.includes('vacanc') || key.includes('job_vacanc')) {
                    label = 'Updated Job Vacancies (prescribed form)';
                    icon = 'briefcase';
                    issuer = 'Cebu City DMDP Prescribed Template';
                    validity = 'Active Hiring Needs';
                } else if (key.includes('dole')) {
                    label = 'DOLE License / DO 174';
                    icon = 'shield';
                    issuer = 'Department of Labor and Employment (DOLE RO-7)';
                    validity = 'Valid License (PRPA / Subcontractor)';
                } else if (key.includes('dmw_license') || (key.includes('dmw') && !key.includes('order'))) {
                    label = 'DMW License (Overseas Agency)';
                    icon = 'agency';
                    issuer = 'Department of Migrant Workers (DMW / POEA)';
                    validity = 'Valid DMW License';
                } else if (key.includes('order') || key.includes('job_order')) {
                    label = 'DMW Approved and Validated Job Orders';
                    icon = 'clipboard';
                    issuer = 'Department of Migrant Workers (DMW)';
                    validity = 'Verified Job Orders Period';
                } else if (key.includes('intent') || key.includes('letter')) {
                    label = 'Letter of Intent (Services & Assistance Details)';
                    icon = 'letter';
                    issuer = 'Corporate Executive / Authorized Signatory';
                    validity = 'Official Signed Request';
                } else if (key.includes('profile')) {
                    label = 'Company Overview Profile / Org Chart';
                    icon = 'folder';
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
    <div class="w-full max-w-[1680px] mx-auto space-y-8">

        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    DMDP Corporate Partnerships
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Employers Registry</h1>
                <p class="text-sm text-slate-300">
                    Manage corporate partner accounts, review legal accreditation documents, and audit employer job posting quotas.
                </p>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Employers</span>
                    <p class="text-3xl font-black text-slate-900">{{ $totalEmployers ?? 0 }}</p>
                    <span class="text-[11px] text-slate-500">Registered companies</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-slate-100 text-slate-800 flex items-center justify-center">
                    <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Accredited Partners</span>
                    <p class="text-3xl font-black text-emerald-700">{{ $accreditedEmployers ?? 0 }}</p>
                    <span class="text-[11px] text-emerald-800">Verified & authorized</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pending Accreditation</span>
                    <p class="text-3xl font-black text-amber-600">{{ $pendingAccreditation ?? 0 }}</p>
                    <span class="text-[11px] text-amber-700">Awaiting authorization</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-800 border border-amber-200 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="GET" action="{{ route('admin.employers') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
                <div class="lg:col-span-6 space-y-1">
                    <label class="text-xs font-bold text-slate-700">Search Company</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Company name..."
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                </div>

                <div class="lg:col-span-3 space-y-1">
                    <label class="text-xs font-bold text-slate-700">Accreditation Status</label>
                    <select name="status" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        <option value="">All Statuses</option>
                        <option value="accredited" {{ request('status') == 'accredited' ? 'selected' : '' }}>Accredited Only</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Only</option>
                    </select>
                </div>

                <div class="lg:col-span-3 flex items-center gap-2">
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('admin.employers') }}" class="px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Employers Table -->
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="p-6 sm:p-8 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-900">Registered Partner Employers</h3>
                    <p class="text-xs text-slate-500">Corporate entities authorized to post job listings and review candidate referrals</p>
                </div>
                <span class="text-xs font-bold text-slate-400">{{ $employers->total() ?? count($employers) }} Employers</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-100 text-[11px] uppercase font-bold text-slate-500 tracking-wider">
                        <tr>
                            <th class="py-4 px-6">Company</th>
                            <th class="py-4 px-6">Account Status</th>
                            <th class="py-4 px-6">Legal Verification Documents</th>
                            <th class="py-4 px-6 text-center">Accreditation Status</th>
                            <th class="py-4 px-6 text-center">Jobs Posted</th>
                            <th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($employers ?? [] as $employer)
                            @php
                                $docs = is_array($employer->documents ?? null) ? $employer->documents : json_decode($employer->documents ?? '[]', true);
                                $isAccountApproved = ($employer->user_status === 'active' && ($employer->user_approved ?? 1));
                                $isJpoRecommended = ($employer->accreditation_status === 'jpo_approved' || $employer->accreditation_status === 'supervisor_approved');
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-xl bg-emerald-900 text-white flex items-center justify-center ring-2 ring-emerald-500/30 shrink-0">
                                            <svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        </div>
                                        <div>
                                            <div class="font-black text-slate-900 text-sm">{{ $employer->company_name }}</div>
                                            <div class="text-[11px] text-slate-500">Employer ID #{{ $employer->employer_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="space-y-1">
                                        <div class="text-xs font-bold text-slate-900">{{ $employer->email ?? 'N/A' }}</div>
                                        <div>
                                            @if($isAccountApproved)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 text-[10px] font-bold border border-emerald-200">
                                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Account Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-amber-50 text-amber-800 text-[10px] font-bold border border-amber-200">
                                                    <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Account Pending Approval
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    @if(is_array($docs) && count($docs) > 0)
                                        <div class="space-y-1">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($docs as $k => $d)
                                                    <button type="button" 
                                                            @click='openDocInspection("{{ addslashes($employer->company_name) }}", @json($docs), "{{ $k }}")'
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-emerald-100 hover:text-emerald-900 text-slate-700 text-[10px] font-bold border border-slate-200 transition-colors cursor-pointer"
                                                            title="Click to inspect this document">
                                                        <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                        <span>{{ ucfirst(str_replace('_', ' ', $k)) }}</span>
                                                        <span class="text-[9px] text-emerald-600">&rarr;</span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">No files on record</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if($employer->is_accredited)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-bold border bg-emerald-50 text-emerald-800 border-emerald-200">
                                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            Accredited
                                        </span>
                                    @elseif($isJpoRecommended)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-bold border bg-blue-50 text-blue-800 border-blue-200" title="JPO has evaluated and recommended this employer">
                                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                            JPO Recommended
                                        </span>
                                    @elseif($employer->accreditation_status === 'submitted_to_jpo')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-bold border bg-amber-50 text-amber-800 border-amber-200" title="Awaiting JPO evaluation">
                                            <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Under JPO Review
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-bold border bg-slate-100 text-slate-600 border-slate-200">
                                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Not Accredited
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 font-bold border border-slate-200">
                                        {{ $employer->jobs_count ?? 0 }} jobs
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        @php
                                            $empData = [
                                                'id' => $employer->employer_id,
                                                'company_name' => $employer->company_name,
                                                'trade_name' => $employer->trade_name ?? '',
                                                'email' => $employer->email ?? 'N/A',
                                                'contact_person' => $employer->contact_person ?? 'N/A',
                                                'contact_number' => $employer->contact_number ?? 'Not provided',
                                                'company_address' => $employer->company_address ?? 'Cebu City',
                                                'business_nature' => $employer->business_nature ?? 'General Business',
                                                'workforce_size' => $employer->workforce_size ?? 'Not specified',
                                                'is_accredited' => (bool)($employer->is_accredited ?? false),
                                                'is_account_approved' => (bool)$isAccountApproved,
                                                'is_jpo_recommended' => (bool)$isJpoRecommended,
                                                'accreditation_status' => $employer->accreditation_status ?? 'pending',
                                                'jpo_remarks' => $employer->jpo_remarks ?? '',
                                                'jobs_count' => $employer->jobs_count ?? 0,
                                                'docs' => $docs,
                                                'user_id' => $employer->user_id ?? null,
                                                'created_at' => !empty($employer->created_at) ? date('F d, Y', strtotime($employer->created_at)) : 'N/A'
                                            ];
                                        @endphp
                                        <button type="button" 
                                                @click='openView(@json($empData))'
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition-colors"
                                                title="View Employer Profile">
                                            <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>View</span>
                                        </button>

                                        @if(is_array($docs) && count($docs) > 0)
                                            <button type="button" 
                                                    @click='openDocInspection("{{ addslashes($employer->company_name) }}", @json($docs))'
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition-colors"
                                                    title="View Legal Documents">
                                                <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                <span>Docs</span>
                                            </button>
                                        @endif

                                        {{-- 1. Employer Account Approval (Independent from Accreditation) --}}
                                        @if(!$isAccountApproved && !empty($employer->user_id))
                                            <form action="{{ route('admin.users.approve', $employer->user_id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-colors shadow-sm"
                                                        title="Approve Employer Account">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Approve Account</span>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- 2. Employer Accreditation & Control Actions --}}
                                        @if(!$employer->is_accredited)
                                            {{-- Accredit Button (Direct Accredit with warning or JPO Recommended) --}}
                                            <button onclick="accreditEmployer({{ $employer->employer_id }}, {{ $isJpoRecommended ? 'true' : 'false' }})" 
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg {{ $isJpoRecommended ? 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm' : 'bg-amber-600 hover:bg-amber-700 text-white shadow-sm' }} font-bold text-xs transition-colors" 
                                                    title="{{ $isJpoRecommended ? 'Grant DMDP Accreditation' : 'Direct Accredit without JPO Recommendation (with Warning)' }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                <span>{{ $isJpoRecommended ? 'Accredit' : 'Direct Accredit' }}</span>
                                            </button>

                                            {{-- Reject / Return to JPO with remarks --}}
                                            <button onclick="rejectAccreditationAdmin({{ $employer->employer_id }})" 
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs transition-colors" 
                                                    title="Reject & Return Application to JPO with Mandatory Remarks">
                                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                <span>Reject</span>
                                            </button>
                                        @else
                                            {{-- Print / Save Certificate --}}
                                            <a href="{{ route('admin.employers.certificate', $employer->employer_id) }}" target="_blank"
                                               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 font-bold text-xs transition-colors"
                                               title="View & Print Official Certificate of Accreditation">
                                                <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                <span>Certificate</span>
                                            </a>

                                            {{-- Disaccredit Button --}}
                                            <button onclick="disaccreditEmployer({{ $employer->employer_id }})" 
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs transition-colors" 
                                                    title="Disaccredit Employer & Revoke Status">
                                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                <span>Disaccredit</span>
                                            </button>
                                        @endif

                                        {{-- 3. Direct Account Impersonation / Use --}}
                                        <form action="{{ route('admin.employers.impersonate', $employer->employer_id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 font-bold text-xs transition-colors" 
                                                    title="Directly Log In & Use this Employer Account">
                                                <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                                <span>Use</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <div class="h-12 w-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <p class="font-bold text-slate-700">No employers found</p>
                                    <p class="text-xs mt-0.5">Try adjusting your search criteria.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($employers) && method_exists($employers, 'hasPages') && $employers->hasPages())
                <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                    {{ $employers->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- View Employer Details Modal -->
    <div x-show="viewModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="viewModal = false" class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6 max-h-[90vh] overflow-y-auto" x-show="selectedEmployer">
            
            <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Employer Profile</span>
                    <h3 class="text-2xl font-black text-slate-900" x-text="selectedEmployer.company_name"></h3>
                    <p class="text-xs text-slate-400">Employer ID #<span x-text="selectedEmployer.id"></span> &bull; <span x-text="selectedEmployer.trade_name ? ('Trade: ' + selectedEmployer.trade_name) : selectedEmployer.email"></span></p>
                </div>
                <button @click="viewModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <!-- Badges -->
            <div class="grid grid-cols-3 gap-3 text-xs bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <div>
                    <span class="text-slate-400 text-[10px] font-bold uppercase block">Accreditation</span>
                    <span class="font-bold uppercase" :class="selectedEmployer.is_accredited ? 'text-emerald-700' : 'text-amber-600'" x-text="selectedEmployer.is_accredited ? 'Accredited' : (selectedEmployer.accreditation_status || 'Pending')"></span>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] font-bold uppercase block">Account</span>
                    <span class="font-bold" :class="selectedEmployer.is_account_approved ? 'text-emerald-700' : 'text-amber-600'" x-text="selectedEmployer.is_account_approved ? 'Approved' : 'Pending'"></span>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] font-bold uppercase block">Vacancies</span>
                    <span class="font-bold text-slate-900" x-text="selectedEmployer.jobs_count + ' Openings'"></span>
                </div>
            </div>

            <!-- Corporate Details -->
            <div class="space-y-2 text-xs">
                <h4 class="font-black uppercase tracking-wider text-slate-800">Establishment Information</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                    <div>
                        <span class="text-slate-400 font-semibold block text-[10px] uppercase">Industry / Nature</span>
                        <span class="font-bold text-slate-900" x-text="selectedEmployer.business_nature"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold block text-[10px] uppercase">Workforce Size</span>
                        <span class="font-bold text-slate-900" x-text="selectedEmployer.workforce_size"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold block text-[10px] uppercase">Contact Person</span>
                        <span class="font-bold text-slate-900" x-text="selectedEmployer.contact_person"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold block text-[10px] uppercase">Contact Phone</span>
                        <span class="font-bold text-slate-900" x-text="selectedEmployer.contact_number"></span>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-slate-400 font-semibold block text-[10px] uppercase">Official Address</span>
                        <span class="font-bold text-slate-900" x-text="selectedEmployer.company_address"></span>
                    </div>
                </div>
            </div>

            <!-- JPO Remarks / Incomplete Reason -->
            <template x-if="selectedEmployer.jpo_remarks">
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900 space-y-1">
                    <span class="font-black uppercase text-[11px] block">JPO Accreditation Feedback / Remarks:</span>
                    <p class="leading-relaxed" x-text="selectedEmployer.jpo_remarks"></p>
                </div>
            </template>

            <!-- Attached Documents -->
            <div class="space-y-2 text-xs">
                <h4 class="font-black uppercase tracking-wider text-slate-800">Accreditation Documents</h4>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                    <template x-if="selectedEmployer.docs && Object.keys(selectedEmployer.docs).length > 0">
                        <div class="flex flex-wrap gap-2">
                            <button type="button" 
                                    @click="viewModal = false; openDocInspection(selectedEmployer.company_name, selectedEmployer.docs)"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Open Live Document Viewer & Inspection</span>
                            </button>
                        </div>
                    </template>
                    <template x-if="!selectedEmployer.docs || Object.keys(selectedEmployer.docs).length === 0">
                        <p class="text-slate-400 italic">No accreditation documents uploaded yet.</p>
                    </template>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-2 flex-wrap">
                    <template x-if="!selectedEmployer.is_accredited">
                        <div class="flex items-center gap-2 flex-wrap">
                            <button type="button" @click="viewModal = false; accreditEmployer(selectedEmployer.id, selectedEmployer.is_jpo_recommended)"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-white font-bold text-xs shadow-sm transition-colors"
                                    :class="selectedEmployer.is_jpo_recommended ? 'bg-emerald-600 hover:bg-emerald-500' : 'bg-amber-600 hover:bg-amber-500'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span x-text="selectedEmployer.is_jpo_recommended ? 'Grant DMDP Accreditation' : 'Direct Accredit (Override)'"></span>
                            </button>

                            <button type="button" @click="viewModal = false; rejectAccreditationAdmin(selectedEmployer.id)"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Reject & Return to JPO</span>
                            </button>
                        </div>
                    </template>

                    <template x-if="selectedEmployer.is_accredited">
                        <div class="flex items-center gap-2 flex-wrap">
                            <a :href="'/admin/employers/' + selectedEmployer.id + '/certificate'" target="_blank"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 font-bold text-xs transition-colors">
                                <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>View Certificate of Accreditation</span>
                            </a>

                            <button type="button" @click="viewModal = false; disaccreditEmployer(selectedEmployer.id)"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                <span>Disaccredit Employer</span>
                            </button>
                        </div>
                    </template>
                </div>
                <button type="button" @click="viewModal = false" class="px-5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Reusable Employer Document Viewer Modal -->
    @include('partials.employer-document-viewer-modal')

</div>

<script>
function executeAccreditation(id, isDirectOverride) {
    fetch(`/admin/employers/${id}/accredit`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ direct_override: isDirectOverride })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Accreditation Granted!',
                html: `<p class="text-xs text-slate-700">${data.success}</p>${data.certificate_no ? `<p class="mt-2 text-xs font-mono font-bold text-emerald-800">Certificate No: ${data.certificate_no}</p>` : ''}`,
                icon: 'success',
                confirmButtonColor: '#059669',
                confirmButtonText: 'View Registry'
            }).then(() => location.reload());
        } else {
            Swal.fire('Cannot Accredit', data.error || 'Something went wrong.', 'error');
        }
    })
    .catch(() => Swal.fire('Error', 'Network error occurred.', 'error'));
}

function accreditEmployer(id, isJpoRecommended) {
    if (!isJpoRecommended) {
        Swal.fire({
            title: '⚠️ Direct Accreditation Override',
            html: '<div class="text-left text-xs space-y-2 text-slate-700 bg-amber-50 p-4 rounded-xl border border-amber-200"><p class="font-bold text-amber-900">WARNING: No JPO Recommendation Recorded</p><p>This employer has not yet received a recommendation from the Job Placement Office (JPO).</p><p>As DMDP Administrator, you are directly bypassing JPO recommendation to officially accredit this employer and generate their official Certificate.</p><p class="font-bold text-amber-900">Are you sure you want to proceed?</p></div>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d97706',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Grant Direct Accreditation',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                executeAccreditation(id, true);
            }
        });
        return;
    }

    Swal.fire({
        title: 'Grant Official Employer Accreditation?',
        text: 'JPO has recommended this company. This will officially accredit the employer and generate their official Certificate of Accreditation.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, Accredit Company'
    }).then((result) => {
        if (result.isConfirmed) {
            executeAccreditation(id, false);
        }
    });
}

function disaccreditEmployer(id) {
    Swal.fire({
        title: '⚠️ Revoke Employer Accreditation?',
        html: '<div class="text-left text-xs space-y-2 text-slate-700 bg-rose-50 p-4 rounded-xl border border-rose-200"><p class="font-bold text-rose-900">WARNING: Accreditation Disaccreditation</p><p>This will immediately revoke official DMDP accreditation for this employer, invalidate their Certificate of Accreditation, and pause jobseeker referral processing.</p><p class="font-bold text-rose-900">Are you sure you want to proceed?</p></div>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, Disaccredit Employer',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/employers/${id}/disaccredit`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Disaccredited', data.success, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.error || 'Failed to disaccredit employer.', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Network error occurred.', 'error'));
        }
    });
}

function rejectAccreditationAdmin(id) {
    Swal.fire({
        title: 'Return Accreditation Application to JPO',
        text: 'Mandatory administrative remarks are required to explain why this employer application is rejected and returned to the JPO queue.',
        input: 'textarea',
        inputPlaceholder: 'Enter mandatory administrative remarks explaining the reason for rejection (min 5 characters)...',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Reject & Return to JPO',
        cancelButtonText: 'Cancel',
        inputValidator: (value) => {
            if (!value || value.trim().length < 5) {
                return 'Mandatory remarks are required (minimum 5 characters).';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/employers/${id}/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ remarks: result.value })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Returned to JPO', data.success, 'info').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.error || 'Failed to reject accreditation.', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Network error occurred.', 'error'));
        }
    });
}
</script>
@endsection