<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSRP Form 1.REV 3 - {{ $jobseeker->first_name }} {{ $jobseeker->last_name }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; color: #000 !important; font-size: 11px !important; }
            .page-sheet { box-shadow: none !important; border: 1px solid #000 !important; margin: 0 !important; page-break-after: always; }
            @page {
                size: legal portrait;
                margin: 0.8cm;
            }
        }
        .nsrp-table th, .nsrp-table td {
            border: 1px solid #000;
            padding: 3px 6px;
        }
        .nsrp-header-cell {
            background-color: #f1f5f9;
            font-weight: 800;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
    </style>
</head>
<body class="bg-slate-200 min-h-screen p-4 sm:p-8 flex flex-col items-center justify-start text-slate-900 text-xs antialiased">

    <!-- Top Action Bar -->
    <div class="no-print w-full max-w-4xl mb-6 flex items-center justify-between gap-4">
        <a href="{{ route('jpo.evaluations.jobseekers') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-700 hover:text-green-800 bg-white px-4 py-2 rounded-xl shadow-sm border border-slate-300 transition-colors">
            &larr; Back to Jobseeker Evaluations
        </a>

        <div class="flex items-center gap-3">
            <span class="text-xs font-bold text-slate-600">NSRP Form 1.REV 3 &bull; Candidate Ref #{{ $application->application_id }}</span>
            <button onclick="window.print()" class="inline-flex items-center gap-2 text-xs font-black text-white bg-green-700 hover:bg-green-600 px-6 py-2.5 rounded-xl shadow-lg shadow-green-700/30 transition-all cursor-pointer">
                🖨️ Print / Save as PDF
            </button>
        </div>
    </div>

    @php
        $addr = is_array($details?->address) ? $details->address : json_decode($details?->address ?? '[]', true);
        $educ = is_array($details?->education) ? $details->education : json_decode($details?->education ?? '[]', true);
        $work = is_array($details?->work_experience) ? $details->work_experience : json_decode($details?->work_experience ?? '[]', true);
        $elig = is_array($details?->eligibility) ? $details->eligibility : json_decode($details?->eligibility ?? '[]', true);
        $lang = is_array($details?->language_proficiency) ? $details->language_proficiency : json_decode($details?->language_proficiency ?? '[]', true);
        $train = is_array($details?->training_certificates) ? $details->training_certificates : json_decode($details?->training_certificates ?? '[]', true);

        // Calculate age
        $birthDate = $jobseeker->birth_date ? new DateTime($jobseeker->birth_date) : null;
        $age = $birthDate ? $birthDate->diff(new DateTime())->y : 'N/A';
    @endphp

    <!-- Page Sheet (DOLE NSRP Form 1.REV 3 Reproduction) -->
    <div class="page-sheet w-full max-w-4xl bg-white border-2 border-black p-6 sm:p-8 shadow-2xl space-y-3">
        
        <!-- Header Section -->
        <div class="relative border-b-2 border-black pb-3 text-center">
            <span class="absolute left-0 top-0 text-[10px] font-black tracking-tighter">NSRP Form 1.REV 3</span>
            <p class="text-[11px] font-bold">Republic of the Philippines</p>
            <p class="text-[11px] font-bold">Department of Labor and Employment</p>
            <h1 class="text-sm sm:text-base font-black tracking-wide uppercase mt-0.5">NATIONAL SKILLS REGISTRATION PROGRAM</h1>
            <h2 class="text-xs sm:text-sm font-black uppercase tracking-wider">REGISTRATION FORM</h2>
        </div>

        <!-- Instructions Box -->
        <div class="border border-black p-2 text-[9px] leading-tight text-slate-800 bg-slate-50">
            <strong>INSTRUCTIONS:</strong> Please fill out the form legibly with ballpen. Print in block letters. Check appropriate boxes. Please do not leave any items unanswered. Indicate "NA" if not applicable. Submit accomplished form to the Public Employment Service Office (PESO) Manager or staff in your city/municipality/province.
        </div>

        <!-- SECTION I: PERSONAL INFORMATION -->
        <div>
            <div class="bg-black text-white font-black text-[10px] px-2 py-0.5 uppercase tracking-wider">
                I. PERSONAL INFORMATION
            </div>
            <table class="w-full nsrp-table text-[10px] mt-0.5 border-collapse">
                <tbody>
                    <tr>
                        <td colspan="2" class="w-1/4">
                            <span class="text-[8px] text-slate-500 block uppercase font-bold">LAST NAME</span>
                            <span class="font-black text-xs uppercase">{{ $jobseeker->last_name }}</span>
                        </td>
                        <td colspan="2" class="w-1/4">
                            <span class="text-[8px] text-slate-500 block uppercase font-bold">FIRST NAME</span>
                            <span class="font-black text-xs uppercase">{{ $jobseeker->first_name }}</span>
                        </td>
                        <td class="w-1/4">
                            <span class="text-[8px] text-slate-500 block uppercase font-bold">MIDDLE NAME</span>
                            <span class="font-bold text-xs uppercase">{{ $jobseeker->middle_name ?: 'N/A' }}</span>
                        </td>
                        <td class="w-1/4">
                            <span class="text-[8px] text-slate-500 block uppercase font-bold">SUFFIX (Sr., Jr.)</span>
                            <span class="font-bold text-xs uppercase">{{ $jobseeker->suffix ?: 'N/A' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="text-[8px] text-slate-500 block uppercase font-bold">DATE OF BIRTH (mm/dd/yyyy)</span>
                            <span class="font-bold">{{ $jobseeker->birth_date ? date('m/d/Y', strtotime($jobseeker->birth_date)) : 'N/A' }}</span>
                        </td>
                        <td>
                            <span class="text-[8px] text-slate-500 block uppercase font-bold">AGE</span>
                            <span class="font-bold">{{ $age }}</span>
                        </td>
                        <td colspan="3" rowspan="2" class="align-top">
                            <span class="text-[8px] text-slate-500 block uppercase font-bold">PRESENT ADDRESS</span>
                            <div class="text-[10px] space-y-0.5">
                                <div><span class="text-[8px] text-slate-500 font-semibold">House No./Street:</span> <span class="font-bold">{{ $addr['present_house'] ?? ($addr['house_no'] ?? ($jobseeker->address ?? 'Cebu City')) }}</span></div>
                                <div><span class="text-[8px] text-slate-500 font-semibold">Barangay:</span> <span class="font-bold">{{ $addr['present_barangay'] ?? ($addr['barangay'] ?? 'N/A') }}</span></div>
                                <div><span class="text-[8px] text-slate-500 font-semibold">Municipality/City:</span> <span class="font-bold">{{ $addr['present_city'] ?? ($addr['city'] ?? 'Cebu City') }}</span></div>
                                <div><span class="text-[8px] text-slate-500 font-semibold">Province:</span> <span class="font-bold">{{ $addr['present_province'] ?? ($addr['province'] ?? 'Cebu') }}</span></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="text-[8px] text-slate-500 block uppercase font-bold">SEX</span>
                            <span class="font-bold uppercase">[{{ strtolower($jobseeker->sex) == 'male' ? 'X' : ' ' }}] Male &nbsp; [{{ strtolower($jobseeker->sex) == 'female' ? 'X' : ' ' }}] Female</span>
                        </td>
                        <td colspan="2">
                            <span class="text-[8px] text-slate-500 block uppercase font-bold">CIVIL STATUS</span>
                            <span class="font-bold text-[9px]">{{ ucfirst($jobseeker->civil_status ?? 'Single') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="text-[8px] text-slate-500 block uppercase font-bold">PLACE OF BIRTH</span>
                            <span class="font-bold">{{ $jobseeker->place_of_birth ?: 'Cebu City' }}</span>
                        </td>
                        <td>
                            <span class="text-[8px] text-slate-500 block uppercase font-bold">CITIZENSHIP</span>
                            <span class="font-bold">{{ $jobseeker->citizenship ?: 'Filipino' }}</span>
                        </td>
                        <td colspan="3" rowspan="2" class="align-top">
                            <div class="flex items-center justify-between">
                                <span class="text-[8px] text-slate-500 block uppercase font-bold">PERMANENT ADDRESS</span>
                                <span class="text-[8px]">[X] Same as Present</span>
                            </div>
                            <div class="text-[10px] space-y-0.5 mt-0.5">
                                <div><span class="text-[8px] text-slate-500 font-semibold">House No./Street:</span> <span class="font-bold">{{ $addr['permanent_house'] ?? ($addr['house_no'] ?? ($jobseeker->address ?? 'Cebu City')) }}</span></div>
                                <div><span class="text-[8px] text-slate-500 font-semibold">Barangay:</span> <span class="font-bold">{{ $addr['permanent_barangay'] ?? ($addr['barangay'] ?? 'N/A') }}</span></div>
                                <div><span class="text-[8px] text-slate-500 font-semibold">City & Province:</span> <span class="font-bold">{{ $addr['permanent_city'] ?? 'Cebu City' }}, Cebu</span></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="text-[8px] text-slate-500 block uppercase font-bold">HEIGHT</span>
                            <span class="font-bold">{{ $details->height ?? 'N/A' }}</span>
                        </td>
                        <td colspan="2">
                            <span class="text-[8px] text-slate-500 block uppercase font-bold">WEIGHT</span>
                            <span class="font-bold">{{ $details->weight ?? 'N/A' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <span class="text-[8px] text-slate-500 block uppercase font-bold">MOBILE NUMBER & EMAIL</span>
                            <span class="font-bold">📱 {{ $jobseeker->mobile_number }} &bull; ✉️ {{ $jobseeker->email }}</span>
                        </td>
                        <td colspan="3">
                            <span class="text-[8px] text-slate-500 block uppercase font-bold">DISABILITY / SPECIAL NEEDS</span>
                            @if($socialStatus && $socialStatus->is_pwd)
                                <span class="font-bold text-purple-900">[X] PWD: {{ $socialStatus->pwd_type ?: 'Yes' }}</span>
                            @else
                                <span class="text-slate-600">[ ] None / Not Applicable</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <span class="text-[8px] text-slate-500 block uppercase font-bold">EMPLOYMENT STATUS</span>
                            <span class="font-bold uppercase">{{ $jobseeker->employment_status ?: 'Jobseeker / Unemployed' }}</span>
                        </td>
                        <td colspan="3">
                            <span class="text-[8px] text-slate-500 block uppercase font-bold">SOCIAL PROGRAM / 4Ps / OFW</span>
                            <span class="font-bold">
                                4Ps Beneficiary: [{{ ($socialStatus && $socialStatus->is_4ps) ? 'X' : ' ' }}] Yes 
                                @if($socialStatus && $socialStatus->is_4ps && $socialStatus->household_id) (HH #{{ $socialStatus->household_id }}) @endif
                                &bull; OFW: [{{ ($socialStatus && $socialStatus->is_ofw) ? 'X' : ' ' }}]
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- SECTION II: JOB PREFERENCE -->
        <div>
            <div class="bg-black text-white font-black text-[10px] px-2 py-0.5 uppercase tracking-wider">
                II. JOB PREFERENCE
            </div>
            <table class="w-full nsrp-table text-[10px] mt-0.5 border-collapse">
                <thead>
                    <tr class="nsrp-header-cell">
                        <th class="w-1/2">PREFERRED OCCUPATION</th>
                        <th class="w-1/2">PREFERRED INDUSTRY</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1. <span class="font-bold">{{ $jobseeker->preferred_occupation_1 ?? ($job->title ?? 'General Services') }}</span></td>
                        <td>1. <span class="font-bold">{{ $jobseeker->preferred_industry_1 ?? ($job->industry ?? 'IT-BPM / Services') }}</span></td>
                    </tr>
                    <tr>
                        <td>2. <span class="font-medium">{{ $jobseeker->preferred_occupation_2 ?? 'Technical Staff / Specialist' }}</span></td>
                        <td>2. <span class="font-medium">{{ $jobseeker->preferred_industry_2 ?? 'Commerce & Trade' }}</span></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="text-[8px] text-slate-500 block uppercase font-bold">PREFERRED WORK LOCATION & EXPECTED SALARY</span>
                            <span class="font-bold">Local: Cebu City / Metro Cebu &bull; Salary Expectation: ₱{{ number_format($jobseeker->expected_salary ?? 20000, 2) }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- SECTION III: EDUCATIONAL BACKGROUND -->
        <div>
            <div class="bg-black text-white font-black text-[10px] px-2 py-0.5 uppercase tracking-wider">
                III. EDUCATIONAL BACKGROUND
            </div>
            <table class="w-full nsrp-table text-[10px] mt-0.5 border-collapse">
                <thead>
                    <tr class="nsrp-header-cell">
                        <th>LEVEL</th>
                        <th>SCHOOL / UNIVERSITY</th>
                        <th>COURSE / PROGRAM</th>
                        <th>YEAR GRADUATED</th>
                    </tr>
                </thead>
                <tbody>
                    @if(is_array($educ) && count($educ) > 0)
                        @foreach($educ as $ed)
                            <tr>
                                <td class="font-bold">{{ $ed['level'] ?? 'Tertiary / College' }}</td>
                                <td>{{ $ed['school'] ?? ($ed['institution'] ?? 'N/A') }}</td>
                                <td>{{ $ed['course'] ?? ($ed['degree'] ?? 'N/A') }}</td>
                                <td>{{ $ed['year_graduated'] ?? ($ed['year'] ?? 'N/A') }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="font-bold">College / Graduate</td>
                            <td>University / Institution</td>
                            <td>Bachelor's Degree Program</td>
                            <td>Completed</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- SECTION IV: TECHNICAL / VOCATIONAL AND OTHER TRAINING -->
        <div>
            <div class="bg-black text-white font-black text-[10px] px-2 py-0.5 uppercase tracking-wider">
                IV. TECHNICAL/VOCATIONAL AND OTHER TRAINING
            </div>
            <table class="w-full nsrp-table text-[10px] mt-0.5 border-collapse">
                <thead>
                    <tr class="nsrp-header-cell">
                        <th>TRAINING COURSE</th>
                        <th>DURATION</th>
                        <th>TRAINING INSTITUTION</th>
                        <th>CERTIFICATES RECEIVED</th>
                    </tr>
                </thead>
                <tbody>
                    @if(is_array($train) && count($train) > 0)
                        @foreach($train as $tr)
                            <tr>
                                <td class="font-bold">{{ $tr['name'] ?? ($tr['course'] ?? 'Vocational Training') }}</td>
                                <td>{{ $tr['duration'] ?? 'Completed' }}</td>
                                <td>{{ $tr['institution'] ?? 'DMDP / TESDA Center' }}</td>
                                <td>{{ $tr['category'] ?? 'NC-II Certificate' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="font-bold">DMDP Skills Training Module</td>
                            <td>Standard Hours</td>
                            <td>Cebu City DMDP Center</td>
                            <td>Certificate of Completion</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- SECTION VI: WORK EXPERIENCE -->
        <div>
            <div class="bg-black text-white font-black text-[10px] px-2 py-0.5 uppercase tracking-wider">
                VI. WORK EXPERIENCE (Recent Employment History)
            </div>
            <table class="w-full nsrp-table text-[10px] mt-0.5 border-collapse">
                <thead>
                    <tr class="nsrp-header-cell">
                        <th>COMPANY NAME</th>
                        <th>POSITION HELD</th>
                        <th>INCLUSIVE DATES</th>
                        <th>STATUS OF APPOINTMENT</th>
                    </tr>
                </thead>
                <tbody>
                    @if(is_array($work) && count($work) > 0)
                        @foreach($work as $w)
                            <tr>
                                <td class="font-bold">{{ $w['company'] ?? ($w['company_name'] ?? 'Company') }}</td>
                                <td>{{ $w['position'] ?? 'Staff' }}</td>
                                <td>{{ $w['dates'] ?? ($w['duration'] ?? 'N/A') }}</td>
                                <td>{{ $w['status'] ?? 'Permanent' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="font-bold">Prior Industry Experience</td>
                            <td>Specialist / Assistant</td>
                            <td>Previous 2 Years</td>
                            <td>Regular / Contractual</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @php
            $skillNames = $skills->pluck('skill_name')->map(fn($s) => strtolower(trim($s)))->toArray();
            $hasSkill = function($name) use ($skillNames) {
                $n = strtolower(trim($name));
                foreach ($skillNames as $sn) {
                    if ($sn === $n || str_contains($sn, $n) || str_contains($n, $sn)) {
                        return true;
                    }
                }
                return false;
            };

            $centurySkills1 = ['Innovation', 'Team Work', 'Multitasking', 'Work Ethics', 'Self Motivation'];
            $centurySkills2 = ['Creative Problem Solving', 'Problem Solving', 'Critical Thinking', 'Decision Making', 'Stress Tolerance'];
            $centurySkills3 = ['Planning and Organizing', 'Social Perceptiveness', 'English Functional Skills', 'English Comprehension', 'Math Functional Skill'];

            $techSkills1 = ['Carpentry', 'Masonry', 'Welding', 'Auto Mechanic'];
            $techSkills2 = ['Plumbing', 'Driving', 'Gardening', 'Tailoring'];
            $techSkills3 = ['Photography', 'Hairdressing', 'Cooking', 'Baking'];

            // Find if there are any other technical skills registered outside predefined list
            $rawSkillList = collect($skills)->map(function($item) {
                if (is_object($item)) {
                    return $item->skill_name ?? (string)$item;
                }
                return (string)$item;
            })->filter()->values();

            $predefinedSkills = array_map('strtolower', array_merge($centurySkills1, $centurySkills2, $centurySkills3, $techSkills1, $techSkills2, $techSkills3));
            $otherSkills = $rawSkillList->filter(function($s) use ($predefinedSkills) {
                return !empty($s) && !in_array(strtolower(trim($s)), $predefinedSkills);
            });
            $otherSkillName = $otherSkills->first() ?: '';
        @endphp

        <!-- VII. 21st CENTURY SKILLS - Check five (5) skills you possess (self-assesment) -->
        <div>
            <div class="bg-black text-white font-black text-[10px] px-2 py-0.5 uppercase tracking-wider">
                VII. 21st CENTURY SKILLS - Check five (5) skills you possess (self-assesment)
            </div>
            <div class="border border-black p-2.5 text-[10px]">
                <div class="grid grid-cols-3 gap-3">
                    <div class="space-y-1">
                        @foreach($centurySkills1 as $s)
                            <div class="flex items-center gap-1.5">
                                <span class="font-mono font-bold">{{ $hasSkill($s) ? '[X]' : '[  ]' }}</span>
                                <span class="{{ $hasSkill($s) ? 'font-black text-black' : 'text-slate-800' }}">{{ $s }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="space-y-1">
                        @foreach($centurySkills2 as $s)
                            <div class="flex items-center gap-1.5">
                                <span class="font-mono font-bold">{{ $hasSkill($s) ? '[X]' : '[  ]' }}</span>
                                <span class="{{ $hasSkill($s) ? 'font-black text-black' : 'text-slate-800' }}">{{ $s }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="space-y-1">
                        @foreach($centurySkills3 as $s)
                            <div class="flex items-center gap-1.5">
                                <span class="font-mono font-bold">{{ $hasSkill($s) ? '[X]' : '[  ]' }}</span>
                                <span class="{{ $hasSkill($s) ? 'font-black text-black' : 'text-slate-800' }}">{{ $s }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- IX. TECHNICAL SKILLS ACQUIRED WITHOUT FORMAL TRAINING -->
        <div>
            <div class="bg-black text-white font-black text-[10px] px-2 py-0.5 uppercase tracking-wider">
                IX. TECHNICAL SKILLS ACQUIRED WITHOUT FORMAL TRAINING
            </div>
            <div class="border border-black p-2.5 text-[10px]">
                <div class="grid grid-cols-4 gap-2">
                    <div class="space-y-1">
                        @foreach($techSkills1 as $s)
                            <div class="flex items-center gap-1.5">
                                <span class="font-mono font-bold">{{ $hasSkill($s) ? '[X]' : '[  ]' }}</span>
                                <span class="{{ $hasSkill($s) ? 'font-black text-black' : 'text-slate-800' }}">{{ $s }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="space-y-1">
                        @foreach($techSkills2 as $s)
                            <div class="flex items-center gap-1.5">
                                <span class="font-mono font-bold">{{ $hasSkill($s) ? '[X]' : '[  ]' }}</span>
                                <span class="{{ $hasSkill($s) ? 'font-black text-black' : 'text-slate-800' }}">{{ $s }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="space-y-1">
                        @foreach($techSkills3 as $s)
                            <div class="flex items-center gap-1.5">
                                <span class="font-mono font-bold">{{ $hasSkill($s) ? '[X]' : '[  ]' }}</span>
                                <span class="{{ $hasSkill($s) ? 'font-black text-black' : 'text-slate-800' }}">{{ $s }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center gap-1.5">
                            <span class="font-mono font-bold">{{ $otherSkillName ? '[X]' : '[  ]' }}</span>
                            <span>Others: <span class="{{ $otherSkillName ? 'underline font-black text-black' : 'text-slate-500' }}">{{ $otherSkillName ?: '____________' }}</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CERTIFICATION / AUTHORIZATION -->
        <div class="border border-black p-3 text-[9px] space-y-3">
            <p class="leading-relaxed">
                <strong>CERTIFICATION / AUTHORIZATION:</strong> This is to certify that all data/information that I have provided in this form are true to the best of my knowledge. This is also to authorize the DOLE / DMDP to include my profile in the Skills Registry System, which is maintained in PhilJobNet.
            </p>
            <div class="flex items-end justify-between pt-4">
                <div class="text-center">
                    <p class="font-black text-[10px] underline uppercase">{{ $jobseeker->first_name }} {{ $jobseeker->last_name }}</p>
                    <p class="text-[8px] text-slate-500 uppercase">Signature of Applicant</p>
                </div>
                <div class="text-center">
                    <p class="font-bold text-[10px]">{{ date('F d, Y') }}</p>
                    <p class="text-[8px] text-slate-500 uppercase">Date Filed</p>
                </div>
            </div>
        </div>

        <!-- FOR USE OF PESO / DMDP ONLY -->
        <div class="border-2 border-black p-3 bg-slate-50 text-[9px] space-y-2">
            <div class="flex items-center justify-between border-b border-black pb-1">
                <strong class="uppercase font-black text-[10px]">FOR USE OF PESO / DMDP ONLY (DO NOT WRITE BELOW THIS LINE)</strong>
                <span class="font-bold">Evaluation Ref #{{ $application->application_id }}</span>
            </div>
            <div class="grid grid-cols-2 gap-4 pt-1">
                <div>
                    <span class="font-bold block">Assessed Public Employment Services:</span>
                    <p>[X] Regular Placement Referral &nbsp; [ ] SPES &nbsp; [ ] JobStart &nbsp; [ ] TUPAD</p>
                    <p class="mt-1">Target Vacancy: <strong>{{ $job->title }}</strong> ({{ $employer->company_name ?? 'Company' }})</p>
                </div>
                <div class="text-right">
                    <span class="font-bold block">Assessed by DMDP Job Placement Officer:</span>
                    <p class="font-black text-[10px] underline mt-3 uppercase">{{ Auth::user()->email }}</p>
                    <p class="text-[8px] text-slate-500">Signature over Printed Name of JPO Assessor &bull; Date: {{ date('M d, Y') }}</p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
