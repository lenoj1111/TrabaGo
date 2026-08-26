@extends('layouts.admin')

@section('title', 'Employer Documents')

@section('content')
<div class="p-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.employers') }}" class="btn btn-sm btn-outline-secondary" title="Back to employers">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h5 class="mb-1 fw-bold">Accreditation Documents</h5>
            <p class="text-muted small mb-0">Review documents submitted by {{ $employer->company_name }}</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <span class="d-block text-muted small text-uppercase">Company</span>
                    <strong>{{ $employer->company_name }}</strong>
                </div>
                <div class="col-md-4">
                    <span class="d-block text-muted small text-uppercase">Email</span>
                    <strong>{{ $employer->email }}</strong>
                </div>
                <div class="col-md-4">
                    <span class="d-block text-muted small text-uppercase">Status</span>
                    <span class="badge bg-{{ $employer->is_accredited ? 'success' : 'warning' }}">
                        {{ $employer->is_accredited ? 'Accredited' : 'Pending' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold">Submitted files</h6>
            @if($employer->submitted_at)
                <small class="text-muted">Submitted {{ $employer->submitted_at }}</small>
            @endif
        </div>
        <div class="card-body">
            @forelse($documents as $type => $path)
                <div class="d-flex align-items-center justify-content-between border rounded p-3 mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-file-earmark-text fs-3 text-primary"></i>
                        <div>
                            <strong class="d-block">{{ ucwords(str_replace('_', ' ', $type)) }}</strong>
                            <small class="text-muted">{{ basename($path) }}</small>
                        </div>
                    </div>
                    <a href="{{ Storage::disk('public')->url($path) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i> View
                    </a>
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-file-earmark-x fs-1 d-block mb-2"></i>
                    <p class="mb-0">No accreditation documents were submitted.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
