@extends('layouts.admin')

@section('title', 'User Management')

@section('header-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus"></i> Add User
        </a>
    </div>
@endsection

@section('content')
<div class="p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1 fw-bold">User Management</h5>
            <p class="text-muted small mb-0">Manage all registered users on the platform</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase">Total Users</p>
                            <h4 class="mb-0 fw-bold">{{ $counts['total'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-people text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase">Active</p>
                            <h4 class="mb-0 fw-bold text-success">{{ $counts['active'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-check-circle text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase">Inactive</p>
                            <h4 class="mb-0 fw-bold text-danger">{{ $counts['inactive'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-x-circle text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase">Pending Approval</p>
                            <h4 class="mb-0 fw-bold text-warning">{{ $counts['pending'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-clock text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" 
                           placeholder="Email or name..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Role</label>
                    <select name="role" class="form-select form-select-sm">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>
                                {{ ucfirst($role) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Approved</th>
                            <th>Created At</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <span class="fw-semibold">{{ $user->email }}</span>
                                </td>
                                <td>
                                    @php
                                        $roleColors = [
                                            'admin' => 'danger',
                                            'employer' => 'primary',
                                            'jobseeker' => 'success',
                                            'jpo' => 'info',
                                            'trainer' => 'warning',
                                            'lmo' => 'secondary'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $roleColors[$user->role] ?? 'secondary' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $user->status == 'active' ? 'success' : 'danger' }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $user->is_approved ? 'success' : 'warning' }}">
                                        {{ $user->is_approved ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('admin.users.edit', $user->user_id) }}" 
                                           class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($user->status == 'active')
                                            <button onclick="toggleStatus({{ $user->user_id }})" 
                                                    class="btn btn-sm btn-outline-danger" title="Deactivate">
                                                <i class="bi bi-person-x"></i>
                                            </button>
                                        @else
                                            <button onclick="toggleStatus({{ $user->user_id }})" 
                                                    class="btn btn-sm btn-outline-success" title="Activate">
                                                <i class="bi bi-person-check"></i>
                                            </button>
                                        @endif
                                        @if(!$user->is_approved && $user->role != 'admin')
                                            <button onclick="approveUser({{ $user->user_id }})" 
                                                    class="btn btn-sm btn-outline-primary" title="Approve">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 d-block text-muted"></i>
                                    <p class="text-muted mt-2">No users found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if(isset($users) && $users->hasPages())
        <div class="mt-4 d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} 
                of {{ $users->total() }} entries
            </small>
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>

<style>
    .rounded-circle {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .rounded-circle i {
        font-size: 1.25rem;
    }
</style>

<script>
function toggleStatus(id) {
    Swal.fire({
        title: 'Toggle User Status?',
        text: 'Are you sure you want to change this user\'s status?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, toggle!'
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
        title: 'Approve User?',
        text: 'This will approve this user account.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, approve!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/users/${id}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
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