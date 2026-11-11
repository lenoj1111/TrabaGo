@extends('layouts.admin')

@section('title', 'Provision Account')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8" x-data="{ selectedRole: '{{ old('role', 'employer') }}', isImmigrant: {{ old('is_immigrant') ? 'true' : 'false' }} }">
    <div class="mx-auto max-w-4xl space-y-8">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 inline-flex items-center gap-1">
                    &larr; Back to Users Directory
                </a>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Provision System Account</h1>
                <p class="text-xs text-slate-500">Create Employer, Jobseeker, or Staff accounts with official DMDP Region VII provisioning.</p>
            </div>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1">
                <p class="font-black text-rose-900 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Please correct the errors below:</span>
                </p>
                <ul class="list-disc list-inside space-y-0.5 font-medium">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-10 shadow-sm">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-8">
                @csrf

                <!-- Account Credentials & Role Selection Section -->
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Account Role & Credentials</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-1.5 sm:col-span-2">
                            <label class="text-xs font-bold text-slate-700">Account Role <span class="text-rose-500">*</span></label>
                            <select name="role" x-model="selectedRole" required
                                    class="w-full px-4 py-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                                <option value="employer">EMPLOYER (Corporate Partner / Company)</option>
                                <option value="jobseeker">JOBSEEKER (Candidate Profile)</option>
                                <option value="jpo">JOB PLACEMENT OFFICER (JPO)</option>
                                <option value="trainer">TECHNICAL TRAINER / INSTRUCTOR</option>
                                <option value="admin">SYSTEM ADMINISTRATOR</option>
                            </select>
                            <p class="text-[11px] text-slate-500">Admins have full authority to create and manage all account types across Region VII.</p>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Account Email (Login ID) <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" required 
                                   placeholder="account@domain.com"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Contact Phone Number <span class="text-rose-500">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone') }}" required 
                                   placeholder="e.g. 09171234567 or (032) 253-1234"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Password <span class="text-rose-500">*</span></label>
                            <input type="password" name="password" required 
                                   placeholder="Minimum 8 characters"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Confirm Password <span class="text-rose-500">*</span></label>
                            <input type="password" name="password_confirmation" required 
                                   placeholder="Re-enter password"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>
                    </div>
                </div>

                <!-- 1. EMPLOYER SPECIFIC FIELDS -->
                <div x-show="selectedRole === 'employer'" x-cloak class="space-y-6">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>Corporate Partner Profile & Authorized Contacts</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-1.5 sm:col-span-2">
                            <label class="text-xs font-bold text-slate-700">Registered Business / Company Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="company_name" value="{{ old('company_name') }}" :required="selectedRole === 'employer'"
                                   placeholder="e.g. Visayas Technology Solutions Inc."
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5 sm:col-span-2">
                            <label class="text-xs font-bold text-slate-700">Authorized Contact Person <span class="text-rose-500">*</span></label>
                            <input type="text" name="contact_person" value="{{ old('contact_person') }}" :required="selectedRole === 'employer'"
                                   placeholder="Full Name of HR Manager or Liaison Officer"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Primary Contact Email</label>
                            <input type="email" name="primary_email" value="{{ old('primary_email') }}" 
                                   placeholder="hr@company.com (defaults to account email)"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Secondary Email (Backup / Recruitment)</label>
                            <input type="email" name="secondary_email" value="{{ old('secondary_email') }}" 
                                   placeholder="recruitment@company.com"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5 sm:col-span-2">
                            <label class="text-xs font-bold text-slate-700">Company Physical Address (Region VII) <span class="text-rose-500">*</span></label>
                            <input type="text" name="company_address" value="{{ old('company_address') }}" :required="selectedRole === 'employer'"
                                   placeholder="e.g. Cebu IT Park, Lahug, Cebu City, 6000"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Nature of Business / Industry</label>
                            <input type="text" name="business_nature" value="{{ old('business_nature') }}" 
                                   placeholder="e.g. BPO / Information Technology, Manufacturing, Hospitality"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5 flex flex-col justify-center">
                            <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-emerald-800 p-3 rounded-xl bg-emerald-50 border border-emerald-200 select-none">
                                <input type="checkbox" name="is_accredited" value="1" {{ old('is_accredited') ? 'checked' : '' }}
                                       class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                <span>Directly Accredit & Issue Certificate of Accreditation</span>
                            </label>
                            <p class="text-[10px] text-slate-400 mt-1">If checked, employer will be immediately accredited without awaiting JPO papers.</p>
                        </div>
                    </div>
                </div>

                <!-- 2. JOBSEEKER SPECIFIC FIELDS -->
                <div x-show="selectedRole === 'jobseeker'" x-cloak class="space-y-6">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Jobseeker Profile & Sector Demographics (Region 7)</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">First Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" :required="selectedRole === 'jobseeker'"
                                   placeholder="Juan"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Last Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" :required="selectedRole === 'jobseeker'"
                                   placeholder="Dela Cruz"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Middle Name</label>
                            <input type="text" name="middle_name" value="{{ old('middle_name') }}" 
                                   placeholder="Santos"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5 sm:col-span-2">
                            <label class="text-xs font-bold text-slate-700">Employment Status <span class="text-rose-500">*</span></label>
                            <select name="employment_status" :required="selectedRole === 'jobseeker'"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                                <option value="Unemployed / Open to all" selected>Open to all / Unemployed</option>
                                <option value="Employed">Currently Employed (Flexible & open to new opportunities)</option>
                                <option value="Self-Employed">Self-Employed / Freelancer</option>
                                <option value="Fresh Graduate">Fresh Graduate</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Regional Scope</label>
                            <input type="text" value="Region VII (Central Visayas - Cebu Hub)" disabled
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 text-xs cursor-not-allowed">
                        </div>
                    </div>

                    <!-- Sector Inclusions & Demographics (NSRP Alignment) -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                        <span class="text-xs font-black uppercase tracking-wider text-slate-800 block">Sector Inclusions & Specialized Demographics</span>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- LGBTQ+ Membership -->
                            <label class="flex items-center gap-2.5 p-3 rounded-xl bg-white border border-slate-200 cursor-pointer select-none">
                                <input type="checkbox" name="is_lgbtq" value="1" {{ old('is_lgbtq') ? 'checked' : '' }}
                                       class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                <span class="text-xs font-bold text-slate-800">LGBTQ+ Member</span>
                            </label>

                            <!-- Senior Citizen -->
                            <label class="flex items-center gap-2.5 p-3 rounded-xl bg-white border border-slate-200 cursor-pointer select-none">
                                <input type="checkbox" name="is_senior_citizen" value="1" {{ old('is_senior_citizen') ? 'checked' : '' }}
                                       class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                <span class="text-xs font-bold text-slate-800">Senior Citizen (60+)</span>
                            </label>

                            <!-- Immigrant Worker -->
                            <label class="flex items-center gap-2.5 p-3 rounded-xl bg-white border border-slate-200 cursor-pointer select-none">
                                <input type="checkbox" name="is_immigrant" value="1" x-model="isImmigrant" {{ old('is_immigrant') ? 'checked' : '' }}
                                       class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                <span class="text-xs font-bold text-slate-800">Immigrant / Migrant</span>
                            </label>
                        </div>

                        <div x-show="isImmigrant" class="pt-2">
                            <label class="text-xs font-bold text-slate-700">Immigrant Country / Province of Origin</label>
                            <input type="text" name="immigrant_origin" value="{{ old('immigrant_origin') }}" 
                                   placeholder="e.g. Bohol, Leyte, Mindanao, or Foreign Country"
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>
                    </div>
                </div>

                <!-- 3. STAFF SPECIFIC FIELDS (Admin, JPO, Trainer) -->
                <div x-show="['admin', 'jpo', 'trainer'].includes(selectedRole)" x-cloak class="space-y-6">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>DMDP Staff & Officer Profile</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Full Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="full_name" value="{{ old('full_name') }}" :required="['admin', 'jpo', 'trainer'].includes(selectedRole)"
                                   placeholder="e.g. Maria Santos"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Official Position <span class="text-rose-500">*</span></label>
                            <input type="text" name="position" value="{{ old('position') }}" :required="['admin', 'jpo', 'trainer'].includes(selectedRole)"
                                   placeholder="e.g. Labor Employment Officer II"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Department</label>
                            <input type="text" name="department" value="{{ old('department', 'DMDP Cebu City') }}" 
                                   placeholder="e.g. Skills Training Division"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Office Location</label>
                            <input type="text" name="office" value="{{ old('office', 'Ramos DMDP Building') }}" 
                                   placeholder="e.g. Ramos DMDP Building"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5 sm:col-span-2">
                            <label class="text-xs font-bold text-slate-700">Specialization / Expertise</label>
                            <input type="text" name="specialization" value="{{ old('specialization') }}" 
                                   placeholder="e.g. Technical Skills, IT, Placement Coordination"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Action Controls -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('admin.users.index') }}" 
                       class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Provision Account</span>
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>
@endsection
