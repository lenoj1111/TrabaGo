@extends('layouts.admin')

@section('title', 'Dashboard')

@section('header-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.job-postings') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> New Job
        </a>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-people"></i> Users
        </a>
    </div>
@endsection

@section('content')
<div class="p-4">

    <!-- Welcome -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1 fw-bold">Dashboard</h5>
            <p class="text-muted small mb-0">Welcome back, {{ Auth::user()->email }}</p>
        </div>
        <span class="text-muted small">{{ now()->format('F d, Y') }}</span>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase">Total Jobs</p>
                            <h3 class="mb-0 fw-bold">{{ $stats['total_jobs'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-briefcase text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase">Pending</p>
                            <h3 class="mb-0 fw-bold">{{ $stats['pending_jobs'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-clock text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase">Employers</p>
                            <h3 class="mb-0 fw-bold">{{ $stats['total_employers'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-building text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase">Applications</p>
                            <h3 class="mb-0 fw-bold">{{ $stats['total_applications'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-file-text text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-3 px-4">
                    <h6 class="mb-0 fw-bold">Quick Actions</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <a href="{{ route('admin.job-postings') }}" class="text-decoration-none">
                                <div class="border rounded-3 p-3 hover-bg">
                                    <i class="bi bi-plus-circle text-primary fs-4 d-block mb-2"></i>
                                    <h6 class="mb-0 fw-semibold small">Post a Job</h6>
                                    <small class="text-muted">Create new listing</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <a href="{{ route('admin.job-postings-list.index') }}" class="text-decoration-none">
                                <div class="border rounded-3 p-3 hover-bg">
                                    <i class="bi bi-list-task text-warning fs-4 d-block mb-2"></i>
                                    <h6 class="mb-0 fw-semibold small">Review Jobs</h6>
                                    <small class="text-muted">{{ $stats['pending_jobs'] ?? 0 }} pending</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                                <div class="border rounded-3 p-3 hover-bg">
                                    <i class="bi bi-people text-success fs-4 d-block mb-2"></i>
                                    <h6 class="mb-0 fw-semibold small">Manage Users</h6>
                                    <small class="text-muted">View all accounts</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <a href="{{ route('admin.reports') }}" class="text-decoration-none">
                                <div class="border rounded-3 p-3 hover-bg">
                                    <i class="bi bi-bar-chart text-info fs-4 d-block mb-2"></i>
                                    <h6 class="mb-0 fw-semibold small">Reports</h6>
                                    <small class="text-muted">View analytics</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Platform Overview -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-3 px-4">
                    <h6 class="mb-0 fw-bold">Platform Overview</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Jobseekers</span>
                        <span class="fw-semibold">{{ $stats['total_jobseekers'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Employers</span>
                        <span class="fw-semibold">{{ $stats['total_employers'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Applications</span>
                        <span class="fw-semibold">{{ $stats['total_applications'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Total Jobs</span>
                        <span class="fw-semibold">{{ $stats['total_jobs'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted small">Pending Review</span>
                        <span class="fw-semibold text-warning">{{ $stats['pending_jobs'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .hover-bg {
        transition: background-color 0.2s ease;
        cursor: pointer;
    }
    .hover-bg:hover {
        background-color: #f8f9fa;
        border-color: #0d6efd !important;
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
</style>
@endsection