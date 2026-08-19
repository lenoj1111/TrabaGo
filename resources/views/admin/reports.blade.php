@extends('layouts.admin')

@section('title', 'Reports')

@section('header-actions')
    <div class="d-flex gap-2">
        <button class="btn btn-primary btn-sm" onclick="window.print()">
            <i class="bi bi-printer"></i> Print
        </button>
        <button class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-download"></i> Export
        </button>
    </div>
@endsection

@section('content')
<div class="p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1 fw-bold">Reports</h5>
            <p class="text-muted small mb-0">View platform analytics and reports</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase">Total Jobs</p>
                            <h4 class="mb-0 fw-bold">{{ $stats['total_jobs'] ?? 0 }}</h4>
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
                            <p class="text-muted mb-1 small text-uppercase">Employers</p>
                            <h4 class="mb-0 fw-bold">{{ $stats['total_employers'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-building text-success"></i>
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
                            <p class="text-muted mb-1 small text-uppercase">Jobseekers</p>
                            <h4 class="mb-0 fw-bold">{{ $stats['total_jobseekers'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-people text-info"></i>
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
                            <p class="text-muted mb-1 small text-uppercase">Applications</p>
                            <h4 class="mb-0 fw-bold">{{ $stats['total_applications'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-file-text text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jobs by Status -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-3 px-4">
                    <h6 class="mb-0 fw-bold">Jobs by Status</h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span class="text-muted small">Pending</span>
                        <span class="fw-semibold text-warning">{{ $stats['pending_jobs'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span class="text-muted small">Approved</span>
                        <span class="fw-semibold text-success">{{ $stats['approved'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span class="text-muted small">Rejected</span>
                        <span class="fw-semibold text-danger">{{ $stats['rejected'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted small">Closed</span>
                        <span class="fw-semibold text-secondary">{{ $stats['closed'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-3 px-4">
                    <h6 class="mb-0 fw-bold">User Distribution</h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span class="text-muted small">Admins</span>
                        <span class="fw-semibold text-danger">{{ $stats['admin_count'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span class="text-muted small">Employers</span>
                        <span class="fw-semibold text-primary">{{ $stats['total_employers'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span class="text-muted small">Jobseekers</span>
                        <span class="fw-semibold text-success">{{ $stats['total_jobseekers'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted small">Staff (JPO/Trainer/LMO)</span>
                        <span class="fw-semibold text-info">{{ $stats['staff_count'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
@endsection