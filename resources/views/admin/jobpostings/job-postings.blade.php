@extends('layouts.admin')

@section('title', 'Job Postings')

@section('header-actions')
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createJobModal">
            <i class="bi bi-plus-circle"></i> New Job
        </button>
        <a href="{{ route('admin.job-postings-list.export') }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-download"></i> Export
        </a>
    </div>
@endsection

@section('content')
<div class="p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1 fw-bold">Job Postings</h5>
            <p class="text-muted small mb-0">Manage all job postings on the platform</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase">Total</p>
                            <h4 class="mb-0 fw-bold">{{ $stats['total'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-briefcase text-primary"></i>
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
                            <p class="text-muted mb-1 small text-uppercase">Pending</p>
                            <h4 class="mb-0 fw-bold">{{ $stats['pending'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-clock text-warning"></i>
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
                            <p class="text-muted mb-1 small text-uppercase">Approved</p>
                            <h4 class="mb-0 fw-bold">{{ $stats['approved'] ?? 0 }}</h4>
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
                            <p class="text-muted mb-1 small text-uppercase">Expired</p>
                            <h4 class="mb-0 fw-bold">{{ $stats['expired'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-exclamation-circle text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.job-postings-list.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" 
                           placeholder="Title or company..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Created By</label>
                    <select name="created_by" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="employer" {{ request('created_by') == 'employer' ? 'selected' : '' }}>Employer</option>
                        <option value="admin" {{ request('created_by') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Date From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Date To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Job Listings Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Company</th>
                            <th class="text-center">Vacancies</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Created By</th>
                            <th class="text-center">Valid Until</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobPostings ?? [] as $job)
                            @php
                                $isExpired = $job->status == 'approved' && $job->valid_until < now()->toDateString();
                                $statusColor = match($job->status) {
                                    'pending' => 'warning',
                                    'approved' => $isExpired ? 'secondary' : 'success',
                                    'rejected' => 'danger',
                                    'closed' => 'secondary',
                                    default => 'secondary'
                                };
                                $statusLabel = $isExpired ? 'Expired' : ucfirst($job->status);
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <a href="{{ route('admin.job-postings-list.show', $job->job_id) }}" class="text-decoration-none fw-semibold">
                                        {{ Str::limit($job->title, 40) }}
                                    </a>
                                    <br>
                                    <small class="text-muted">{{ $job->created_at }}</small>
                                </td>
                                <td>
                                    @if($job->company_name == 'DMDP')
                                        <span class="badge bg-primary bg-opacity-10 text-primary">DMDP</span>
                                    @else
                                        {{ $job->company_name ?? 'N/A' }}
                                    @endif
                                </td>
                                <td class="text-center">{{ $job->vacancy_count }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($job->created_by == 'admin')
                                        <span class="badge bg-primary bg-opacity-10 text-primary">Admin</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">Employer</span>
                                    @endif
                                </td>
                                <td class="text-center small">
                                    {{ $job->valid_until }}
                                    @if($isExpired)
                                        <br>
                                        <span class="text-danger small fw-semibold">Expired</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('admin.job-postings-list.show', $job->job_id) }}" 
                                           class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.job-postings-list.edit', $job->job_id) }}" 
                                           class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($job->status == 'pending')
                                            <button onclick="approveJob({{ $job->job_id }})" 
                                                    class="btn btn-sm btn-outline-success" title="Approve">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <button onclick="rejectJob({{ $job->job_id }})" 
                                                    class="btn btn-sm btn-outline-danger" title="Reject">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        @endif
                                        @if(in_array($job->status, ['approved', 'pending']))
                                            <button onclick="closeJob({{ $job->job_id }})" 
                                                    class="btn btn-sm btn-outline-secondary" title="Close">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 d-block text-muted"></i>
                                    <p class="text-muted mt-2">No job postings found.</p>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createJobModal">
                                        Create your first job posting
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if(isset($jobPostings) && $jobPostings->hasPages())
        <div class="mt-4 d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Showing {{ $jobPostings->firstItem() ?? 0 }} to {{ $jobPostings->lastItem() ?? 0 }} 
                of {{ $jobPostings->total() }} entries
            </small>
            {{ $jobPostings->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>

<!-- ============================================ -->
<!-- CREATE JOB MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="createJobModal" tabindex="-1" aria-labelledby="createJobModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            
            <!-- Modal Header -->
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #1b2739 0%, #33455e 100%);">
                <h5 class="modal-title fw-bold" id="createJobModalLabel">
                    <i class="bi bi-plus-circle me-2"></i> Create New Job Posting
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body p-4">
                <form method="POST" action="{{ route('admin.job-postings-list.store') }}" id="createJobForm">
                    @csrf
                    
                    <div class="row g-3">
                        <!-- Employer Selection -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="employer_id" class="form-select @error('employer_id') is-invalid @enderror" id="employer_id">
                                    <option value="">DMDP (Default)</option>
                                    @foreach($employers ?? [] as $employer)
                                        <option value="{{ $employer->employer_id }}" {{ old('employer_id') == $employer->employer_id ? 'selected' : '' }}>
                                            {{ $employer->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="employer_id">Employer <span class="text-muted small">(Optional)</span></label>
                            </div>
                            @error('employer_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Leave blank for DMDP job posting</small>
                        </div>

                        <!-- Vacancy Count -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" name="vacancy_count" class="form-control @error('vacancy_count') is-invalid @enderror" 
                                       id="vacancy_count" value="{{ old('vacancy_count', 1) }}" min="1" required>
                                <label for="vacancy_count">Vacancies <span class="text-danger">*</span></label>
                            </div>
                            @error('vacancy_count')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Job Title -->
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" value="{{ old('title') }}" placeholder="Senior Software Developer" required>
                                <label for="title">Job Title <span class="text-danger">*</span></label>
                            </div>
                            @error('title')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                          id="description" style="height: 120px;" placeholder="Job description..." required>{{ old('description') }}</textarea>
                                <label for="description">Job Description <span class="text-danger">*</span></label>
                            </div>
                            @error('description')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Qualifications -->
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea name="qualifications" class="form-control @error('qualifications') is-invalid @enderror" 
                                          id="qualifications" style="height: 100px;" placeholder="Qualifications...">{{ old('qualifications') }}</textarea>
                                <label for="qualifications">Qualifications</label>
                            </div>
                            @error('qualifications')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Valid Until -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" name="valid_until" class="form-control @error('valid_until') is-invalid @enderror" 
                                       id="valid_until" value="{{ old('valid_until') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                <label for="valid_until">Valid Until <span class="text-danger">*</span></label>
                            </div>
                            @error('valid_until')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Disability Options -->
                        <div class="col-md-6">
                            <div class="card border-dashed p-3 bg-light">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="accepts_disability" class="form-check-input" 
                                           value="1" id="acceptsDisability" {{ old('accepts_disability') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="acceptsDisability">
                                        <i class="bi bi-person-wheelchair me-1"></i> Accepts applicants with disabilities
                                    </label>
                                </div>
                                <div id="disabilityTypeContainer" style="{{ old('accepts_disability') ? '' : 'display: none;' }}" class="mt-2">
                                    <input type="text" name="disability_type" class="form-control form-control-sm" 
                                           placeholder="Specify disability type (e.g., Physical, Visual, Hearing)" 
                                           value="{{ old('disability_type') }}">
                                    <small class="text-muted">Optional: Specify the type of disability accepted</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Modal Footer -->
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
                <button type="submit" form="createJobForm" class="btn btn-primary" style="background: linear-gradient(135deg, #1b2739 0%, #33455e 100%); border: none;">
                    <i class="bi bi-check-circle"></i> Create Job Posting
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-content {
        border-radius: 16px;
        overflow: hidden;
    }
    .form-floating > .form-control,
    .form-floating > .form-select {
        border-radius: 10px;
        border: 1px solid #dee2e6;
        transition: all 0.2s ease;
    }
    .form-floating > .form-control:focus,
    .form-floating > .form-select:focus {
        border-color: #33455e;
        box-shadow: 0 0 0 0.2rem rgba(51, 69, 94, 0.15);
    }
    .form-floating > label {
        color: #6c757d;
        font-weight: 500;
    }
    .border-dashed {
        border: 2px dashed #dee2e6;
        border-radius: 10px;
    }
    .form-check-input:checked {
        background-color: #1b2739;
        border-color: #1b2739;
    }
    .modal-footer .btn {
        border-radius: 8px;
        padding: 8px 20px;
        font-weight: 500;
    }
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
    .bg-warning-subtle { background-color: #fff3cd; }
    .bg-success-subtle { background-color: #d1e7dd; }
    .bg-danger-subtle { background-color: #f8d7da; }
    .bg-secondary-subtle { background-color: #e2e3e5; }
    .bg-primary-subtle { background-color: #cfe2ff; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const acceptsDisability = document.getElementById('acceptsDisability');
    const disabilityContainer = document.getElementById('disabilityTypeContainer');
    
    if (acceptsDisability && disabilityContainer) {
        acceptsDisability.addEventListener('change', function() {
            disabilityContainer.style.display = this.checked ? 'block' : 'none';
        });
    }
});

function approveJob(id) {
    Swal.fire({
        title: 'Approve Job?',
        text: 'This will make the job visible to jobseekers.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, approve!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/job-postings-list/${id}/approve`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
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

function rejectJob(id) {
    Swal.fire({
        title: 'Reject Job?',
        text: 'Are you sure you want to reject this job posting?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, reject!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/job-postings-list/${id}/reject`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Rejected!', data.success, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.error || 'Something went wrong.', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Network error occurred.', 'error'));
        }
    });
}

function closeJob(id) {
    Swal.fire({
        title: 'Close Job?',
        text: 'This will close the job posting and prevent further applications.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#6c757d',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, close it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/job-postings-list/${id}/close`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Closed!', data.success, 'success').then(() => location.reload());
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