@extends('layouts.public')

@section('title', 'DMDP Jobseeker Registration - Department of Manpower Development and Placement')

@section('content')
<div class="bg-gradient-to-b from-brand-50/50 to-white min-h-screen py-8">
    <div class="max-w-5xl mx-auto px-4">
        <!-- Breadcrumb -->
        <div class="bg-white/80 backdrop-blur border border-brand-100 rounded-xl px-5 py-3 mb-6 shadow-sm">
            <nav class="text-sm">
                <ol class="flex items-center gap-2 text-brand-600">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-900 transition-colors">Home</a></li>
                    <li><span class="text-brand-300">/</span></li>
                    <li class="text-brand-900 font-semibold">Jobseeker Registration</li>
                </ol>
            </nav>
        </div>

        <!-- Header -->
        <div class="relative overflow-hidden rounded-2xl mb-8" style="background: linear-gradient(135deg, #1b2739 0%, #33455e 50%, #405673 100%);">
            <div class="relative px-6 py-8 md:px-8 md:py-10">
                <h1 class="text-2xl md:text-3xl font-bold text-white">Jobseeker Registration</h1>
                <p class="text-brand-300 text-sm">Department of Manpower Development and Placement</p>
            </div>
        </div>

        <!-- Display Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('jobseeker.register.post') }}" class="bg-white border border-brand-100 rounded-xl shadow-card overflow-hidden">
            @csrf

            <!-- Account Information -->
            <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #f5f7fa 0%, #e9edf3 100%);">
                <div class="relative px-6 py-5">
                    <h2 class="text-lg font-bold text-brand-900">Account Information</h2>
                </div>
            </div>

            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">
                            <span class="text-red-500">*</span> Email Address
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" 
                               class="w-full px-4 py-2.5 bg-white border @error('email') border-red-500 @else border-brand-200 @enderror rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        @error('email')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">
                            <span class="text-red-500">*</span> Password
                        </label>
                        <input type="password" name="password" 
                               class="w-full px-4 py-2.5 bg-white border @error('password') border-red-500 @else border-brand-200 @enderror rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        @error('password')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">
                            <span class="text-red-500">*</span> Confirm Password
                        </label>
                        <input type="password" name="password_confirmation" 
                               class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">
                            <span class="text-red-500">*</span> Role
                        </label>
                        <select name="role" class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none">
                            <option value="jobseeker" selected>Jobseeker</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #f5f7fa 0%, #e9edf3 100%);">
                <div class="relative px-6 py-5">
                    <h2 class="text-lg font-bold text-brand-900">Personal Information</h2>
                </div>
            </div>

            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">
                            <span class="text-red-500">*</span> First Name
                        </label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" 
                               class="w-full px-4 py-2.5 bg-white border @error('first_name') border-red-500 @else border-brand-200 @enderror rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        @error('first_name')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">
                            <span class="text-red-500">*</span> Last Name
                        </label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" 
                               class="w-full px-4 py-2.5 bg-white border @error('last_name') border-red-500 @else border-brand-200 @enderror rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        @error('last_name')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Middle Name</label>
                        <input type="text" name="middle_name" value="{{ old('middle_name') }}" 
                               class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">
                            <span class="text-red-500">*</span> Birth Date
                        </label>
                        <input type="date" name="birth_date" value="{{ old('birth_date') }}" 
                               class="w-full px-4 py-2.5 bg-white border @error('birth_date') border-red-500 @else border-brand-200 @enderror rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        @error('birth_date')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">
                            <span class="text-red-500">*</span> Sex
                        </label>
                        <select name="sex" class="w-full px-4 py-2.5 bg-white border @error('sex') border-red-500 @else border-brand-200 @enderror rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none">
                            <option value="">Select...</option>
                            <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Prefer not to say" {{ old('sex') == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                        </select>
                        @error('sex')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">
                            <span class="text-red-500">*</span> Civil Status
                        </label>
                        <select name="civil_status" class="w-full px-4 py-2.5 bg-white border @error('civil_status') border-red-500 @else border-brand-200 @enderror rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none">
                            <option value="">Select...</option>
                            <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>Married</option>
                            <option value="Divorced" {{ old('civil_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                            <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                        </select>
                        @error('civil_status')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">
                            <span class="text-red-500">*</span> Citizenship
                        </label>
                        <input type="text" name="citizenship" value="{{ old('citizenship') }}" 
                               class="w-full px-4 py-2.5 bg-white border @error('citizenship') border-red-500 @else border-brand-200 @enderror rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        @error('citizenship')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">
                            <span class="text-red-500">*</span> Mobile Number
                        </label>
                        <input type="text" name="mobile_number" value="{{ old('mobile_number') }}" 
                               class="w-full px-4 py-2.5 bg-white border @error('mobile_number') border-red-500 @else border-brand-200 @enderror rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        @error('mobile_number')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">
                            <span class="text-red-500">*</span> Employment Status
                        </label>
                        <select name="employment_status" class="w-full px-4 py-2.5 bg-white border @error('employment_status') border-red-500 @else border-brand-200 @enderror rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none">
                            <option value="">Select...</option>
                            <option value="Unemployed" {{ old('employment_status') == 'Unemployed' ? 'selected' : '' }}>Unemployed</option>
                            <option value="Employed" {{ old('employment_status') == 'Employed' ? 'selected' : '' }}>Employed</option>
                            <option value="Self-employed" {{ old('employment_status') == 'Self-employed' ? 'selected' : '' }}>Self-employed</option>
                            <option value="Fresh Graduate" {{ old('employment_status') == 'Fresh Graduate' ? 'selected' : '' }}>Fresh Graduate</option>
                            <option value="Underemployed" {{ old('employment_status') == 'Underemployed' ? 'selected' : '' }}>Underemployed</option>
                        </select>
                        @error('employment_status')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #f5f7fa 0%, #e9edf3 100%);">
                <div class="relative px-6 py-5">
                    <h2 class="text-lg font-bold text-brand-900">Address</h2>
                </div>
            </div>

            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Complete Address</label>
                        <input type="text" name="address[street]" value="{{ old('address.street') }}" placeholder="Street address" 
                               class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">City</label>
                        <input type="text" name="address[city]" value="{{ old('address.city') }}" 
                               class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Province</label>
                        <input type="text" name="address[province]" value="{{ old('address.province') }}" 
                               class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Postal Code</label>
                        <input type="text" name="address[postal_code]" value="{{ old('address.postal_code') }}" 
                               class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Country</label>
                        <input type="text" name="address[country]" value="{{ old('address.country', 'Philippines') }}" 
                               class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                    </div>
                </div>
            </div>

            <!-- Education -->
            <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #f5f7fa 0%, #e9edf3 100%);">
                <div class="relative px-6 py-5">
                    <h2 class="text-lg font-bold text-brand-900">Education</h2>
                </div>
            </div>

            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Highest Education Attained</label>
                        <select name="education[level]" class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none">
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
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Course / Program</label>
                        <input type="text" name="education[course]" value="{{ old('education.course') }}" 
                               class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">School / Institution</label>
                        <input type="text" name="education[school]" value="{{ old('education.school') }}" 
                               class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Year Graduated</label>
                        <input type="text" name="education[year_graduated]" value="{{ old('education.year_graduated') }}" 
                               class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                    </div>
                </div>
            </div>

            <!-- Skills -->
            <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #f5f7fa 0%, #e9edf3 100%);">
                <div class="relative px-6 py-5">
                    <h2 class="text-lg font-bold text-brand-900">Skills</h2>
                </div>
            </div>

            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Technical Skills</label>
                        <input type="text" name="skills[0][name]" value="{{ old('skills.0.name') }}" placeholder="e.g., JavaScript, SQL, Design" 
                               class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        <input type="hidden" name="skills[0][type]" value="technical" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">21st Century Skills</label>
                        <input type="text" name="skills[1][name]" value="{{ old('skills.1.name') }}" placeholder="e.g., Communication, Teamwork" 
                               class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        <input type="hidden" name="skills[1][type]" value="21st_century" />
                    </div>
                    <div>
                        <input type="text" name="skills[2][name]" value="{{ old('skills.2.name') }}" placeholder="Additional skill..." 
                               class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        <input type="hidden" name="skills[2][type]" value="technical" />
                    </div>
                    <div>
                        <input type="text" name="skills[3][name]" value="{{ old('skills.3.name') }}" placeholder="Additional skill..." 
                               class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        <input type="hidden" name="skills[3][type]" value="21st_century" />
                    </div>
                </div>
            </div>

            <!-- Job Preferences -->
            <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #f5f7fa 0%, #e9edf3 100%);">
                <div class="relative px-6 py-5">
                    <h2 class="text-lg font-bold text-brand-900">Job Preferences</h2>
                </div>
            </div>

            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Desired Occupation 1</label>
                        <input type="text" name="occupation1" value="{{ old('occupation1') }}" 
                               class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Desired Occupation 2</label>
                        <input type="text" name="occupation2" value="{{ old('occupation2') }}" 
                               class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Desired Occupation 3</label>
                        <input type="text" name="occupation3" value="{{ old('occupation3') }}" 
                               class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Industry Preference 1</label>
                        <input type="text" name="industry1" value="{{ old('industry1') }}" 
                               class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Industry Preference 2</label>
                        <input type="text" name="industry2" value="{{ old('industry2') }}" 
                               class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Industry Preference 3</label>
                        <input type="text" name="industry3" value="{{ old('industry3') }}" 
                               class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Expected Salary (PHP)</label>
                        <input type="text" name="salary_expectation" value="{{ old('salary_expectation') }}" placeholder="e.g., 25,000 - 30,000" 
                               class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Preferred Location</label>
                        <input type="text" name="preferred_location" value="{{ old('preferred_location') }}" placeholder="e.g., Manila, Cebu, Davao" 
                               class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                    </div>
                </div>
            </div>

            <!-- Social Status -->
            <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #f5f7fa 0%, #e9edf3 100%);">
                <div class="relative px-6 py-5">
                    <h2 class="text-lg font-bold text-brand-900">Social Status</h2>
                </div>
            </div>

            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="flex items-center gap-2 text-sm text-brand-700 cursor-pointer">
                            <input type="checkbox" name="is_4ps" value="1" {{ old('is_4ps') ? 'checked' : '' }} 
                                   class="w-4 h-4 text-brand-600 rounded border-brand-300 focus:ring-brand-500" />
                            4Ps Beneficiary
                        </label>
                        <input type="text" name="household_id" value="{{ old('household_id') }}" placeholder="Household ID (if applicable)" 
                               class="w-full mt-2 px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm text-brand-700 cursor-pointer">
                            <input type="checkbox" name="is_ofw" value="1" {{ old('is_ofw') ? 'checked' : '' }} 
                                   class="w-4 h-4 text-brand-600 rounded border-brand-300 focus:ring-brand-500" />
                            OFW / OFW Dependent
                        </label>
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center gap-2 text-sm text-brand-700 cursor-pointer">
                            <input type="checkbox" name="is_pwd" value="1" {{ old('is_pwd') ? 'checked' : '' }} 
                                   class="w-4 h-4 text-brand-600 rounded border-brand-300 focus:ring-brand-500" />
                            Person with Disability (PWD)
                        </label>
                        <input type="text" name="pwd_type" value="{{ old('pwd_type') }}" placeholder="Type of disability (if applicable)" 
                               class="w-full mt-2 px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="px-6 py-5 border-t border-brand-100" style="background: linear-gradient(135deg, #f5f7fa 0%, #e9edf3 100%);">
                <button type="submit" class="w-full md:w-auto px-8 py-2.5 text-white font-semibold rounded-lg transition shadow-lg hover:shadow-xl flex items-center justify-center gap-2" 
                        style="background: linear-gradient(135deg, #1b2739 0%, #33455e 100%);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Register
                </button>
            </div>
        </form>

        <!-- Footer -->
        <div class="mt-8 flex flex-wrap justify-center gap-6 text-xs text-brand-400 border-t border-brand-100 pt-6">
            <span class="flex items-center gap-1 bg-white px-3 py-1.5 rounded-full border border-brand-100 shadow-sm">
                DMDP Capstone Project
            </span>
        </div>
    </div>
</div>
@endsection