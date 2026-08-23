@extends('layouts.admin')

@section('title', 'Employee Accounts & User Management')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">

        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    DMDP User Directory & Identity Management
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Employee Accounts & Users</h1>
                <p class="text-sm text-slate-300">
                    Manage system access, provision staff accounts (JPO, Trainer, Supervisor, LMO), and audit registered portal users.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('admin.users.create') }}" 
                   class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 px-5 py-3 text-xs font-black text-white shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
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
                <div class="h-12 w-12 rounded-2xl bg-slate-100 text-slate-800 flex items-center justify-center text-xl font-black">
                    👥
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Active Accounts</span>
                    <p class="text-3xl font-black text-emerald-700">{{ $counts['active'] ?? 0 }}</p>
                    <span class="text-[11px] text-emerald-800">Operational users</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center justify-center text-xl font-black">
                    ✓
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Deactivated</span>
                    <p class="text-3xl font-black text-rose-700">{{ $counts['inactive'] ?? 0 }}</p>
                    <span class="text-[11px] text-rose-800">Access disabled</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-rose-50 text-rose-800 border border-rose-200 flex items-center justify-center text-xl font-black">
                    ✕
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pending Approval</span>
                    <p class="text-3xl font-black text-amber-600">{{ $counts['pending'] ?? 0 }}</p>
                    <span class="text-[11px] text-amber-700">Awaiting authorization</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-800 border border-amber-200 flex items-center justify-center text-xl font-black">
                    ⏳
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
                        @foreach($roles ?? ['admin', 'supervisor', 'jpo', 'trainer', 'lmo', 'employer', 'jobseeker'] as $role)
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
                                    'employer' => 'bg-teal-50 text-teal-800 border-teal-200',
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
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-bold border {{ $user->is_approved ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-amber-50 text-amber-800 border-amber-200' }}">
                                        {{ $user->is_approved ? '✓ Approved' : '⏳ Pending' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-slate-500 text-[11px]">
                                    {{ $user->created_at ? date('M d, Y', strtotime($user->created_at)) : 'N/A' }}
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('admin.users.edit', $user->user_id) }}" 
                                           class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors" title="Edit User">
                                            ✏️ Edit
                                        </a>

                                        @if($user->status === 'active')
                                            <button onclick="toggleStatus({{ $user->user_id }})" 
                                                    class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition-colors" title="Deactivate Account">
                                                Deactivate
                                            </button>
                                        @else
                                            <button onclick="toggleStatus({{ $user->user_id }})" 
                                                    class="px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs border border-emerald-200 transition-colors" title="Activate Account">
                                                Activate
                                            </button>
                                        @endif

                                        @if(!$user->is_approved && $user->role !== 'admin')
                                            <button onclick="approveUser({{ $user->user_id }})" 
                                                    class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-colors shadow-sm" title="Approve Account">
                                                ✓ Approve
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <div class="text-3xl mb-2">👥</div>
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

    </div>
</div>

<script>
function toggleStatus(id) {
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
            fetch(`/admin/users/${id}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Success!', data.success, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.error || 'Something went wrong.', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Network error occurred.', 'error'));
        }
    });
}

function approveUser(id) {
    Swal.fire({
        title: 'Approve User Account?',
        text: 'This will authorize the user account to log in and access system functions.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, approve'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/users/${id}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Approved!', data.success, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.error || 'Something went wrong.', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Network error occurred.', 'error'));
        }
    });
}
</script>
@endsection