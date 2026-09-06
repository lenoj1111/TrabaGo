@extends('layouts.supervisor')

@section('title', 'Review Accreditation Papers - PESD Supervisor')

@section('content')
<div x-data="{
    endorseModal: false,
    selectedAccId: null,
    selectedCompany: '',
    selectedJpoNotes: '',
    docModalOpen: false,
    activeDocCompany: '',
    activeDocList: [],
    selectedDocKey: '',
    currentDoc: {},
    openEndorse(id, company, jpoNotes) {
        this.selectedAccId = id;
        this.selectedCompany = company;
        this.selectedJpoNotes = jpoNotes;
        this.endorseModal = true;
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
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Stage 3: PESD Supervisor Endorsement
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Review Accreditation Papers</h1>
                <p class="text-sm text-slate-300">Evaluate accreditation documents verified and forwarded by Job Placement Officers. Endorse qualified employers to the Admin for final processing and official accreditation.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('supervisor.accreditations') }}" 
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ !request('filter') ? 'bg-emerald-600 text-white' : 'bg-white/10 text-slate-300 hover:bg-white/20' }}">
                    All Records
                </a>
                <a href="{{ route('supervisor.accreditations', ['filter' => 'pending']) }}" 
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ request('filter') === 'pending' ? 'bg-emerald-600 text-white' : 'bg-white/10 text-slate-300 hover:bg-white/20' }}">
                    Pending Supervisor
                </a>
                <a href="{{ route('supervisor.accreditations', ['filter' => 'endorsed']) }}" 
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ request('filter') === 'endorsed' ? 'bg-emerald-600 text-white' : 'bg-white/10 text-slate-300 hover:bg-white/20' }}">
                    Endorsed to Admin
                </a>
            </div>
        </div>

        <!-- Accreditations Table -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h2 class="text-lg font-black text-slate-900">Employer Accreditation Reviews</h2>
                <span class="text-xs font-bold text-slate-500">Figure 11: Endorse & Send to Admin</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-400 uppercase tracking-wider font-bold">
                            <th class="pb-3 px-3">Company Name</th>
                            <th class="pb-3 px-3">Legal Verification Documents</th>
                            <th class="pb-3 px-3">JPO Evaluation Remarks</th>
                            <th class="pb-3 px-3">Status</th>
                            <th class="pb-3 px-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($accreditations as $acc)
                            @php
                                $docs = is_array($acc->documents) ? $acc->documents : json_decode($acc->documents ?? '[]', true);
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-3 font-bold text-slate-900">
                                    <span class="text-sm block">{{ $acc->company_name }}</span>
                                    <span class="text-[11px] text-slate-400">Employer ID #{{ $acc->employer_id }}</span>
                                </td>
                                <td class="py-4 px-3">
                                    <div class="space-y-1.5">
                                        <div class="flex flex-wrap gap-1">
                                            @if(is_array($docs) && count($docs) > 0)
                                                @foreach($docs as $key => $doc)
                                                    <button type="button" 
                                                            @click='openDocInspection("{{ addslashes($acc->company_name) }}", @json($docs))'
                                                            class="inline-flex items-center gap-1 rounded-xl bg-slate-100 hover:bg-emerald-100 hover:text-emerald-900 border border-slate-200 px-2.5 py-1 text-[10px] font-bold text-slate-700 transition-colors cursor-pointer"
                                                            title="Click to inspect this document">
                                                        <span>📄</span>
                                                        <span>{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                                                        <span class="text-[9px] text-emerald-600">↗</span>
                                                    </button>
                                                @endforeach
                                            @else
                                                <span class="text-slate-400">No documents attached</span>
                                            @endif
                                        </div>
                                        @if(is_array($docs) && count($docs) > 0)
                                            <button type="button" 
                                                    @click='openDocInspection("{{ addslashes($acc->company_name) }}", @json($docs))'
                                                    class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 hover:text-emerald-900 transition-colors">
                                                <span>👁️</span> Inspect Document Hub ({{ count($docs) }} Files)
                                            </button>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-3 text-slate-600 text-[11px] max-w-xs">
                                    {{ $acc->jpo_remarks ?: 'Recommended by Job Placement Officer.' }}
                                </td>
                                <td class="py-4 px-3">
                                    @if($acc->status === 'admin_approved')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 border border-emerald-300 px-2.5 py-0.5 text-xs font-bold text-emerald-800">
                                            ✓ Officially Accredited by Admin
                                        </span>
                                    @elseif($acc->status === 'supervisor_approved')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-teal-100 border border-teal-300 px-2.5 py-0.5 text-xs font-bold text-teal-800">
                                            🏛️ Endorsed & Sent to Admin
                                        </span>
                                    @elseif($acc->status === 'jpo_approved')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 border border-blue-300 px-2.5 py-0.5 text-xs font-bold text-blue-800">
                                            📋 Awaiting Supervisor Action
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                                            {{ ucfirst($acc->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('jpo.evaluations.accreditations.print', $acc->accreditation_id) }}" target="_blank"
                                           class="px-2.5 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200 transition-colors flex items-center gap-1"
                                           title="Inspect Official Form (PDF)">
                                            <span>📑 PDF</span>
                                            <span class="text-[10px] text-slate-400">↗</span>
                                        </a>

                                        @if(is_array($docs) && count($docs) > 0)
                                            <button type="button" 
                                                    @click='openDocInspection("{{ addslashes($acc->company_name) }}", @json($docs))'
                                                    class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition-colors">
                                                👁️ View Docs
                                            </button>
                                        @endif

                                        @if($acc->status === 'jpo_approved')
                                            <button type="button" 
                                                    @click="openEndorse({{ $acc->accreditation_id }}, '{{ addslashes($acc->company_name) }}', '{{ addslashes($acc->jpo_remarks ?? '') }}')"
                                                    class="px-4 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md shadow-emerald-600/20">
                                                Endorse &rarr;
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400 italic">
                                    No accreditation papers found matching this filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-4 border-t border-slate-100">
                {{ $accreditations->links() }}
            </div>
        </div>

    </div>

    <!-- Supervisor Endorse Modal -->
    <div x-show="endorseModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="endorseModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">PESD Supervisor Endorsement</span>
                    <h3 class="text-xl font-black text-slate-900 mt-0.5">Endorse <span x-text="selectedCompany"></span></h3>
                </div>
                <button @click="endorseModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <form :action="'/supervisor/accreditations/' + selectedAccId + '/approve'" method="POST" class="space-y-4">
                @csrf

                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs space-y-1">
                    <p class="font-bold text-emerald-950">Company: <span x-text="selectedCompany" class="text-slate-900 font-extrabold"></span></p>
                    <p class="text-emerald-800">JPO Notes: <span x-text="selectedJpoNotes || 'Verified legal credentials.'" class="font-semibold italic"></span></p>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Supervisor Decision *</label>
                    <select name="action" required
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none">
                        <option value="approve">✓ Endorse & Forward to Admin for Final Accreditation</option>
                        <option value="reject">✕ Return to JPO / Employer for Corrections</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Supervisor Endorsement Remarks</label>
                    <textarea name="remarks" rows="3" placeholder="Endorsement justification and compliance remarks for the DMDP Administrator..."
                              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="endorseModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-lg shadow-emerald-600/30">
                        Endorse & Send to Admin &rarr;
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Reusable Employer Document Viewer Modal -->
    @include('partials.employer-document-viewer-modal')

</div>
@endsection

