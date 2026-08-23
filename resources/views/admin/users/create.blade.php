@extends('layouts.admin')

@section('title', 'Add Employee Account')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-4xl space-y-8">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 inline-flex items-center gap-1">
                    &larr; Back to Users Directory
                </a>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Provision Employee Account</h1>
                <p class="text-xs text-slate-500">Create a new staff account (JPO, Trainer, Supervisor, LMO, or Admin) with system privileges.</p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-10 shadow-sm">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-8">
                @csrf

                <!-- Account Credentials Section -->
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <span>🔐</span> Account Credentials
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Official Email <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" required 
                                   placeholder="staff@trabago.gov.ph"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none @error('email') border-rose-400 @enderror">
                            @error('email')
                                <p class="text-[11px] font-bold text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">System Role <span class="text-rose-500">*</span></label>
                            <select name="role" id="roleSelect" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none @error('role') border-rose-400 @enderror">
                                <option value="">Select Account Role...</option>
                                @foreach($roles ?? ['admin', 'jpo', 'trainer', 'lmo'] as $role)
                                    <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>
                                        {{ strtoupper(str_replace('_', ' ', $role)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <p class="text-[11px] font-bold text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Password <span class="text-rose-500">*</span></label>
                            <input type="password" name="password" required 
                                   placeholder="Minimum 8 characters"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none @error('password') border-rose-400 @enderror">
                            @error('password')
                                <p class="text-[11px] font-bold text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Confirm Password <span class="text-rose-500">*</span></label>
                            <input type="password" name="password_confirmation" required 
                                   placeholder="Re-enter password"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Staff Profile Details Section -->
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <span>👤</span> Employee Profile Information
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Full Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="full_name" value="{{ old('full_name') }}" required 
                                   placeholder="e.g. Maria Santos"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none @error('full_name') border-rose-400 @enderror">
                            @error('full_name')
                                <p class="text-[11px] font-bold text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Official Position <span class="text-rose-500">*</span></label>
                            <input type="text" name="position" value="{{ old('position') }}" required 
                                   placeholder="e.g. Labor Employment Officer II"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none @error('position') border-rose-400 @enderror">
                            @error('position')
                                <p class="text-[11px] font-bold text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Department</label>
                            <input type="text" name="department" value="{{ old('department', 'DMDP Cebu City') }}" 
                                   placeholder="e.g. Skills Training Division"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Office Location</label>
                            <input type="text" name="office" value="{{ old('office') }}" 
                                   placeholder="e.g. Ramos DMDP Building"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" 
                                   placeholder="e.g. +63 912 345 6789"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Specialization / Expertise</label>
                            <input type="text" name="specialization" value="{{ old('specialization') }}" 
                                   placeholder="e.g. Technical Skills, IT, Hospitality"
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
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-black text-xs shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                        ✓ Provision Account
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>
@endsection
