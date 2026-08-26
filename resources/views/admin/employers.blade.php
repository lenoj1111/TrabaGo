@extends('layouts.admin')

@section('title', 'Employers')

@section('header-actions')
    <div class="d-flex gap-2">
        <a href="#" class="btn btn-primary btn-sm">
            <i class="bi bi-building-add"></i> Add Employer
        </a>
    </div>
@endsection

@section('content')
<div class="p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1 fw-bold">Employers</h5>
            <p class="text-muted small mb-0">Manage all registered employers</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase">Total Employers</p>
                            <h4 class="mb-0 fw-bold">{{ $totalEmployers ?? 0 }}</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-building text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase">Accredited</p>
                            <h4 class="mb-0 fw-bold text-success">{{ $accreditedEmployers ?? 0 }}</h4>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-check-circle text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase">Pending</p>
                            <h4 class="mb-0 fw-bold text-warning">{{ $pendingAccreditation ?? 0 }}</h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-clock text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.employers') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label small text-muted">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" 
                           placeholder="Company name..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="accredited" {{ request('status') == 'accredited' ? 'selected' : '' }}>Accredited</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
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

    <!-- Employers Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Company Name</th>
                            <th>Email</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Jobs Posted</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employers ?? [] as $employer)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <span class="fw-semibold">{{ $employer->company_name }}</span>
                                </td>
                                <td>{{ $employer->email ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $employer->is_accredited ? 'success' : 'warning' }}">
                                        {{ $employer->is_accredited ? 'Accredited' : 'Pending' }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $employer->jobs_count ?? 0 }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('admin.employers.documents', $employer->employer_id) }}" class="btn btn-sm btn-outline-primary" title="View submitted documents">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if(!$employer->is_accredited)
                                            <button onclick="accreditEmployer({{ $employer->employer_id }})" 
                                                    class="btn btn-sm btn-outline-success" title="Accredit">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 d-block text-muted"></i>
                                    <p class="text-muted mt-2">No employers found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
function accreditEmployer(id) {
    Swal.fire({
        title: 'Accredit Employer?',
        text: 'This will accredit this employer account.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, accredit!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ url('/admin/employers') }}/${id}/accredit`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) throw new Error(data.error || 'Accreditation failed.');
                return data;
            })
            .then(data => Swal.fire('Success!', data.success, 'success').then(() => location.reload()))
            .catch(error => Swal.fire('Error', error.message, 'error'));
        }
    });
}
</script>

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
@endsection