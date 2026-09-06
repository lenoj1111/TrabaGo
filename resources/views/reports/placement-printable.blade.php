<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FORM 8: PLACEMENT REPORT - {{ $report->company_name ?? ($report->employer->company_name ?? 'Cebu City DMDP') }}</title>
    
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
            .form-sheet { 
                box-shadow: none !important; 
                border: none !important;
                padding: 0 !important; 
                width: 100% !important; 
                max-width: 100% !important;
                margin: 0 !important;
            }
            @page {
                size: A4 portrait;
                margin: 8mm 10mm 8mm 10mm;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-4 sm:p-8 flex flex-col items-center justify-start text-black antialiased font-sans">

    @php
        $rData = is_array($report->report_data) ? $report->report_data : (json_decode($report->report_data ?? '[]', true) ?: []);
        $hiredList = $rData['hired_list'] ?? [];
        $totalHired = $rData['total_hired'] ?? count($hiredList);

        $currentUser = Auth::user();
        $backUrl = route('admin.placement-reports.index');
        if ($currentUser && $currentUser->role === 'employer') {
            $backUrl = route('employer.placement-reports');
        } elseif ($currentUser && $currentUser->role === 'jpo') {
            $backUrl = route('jpo.evaluations.placement-reports');
        }

        $reportDate = !empty($report->created_at) ? date('F d, Y', strtotime($report->created_at)) : date('F d, Y');
        $activityName = $rData['activity_name'] ?? ('Placement Referral & Hiring Activity (' . date('F Y', strtotime($report->report_month ?? 'now')) . ')');
        $companyName = $report->company_name ?? ($report->employer->company_name ?? 'Cebu IT Global Corp');

        // Pad list to at least 15 rows matching official DOLE Form 8
        $rowCount = max(15, count($hiredList));
    @endphp

    <!-- Top Action Bar (No-Print) -->
    <div class="no-print w-full max-w-4xl mb-5 flex items-center justify-between gap-4">
        <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-700 hover:text-emerald-700 bg-white px-4 py-2.5 rounded-xl shadow-xs border border-slate-200 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Portal
        </a>

        <div class="flex items-center gap-3">
            <span class="text-xs text-slate-500 font-semibold">Official DOLE / DMDP Form 8 Standard</span>
            <button onclick="window.print()" class="inline-flex items-center gap-2 text-xs font-black text-white bg-emerald-700 hover:bg-emerald-600 px-6 py-2.5 rounded-xl shadow-lg shadow-emerald-700/20 transition-all hover:scale-105">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print / Save PDF (Form 8)
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- OFFICIAL FORM 8: PLACEMENT REPORT SHEET -->
    <!-- ========================================================================= -->
    <div class="form-sheet w-full max-w-4xl bg-white border border-slate-300 p-6 sm:p-10 shadow-xl space-y-3.5 text-black">
        
        <!-- Header Section with Official Logos & Headings -->
        <div class="relative">
            <!-- Top Right Form Indicator -->
            <div class="text-right">
                <span class="font-black text-xs sm:text-sm tracking-wider text-black">FORM 8</span>
            </div>

            <div class="flex items-center justify-between gap-4 -mt-2">
                <!-- Left: DOLE Official Seal Graphic -->
                <div class="w-16 h-16 shrink-0 flex items-center justify-center">
                    <svg class="w-15 h-15" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- DOLE Triangle Outline -->
                        <polygon points="50,6 94,88 6,88" fill="#1E3A8A" stroke="#B45309" stroke-width="2.5" stroke-linejoin="round"/>
                        <!-- Lower Red Field -->
                        <polygon points="50,54 94,88 6,88" fill="#DC2626"/>
                        <!-- Center White/Gold Gear -->
                        <circle cx="50" cy="50" r="17" fill="#FBBF24" stroke="#92400E" stroke-width="1.5"/>
                        <circle cx="50" cy="50" r="8" fill="#FFFFFF"/>
                        <!-- Gear teeth hints -->
                        <circle cx="50" cy="50" r="14" stroke="#78350F" stroke-width="2" stroke-dasharray="3,3"/>
                        <!-- Golden Stars -->
                        <polygon points="50,16 52,21 57,21 53,24 55,29 50,26 45,29 47,24 43,21 48,21" fill="#FDE047"/>
                        <polygon points="26,76 28,79 32,79 29,81 30,85 26,83 22,85 24,81 20,79 24,79" fill="#FDE047"/>
                        <polygon points="74,76 76,79 80,79 77,81 78,85 74,83 70,85 72,81 68,79 72,79" fill="#FDE047"/>
                        <!-- Department Text -->
                        <text x="50" y="96" text-anchor="middle" font-size="7" font-weight="900" fill="#1E3A8A" font-family="sans-serif">DOLE RO-VII</text>
                    </svg>
                </div>

                <!-- Center: Official Republic & DOLE / DMDP Text -->
                <div class="text-center flex-1 space-y-0.5">
                    <p class="text-[11px] sm:text-xs text-slate-800 font-serif leading-tight">Republic of the Philippines</p>
                    <p class="text-[11px] sm:text-xs text-slate-800 font-serif leading-tight">Department of Labor and Employment</p>
                    <p class="text-[11px] sm:text-xs text-slate-800 font-serif leading-tight">Regional Office No. VII</p>
                    <p class="text-[11px] sm:text-xs text-slate-800 font-serif leading-tight">Tri-City Field Office</p>
                    <p class="text-[11px] sm:text-xs font-bold text-slate-900 font-serif leading-tight">Cebu City Government</p>
                    <h2 class="text-xs sm:text-[13px] font-black text-black tracking-tight uppercase mt-0.5">
                        DEPARTMENT OF MANPOWER DEVELOPMENT AND PLACEMENT
                    </h2>
                </div>

                <!-- Right: DMDP Seal & City of Cebu Official Seal -->
                <div class="shrink-0 flex items-center justify-center gap-1.5">
                    <!-- DMDP Round Seal -->
                    <div class="w-12 h-12 rounded-full border-2 border-amber-600 bg-amber-400 p-0.5 flex flex-col items-center justify-center shadow-xs text-center">
                        <div class="w-full h-full rounded-full border border-amber-800 bg-white flex flex-col items-center justify-center leading-none p-0.5">
                            <span class="text-[6px] font-black text-amber-800 tracking-tighter">CEBU CITY</span>
                            <span class="text-[7.5px] font-black text-emerald-800">DMDP</span>
                            <span class="text-[5px] font-bold text-slate-600">PESO</span>
                        </div>
                    </div>

                    <!-- City of Cebu Round Official Seal -->
                    <div class="w-12 h-12 rounded-full border-2 border-amber-600 bg-amber-400 p-0.5 flex flex-col items-center justify-center shadow-xs text-center">
                        <div class="w-full h-full rounded-full border border-amber-800 bg-white flex flex-col items-center justify-center leading-none p-0.5">
                            <span class="text-[6px] font-black text-black tracking-tighter">OFFICIAL SEAL</span>
                            <span class="text-[7.5px] font-black text-red-700">CEBU</span>
                            <span class="text-[5px] font-bold text-slate-700">CITY</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Document Title -->
            <div class="text-center pt-2 pb-0.5">
                <h1 class="text-base sm:text-lg font-black text-black tracking-wide uppercase">
                    PLACEMENT REPORT
                </h1>
                <p class="text-[10px] text-slate-600 font-semibold uppercase tracking-wider">
                    Monthly Employer Placement Report
                </p>
            </div>
        </div>

        <!-- Meta Activity & Date Header Box -->
        <div class="border-2 border-black text-xs divide-y-2 divide-black">
            <div class="px-3 py-1 flex items-center">
                <span class="font-bold text-black min-w-[130px]">Name of Activity:</span>
                <span class="font-semibold text-slate-900 flex-1">{{ $activityName }}</span>
            </div>
            <div class="grid grid-cols-2 divide-x-2 divide-black">
                <div class="px-3 py-1 flex items-center">
                    <span class="font-bold text-black min-w-[130px]">Regional Office:</span>
                    <span class="font-black text-slate-900">VII</span>
                </div>
                <div class="px-3 py-1 flex items-center">
                    <span class="font-bold text-black min-w-[60px]">Date :</span>
                    <span class="font-semibold text-slate-900">{{ $reportDate }}</span>
                </div>
            </div>
        </div>

        <!-- Main Form 8 Table (Exact Column Structure) -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border-2 border-black text-[11px] leading-tight text-black">
                <thead>
                    <tr class="border-b-2 border-black text-center font-black bg-slate-50">
                        <th rowspan="2" class="border-r-2 border-black p-1.5 w-7 text-center">#</th>
                        <th rowspan="2" class="border-r-2 border-black p-1.5 text-center min-w-[140px]">Name of Jobseeker</th>
                        <th rowspan="2" class="border-r-2 border-black p-1.5 w-12 text-center">Sex<br><span class="font-normal text-[9px]">M / F</span></th>
                        <th rowspan="2" class="border-r-2 border-black p-1.5 text-center min-w-[110px]">Occupation Title</th>
                        <th rowspan="2" class="border-r-2 border-black p-1.5 text-center min-w-[120px]">Hiring Company</th>
                        <th colspan="3" class="border-r-2 border-black p-1 text-center text-[10px]">
                            Status of Application<br>
                            <span class="font-normal italic text-[8px]">(Please check only one)</span>
                        </th>
                        <th rowspan="2" class="border-r-2 border-black p-1.5 w-24 text-center">Date Hired</th>
                        <th rowspan="2" class="p-1.5 min-w-[90px] text-center">Municipality<br>/ City</th>
                    </tr>
                    <tr class="border-b-2 border-black text-center font-bold text-[9px] bg-slate-50">
                        <th class="border-r border-black p-1 w-12 text-center">Hired</th>
                        <th class="border-r border-black p-1 w-12 text-center">On-going</th>
                        <th class="border-r-2 border-black p-1 w-12 text-center">Not Hired</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < $rowCount; $i++)
                        @php
                            $hire = $hiredList[$i] ?? null;
                            $statusVal = strtolower($hire['status'] ?? 'hired');
                            $isHired = ($statusVal === 'hired' || $statusVal === 'placed' || empty($statusVal));
                            $isOnGoing = in_array($statusVal, ['on-going', 'ongoing', 'interview', 'pending', 'assessment']);
                            $isNotHired = in_array($statusVal, ['not hired', 'nothired', 'rejected', 'failed']);
                        @endphp
                        <tr class="border-b border-black hover:bg-slate-50/50 min-h-[26px]">
                            <!-- Row Number -->
                            <td class="border-r-2 border-black p-1 text-center font-bold">
                                {{ $i + 1 }}
                            </td>

                            <!-- Candidate Name -->
                            <td class="border-r-2 border-black p-1 font-semibold">
                                {{ $hire['jobseeker_name'] ?? ($hire['name'] ?? '') }}
                            </td>

                            <!-- Sex (M / F) -->
                            <td class="border-r-2 border-black p-1 text-center font-bold">
                                {{ strtoupper($hire['sex'] ?? ($hire['gender'] ?? ($hire ? 'M' : ''))) }}
                            </td>

                            <!-- Occupation Title -->
                            <td class="border-r-2 border-black p-1">
                                {{ $hire['position'] ?? ($hire['occupation_title'] ?? '') }}
                            </td>

                            <!-- Hiring Company -->
                            <td class="border-r-2 border-black p-1 font-medium">
                                {{ $hire ? ($hire['company_name'] ?? $companyName) : '' }}
                            </td>

                            <!-- Status Checkboxes: Hired -->
                            <td class="border-r border-black p-1 text-center font-mono font-bold text-xs">
                                @if($hire)
                                    {{ $isHired ? '✓' : '' }}
                                @endif
                            </td>

                            <!-- Status Checkboxes: On-going -->
                            <td class="border-r border-black p-1 text-center font-mono font-bold text-xs">
                                @if($hire)
                                    {{ $isOnGoing ? '✓' : '' }}
                                @endif
                            </td>

                            <!-- Status Checkboxes: Not Hired -->
                            <td class="border-r-2 border-black p-1 text-center font-mono font-bold text-xs">
                                @if($hire)
                                    {{ $isNotHired ? '✓' : '' }}
                                @endif
                            </td>

                            <!-- Date Hired -->
                            <td class="border-r-2 border-black p-1 text-center font-medium text-[10px]">
                                @if($hire && !empty($hire['hired_date']))
                                    {{ date('m/d/Y', strtotime($hire['hired_date'])) }}
                                @elseif($hire && $isHired)
                                    {{ date('m/d/Y') }}
                                @endif
                            </td>

                            <!-- Municipality / City -->
                            <td class="p-1 text-center font-medium text-[10px]">
                                {{ $hire ? ($hire['city'] ?? ($hire['municipality'] ?? 'Cebu City')) : '' }}
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <!-- Official Signatures Box (Matching Form 8) -->
        <div class="border-2 border-black grid grid-cols-2 divide-x-2 divide-black text-xs">
            <!-- Prepared by: PESO Officer / Staff -->
            <div class="p-3 sm:p-4 space-y-8">
                <span class="font-bold text-black block">Prepared by:</span>
                <div class="pt-6 text-center">
                    <p class="font-bold text-black border-b border-black pb-0.5 mx-auto max-w-[240px]">
                        {{ $report->jpo_remarks ? 'Job Placement Officer' : '______________________________' }}
                    </p>
                    <span class="text-[10px] text-slate-700 block mt-1 uppercase font-semibold">PESO Officer / Staff</span>
                </div>
            </div>

            <!-- Approved by: PESO Manager -->
            <div class="p-3 sm:p-4 space-y-8">
                <span class="font-bold text-black block">Approved by:</span>
                <div class="pt-6 text-center">
                    <p class="font-bold text-black border-b border-black pb-0.5 mx-auto max-w-[240px]">
                        ANTHONY V. AGUHAR, PhD
                    </p>
                    <span class="text-[10px] text-slate-700 block mt-1 uppercase font-semibold">PESO Manager</span>
                </div>
            </div>
        </div>

        <!-- Electronic Verification Footnote -->
        <div class="pt-2 text-[9px] text-slate-500 flex items-center justify-between border-t border-slate-200">
            <p>TrabaGo Automated Placement Monitoring &bull; Cebu City DMDP &bull; Ref #{{ str_pad($report->report_id ?? 1, 5, '0', STR_PAD_LEFT) }}</p>
            <p>Generated: {{ date('Y-m-d H:i:s') }}</p>
        </div>

    </div>

</body>
</html>
