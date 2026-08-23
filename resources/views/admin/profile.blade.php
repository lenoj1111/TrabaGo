@extends('layouts.admin')

@section('title', 'Admin Profile - TrabaGo DMDP')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-3xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center gap-6">
            <div class="h-16 w-16 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white text-2xl font-black shrink-0 ring-2 ring-emerald-400/40 shadow-lg">
                {{ strtoupper(substr($user->email ?? 'A', 0, 1)) }}
            </div>
            <div class="space-y-1">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Super Administrator
                </span>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">{{ $profile->full_name ?? 'System Administrator' }}</h1>
                <p class="text-xs text-slate-300">{{ $user->email }} &bull; {{ $profile->position ?? 'DMDP Central Administrator' }}</p>
            </div>
        </div>

        <!-- Profile Form Card -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-10 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-lg font-black text-slate-900">Administrator Profile Details</h2>
                <p class="text-xs text-slate-500">Update your official contact credentials and administrative department assignment.</p>
            </div>

            <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Official Account Email</label>
                        <input type="email" value="{{ $user->email }}" disabled 
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 text-xs cursor-not-allowed">
                        <p class="text-[11px] text-slate-400">Primary login email address.</p>
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
                        <input type="text" name="department" value="{{ old('department', $profile->department ?? 'DMDP Central Administration') }}" 
                               placeholder="e.g. City Manpower Development Division"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                    </div>

                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Office Location</label>
                        <input type="text" name="office" value="{{ old('office', $profile->office ?? 'Cebu City Hall Annex, Ramos DMDP Complex') }}" 
                               placeholder="e.g. 2F Cebu City Hall"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-7 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-black text-xs shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                        ✓ Save Profile
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
