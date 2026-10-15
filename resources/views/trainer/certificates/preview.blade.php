<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Completion - {{ $enrollment->first_name }} {{ $enrollment->last_name }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Inter:wght@400;600;700&family=Playfair+Display:ital,wght@1,600&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css'])

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .cert-container { box-shadow: none !important; border: 4px solid #065f46 !important; }
        }
        .font-cinzel { font-family: 'Cinzel', serif; }
        .font-playfair { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-4 sm:p-8 flex flex-col items-center justify-center antialiased">

    <!-- Top Action Bar -->
    <div class="no-print w-full max-w-4xl mb-6 flex items-center justify-between">
        <a href="{{ route('trainer.enrollments.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-white px-4 py-2 rounded-xl shadow-sm border border-slate-200">
            &larr; Back to Enrollments
        </a>
        <button onclick="window.print()" class="inline-flex items-center gap-2 text-xs font-black text-white bg-emerald-600 hover:bg-emerald-500 px-6 py-2 rounded-xl shadow-lg shadow-emerald-600/30">
            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            <span>Print / Download PDF</span>
        </button>
    </div>

    <!-- Official Printable Certificate -->
    <div class="cert-container w-full max-w-4xl bg-white border-8 border-double border-emerald-800 rounded-3xl p-8 sm:p-14 shadow-2xl relative overflow-hidden text-center space-y-6">
        
        <!-- Watermark / Background Accent -->
        <div class="absolute -right-20 -bottom-20 w-96 h-96 rounded-full bg-emerald-50/50 pointer-events-none -z-0"></div>
        <div class="absolute -left-20 -top-20 w-96 h-96 rounded-full bg-emerald-50/50 pointer-events-none -z-0"></div>

        <!-- Header Seals & Logo -->
        <div class="relative z-10 flex items-center justify-center gap-6">
            <div class="h-16 w-16 rounded-full bg-gradient-to-tr from-emerald-800 to-emerald-600 flex items-center justify-center text-white shadow-md">
                <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <h3 class="text-xs font-extrabold uppercase tracking-widest text-emerald-800">Republic of the Philippines &bull; City of Cebu</h3>
                <h2 class="text-sm font-bold text-slate-700">Department of Manpower Development and Placement (DMDP)</h2>
                <span class="text-[10px] uppercase tracking-widest text-slate-400 font-semibold">Skills Training & Certification Division</span>
            </div>
        </div>

        <div class="relative z-10 pt-4">
            <h1 class="font-cinzel text-3xl sm:text-4xl font-black tracking-wider text-emerald-950 uppercase">
                Certificate of Completion
            </h1>
            <p class="font-playfair text-slate-500 text-sm italic mt-1">This official credential is proudly awarded to</p>
        </div>

        <!-- Recipient Name -->
        <div class="relative z-10 py-2 border-b-2 border-emerald-800 max-w-xl mx-auto">
            <h2 class="font-cinzel text-2xl sm:text-3xl font-black text-slate-900 tracking-wide uppercase">
                {{ $enrollment->first_name }} {{ $enrollment->last_name }}
            </h2>
        </div>

        <!-- Course Details -->
        <div class="relative z-10 max-w-2xl mx-auto space-y-2">
            <p class="text-xs text-slate-600 leading-relaxed">
                for successfully completing the standardized industry competency training program in
            </p>
            <h3 class="font-cinzel text-lg sm:text-xl font-black text-emerald-800 uppercase">
                {{ $enrollment->course_title }}
            </h3>
            <p class="text-xs text-slate-500">
                demonstrating professional proficiency, practical laboratory execution, and assessment scoring exceeding the official DMDP benchmark.
            </p>
        </div>

        <!-- Verified Credential Badge & Signatures -->
        <div class="relative z-10 pt-8 grid grid-cols-1 sm:grid-cols-3 gap-6 items-end">
            
            <!-- Left: Certificate ID -->
            <div class="text-center sm:text-left space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Certificate ID</span>
                <p class="text-xs font-black font-mono text-emerald-900">{{ $enrollment->certificate_no }}</p>
                <span class="text-[10px] text-slate-400">Issued: {{ date('F d, Y', strtotime($enrollment->certificate_issued_at ?? now())) }}</span>
            </div>

            <!-- Center: Gold Seal -->
            <div class="flex flex-col items-center justify-center">
                <div class="h-20 w-20 rounded-full border-4 border-amber-400 bg-amber-50 flex items-center justify-center text-amber-600 shadow-inner">
                    <svg class="h-10 w-10 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-amber-700 mt-1">Verified Competency</span>
            </div>

            <!-- Right: Signature Line -->
            <div class="text-center sm:text-right space-y-1">
                <div class="border-b border-slate-400 pb-1 max-w-[180px] ml-auto">
                    <span class="font-playfair text-sm text-slate-800 italic">DMDP Certified Trainer</span>
                </div>
                <p class="text-xs font-bold text-slate-900">Skills Training Director</p>
                <span class="text-[10px] text-slate-400">Cebu City DMDP</span>
            </div>

        </div>

    </div>

</body>
</html>
