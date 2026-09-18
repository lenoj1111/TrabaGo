@extends('layouts.public')

@section('title', 'Employer Registration - DMDP Cebu City')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 md:py-12">
    <div class="max-w-4xl mx-auto px-5">

        <!-- Header -->
        <div class="bg-green-950 rounded-2xl p-6 md:p-8 mb-6">
            <p class="text-xs font-semibold text-green-400 uppercase tracking-widest">DMDP Registration</p>
            <h1 class="text-2xl md:text-3xl font-extrabold text-white mt-1">Employer Registration</h1>
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
        <form method="POST" action="{{ route('employer.register.post') }}" class="bg-white border border-gray-200 rounded-2xl overflow-hidden" id="registrationForm" enctype="multipart/form-data">
            @csrf

            <!-- Progress Steps -->
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <div class="flex items-center justify-between max-w-2xl mx-auto">
                    <div class="flex items-center gap-2" id="stepIndicator">
                        <div class="step-dot active" data-step="1">
                            <span class="step-number">1</span><span class="step-label">Account</span>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-dot" data-step="2">
                            <span class="step-number">2</span><span class="step-label">Company</span>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-dot" data-step="3">
                            <span class="step-number">3</span><span class="step-label">Contact</span>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-dot" data-step="4">
                            <span class="step-number">4</span><span class="step-label">Documents</span>
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
                                <option value="employer" selected>Employer</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Company -->
            <div class="step-content" data-step="2" style="display:none;">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h2 class="text-base font-bold text-gray-900">Company Information</h2>
                    <p class="text-sm text-gray-500">Tell us about your company</p>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1"><span class="text-red-500">*</span> Company Name</label>
                            <input type="text" name="company_name" value="{{ old('company_name') }}" class="w-full px-4 py-2.5 bg-white border @error('company_name') border-red-400 @else border-gray-200 @enderror rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                            @error('company_name')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Company Description</label>
                            <textarea name="company_description" rows="3" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" placeholder="Brief description">{{ old('company_description') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Industry</label>
                            <select name="industry" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm">
                                <option value="">Select...</option>
                                @foreach(['Information Technology','Manufacturing','Retail','Healthcare','Education','Construction','Finance','Hospitality','Transportation','Other'] as $ind)
                                    <option value="{{ $ind }}" {{ old('industry') == $ind ? 'selected' : '' }}>{{ $ind }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Company Size</label>
                            <select name="company_size" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm">
                                <option value="">Select...</option>
                                @foreach(['1-10','11-50','51-200','201-500','501-1000','1000+'] as $sz)
                                    <option value="{{ $sz }}" {{ old('company_size') == $sz ? 'selected' : '' }}>{{ $sz }} employees</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                            <input type="url" name="website" value="{{ old('website') }}" placeholder="https://..." class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Company Type</label>
                            <select name="company_type" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm">
                                <option value="">Select...</option>
                                @foreach(['Private','Public','Government','Non-Profit','Sole Proprietorship'] as $ct)
                                    <option value="{{ $ct }}" {{ old('company_type') == $ct ? 'selected' : '' }}>{{ $ct }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Contact -->
            <div class="step-content" data-step="3" style="display:none;">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h2 class="text-base font-bold text-gray-900">Contact Information</h2>
                    <p class="text-sm text-gray-500">How can we reach you?</p>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1"><span class="text-red-500">*</span> Contact Person</label>
                            <input type="text" name="contact_person" value="{{ old('contact_person') }}" class="w-full px-4 py-2.5 bg-white border @error('contact_person') border-red-400 @else border-gray-200 @enderror rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                            @error('contact_person')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1"><span class="text-red-500">*</span> Contact Position</label>
                            <input type="text" name="contact_position" value="{{ old('contact_position') }}" class="w-full px-4 py-2.5 bg-white border @error('contact_position') border-red-400 @else border-gray-200 @enderror rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                            @error('contact_position')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1"><span class="text-red-500">*</span> Phone Number</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-2.5 bg-white border @error('phone') border-red-400 @else border-gray-200 @enderror rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                            @error('phone')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                            <input type="tel" name="mobile" value="{{ old('mobile') }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Company Address</label>
                            <input type="text" name="address" value="{{ old('address') }}" placeholder="Street address" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input type="text" name="city" value="{{ old('city') }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Province</label>
                            <input type="text" name="province" value="{{ old('province') }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code') }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                            <input type="text" name="country" value="{{ old('country', 'Philippines') }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition outline-none text-sm" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 4: Documents -->
            <div class="step-content" data-step="4" style="display:none;">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h2 class="text-base font-bold text-gray-900">Accreditation Documents</h2>
                    <p class="text-sm text-gray-500">Upload required documents for accreditation</p>
                </div>
                <div class="px-6 py-6 space-y-5">
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                        <p class="text-xs font-semibold text-green-900">Official Requirements — DMDP Cebu City / DOLE Guidelines</p>
                        <p class="text-xs text-green-700 mt-0.5">Original documents should be presented for validation when applicable.</p>
                    </div>

                    <div class="space-y-4">
                        <p class="text-sm font-semibold text-gray-900">Mandatory Business Credentials</p>
                        
                        @php $docs = [
                            ['name' => 'documents[bir_2303]', 'label' => 'BIR Certificate of Registration (Form 2303)', 'note' => 'Current / Valid'],
                            ['name' => 'documents[sec_dti]', 'label' => 'SEC or DTI Registration', 'note' => 'Perpetual / Registered'],
                            ['name' => 'documents[mayors_permit]', 'label' => "Mayor's Business Permit (" . date('Y') . ")", 'note' => 'Calendar Year ' . date('Y')],
                            ['name' => 'documents[philjobnet_proof]', 'label' => 'PhilJobNet Proof of Registration', 'note' => 'Active'],
                            ['name' => 'documents[letter_of_intent]', 'label' => 'Letter of Intent', 'note' => 'For specific services'],
                        ]; @endphp
                        @foreach($docs as $doc)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $doc['label'] }} <span class="text-xs text-green-600 font-normal">· {{ $doc['note'] }}</span></label>
                                <input type="file" name="{{ $doc['name'] }}" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100 file:cursor-pointer" />
                            </div>
                        @endforeach
                    </div>

                    <div class="p-4 rounded-xl border border-gray-200 bg-gray-50 space-y-3">
                        <p class="text-sm font-semibold text-gray-900">Specialized Agency Documents <span class="text-xs text-gray-500 font-normal">(when applicable)</span></p>
                        @php $agencyDocs = [
                            ['name' => 'documents[dole_license]', 'label' => 'DOLE License / DO 174', 'note' => 'For Licensed PRA / Subcontractor'],
                            ['name' => 'documents[dmw_license]', 'label' => 'DMW License', 'note' => 'For Overseas Recruitment Agency'],
                            ['name' => 'documents[dmw_job_orders]', 'label' => 'DMW Approved Job Orders', 'note' => 'For Overseas Recruitment'],
                        ]; @endphp
                        @foreach($agencyDocs as $doc)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $doc['label'] }} <span class="text-xs text-gray-500 font-normal">· {{ $doc['note'] }}</span></label>
                                <input type="file" name="{{ $doc['name'] }}" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 file:cursor-pointer" />
                            </div>
                        @endforeach
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-xl p-3">
                        <p class="text-xs text-green-800"><strong>Note:</strong> Documents will be reviewed by JPO and DMDP officers. You will be notified once approved.</p>
                    </div>

                    <div>
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} class="w-4 h-4 text-green-600 rounded border-gray-300 focus:ring-green-500" />
                            I agree to the terms and conditions and confirm all information is accurate
                        </label>
                        @error('terms')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
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
    const totalSteps = 4;
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
        if(prevBtn) prevBtn.style.display = currentStep > 1 ? 'inline-flex' : 'none';
        if(nextBtn && submitBtn) { if(currentStep === totalSteps) { nextBtn.style.display='none'; submitBtn.style.display='inline-flex'; } else { nextBtn.style.display='inline-flex'; submitBtn.style.display='none'; } }
    }

    function validateStep(step) {
        const c = document.querySelector(`.step-content[data-step="${step}"]`);
        if(!c) return true;
        const inputs = c.querySelectorAll('input[required], select[required]');
        let valid = true;
        inputs.forEach(i => { if(!i.value.trim()) { valid=false; i.classList.add('border-red-400'); } else { i.classList.remove('border-red-400'); } });
        if(step === 4) { const t = document.querySelector('input[name="terms"]'); if(t && !t.checked) { valid=false; } }
        return valid;
    }

    function goToStep(step) { if(step > currentStep && !validateStep(currentStep)) return; currentStep = step; updateSteps(); form.scrollIntoView({ behavior: 'smooth', block: 'start' }); }

    if(nextBtn) nextBtn.addEventListener('click', function(e) { e.preventDefault(); if(validateStep(currentStep) && currentStep < totalSteps) goToStep(currentStep+1); else if(!validateStep(currentStep)) alert('Please fill in all required fields.'); });
    if(prevBtn) prevBtn.addEventListener('click', function(e) { e.preventDefault(); if(currentStep > 1) goToStep(currentStep-1); });
    if(submitBtn) submitBtn.addEventListener('click', function(e) { let allValid = true; for(let i=1;i<=totalSteps;i++) { if(!validateStep(i)) { allValid=false; if(i<currentStep) goToStep(i); break; } } if(allValid) form.submit(); else { e.preventDefault(); alert('Please fill in all required fields.'); } });

    stepDots.forEach(d => { d.addEventListener('click', function() { const s = parseInt(this.dataset.step); if(s < currentStep || validateStep(currentStep)) goToStep(s); }); });

    form.addEventListener('keydown', function(e) { if(e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') { if(currentStep < totalSteps) { e.preventDefault(); if(validateStep(currentStep)) goToStep(currentStep+1); } } });

    updateSteps();
});
</script>
@endsection