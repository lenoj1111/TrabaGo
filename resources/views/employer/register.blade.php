@extends('layouts.public')

@section('title', 'DMDP Employer Registration - Department of Manpower Development and Placement')

@section('content')
<div class="bg-gradient-to-b from-brand-50/50 to-white min-h-screen py-8">
    <div class="max-w-5xl mx-auto px-4">
        <!-- Breadcrumb -->
        <div class="bg-white/80 backdrop-blur border border-brand-100 rounded-xl px-5 py-3 mb-6 shadow-sm">
            <nav class="text-sm">
                <ol class="flex items-center gap-2 text-brand-600">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-900 transition-colors">Home</a></li>
                    <li><span class="text-brand-300">/</span></li>
                    <li class="text-brand-900 font-semibold">Employer Registration</li>
                </ol>
            </nav>
        </div>

        <!-- Header -->
        <div class="relative overflow-hidden rounded-2xl mb-8" style="background: linear-gradient(135deg, #1b2739 0%, #33455e 50%, #405673 100%);">
            <div class="relative px-6 py-8 md:px-8 md:py-10">
                <h1 class="text-2xl md:text-3xl font-bold text-white">Employer Registration</h1>
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
        <form method="POST" action="{{ route('employer.register.post') }}" class="bg-white border border-brand-100 rounded-xl shadow-card overflow-hidden" id="registrationForm" enctype="multipart/form-data">
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
                            <span class="step-label">Company</span>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-dot" data-step="3">
                            <span class="step-number">3</span>
                            <span class="step-label">Contact</span>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-dot" data-step="4">
                            <span class="step-number">4</span>
                            <span class="step-label">Documents</span>
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
                                <option value="employer" selected>Employer</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Company Information -->
            <div class="step-content" data-step="2" style="display:none;">
                <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #f5f7fa 0%, #e9edf3 100%);">
                    <div class="relative px-6 py-5">
                        <h2 class="text-lg font-bold text-brand-900">Company Information</h2>
                        <p class="text-sm text-brand-600">Tell us about your company</p>
                    </div>
                </div>

                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">
                                <span class="text-red-500">*</span> Company Name
                            </label>
                            <input type="text" name="company_name" value="{{ old('company_name') }}" 
                                   class="w-full px-4 py-2.5 bg-white border @error('company_name') border-red-500 @else border-brand-200 @enderror rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                            @error('company_name')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Company Description</label>
                            <textarea name="company_description" rows="3" 
                                      class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none"
                                      placeholder="Brief description of your company">{{ old('company_description') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Industry</label>
                            <select name="industry" class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none">
                                <option value="">Select Industry...</option>
                                <option value="Information Technology" {{ old('industry') == 'Information Technology' ? 'selected' : '' }}>Information Technology</option>
                                <option value="Manufacturing" {{ old('industry') == 'Manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                <option value="Retail" {{ old('industry') == 'Retail' ? 'selected' : '' }}>Retail</option>
                                <option value="Healthcare" {{ old('industry') == 'Healthcare' ? 'selected' : '' }}>Healthcare</option>
                                <option value="Education" {{ old('industry') == 'Education' ? 'selected' : '' }}>Education</option>
                                <option value="Construction" {{ old('industry') == 'Construction' ? 'selected' : '' }}>Construction</option>
                                <option value="Finance" {{ old('industry') == 'Finance' ? 'selected' : '' }}>Finance</option>
                                <option value="Hospitality" {{ old('industry') == 'Hospitality' ? 'selected' : '' }}>Hospitality</option>
                                <option value="Transportation" {{ old('industry') == 'Transportation' ? 'selected' : '' }}>Transportation</option>
                                <option value="Other" {{ old('industry') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Company Size</label>
                            <select name="company_size" class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none">
                                <option value="">Select Size...</option>
                                <option value="1-10" {{ old('company_size') == '1-10' ? 'selected' : '' }}>1-10 employees</option>
                                <option value="11-50" {{ old('company_size') == '11-50' ? 'selected' : '' }}>11-50 employees</option>
                                <option value="51-200" {{ old('company_size') == '51-200' ? 'selected' : '' }}>51-200 employees</option>
                                <option value="201-500" {{ old('company_size') == '201-500' ? 'selected' : '' }}>201-500 employees</option>
                                <option value="501-1000" {{ old('company_size') == '501-1000' ? 'selected' : '' }}>501-1000 employees</option>
                                <option value="1000+" {{ old('company_size') == '1000+' ? 'selected' : '' }}>1000+ employees</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Website</label>
                            <input type="url" name="website" value="{{ old('website') }}" placeholder="https://www.company.com" 
                                   class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Company Type</label>
                            <select name="company_type" class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none">
                                <option value="">Select Type...</option>
                                <option value="Private" {{ old('company_type') == 'Private' ? 'selected' : '' }}>Private</option>
                                <option value="Public" {{ old('company_type') == 'Public' ? 'selected' : '' }}>Public</option>
                                <option value="Government" {{ old('company_type') == 'Government' ? 'selected' : '' }}>Government</option>
                                <option value="Non-Profit" {{ old('company_type') == 'Non-Profit' ? 'selected' : '' }}>Non-Profit</option>
                                <option value="Sole Proprietorship" {{ old('company_type') == 'Sole Proprietorship' ? 'selected' : '' }}>Sole Proprietorship</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Contact Information -->
            <div class="step-content" data-step="3" style="display:none;">
                <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #f5f7fa 0%, #e9edf3 100%);">
                    <div class="relative px-6 py-5">
                        <h2 class="text-lg font-bold text-brand-900">Contact Information</h2>
                        <p class="text-sm text-brand-600">How can we reach you?</p>
                    </div>
                </div>

                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">
                                <span class="text-red-500">*</span> Contact Person
                            </label>
                            <input type="text" name="contact_person" value="{{ old('contact_person') }}" 
                                   class="w-full px-4 py-2.5 bg-white border @error('contact_person') border-red-500 @else border-brand-200 @enderror rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                            @error('contact_person')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">
                                <span class="text-red-500">*</span> Contact Position
                            </label>
                            <input type="text" name="contact_position" value="{{ old('contact_position') }}" 
                                   class="w-full px-4 py-2.5 bg-white border @error('contact_position') border-red-500 @else border-brand-200 @enderror rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                            @error('contact_position')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">
                                <span class="text-red-500">*</span> Phone Number
                            </label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" 
                                   class="w-full px-4 py-2.5 bg-white border @error('phone') border-red-500 @else border-brand-200 @enderror rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                            @error('phone')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Mobile Number</label>
                            <input type="tel" name="mobile" value="{{ old('mobile') }}" 
                                   class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Company Address</label>
                            <input type="text" name="address" value="{{ old('address') }}" placeholder="Street address" 
                                   class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">City</label>
                            <input type="text" name="city" value="{{ old('city') }}" 
                                   class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Province</label>
                            <input type="text" name="province" value="{{ old('province') }}" 
                                   class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Postal Code</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code') }}" 
                                   class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Country</label>
                            <input type="text" name="country" value="{{ old('country', 'Philippines') }}" 
                                   class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 4: Documents for Accreditation -->
            <div class="step-content" data-step="4" style="display:none;">
                <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #f5f7fa 0%, #e9edf3 100%);">
                    <div class="relative px-6 py-5">
                        <h2 class="text-lg font-bold text-brand-900">Accreditation Documents</h2>
                        <p class="text-sm text-brand-600">Upload required documents for accreditation</p>
                    </div>
                </div>

                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Business Permit / DTI Registration</label>
                            <input type="file" name="documents[business_permit]" accept=".pdf,.jpg,.jpeg,.png" 
                                   class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100" />
                            <p class="text-xs text-brand-400 mt-1">Accepted formats: PDF, JPG, PNG (Max: 5MB)</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">SEC Registration (if applicable)</label>
                            <input type="file" name="documents[sec_registration]" accept=".pdf,.jpg,.jpeg,.png" 
                                   class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100" />
                            <p class="text-xs text-brand-400 mt-1">Accepted formats: PDF, JPG, PNG (Max: 5MB)</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Mayor's Permit / Barangay Clearance</label>
                            <input type="file" name="documents[mayors_permit]" accept=".pdf,.jpg,.jpeg,.png" 
                                   class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100" />
                            <p class="text-xs text-brand-400 mt-1">Accepted formats: PDF, JPG, PNG (Max: 5MB)</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Tax Identification Number (TIN)</label>
                            <input type="file" name="documents[tin]" accept=".pdf,.jpg,.jpeg,.png" 
                                   class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100" />
                            <p class="text-xs text-brand-400 mt-1">Accepted formats: PDF, JPG, PNG (Max: 5MB)</p>
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-700">
                                <strong>Note:</strong> Your submitted documents will be reviewed by JPO and DMDP for accreditation. 
                                You will be notified once your accreditation is approved.
                            </p>
                        </div>
                        <div>
                            <label class="flex items-center gap-2 text-sm text-brand-700 cursor-pointer">
                                <input type="checkbox" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} 
                                       class="w-4 h-4 text-brand-600 rounded border-brand-300 focus:ring-brand-500" />
                                I agree to the terms and conditions and confirm that all information provided is accurate
                            </label>
                            @error('terms')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
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
                    <button type="button" id="nextBtn" class="px-8 py-2.5 text-white font-semibold rounded-lg transition shadow-lg hover:shadow-xl flex items-center justify-center gap-2" 
                            style="background: linear-gradient(135deg, #1b2739 0%, #33455e 100%);">
                        Next
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <button type="submit" id="submitBtn" class="px-8 py-2.5 text-white font-semibold rounded-lg transition shadow-lg hover:shadow-xl flex items-center justify-center gap-2" 
                            style="background: linear-gradient(135deg, #1b2739 0%, #33455e 100%); display:none;">
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
    background: linear-gradient(135deg, #1b2739 0%, #33455e 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(27, 39, 57, 0.3);
}

.step-dot.active .step-label {
    color: #1b2739;
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
    const totalSteps = 4;
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

        // Special validation for step 4 (terms checkbox)
        if (step === 4) {
            const termsCheckbox = document.querySelector('input[name="terms"]');
            if (!termsCheckbox.checked) {
                isValid = false;
                termsCheckbox.classList.add('border-red-500');
            } else {
                termsCheckbox.classList.remove('border-red-500');
            }
        }

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

    // File input validation - show filename
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function() {
            if (this.files.length > 0) {
                const label = this.closest('div').querySelector('.file-label');
                if (label) {
                    label.textContent = this.files[0].name;
                }
            }
        });
    });
});
</script>
@endsection