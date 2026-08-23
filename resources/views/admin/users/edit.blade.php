@extends('layouts.admin')

@section('title', 'Edit User Account')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-4xl space-y-8">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 inline-flex items-center gap-1">
                    &larr; Back to Users Directory
                </a>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Edit User #{{ $user->user_id }}</h1>
                <p class="text-xs text-slate-500">Update system role, access status, approval state, and profile information.</p>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-bold border {{ $user->status === 'active' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-rose-50 text-rose-800 border-rose-200' }}">
                    Status: {{ ucfirst($user->status) }}
                </span>
            </div>
        </div>

        <!-- Form Card -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-10 shadow-sm">
            <form method="POST" action="{{ route('admin.users.update', ['id' => $user->user_id]) }}" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Account Credentials & Roles -->
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <span>🔐</span> Account Privileges & Access
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Account Email</label>
                            <input type="email" value="{{ $user->email }}" disabled 
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 text-xs cursor-not-allowed">
                            <p class="text-[11px] text-slate-400">Primary email cannot be modified directly.</p>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Assigned Role <span class="text-rose-500">*</span></label>
                            <select name="role" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none @error('role') border-rose-400 @enderror">
                                @foreach($roles ?? ['admin', 'supervisor', 'pesd_supervisor', 'jpo', 'trainer', 'lmo', 'employer', 'jobseeker'] as $role)
                                    <option value="{{ $role }}" {{ old('role', $user->role) == $role ? 'selected' : '' }}>
                                        {{ strtoupper(str_replace('_', ' ', $role)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <p class="text-[11px] font-bold text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Account Status <span class="text-rose-500">*</span></label>
                            <select name="status" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none @error('status') border-rose-400 @enderror">
                                @foreach($statuses ?? ['active', 'inactive'] as $status)
                                    <option value="{{ $status }}" {{ old('status', $user->status) == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="text-[11px] font-bold text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Approval State</label>
                            <select name="is_approved" 
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                                <option value="1" {{ old('is_approved', $user->is_approved) == 1 ? 'selected' : '' }}>Approved & Verified</option>
                                <option value="0" {{ old('is_approved', $user->is_approved) == 0 ? 'selected' : '' }}>Pending / Unapproved</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Profile Information Section -->
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <span>👤</span> Profile Information
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Full Name</label>
                            <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" 
                                   placeholder="Full Name"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Position</label>
                            <input type="text" name="position" value="{{ old('position', $user->position) }}" 
                                   placeholder="Official Position"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Department</label>
                            <input type="text" name="department" value="{{ old('department', $user->department) }}" 
                                   placeholder="Department / Division"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Office</label>
                            <input type="text" name="office" value="{{ old('office', $user->office) }}" 
                                   placeholder="Office Location"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" 
                                   placeholder="Contact Number"
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
                        ✓ Save Changes
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>
@endsection
