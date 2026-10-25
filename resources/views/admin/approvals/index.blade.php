@extends('layouts.admin')

@section('title', 'Manage Approvals Center')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8" x-data="{
    tab: 'jobs',
    jobDetailsOpen: false,
    selectedJobDetails: {},
    openJobDetails(job) {
        this.selectedJobDetails = job;
        this.jobDetailsOpen = true;
    },
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
    jsDocModalOpen: false,
    selectedJobseekerName: '',
    selectedJobseekerDocUrl: '',
    selectedJobseekerDocName: '',
    openJsDocInspection(name, url, docName) {
        this.selectedJobseekerName = name;
        this.selectedJobseekerDocUrl = url;
        this.selectedJobseekerDocName = docName || 'Government-Issued Valid ID';
        this.jsDocModalOpen = true;
    },
    openDocInspection(company, docs, accreditationId) {
        this.activeDocCompany = company;
        this.activeDocList = [];
        const documentRouteTemplate = '{{ route('accreditation.documents', ['accreditationId' => '__ACCREDITATION_ID__', 'documentKey' => '__DOCUMENT_KEY__', 'action' => '__ACTION__']) }}';
        const seenPaths = new Set();
        const aliases = {
            sec_dti_registration: 'sec_dti',
            updated_job_vacancies: 'job_vacancies_form',
            business_permit: 'mayors_permit'
        };
        const versionGroups = new Map();
        for (const [key, val] of Object.entries(docs || {})) {
            if (!val || key.includes('_previous_')) continue;
            const canonicalKey = aliases[key] || key;
            const path = typeof val === 'object' && val !== null ? val.path : val;
            if (!versionGroups.has(canonicalKey)) versionGroups.set(canonicalKey, []);
            versionGroups.get(canonicalKey).push({ key, val, path });
        }
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
            for (const candidate of candidates) {
                if (candidate !== current) inferredPreviousKeys.add(candidate.key);
            }
        }
        
        for (let [key, val] of Object.entries(docs || {})) {
            const documentPath = typeof val === 'object' && val !== null ? val.path : val;
            const normalizedPath = typeof documentPath === 'string' ? documentPath.replace(/\\/g, '/') : '';
            if (normalizedPath && seenPaths.has(normalizedPath)) continue;
            if (normalizedPath) seenPaths.add(normalizedPath);

            let label = 'Attached Document';
            let icon = 'document';
            let issuer = 'Regulatory Authority';
            let filename = '';

            if (typeof val === 'object' && val !== null) {
                filename = val.original_name || val.path || '';
            } else if (typeof val === 'string') {
                filename = val.split('/').pop() || val;
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

            const extensionSource = typeof val === 'object' && val !== null ? (val.path || filename) : val;
            const extension = String(extensionSource || '').split('.').pop().toLowerCase();
            const isImage = ['jpg', 'jpeg', 'png'].includes(extension);
            const fileRoute = documentRouteTemplate
                .replace('__ACCREDITATION_ID__', encodeURIComponent(accreditationId))
                .replace('__DOCUMENT_KEY__', encodeURIComponent(key));
            const revisionStatus = (typeof val === 'object' && val !== null && val.revision_status)
                || (key.includes('_previous_') || inferredPreviousKeys.has(key) ? 'previous' : '');
            const status = revisionStatus === 'new'
                ? 'New upload'
                : (revisionStatus === 'previous' ? 'Previous version' : 'Verified Valid');

            this.activeDocList.push({
                key: key,
                label: label,
                icon: icon,
                issuer: issuer,
                filename: filename,
                isImage: isImage,
                previewUrl: isImage ? fileRoute.replace('__ACTION__', 'preview') : '',
                downloadUrl: fileRoute.replace('__ACTION__', 'download'),
                status: status,
                revisionStatus: revisionStatus,
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
    <div class="w-full max-w-[1680px] mx-auto space-y-8">

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
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span>Job Postings</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black" 
                      :class="tab === 'jobs' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700'">
                    {{ $pendingJobs->count() }}
                </span>
            </button>

            <button @click="tab = 'accreditations'" 
                    :class="tab === 'accreditations' ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-300 shadow-sm font-black' : 'text-slate-600 hover:text-slate-900 font-bold hover:bg-slate-50'"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Accreditations</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black" 
                      :class="tab === 'accreditations' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700'">
                    {{ $pendingAccreditations->count() }}
                </span>
            </button>

            <button @click="tab = 'reports'" 
                    :class="tab === 'reports' ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-300 shadow-sm font-black' : 'text-slate-600 hover:text-slate-900 font-bold hover:bg-slate-50'"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Placement Reports</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black" 
                      :class="tab === 'reports' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700'">
                    {{ $pendingPlacementReports->count() }}
                </span>
            </button>

            <button @click="tab = 'trainers'" 
                    :class="tab === 'trainers' ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-300 shadow-sm font-black' : 'text-slate-600 hover:text-slate-900 font-bold hover:bg-slate-50'"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Collaborator Trainers</span>
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
                                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                Accredited
                                            </span>
                                        @elseif(($job->employer_accreditation_state ?? '') === 'pending')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-amber-50 text-amber-800 text-[10px] font-bold border border-amber-200" title="Accreditation application in progress">
                                                <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Pending
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-slate-100 text-slate-600 text-[10px] font-bold border border-slate-200" title="Not accredited">
                                                <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                Not Accredited
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
                                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                {{ $job->disability_type ?: 'PWD Inclusive' }}
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
                                                <button type="button"
                                                    data-details="{{ json_encode([
                                                        "id" => $job->job_id,
                                                        "title" => $job->title,
                                                        "company" => $job->employer->company_name ?? "DMDP Portal",
                                                        "email" => $job->employer->user->email ?? "N/A",
                                                        "status" => ucfirst($job->status),
                                                        "employer_accreditation" => $job->employer_accreditation_badge ?? "Not Accredited",
                                                        "job_type" => $job->job_type,
                                                        "location" => $job->location,
                                                        "salary" => $job->salary_compensation,
                                                        "vacancies" => $job->vacancy_count,
                                                        "valid_until" => $job->valid_until ? date("M d, Y", strtotime($job->valid_until)) : "Continuous",
                                                        "description" => $job->description,
                                                        "qualifications" => array_values(array_filter(array_map("trim", preg_split("/\\r\\n|\\r|\\n/", $job->qualifications ?? "")))),
                                                        "benefits" => array_values(array_filter(array_map("trim", preg_split("/\\r\\n|\\r|\\n/", $job->benefits_perks ?? "")))),
                                                        "instructions" => $job->other_instructions,
                                                        "pwd" => (bool) $job->accepts_disability,
                                                        "disability_type" => $job->disability_type,
                                                        "applications" => $job->applications_count ?? 0,
                                                        "submitted" => $job->created_at ? date("M d, Y", strtotime($job->created_at)) : "N/A"
                                                    ]) }}"
                                                    @click="openJobDetails(JSON.parse($el.dataset.details))"
                                                    class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition-colors">
                                                View Details
                                            </button>
                                            <form action="{{ route('admin.approvals.job-postings.approve', $job->job_id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-colors shadow-sm">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Approve
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
                                        <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
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

        <!-- Job Details Modal -->
        <div x-show="jobDetailsOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="jobDetailsOpen = false" class="bg-white rounded-3xl max-w-4xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
                <template x-if="selectedJobDetails && Object.keys(selectedJobDetails).length">
                    <div class="space-y-6">
                        <div class="flex items-start justify-between border-b border-slate-100 pb-4 gap-4">
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700">Job Posting Inspection</span>
                                <h3 class="text-2xl font-black text-slate-900 mt-1" x-text="selectedJobDetails.title || 'Job Posting'">Job Posting</h3>
                                <p class="text-xs text-slate-500 mt-1">
                                    Employer: <span x-text="selectedJobDetails.company || 'DMDP Portal'"></span>
                                    &bull; Listing #<span x-text="selectedJobDetails.id || 'N/A'"></span>
                                </p>
                            </div>
                            <button type="button" @click="jobDetailsOpen = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold leading-none">&times;</button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                                <div class="text-[10px] font-bold uppercase text-slate-400">Employer</div>
                                <div class="mt-1 font-black text-slate-900" x-text="selectedJobDetails.company || 'DMDP Portal'"></div>
                                <div class="text-[11px] text-slate-500" x-text="selectedJobDetails.email || 'N/A'"></div>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                                <div class="text-[10px] font-bold uppercase text-slate-400">Role / Type</div>
                                <div class="mt-1 font-black text-slate-900" x-text="selectedJobDetails.job_type || 'Full-time'"></div>
                                <div class="text-[11px] text-slate-500" x-text="selectedJobDetails.location || 'Cebu City'"></div>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                                <div class="text-[10px] font-bold uppercase text-slate-400">Compensation</div>
                                <div class="mt-1 font-black text-emerald-700" x-text="selectedJobDetails.salary || 'Competitive / Negotiable'"></div>
                                <div class="text-[11px] text-slate-500">Vacancies: <span x-text="selectedJobDetails.vacancies || '0'"></span></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-xs">
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                                <div class="text-[10px] font-bold uppercase text-slate-400">Description</div>
                                <p class="mt-2 text-slate-700 whitespace-pre-line leading-relaxed" x-text="selectedJobDetails.description || 'No description provided.'"></p>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                                <div class="text-[10px] font-bold uppercase text-slate-400">Application Details</div>
                                <div class="mt-2 space-y-2 text-slate-700">
                                    <div><span class="font-bold text-slate-800">Status:</span> <span x-text="selectedJobDetails.status || 'Pending'"></span></div>
                                    <div><span class="font-bold text-slate-800">Valid Until:</span> <span x-text="selectedJobDetails.valid_until || 'Open Continuous'"></span></div>
                                    <div><span class="font-bold text-slate-800">PWD Inclusive:</span> <span x-text="selectedJobDetails.pwd ? 'Yes' : 'No'"></span></div>
                                    <div x-show="selectedJobDetails.disability_type"><span class="font-bold text-slate-800">Disability Type:</span> <span x-text="selectedJobDetails.disability_type"></span></div>
                                    <div><span class="font-bold text-slate-800">Submitted:</span> <span x-text="selectedJobDetails.submitted || 'N/A'"></span></div>
                                    <div><span class="font-bold text-slate-800">Applications:</span> <span x-text="selectedJobDetails.applications || 0"></span></div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-xs">
                            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50" x-show="selectedJobDetails.qualifications && selectedJobDetails.qualifications.length">
                                <div class="text-[10px] font-bold uppercase text-slate-400">Qualifications</div>
                                <ul class="mt-2 list-disc list-inside space-y-1 text-slate-700">
                                    <template x-for="(item, index) in (selectedJobDetails.qualifications || [])" :key="index">
                                        <li x-text="item"></li>
                                    </template>
                                </ul>
                            </div>
                            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50" x-show="selectedJobDetails.benefits && selectedJobDetails.benefits.length">
                                <div class="text-[10px] font-bold uppercase text-slate-400">Benefits & Perks</div>
                                <ul class="mt-2 list-disc list-inside space-y-1 text-slate-700">
                                    <template x-for="(item, index) in (selectedJobDetails.benefits || [])" :key="index">
                                        <li x-text="item"></li>
                                    </template>
                                </ul>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50" x-show="selectedJobDetails.instructions">
                            <div class="text-[10px] font-bold uppercase text-slate-400">Application Instructions</div>
                            <p class="mt-2 text-slate-700 whitespace-pre-line leading-relaxed" x-text="selectedJobDetails.instructions"></p>
                        </div>

                        <div class="flex justify-end pt-2 border-t border-slate-100">
                            <button type="button" @click="jobDetailsOpen = false" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                                Close
                            </button>
                        </div>
                    </div>
                </template>
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
                                                    @foreach($displayDocs as $k => $d)
                                                        <button type="button" 
                                                                @click='openDocInspection("{{ addslashes($acc->company_name) }}", @json($docs), {{ $acc->accreditation_id }})'
                                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-emerald-100 hover:text-emerald-900 text-slate-700 text-[10px] font-bold border border-slate-200 transition-colors cursor-pointer"
                                                                title="Click to inspect this document">
                                                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                            <span>{{ ucfirst(str_replace('_', ' ', $k)) }}</span>
                                                            <span class="text-[9px] text-emerald-600">&rarr;</span>
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
                                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Complete
                                            </span>
                                        @elseif($docStatus === 'incomplete')
                                            <div class="space-y-1">
                                                <span class="inline-flex items-center gap-1 rounded-xl bg-rose-50 text-rose-800 px-2.5 py-1 text-[11px] font-extrabold border border-rose-200">
                                                    <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    Incomplete
                                                </span>
                                                @if($acc->document_incomplete_reason)
                                                    <p class="text-[10px] text-rose-700 italic max-w-xs leading-tight">
                                                        {{ $acc->document_incomplete_reason }}
                                                    </p>
                                                @endif
                                            </div>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-xl bg-amber-50 text-amber-800 px-2.5 py-1 text-[11px] font-extrabold border border-amber-200">
                                                <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Pending JPO Review
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6">
                                        @if(in_array($acc->status, ['supervisor_approved', 'jpo_approved']))
                                            <span class="inline-flex items-center gap-1 rounded-xl bg-blue-50 text-blue-800 px-2.5 py-1 font-bold border border-blue-200">
                                                <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                JPO Recommended
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-xl bg-amber-50 text-amber-800 px-2.5 py-1 font-bold border border-amber-200">
                                                <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                In Review
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
                                                <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                <span>Form</span>
                                                <span class="text-[9px] text-slate-400">&rarr;</span>
                                            </a>

                                            @if(is_array($docs) && count($docs) > 0)
                                                <button type="button" 
                                                        @click='openDocInspection("{{ addslashes($acc->company_name) }}", @json($docs), {{ $acc->accreditation_id }})'
                                                        class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition-colors">
                                                    <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    View Docs
                                                </button>
                                            @endif

                                            @if($isDocsComplete && in_array($acc->status, ['jpo_approved', 'supervisor_approved']))
                                                <form action="{{ route('admin.approvals.accreditations.approve', $acc->accreditation_id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center gap-1 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-colors shadow-sm cursor-pointer">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                        Officially Accredit
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" disabled 
                                                        class="inline-flex items-center gap-1 px-3.5 py-2 rounded-xl bg-slate-100 text-slate-400 font-bold text-xs border border-dashed border-slate-300 cursor-not-allowed"
                                                        title="{{ !$isDocsComplete ? 'Cannot accredit: Required employer documents are incomplete or unverified' : 'Cannot accredit: Awaiting JPO recommending approval first' }}">
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    {{ !$isDocsComplete ? 'Documents Incomplete' : 'Awaiting JPO' }}
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
                                        <div class="h-12 w-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        </div>
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
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            </a>

                                            <form action="{{ route('admin.approvals.placement-reports.approve', $rep->report_id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-colors shadow-sm">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Authorize
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
                                        <div class="h-12 w-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                        </div>
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
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    <span>View Official Printable Report &rarr;</span>
                                </a>

                                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                                    <form :action="selectedReport.approve_url" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Authorize & Archive
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
                                                <button type="submit" class="inline-flex items-center gap-1 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-sm">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Approve Trainer
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
                                        <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
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

        <!-- =================================================================== -->
        <!-- PILLAR 5: JOBSEEKER IDENTITY & VERIFICATION APPROVALS -->
        <!-- =================================================================== -->
        <div x-show="tab === 'jobseekers'" class="space-y-4" style="display: none;">
            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Candidate Verification Hub</span>
                        <h2 class="text-xl font-black text-slate-900">Jobseeker Accounts & Government ID Approvals</h2>
                        <p class="text-xs text-slate-500">
                            Review candidate identities, inspect uploaded Government Valid IDs, and authorize jobseekers for active job application submissions.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="px-3.5 py-1.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-black">
                            {{ $pendingJobseekers->count() }} Awaiting Verification
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-black uppercase tracking-wider text-slate-500">
                                <th class="py-4 px-6">Candidate Details</th>
                                <th class="py-4 px-6">Account Status</th>
                                <th class="py-4 px-6">Government ID Submission</th>
                                <th class="py-4 px-6">Optional Vault Records</th>
                                <th class="py-4 px-6 text-right">Verification Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse($pendingJobseekers as $js)
                                @php
                                    $validIdDoc = $js->valid_id_doc;
                                    $validIdUrl = $validIdDoc ? ($validIdDoc['file_url'] ?? ($validIdDoc['url'] ?? (isset($validIdDoc['path']) ? '/storage/' . $validIdDoc['path'] : ''))) : '';
                                    $validIdName = $validIdDoc['name'] ?? ($validIdDoc['original_name'] ?? 'Government-Issued Valid ID');
                                    $validIdDate = isset($validIdDoc['uploaded_at']) ? date('M d, Y', strtotime($validIdDoc['uploaded_at'])) : null;
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-2xl bg-gradient-to-tr from-emerald-600 to-emerald-400 flex items-center justify-center text-white font-black text-sm shrink-0 shadow-xs">
                                                {{ strtoupper(substr($js->first_name ?? 'J', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-black text-slate-900 text-sm">
                                                    {{ $js->first_name }} {{ $js->last_name }}
                                                </div>
                                                <div class="text-[11px] text-slate-500 font-medium">
                                                    {{ $js->user_email ?: $js->email }} &bull; {{ $js->mobile_number ?: 'No phone provided' }}
                                                </div>
                                                <div class="text-[10px] text-slate-400 mt-0.5">
                                                    Registered: {{ date('M d, Y', strtotime($js->registered_at ?: $js->created_at)) }}
                                                    @if(!empty($js->is_pwd))
                                                        &bull; <span class="text-indigo-600 font-bold">PWD: {{ $js->pwd_type ?: 'Registered' }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-4 px-6">
                                        @if(($js->account_status ?? 'unverified') === 'pending_review')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                                Pending Review
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                                Unverified
                                            </span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-6">
                                        @if($js->has_valid_id && $validIdUrl)
                                            <div class="space-y-1.5">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-emerald-100 text-emerald-800 text-[10px] font-bold border border-emerald-200">
                                                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                        ID Attached
                                                    </span>
                                                    <span class="text-[11px] font-bold text-slate-800 truncate max-w-[150px]" title="{{ $validIdName }}">
                                                        {{ $validIdName }}
                                                    </span>
                                                </div>
                                                @if($validIdDate)
                                                    <p class="text-[10px] text-slate-400">Uploaded on {{ $validIdDate }}</p>
                                                @endif
                                                <div>
                                                    <button type="button" 
                                                            @click="openJsDocInspection('{{ addslashes($js->first_name . ' ' . $js->last_name) }}', '{{ $validIdUrl }}', '{{ addslashes($validIdName) }}')"
                                                            class="inline-flex items-center gap-1 text-[11px] font-extrabold text-emerald-700 hover:text-emerald-900 hover:underline">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        <span>Inspect ID Document &rarr;</span>
                                                    </button>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-1.5 text-slate-400">
                                                <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                <span class="text-[11px] font-semibold text-slate-500">No Government ID Uploaded</span>
                                            </div>
                                        @endif
                                    </td>

                                    <td class="py-4 px-6">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            @if($js->has_resume)
                                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px] font-semibold border border-slate-200">
                                                    Resume Attached
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-md bg-slate-50 text-slate-400 text-[10px]">
                                                    No Resume
                                                </span>
                                            @endif

                                            @if(!empty($js->parsed_docs['certificate']))
                                                <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[10px] font-semibold border border-emerald-200">
                                                    Certificates Attached
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ route('admin.approvals.jobseekers.approve', $js->jobseeker_id) }}" method="POST" onsubmit="return confirm('Approve and verify {{ addslashes($js->first_name . ' ' . $js->last_name) }}? This will unlock job applications for this candidate.');" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1 px-3.5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-extrabold text-xs shadow-sm transition-all hover:scale-105">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Approve Jobseeker</span>
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.approvals.jobseekers.reject', $js->jobseeker_id) }}" method="POST" onsubmit="const r = prompt('Specify rejection or revision reason for {{ addslashes($js->first_name . ' ' . $js->last_name) }}:', 'Please upload a clearer copy of your Government-Issued Valid ID.'); if(!r) return false; this.remarks.value = r; return true;" class="inline">
                                                @csrf
                                                <input type="hidden" name="remarks" value="">
                                                <button type="submit" class="px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs transition-colors">
                                                    Return
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400">
                                        <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <div class="font-bold text-slate-700">All registered jobseekers have been verified!</div>
                                        <div class="text-xs text-slate-400 mt-1">No pending candidate identities or Government IDs awaiting authorization.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Jobseeker Document Inspection Modal -->
        <div x-show="jsDocModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 sm:p-6"
             @keydown.escape.window="jsDocModalOpen = false">
            <div @click.away="jsDocModalOpen = false" 
                 class="bg-white rounded-3xl max-w-3xl w-full shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh]">
                <div class="bg-slate-900 p-5 text-white flex items-center justify-between border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-bold">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15A2.25 2.25 0 002.25 6.75v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-semibold text-emerald-400 uppercase tracking-wider block">Jobseeker Identity Verification</span>
                            <h3 class="text-base font-bold text-white leading-tight" x-text="selectedJobseekerName"></h3>
                        </div>
                    </div>
                    <button @click="jsDocModalOpen = false" type="button" class="text-slate-400 hover:text-white text-2xl font-bold leading-none">&times;</button>
                </div>
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between text-xs">
                    <span class="font-bold text-slate-700" x-text="selectedJobseekerDocName || 'Government-Issued Valid ID'"></span>
                    <a :href="selectedJobseekerDocUrl" target="_blank" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold inline-flex items-center gap-1.5 transition-colors">
                        <span>Open in New Tab</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
                <div class="p-6 overflow-y-auto flex-1 flex items-center justify-center bg-slate-100 min-h-[350px]">
                    <template x-if="selectedJobseekerDocUrl && (selectedJobseekerDocUrl.endsWith('.jpg') || selectedJobseekerDocUrl.endsWith('.jpeg') || selectedJobseekerDocUrl.endsWith('.png') || selectedJobseekerDocUrl.includes('image'))">
                        <img :src="selectedJobseekerDocUrl" alt="Government ID" class="max-h-[500px] max-w-full rounded-2xl object-contain shadow-md border border-slate-200">
                    </template>
                    <template x-if="selectedJobseekerDocUrl && selectedJobseekerDocUrl.endsWith('.pdf')">
                        <iframe :src="selectedJobseekerDocUrl" class="w-full h-[500px] rounded-2xl border border-slate-200"></iframe>
                    </template>
                    <template x-if="!selectedJobseekerDocUrl || (!selectedJobseekerDocUrl.endsWith('.pdf') && !selectedJobseekerDocUrl.endsWith('.jpg') && !selectedJobseekerDocUrl.endsWith('.jpeg') && !selectedJobseekerDocUrl.endsWith('.png'))">
                        <div class="text-center p-8 space-y-3">
                            <svg class="w-12 h-12 text-slate-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-sm font-bold text-slate-700" x-text="selectedJobseekerDocName"></p>
                            <a :href="selectedJobseekerDocUrl" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700">
                                Download / View Document
                            </a>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Reusable Employer Document Viewer Modal -->
        @include('partials.employer-document-viewer-modal')

    </div>
</div>
@endsection
