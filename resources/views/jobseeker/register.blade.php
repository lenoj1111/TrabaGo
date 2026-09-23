@extends('layouts.public')

@section('title', 'Jobseeker Registration - DMDP Cebu City')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 md:py-12">
    <div class="max-w-4xl mx-auto px-5">

        <!-- Header -->
        <div class="bg-green-950 rounded-2xl p-6 md:p-8 mb-6">
            <p class="text-xs font-semibold text-green-400 uppercase tracking-widest">DMDP Registration</p>
            <h1 class="text-2xl md:text-3xl font-extrabold text-white mt-1">Jobseeker Registration</h1>
            <p class="text-green-200/70 text-sm mt-1">Department of Manpower Development and Placement</p>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl mb-5 text-sm font-medium">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-5 text-sm font-medium">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-5 text-sm font-medium">
                <ul>@foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('jobseeker.register.post') }}" class="bg-white border border-gray-200 rounded-2xl overflow-hidden" id="registrationForm">
            @csrf

            <!-- Progress Steps -->
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <div class="flex items-center justify-between max-w-2xl mx-auto">
                    <div class="flex items-center gap-2" id="stepIndicator">
                        <div class="step-dot active" data-step="1">
                            <span class="step-number">1</span>
                            <span class="step-label">Account</span>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-dot" data-step="2">
                            <span class="step-number">2</span>
                            <span class="step-label">Personal</span>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-dot" data-step="3">
                            <span class="step-number">3</span>
                            <span class="step-label">Address</span>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-dot" data-step="4">
                            <span class="step-number">4</span>
                            <span class="step-label">Education</span>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-dot" data-step="5">
                            <span class="step-number">5</span>
                            <span class="step-label">Skills</span>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-dot" data-step="6">
                            <span class="step-number">6</span>
                            <span class="step-label">Preferences</span>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-dot" data-step="7">
                            <span class="step-number">7</span>
                            <span class="step-label">Social</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 1: Account -->
            <div class="step-content" data-step="1">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h2 class="text-base font-bold text-gray-900">Account Information</h2>
                    <p class="text-sm text-gray-500">Create your login credentials</p>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1"><span class="text-red-500">*</span> Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2.5 bg-white border @error('email') border-red-400 @else border-gray-200 @enderror rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                            @error('email')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1"><span class="text-red-500">*</span> Password</label>
                            <input type="password" name="password" class="w-full px-4 py-2.5 bg-white border @error('password') border-red-400 @else border-gray-200 @enderror rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                            @error('password')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1"><span class="text-red-500">*</span> Confirm Password</label>
                            <input type="password" name="password_confirmation" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1"><span class="text-red-500">*</span> Role</label>
                            <select name="role" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm">
                                <option value="jobseeker" selected>Jobseeker</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Personal -->
            <div class="step-content" data-step="2" style="display:none;">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h2 class="text-base font-bold text-gray-900">Personal Information</h2>
                    <p class="text-sm text-gray-500">Tell us about yourself</p>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1"><span class="text-red-500">*</span> First Name</label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" class="w-full px-4 py-2.5 bg-white border @error('first_name') border-red-400 @else border-gray-200 @enderror rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                            @error('first_name')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1"><span class="text-red-500">*</span> Last Name</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" class="w-full px-4 py-2.5 bg-white border @error('last_name') border-red-400 @else border-gray-200 @enderror rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                            @error('last_name')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                            <input type="text" name="middle_name" value="{{ old('middle_name') }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1"><span class="text-red-500">*</span> Birth Date</label>
                            <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full px-4 py-2.5 bg-white border @error('birth_date') border-red-400 @else border-gray-200 @enderror rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                            @error('birth_date')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1"><span class="text-red-500">*</span> Sex</label>
                            <select name="sex" class="w-full px-4 py-2.5 bg-white border @error('sex') border-red-400 @else border-gray-200 @enderror rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm">
                                <option value="">Select...</option>
                                <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Prefer not to say" {{ old('sex') == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                            </select>
                            @error('sex')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1"><span class="text-red-500">*</span> Civil Status</label>
                            <select name="civil_status" class="w-full px-4 py-2.5 bg-white border @error('civil_status') border-red-400 @else border-gray-200 @enderror rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm">
                                <option value="">Select...</option>
                                <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>Single</option>
                                <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>Married</option>
                                <option value="Divorced" {{ old('civil_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                            </select>
                            @error('civil_status')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1"><span class="text-red-500">*</span> Citizenship</label>
                            <input type="text" name="citizenship" value="{{ old('citizenship') }}" class="w-full px-4 py-2.5 bg-white border @error('citizenship') border-red-400 @else border-gray-200 @enderror rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                            @error('citizenship')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1"><span class="text-red-500">*</span> Mobile Number</label>
                            <input type="text" name="mobile_number" value="{{ old('mobile_number') }}" class="w-full px-4 py-2.5 bg-white border @error('mobile_number') border-red-400 @else border-gray-200 @enderror rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                            @error('mobile_number')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1"><span class="text-red-500">*</span> Employment Status</label>
                            <select name="employment_status" class="w-full px-4 py-2.5 bg-white border @error('employment_status') border-red-400 @else border-gray-200 @enderror rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm">
                                <option value="">Select...</option>
                                <option value="Unemployed" {{ old('employment_status') == 'Unemployed' ? 'selected' : '' }}>Unemployed</option>
                                <option value="Employed" {{ old('employment_status') == 'Employed' ? 'selected' : '' }}>Employed</option>
                                <option value="Self-employed" {{ old('employment_status') == 'Self-employed' ? 'selected' : '' }}>Self-employed</option>
                                <option value="Fresh Graduate" {{ old('employment_status') == 'Fresh Graduate' ? 'selected' : '' }}>Fresh Graduate</option>
                                <option value="Underemployed" {{ old('employment_status') == 'Underemployed' ? 'selected' : '' }}>Underemployed</option>
                            </select>
                            @error('employment_status')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Address -->
            <div class="step-content" data-step="3" style="display:none;">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h2 class="text-base font-bold text-gray-900">Address</h2>
                    <p class="text-sm text-gray-500">Where do you currently reside?</p>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Complete Address</label>
                            <input type="text" name="address[street]" value="{{ old('address.street') }}" placeholder="Street address" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input type="text" name="address[city]" value="{{ old('address.city') }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Province</label>
                            <input type="text" name="address[province]" value="{{ old('address.province') }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                            <input type="text" name="address[postal_code]" value="{{ old('address.postal_code') }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                            <input type="text" name="address[country]" value="{{ old('address.country', 'Philippines') }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 4: Education -->
            <div class="step-content" data-step="4" style="display:none;">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h2 class="text-base font-bold text-gray-900">Education</h2>
                    <p class="text-sm text-gray-500">Your educational background</p>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Highest Education</label>
                            <select name="education[level]" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm">
                                <option value="">Select...</option>
                                <option value="Elementary" {{ old('education.level') == 'Elementary' ? 'selected' : '' }}>Elementary</option>
                                <option value="High School" {{ old('education.level') == 'High School' ? 'selected' : '' }}>High School</option>
                                <option value="Senior High School" {{ old('education.level') == 'Senior High School' ? 'selected' : '' }}>Senior High School</option>
                                <option value="Vocational" {{ old('education.level') == 'Vocational' ? 'selected' : '' }}>Vocational</option>
                                <option value="College" {{ old('education.level') == 'College' ? 'selected' : '' }}>College</option>
                                <option value="Post Graduate" {{ old('education.level') == 'Post Graduate' ? 'selected' : '' }}>Post Graduate</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Course / Program</label>
                            <input type="text" name="education[course]" value="{{ old('education.course') }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">School / Institution</label>
                            <input type="text" name="education[school]" value="{{ old('education.school') }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Year Graduated</label>
                            <input type="text" name="education[year_graduated]" value="{{ old('education.year_graduated') }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 5: Skills -->
            <div class="step-content" data-step="5" style="display:none;">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h2 class="text-base font-bold text-gray-900">Skills Assessment</h2>
                    <p class="text-sm text-gray-500">Select your skills and technical abilities</p>
                </div>
                <div class="px-6 py-6 space-y-6">
                    <!-- 21st Century Skills -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-1">21st Century Skills</h3>
                        <p class="text-xs text-gray-500 mb-3">Check five (5) skills you possess</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-sm">
                            @php
                                $centurySkills = ['Innovation', 'Team Work', 'Multitasking', 'Work Ethics', 'Self Motivation', 'Creative Problem Solving', 'Problem Solving', 'Critical Thinking', 'Decision Making', 'Stress Tolerance', 'Planning and Organizing', 'Social Perceptiveness', 'English Functional Skills', 'English Comprehension', 'Math Functional Skill'];
                            @endphp
                            @foreach($centurySkills as $cs)
                                <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-100 bg-white hover:bg-green-50 cursor-pointer transition-colors">
                                    <input type="checkbox" name="skills_century[]" value="{{ $cs }}" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="text-gray-700">{{ $cs }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Technical Skills -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-1">Technical Skills Without Formal Training</h3>
                        <p class="text-xs text-gray-500 mb-3">Check practical skills developed through experience</p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-sm">
                            @php $informalList = ['Carpentry', 'Masonry', 'Welding', 'Auto Mechanic', 'Plumbing', 'Driving', 'Gardening', 'Tailoring', 'Photography', 'Hairdressing', 'Cooking', 'Baking']; @endphp
                            @foreach($informalList as $is)
                                <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-100 bg-white hover:bg-green-50 cursor-pointer transition-colors">
                                    <input type="checkbox" name="skills_informal_tech[]" value="{{ $is }}" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="text-gray-700">{{ $is }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Other Skills -->
                    <div class="border-t border-gray-100 pt-4">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Other Specialized Skills (Optional)</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <input type="text" name="skills[0][name]" value="{{ old('skills.0.name') }}" placeholder="e.g. IT, Programming, Accounting" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 text-sm outline-none" />
                            <input type="hidden" name="skills[0][type]" value="technical" />
                            <input type="text" name="skills[1][name]" value="{{ old('skills.1.name') }}" placeholder="Additional skill..." class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 text-sm outline-none" />
                            <input type="hidden" name="skills[1][type]" value="technical" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 6: Preferences -->
            <div class="step-content" data-step="6" style="display:none;">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h2 class="text-base font-bold text-gray-900">Job Preferences</h2>
                    <p class="text-sm text-gray-500">What kind of job are you looking for?</p>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Desired Occupation 1</label><input type="text" name="occupation1" value="{{ old('occupation1') }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" /></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Desired Occupation 2</label><input type="text" name="occupation2" value="{{ old('occupation2') }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" /></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Desired Occupation 3</label><input type="text" name="occupation3" value="{{ old('occupation3') }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" /></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Industry Preference 1</label><input type="text" name="industry1" value="{{ old('industry1') }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" /></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Industry Preference 2</label><input type="text" name="industry2" value="{{ old('industry2') }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" /></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Industry Preference 3</label><input type="text" name="industry3" value="{{ old('industry3') }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" /></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Expected Salary (PHP)</label><input type="text" name="salary_expectation" value="{{ old('salary_expectation') }}" placeholder="e.g., 25,000 - 30,000" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" /></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Preferred Location</label><input type="text" name="preferred_location" value="{{ old('preferred_location') }}" placeholder="e.g., Cebu, Manila" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" /></div>
                    </div>
                </div>
            </div>

            <!-- Step 7: Social -->
            <div class="step-content" data-step="7" style="display:none;">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h2 class="text-base font-bold text-gray-900">Social Status</h2>
                    <p class="text-sm text-gray-500">Additional information</p>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="is_4ps" value="1" {{ old('is_4ps') ? 'checked' : '' }} class="w-4 h-4 text-green-600 rounded border-gray-300 focus:ring-green-500" />
                                4Ps Beneficiary
                            </label>
                            <input type="text" name="household_id" value="{{ old('household_id') }}" placeholder="Household ID (if applicable)" class="w-full mt-2 px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                        </div>
                        <div>
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="is_ofw" value="1" {{ old('is_ofw') ? 'checked' : '' }} class="w-4 h-4 text-green-600 rounded border-gray-300 focus:ring-green-500" />
                                OFW / OFW Dependent
                            </label>
                        </div>
                        <div class="md:col-span-2">
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="is_pwd" value="1" {{ old('is_pwd') ? 'checked' : '' }} class="w-4 h-4 text-green-600 rounded border-gray-300 focus:ring-green-500" />
                                Person with Disability (PWD)
                            </label>
                            <input type="text" name="pwd_type" value="{{ old('pwd_type') }}" placeholder="Type of disability (if applicable)" class="w-full mt-2 px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                <div class="flex items-center justify-between max-w-2xl mx-auto">
                    <button type="button" id="prevBtn" class="px-5 py-2.5 text-gray-600 font-medium rounded-lg border border-gray-200 hover:bg-gray-100 transition flex items-center gap-2 text-sm" style="display:none;">
                        ← Back
                    </button>
                    <button type="button" id="nextBtn" class="px-6 py-2.5 text-white font-semibold rounded-lg bg-green-600 hover:bg-green-700 transition flex items-center gap-2 text-sm">
                        Next →
                    </button>
                    <button type="submit" id="submitBtn" class="px-6 py-2.5 text-white font-semibold rounded-lg bg-green-600 hover:bg-green-700 transition flex items-center gap-2 text-sm" style="display:none;">
                        Register →
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.step-dot { display: flex; align-items: center; gap: 6px; cursor: pointer; padding: 3px 6px; border-radius: 16px; transition: all 0.2s; }
.step-dot .step-number { display: flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: #e5e7eb; color: #6b7280; font-weight: 600; font-size: 12px; transition: all 0.2s; }
.step-dot .step-label { font-size: 12px; font-weight: 500; color: #9ca3af; transition: all 0.2s; }
.step-dot.active .step-number { background: #16a34a; color: white; }
.step-dot.active .step-label { color: #15803d; font-weight: 600; }
.step-dot.completed .step-number { background: #22c55e; color: white; }
.step-dot.completed .step-label { color: #22c55e; }
.step-line { flex: 1; height: 2px; background: #e5e7eb; transition: all 0.2s; min-width: 16px; }
.step-line.completed { background: #22c55e; }
@media (max-width: 768px) { .step-dot .step-label { display: none; } .step-line { min-width: 8px; } .step-dot .step-number { width: 22px; height: 22px; font-size: 10px; } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 7;
    const form = document.getElementById('registrationForm');
    const stepContents = document.querySelectorAll('.step-content');
    const stepDots = document.querySelectorAll('.step-dot');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    function updateSteps() {
        stepContents.forEach(c => { c.style.display = parseInt(c.dataset.step) === currentStep ? 'block' : 'none'; });
        stepDots.forEach(d => { const s = parseInt(d.dataset.step); d.classList.remove('active','completed'); if(s===currentStep) d.classList.add('active'); else if(s<currentStep) d.classList.add('completed'); });
        document.querySelectorAll('.step-line').forEach((l, i) => { l.classList.toggle('completed', i < currentStep - 1); });
        prevBtn.style.display = currentStep > 1 ? 'inline-flex' : 'none';
        if(currentStep === totalSteps) { nextBtn.style.display='none'; submitBtn.style.display='inline-flex'; } else { nextBtn.style.display='inline-flex'; submitBtn.style.display='none'; }
    }

    function validateStep(step) {
        const c = document.querySelector(`.step-content[data-step="${step}"]`);
        const inputs = c.querySelectorAll('input[required], select[required]');
        let valid = true;
        inputs.forEach(i => { if(!i.value.trim()) { valid=false; i.classList.add('border-red-400'); } else { i.classList.remove('border-red-400'); } });
        return valid;
    }

    function goToStep(step) { if(step > currentStep && !validateStep(currentStep)) return; currentStep = step; updateSteps(); }

    nextBtn.addEventListener('click', function(e) { e.preventDefault(); if(validateStep(currentStep) && currentStep < totalSteps) goToStep(currentStep+1); });
    prevBtn.addEventListener('click', function(e) { e.preventDefault(); if(currentStep > 1) goToStep(currentStep-1); });

    stepDots.forEach(d => { d.addEventListener('click', function() { const s = parseInt(this.dataset.step); if(s < currentStep || validateStep(currentStep)) goToStep(s); }); });

    form.addEventListener('keydown', function(e) { if(e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') { if(currentStep < totalSteps) { e.preventDefault(); if(validateStep(currentStep)) goToStep(currentStep+1); } } });

    updateSteps();
});
</script>
@endsection