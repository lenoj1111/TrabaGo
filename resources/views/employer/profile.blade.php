@extends('layouts.employer')

@section('title', 'Company Profile & Security - Employer Portal')

@section('content')
<div x-data="{ 
    activeTab: '{{ request('tab', ($errors->has('current_password') || $errors->has('password')) ? 'security' : 'profile') }}',
    showCurrent: false,
    showNew: false,
    showConfirm: false
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-4xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="h-16 w-16 rounded-2xl bg-gradient-to-tr from-emerald-600 to-emerald-400 flex items-center justify-center text-white shrink-0 shadow-lg shadow-emerald-500/30">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-2xl sm:text-3xl font-black">{{ $employer->company_name }}</h1>
                        @if($employer->is_accredited)
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-400/20 border border-emerald-400/30 px-2.5 py-0.5 text-[11px] font-bold text-emerald-300">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Officially Accredited
                            </span>
                        @else
                            <span class="rounded-full bg-amber-400/20 border border-amber-400/30 px-2.5 py-0.5 text-[11px] font-bold text-amber-300">
                                Accreditation Under Review
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-300">{{ $user->email }} &bull; Employer ID #{{ $employer->employer_id }}</p>
                    <p class="text-xs text-emerald-300 font-semibold">{{ $profile->position ?? 'Corporate Partner' }} {{ $profile->office ? '&bull; ' . $profile->office : '' }}</p>
                </div>
            </div>

            <a href="{{ route('employer.accreditation') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold border border-white/20 backdrop-blur-sm transition-all self-start sm:self-auto">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Manage Accreditation &rarr;</span>
            </a>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2 shadow-sm">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2 shadow-sm">
                <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                {{ session('error') }}
            </div>
        @endif

        <!-- Tab Switcher Navigation -->
        <div class="flex items-center gap-2 bg-white p-2 rounded-2xl border border-slate-200 shadow-sm">
            <button type="button" 
                    @click="activeTab = 'profile'"
                    :class="activeTab === 'profile' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100'"
                    class="flex-1 py-2.5 px-4 rounded-xl font-bold text-xs transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Company & Representative Profile</span>
            </button>
            <button type="button" 
                    @click="activeTab = 'security'"
                    :class="activeTab === 'security' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100'"
                    class="flex-1 py-2.5 px-4 rounded-xl font-bold text-xs transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Security & Reset Password</span>
                @if($errors->has('current_password') || $errors->has('password'))
                    <span class="h-2 w-2 rounded-full bg-rose-400 animate-pulse"></span>
                @endif
            </button>
        </div>

        <!-- Tab 1: Profile Information & Update Profile -->
        <div x-show="activeTab === 'profile'" x-cloak class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-8">
            <form action="{{ route('employer.profile.update') }}" method="POST" class="space-y-8">
                @csrf

                <!-- Section 1: Enterprise Information -->
                <div class="space-y-4">
                    <div class="border-b border-slate-100 pb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Enterprise & Establishment Information</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Registered Business / Company Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="company_name" value="{{ old('company_name', $employer->company_name) }}" required
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            @error('company_name')
                                <p class="text-[11px] text-rose-600 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Industry / Line of Business</label>
                            <select name="specialization" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                                <option value="">-- Select Industry Sector --</option>
                                <option value="Information Technology & BPO" {{ old('specialization', $profile->specialization ?? '') === 'Information Technology & BPO' ? 'selected' : '' }}>Information Technology & BPO</option>
                                <option value="Retail, Wholesale & Distribution" {{ old('specialization', $profile->specialization ?? '') === 'Retail, Wholesale & Distribution' ? 'selected' : '' }}>Retail, Wholesale & Distribution</option>
                                <option value="Construction & Engineering" {{ old('specialization', $profile->specialization ?? '') === 'Construction & Engineering' ? 'selected' : '' }}>Construction & Engineering</option>
                                <option value="Hospitality, Hotel & Restaurant" {{ old('specialization', $profile->specialization ?? '') === 'Hospitality, Hotel & Restaurant' ? 'selected' : '' }}>Hospitality, Hotel & Restaurant</option>
                                <option value="Healthcare & Pharmaceuticals" {{ old('specialization', $profile->specialization ?? '') === 'Healthcare & Pharmaceuticals' ? 'selected' : '' }}>Healthcare & Pharmaceuticals</option>
                                <option value="Manufacturing & Industrial" {{ old('specialization', $profile->specialization ?? '') === 'Manufacturing & Industrial' ? 'selected' : '' }}>Manufacturing & Industrial</option>
                                <option value="Transportation & Logistics" {{ old('specialization', $profile->specialization ?? '') === 'Transportation & Logistics' ? 'selected' : '' }}>Transportation & Logistics</option>
                                <option value="Financial & Professional Services" {{ old('specialization', $profile->specialization ?? '') === 'Financial & Professional Services' ? 'selected' : '' }}>Financial & Professional Services</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2 space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Headquarters / Cebu Branch Address</label>
                            <input type="text" name="office" value="{{ old('office', $profile->office ?? '') }}" placeholder="e.g. 8th Floor, Cebu IT Park, Lahug, Cebu City"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Authorized Company Representative -->
                <div class="space-y-4">
                    <div class="border-b border-slate-100 pb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Authorized Company Representative / HR Contact</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Representative Full Name / Authorized Contact Person <span class="text-rose-500">*</span></label>
                            <input type="text" name="contact_person" value="{{ old('contact_person', $employer->contact_person ?? ($profile->full_name ?? ($user->full_name ?? ''))) }}" placeholder="e.g. Maria Santos" required
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Position / Designation</label>
                            <input type="text" name="position" value="{{ old('position', $profile->position ?? 'HR Manager') }}" placeholder="e.g. Human Resources Officer / Talent Lead"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Department / Division</label>
                            <input type="text" name="department" value="{{ old('department', $profile->department ?? 'Human Resources') }}" placeholder="e.g. Talent Acquisition & Placement"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Official Contact Number / Mobile</label>
                            <input type="text" name="phone" value="{{ old('phone', $employer->contact_phone ?? ($profile->phone ?? '')) }}" placeholder="e.g. (032) 234-5678 / 0912-345-6789"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Primary Official Email</label>
                            <input type="email" name="primary_email" value="{{ old('primary_email', $employer->primary_email ?? $user->email) }}" placeholder="e.g. hr@company.com"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            <span class="text-[10px] text-slate-400">Main email for recruitment correspondence and PESO endorsements.</span>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Secondary / Alternate Email</label>
                            <input type="email" name="secondary_email" value="{{ old('secondary_email', $employer->secondary_email ?? '') }}" placeholder="e.g. recruitment@company.com"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            <span class="text-[10px] text-slate-400">Backup email for notifications, certificates, and alerts.</span>
                        </div>

                        <div class="sm:col-span-2 space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Account System Login Email</label>
                            <input type="text" value="{{ $user->email }}" disabled
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs bg-slate-100 text-slate-500 cursor-not-allowed">
                            <span class="text-[10px] text-slate-400">Account login ID credential.</span>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                    <p class="text-[11px] text-slate-400">Accredited employer profile data is shared with the Cebu City DMDP Placement Division.</p>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 px-8 py-3 text-xs font-black text-white shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save Company Profile
                    </button>
                </div>
            </form>
        </div>

        <!-- Tab 2: Reset Password & Security -->
        <div x-show="activeTab === 'security'" x-cloak class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-8">
            <div class="border-b border-slate-100 pb-4">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <h2 class="text-base font-black text-slate-900">Reset Account Password</h2>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Ensure your account stays secure by choosing a robust password with at least 8 characters.
                </p>
            </div>

            <form action="{{ route('employer.password.reset') }}" method="POST" class="max-w-xl space-y-6">
                @csrf

                <!-- Current Password -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Current Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input :type="showCurrent ? 'text' : 'password'" 
                               name="current_password" 
                               required 
                               placeholder="Enter your current account password"
                               class="w-full rounded-xl border border-slate-200 pl-4 pr-10 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        <button type="button" @click="showCurrent = !showCurrent" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600">
                            <svg x-show="!showCurrent" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="showCurrent" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="text-[11px] text-rose-600 font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        New Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input :type="showNew ? 'text' : 'password'" 
                               name="password" 
                               required 
                               placeholder="At least 8 characters"
                               class="w-full rounded-xl border border-slate-200 pl-4 pr-10 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        <button type="button" @click="showNew = !showNew" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600">
                            <svg x-show="!showNew" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="showNew" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-[11px] text-rose-600 font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm New Password -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Confirm New Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input :type="showConfirm ? 'text' : 'password'" 
                               name="password_confirmation" 
                               required 
                               placeholder="Re-enter your new password"
                               class="w-full rounded-xl border border-slate-200 pl-4 pr-10 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        <button type="button" @click="showConfirm = !showConfirm" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600">
                            <svg x-show="!showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="showConfirm" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Password Advice Box -->
                <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4 text-xs text-slate-600 space-y-1.5">
                    <span class="font-bold text-slate-800 block">Security Tips:</span>
                    <ul class="list-disc list-inside space-y-0.5 text-[11px] text-slate-500">
                        <li>Minimum of 8 characters in length.</li>
                        <li>Include numbers, symbols, and mixed case letters for maximum protection.</li>
                        <li>Do not share your corporate employer credentials.</li>
                    </ul>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 hover:bg-emerald-600 px-8 py-3 text-xs font-black text-white shadow-lg transition-all hover:scale-105">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Update & Reset Password
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

