@extends('layouts.admin')

@section('title', 'Edit Job Posting')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8" x-data="{ acceptsDisability: {{ old('accepts_disability', $jobPosting->accepts_disability) ? 'true' : 'false' }} }">
    <div class="mx-auto max-w-4xl space-y-8">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <a href="{{ route('admin.job-postings') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 inline-flex items-center gap-1">
                    &larr; Back to Job Postings
                </a>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Edit Job Posting #{{ $jobPosting->job_id }}</h1>
                <p class="text-xs text-slate-500">Update listing details, vacancies, requirements, or application deadline.</p>
            </div>

            <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-bold border {{ $jobPosting->status === 'approved' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-amber-50 text-amber-800 border-amber-200' }}">
                Status: {{ ucfirst($jobPosting->status) }}
            </span>
        </div>

        <!-- Form Card -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-10 shadow-sm">
            <form method="POST" action="{{ route('admin.job-postings.update', $jobPosting->job_id) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Employer Entity</label>
                        <select name="employer_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                            <option value="">DMDP Direct</option>
                            @foreach($employers as $employer)
                                <option value="{{ $employer->employer_id }}" {{ old('employer_id', $jobPosting->employer_id) == $employer->employer_id ? 'selected' : '' }}>
                                    {{ $employer->company_name }} ({{ $employer->email }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-400">Leave blank for direct DMDP public posting.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Number of Vacancies <span class="text-rose-500">*</span></label>
                        <input type="number" name="vacancy_count" value="{{ old('vacancy_count', $jobPosting->vacancy_count) }}" min="1" required 
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none @error('vacancy_count') border-rose-400 @enderror">
                        @error('vacancy_count')
                            <p class="text-[11px] font-bold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Job Title <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $jobPosting->title) }}" required placeholder="e.g. Senior Software Developer"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none @error('title') border-rose-400 @enderror">
                        @error('title')
                            <p class="text-[11px] font-bold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Job Description & Responsibilities <span class="text-rose-500">*</span></label>
                        <textarea name="description" rows="5" required placeholder="Describe duties and benefits..."
                                  class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none @error('description') border-rose-400 @enderror">{{ old('description', $jobPosting->description) }}</textarea>
                        @error('description')
                            <p class="text-[11px] font-bold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Qualifications & Requirements</label>
                        <textarea name="qualifications" rows="4" placeholder="Qualifications and skills required..."
                                  class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none @error('qualifications') border-rose-400 @enderror">{{ old('qualifications', $jobPosting->qualifications) }}</textarea>
                        @error('qualifications')
                            <p class="text-[11px] font-bold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Application Deadline (Valid Until) <span class="text-rose-500">*</span></label>
                        <input type="date" name="valid_until" value="{{ old('valid_until', $jobPosting->valid_until) }}" required min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none @error('valid_until') border-rose-400 @enderror">
                        @error('valid_until')
                            <p class="text-[11px] font-bold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700 select-none">
                            <input type="checkbox" name="accepts_disability" value="1" x-model="acceptsDisability"
                                   class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                            <span>♿ Accepts applicants with disabilities (PWD)</span>
                        </label>
                        <div x-show="acceptsDisability" style="display: none;" class="pt-1">
                            <input type="text" name="disability_type" value="{{ old('disability_type', $jobPosting->disability_type) }}" placeholder="Specify disability type (e.g. Visual, Hearing, Orthopedic)"
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Form Controls -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('admin.job-postings') }}" 
                       class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-black text-xs shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                        ✓ Update Job Posting
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection