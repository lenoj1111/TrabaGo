@extends('layouts.jobseeker')

@section('title', 'My Profile & Account Settings - TrabaGo')

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
    activeTab: '{{ request('tab', ($errors->has('current_password') || $errors->has('password') ? 'security' : 'view')) }}',
    showCurrentPass: false,
    showNewPass: false,
    showConfirmPass: false,
    skills: ({{ json_encode($skills) }} || []).map(s => (typeof s === 'object' && s !== null) ? (s.skill_name || '') : String(s)).filter(s => s && s.trim() !== '' && s !== '[object Object]' && s !== 'object Object'),
    newSkill: '',
    addSkill(skillName) {
        let raw = (skillName !== undefined && skillName !== null && typeof skillName === 'string') ? skillName : this.newSkill;
        let trimmed = String(raw || '').trim();
        if (trimmed && trimmed !== '[object Object]' && !this.skills.some(s => s.toLowerCase() === trimmed.toLowerCase())) {
            this.skills.push(trimmed);
            this.newSkill = '';
        }
    },
    removeSkill(index) {
        this.skills.splice(index, 1);
    },
    toggleSkill(skillName) {
        let trimmed = String(skillName || '').trim();
        if (!trimmed) return;
        let idx = this.skills.findIndex(s => s.toLowerCase() === trimmed.toLowerCase());
        if (idx > -1) {
            this.skills.splice(idx, 1);
        } else {
            this.skills.push(trimmed);
        }
    },
    hasSkill(skillName) {
        let trimmed = String(skillName || '').trim().toLowerCase();
        return this.skills.some(s => s.toLowerCase() === trimmed);
    },
    isPwd: {{ ($socialStatus->is_pwd ?? false) ? 'true' : 'false' }},
    is4ps: {{ ($socialStatus->is_4ps ?? false) ? 'true' : 'false' }}
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-5xl space-y-8">
        
        <!-- Profile Banner Card in green Theme -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-green-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-green-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="h-20 w-20 rounded-3xl bg-gradient-to-tr from-green-600 via-green-500 to-green-400 flex items-center justify-center text-white text-3xl font-black shadow-lg shadow-green-500/30 ring-4 ring-white/10 shrink-0">
                    {{ strtoupper(substr($user->full_name ?? ($user->email ?? 'U'), 0, 1)) }}
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-2xl sm:text-3xl font-black">{{ $jobseeker->first_name ? ($jobseeker->first_name . ' ' . ($jobseeker->middle_name ? $jobseeker->middle_name . ' ' : '') . $jobseeker->last_name) : $user->full_name }}</h1>
                        @if($jobseeker->isEmployed())
                            <span class="rounded-full bg-green-500 text-white px-3 py-0.5 text-[11px] font-black shadow-sm flex items-center gap-1">
                                <span>💼</span> Employed
                            </span>
                        @else
                            <span class="rounded-full bg-green-400/20 border border-green-400/30 px-2.5 py-0.5 text-[11px] font-bold text-green-300">
                                Verified Jobseeker
                            </span>
                        @endif
                        @if($socialStatus->is_pwd)
                            <span class="rounded-full bg-blue-400/20 border border-blue-400/30 px-2.5 py-0.5 text-[11px] font-bold text-blue-300">
                                ♿ PWD Inclusive
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-300">{{ $user->email }} &bull; Member since {{ $user->created_at ? $user->created_at->format('M Y') : '2026' }}</p>
                    @if($jobseeker->isEmployed())
                        <div class="pt-0.5 space-y-0.5">
                            <p class="text-xs text-green-300 font-extrabold flex items-center gap-1.5">
                                <span class="h-2 w-2 rounded-full bg-green-400 animate-pulse"></span>
                                Tagged as Employed
                            </p>
                            @if($jobseeker->hired_company)
                                <p class="text-xs text-white font-bold flex items-center gap-1.5">
                                    <svg class="h-3.5 w-3.5 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    Hired at: <span class="underline decoration-green-400 decoration-2 font-black text-green-200">{{ $jobseeker->hired_company }}</span>
                                </p>
                            @endif
                        </div>
                    @else
                        <p class="text-xs text-green-300 font-semibold">{{ $jobseeker->employment_status ?? 'Actively Seeking Employment' }}</p>
                    @endif
                </div>
            </div>

            <!-- Profile Strength Widget -->
            <div class="shrink-0 bg-white/10 backdrop-blur rounded-2xl p-5 border border-white/10 text-center min-w-[150px]">
                <span class="text-xs font-bold text-green-300 uppercase tracking-wider">Profile Strength</span>
                <p class="text-3xl font-black text-green-400 mt-0.5">{{ $profileStrength ?? 50 }}%</p>
                <div class="w-full bg-white/20 h-1.5 rounded-full overflow-hidden mt-1.5">
                    <div class="bg-green-400 h-full rounded-full" style="width: {{ $profileStrength ?? 50 }}%"></div>
                </div>
                <span class="text-[10px] text-slate-300 mt-1 block" x-text="skills.length + ' Skills Active'">{{ count($skills) }} Skills Active</span>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-green-50 border border-green-200 text-green-800 text-xs font-bold flex items-center gap-2">
                <span>✓</span> {{ session('success') }}
            </div>
        @endif

        @if(session('info'))
            <div class="p-4 rounded-2xl bg-green-50 border border-green-200 text-green-800 text-xs font-bold flex items-center gap-2">
                <span>ℹ️</span> {{ session('info') }}
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
        <!-- NAVIGATION TABS: VIEW PROFILE, UPDATE PROFILE, SKILLS, RESET PASSWORD -->
        <!-- =================================================================== -->
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-4">
            <button type="button" @click="activeTab = 'view'"
                    class="px-5 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer"
                    :class="activeTab === 'view' ? 'bg-slate-900 text-white shadow-md' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
                <span>👁️</span>
                <span>My Profile</span>
            </button>

            <button type="button" @click="activeTab = 'edit'"
                    class="px-5 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer"
                    :class="activeTab === 'edit' ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
                <span>✏️</span>
                <span>Update Profile</span>
            </button>

            <button type="button" @click="activeTab = 'skills'"
                    class="px-5 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer"
                    :class="activeTab === 'skills' ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
                <span>⚡</span>
                <span>Skills Matrix</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-extrabold"
                      :class="activeTab === 'skills' ? 'bg-green-800 text-white' : 'bg-slate-100 text-slate-700'"
                      x-text="skills.length"></span>
            </button>

            <button type="button" @click="activeTab = 'security'"
                    class="px-5 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer"
                    :class="activeTab === 'security' ? 'bg-amber-600 text-white shadow-md shadow-amber-600/20' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
                <span>🔒</span>
                <span>Reset Password</span>
            </button>
        </div>

        <!-- =================================================================== -->
        <!-- TAB 1: VIEW PROFILE (READ-ONLY OVERVIEW) -->
        <!-- =================================================================== -->
        <div x-show="activeTab === 'view'" class="space-y-6">
            
            <!-- Quick Action Callout Header -->
            <div class="rounded-2xl bg-white border border-slate-200 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
                <div>
                    <h2 class="text-base font-black text-slate-900">Jobseeker Profile Overview</h2>
                    <p class="text-xs text-slate-500">This is how employers and DMDP placement officers view your qualifications.</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" @click="activeTab = 'edit'"
                            class="px-4 py-2 rounded-xl bg-green-600 hover:bg-green-500 text-white text-xs font-black shadow-sm transition-colors flex items-center gap-1.5">
                        <span>✏️</span> Edit Profile
                    </button>
                    <button type="button" @click="activeTab = 'security'"
                            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors flex items-center gap-1.5">
                        <span>🔒</span> Reset Password
                    </button>
                </div>
            </div>

            <!-- Grid: Personal & Residential Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Personal Info Card -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <div class="h-9 w-9 rounded-xl bg-green-50 text-green-700 flex items-center justify-center font-bold">
                            👤
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-900">Personal Information</h3>
                            <p class="text-[11px] text-slate-400">Basic identification data</p>
                        </div>
                    </div>

                    <dl class="grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase">Full Name</dt>
                            <dd class="font-bold text-slate-900 mt-0.5">
                                {{ $jobseeker->first_name ? ($jobseeker->first_name . ' ' . ($jobseeker->middle_name ? $jobseeker->middle_name . ' ' : '') . $jobseeker->last_name) : $user->full_name }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase">Email Address</dt>
                            <dd class="font-bold text-slate-900 mt-0.5 truncate">{{ $user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase">Mobile Number</dt>
                            <dd class="font-bold text-slate-900 mt-0.5">{{ $jobseeker->mobile_number ?: 'Not provided' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase">Date of Birth</dt>
                            <dd class="font-bold text-slate-900 mt-0.5">
                                {{ $jobseeker->birth_date ? date('M d, Y', strtotime($jobseeker->birth_date)) : 'Not provided' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase">Sex / Gender</dt>
                            <dd class="font-bold text-slate-900 mt-0.5">{{ $jobseeker->sex ?: 'Not specified' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase">Civil Status</dt>
                            <dd class="font-bold text-slate-900 mt-0.5">{{ $jobseeker->civil_status ?: 'Single' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase">Citizenship</dt>
                            <dd class="font-bold text-slate-900 mt-0.5">{{ $jobseeker->citizenship ?: 'Filipino' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase">Employment Status</dt>
                            @if($jobseeker->isEmployed())
                                <dd class="mt-0.5 space-y-1.5">
                                    <span class="inline-flex items-center gap-1 font-black text-xs text-green-800 bg-green-100 border border-green-300 px-2.5 py-0.5 rounded-lg">
                                        💼 Employed
                                    </span>
                                    @if($jobseeker->hired_company)
                                        <div class="text-[11px] text-slate-700 font-semibold flex items-center gap-1">
                                            <span class="text-slate-400 font-normal">at</span>
                                            <span class="font-extrabold text-slate-950">{{ $jobseeker->hired_company }}</span>
                                        </div>
                                    @endif
                                    @php
                                        $activeApp = $jobseeker->activeHiredApplication;
                                    @endphp
                                    @if($activeApp && $activeApp->resignation_status === 'requested')
                                        <div class="text-[10px] font-bold text-amber-800 bg-amber-50 border border-amber-200 rounded-lg p-1.5 flex items-center gap-1">
                                            <span>⏳</span> Resignation Pending Approval
                                        </div>
                                    @else
                                        <div class="pt-1">
                                            <a href="{{ route('jobseeker.applications') }}" class="text-[11px] font-bold text-green-700 hover:text-green-800 underline">
                                                Request Resignation to Apply for Jobs &rarr;
                                            </a>
                                        </div>
                                    @endif
                                </dd>
                            @else
                                <dd class="font-bold text-green-700 mt-0.5">{{ $jobseeker->employment_status ?: 'Unemployed' }}</dd>
                            @endif
                        </div>
                    </dl>
                </div>

                <!-- Residential Address Card -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <div class="h-9 w-9 rounded-xl bg-green-50 text-green-700 flex items-center justify-center font-bold">
                            📍
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-900">Residential Address</h3>
                            <p class="text-[11px] text-slate-400">Cebu Metropolitan location</p>
                        </div>
                    </div>

                    <dl class="space-y-3 text-xs">
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase">Street / Building Address</dt>
                            <dd class="font-bold text-slate-900 mt-0.5">{{ $streetVal ?: 'Not provided' }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-[10px] font-bold text-slate-400 uppercase">Barangay</dt>
                                <dd class="font-bold text-slate-900 mt-0.5">{{ $brgyVal ?: 'Not specified' }}</dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-bold text-slate-400 uppercase">City / Municipality</dt>
                                <dd class="font-bold text-slate-900 mt-0.5">{{ $cityVal ?: 'Cebu City' }}</dd>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-[10px] font-bold text-slate-400 uppercase">Province</dt>
                                <dd class="font-bold text-slate-900 mt-0.5">{{ $provVal ?: 'Cebu' }}</dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-bold text-slate-400 uppercase">Postal / Zip Code</dt>
                                <dd class="font-bold text-slate-900 mt-0.5">{{ $zipVal ?: '6000' }}</dd>
                            </div>
                        </div>
                    </dl>
                </div>

            </div>

            <!-- Education & Experience Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Education Card -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <div class="h-9 w-9 rounded-xl bg-green-50 text-green-700 flex items-center justify-center font-bold">
                            🎓
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-900">Educational Attainment</h3>
                            <p class="text-[11px] text-slate-400">Academic and vocational background</p>
                        </div>
                    </div>

                    <dl class="space-y-3 text-xs">
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase">Highest Level</dt>
                            <dd class="font-bold text-slate-900 mt-0.5">{{ $eduLevel ?: 'College Degree' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase">School / Institution</dt>
                            <dd class="font-bold text-slate-900 mt-0.5">{{ $eduSchool ?: 'Not provided' }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-[10px] font-bold text-slate-400 uppercase">Degree / Course</dt>
                                <dd class="font-bold text-slate-900 mt-0.5">{{ $eduCourse ?: 'Not provided' }}</dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-bold text-slate-400 uppercase">Year Graduated</dt>
                                <dd class="font-bold text-slate-900 mt-0.5">{{ $eduYear ?: 'Not provided' }}</dd>
                            </div>
                        </div>
                    </dl>
                </div>

                <!-- Experience & Bio Card -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <div class="h-9 w-9 rounded-xl bg-green-50 text-green-700 flex items-center justify-center font-bold">
                            💼
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-900">Work Experience</h3>
                            <p class="text-[11px] text-slate-400">Career history and summary</p>
                        </div>
                    </div>

                    <dl class="space-y-3 text-xs">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-[10px] font-bold text-slate-400 uppercase">Recent Company</dt>
                                <dd class="font-bold text-slate-900 mt-0.5">{{ $expCompany ?: 'None listed' }}</dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-bold text-slate-400 uppercase">Role / Position</dt>
                                <dd class="font-bold text-slate-900 mt-0.5">{{ $expPosition ?: 'Entry-level' }}</dd>
                            </div>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase">Total Experience</dt>
                            <dd class="font-bold text-slate-900 mt-0.5">{{ $expDuration ?: 'Fresh graduate' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase">Professional Summary / Bio</dt>
                            <dd class="font-medium text-slate-700 mt-0.5 leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-100">
                                {{ $expBio ?: 'No professional bio added yet.' }}
                            </dd>
                        </div>
                    </dl>
                </div>

            </div>

            <!-- Eligibilities, Languages & Inclusivity -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Eligibilities Card -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <div class="h-9 w-9 rounded-xl bg-green-50 text-green-700 flex items-center justify-center font-bold">
                            📜
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-900">Eligibilities & Licenses</h3>
                            <p class="text-[11px] text-slate-400">Official government credentials</p>
                        </div>
                    </div>

                    <dl class="space-y-2 text-xs">
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase">Civil Service</dt>
                            <dd class="font-bold text-slate-900 mt-0.5">{{ $eligCS ?: 'None' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase">PRC License</dt>
                            <dd class="font-bold text-slate-900 mt-0.5">{{ $eligPRC ?: 'None' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase">TESDA NC</dt>
                            <dd class="font-bold text-slate-900 mt-0.5">{{ $eligTESDA ?: 'None' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase">Driver's License</dt>
                            <dd class="font-bold text-slate-900 mt-0.5">{{ $eligDriver ?: 'None' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Languages Card -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <div class="h-9 w-9 rounded-xl bg-green-50 text-green-700 flex items-center justify-center font-bold">
                            🗣️
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-900">Languages & Dialects</h3>
                            <p class="text-[11px] text-slate-400">Communication capabilities</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-1.5 pt-1">
                        @forelse($langList as $lang)
                            <span class="rounded-xl bg-green-50 text-green-800 border border-green-200 px-3 py-1 text-xs font-bold">
                                {{ $lang }}
                            </span>
                        @empty
                            <p class="text-xs text-slate-400 italic">No languages listed.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Social Inclusivity Card -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <div class="h-9 w-9 rounded-xl bg-green-50 text-green-700 flex items-center justify-center font-bold">
                            🤝
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-900">Social Support & Priority</h3>
                            <p class="text-[11px] text-slate-400">DMDP inclusivity programs</p>
                        </div>
                    </div>

                    <dl class="space-y-2.5 text-xs">
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase">PWD Status</dt>
                            <dd class="font-bold text-slate-900 mt-0.5">
                                @if($socialStatus->is_pwd)
                                    <span class="text-green-700 font-black">✓ Registered ({{ $socialStatus->pwd_type ?: 'PWD' }})</span>
                                @else
                                    <span class="text-slate-500">Not Applicable</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase">4Ps Beneficiary</dt>
                            <dd class="font-bold text-slate-900 mt-0.5">
                                @if($socialStatus->is_4ps)
                                    <span class="text-amber-700 font-black">✓ Registered (ID: {{ $socialStatus->household_id ?: 'Active' }})</span>
                                @else
                                    <span class="text-slate-500">No</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase">Returning OFW</dt>
                            <dd class="font-bold text-slate-900 mt-0.5">
                                {{ ($socialStatus->is_ofw ?? false) ? 'Yes' : 'No' }}
                            </dd>
                        </div>
                    </dl>
                </div>

            </div>

            <!-- Skills Matrix Preview Card -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl bg-green-50 text-green-700 flex items-center justify-center font-bold">
                            ⚡
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-900">Verified Skills Matrix</h3>
                            <p class="text-[11px] text-slate-400">Used by AI matching engine to match you with top Cebu jobs</p>
                        </div>
                    </div>
                    <button type="button" @click="activeTab = 'skills'" class="text-xs font-bold text-green-700 hover:text-green-800 flex items-center gap-1">
                        Manage Skills &rarr;
                    </button>
                </div>

                <div class="flex flex-wrap gap-2 pt-1">
                    @forelse($skills as $s)
                        <span class="inline-flex items-center gap-1 rounded-xl bg-green-50 border border-green-200 px-3 py-1.5 text-xs font-bold text-green-900">
                            ✓ {{ $s }}
                        </span>
                    @empty
                        <p class="text-xs text-slate-400 italic">No skills registered yet. Go to Skills Matrix tab to add verified skills.</p>
                    @endforelse
                </div>
            </div>

            <!-- Training Certificates Card -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold">
                            🎓
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-900">Official DMDP Training Certificates</h3>
                            <p class="text-[11px] text-slate-400">Certificates earned through completed courses</p>
                        </div>
                    </div>
                    <a href="{{ route('jobseeker.training') }}" class="text-xs font-bold text-green-700 hover:text-green-800">
                        Enroll in Courses &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse($certificates ?? [] as $cert)
                        <div class="p-4 rounded-2xl bg-slate-950 text-white flex flex-col justify-between gap-3 border border-green-500/20">
                            <div>
                                <span class="text-[10px] font-bold text-green-400 uppercase tracking-wider">DMDP Certified</span>
                                <h4 class="text-sm font-bold text-white mt-1">{{ $cert->course_title }}</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5">Cert #{{ $cert->certificate_no }}</p>
                            </div>
                            <a href="{{ route('jobseeker.certificates.preview', $cert->enrollment_id) }}" target="_blank"
                               class="text-xs font-bold text-green-300 hover:text-green-200 flex items-center gap-1">
                                🖨️ View Certificate PDF &rarr;
                            </a>
                        </div>
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-slate-200 p-6 text-center text-slate-400">
                            <p class="text-xs">No certificates earned yet. <a href="{{ route('jobseeker.training') }}" class="text-green-700 font-bold underline">Enroll in a course</a> to earn certification!</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- =================================================================== -->
        <!-- TAB 2: UPDATE PROFILE FORM -->
        <!-- =================================================================== -->
        <div x-show="activeTab === 'edit'" class="space-y-8">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-black text-slate-900">Update Profile Details</h2>
                    <p class="text-xs text-slate-500">Edit and save your official jobseeker portfolio details.</p>
                </div>
                <button type="button" @click="activeTab = 'view'" class="text-xs font-bold text-slate-600 hover:text-slate-900">
                    ✕ Cancel & Return to View
                </button>
            </div>

            <form action="{{ route('jobseeker.profile.update_info') }}" method="POST" class="space-y-8">
                @csrf

                <!-- Section 2.1: Personal & Civil Identification -->
                <section class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="pb-4 border-b border-slate-100 flex items-center gap-3">
                        <div class="h-10 w-10 rounded-2xl bg-green-50 text-green-700 flex items-center justify-center text-lg font-bold">
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
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Middle Name</label>
                            <input type="text" name="middle_name" value="{{ old('middle_name', $jobseeker->middle_name ?? '') }}" placeholder="Middle Name"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Last Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name', $jobseeker->last_name ?? '') }}" required
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Date of Birth</label>
                            <input type="date" name="birth_date" value="{{ old('birth_date', $jobseeker->birth_date ? date('Y-m-d', strtotime($jobseeker->birth_date)) : '') }}"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Sex at Birth</label>
                            <select name="sex" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
                                <option value="">-- Select Sex --</option>
                                <option value="Male" {{ old('sex', $jobseeker->sex ?? '') === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('sex', $jobseeker->sex ?? '') === 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Civil Status</label>
                            <select name="civil_status" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
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
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Primary Mobile Number <span class="text-rose-500">*</span></label>
                            <input type="text" name="mobile_number" value="{{ old('mobile_number', $jobseeker->mobile_number ?? '') }}" placeholder="09123456789"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Current Employment Status</label>
                            <select name="employment_status" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
                                <option value="Unemployed" {{ old('employment_status', $jobseeker->employment_status ?? '') === 'Unemployed' ? 'selected' : '' }}>Unemployed / Looking for Work</option>
                                <option value="Fresh Graduate" {{ old('employment_status', $jobseeker->employment_status ?? '') === 'Fresh Graduate' ? 'selected' : '' }}>Fresh Graduate / Entry Level</option>
                                <option value="Employed" {{ old('employment_status', $jobseeker->employment_status ?? '') === 'Employed' ? 'selected' : '' }}>Employed (Hired)</option>
                                <option value="Self-Employed" {{ old('employment_status', $jobseeker->employment_status ?? '') === 'Self-Employed' ? 'selected' : '' }}>Self-Employed / Freelancer</option>
                                <option value="Returning OFW" {{ old('employment_status', $jobseeker->employment_status ?? '') === 'Returning OFW' ? 'selected' : '' }}>Returning Overseas Filipino Worker (OFW)</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Hiring / Current Company</label>
                            <input type="text" name="hired_company" value="{{ old('hired_company', $jobseeker->hired_company ?? '') }}" placeholder="e.g. Cebu IT Solutions Inc"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
                        </div>
                    </div>
                </section>

                <!-- Section 2.2: Address & Location Details -->
                <section class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="pb-4 border-b border-slate-100 flex items-center gap-3">
                        <div class="h-10 w-10 rounded-2xl bg-green-50 text-green-700 flex items-center justify-center text-lg font-bold">
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
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Barangay</label>
                            <input type="text" name="address_barangay" value="{{ old('address_barangay', $brgyVal) }}" placeholder="e.g. Lahug, Mabolo, Guadalupe, Apas"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">City / Municipality</label>
                            <input type="text" name="address_city" value="{{ old('address_city', $cityVal ?: 'Cebu City') }}" placeholder="Cebu City"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Province</label>
                            <input type="text" name="address_province" value="{{ old('address_province', $provVal ?: 'Cebu') }}" placeholder="Cebu"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Postal / Zip Code</label>
                            <input type="text" name="address_zip" value="{{ old('address_zip', $zipVal) }}" placeholder="6000"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
                        </div>
                    </div>
                </section>

                <!-- Section 2.3: Educational Attainment -->
                <section class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="pb-4 border-b border-slate-100 flex items-center gap-3">
                        <div class="h-10 w-10 rounded-2xl bg-green-50 text-green-700 flex items-center justify-center text-lg font-bold">
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
                            <select name="education_level" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
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
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Degree / Course / Major</label>
                            <input type="text" name="education_course" value="{{ old('education_course', $eduCourse) }}" placeholder="e.g. BS Information Technology / Automotive NC II"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Year Graduated / Last Attended</label>
                            <input type="text" name="education_year" value="{{ old('education_year', $eduYear) }}" placeholder="e.g. 2024"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
                        </div>
                    </div>
                </section>

                <!-- Section 2.4: Work Experience & Career Background -->
                <section class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="pb-4 border-b border-slate-100 flex items-center gap-3">
                        <div class="h-10 w-10 rounded-2xl bg-green-50 text-green-700 flex items-center justify-center text-lg font-bold">
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
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Job Title / Role</label>
                            <input type="text" name="experience_position" value="{{ old('experience_position', $expPosition) }}" placeholder="e.g. Customer Service Rep, Junior Developer"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Duration / Total Experience</label>
                            <select name="experience_duration" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
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
                                  class="w-full rounded-xl border border-slate-200 p-4 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">{{ old('experience_description', $expDesc) }}</textarea>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Professional Summary / Candidate Bio</label>
                        <textarea name="bio" rows="3" placeholder="Write a short introductory profile summarizing your strengths, career goals, and what you offer to prospective employers..."
                                  class="w-full rounded-xl border border-slate-200 p-4 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">{{ old('bio', $expBio) }}</textarea>
                    </div>
                </section>

                <!-- Section 2.5: Career Target & Job Preferences -->
                <section class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="pb-4 border-b border-slate-100 flex items-center gap-3">
                        <div class="h-10 w-10 rounded-2xl bg-green-50 text-green-700 flex items-center justify-center text-lg font-bold">
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
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Alternative Target Occupation (Secondary)</label>
                            <input type="text" name="occupation2" value="{{ old('occupation2', $preferences->occupation2 ?? '') }}" placeholder="e.g. Technical Support / Admin Assistant"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Preferred Industry / Sector</label>
                            <select name="industry1" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
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
                            <select name="preferred_location" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
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
                            <select name="salary_expectation" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500 focus:ring-green-400">
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
                        <div class="h-10 w-10 rounded-2xl bg-green-50 text-green-700 flex items-center justify-center text-lg font-bold">
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
                                       class="h-4 w-4 rounded text-green-600 focus:ring-green-500 border-slate-300">
                                <span class="text-xs font-bold text-slate-900">I am a registered Person with Disability (PWD)</span>
                            </label>

                            <div x-show="isPwd" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-200">
                                <div class="space-y-1">
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase">Type of Disability / Accommodation Need</label>
                                    <select name="pwd_type" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-900 focus:border-green-500">
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
                                           class="h-4 w-4 rounded text-green-600 focus:ring-green-500 border-slate-300">
                                    <span class="text-xs font-bold text-slate-900">4Ps (Pantawid Pamilya) Beneficiary</span>
                                </label>
                                <div x-show="is4ps" x-cloak class="pt-2 border-t border-slate-200">
                                    <input type="text" name="household_id" value="{{ old('household_id', $socialStatus->household_id ?? '') }}" placeholder="Household ID Number"
                                           class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-900 focus:border-green-500">
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="is_ofw" value="1" {{ ($socialStatus->is_ofw ?? false) ? 'checked' : '' }}
                                           class="h-4 w-4 rounded text-green-600 focus:ring-green-500 border-slate-300">
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
                        <div class="h-10 w-10 rounded-2xl bg-green-50 text-green-700 flex items-center justify-center text-lg font-bold">
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
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">PRC Board License</label>
                            <input type="text" name="eligibility_prc_license" value="{{ old('eligibility_prc_license', $eligPRC) }}" placeholder="e.g. Registered Nurse / LPT"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">TESDA NC Certificate</label>
                            <input type="text" name="eligibility_tesda_nc" value="{{ old('eligibility_tesda_nc', $eligTESDA) }}" placeholder="e.g. NC II Barista, SMAW NC II"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Driver's License</label>
                            <input type="text" name="eligibility_driver_license" value="{{ old('eligibility_driver_license', $eligDriver) }}" placeholder="e.g. Non-Pro (Code 1, 2) / Pro"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-green-500">
                        </div>
                    </div>

                    <div class="space-y-2 pt-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Spoken Languages & Dialects</label>
                        <div class="flex flex-wrap gap-3">
                            @php
                                $availableLangs = ['Cebuano / Bisaya', 'English', 'Tagalog / Filipino', 'Ilonggo / Hiligaynon', 'Waray', 'Mandarin', 'Japanese'];
                            @endphp
                            @foreach($availableLangs as $l)
                                <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-700 cursor-pointer hover:bg-green-50 hover:border-green-300 transition-colors">
                                    <input type="checkbox" name="languages[]" value="{{ $l }}" {{ in_array($l, $langList) ? 'checked' : '' }}
                                           class="h-3.5 w-3.5 rounded text-green-600 focus:ring-green-500 border-slate-300">
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
                    <div class="flex items-center gap-3">
                        <button type="button" @click="activeTab = 'view'" class="px-5 py-3 rounded-xl border border-white/20 text-xs font-bold text-slate-300 hover:bg-white/10 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="rounded-xl bg-gradient-to-r from-green-500 to-green-400 hover:from-green-400 hover:to-green-300 text-slate-950 px-8 py-3 text-xs font-black shadow-lg shadow-green-500/30 transition-all hover:scale-105">
                            ✓ Save Complete Profile
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- =================================================================== -->
        <!-- TAB 3: SKILLS MATRIX (INTERACTIVE SKILLS TAGGER) -->
        <!-- =================================================================== -->
        <div x-show="activeTab === 'skills'" class="space-y-6">
            <section class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-100">
                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-800 border border-green-200">
                            <span class="h-2 w-2 rounded-full bg-green-600 animate-pulse"></span>
                            AI Vector Skills Matrix
                        </span>
                        <h2 class="text-xl font-black text-slate-900 mt-1">My Skills Profile</h2>
                        <p class="text-xs text-slate-500">Add verified skills to raise your compatibility percentage with Cebu job vacancies.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-400">Skills Total:</span>
                        <span class="rounded-xl bg-green-100 px-3 py-1 text-xs font-black text-green-800" x-text="skills.length"></span>
                    </div>
                </div>

                <!-- Input to add custom skill -->
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative flex-1">
                        <input type="text" x-model="newSkill" @keydown.enter.prevent="addSkill()"
                               placeholder="Type a skill (e.g., Python, Welding, Barista, Customer Service, Bookkeeping, ESL)..."
                               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 placeholder:text-slate-400 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-400/30 shadow-xs">
                    </div>
                    <button type="button" @click="addSkill()" 
                            class="rounded-2xl bg-green-600 hover:bg-green-500 px-6 py-3 text-xs font-black text-white shadow-md shadow-green-600/30 transition-all hover:scale-105 shrink-0">
                        + Add Skill
                    </button>
                </div>

                <!-- VII. 21st Century Skills (DOLE NSRP Section VII) -->
                <div class="space-y-3 rounded-2xl bg-slate-50/70 border border-slate-200 p-5">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                        <div>
                            <span class="text-[11px] font-black uppercase tracking-wider text-green-800">DOLE NSRP Form 1 • Section VII</span>
                            <h3 class="text-xs font-black text-slate-900">21st Century Skills (Self-Assessment)</h3>
                        </div>
                        <span class="text-[10px] text-slate-500 font-semibold">Click to select/deselect skills</span>
                    </div>

                    @php
                        $nsrpCenturyCols = [
                            ['Innovation', 'Team Work', 'Multitasking', 'Work Ethics', 'Self Motivation'],
                            ['Creative Problem Solving', 'Problem Solving', 'Critical Thinking', 'Decision Making', 'Stress Tolerance'],
                            ['Planning and Organizing', 'Social Perceptiveness', 'English Functional Skills', 'English Comprehension', 'Math Functional Skill']
                        ];
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach($nsrpCenturyCols as $col)
                            <div class="space-y-1.5">
                                @foreach($col as $cs)
                                    <button type="button" @click="toggleSkill('{{ $cs }}')"
                                            class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition-all text-left border"
                                            :class="hasSkill('{{ $cs }}') 
                                                ? 'bg-green-600 text-white border-green-600 shadow-xs' 
                                                : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100/80'">
                                        <span>{{ $cs }}</span>
                                        <span x-text="hasSkill('{{ $cs }}') ? '✓' : '+'" class="text-xs font-black"></span>
                                    </button>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- IX. Technical Skills Acquired Without Formal Training (DOLE NSRP Section IX) -->
                <div class="space-y-3 rounded-2xl bg-slate-50/70 border border-slate-200 p-5">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                        <div>
                            <span class="text-[11px] font-black uppercase tracking-wider text-green-800">DOLE NSRP Form 1 • Section IX</span>
                            <h3 class="text-xs font-black text-slate-900">Technical Skills Acquired Without Formal Training</h3>
                        </div>
                        <span class="text-[10px] text-slate-500 font-semibold">Practical / Vocational Experience</span>
                    </div>

                    @php
                        $nsrpTechCols = [
                            ['Carpentry', 'Masonry', 'Welding', 'Auto Mechanic'],
                            ['Plumbing', 'Driving', 'Gardening', 'Tailoring'],
                            ['Photography', 'Hairdressing', 'Cooking', 'Baking']
                        ];
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach($nsrpTechCols as $col)
                            <div class="space-y-1.5">
                                @foreach($col as $ts)
                                    <button type="button" @click="toggleSkill('{{ $ts }}')"
                                            class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition-all text-left border"
                                            :class="hasSkill('{{ $ts }}') 
                                                ? 'bg-green-600 text-white border-green-600 shadow-xs' 
                                                : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100/80'">
                                        <span>{{ $ts }}</span>
                                        <span x-text="hasSkill('{{ $ts }}') ? '✓' : '+'" class="text-xs font-black"></span>
                                    </button>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Suggested In-Demand Skills in Cebu City -->
                <div class="space-y-2">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Common High-Demand Cebu Skills (Click to Add):</span>
                    <div class="flex flex-wrap gap-1.5">
                        @php
                            $cebuSkills = [
                                'Customer Service', 'BPO Voice', 'Technical Support', 'JavaScript', 'PHP', 'Laravel', 
                                'Bookkeeping', 'QuickBooks', 'Data Entry', 'Barista NC II', 'Welding SMAW',
                                'Forklift Operation', 'Caregiving NC II', 'Digital Marketing', 'Graphic Design'
                            ];
                        @endphp
                        @foreach($cebuSkills as $cs)
                            <button type="button" @click="addSkill('{{ $cs }}')"
                                    class="rounded-xl bg-slate-50 hover:bg-green-50 hover:text-green-800 hover:border-green-300 border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-600 transition-colors shadow-2xs">
                                + {{ $cs }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Active Skills Badges -->
                <div class="space-y-2 pt-2">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Your Active Verified Skills:</span>
                    <div class="flex flex-wrap gap-2 min-h-[50px] p-4 rounded-2xl bg-slate-50/50 border border-dashed border-slate-200">
                        <template x-for="(skill, index) in skills" :key="index">
                            <span class="inline-flex items-center gap-1.5 rounded-xl bg-green-100 text-green-950 border border-green-300 px-3 py-1.5 text-xs font-black shadow-xs">
                                <span x-text="skill"></span>
                                <button type="button" @click="removeSkill(index)" class="text-green-700 hover:text-rose-600 font-black ml-1 text-sm leading-none">&times;</button>
                            </span>
                        </template>
                        <template x-if="skills.length === 0">
                            <p class="text-xs text-slate-400 italic">No skills added yet. Type a skill above or click recommendations to get started.</p>
                        </template>
                    </div>
                </div>

                <!-- Sync Skills Form -->
                <form action="{{ route('jobseeker.profile.update_skills') }}" method="POST" class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    @csrf
                    <template x-for="skill in skills" :key="skill">
                        <input type="hidden" name="skills[]" :value="skill">
                    </template>
                    
                    <p class="text-[11px] text-slate-500">Changes will instantly update your AI job match percentages.</p>
                    <button type="submit" class="rounded-xl bg-green-700 hover:bg-green-800 text-white px-6 py-2.5 text-xs font-black shadow-md transition-all">
                        Save Skills Matrix
                    </button>
                </form>
            </section>
        </div>

        <!-- =================================================================== -->
        <!-- TAB 4: RESET PASSWORD & ACCOUNT SECURITY -->
        <!-- =================================================================== -->
        <div x-show="activeTab === 'security'" class="space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Password Change Form (2 cols) -->
                <div class="md:col-span-2 rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="pb-4 border-b border-slate-100 flex items-center gap-3">
                        <div class="h-10 w-10 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center text-lg font-bold">
                            🔒
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-900">Reset & Change Account Password</h2>
                            <p class="text-xs text-slate-500">Ensure your account uses a long, secure password to protect your identity.</p>
                        </div>
                    </div>

                    <form action="{{ route('jobseeker.password.change') }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- Current Password -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Current Password <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <input :type="showCurrentPass ? 'text' : 'password'" name="current_password" required
                                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-amber-500 focus:ring-amber-400 pr-10">
                                <button type="button" @click="showCurrentPass = !showCurrentPass"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 text-xs">
                                    <span x-text="showCurrentPass ? 'Hide' : 'Show'"></span>
                                </button>
                            </div>
                            @error('current_password')
                                <p class="text-[11px] text-rose-600 font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">New Password <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <input :type="showNewPass ? 'text' : 'password'" name="password" required minlength="8"
                                       placeholder="Minimum 8 characters"
                                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-amber-500 focus:ring-amber-400 pr-10">
                                <button type="button" @click="showNewPass = !showNewPass"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 text-xs">
                                    <span x-text="showNewPass ? 'Hide' : 'Show'"></span>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-[11px] text-rose-600 font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm New Password -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Confirm New Password <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <input :type="showConfirmPass ? 'text' : 'password'" name="password_confirmation" required minlength="8"
                                       placeholder="Re-type new password"
                                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-amber-500 focus:ring-amber-400 pr-10">
                                <button type="button" @click="showConfirmPass = !showConfirmPass"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 text-xs">
                                    <span x-text="showConfirmPass ? 'Hide' : 'Show'"></span>
                                </button>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-bold text-amber-700 hover:underline">
                                    Forgot current password?
                                </a>
                            @else
                                <span class="text-[11px] text-slate-400">Need help? Contact DMDP Cebu support</span>
                            @endif
                            <button type="submit" class="rounded-xl bg-amber-600 hover:bg-amber-500 text-white px-7 py-3 text-xs font-black shadow-lg shadow-amber-600/20 transition-all hover:scale-105">
                                Update / Reset Password
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Security Tips & Information (1 col) -->
                <div class="space-y-6">
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Password Requirements</h3>
                        <ul class="space-y-2 text-xs text-slate-600">
                            <li class="flex items-center gap-2">
                                <span class="text-green-600 font-bold">✓</span> At least 8 characters long
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-green-600 font-bold">✓</span> Mix of uppercase & lowercase letters
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-green-600 font-bold">✓</span> Include numbers & special symbols
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-green-600 font-bold">✓</span> Avoid using your birth date or name
                            </li>
                        </ul>
                    </div>

                    <div class="rounded-3xl bg-green-50/70 border border-green-200 p-6 space-y-2">
                        <span class="text-xs font-bold text-green-900">🛡️ Account Protection</span>
                        <p class="text-[11px] text-green-800 leading-relaxed">
                            Your password secures your legal documentation, verified government IDs, and application records within the Cebu City DMDP TrabaGo ecosystem.
                        </p>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
@endsection
