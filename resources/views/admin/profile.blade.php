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
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-green-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-green-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="h-16 w-16 rounded-2xl bg-gradient-to-tr from-green-600 to-green-400 flex items-center justify-center text-white text-2xl font-black shrink-0 ring-4 ring-green-400/30 shadow-lg">
                    {{ strtoupper(substr($user->email ?? 'A', 0, 1)) }}
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-green-400/20 px-3 py-1 text-xs font-bold text-green-300 border border-green-400/30">
                            <span class="h-2 w-2 rounded-full bg-green-400 animate-pulse"></span>
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
            <div class="rounded-2xl bg-green-50 border border-green-200 p-4 flex items-center gap-3 text-green-800 text-xs font-bold shadow-sm">
                <span class="text-base">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="rounded-2xl bg-rose-50 border border-rose-200 p-4 space-y-1 text-rose-800 text-xs font-bold shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="text-base">⚠️</span>
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
                    :class="activeTab === 'profile' ? 'bg-green-50 text-green-800 ring-1 ring-green-300 shadow-sm font-black' : 'text-slate-600 hover:text-slate-900 font-bold hover:bg-slate-50'"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs transition-all">
                <span>👤</span> Administrator Profile
            </button>

            <button @click="activeTab = 'security'"
                    :class="activeTab === 'security' ? 'bg-green-50 text-green-800 ring-1 ring-green-300 shadow-sm font-black' : 'text-slate-600 hover:text-slate-900 font-bold hover:bg-slate-50'"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs transition-all">
                <span>🔐</span> Reset Password & Security
            </button>
        </div>

        <!-- 1. Administrator Profile Card -->
        <div x-show="activeTab === 'profile'" class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-10 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-black text-slate-900">Administrator Profile Details</h2>
                    <p class="text-xs text-slate-500">Update your official contact credentials and administrative department assignment.</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-green-100 text-green-800 text-[10px] font-bold">Active Staff</span>
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
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none @error('full_name') border-rose-400 @enderror">
                        @error('full_name')
                            <p class="text-[11px] font-bold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Contact / Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $profile->phone ?? '') }}" 
                               placeholder="e.g. +63 912 345 6789"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Official Position / Title</label>
                        <input type="text" name="position" value="{{ old('position', $profile->position ?? 'DMDP Central Administrator') }}" 
                               placeholder="e.g. Supervising Labor Employment Officer"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Department / Division</label>
                        <input type="text" name="department" value="{{ old('department', $profile->department ?? 'City Manpower Development Division') }}" 
                               placeholder="e.g. City Manpower Development Division"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                    </div>

                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Office Location</label>
                        <input type="text" name="office" value="{{ old('office', $profile->office ?? 'Cebu City Hall Annex, Ramos DMDP Complex') }}" 
                               placeholder="e.g. 2F Cebu City Hall Annex"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-7 py-2.5 rounded-xl bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white font-black text-xs shadow-lg shadow-green-600/30 transition-all hover:scale-105">
                        ✓ Update Profile
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
                                   class="w-full px-4 py-2.5 pr-10 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none @error('current_password') border-rose-400 @enderror">
                            <button type="button" @click="showCurrentPass = !showCurrentPass" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs focus:outline-none">
                                <span x-show="!showCurrentPass">👁️</span>
                                <span x-show="showCurrentPass" x-cloak>🙈</span>
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
                                   class="w-full px-4 py-2.5 pr-10 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none @error('password') border-rose-400 @enderror">
                            <button type="button" @click="showNewPass = !showNewPass" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs focus:outline-none">
                                <span x-show="!showNewPass">👁️</span>
                                <span x-show="showNewPass" x-cloak>🙈</span>
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
                                   class="w-full px-4 py-2.5 pr-10 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                            <button type="button" @click="showConfirmPass = !showConfirmPass" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs focus:outline-none">
                                <span x-show="!showConfirmPass">👁️</span>
                                <span x-show="showConfirmPass" x-cloak>🙈</span>
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
                            class="px-7 py-2.5 rounded-xl bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white font-black text-xs shadow-lg shadow-green-600/30 transition-all hover:scale-105">
                        🔐 Reset Password
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
