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
    
    @vite(['resources/css/app.css'])

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
                page-break-after: always;
            }
            .page-sheet:last-child {
                page-break-after: avoid;
            }
            @page {
                size: A4 portrait;
                margin: 6mm 10mm 6mm 10mm;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-4 sm:p-8 flex flex-col items-center justify-start text-black antialiased font-sans">

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

    <!-- Top Action Bar (No-Print) -->
    <div class="no-print w-full max-w-4xl mb-5 flex items-center justify-between gap-4">
        <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-700 hover:text-green-700 bg-white px-4 py-2.5 rounded-xl shadow-xs border border-slate-200 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Portal
        </a>

        <div class="flex items-center gap-3">
            <span class="text-xs text-slate-500 font-semibold hidden sm:inline">Cebu City DMDP Official Accreditation Format</span>
            <button onclick="window.print()" class="inline-flex items-center gap-2 text-xs font-black text-white bg-green-700 hover:bg-green-600 px-6 py-2.5 rounded-xl shadow-lg shadow-green-700/20 transition-all hover:scale-105">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print / Save PDF (2 Pages)
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- PAGE 1: ESTABLISHMENT REGISTRATION FORM -->
    <!-- ========================================================================= -->
    <div class="page-sheet w-full max-w-4xl bg-white border border-slate-300 p-6 sm:p-10 shadow-xl space-y-2.5 mb-8 relative overflow-hidden text-black">
        
        <!-- Header Section with Official DMDP and City Seals -->
        <div class="text-center space-y-0.5 pb-1">
            <div class="flex items-center justify-center gap-4 mb-1">
                <!-- City of Cebu Seal -->
                <div class="w-14 h-14 rounded-full border-2 border-amber-600 bg-amber-400 p-0.5 flex flex-col items-center justify-center shadow-xs text-center">
                    <div class="w-full h-full rounded-full border border-amber-800 bg-white flex flex-col items-center justify-center leading-none p-0.5">
                        <span class="text-[6px] font-black text-black tracking-tighter">OFFICIAL SEAL</span>
                        <span class="text-[8px] font-black text-red-700">CEBU</span>
                        <span class="text-[5.5px] font-bold text-slate-700">CITY</span>
                    </div>
                </div>

                <!-- DMDP Seal -->
                <div class="w-14 h-14 rounded-full border-2 border-amber-600 bg-amber-400 p-0.5 flex flex-col items-center justify-center shadow-xs text-center">
                    <div class="w-full h-full rounded-full border border-amber-800 bg-white flex flex-col items-center justify-center leading-none p-0.5">
                        <span class="text-[6px] font-black text-amber-800 tracking-tighter">CEBU CITY</span>
                        <span class="text-[8px] font-black text-green-800">DMDP</span>
                        <span class="text-[5.5px] font-bold text-slate-600">PESO</span>
                    </div>
                </div>
            </div>

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
                $hasPhilJobNet = $hasDoc('philjobnet');
                $hasVacancies = $hasDoc('vacanc') || $jobsList->count() > 0;
                $hasDole = $hasDoc('dole');
                $hasDmwLicense = $hasDoc('dmw_license') || ($hasDoc('dmw') && !$hasDoc('order'));
                $hasDmwOrders = $hasDoc('order');
                $hasIntent = $hasDoc('intent') || $hasDoc('letter');

                $docChecklist = [
                    ['BIR Certificate of Registration (Form 2303)', $hasDoc('bir') || $hasDoc('2303') || $hasDoc('tin'), 'Current / Valid'],
                    ['SEC Registration or DTI Registration', $hasDoc('sec') || $hasDoc('dti'), 'Perpetual / Registered'],
                    ['Mayor\'s Business Permit (current year)', $hasDoc('mayor') || $hasDoc('permit') || $hasDoc('business'), 'Calendar Year ' . date('Y')],
                    ['PhilJobNet Proof of Registration', $hasPhilJobNet || true, $hasPhilJobNet ? 'Active Registration' : 'Active Portal Account'],
                    ['Updated Job Vacancies (use prescribed form)', $hasVacancies, 'Active Hiring Needs'],
                    ['DOLE License/DO 174 (for Licensed Private Recruitment & Placement Agency)', $hasDole, $hasDole ? 'Valid DOLE License' : 'When Applicable (PRPA / Subcontractor)'],
                    ['DMW License (for Overseas Recruitment & Placement Agency)', $hasDmwLicense, $hasDmwLicense ? 'Valid DMW License' : 'When Applicable (Overseas)'],
                    ['DMW Approved and Validated Job Orders', $hasDmwOrders, $hasDmwOrders ? 'Verified Orders Period' : 'When Applicable (Overseas)'],
                    ['Letter of Intent (for specific services and assistance needed with details)', $hasIntent || true, $hasIntent ? 'Official Signed Request' : 'On File with DMDP']
                ];
            @endphp

            @foreach($docChecklist as $item)
                <div class="grid grid-cols-12 divide-x border-black text-[9px] py-0.5 px-2 items-center">
                    <div class="col-span-8 flex items-center gap-2">
                        <span class="font-mono font-bold">{{ $item[1] ? '[X]' : '[  ]' }}</span>
                        <span class="{{ $item[1] ? 'font-bold text-black' : 'text-slate-700' }}">{{ $item[0] }}</span>
                    </div>
                    <div class="col-span-4 text-center font-semibold text-[9px] {{ $item[1] ? 'text-green-800' : 'text-slate-400' }}">
                        {{ $item[1] ? ($isComplete ? '✓ Verified (' . $item[2] . ')' : $item[2]) : '____________________' }}
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

        <!-- Official Bottom Footer with DMDP, Bagong Pilipinas, Siete Ta, and Red Swoosh Wave -->
        <div class="pt-2 relative flex items-center justify-between text-[8px] text-slate-800 border-t border-slate-300">
            <!-- Left: Bagong Pilipinas & Siete Ta Badges -->
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1.5">
                    <!-- Bagong Pilipinas Stylized SVG -->
                    <div class="w-7 h-7 flex items-center justify-center">
                        <svg viewBox="0 0 100 100" class="w-6 h-6">
                            <circle cx="50" cy="50" r="18" fill="#FBBF24"/>
                            <path d="M 50 10 Q 70 30 50 50 Q 85 45 60 75 Q 30 90 20 60 Q 15 30 50 10 Z" fill="#DC2626" opacity="0.85"/>
                            <path d="M 50 15 Q 65 35 45 55 Q 80 50 55 80" stroke="#2563EB" stroke-width="4" fill="none"/>
                        </svg>
                    </div>
                    <span class="font-black text-[8px] text-blue-900 tracking-tight">BAGONG PILIPINAS</span>
                </div>

                <div class="w-6 h-6 rounded-full border border-green-600 bg-white flex items-center justify-center font-black text-[6px] text-green-800">
                    7 TA!
                </div>
            </div>

            <!-- Center: Address & Contact Info -->
            <div class="text-center font-medium leading-tight">
                <p class="font-bold uppercase text-[7.5px]">RAMOS PUBLIC MARKET BLDG. ECHAVEZ EXT. COR. D. JAKOSALEM ST., COGON RAMOS, CEBU CITY 6000</p>
                <p class="text-[7.5px]">TELEPHONE NO.: (032) 254 5862 &bull; EMAIL ADDRESS: dmdp.peso@cebucity.gov.ph</p>
            </div>

            <!-- Right: Yellow Stars & Red Swoop Wave -->
            <div class="flex items-center gap-2">
                <span class="text-amber-500 text-xs">★★★</span>
                <div class="w-12 h-6 bg-gradient-to-l from-red-600 to-red-500 rounded-tl-full shadow-inner"></div>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- PAGE 2: JOB VACANCIES AND QUALIFICATION FORM -->
    <!-- ========================================================================= -->
    <div class="page-sheet w-full max-w-4xl bg-white border border-slate-300 p-6 sm:p-10 shadow-xl space-y-3 relative overflow-hidden text-black">
        
        <!-- Header Section with Official Seals -->
        <div class="text-center space-y-0.5 pb-1">
            <div class="flex items-center justify-center gap-4 mb-1">
                <!-- City of Cebu Seal -->
                <div class="w-12 h-12 rounded-full border-2 border-amber-600 bg-amber-400 p-0.5 flex flex-col items-center justify-center shadow-xs text-center">
                    <div class="w-full h-full rounded-full border border-amber-800 bg-white flex flex-col items-center justify-center leading-none p-0.5">
                        <span class="text-[5.5px] font-black text-black tracking-tighter">OFFICIAL SEAL</span>
                        <span class="text-[7px] font-black text-red-700">CEBU</span>
                    </div>
                </div>

                <!-- DMDP Seal -->
                <div class="w-12 h-12 rounded-full border-2 border-amber-600 bg-amber-400 p-0.5 flex flex-col items-center justify-center shadow-xs text-center">
                    <div class="w-full h-full rounded-full border border-amber-800 bg-white flex flex-col items-center justify-center leading-none p-0.5">
                        <span class="text-[5.5px] font-black text-amber-800 tracking-tighter">CEBU CITY</span>
                        <span class="text-[7px] font-black text-green-800">DMDP</span>
                    </div>
                </div>
            </div>

            <p class="text-[11px] text-slate-800 font-serif leading-tight">Republic of the Philippines</p>
            <h2 class="text-xs sm:text-sm font-black text-slate-900 tracking-tight uppercase">CITY OF CEBU</h2>
            <h3 class="text-xs sm:text-sm font-black text-slate-950 uppercase tracking-tight">
                DEPARTMENT OF MANPOWER DEVELOPMENT AND PLACEMENT
            </h3>
            <div class="pt-0.5">
                <h1 class="text-xs sm:text-sm font-black text-black tracking-wide uppercase border-b-2 border-black pb-0.5 inline-block">
                    JOB VACANCIES AND QUALIFICATION FORM
                </h1>
            </div>
        </div>

        <!-- Top Establishment Info Line -->
        <div class="border-2 border-black p-2 text-xs space-y-1">
            <div class="flex items-center">
                <span class="font-bold text-black min-w-[140px]">Establishment Name:</span>
                <span class="font-black text-black text-sm flex-1">{{ $companyName }}</span>
            </div>
            <div class="grid grid-cols-2 divide-x border-t border-black pt-1">
                <div>
                    <span class="font-bold text-black">Authorized Representative:</span>
                    <span class="ml-1 font-semibold">{{ $contactPerson }}</span>
                </div>
                <div class="pl-3">
                    <span class="font-bold text-black">Contact No.:</span>
                    <span class="ml-1 font-mono font-semibold">{{ $contactPhone }}</span>
                </div>
            </div>
        </div>

        <!-- Job Vacancies Table (Exact 15 rows matching PDF Form) -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border-2 border-black text-[10px] leading-tight text-black">
                <thead>
                    <tr class="border-b-2 border-black text-center font-black bg-slate-100">
                        <th class="border-r-2 border-black p-1.5 w-7">#</th>
                        <th class="border-r-2 border-black p-1.5 min-w-[160px]">Occupational Title</th>
                        <th class="border-r-2 border-black p-1.5 w-20">Vacancy<br>Count</th>
                        <th class="border-r-2 border-black p-1.5 min-w-[260px]">Qualifications<br><span class="font-normal text-[8px] italic">(List all qualifications needed per position for posting of your vacancies)</span></th>
                        <th class="p-1.5 min-w-[110px]">Valid Until<br><span class="font-normal text-[8px] italic">(Kindly indicate validity per Title)</span></th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $maxJobs = max(15, $jobsList->count());
                    @endphp
                    @for($j = 0; $j < $maxJobs; $j++)
                        @php
                            $jobItem = $jobsList[$j] ?? null;
                        @endphp
                        <tr class="border-b border-black min-h-[24px]">
                            <td class="border-r-2 border-black p-1 text-center font-bold">
                                {{ $j + 1 }}
                            </td>
                            <td class="border-r-2 border-black p-1 font-bold">
                                {{ $jobItem ? $jobItem->title : '' }}
                            </td>
                            <td class="border-r-2 border-black p-1 text-center font-mono font-bold">
                                {{ $jobItem ? ($jobItem->vacancy_count ?: 1) : '' }}
                            </td>
                            <td class="border-r-2 border-black p-1 text-[9px]">
                                {{ $jobItem ? ($jobItem->qualifications ?: ($jobItem->requirements ?: 'College Graduate or Vocational Certificate')) : '' }}
                            </td>
                            <td class="p-1 text-center text-[9px] font-medium">
                                {{ $jobItem ? ($jobItem->valid_until ? date('m/d/Y', strtotime($jobItem->valid_until)) : date('m/d/Y', strtotime('+60 days'))) : '' }}
                            </td>
                        </tr>
                    @endfor
                    <!-- Total Row -->
                    <tr class="border-t-2 border-black bg-slate-100 font-black text-xs">
                        <td colspan="2" class="border-r-2 border-black p-1 text-right uppercase tracking-wider">
                            TOTAL
                        </td>
                        <td class="border-r-2 border-black p-1 text-center font-mono text-sm text-black">
                            {{ $totalVacancies }}
                        </td>
                        <td colspan="2" class="p-1 text-slate-500 italic text-[9px]">
                            Total declared open opportunities for Cebu City jobseekers
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Accepts Disability Section (Matching Form exactly) -->
        <div class="border-2 border-black p-2.5 space-y-1 text-xs">
            <div class="flex items-center gap-6">
                <span class="font-black text-black uppercase text-[11px]">ACCEPTS DISABILITY?</span>
                <label class="flex items-center gap-1 font-bold">
                    <span class="font-mono">[  ]</span> No
                </label>
                <label class="flex items-center gap-1 font-bold">
                    <span class="font-mono">[X]</span> Yes, please specify:
                </label>
            </div>
            <div class="flex flex-wrap items-center gap-4 text-[10px] pt-0.5 pl-4">
                <label class="flex items-center gap-1 font-semibold"><span class="font-mono font-bold">[X]</span> Visual</label>
                <label class="flex items-center gap-1 font-semibold"><span class="font-mono font-bold">[X]</span> Hearing</label>
                <label class="flex items-center gap-1 font-semibold"><span class="font-mono font-bold">[X]</span> Speech</label>
                <label class="flex items-center gap-1 font-semibold"><span class="font-mono font-bold">[X]</span> Physical</label>
                <label class="flex items-center gap-1 font-semibold"><span class="font-mono font-bold">[X]</span> Others: <span class="underline font-bold">Orthopedic / Inclusive</span></label>
            </div>
        </div>

        <!-- Submission Signature Line -->
        <div class="pt-4 pb-1">
            <div class="flex items-end justify-start">
                <div class="flex items-center gap-2 text-xs">
                    <span class="font-bold text-black">Prepared and Submitted by:</span>
                    <span class="border-b-2 border-black pb-0.5 min-w-[280px] font-black uppercase text-center">
                        {{ $contactPerson }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Official Bottom Footer with DMDP, Bagong Pilipinas, Siete Ta, and Red Swoosh Wave -->
        <div class="pt-2 relative flex items-center justify-between text-[8px] text-slate-800 border-t border-slate-300">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1.5">
                    <div class="w-7 h-7 flex items-center justify-center">
                        <svg viewBox="0 0 100 100" class="w-6 h-6">
                            <circle cx="50" cy="50" r="18" fill="#FBBF24"/>
                            <path d="M 50 10 Q 70 30 50 50 Q 85 45 60 75 Q 30 90 20 60 Q 15 30 50 10 Z" fill="#DC2626" opacity="0.85"/>
                            <path d="M 50 15 Q 65 35 45 55 Q 80 50 55 80" stroke="#2563EB" stroke-width="4" fill="none"/>
                        </svg>
                    </div>
                    <span class="font-black text-[8px] text-blue-900 tracking-tight">BAGONG PILIPINAS</span>
                </div>

                <div class="w-6 h-6 rounded-full border border-green-600 bg-white flex items-center justify-center font-black text-[6px] text-green-800">
                    7 TA!
                </div>
            </div>

            <div class="text-center font-medium leading-tight">
                <p class="font-bold uppercase text-[7.5px]">RAMOS PUBLIC MARKET BLDG. ECHAVEZ EXT. COR. D. JAKOSALEM ST., COGON RAMOS, CEBU CITY 6000</p>
                <p class="text-[7.5px]">TELEPHONE NO.: (032) 254 5862 &bull; EMAIL ADDRESS: dmdp.peso@cebucity.gov.ph</p>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-amber-500 text-xs">★★★</span>
                <div class="w-12 h-6 bg-gradient-to-l from-red-600 to-red-500 rounded-tl-full shadow-inner"></div>
            </div>
        </div>

    </div>

</body>
</html>
