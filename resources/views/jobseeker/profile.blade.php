@extends('layouts.jobseeker')

@section('title', 'My Profile & Skills Matrix - TrabaGo')

@section('content')
@php
    $addr = is_array($details->address ?? null) ? $details->address : (json_decode($details->address ?? '', true) ?: []);
    $edu = is_array($details->education ?? null) ? $details->education : (json_decode($details->education ?? '', true) ?: []);
    $exp = is_array($details->work_experience ?? null) ? $details->work_experience : (json_decode($details->work_experience ?? '', true) ?: []);
    $elig = is_array($details->eligibility ?? null) ? $details->eligibility : (json_decode($details->eligibility ?? '', true) ?: []);
    $langList = is_array($details->language_proficiency ?? null) ? $details->language_proficiency : (json_decode($details->language_proficiency ?? '', true) ?: ['English', 'Tagalog / Filipino', 'Cebuano / Bisaya']);
    
    // Address fallbacks
    $streetVal = $addr['street'] ?? '';
    $brgyVal = $addr['barangay'] ?? '';
    $cityVal = $addr['city'] ?? ($addr['full'] ?? 'Cebu City');
    $provVal = $addr['province'] ?? 'Cebu';
    $zipVal = $addr['zip'] ?? '';

    // Education fallbacks
    $eduLevel = is_array($edu) ? ($edu['level'] ?? '') : '';
    $eduSchool = is_array($edu) ? ($edu['school'] ?? ($edu[1] ?? '')) : '';
    $eduCourse = is_array($edu) ? ($edu['course'] ?? ($edu[0] ?? '')) : (is_string($edu) ? $edu : '');
    $eduYear = is_array($edu) ? ($edu['year_graduated'] ?? '') : '';

    // Experience fallbacks
    $expCompany = is_array($exp) ? ($exp['company'] ?? '') : '';
    $expPosition = is_array($exp) ? ($exp['position'] ?? '') : '';
    $expDuration = is_array($exp) ? ($exp['duration'] ?? '') : '';
    $expDesc = is_array($exp) ? ($exp['description'] ?? '') : '';
    $expBio = is_array($exp) ? ($exp['summary'] ?? '') : ($details->bio ?? '');

    // Eligibility fallbacks
    $eligCS = is_array($elig) ? ($elig['civil_service'] ?? '') : '';
    $eligPRC = is_array($elig) ? ($elig['prc_license'] ?? '') : '';
    $eligTESDA = is_array($elig) ? ($elig['tesda_nc'] ?? '') : '';
    $eligDriver = is_array($elig) ? ($elig['driver_license'] ?? '') : '';
@endphp

