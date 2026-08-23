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
        <div class="relative overflow-hidden rounded-3xl mb-8 shadow-xl" style="background: linear-gradient(135deg, #022c22 0%, #064e3b 50%, #047857 100%);">
            <div class="relative px-6 py-8 md:px-8 md:py-10">
                <h1 class="text-2xl md:text-3xl font-black text-white">Jobseeker Registration</h1>
                <p class="text-emerald-200 text-sm mt-1">Department of Manpower Development and Placement</p>
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
        <form method="POST" action="{{ route('jobseeker.register.post') }}" class="bg-white border border-brand-100 rounded-xl shadow-card overflow-hidden" id="registrationForm">
            @csrf

            <!-- Progress Steps -->
            <div class="px-6 py-5 border-b border-brand-100" style="background: linear-gradient(135deg, #f5f7fa 0%, #e9edf3 100%);">
                <div class="flex items-center justify-between max-w-3xl mx-auto">
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

            <!-- Step 1: Account Information -->
            <div class="step-content" data-step="1">
                <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #f5f7fa 0%, #e9edf3 100%);">
                    <div class="relative px-6 py-5">
                        <h2 class="text-lg font-bold text-brand-900">Account Information</h2>
                        <p class="text-sm text-brand-600">Create your login credentials</p>
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
            </div>

            <!-- Step 2: Personal Information -->
            <div class="step-content" data-step="2" style="display:none;">
                <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #f5f7fa 0%, #e9edf3 100%);">
                    <div class="relative px-6 py-5">
                        <h2 class="text-lg font-bold text-brand-900">Personal Information</h2>
                        <p class="text-sm text-brand-600">Tell us about yourself</p>
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
            </div>

            <!-- Step 3: Address -->
            <div class="step-content" data-step="3" style="display:none;">
                <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #f5f7fa 0%, #e9edf3 100%);">
                    <div class="relative px-6 py-5">
                        <h2 class="text-lg font-bold text-brand-900">Address</h2>
                        <p class="text-sm text-brand-600">Where do you currently reside?</p>
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
            </div>

            <!-- Step 4: Education -->
            <div class="step-content" data-step="4" style="display:none;">
                <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #f5f7fa 0%, #e9edf3 100%);">
                    <div class="relative px-6 py-5">
                        <h2 class="text-lg font-bold text-brand-900">Education</h2>
                        <p class="text-sm text-brand-600">Your educational background</p>
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
            </div>

            <!-- Step 5: Skills -->
            <div class="step-content" data-step="5" style="display:none;">
                <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #f5f7fa 0%, #e9edf3 100%);">
                    <div class="relative px-6 py-5">
                        <h2 class="text-lg font-bold text-brand-900">Skills</h2>
                        <p class="text-sm text-brand-600">What are you good at?</p>
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
            </div>

            <!-- Step 6: Job Preferences -->
            <div class="step-content" data-step="6" style="display:none;">
                <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #f5f7fa 0%, #e9edf3 100%);">
                    <div class="relative px-6 py-5">
                        <h2 class="text-lg font-bold text-brand-900">Job Preferences</h2>
                        <p class="text-sm text-brand-600">What kind of job are you looking for?</p>
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
            </div>

            <!-- Step 7: Social Status -->
            <div class="step-content" data-step="7" style="display:none;">
                <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #f5f7fa 0%, #e9edf3 100%);">
                    <div class="relative px-6 py-5">
                        <h2 class="text-lg font-bold text-brand-900">Social Status</h2>
                        <p class="text-sm text-brand-600">Additional information</p>
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
            </div>

            <!-- Navigation Buttons -->
            <div class="px-6 py-5 border-t border-brand-100" style="background: linear-gradient(135deg, #f5f7fa 0%, #e9edf3 100%);">
                <div class="flex items-center justify-between max-w-3xl mx-auto">
                    <button type="button" id="prevBtn" class="px-6 py-2.5 text-brand-700 font-semibold rounded-lg border border-brand-300 hover:bg-brand-50 transition flex items-center gap-2" style="display:none;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back
                    </button>
                    <button type="button" id="nextBtn" class="px-8 py-2.5 text-white font-semibold rounded-lg transition shadow-lg hover:shadow-xl flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-500 hover:to-teal-400">
                        Next
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <button type="submit" id="submitBtn" class="px-8 py-2.5 text-white font-semibold rounded-lg transition shadow-lg hover:shadow-xl flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-500 hover:to-teal-400" 
                            style="display:none;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Register
                    </button>
                </div>
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

