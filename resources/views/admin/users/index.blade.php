@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div class="p-4 md:p-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                👥 User Management
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage all users in the system</p>
        </div>
        <a href="{{ route('admin.users.create') }}" 
           class="mt-3 md:mt-0 px-4 py-2 bg-gradient-to-r from-brand-700 to-brand-900 text-white text-sm font-semibold rounded-lg transition-all duration-300 hover:shadow-md hover:scale-105 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add User
        </a>
    </div>

    <!-- Statistics Cards - Compact -->
    <div class="grid grid-cols-3 md:grid-cols-6 gap-3 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3 text-center">
            <p class="text-lg font-bold text-gray-800">{{ $counts['total'] }}</p>
            <p class="text-[10px] text-gray-500 uppercase tracking-wider">Total</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3 text-center">
            <p class="text-lg font-bold text-green-600">{{ $counts['active'] }}</p>
            <p class="text-[10px] text-gray-500 uppercase tracking-wider">Active</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3 text-center">
            <p class="text-lg font-bold text-red-600">{{ $counts['inactive'] }}</p>
            <p class="text-[10px] text-gray-500 uppercase tracking-wider">Inactive</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3 text-center">
            <p class="text-lg font-bold text-yellow-600">{{ $counts['pending'] }}</p>
            <p class="text-[10px] text-gray-500 uppercase tracking-wider">Pending</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3 text-center">
            <p class="text-lg font-bold text-purple-600">{{ $counts['admins'] }}</p>
            <p class="text-[10px] text-gray-500 uppercase tracking-wider">Admins</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3 text-center">
            <p class="text-lg font-bold text-orange-600">{{ $counts['employers'] }}</p>
            <p class="text-[10px] text-gray-500 uppercase tracking-wider">Employers</p>
        </div>
    </div>

    <!-- Filters - Compact -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3 mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[150px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email..." 
                       class="w-full px-3 py-1.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition outline-none">
            </div>
            <div>
                <select name="role" class="px-3 py-1.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition outline-none">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" class="px-3 py-1.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition outline-none">
                    <option value="">All Status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-1.5 text-sm bg-brand-700 text-white font-medium rounded-lg hover:bg-brand-800 transition flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-1.5 text-sm bg-gray-100 text-gray-600 font-medium rounded-lg hover:bg-gray-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table - Compact -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-2.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-wider">User</th>
                        <th class="px-4 py-2.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                        <th class="px-4 py-2.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-wider">Approval</th>
                        <th class="px-4 py-2.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-wider">Registered</th>
                        <th class="px-4 py-2.5 text-right text-[10px] font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-brand-100 flex items-center justify-center text-brand-700 font-bold text-xs">
                                        {{ strtoupper(substr($user->full_name ?? $user->company_name ?? $user->first_name ?? $user->email ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-800 text-sm">
                                            {{ $user->full_name ?? $user->company_name ?? $user->first_name . ' ' . $user->last_name ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full 
                                    @if($user->role == 'admin') bg-purple-100 text-purple-700
                                    @elseif($user->role == 'jpo') bg-blue-100 text-blue-700
                                    @elseif($user->role == 'trainer') bg-green-100 text-green-700
                                    @elseif($user->role == 'lmo') bg-indigo-100 text-indigo-700
                                    @elseif($user->role == 'employer') bg-orange-100 text-orange-700
                                    @elseif($user->role == 'jobseeker') bg-cyan-100 text-cyan-700
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ strtoupper($user->role) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full 
                                    @if($user->status == 'active') bg-green-100 text-green-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($user->is_approved)
                                    <span class="text-green-600 text-xs font-medium flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Approved
                                    </span>
                                @else
                                    <span class="text-yellow-600 text-xs font-medium flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                    @if(!$user->is_approved)
                                        <form action="{{ route('admin.users.approve', $user->user_id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 bg-green-600 text-white text-[10px] font-semibold rounded hover:bg-green-700 transition flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Approve
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <form action="{{ route('admin.users.toggle-status', $user->user_id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2 py-1 text-[10px] font-semibold rounded transition
                                            @if($user->status == 'active') bg-yellow-600 text-white hover:bg-yellow-700
                                            @else bg-green-600 text-white hover:bg-green-700 @endif">
                                            {{ $user->status == 'active' ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>

                                    <a href="{{ route('admin.users.edit', $user->user_id) }}" class="px-2 py-1 bg-blue-600 text-white text-[10px] font-semibold rounded hover:bg-blue-700 transition">
                                        Edit
                                    </a>

                                    @if($user->user_id != session('user_id'))
                                        <form action="{{ route('admin.users.destroy', $user->user_id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this user?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 bg-red-600 text-white text-[10px] font-semibold rounded hover:bg-red-700 transition">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                <p class="text-sm">No users found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination - Compact -->
        @if($users->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
                <div class="flex items-center justify-between">
                    <p class="text-xs text-gray-500">
                        Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }}
                    </p>
                    {{ $users->appends(request()->query())->links('pagination::tailwind') }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection