@extends('layouts.admin')

@section('title', 'Admin Profile & Security - TrabaGo DMDP')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8" x-data="{
    activeTab: 'profile',
    showCurrentPass: false,
    showNewPass: false,
    showConfirmPass: false
}">
    <div class="mx-auto max-w-4xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="h-16 w-16 rounded-2xl bg-gradient-to-tr from-emerald-600 to-emerald-400 flex items-center justify-center text-white text-2xl font-black shrink-0 ring-4 ring-emerald-400/30 shadow-lg">
                    {{ strtoupper(substr($user->email ?? 'A', 0, 1)) }}
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Super Administrator
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight">{{ $profile->full_name ?? 'System Administrator' }}</h1>
                    <p class="text-xs text-slate-300">{{ $user->email }} &bull; {{ $profile->position ?? 'DMDP Central Administrator' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" 
                   class="inline-flex items-center gap-2 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/20 px-4 py-2.5 text-xs font-bold text-white transition-all">
                    <span>&larr;</span> Dashboard
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 flex items-center gap-3 text-emerald-800 text-xs font-bold shadow-sm">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="rounded-2xl bg-rose-50 border border-rose-200 p-4 space-y-1 text-rose-800 text-xs font-bold shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Please correct the errors below:</span>
                </div>
                @if(session('error'))
                    <p class="font-normal pl-6">{{ session('error') }}</p>
                @endif
                @foreach($errors->all() as $error)
                    <p class="font-normal pl-6">&bull; {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Tab Controls -->
        <div class="flex items-center gap-3 p-1.5 rounded-2xl bg-white border border-slate-200 shadow-sm max-w-fit">
            <button @click="activeTab = 'profile'"
                    :class="activeTab === 'profile' ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-300 shadow-sm font-black' : 'text-slate-600 hover:text-slate-900 font-bold hover:bg-slate-50'"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs transition-all">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Administrator Profile</span>
            </button>

            <button @click="activeTab = 'security'"
                    :class="activeTab === 'security' ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-300 shadow-sm font-black' : 'text-slate-600 hover:text-slate-900 font-bold hover:bg-slate-50'"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs transition-all">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Reset Password & Security</span>
            </button>
        </div>

        <!-- 1. Administrator Profile Card -->
        <div x-show="activeTab === 'profile'" class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-10 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-black text-slate-900">Administrator Profile Details</h2>
                    <p class="text-xs text-slate-500">Update your official contact credentials and administrative department assignment.</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">Active Staff</span>
            </div>

            <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Official Account Email</label>
                        <input type="email" value="{{ $user->email }}" disabled 
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 text-xs cursor-not-allowed font-medium">
                        <p class="text-[11px] text-slate-400">Primary login credential (managed by DMDP Central System).</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Full Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name', $profile->full_name ?? '') }}" required 
                               placeholder="e.g. Juan dela Cruz"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none @error('full_name') border-rose-400 @enderror">
                        @error('full_name')
                            <p class="text-[11px] font-bold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Contact / Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $profile->phone ?? '') }}" 
                               placeholder="e.g. +63 912 345 6789"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Official Position / Title</label>
                        <input type="text" name="position" value="{{ old('position', $profile->position ?? 'DMDP Central Administrator') }}" 
                               placeholder="e.g. Supervising Labor Employment Officer"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Department / Division</label>
                        <input type="text" name="department" value="{{ old('department', $profile->department ?? 'City Manpower Development Division') }}" 
                               placeholder="e.g. City Manpower Development Division"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                    </div>

                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Office Location</label>
                        <input type="text" name="office" value="{{ old('office', $profile->office ?? 'Cebu City Hall Annex, Ramos DMDP Complex') }}" 
                               placeholder="e.g. 2F Cebu City Hall Annex"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center gap-1.5 px-7 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-black text-xs shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Update Profile</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- 2. Security & Reset Password Card -->
        <div x-show="activeTab === 'security'" class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-10 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-lg font-black text-slate-900">Reset Administrator Password</h2>
                <p class="text-xs text-slate-500">Update your security passkey. Password must be at least 8 characters long and securely stored.</p>
            </div>

            <form action="{{ route('admin.password.reset') }}" method="POST" class="space-y-6">
                @csrf

                <div class="max-w-xl space-y-5">
                    <!-- Current Password -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Current Password <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input :type="showCurrentPass ? 'text' : 'password'" 
                                   name="current_password" required 
                                   placeholder="Enter current password"
                                   class="w-full px-4 py-2.5 pr-10 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none @error('current_password') border-rose-400 @enderror">
                            <button type="button" @click="showCurrentPass = !showCurrentPass" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs focus:outline-none">
                                <span x-show="!showCurrentPass">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </span>
                                <span x-show="showCurrentPass" x-cloak>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                </span>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="text-[11px] font-bold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">New Password <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input :type="showNewPass ? 'text' : 'password'" 
                                   name="password" required 
                                   placeholder="Minimum 8 characters"
                                   class="w-full px-4 py-2.5 pr-10 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none @error('password') border-rose-400 @enderror">
                            <button type="button" @click="showNewPass = !showNewPass" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs focus:outline-none">
                                <span x-show="!showNewPass">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </span>
                                <span x-show="showNewPass" x-cloak>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                </span>
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-400">Must include minimum 8 characters with a mix of numbers and letters.</p>
                        @error('password')
                            <p class="text-[11px] font-bold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Confirm New Password <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input :type="showConfirmPass ? 'text' : 'password'" 
                                   name="password_confirmation" required 
                                   placeholder="Re-enter new password"
                                   class="w-full px-4 py-2.5 pr-10 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                            <button type="button" @click="showConfirmPass = !showConfirmPass" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs focus:outline-none">
                                <span x-show="!showConfirmPass">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </span>
                                <span x-show="showConfirmPass" x-cloak>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="reset" 
                            class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                        Clear
                    </button>
                    <button type="submit" 
                            class="inline-flex items-center gap-1.5 px-7 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-black text-xs shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Reset Password</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