<style>
.step-dot {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 20px;
    transition: all 0.3s ease;
}

.step-dot .step-number {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #e5e7eb;
    color: #6b7280;
    font-weight: 600;
    font-size: 13px;
    transition: all 0.3s ease;
}

.step-dot .step-label {
    font-size: 13px;
    font-weight: 500;
    color: #9ca3af;
    transition: all 0.3s ease;
}

.step-dot.active .step-number {
    background: linear-gradient(135deg, #022c22 0%, #059669 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(5, 150, 105, 0.3);
}

.step-dot.active .step-label {
    color: #064e3b;
    font-weight: 700;
}

.step-dot.completed .step-number {
    background: #10b981;
    color: white;
}

.step-dot.completed .step-label {
    color: #10b981;
}

.step-line {
    flex: 1;
    height: 2px;
    background: #e5e7eb;
    transition: all 0.3s ease;
    min-width: 20px;
}

.step-line.completed {
    background: #10b981;
}

@media (max-width: 768px) {
    .step-dot .step-label {
        display: none;
    }
    .step-line {
        min-width: 10px;
    }
    .step-dot .step-number {
        width: 24px;
        height: 24px;
        font-size: 11px;
    }
}
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
        // Show/hide step contents
        stepContents.forEach(content => {
            const step = parseInt(content.dataset.step);
            content.style.display = step === currentStep ? 'block' : 'none';
        });

        // Update step dots
        stepDots.forEach(dot => {
            const step = parseInt(dot.dataset.step);
            dot.classList.remove('active', 'completed');
            if (step === currentStep) {
                dot.classList.add('active');
            } else if (step < currentStep) {
                dot.classList.add('completed');
            }
        });

        // Update step lines
        document.querySelectorAll('.step-line').forEach((line, index) => {
            line.classList.toggle('completed', index < currentStep - 1);
        });

        // Update navigation buttons
        prevBtn.style.display = currentStep > 1 ? 'inline-flex' : 'none';
        if (currentStep === totalSteps) {
            nextBtn.style.display = 'none';
            submitBtn.style.display = 'inline-flex';
        } else {
            nextBtn.style.display = 'inline-flex';
            submitBtn.style.display = 'none';
        }
    }

    function validateStep(step) {
        const currentContent = document.querySelector(`.step-content[data-step="${step}"]`);
        const inputs = currentContent.querySelectorAll('input[required], select[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('border-red-500');
            } else {
                input.classList.remove('border-red-500');
            }
        });

        return isValid;
    }

    function goToStep(step) {
        if (step > currentStep && !validateStep(currentStep)) {
            return;
        }
        currentStep = step;
        updateSteps();
    }

    // Next button
    nextBtn.addEventListener('click', function() {
        if (validateStep(currentStep)) {
            if (currentStep < totalSteps) {
                goToStep(currentStep + 1);
            }
        }
    });

    // Previous button
    prevBtn.addEventListener('click', function() {
        if (currentStep > 1) {
            goToStep(currentStep - 1);
        }
    });

    // Click on step dots to navigate
    stepDots.forEach(dot => {
        dot.addEventListener('click', function() {
            const step = parseInt(this.dataset.step);
            if (step < currentStep || validateStep(currentStep)) {
                goToStep(step);
            }
        });
    });

    // Enter key to go to next step
    form.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
            if (currentStep === totalSteps) {
                return;
            }
            e.preventDefault();
            if (validateStep(currentStep)) {
                goToStep(currentStep + 1);
            }
        }
    });

    // Initialize
    updateSteps();
});
</script>
@endsection