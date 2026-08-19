{{-- resources/views/admin/job-postings/show.blade.php --}}

@extends('layouts.admin')

@section('title', 'View Job Posting')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <!-- Job Details -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $jobPosting->title }}</h5>
                    <div>
                        <a href="{{ route('admin.job-postings.edit', $jobPosting->job_id) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="{{ route('admin.job-postings.index') }}" class="btn btn-sm btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted">Company</label>
                                <p class="fw-bold">{{ $jobPosting->company_name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted">Status</label>
                                <p>
                                    <span class="badge bg-{{ $jobPosting->status == 'pending' ? 'warning' : ($jobPosting->status == 'approved' ? 'success' : 'secondary') }}">
                                        {{ ucfirst($jobPosting->status) }}
                                    </span>
                                    @if($jobPosting->status == 'approved' && $jobPosting->valid_until < now()->toDateString())
                                        <span class="badge bg-danger">Expired</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted">Vacancies</label>
                                <p class="fw-bold">{{ $jobPosting->vacancy_count }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted">Created By</label>
                                <p>
                                    @if($jobPosting->created_by == 'admin')
                                        <span class="badge bg-primary">Admin</span>
                                        <br>
                                        <small>{{ $jobPosting->admin_name ?? 'N/A' }}</small>
                                    @else
                                        <span class="badge bg-secondary">Employer</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted">Valid Until</label>
                                <p class="fw-bold">
                                    {{ \Carbon\Carbon::parse($jobPosting->valid_until)->format('F d, Y') }}
                                    @if($jobPosting->valid_until < now()->toDateString())
                                        <span class="badge bg-danger ms-2">Expired</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted">Disability Inclusion</label>
                                <p>
                                    @if($jobPosting->accepts_disability)
                                        <span class="badge bg-success">Accepts PWD Applicants</span>
                                        @if($jobPosting->disability_type)
                                            <br>
                                            <small class="text-muted">Type: {{ $jobPosting->disability_type }}</small>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Not Specified</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="info-item">
                                <label class="text-muted">Created At</label>
                                <p class="fw-bold">{{ \Carbon\Carbon::parse($jobPosting->created_at)->format('F d, Y h:i A') }}</p>
                            </div>
                        </div>
                        @if($jobPosting->approved_at)
                            <div class="col-md-12">
                                <div class="info-item">
                                    <label class="text-muted">Approved At</label>
                                    <p class="fw-bold">{{ \Carbon\Carbon::parse($jobPosting->approved_at)->format('F d, Y h:i A') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <hr>

                    <!-- Description -->
                    <div class="mt-3">
                        <h6 class="fw-bold">Job Description</h6>
                        <div class="p-3 bg-light rounded">
                            {!! nl2br(e($jobPosting->description)) !!}
                        </div>
                    </div>

                    <!-- Qualifications -->
                    @if($jobPosting->qualifications)
                        <div class="mt-3">
                            <h6 class="fw-bold">Qualifications</h6>
                            <div class="p-3 bg-light rounded">
                                {!! nl2br(e($jobPosting->qualifications)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Applications -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Applications ({{ $applications->count() }})</h5>
                </div>
                <div class="card-body p-0">
                    @if($applications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Applicant</th>
                                        <th>Contact</th>
                                        <th>Status</th>
                                        <th>JPO Assessment</th>
                                        <th>Applied At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applications as $app)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $app->first_name }} {{ $app->last_name }}</strong>
                                                @if($app->middle_name)
                                                    <br>
                                                    <small class="text-muted">{{ $app->middle_name }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div><i class="bi bi-envelope"></i> {{ $app->email ?? $app->jobseeker_email }}</div>
                                                <div><i class="bi bi-phone"></i> {{ $app->mobile_number ?? 'N/A' }}</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $app->status == 'pending' ? 'warning' : ($app->status == 'interview' ? 'info' : ($app->status == 'hired' ? 'success' : ($app->status == 'rejected' ? 'danger' : 'secondary'))) }}">
                                                    {{ ucfirst($app->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($app->recommendation)
                                                    <span class="badge bg-{{ $app->recommendation == 'refer' ? 'success' : 'primary' }}">
                                                        {{ ucfirst($app->recommendation) }}
                                                    </span>
                                                    @if($app->jpo_remarks)
                                                        <br>
                                                        <small class="text-muted">{{ Str::limit($app->jpo_remarks, 30) }}</small>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">Not assessed</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ \Carbon\Carbon::parse($app->created_at)->format('M d, Y') }}</small>
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-info" title="View Application">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox fs-1 d-block text-muted"></i>
                            <p class="text-muted mt-2">No applications for this job posting.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($jobPosting->status == 'pending')
                            <button class="btn btn-success btn-approve" data-id="{{ $jobPosting->job_id }}">
                                <i class="bi bi-check-lg"></i> Approve Job
                            </button>
                            <button class="btn btn-danger btn-reject" data-id="{{ $jobPosting->job_id }}">
                                <i class="bi bi-x-lg"></i> Reject Job
                            </button>
                        @endif
                        
                        @if(in_array($jobPosting->status, ['approved', 'pending']))
                            <button class="btn btn-secondary btn-close-job" data-id="{{ $jobPosting->job_id }}">
                                <i class="bi bi-x-circle"></i> Close Job
                            </button>
                        @endif
                        
                        <a href="{{ route('admin.job-postings.edit', $jobPosting->job_id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit Job
                        </a>
                    </div>
                </div>
            </div>

            <!-- Job Statistics -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span>Total Applications</span>
                        <span class="fw-bold">{{ $applications->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span>Pending</span>
                        <span class="fw-bold">{{ $applications->where('status', 'pending')->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span>In Interview</span>
                        <span class="fw-bold">{{ $applications->where('status', 'interview')->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span>Hired</span>
                        <span class="fw-bold">{{ $applications->where('status', 'hired')->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between pt-2">
                        <span>Rejected</span>
                        <span class="fw-bold">{{ $applications->where('status', 'rejected')->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Job Posting</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason for Rejection</label>
                        <textarea name="rejection_reason" class="form-control" rows="3" 
                                  placeholder="Please provide a reason for rejecting this job posting..."></textarea>
                        <small class="text-muted">This reason will be recorded in the audit log.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Job</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Approve job
    document.querySelector('.btn-approve')?.addEventListener('click', function() {
        const id = this.dataset.id;
        Swal.fire({
            title: 'Approve Job Posting?',
            text: 'This will make the job posting visible to jobseekers.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, approve it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ url('admin/job-postings') }}/${id}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Approved!', data.success, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', data.error || 'Something went wrong.', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'Network error occurred.', 'error');
                });
            }
        });
    });

    // Reject job modal
    document.querySelector('.btn-reject')?.addEventListener('click', function() {
        const id = this.dataset.id;
        const form = document.getElementById('rejectForm');
        form.action = `{{ url('admin/job-postings') }}/${id}/reject`;
        const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
        modal.show();
    });

    // Close job
    document.querySelector('.btn-close-job')?.addEventListener('click', function() {
        const id = this.dataset.id;
        Swal.fire({
            title: 'Close Job Posting?',
            text: 'This will close the job posting and prevent further applications.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6c757d',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, close it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ url('admin/job-postings') }}/${id}/close`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Closed!', data.success, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', data.error || 'Something went wrong.', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'Network error occurred.', 'error');
                });
            }
        });
    });
});
</script>
@endpush