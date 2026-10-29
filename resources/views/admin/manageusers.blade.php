@extends('layouts.admin')

@section('title', 'Employee Accounts & User Management')

@section('content')
<div x-data="manageUsersComponent()" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="w-full max-w-[1680px] mx-auto space-y-8">

        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    DMDP User Directory & Identity Management
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Employee Accounts & Users</h1>
                <p class="text-sm text-slate-300">
                    Manage system access, provision staff accounts (JPO, Trainer, and Admin), and audit registered portal users.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('admin.users.create') }}" 
                   class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 px-5 py-3 text-xs font-black text-white shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                    <span>+ Provision Staff User</span>
                </a>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Users</span>
                    <p class="text-3xl font-black text-slate-900">{{ $counts['total'] ?? 0 }}</p>
                    <span class="text-[11px] text-slate-500">Across all system roles</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-slate-100 text-slate-800 flex items-center justify-center font-black">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Active Accounts</span>
                    <p class="text-3xl font-black text-emerald-700">{{ $counts['active'] ?? 0 }}</p>
                    <span class="text-[11px] text-emerald-800">Operational users</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center justify-center font-black">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Deactivated</span>
                    <p class="text-3xl font-black text-rose-700">{{ $counts['inactive'] ?? 0 }}</p>
                    <span class="text-[11px] text-rose-800">Access disabled</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-rose-50 text-rose-800 border border-rose-200 flex items-center justify-center font-black">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pending Approval</span>
                    <p class="text-3xl font-black text-amber-600">{{ $counts['pending'] ?? 0 }}</p>
                    <span class="text-[11px] text-amber-700">Awaiting authorization</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-800 border border-amber-200 flex items-center justify-center font-black">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
                <div class="lg:col-span-5 space-y-1">
                    <label class="text-xs font-bold text-slate-700">Search User</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search by email, name, or company..."
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                </div>

                <div class="lg:col-span-3 space-y-1">
                    <label class="text-xs font-bold text-slate-700">Role</label>
                    <select name="role" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        <option value="">All Roles</option>
                        @foreach($roles ?? ['admin', 'jpo', 'trainer', 'employer', 'jobseeker'] as $role)
                            <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $role)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2 space-y-1">
                    <label class="text-xs font-bold text-slate-700">Status</label>
                    <select name="status" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        <option value="">All Statuses</option>
                        @foreach($statuses ?? ['active', 'inactive'] as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2 flex items-center gap-2">
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="p-6 sm:p-8 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-900">Registered Users Directory</h3>
                    <p class="text-xs text-slate-500">Full list of administrator, staff, employer, and jobseeker accounts</p>
                </div>
                <span class="text-xs font-bold text-slate-400">{{ $users->total() }} Total</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-100 text-[11px] uppercase font-bold text-slate-500 tracking-wider">
                        <tr>
                            <th class="py-4 px-6">User / Identity</th>
                            <th class="py-4 px-6">Assigned Role</th>
                            <th class="py-4 px-6 text-center">Account Status</th>
                            <th class="py-4 px-6 text-center">Approved</th>
                            <th class="py-4 px-6">Created Date</th>
                            <th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($users as $user)
                            @php
                                $displayName = $user->full_name ?: (
                                    trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: (
                                        $user->company_name ?: 'User #' . $user->user_id
                                    )
                                );

                                $roleBadgeClasses = match($user->role) {
                                    'admin' => 'bg-rose-50 text-rose-800 border-rose-200',
                                    'supervisor', 'pesd_supervisor' => 'bg-purple-50 text-purple-800 border-purple-200',
                                    'jpo' => 'bg-blue-50 text-blue-800 border-blue-200',
                                    'trainer' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                    'lmo' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
                                    'employer' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                    'jobseeker' => 'bg-slate-100 text-slate-800 border-slate-200',
                                    default => 'bg-slate-100 text-slate-800 border-slate-200',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-xl bg-slate-900 text-white font-bold text-xs flex items-center justify-center ring-2 ring-emerald-500/30 shrink-0">
                                            {{ strtoupper(substr($user->email ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-black text-slate-900 text-sm">{{ $displayName }}</div>
                                            <div class="text-[11px] text-slate-500 font-medium">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-black border {{ $roleBadgeClasses }}">
                                        {{ strtoupper(str_replace('_', ' ', $user->role)) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-bold border {{ $user->status === 'active' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-rose-50 text-rose-800 border-rose-200' }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-bold border {{ $user->is_approved ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-amber-50 text-amber-800 border-amber-200' }}">
                                            @if($user->is_approved)
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                Approved
                                            @else
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Pending
                                            @endif
                                        </span>
                                        @if(!$user->is_approved)
                                            @if($user->role === 'employer')
                                                <a href="{{ route('admin.employers') }}" class="text-[10px] font-bold text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-0.5">
                                                    <span>Review in Employers</span>
                                                    <span>&rarr;</span>
                                                </a>
                                            @elseif($user->role === 'jobseeker')
                                                <a href="{{ route('admin.jobseekers.index') }}" class="text-[10px] font-bold text-emerald-600 hover:text-emerald-800 hover:underline inline-flex items-center gap-0.5">
                                                    <span>Review in Jobseekers</span>
                                                    <span>&rarr;</span>
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-slate-500 text-[11px]">
                                    {{ $user->created_at ? date('M d, Y', strtotime($user->created_at)) : 'N/A' }}
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="inline-flex items-center justify-end gap-1.5 flex-wrap">
                                        @php
                                            $userData = [
                                                'user_id' => $user->user_id,
                                                'email' => $user->email,
                                                'role' => $user->role,
                                                'status' => $user->status,
                                                'is_approved' => (bool)$user->is_approved,
                                                'full_name' => $displayName,
                                                'position' => $user->position ?? '',
                                                'department' => $user->department ?? '',
                                                'company_name' => $user->company_name ?? '',
                                                'trade_name' => $user->trade_name ?? '',
                                                'contact_person' => $user->contact_person ?? '',
                                                'phone' => $user->employer_contact ?: ($user->jobseeker_mobile ?: ''),
                                                'business_nature' => $user->business_nature ?? '',
                                                'location' => trim(($user->current_barangay ?? '') . ' ' . ($user->current_city ?? '')),
                                                'employment_status' => $user->employment_status ?? '',
                                                'created_at' => $user->created_at ? date('F d, Y h:i A', strtotime($user->created_at)) : 'N/A',
                                                'edit_url' => route('admin.users.edit', $user->user_id)
                                            ];
                                        @endphp

                                        {{-- 1. Update Email or Password (HR Turnover / Resignation Recovery) --}}
                                        @if($user->role === 'employer' && !empty($user->employer_id))
                                            <button type="button" 
                                                    @click="openCredentialsModal({{ json_encode([
                                                        'employer_id' => $user->employer_id,
                                                        'user_id' => $user->user_id,
                                                        'company_name' => $user->company_name ?: $displayName,
                                                        'email' => $user->email,
                                                        'secondary_email' => $user->employer_secondary_email ?? '',
                                                        'contact_person' => $user->contact_person ?? '',
                                                        'contact_phone' => $user->employer_contact ?? '',
                                                    ]) }})"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 font-bold text-xs transition-colors" 
                                                    title="Update Email or Password (e.g. Assigned HR resigned without leaving credentials)">
                                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                                <span>Update Email/Pass</span>
                                            </button>
                                        @endif

                                        {{-- 2. Deactivate / Activate Account Toggle --}}
                                        <form action="{{ route('admin.users.toggle-status', $user->user_id) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Are you sure you want to {{ $user->status === 'active' ? 'DEACTIVATE' : 'ACTIVATE' }} account #{{ $user->user_id }} ({{ addslashes($displayName) }})?');">
                                            @csrf
                                            @if($user->status === 'active')
                                                <button type="submit" 
                                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs transition-colors" 
                                                        title="Deactivate Account">
                                                    <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                    <span>Deac</span>
                                                </button>
                                            @else
                                                <button type="submit" 
                                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 font-bold text-xs transition-colors" 
                                                        title="Activate Account">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>Activate</span>
                                                </button>
                                            @endif
                                        </form>

                                        {{-- 3. Edit Account --}}
                                        <a href="{{ route('admin.users.edit', $user->user_id) }}" 
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 font-bold text-xs transition-colors" title="Edit User">
                                            <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </a>

                                        {{-- 4. View Details --}}
                                        <button type="button" @click='openView(@json($userData))'
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition-colors" title="View Account Details">
                                            <svg class="w-3.5 h-3.5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>View</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <div class="flex justify-center mb-2">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </div>
                                    <p class="font-bold text-slate-700">No users match your query</p>
                                    <p class="text-xs mt-0.5">Try clearing filters or search parameters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        <!-- View User Details Modal -->
        <div x-show="viewModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="viewModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6" x-show="selectedUser">
                <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Account Overview</span>
                        <h3 class="text-2xl font-black text-slate-900" x-text="selectedUser.full_name"></h3>
                        <p class="text-xs text-slate-400">User ID #<span x-text="selectedUser.user_id"></span> &bull; <span x-text="selectedUser.email"></span></p>
                    </div>
                    <button @click="viewModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
                </div>

                <!-- Badges -->
                <div class="grid grid-cols-3 gap-3 text-xs bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <div>
                        <span class="text-slate-400 text-[10px] font-bold uppercase block">Role</span>
                        <span class="font-bold text-slate-900 uppercase" x-text="selectedUser.role"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] font-bold uppercase block">Status</span>
                        <span class="font-bold capitalize" :class="selectedUser.status === 'active' ? 'text-emerald-700' : 'text-rose-600'" x-text="selectedUser.status"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] font-bold uppercase block">Approval</span>
                        <span class="font-bold" :class="selectedUser.is_approved ? 'text-emerald-700' : 'text-amber-600'" x-text="selectedUser.is_approved ? 'Approved' : 'Pending'"></span>
                    </div>
                </div>

                <!-- Detail Fields -->
                <div class="space-y-3 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-slate-500 font-semibold">Registered Email:</span>
                            <span class="font-bold text-slate-900" x-text="selectedUser.email"></span>
                        </div>
                        <template x-if="selectedUser.phone">
                            <div class="flex justify-between">
                                <span class="text-slate-500 font-semibold">Contact Phone:</span>
                                <span class="font-bold text-slate-900" x-text="selectedUser.phone"></span>
                            </div>
                        </template>
                        <div class="flex justify-between">
                            <span class="text-slate-500 font-semibold">Date Registered:</span>
                            <span class="font-bold text-slate-900" x-text="selectedUser.created_at"></span>
                        </div>
                    </div>

                    <!-- Employer Info -->
                    <template x-if="selectedUser.role === 'employer' && selectedUser.company_name">
                        <div class="p-4 rounded-2xl bg-emerald-50/50 border border-emerald-200 space-y-2">
                            <span class="font-black text-emerald-950 uppercase tracking-wider text-[11px] block">Corporate Profile</span>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Company Name:</span>
                                <span class="font-bold text-emerald-900" x-text="selectedUser.company_name"></span>
                            </div>
                            <template x-if="selectedUser.trade_name">
                                <div class="flex justify-between">
                                    <span class="text-slate-600">Trade / Brand Name:</span>
                                    <span class="font-bold text-emerald-900" x-text="selectedUser.trade_name"></span>
                                </div>
                            </template>
                            <template x-if="selectedUser.contact_person">
                                <div class="flex justify-between">
                                    <span class="text-slate-600">Contact Person:</span>
                                    <span class="font-bold text-emerald-900" x-text="selectedUser.contact_person"></span>
                                </div>
                            </template>
                            <template x-if="selectedUser.business_nature">
                                <div class="flex justify-between">
                                    <span class="text-slate-600">Business Nature:</span>
                                    <span class="font-bold text-emerald-900" x-text="selectedUser.business_nature"></span>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Staff Info -->
                    <template x-if="selectedUser.position || selectedUser.department">
                        <div class="p-4 rounded-2xl bg-blue-50/50 border border-blue-200 space-y-2">
                            <span class="font-black text-blue-950 uppercase tracking-wider text-[11px] block">Staff Assignment</span>
                            <template x-if="selectedUser.position">
                                <div class="flex justify-between">
                                    <span class="text-slate-600">Position / Title:</span>
                                    <span class="font-bold text-blue-900" x-text="selectedUser.position"></span>
                                </div>
                            </template>
                            <template x-if="selectedUser.department">
                                <div class="flex justify-between">
                                    <span class="text-slate-600">Department / Division:</span>
                                    <span class="font-bold text-blue-900" x-text="selectedUser.department"></span>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Jobseeker Info -->
                    <template x-if="selectedUser.role === 'jobseeker'">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="font-black text-slate-800 uppercase tracking-wider text-[11px] block">Jobseeker Profile</span>
                            <template x-if="selectedUser.location">
                                <div class="flex justify-between">
                                    <span class="text-slate-600">Address / City:</span>
                                    <span class="font-bold text-slate-900" x-text="selectedUser.location"></span>
                                </div>
                            </template>
                            <template x-if="selectedUser.employment_status">
                                <div class="flex justify-between">
                                    <span class="text-slate-600">Employment Status:</span>
                                    <span class="font-bold text-slate-900" x-text="selectedUser.employment_status"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <a :href="selectedUser.edit_url" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-emerald-600 transition-colors">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        <span>Edit Account</span>
                    </a>
                    <button type="button" @click="viewModal = false" class="px-5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Close
                    </button>
                </div>
            </div>
        </div>

        {{-- Reusable Employer Credentials Modal --}}
        @include('partials.employer-credentials-modal')

    </div>
</div>

<script>
    function manageUsersComponent() {
        return {
            viewModal: false,
            selectedUser: null,
            openView(u) { this.selectedUser = u; this.viewModal = true; },
            ...(window.employerCredentialsHandler ? window.employerCredentialsHandler('{{ csrf_token() }}') : {})
        };
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-action="toggle-status"]').forEach(function (button) {
            button.addEventListener('click', function () {
                const userId = this.dataset.userId;

                Swal.fire({
                    title: 'Toggle User Status?',
                    text: 'Are you sure you want to change this user\'s access status?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, change status'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/admin/users/${userId}/toggle-status`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Success!', data.success, 'success').then(() => location.reload());
                            } 
                        })
                    }
                });
            });
        });

    });
</script>
@endsection