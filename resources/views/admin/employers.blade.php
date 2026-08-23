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
                            <th class="py-4 px-6">Account Email</th>
                            <th class="py-4 px-6">Legal Verification Documents</th>
                            <th class="py-4 px-6 text-center">Accreditation</th>
                            <th class="py-4 px-6 text-center">Jobs Posted</th>
                            <th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($employers ?? [] as $employer)
                            @php
                                $docs = is_array($employer->documents ?? null) ? $employer->documents : json_decode($employer->documents ?? '[]', true);
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
                                <td class="py-4 px-6 text-slate-600 font-bold">
                                    {{ $employer->email ?? 'N/A' }}
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
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-bold border {{ $employer->is_accredited ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-amber-50 text-amber-800 border-amber-200' }}">
                                        {{ $employer->is_accredited ? '🛡️ Accredited' : '⏳ Pending' }}
                                    </span>
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
                                                    class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition-colors">
                                                👁️ Docs
                                            </button>
                                        @endif

                                        @if(!$employer->is_accredited)
                                            <button onclick="accreditEmployer({{ $employer->employer_id }})" 
                                                    class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-colors shadow-sm" title="Accredit Company">
                                                🛡️ Accredit
                                            </button>
                                        @else
                                            <span class="text-[11px] font-bold text-emerald-700">✓ Verified</span>
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
function accreditEmployer(id) {
    Swal.fire({
        title: 'Grant Employer Accreditation?',
        text: 'This will officially accredit this employer account and enable candidate referrals.',
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
                    Swal.fire('Error', data.error || 'Something went wrong.', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Network error occurred.', 'error'));
        }
    });
}
</script>
@endsection