<div x-data="{
    skills: ({{ json_encode($skills) }} || []).map(s => (typeof s === 'object' && s !== null) ? (s.skill_name || '') : String(s)).filter(s => s && s.trim() !== '' && s !== '[object Object]' && s !== 'object Object'),
    newSkill: '',
    addSkill(skillName) {
        let raw = (skillName !== undefined && skillName !== null && typeof skillName === 'string') ? skillName : this.newSkill;
        let trimmed = String(raw || '').trim();
        if (trimmed && trimmed !== '[object Object]' && !this.skills.includes(trimmed)) {
            this.skills.push(trimmed);
            this.newSkill = '';
        }
    },
    removeSkill(index) {
        this.skills.splice(index, 1);
    },
    isPwd: {{ ($socialStatus->is_pwd ?? false) ? 'true' : 'false' }},
    is4ps: {{ ($socialStatus->is_4ps ?? false) ? 'true' : 'false' }}
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-5xl space-y-8">
        
        <!-- Profile Banner Card in Emerald Theme -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="h-20 w-20 rounded-3xl bg-gradient-to-tr from-emerald-600 via-emerald-500 to-teal-400 flex items-center justify-center text-white text-3xl font-black shadow-lg shadow-emerald-500/30 ring-4 ring-white/10 shrink-0">
                    {{ strtoupper(substr($user->full_name ?? ($user->email ?? 'U'), 0, 1)) }}
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-2xl sm:text-3xl font-black">{{ $jobseeker->first_name ? ($jobseeker->first_name . ' ' . ($jobseeker->middle_name ? $jobseeker->middle_name . ' ' : '') . $jobseeker->last_name) : $user->full_name }}</h1>
                        <span class="rounded-full bg-emerald-400/20 border border-emerald-400/30 px-2.5 py-0.5 text-[11px] font-bold text-emerald-300">
                            Verified Jobseeker
                        </span>
                        @if($socialStatus->is_pwd)
                            <span class="rounded-full bg-blue-400/20 border border-blue-400/30 px-2.5 py-0.5 text-[11px] font-bold text-blue-300">
                                ♿ PWD Inclusive
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-300">{{ $user->email }} &bull; Member since {{ $user->created_at ? $user->created_at->format('M Y') : '2026' }}</p>
                    <p class="text-xs text-emerald-300 font-semibold">{{ $jobseeker->employment_status ?? 'Actively Seeking Employment' }}</p>
                </div>
            </div>

            <!-- Profile Strength Widget -->
            <div class="shrink-0 bg-white/10 backdrop-blur rounded-2xl p-5 border border-white/10 text-center min-w-[150px]">
                <span class="text-xs font-bold text-emerald-300 uppercase tracking-wider">Profile Strength</span>
                <p class="text-3xl font-black text-emerald-400 mt-0.5">{{ $profileStrength ?? 50 }}%</p>
                <div class="w-full bg-white/20 h-1.5 rounded-full overflow-hidden mt-1.5">
                    <div class="bg-emerald-400 h-full rounded-full" style="width: {{ $profileStrength ?? 50 }}%"></div>
                </div>
                <span class="text-[10px] text-slate-300 mt-1 block">{{ count($skills) }} Skills Active</span>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
                <span>✓</span> {{ session('success') }}
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1">
                <p class="font-black">Please resolve the following input issues:</p>
                <ul class="list-disc list-inside font-medium text-[11px]">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- =================================================================== -->
        <!-- 1. INTERACTIVE SKILLS MATRIX TAGGER (CRITICAL FOR MATCHING) -->
        <!-- =================================================================== -->
        <section class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-100">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800 border border-emerald-200">
                        <span class="h-2 w-2 rounded-full bg-emerald-600 animate-pulse"></span>
                        AI Vector Skills Matrix
                    </span>
                    <h2 class="text-xl font-black text-slate-900 mt-1">My Skills Profile</h2>
                    <p class="text-xs text-slate-500">Add all skills you possess. The Cosine Similarity matching engine uses this to calculate match scores for all job postings.</p>
                </div>

                <span class="text-xs font-bold text-emerald-800 self-start sm:self-auto" x-text="skills.length + ' Skills Active'"></span>
            </div>

            <form action="{{ route('jobseeker.profile.update_skills') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Add Skill Input Field -->
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <input type="text" 
                               x-model="newSkill" 
                               @keydown.enter.prevent="addSkill()"
                               placeholder="Type a skill (e.g. PHP, Customer Service, React, Welding, SQL, Food Prep)..." 
                               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 placeholder-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    </div>
                    <button type="button" 
                            @click="addSkill()"
                            class="rounded-2xl bg-slate-900 hover:bg-emerald-600 text-white px-6 py-3 text-xs font-bold transition-colors shrink-0">
                        + Add Skill
                    </button>
                </div>

                <!-- Hidden inputs for form submission -->
                <template x-for="(s, idx) in skills" :key="idx">
                    <input type="hidden" name="skills[]" :value="s">
                </template>

                <!-- Active Skills Chip Pool in Emerald Theme -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Your Active Skills:</label>
                    <div class="flex flex-wrap gap-2 min-h-[50px] p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <template x-for="(s, idx) in skills" :key="idx">
                            <span class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 border border-emerald-300 px-3 py-1.5 text-xs font-bold text-emerald-950 shadow-2xs group hover:bg-emerald-100 transition-colors">
                                <span x-text="s"></span>
                                <button type="button" @click="removeSkill(idx)" class="text-emerald-700 hover:text-rose-600 font-bold ml-1 text-sm leading-none">&times;</button>
                            </span>
                        </template>
                        <p x-show="skills.length === 0" class="text-xs text-slate-400 italic">No skills added yet. Add some above or click popular suggestions below!</p>
                    </div>
                </div>

                <!-- Suggested Popular In-Demand Skills in Cebu -->
                <div class="space-y-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Quick-Add In-Demand Cebu Skills:</span>
                    <div class="flex flex-wrap gap-1.5">
                        @php
                            $suggested = [
                                'Customer Service', 'Communication', 'English Fluency', 'Technical Support',
                                'PHP', 'Laravel', 'JavaScript', 'React', 'HTML/CSS', 'MySQL', 'SQL Server',
                                'Administrative Support', 'Data Entry', 'Bookkeeping', 'Sales', 'Graphic Design',
                                'Food Service', 'Barista', 'Welding', 'Automotive', 'Warehouse Management',
                                'Housekeeping', 'Electrical Installation', 'Security Operations', 'Nursing / Caregiving'
                            ];
                        @endphp
                        @foreach($suggested as $sug)
                            <button type="button" 
                                    @click="addSkill('{{ $sug }}')"
                                    :disabled="skills.includes('{{ $sug }}')"
                                    class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:border-emerald-400 hover:bg-emerald-50 hover:text-emerald-800 disabled:opacity-30 disabled:cursor-not-allowed transition-all">
                                + {{ $sug }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Save Skills Button -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <p class="text-[11px] text-slate-400">Match scores across all job openings update immediately after saving.</p>
                    <button type="submit" class="rounded-xl bg-emerald-600 hover:bg-emerald-500 px-8 py-3 text-xs font-black text-white shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                        Save Skills Matrix
                    </button>
                </div>
            </form>
        </section>

        <!-- =================================================================== -->
        <!-- 2. COMPREHENSIVE PROFILE EDIT FORM (ALL SECTIONS) -->
        <!-- =================================================================== -->
        <form action="{{ route('jobseeker.profile.update_info') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Section 2.1: Personal & Civil Identification -->
            <section class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                <div class="pb-4 border-b border-slate-100 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg font-bold">
                        👤
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900">Personal & Civil Identification</h2>
                        <p class="text-xs text-slate-500">Official legal identity details verified by Cebu City DMDP.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">First Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name', $jobseeker->first_name ?? '') }}" required
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Middle Name</label>
                        <input type="text" name="middle_name" value="{{ old('middle_name', $jobseeker->middle_name ?? '') }}" placeholder="Middle Name"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Last Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name', $jobseeker->last_name ?? '') }}" required
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Date of Birth</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date', $jobseeker->birth_date ? date('Y-m-d', strtotime($jobseeker->birth_date)) : '') }}"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Sex at Birth</label>
                        <select name="sex" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                            <option value="">-- Select Sex --</option>
                            <option value="Male" {{ old('sex', $jobseeker->sex ?? '') === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('sex', $jobseeker->sex ?? '') === 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Civil Status</label>
                        <select name="civil_status" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                            <option value="">-- Select Civil Status --</option>
                            <option value="Single" {{ old('civil_status', $jobseeker->civil_status ?? '') === 'Single' ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ old('civil_status', $jobseeker->civil_status ?? '') === 'Married' ? 'selected' : '' }}>Married</option>
                            <option value="Widowed" {{ old('civil_status', $jobseeker->civil_status ?? '') === 'Widowed' ? 'selected' : '' }}>Widowed</option>
                            <option value="Separated" {{ old('civil_status', $jobseeker->civil_status ?? '') === 'Separated' ? 'selected' : '' }}>Separated</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Citizenship</label>
                        <input type="text" name="citizenship" value="{{ old('citizenship', $jobseeker->citizenship ?? 'Filipino') }}"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Primary Mobile Number <span class="text-rose-500">*</span></label>
                        <input type="text" name="mobile_number" value="{{ old('mobile_number', $jobseeker->mobile_number ?? '') }}" placeholder="09123456789"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Current Employment Status</label>
                        <select name="employment_status" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                            <option value="Unemployed" {{ old('employment_status', $jobseeker->employment_status ?? '') === 'Unemployed' ? 'selected' : '' }}>Unemployed / Looking for Work</option>
                            <option value="Fresh Graduate" {{ old('employment_status', $jobseeker->employment_status ?? '') === 'Fresh Graduate' ? 'selected' : '' }}>Fresh Graduate / Entry Level</option>
                            <option value="Employed" {{ old('employment_status', $jobseeker->employment_status ?? '') === 'Employed' ? 'selected' : '' }}>Currently Employed (Looking for Change)</option>
                            <option value="Self-Employed" {{ old('employment_status', $jobseeker->employment_status ?? '') === 'Self-Employed' ? 'selected' : '' }}>Self-Employed / Freelancer</option>
                            <option value="Returning OFW" {{ old('employment_status', $jobseeker->employment_status ?? '') === 'Returning OFW' ? 'selected' : '' }}>Returning Overseas Filipino Worker (OFW)</option>
                        </select>
                    </div>
                </div>
            </section>

            <!-- Section 2.2: Address & Location Details (Cebu Metropolitan Area) -->
            <section class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                <div class="pb-4 border-b border-slate-100 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg font-bold">
                        📍
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900">Residential Address & Barangay</h2>
                        <p class="text-xs text-slate-500">Helps employers find candidates residing near job sites across Cebu City and neighboring districts.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">House No. / Street / Building</label>
                        <input type="text" name="address_street" value="{{ old('address_street', $streetVal) }}" placeholder="e.g. 123 Gorordo Ave / Sitio San Roque"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Barangay</label>
                        <input type="text" name="address_barangay" value="{{ old('address_barangay', $brgyVal) }}" placeholder="e.g. Lahug, Mabolo, Guadalupe, Apas"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">City / Municipality</label>
                        <input type="text" name="address_city" value="{{ old('address_city', $cityVal ?: 'Cebu City') }}" placeholder="Cebu City"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Province</label>
                        <input type="text" name="address_province" value="{{ old('address_province', $provVal ?: 'Cebu') }}" placeholder="Cebu"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Postal / Zip Code</label>
                        <input type="text" name="address_zip" value="{{ old('address_zip', $zipVal) }}" placeholder="6000"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                    </div>
                </div>
            </section>

            <!-- Section 2.3: Educational Attainment -->
            <section class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                <div class="pb-4 border-b border-slate-100 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg font-bold">
                        🎓
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900">Educational Background</h2>
                        <p class="text-xs text-slate-500">Highest educational credential, formal training, or vocational program completed.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Highest Level Attained</label>
                        <select name="education_level" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                            <option value="">-- Select Education Level --</option>
                            <option value="College Degree" {{ old('education_level', $eduLevel) === 'College Degree' ? 'selected' : '' }}>College / Bachelor's Degree</option>
                            <option value="TVET / Vocational NC II" {{ old('education_level', $eduLevel) === 'TVET / Vocational NC II' ? 'selected' : '' }}>TVET / TESDA Vocational NC II / NC III</option>
                            <option value="Senior High School" {{ old('education_level', $eduLevel) === 'Senior High School' ? 'selected' : '' }}>Senior High School Graduate</option>
                            <option value="Junior High School" {{ old('education_level', $eduLevel) === 'Junior High School' ? 'selected' : '' }}>Junior High / High School Graduate</option>
                            <option value="Associate Degree" {{ old('education_level', $eduLevel) === 'Associate Degree' ? 'selected' : '' }}>Associate / Technical Diploma</option>
                            <option value="Post-Graduate / Masteral" {{ old('education_level', $eduLevel) === 'Post-Graduate / Masteral' ? 'selected' : '' }}>Master's / Post-Graduate</option>
                            <option value="Elementary" {{ old('education_level', $eduLevel) === 'Elementary' ? 'selected' : '' }}>Elementary Level / Graduate</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">School / College / Training Center</label>
                        <input type="text" name="education_school" value="{{ old('education_school', $eduSchool) }}" placeholder="e.g. University of Cebu, DMDP Center, CIT-U"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Degree / Course / Major</label>
                        <input type="text" name="education_course" value="{{ old('education_course', $eduCourse) }}" placeholder="e.g. BS Information Technology / Automotive NC II"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Year Graduated / Last Attended</label>
                        <input type="text" name="education_year" value="{{ old('education_year', $eduYear) }}" placeholder="e.g. 2024"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                    </div>
                </div>
            </section>

            <!-- Section 2.4: Work Experience & Career Background -->
            <section class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                <div class="pb-4 border-b border-slate-100 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg font-bold">
                        💼
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900">Work Experience & Professional History</h2>
                        <p class="text-xs text-slate-500">Provide details on your most recent employment, OJT, or freelance projects.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Most Recent Company / Employer</label>
                        <input type="text" name="experience_company" value="{{ old('experience_company', $expCompany) }}" placeholder="e.g. Qualfon Cebu, Concentrix, City Gov"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Job Title / Role</label>
                        <input type="text" name="experience_position" value="{{ old('experience_position', $expPosition) }}" placeholder="e.g. Customer Service Rep, Junior Developer"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Duration / Total Experience</label>
                        <select name="experience_duration" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                            <option value="Fresh Graduate / No Experience" {{ old('experience_duration', $expDuration) === 'Fresh Graduate / No Experience' ? 'selected' : '' }}>Fresh Graduate / No Experience</option>
                            <option value="Less than 1 Year" {{ old('experience_duration', $expDuration) === 'Less than 1 Year' ? 'selected' : '' }}>Less than 1 Year</option>
                            <option value="1 - 2 Years" {{ old('experience_duration', $expDuration) === '1 - 2 Years' ? 'selected' : '' }}>1 - 2 Years</option>
                            <option value="3 - 5 Years" {{ old('experience_duration', $expDuration) === '3 - 5 Years' ? 'selected' : '' }}>3 - 5 Years</option>
                            <option value="More than 5 Years" {{ old('experience_duration', $expDuration) === 'More than 5 Years' ? 'selected' : '' }}>More than 5 Years</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Key Duties & Achievements</label>
                    <textarea name="experience_description" rows="2" placeholder="Summary of major responsibilities and skills used..."
                              class="w-full rounded-xl border border-slate-200 p-4 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">{{ old('experience_description', $expDesc) }}</textarea>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Professional Summary / Candidate Bio</label>
                    <textarea name="bio" rows="3" placeholder="Write a short introductory profile summarizing your strengths, career goals, and what you offer to prospective employers..."
                              class="w-full rounded-xl border border-slate-200 p-4 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">{{ old('bio', $expBio) }}</textarea>
                </div>
            </section>

            <!-- Section 2.5: Career Target & Job Preferences -->
            <section class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                <div class="pb-4 border-b border-slate-100 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg font-bold">
                        🎯
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900">Career Targets & Job Preferences</h2>
                        <p class="text-xs text-slate-500">Specifies what types of roles, industries, and salary ranges you are aiming for.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Preferred Target Occupation (Primary)</label>
                        <input type="text" name="occupation1" value="{{ old('occupation1', $preferences->occupation1 ?? '') }}" placeholder="e.g. Software Developer / CSR / Electrician"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Alternative Target Occupation (Secondary)</label>
                        <input type="text" name="occupation2" value="{{ old('occupation2', $preferences->occupation2 ?? '') }}" placeholder="e.g. Technical Support / Admin Assistant"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Preferred Industry / Sector</label>
                        <select name="industry1" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                            <option value="">-- Select Target Industry --</option>
                            <option value="Information Technology & BPO" {{ old('industry1', $preferences->industry1 ?? '') === 'Information Technology & BPO' ? 'selected' : '' }}>Information Technology & BPO</option>
                            <option value="Customer Service & Sales" {{ old('industry1', $preferences->industry1 ?? '') === 'Customer Service & Sales' ? 'selected' : '' }}>Customer Service & Retail</option>
                            <option value="Construction & Technical Trades" {{ old('industry1', $preferences->industry1 ?? '') === 'Construction & Technical Trades' ? 'selected' : '' }}>Construction & Technical Trades</option>
                            <option value="Tourism & Hospitality" {{ old('industry1', $preferences->industry1 ?? '') === 'Tourism & Hospitality' ? 'selected' : '' }}>Tourism, Hotel & Food Service</option>
                            <option value="Healthcare & Caregiving" {{ old('industry1', $preferences->industry1 ?? '') === 'Healthcare & Caregiving' ? 'selected' : '' }}>Healthcare & Caregiving</option>
                            <option value="Transportation & Logistics" {{ old('industry1', $preferences->industry1 ?? '') === 'Transportation & Logistics' ? 'selected' : '' }}>Transportation & Logistics</option>
                            <option value="Administration & Government" {{ old('industry1', $preferences->industry1 ?? '') === 'Administration & Government' ? 'selected' : '' }}>Administration & Public Service</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Preferred Work Location / Modality</label>
                        <select name="preferred_location" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                            <option value="Cebu City (Onsite)" {{ old('preferred_location', $preferences->preferred_location ?? '') === 'Cebu City (Onsite)' ? 'selected' : '' }}>Cebu City (Onsite)</option>
                            <option value="Cebu IT Park / Business Park" {{ old('preferred_location', $preferences->preferred_location ?? '') === 'Cebu IT Park / Business Park' ? 'selected' : '' }}>Cebu IT Park / Business Park</option>
                            <option value="Mandaue / Lapu-Lapu" {{ old('preferred_location', $preferences->preferred_location ?? '') === 'Mandaue / Lapu-Lapu' ? 'selected' : '' }}>Mandaue / Lapu-Lapu District</option>
                            <option value="Hybrid (Work from Home + Onsite)" {{ old('preferred_location', $preferences->preferred_location ?? '') === 'Hybrid (Work from Home + Onsite)' ? 'selected' : '' }}>Hybrid Setup</option>
                            <option value="Remote / Work From Home" {{ old('preferred_location', $preferences->preferred_location ?? '') === 'Remote / Work From Home' ? 'selected' : '' }}>100% Remote / Work from Home</option>
                            <option value="Any Location in Cebu" {{ old('preferred_location', $preferences->preferred_location ?? '') === 'Any Location in Cebu' ? 'selected' : '' }}>Any Location in Cebu</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Expected Monthly Salary Range</label>
                        <select name="salary_expectation" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                            <option value="Minimum Wage (₱12,000 - ₱16,000)" {{ old('salary_expectation', $preferences->salary_expectation ?? '') === 'Minimum Wage (₱12,000 - ₱16,000)' ? 'selected' : '' }}>Minimum Wage (₱12,000 - ₱16,000)</option>
                            <option value="₱16,000 - ₱22,000 / month" {{ old('salary_expectation', $preferences->salary_expectation ?? '') === '₱16,000 - ₱22,000 / month' ? 'selected' : '' }}>₱16,000 - ₱22,000 / month</option>
                            <option value="₱22,000 - ₱30,000 / month" {{ old('salary_expectation', $preferences->salary_expectation ?? '') === '₱22,000 - ₱30,000 / month' ? 'selected' : '' }}>₱22,000 - ₱30,000 / month</option>
                            <option value="₱30,000 - ₱45,000 / month" {{ old('salary_expectation', $preferences->salary_expectation ?? '') === '₱30,000 - ₱45,000 / month' ? 'selected' : '' }}>₱30,000 - ₱45,000 / month</option>
                            <option value="₱45,000+ / month" {{ old('salary_expectation', $preferences->salary_expectation ?? '') === '₱45,000+ / month' ? 'selected' : '' }}>₱45,000+ / month</option>
                            <option value="Negotiable" {{ old('salary_expectation', $preferences->salary_expectation ?? '') === 'Negotiable' ? 'selected' : '' }}>Negotiable / Open</option>
                        </select>
                    </div>
                </div>
            </section>

            <!-- Section 2.6: Social Inclusivity, PWD & 4Ps Support -->
            <section class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                <div class="pb-4 border-b border-slate-100 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg font-bold">
                        🤝
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900">Government Support & Inclusivity (PWD & 4Ps)</h2>
                        <p class="text-xs text-slate-500">Cebu City DMDP priority programs for Persons with Disabilities, 4Ps beneficiaries, and OFWs.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- PWD Checkbox -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_pwd" value="1" x-model="isPwd"
                                   class="h-4 w-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                            <span class="text-xs font-bold text-slate-900">I am a registered Person with Disability (PWD)</span>
                        </label>

                        <div x-show="isPwd" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-200">
                            <div class="space-y-1">
                                <label class="block text-[11px] font-bold text-slate-700 uppercase">Type of Disability / Accommodation Need</label>
                                <select name="pwd_type" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-900 focus:border-emerald-500">
                                    <option value="Visual / Low Vision" {{ old('pwd_type', $socialStatus->pwd_type ?? '') === 'Visual / Low Vision' ? 'selected' : '' }}>Visual / Low Vision</option>
                                    <option value="Hearing / Hard of Hearing" {{ old('pwd_type', $socialStatus->pwd_type ?? '') === 'Hearing / Hard of Hearing' ? 'selected' : '' }}>Hearing / Hard of Hearing</option>
                                    <option value="Orthopedic / Physical Mobility" {{ old('pwd_type', $socialStatus->pwd_type ?? '') === 'Orthopedic / Physical Mobility' ? 'selected' : '' }}>Orthopedic / Physical Mobility</option>
                                    <option value="Speech and Language" {{ old('pwd_type', $socialStatus->pwd_type ?? '') === 'Speech and Language' ? 'selected' : '' }}>Speech and Language</option>
                                    <option value="Psychosocial / Chronic Illness" {{ old('pwd_type', $socialStatus->pwd_type ?? '') === 'Psychosocial / Chronic Illness' ? 'selected' : '' }}>Psychosocial / Chronic Illness</option>
                                    <option value="Learning / Intellectual" {{ old('pwd_type', $socialStatus->pwd_type ?? '') === 'Learning / Intellectual' ? 'selected' : '' }}>Learning / Intellectual</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 4Ps and OFW Status -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="is_4ps" value="1" x-model="is4ps"
                                       class="h-4 w-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                                <span class="text-xs font-bold text-slate-900">4Ps (Pantawid Pamilya) Beneficiary</span>
                            </label>
                            <div x-show="is4ps" x-cloak class="pt-2 border-t border-slate-200">
                                <input type="text" name="household_id" value="{{ old('household_id', $socialStatus->household_id ?? '') }}" placeholder="Household ID Number"
                                       class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-900 focus:border-emerald-500">
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="is_ofw" value="1" {{ ($socialStatus->is_ofw ?? false) ? 'checked' : '' }}
                                       class="h-4 w-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                                <span class="text-xs font-bold text-slate-900">Returning Overseas Filipino Worker (OFW)</span>
                            </label>
                            <p class="text-[11px] text-slate-400 mt-1 pl-7">Qualifies for DMDP local reintegration and skills bridging programs.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 2.7: Professional Licenses & Languages -->
            <section class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                <div class="pb-4 border-b border-slate-100 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg font-bold">
                        📜
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900">Licenses, Eligibilities & Languages</h2>
                        <p class="text-xs text-slate-500">Official government certifications and spoken dialects.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Civil Service Eligibility</label>
                        <input type="text" name="eligibility_civil_service" value="{{ old('eligibility_civil_service', $eligCS) }}" placeholder="e.g. Professional / Sub-Prof"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">PRC Board License</label>
                        <input type="text" name="eligibility_prc_license" value="{{ old('eligibility_prc_license', $eligPRC) }}" placeholder="e.g. Registered Nurse / LPT"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">TESDA NC Certificate</label>
                        <input type="text" name="eligibility_tesda_nc" value="{{ old('eligibility_tesda_nc', $eligTESDA) }}" placeholder="e.g. NC II Barista, SMAW NC II"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Driver's License</label>
                        <input type="text" name="eligibility_driver_license" value="{{ old('eligibility_driver_license', $eligDriver) }}" placeholder="e.g. Non-Pro (Code 1, 2) / Pro"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500">
                    </div>
                </div>

                <div class="space-y-2 pt-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Spoken Languages & Dialects</label>
                    <div class="flex flex-wrap gap-3">
                        @php
                            $availableLangs = ['Cebuano / Bisaya', 'English', 'Tagalog / Filipino', 'Ilonggo / Hiligaynon', 'Waray', 'Mandarin', 'Japanese'];
                        @endphp
                        @foreach($availableLangs as $l)
                            <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-700 cursor-pointer hover:bg-emerald-50 hover:border-emerald-300 transition-colors">
                                <input type="checkbox" name="languages[]" value="{{ $l }}" {{ in_array($l, $langList) ? 'checked' : '' }}
                                       class="h-3.5 w-3.5 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                                <span>{{ $l }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- Save All Profile Info Button -->
            <div class="p-6 rounded-3xl bg-slate-900 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xl">
                <div class="space-y-0.5">
                    <h3 class="text-sm font-black">Ready to submit profile updates?</h3>
                    <p class="text-xs text-slate-400">All changes will immediately reflect on your candidate card for Cebu City employers.</p>
                </div>
                <button type="submit" class="rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 px-8 py-3 text-xs font-black shadow-lg shadow-emerald-500/30 transition-all hover:scale-105">
                    ✓ Save Complete Profile
                </button>
            </div>
        </form>

        <!-- =================================================================== -->
        <!-- 3. OFFICIAL TRAINING CERTIFICATES & CREDENTIALS -->
        <!-- =================================================================== -->
        <section class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-100">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-800 border border-amber-200">
                        ⭐ Official DMDP Credentials
                    </span>
                    <h2 class="text-xl font-black text-slate-900 mt-1">My Certificates of Completion</h2>
                    <p class="text-xs text-slate-500">Government-verified certifications earned through accredited DMDP vocational courses.</p>
                </div>

                <a href="{{ route('jobseeker.documents') }}" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 hover:text-emerald-800 self-start sm:self-auto">
                    Go to Document Hub &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($certificates ?? [] as $cert)
                    <div class="p-5 rounded-2xl bg-gradient-to-tr from-slate-950 to-emerald-950 text-white flex flex-col justify-between gap-4 shadow-md border border-emerald-500/20">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="rounded-full bg-emerald-400/20 text-emerald-300 border border-emerald-400/30 px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider">
                                    {{ ucfirst($cert->training_type ?: 'Online') }} Track
                                </span>
                                <span class="text-[10px] font-mono text-emerald-200 font-bold">{{ $cert->certificate_no }}</span>
                            </div>
                            <h3 class="text-base font-black text-white leading-snug">{{ $cert->course_title }}</h3>
                            <p class="text-[11px] text-slate-300">Awarded on {{ date('F d, Y', strtotime($cert->certificate_issued_at ?? now())) }}</p>
                        </div>

                        <div class="pt-3 border-t border-emerald-800/60 flex items-center justify-between">
                            <span class="text-[10px] font-bold text-emerald-400 flex items-center gap-1">
                                ✓ Verified Credential
                            </span>
                            <a href="{{ route('jobseeker.certificates.preview', $cert->enrollment_id) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-extrabold transition-all shadow-sm">
                                🖨️ View & Download PDF
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-200 p-8 text-center text-slate-400 space-y-2">
                        <div class="text-3xl">🎓</div>
                        <p class="text-xs font-bold text-slate-600">No certificates earned yet</p>
                        <p class="text-[11px] text-slate-400">Complete vocational training programs and pass competency assessments to earn official certificates.</p>
                        <div class="pt-2">
                            <a href="{{ route('jobseeker.training') }}" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 hover:underline">
                                Browse Vocational Courses &rarr;
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>

    </div>
</div>
@endsection
