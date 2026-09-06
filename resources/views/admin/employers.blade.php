@extends('layouts.admin')

@section('title', 'Employers Registry')

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
                <div class="h-12 w-12 rounded-2xl bg-slate-100 text-slate-800 flex items-center justify-center text-xl font-black">
                    🏢
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Accredited Partners</span>
                    <p class="text-3xl font-black text-emerald-700">{{ $accreditedEmployers ?? 0 }}</p>
                    <span class="text-[11px] text-emerald-800">Verified & authorized</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center justify-center text-xl font-black">
                    🛡️
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pending Accreditation</span>
                    <p class="text-3xl font-black text-amber-600">{{ $pendingAccreditation ?? 0 }}</p>
                    <span class="text-[11px] text-amber-700">Awaiting authorization</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-800 border border-amber-200 flex items-center justify-center text-xl font-black">
                    ⏳
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
                                        <div class="h-9 w-9 rounded-xl bg-teal-900 text-white font-bold text-xs flex items-center justify-center ring-2 ring-teal-500/30 shrink-0">
                                            🏢
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
                                                    ✓ Account Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-amber-50 text-amber-800 text-[10px] font-bold border border-amber-200">
                                                    ⏳ Account Pending Approval
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
                                                            @click='openDocInspection("{{ addslashes($employer->company_name) }}", @json($docs))'
                                                            class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-emerald-100 hover:text-emerald-900 text-slate-700 text-[10px] font-bold border border-slate-200 transition-colors cursor-pointer"
                                                            title="Click to inspect this document">
                                                        <span>📄 {{ ucfirst(str_replace('_', ' ', $k)) }}</span>
                                                        <span class="text-[9px] text-emerald-600">↗</span>
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
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-bold border bg-emerald-50 text-emerald-800 border-emerald-200">
                                            🛡️ Accredited
                                        </span>
                                    @elseif($isJpoRecommended)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-bold border bg-blue-50 text-blue-800 border-blue-200" title="JPO has evaluated and recommended this employer">
                                            ⭐ JPO Recommended
                                        </span>
                                    @elseif($employer->accreditation_status === 'submitted_to_jpo')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-bold border bg-amber-50 text-amber-800 border-amber-200" title="Awaiting JPO evaluation">
                                            ⏳ Under JPO Review
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-bold border bg-slate-100 text-slate-600 border-slate-200">
                                            ✕ Not Accredited
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
                                        @if(is_array($docs) && count($docs) > 0)
                                            <button type="button" 
                                                    @click='openDocInspection("{{ addslashes($employer->company_name) }}", @json($docs))'
                                                    class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition-colors"
                                                    title="View Legal Documents">
                                                👁️ Docs
                                            </button>
                                        @endif

                                        {{-- 1. Employer Account Approval (Independent from Accreditation) --}}
                                        @if(!$isAccountApproved && !empty($employer->user_id))
                                            <form action="{{ route('admin.users.approve', $employer->user_id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-colors shadow-sm"
                                                        title="Approve Employer Account">
                                                    ✓ Approve Account
                                                </button>
                                            </form>
                                        @endif

                                        {{-- 2. Employer Accreditation (Gated by JPO Recommending Approval) --}}
                                        @if(!$employer->is_accredited)
                                            <button onclick="accreditEmployer({{ $employer->employer_id }}, {{ $isJpoRecommended ? 'true' : 'false' }})" 
                                                    class="px-3 py-1.5 rounded-lg {{ $isJpoRecommended ? 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm' : 'bg-slate-100 text-slate-400 border border-slate-200 hover:bg-slate-200' }} font-bold text-xs transition-colors" 
                                                    title="{{ $isJpoRecommended ? 'Accredit Company' : 'JPO Recommending Approval is required first' }}">
                                                🛡️ Accredit
                                            </button>
                                        @else
                                            <span class="text-[11px] font-bold text-emerald-700">✓ Accredited</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <div class="text-3xl mb-2">🏢</div>
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

    <!-- Reusable Employer Document Viewer Modal -->
    @include('partials.employer-document-viewer-modal')

</div>

<script>
function accreditEmployer(id, isJpoRecommended) {
    if (!isJpoRecommended) {
        Swal.fire({
            title: 'JPO Recommendation Required',
            text: 'Admin cannot accredit an employer without the recommending approval of the JPO. Please wait for the JPO to complete document evaluation.',
            icon: 'warning',
            confirmButtonColor: '#059669',
            confirmButtonText: 'Understood'
        });
        return;
    }

    Swal.fire({
        title: 'Grant Official Employer Accreditation?',
        text: 'JPO has recommended this company. This will officially accredit the employer and authorize applicant referrals.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, accredit company'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/employers/${id}/accredit`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Success!', data.success, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Cannot Accredit', data.error || 'Something went wrong.', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Network error occurred.', 'error'));
        }
    });
}
</script>
@endsection