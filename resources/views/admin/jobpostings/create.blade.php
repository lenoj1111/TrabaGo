{{-- resources/views/admin/job-postings/create.blade.php --}}

@extends('layouts.admin')

@section('title', 'Create Job Posting')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Create New Job Posting</h5>
            <small class="text-muted">Admin-created jobs are automatically approved.</small>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.job-postings.store') }}">
                @csrf

                <div class="row g-4">
                    <!-- Employer Selection -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label required">Employer <span class="text-danger">*</span></label>
                            <select name="employer_id" class="form-select @error('employer_id') is-invalid @enderror" required>
                                <option value="">Select Employer...</option>
                                @foreach($employers as $employer)
                                    <option value="{{ $employer->employer_id }}" 
                                        {{ old('employer_id') == $employer->employer_id ? 'selected' : '' }}>
                                        {{ $employer->company_name }} ({{ $employer->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('employer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Only accredited employers are shown.</small>
                        </div>
                    </div>

                    <!-- Vacancy Count -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label required">Number of Vacancies <span class="text-danger">*</span></label>
                            <input type="number" name="vacancy_count" class="form-control @error('vacancy_count') is-invalid @enderror" 
                                   value="{{ old('vacancy_count', 1) }}" min="1" required>
                            @error('vacancy_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Job Title -->
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label required">Job Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" placeholder="e.g., Senior Software Developer" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label required">Job Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="5" placeholder="Describe the job responsibilities, benefits, etc." required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Qualifications -->
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">Qualifications</label>
                            <textarea name="qualifications" class="form-control @error('qualifications') is-invalid @enderror" 
                                      rows="4" placeholder="List the qualifications, skills, and experience required.">{{ old('qualifications') }}</textarea>
                            @error('qualifications')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Valid Until -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label required">Valid Until <span class="text-danger">*</span></label>
                            <input type="date" name="valid_until" class="form-control @error('valid_until') is-invalid @enderror" 
                                   value="{{ old('valid_until') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                            @error('valid_until')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Disability Options -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Disability Inclusion</label>
                            <div class="form-check">
                                <input type="checkbox" name="accepts_disability" class="form-check-input" 
                                       value="1" id="acceptsDisability" {{ old('accepts_disability') ? 'checked' : '' }}>
                                <label class="form-check-label" for="acceptsDisability">
                                    This job accepts applicants with disabilities
                                </label>
                            </div>
                            <div id="disabilityTypeContainer" style="{{ old('accepts_disability') ? '' : 'display: none;' }}">
                                <input type="text" name="disability_type" class="form-control mt-2" 
                                       placeholder="Specify disability type (e.g., Physical, Visual, Hearing)" 
                                       value="{{ old('disability_type') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Create & Approve
                    </button>
                    <a href="{{ route('admin.job-postings.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const acceptsDisability = document.getElementById('acceptsDisability');
    const disabilityContainer = document.getElementById('disabilityTypeContainer');
    
    acceptsDisability?.addEventListener('change', function() {
        disabilityContainer.style.display = this.checked ? 'block' : 'none';
    });
});
</script>
@endpush