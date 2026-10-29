<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Employer Accreditation - {{ $employer->company_name }} | DMDP Cebu City</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 px-4 flex flex-col items-center justify-center font-sans antialiased text-slate-800">

    <!-- Print Control Bar -->
    <div class="no-print w-full max-w-4xl mb-6 flex items-center justify-between bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3">
            <span class="h-3 w-3 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-xs font-bold text-slate-700">Official DMDP Certificate of Accreditation</span>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.history.back()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 transition-colors">
                &larr; Back
            </button>
            <button onclick="window.print()" class="px-5 py-2 text-xs font-black text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print / Save Certificate
            </button>
        </div>
    </div>

    <!-- Official Certificate Container (Landscape Frame) -->
    <div class="w-full max-w-4xl bg-white border-8 border-double border-emerald-900 p-10 sm:p-14 shadow-2xl relative rounded-xl overflow-hidden">
        <!-- Decorative Corner Accents -->
        <div class="absolute top-2 left-2 w-12 h-12 border-t-4 border-l-4 border-emerald-700 pointer-events-none"></div>
        <div class="absolute top-2 right-2 w-12 h-12 border-t-4 border-r-4 border-emerald-700 pointer-events-none"></div>
        <div class="absolute bottom-2 left-2 w-12 h-12 border-b-4 border-l-4 border-emerald-700 pointer-events-none"></div>
        <div class="absolute bottom-2 right-2 w-12 h-12 border-b-4 border-r-4 border-emerald-700 pointer-events-none"></div>

        <!-- Watermark -->
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-[0.03]">
            <span class="text-9xl font-black uppercase text-emerald-950">TRABAGO</span>
        </div>

        <!-- Header -->
        <div class="text-center space-y-1 relative z-10">
            <p class="text-xs uppercase font-extrabold tracking-widest text-slate-500">Republic of the Philippines &bull; Region VII (Central Visayas)</p>
            <h3 class="text-sm uppercase font-black tracking-wider text-slate-800">City Government of Cebu</h3>
            <h4 class="text-xs uppercase font-bold tracking-wide text-emerald-800">Department of Manpower Development and Placement (DMDP)</h4>
            <p class="text-[10px] text-slate-500">Public Employment Service Office (PESO) &bull; Cebu City Hall</p>

            <div class="w-24 h-1 bg-emerald-600 mx-auto my-4 rounded-full"></div>

            <h1 class="text-2xl sm:text-3xl font-serif font-black tracking-wide uppercase text-emerald-950 mt-4">
                Certificate of Accreditation
            </h1>
            <p class="text-xs font-mono font-bold text-slate-500 mt-1">
                Certificate No: <span class="text-emerald-800 font-extrabold">{{ $accreditation->certificate_no ?? ('DMDP-EMP-' . date('Y') . '-' . str_pad($employer->employer_id, 5, '0', STR_PAD_LEFT)) }}</span>
            </p>
        </div>

        <!-- Body Content -->
        <div class="text-center my-8 space-y-5 relative z-10">
            <p class="text-xs italic text-slate-600">This is to officially certify that</p>
            
            <div class="border-b-2 border-slate-900 pb-2 mx-auto max-w-xl">
                <h2 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-slate-900">
                    {{ $employer->company_name }}
                </h2>
                @if($employer->company_address)
                    <p class="text-xs text-slate-500 mt-1">{{ $employer->company_address }}</p>
                @endif
            </div>

            <p class="text-xs sm:text-sm text-slate-700 max-w-2xl mx-auto leading-relaxed">
                has complied with all statutory documentary requirements prescribed under Republic Act No. 8759 (Public Employment Service Office Act of 1999) and the accreditation standards of the City Government of Cebu. 
                The aforementioned corporate establishment is hereby recognized as an <strong>Official Accredited Employment Partner</strong> authorized to post vacancies and receive screened jobseeker referrals.
            </p>
        </div>

        <!-- Signatures and Details -->
        <div class="grid grid-cols-3 gap-6 pt-8 border-t border-slate-200 mt-8 relative z-10 text-xs">
            <div class="text-center space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase block">Date Issued</span>
                <p class="font-bold text-slate-800">
                    {{ $accreditation->certificate_issued_at ? date('F d, Y', strtotime($accreditation->certificate_issued_at)) : date('F d, Y') }}
                </p>
                <span class="text-[9px] text-emerald-700 font-semibold block">Valid for Region VII Scope</span>
            </div>

            <div class="text-center space-y-1">
                <div class="h-10 flex items-center justify-center">
                    <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-300 font-black text-[10px] tracking-wider uppercase">
                        DMDP Verified &bull; Region 7
                    </span>
                </div>
                <p class="text-[10px] text-slate-500">Official PESO / DMDP Accreditation</p>
            </div>

            <div class="text-center space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase block">Authorized Administrator</span>
                <p class="font-black text-slate-900 underline uppercase">Department Head / City PESO Officer</p>
                <span class="text-[9px] text-slate-500 block">Department of Manpower Development & Placement</span>
            </div>
        </div>

        <!-- Security Footer -->
        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-[9px] text-slate-400 relative z-10">
            <span>TrabaGo Digital Facilitation Platform &bull; Cebu City DMDP</span>
            <span>Document Authentication: <span class="font-mono font-bold text-slate-600">SEC-{{ substr(md5($employer->employer_id . ($accreditation->certificate_no ?? '')), 0, 10) }}</span></span>
        </div>
    </div>

</body>
</html>
