@extends('layouts.jpo')

@section('title', 'Officer Profile & Security - JPO Portal')

@section('content')
<div x-data="{ 
    activeTab: 'profile',
    showCurrentPassword: false,
    showNewPassword: false,
    showConfirmPassword: false
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-4xl space-y-8">
        
        <!-- Header Profile Card -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div class="h-20 w-20 rounded-3xl bg-gradient-to-tr from-emerald-600 to-emerald-400 flex items-center justify-center text-white text-3xl font-black shadow-lg shadow-emerald-600/30 shrink-0">
                        {{ strtoupper(substr($user->email ?? 'J', 0, 1)) }}
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <h1 class="text-2xl sm:text-3xl font-black tracking-tight">{{ $profile->full_name ?? 'Job Placement Officer' }}</h1>
                            <span class="rounded-full bg-emerald-500/20 border border-emerald-400/30 px-2.5 py-0.5 text-[10px] font-bold text-emerald-300 uppercase">
                                {{ strtoupper($user->role) }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-300">{{ $user->email }} &bull; Staff ID #{{ $user->user_id }}</p>
                        <p class="text-xs text-emerald-300 font-semibold">{{ $profile->office ?? 'Cebu City DMDP - Job Placement Division' }}</p>
                    </div>
                </div>

                <!-- Quick Stats Badges -->
                <div class="flex sm:flex-col gap-2 shrink-0">
                    <div class="rounded-2xl bg-white/10 px-4 py-2 text-center border border-white/10">
                        <span class="text-[10px] text-slate-300 uppercase font-bold">Candidates Endorsed</span>
                        <div class="text-lg font-black text-emerald-400">{{ $totalReferrals ?? 0 }}</div>
                    </div>
                    <div class="rounded-2xl bg-white/10 px-4 py-2 text-center border border-white/10">
                        <span class="text-[10px] text-slate-300 uppercase font-bold">Accreditations Checked</span>
                        <div class="text-lg font-black text-white">{{ $totalAccreditationsReviewed ?? 0 }}</div>
                    </div>
                    <div class="rounded-2xl bg-white/10 px-4 py-2 text-center border border-white/10">
                        <span class="text-[10px] text-slate-300 uppercase font-bold">Placement Audits</span>
                        <div class="text-lg font-black text-emerald-300">{{ $totalPlacementReportsAudited ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <!-- Tab Buttons -->
            <div class="mt-8 pt-6 border-t border-emerald-500/20 flex gap-3">
                <button @click="activeTab = 'profile'" type="button" 
                        :class="activeTab === 'profile' ? 'bg-emerald-500 text-slate-950 shadow-md font-extrabold' : 'text-slate-300 hover:text-white hover:bg-white/10 font-bold'" 
                        class="px-4 py-2 rounded-xl text-xs transition-all cursor-pointer inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Officer Details</span>
                </button>
                <button @click="activeTab = 'security'" type="button" 
                        :class="activeTab === 'security' ? 'bg-emerald-500 text-slate-950 shadow-md font-extrabold' : 'text-slate-300 hover:text-white hover:bg-white/10 font-bold'" 
                        class="px-4 py-2 rounded-xl text-xs transition-all cursor-pointer inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Reset Password & Security</span>
                </button>
            </div>
        </div>

        <!-- Tab 1: Profile Information -->
        <div x-show="activeTab === 'profile'" x-transition class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-lg font-black text-slate-900">Officer Profile Information</h2>
                <p class="text-xs text-slate-500">Update your officer contact details, official position, and department designation.</p>
            </div>

            <form action="{{ route('jpo.profile.update') }}" method="POST" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Full Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name', $profile->full_name ?? '') }}" required 
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-900 focus:border-emerald-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Official Staff Email</label>
                        <input type="email" value="{{ $user->email }}" disabled 
                               class="w-full rounded-xl border border-slate-100 bg-slate-50 px-4 py-2.5 text-xs font-medium text-slate-400 cursor-not-allowed">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Official Contact Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $profile->phone ?? '') }}" placeholder="0917-xxx-xxxx" 
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-900 focus:border-emerald-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Designation / Title</label>
                        <input type="text" name="position" value="{{ old('position', $profile->position ?? 'Job Placement Officer (JPO)') }}" 
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-900 focus:border-emerald-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Department / Division</label>
                    <input type="text" name="office" value="{{ old('office', $profile->office ?? 'Cebu City DMDP - Job Placement Division') }}" 
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-900 focus:border-emerald-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Officer Bio / Operational Scope</label>
                    <textarea name="bio" rows="3" placeholder="Specify assigned cluster, sector specialties, or departmental responsibilities..." 
                              class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-900 focus:border-emerald-500 outline-none">{{ old('bio', $profile->bio ?? '') }}</textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="rounded-xl bg-emerald-600 hover:bg-emerald-500 px-7 py-2.5 text-xs font-extrabold text-white shadow-md shadow-emerald-600/30 transition-all cursor-pointer">
                        Save Officer Profile
                    </button>
                </div>
            </form>
        </div>

        <!-- Tab 2: Security & Password Reset -->
        <div x-show="activeTab === 'security'" x-transition class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-lg font-black text-slate-900">Reset Account Password</h2>
                <p class="text-xs text-slate-500">Ensure your placement officer staff credentials are secured with a strong password of at least 8 characters.</p>
            </div>

            <form action="{{ route('jpo.password.update') }}" method="POST" class="space-y-5 max-w-xl">
                @csrf

                <!-- Current Password -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Current Password <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input :type="showCurrentPassword ? 'text' : 'password'" name="current_password" required placeholder="Enter current password" 
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 pr-10 text-xs font-medium text-slate-900 focus:border-emerald-500 outline-none">
                        <button type="button" @click="showCurrentPassword = !showCurrentPassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600">
                            <span x-show="!showCurrentPassword">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </span>
                            <span x-show="showCurrentPassword" x-cloak>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            </span>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="text-[11px] text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">New Password <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input :type="showNewPassword ? 'text' : 'password'" name="password" required minlength="8" placeholder="Minimum 8 characters" 
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 pr-10 text-xs font-medium text-slate-900 focus:border-emerald-500 outline-none">
                        <button type="button" @click="showNewPassword = !showNewPassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600">
                            <span x-show="!showNewPassword">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </span>
                            <span x-show="showNewPassword" x-cloak>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            </span>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-[11px] text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Confirm New Password <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" required minlength="8" placeholder="Re-enter new password" 
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 pr-10 text-xs font-medium text-slate-900 focus:border-emerald-500 outline-none">
                        <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600">
                            <span x-show="!showConfirmPassword">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </span>
                            <span x-show="showConfirmPassword" x-cloak>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            </span>
                        </button>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="rounded-xl bg-emerald-600 hover:bg-emerald-500 px-7 py-2.5 text-xs font-extrabold text-white shadow-md shadow-emerald-600/30 transition-all cursor-pointer">
                        Reset Password &rarr;
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
