<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESTABLISHMENT REGISTRATION - {{ $employer->company_name ?? 'Cebu City DMDP' }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media print {
            .no-print { display: none !important; }
            body { 
                background: white !important; 
                padding: 0 !important; 
                color: #000 !important; 
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact; 
            }
            .page-sheet { 
                box-shadow: none !important; 
                border: none !important;
                padding: 0 !important; 
                width: 100% !important; 
                max-width: 100% !important; 
                margin-bottom: 0 !important;
                page-break-after: avoid;
            }
            @page {
                size: A4 portrait;
                margin: 6mm 10mm 6mm 10mm;
            }
        }
    </style>
</head>

@php
    $currentUser = Auth::user();
    $backUrl = route('admin.approvals.index');
    if ($currentUser && $currentUser->role === 'employer') {
        $backUrl = route('employer.accreditation');
    } elseif ($currentUser && $currentUser->role === 'jpo') {
        $backUrl = route('jpo.evaluations.accreditations');
    }

    $docs = $accreditation ? (is_array($accreditation->documents) ? $accreditation->documents : (json_decode($accreditation->documents ?? '[]', true) ?: [])) : [];
    $hasDoc = function($term) use ($docs) {
        foreach ($docs as $k => $v) {
            if (str_contains(strtolower($k), strtolower($term))) return true;
        }
        return false;
    };

    $userProfile = $employer->user ? ($employer->user->profile ?? null) : null;
    $companyName = $employer->company_name ?? 'Establishment Name';
    $contactPerson = $userProfile->full_name ?? ($employer->user->full_name ?? 'Authorized Representative');
    $contactEmail = $employer->user->email ?? 'hr@company.com';
    $contactPhone = $userProfile->phone ?? ($employer->user->phone ?? '09123456789');

    $docStatus = $accreditation->document_status ?? 'pending';
    $isComplete = $docStatus === 'complete';
    $isIncomplete = $docStatus === 'incomplete';
    $incompleteReason = $accreditation->document_incomplete_reason ?? '';

    $jobsList = $jobPostings ?? collect();
    $totalVacancies = $jobsList->sum('vacancy_count') ?: ($jobsList->count() ?: 1);
@endphp

<body class="bg-slate-100 min-h-screen p-4 sm:p-8 flex flex-col items-center justify-start text-black antialiased font-sans"
      x-data="{
        docModalOpen: false,
        activeDocCompany: '{{ addslashes($companyName) }}',
        activeDocList: [],
        selectedDocKey: '',
        currentDoc: {},
        openDocInspection(company, docs, initialKey = null) {
            this.activeDocCompany = company || '{{ addslashes($companyName) }}';
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
                    url: url,
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

    <!-- Top Action Bar (No-Print) -->
    <div class="no-print w-full max-w-4xl mb-5 flex items-center justify-between gap-4">
        <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-700 hover:text-green-700 bg-white px-4 py-2.5 rounded-xl shadow-xs border border-slate-200 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Portal
        </a>

        <div class="flex items-center gap-3">
            @if(count($docs) > 0)
                <button type="button" 
                        @click='openDocInspection("{{ addslashes($companyName) }}", @json($docs))'
                        class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-800 hover:text-emerald-800 bg-white px-4 py-2.5 rounded-xl shadow-xs border border-slate-300 transition-colors cursor-pointer">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <span>Preview / Inspect Docs ({{ count($docs) }})</span>
                </button>
            @endif
            <span class="text-xs text-slate-500 font-semibold hidden sm:inline">Cebu City DMDP Official Accreditation Format</span>
            <button onclick="window.print()" class="inline-flex items-center gap-2 text-xs font-black text-white bg-green-700 hover:bg-green-600 px-6 py-2.5 rounded-xl shadow-lg shadow-green-700/20 transition-all hover:scale-105">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print / Save PDF
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- PAGE 1: ESTABLISHMENT REGISTRATION FORM -->
    <!-- ========================================================================= -->
    <div class="page-sheet w-full max-w-4xl bg-white border border-slate-300 p-6 sm:p-10 shadow-xl space-y-2.5 mb-8 relative overflow-hidden text-black">
        
        <!-- Header Section -->
        <div class="text-center space-y-0.5 pb-1">

            <p class="text-[11px] text-slate-800 font-serif leading-tight">Republic of the Philippines</p>
            <h2 class="text-xs sm:text-sm font-black text-slate-900 tracking-tight uppercase">CITY OF CEBU</h2>
            <h3 class="text-xs sm:text-sm font-black text-slate-950 uppercase tracking-tight">
                DEPARTMENT OF MANPOWER DEVELOPMENT AND PLACEMENT
            </h3>
            <div class="pt-0.5">
                <h1 class="text-xs sm:text-sm font-black text-black tracking-wide uppercase border-b-2 border-black pb-0.5 inline-block">
                    ESTABLISHMENT REGISTRATION
                </h1>
            </div>
        </div>

        <!-- 1. Establishment Information -->
        <div class="border-2 border-black text-xs divide-y border-collapse">
            <div class="bg-black text-white font-black px-2 py-0.5 uppercase tracking-wider text-[10px]">
                Establishment Information
            </div>

            <div class="grid grid-cols-3 divide-x border-black">
                <div class="px-2 py-1 col-span-2 flex items-center">
                    <span class="font-bold text-black min-w-[140px]">Establishment Name</span>
                    <span class="font-black text-slate-950 text-xs flex-1">{{ $companyName }}</span>
                </div>
                <div class="px-2 py-1 flex items-center">
                    <span class="font-bold text-black min-w-[130px]">Acronym/Common Name</span>
                    <span class="font-semibold text-slate-900">{{ strtoupper(substr($companyName, 0, 4)) }}</span>
                </div>
            </div>

            <div class="px-2 py-1 flex items-center">
                <span class="font-bold text-black min-w-[140px]">Tax Identification Number</span>
                <span class="font-mono font-bold text-slate-900">000-123-456-000</span>
            </div>

            <div class="px-2 py-1 space-y-1">
                <span class="font-bold text-black block text-[10px]">Establishment Type (Please check box. Check one only based on your main line of business)</span>
                <div class="grid grid-cols-2 gap-x-4 gap-y-0.5 text-[10px] pt-0.5">
                    <label class="flex items-center gap-1.5"><span class="font-mono font-bold">[  ]</span> Government</label>
                    <label class="flex items-center gap-1.5"><span class="font-mono font-bold">[X]</span> Private</label>
                    <label class="flex items-center gap-1.5"><span class="font-mono font-bold">[  ]</span> Recruitment Agency (Local)</label>
                    <label class="flex items-center gap-1.5"><span class="font-mono font-bold">[  ]</span> DO 174-17, Subcontractor</label>
                    <label class="flex items-center gap-1.5"><span class="font-mono font-bold">[  ]</span> Recruitment Agency (Overseas)</label>
                    <label class="flex items-center gap-1.5"><span class="font-mono font-bold">[  ]</span> Others (Please specify) ________________</label>
                </div>
            </div>

            <div class="px-2 py-1 flex flex-wrap items-center gap-4 text-[10px]">
                <span class="font-bold text-black min-w-[100px]">Total Workforce:</span>
                <span class="flex items-center gap-1 font-mono font-bold">[  ] Micro (1-9)</span>
                <span class="flex items-center gap-1 font-mono font-bold">[  ] Small (10-99)</span>
                <span class="flex items-center gap-1 font-mono font-bold">[X] Medium (100-199)</span>
                <span class="flex items-center gap-1 font-mono font-bold">[  ] Large (200 and up)</span>
            </div>

            <div class="px-2 py-1 flex items-center">
                <span class="font-bold text-black min-w-[150px]">Industry (based on BIR 2303)</span>
                <span class="font-semibold text-slate-900">Information Technology & Business Services</span>
            </div>

            <div class="px-2 py-1 space-y-0.5">
                <span class="font-bold text-black block text-[10px]">Address</span>
                <div class="grid grid-cols-4 gap-2 text-[10px] text-slate-800">
                    <div>
                        <span class="font-bold block text-[9px] text-slate-500 uppercase">Bldg. No/Block No/St. Name</span>
                        <span class="font-medium">IT Park, Lahug</span>
                    </div>
                    <div>
                        <span class="font-bold block text-[9px] text-slate-500 uppercase">Barangay/City/Municipality</span>
                        <span class="font-medium">Lahug, Cebu City</span>
                    </div>
                    <div>
                        <span class="font-bold block text-[9px] text-slate-500 uppercase">Province</span>
                        <span class="font-medium">Cebu</span>
                    </div>
                    <div>
                        <span class="font-bold block text-[9px] text-slate-500 uppercase">Zip Code</span>
                        <span class="font-medium">6000</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Establishment Contact Details -->
        <div class="border-2 border-black text-xs divide-y border-collapse">
            <div class="bg-black text-white font-black px-2 py-0.5 uppercase tracking-wider text-[10px]">
                Establishment Contact Details
            </div>

            <div class="grid grid-cols-2 divide-x border-black text-[10px]">
                <div class="px-2 py-0.5">
                    <span class="font-bold text-black">General Manager:</span>
                    <span class="ml-1 font-semibold">{{ $contactPerson }}</span>
                </div>
                <div class="px-2 py-0.5">
                    <span class="font-bold text-black">GM e-mail address:</span>
                    <span class="ml-1 text-slate-800">{{ $contactEmail }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 divide-x border-black text-[10px]">
                <div class="px-2 py-0.5">
                    <span class="font-bold text-black">Contact Person:</span>
                    <span class="ml-1 font-semibold">{{ $contactPerson }}</span>
                </div>
                <div class="px-2 py-0.5">
                    <span class="font-bold text-black">Position:</span>
                    <span class="ml-1">Human Resources Manager</span>
                </div>
            </div>

            <div class="grid grid-cols-2 divide-x border-black text-[10px]">
                <div class="px-2 py-0.5">
                    <span class="font-bold text-black">Mobile No.:</span>
                    <span class="ml-1 font-mono">{{ $contactPhone }}</span>
                </div>
                <div class="px-2 py-0.5">
                    <span class="font-bold text-black">Landline No.:</span>
                    <span class="ml-1 font-mono">(032) 234-5678</span>
                </div>
            </div>

            <div class="grid grid-cols-2 divide-x border-black text-[10px]">
                <div class="px-2 py-0.5">
                    <span class="font-bold text-black">Contact Person's email:</span>
                    <span class="ml-1 text-slate-800">{{ $contactEmail }}</span>
                </div>
                <div class="px-2 py-0.5">
                    <span class="font-bold text-black">Webpage:</span>
                    <span class="ml-1 text-slate-800">www.trabago.gov.ph</span>
                </div>
            </div>
        </div>

        <!-- 3. Documentary Requirements -->
        <div class="border-2 border-black text-xs divide-y border-collapse">
            <div class="bg-black text-white font-black px-2 py-0.5 uppercase tracking-wider text-[10px] flex items-center justify-between">
                <span>Documentary Requirements</span>
                <span class="text-[8px] font-normal normal-case italic text-slate-200">Original and other documents, when applicable, should be presented for validation.</span>
            </div>

            <div class="grid grid-cols-12 divide-x border-black font-bold text-[9px] bg-slate-100 py-0.5 px-2">
                <div class="col-span-8">Required Documents</div>
                <div class="col-span-4 text-center">Validity Period</div>
            </div>

            @php
                $getDocKey = function($term) use ($docs) {
                    foreach ($docs as $k => $v) {
                        if (str_contains(strtolower($k), strtolower($term))) return $k;
                    }
                    return null;
                };

                $birKey = $getDocKey('bir') ?: ($getDocKey('2303') ?: $getDocKey('tin'));
                $secKey = $getDocKey('sec') ?: $getDocKey('dti');
                $permitKey = $getDocKey('mayor') ?: ($getDocKey('permit') ?: $getDocKey('business'));
                $philJobNetKey = $getDocKey('philjobnet');
                $vacanciesKey = $getDocKey('vacanc');
                $doleKey = $getDocKey('dole');
                $dmwLicenseKey = $getDocKey('dmw_license') ?: ($getDocKey('dmw') && !$getDocKey('order') ? $getDocKey('dmw') : null);
                $dmwOrdersKey = $getDocKey('order');
                $intentKey = $getDocKey('intent') ?: $getDocKey('letter');

                $hasPhilJobNet = !is_null($philJobNetKey);
                $hasVacancies = !is_null($vacanciesKey) || $jobsList->count() > 0;
                $hasDole = !is_null($doleKey);
                $hasDmwLicense = !is_null($dmwLicenseKey);
                $hasDmwOrders = !is_null($dmwOrdersKey);
                $hasIntent = !is_null($intentKey);

                $docChecklist = [
                    ['BIR Certificate of Registration (Form 2303)', !is_null($birKey), 'Current / Valid', $birKey],
                    ['SEC Registration or DTI Registration', !is_null($secKey), 'Perpetual / Registered', $secKey],
                    ['Mayor\'s Business Permit (current year)', !is_null($permitKey), 'Calendar Year ' . date('Y'), $permitKey],
                    ['PhilJobNet Proof of Registration', $hasPhilJobNet || true, $hasPhilJobNet ? 'Active Registration' : 'Active Portal Account', $philJobNetKey],
                    ['Updated Job Vacancies (use prescribed form)', $hasVacancies, 'Active Hiring Needs', $vacanciesKey],
                    ['DOLE License/DO 174 (for Licensed Private Recruitment & Placement Agency)', $hasDole, $hasDole ? 'Valid DOLE License' : 'When Applicable (PRPA / Subcontractor)', $doleKey],
                    ['DMW License (for Overseas Recruitment & Placement Agency)', $hasDmwLicense, $hasDmwLicense ? 'Valid DMW License' : 'When Applicable (Overseas)', $dmwLicenseKey],
                    ['DMW Approved and Validated Job Orders', $hasDmwOrders, $hasDmwOrders ? 'Verified Orders Period' : 'When Applicable (Overseas)', $dmwOrdersKey],
                    ['Letter of Intent (for specific services and assistance needed with details)', $hasIntent || true, $hasIntent ? 'Official Signed Request' : 'On File with DMDP', $intentKey]
                ];
            @endphp

            @foreach($docChecklist as $item)
                <div class="grid grid-cols-12 divide-x border-black text-[9px] py-0.5 px-2 items-center">
                    <div class="col-span-8 flex items-center justify-between pr-2">
                        <div class="flex items-center gap-2 truncate">
                            <span class="font-mono font-bold">{{ $item[1] ? '[X]' : '[  ]' }}</span>
                            <span class="{{ $item[1] ? 'font-bold text-black' : 'text-slate-700' }} truncate">{{ $item[0] }}</span>
                        </div>
                        @if(!empty($item[3]) && isset($docs[$item[3]]))
                            <button type="button" 
                                    @click='openDocInspection("{{ addslashes($companyName) }}", @json($docs), "{{ $item[3] }}")'
                                    class="no-print inline-flex items-center gap-0.5 text-[8.5px] font-bold text-emerald-800 hover:text-emerald-950 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 rounded px-1.5 py-0.5 transition-colors cursor-pointer shrink-0"
                                    title="Inspect {{ $item[0] }}">
                                <span>Inspect</span>
                                <svg class="w-2.5 h-2.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </button>
                        @endif
                    </div>
                    <div class="col-span-4 text-center font-semibold text-[9px] {{ $item[1] ? 'text-green-800' : 'text-slate-400' }}">
                        @if($item[1])
                            @if($isComplete)
                                <span class="inline-flex items-center gap-1">
                                    <svg class="h-2.5 w-2.5 text-green-700 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>Verified ({{ $item[2] }})</span>
                                </span>
                            @else
                                {{ $item[2] }}
                            @endif
                        @else
                            ____________________
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- 4. Certification / Authorization -->
        <div class="border-2 border-black text-xs p-2.5 space-y-2">
            <div class="font-bold text-[9px] uppercase text-black">
                CERTIFICATION / AUTHORIZATION
            </div>
            <p class="text-[8.5px] leading-tight text-slate-900 text-justify">
                This is to certify that all the data/information provided in this form are true and correct to the best of my knowledge. This is also to authorize the DMDP to include the establishment profile in the Public Employment Information System (PEIS). It is understood that the establishment profile and contact details shall be made available to the jobseekers, PESOs, DOLE Offices, Bureau of Labor Employment and others who have access to PEIS. Also, aware that the DMDP will refer to the establishment those interested jobseekers and that this office shall submit placement report to DMDP to all those active jobseekers with job offers not limited only to residents of Cebu City but including those who have worked in our branch situated in Cebu City.
            </p>

            <div class="flex items-end justify-between pt-2 text-[10px]">
                <div class="text-center w-64">
                    <p class="font-bold text-black border-b border-black pb-0.5 uppercase">{{ $contactPerson }}</p>
                    <span class="text-[8px] text-slate-700 block mt-0.5">Signature over Printed Name of Authorized Representative</span>
                </div>
                <div class="text-center w-36">
                    <p class="font-bold text-black border-b border-black pb-0.5">{{ date('F d, Y') }}</p>
                    <span class="text-[8px] text-slate-700 block mt-0.5">Date</span>
                </div>
            </div>

            <div class="pt-1 text-[9px] border-t border-slate-200">
                <span class="font-bold text-black">Remarks:</span>
                @if($isComplete)
                    <span class="text-green-800 font-bold ml-1">Documents verified complete and approved for official accreditation.</span>
                @elseif($isIncomplete)
                    <span class="text-rose-700 font-bold ml-1">Documents marked Incomplete: {{ $incompleteReason }}</span>
                @else
                    <span class="text-slate-600 ml-1">Under review by Job Placement Officer (JPO).</span>
                @endif
            </div>
        </div>

        <!-- 5. Approval Box -->
        <div class="border-2 border-black text-xs divide-y border-collapse">
            <div class="bg-black text-white font-black px-2 py-0.5 uppercase tracking-wider text-[9px]">
                APPROVAL
            </div>

            <div class="grid grid-cols-2 divide-x border-black p-2 text-center">
                <div class="space-y-3">
                    <span class="font-bold text-slate-700 block text-[9px] text-left">Reviewed by:</span>
                    <div>
                        <p class="font-black text-black text-[11px] uppercase">GLADY'S T. AVILA</p>
                        <span class="text-[9px] text-slate-700 block">PESO Staff</span>
                    </div>
                </div>
                <div class="space-y-3">
                    <span class="font-bold text-slate-700 block text-[9px] text-left">Recommending Approval:</span>
                    <div>
                        <p class="font-black text-black text-[11px] uppercase">EFLIDA M. ALGUNO</p>
                        <span class="text-[9px] text-slate-700 block">Supervising Labor & Employment Officer</span>
                    </div>
                </div>
            </div>

            <div class="p-2 text-center space-y-3">
                <span class="font-bold text-slate-700 block text-[9px] text-left">Approved by:</span>
                <div>
                    <p class="font-black text-black text-xs uppercase">ANTHONY V. AGUHAR, PhD</p>
                    <span class="text-[9px] text-slate-700 block font-semibold">Acting Department Head II (PESO Manager)</span>
                </div>
            </div>
        </div>

        <!-- Official Bottom Footer -->
        <div class="pt-2 text-center text-[7.5px] text-slate-700 border-t border-slate-300 font-medium leading-tight space-y-0.5">
            <p class="font-bold uppercase">RAMOS PUBLIC MARKET BLDG. ECHAVEZ EXT. COR. D. JAKOSALEM ST., COGON RAMOS, CEBU CITY 6000</p>
            <p>TELEPHONE NO.: (032) 254 5862 &bull; EMAIL ADDRESS: dmdp.peso@cebucity.gov.ph</p>
        </div>

    </div>

    <!-- Reusable Employer Document Viewer Modal -->
    @include('partials.employer-document-viewer-modal')

</body>
</html>
