@extends('layouts.employer')

@section('title', 'Job Postings')

@section('content')
<div class="min-h-[calc(100vh-4rem)] bg-brand-50/50">
    <section class="relative overflow-hidden" style="background: linear-gradient(135deg, #1b2739 0%, #33455e 58%, #405673 100%);">
        <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 22px 22px;"></div>
        <div class="absolute left-0 top-0 h-1.5 w-full" style="background: linear-gradient(90deg, #1b2739 0 33%, #b3894a 33% 66%, #ce1126 66%);"></div>
        <div class="relative mx-auto flex max-w-7xl flex-col justify-between gap-6 px-5 py-10 md:flex-row md:items-end">
            <div>
                <p class="mb-3 text-xs font-bold uppercase tracking-widest text-gold-300">Employer workspace</p>
                <h1 class="text-3xl font-bold text-white md:text-4xl">Job postings</h1>
                <p class="mt-3 max-w-xl text-sm leading-relaxed text-brand-200">Create opportunities and track their review status in one place.</p>
            </div>
            <a href="#create-posting" class="inline-flex items-center justify-center gap-2 rounded-lg bg-gold-500 px-5 py-3 font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-gold-600">
                <span class="text-xl leading-none">+</span> Create a posting
            </a>
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-5 py-8">
        @if(session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
        @endif

        <div class="mb-8 grid gap-4 sm:grid-cols-3">
            @foreach([
                ['label' => 'All postings', 'value' => $jobPostings->count(), 'color' => 'text-brand-900'],
                ['label' => 'Pending review', 'value' => $jobPostings->where('status', 'pending')->count(), 'color' => 'text-gold-600'],
                ['label' => 'Approved', 'value' => $jobPostings->where('status', 'approved')->count(), 'color' => 'text-green-600'],
            ] as $stat)
                <div class="rounded-xl border border-brand-100 bg-white p-5 shadow-sm"><p class="text-sm font-semibold text-brand-500">{{ $stat['label'] }}</p><p class="mt-3 text-3xl font-bold {{ $stat['color'] }}">{{ $stat['value'] }}</p></div>
            @endforeach
        </div>

        <div class="grid gap-6 lg:grid-cols-5">
            <section class="rounded-xl border border-brand-100 bg-white p-6 shadow-sm lg:col-span-3">
                <div class="flex items-center justify-between border-b border-brand-100 pb-4"><div><p class="text-xs font-bold uppercase tracking-widest text-gold-600">Your opportunities</p><h2 class="mt-1 text-xl font-bold text-brand-900">Posting history</h2></div><span class="text-sm text-brand-400">{{ $jobPostings->count() }} total</span></div>
                <div class="mt-4 space-y-3">
                    @forelse($jobPostings as $job)
                        @php
                            $statusClasses = ['pending' => 'bg-gold-50 text-gold-700', 'approved' => 'bg-green-50 text-green-700', 'rejected' => 'bg-red-50 text-red-700', 'closed' => 'bg-brand-100 text-brand-600'];
                        @endphp
                        <div class="rounded-lg border border-brand-100 p-4 transition hover:border-gold-300">
                            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start"><div><h3 class="font-bold text-brand-900">{{ $job->title }}</h3><p class="mt-1 text-xs text-brand-400">{{ $job->vacancy_count }} {{ $job->vacancy_count == 1 ? 'vacancy' : 'vacancies' }} · Valid until {{ \Carbon\Carbon::parse($job->valid_until)->format('M d, Y') }}</p></div><span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-bold uppercase {{ $statusClasses[$job->status] ?? 'bg-brand-100 text-brand-600' }}">{{ ucfirst($job->status) }}</span></div>
                            <p class="mt-3 line-clamp-2 text-sm leading-relaxed text-brand-600">{{ $job->description }}</p>
                            @if(in_array($job->status, ['pending', 'approved']))
                                <form method="POST" action="{{ route('employer.job-postings.close', $job->job_id) }}" class="mt-3"><button type="submit" class="text-xs font-semibold text-brand-500 hover:text-red-600" onclick="return confirm('Close this job posting?')">Close posting</button></form>
                            @endif
                        </div>
                    @empty
                        <div class="py-12 text-center"><div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-brand-50 text-2xl text-gold-600">+</div><h3 class="mt-4 font-bold text-brand-900">No postings yet</h3><p class="mt-1 text-sm text-brand-500">Create your first opportunity using the form.</p></div>
                    @endforelse
                </div>
            </section>

            <section id="create-posting" class="rounded-xl border border-brand-100 bg-white p-6 shadow-sm lg:col-span-2"><p class="text-xs font-bold uppercase tracking-widest text-gold-600">New opportunity</p><h2 class="mt-1 text-xl font-bold text-brand-900">Create a job posting</h2><p class="mt-2 text-sm text-brand-500">Submissions are sent to DMDP for review.</p>
                <form method="POST" action="{{ route('employer.job-postings.store') }}" class="mt-5 space-y-4">@csrf
                    <div><label for="title" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">Job title</label><input id="title" name="title" value="{{ old('title') }}" required maxlength="150" placeholder="e.g. Customer Service Representative" class="w-full rounded-lg border border-brand-200 px-4 py-2.5 outline-none transition focus:border-gold-500 focus:ring-2 focus:ring-gold-500/20"></div>
                    <div><label for="description" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">Description</label><textarea id="description" name="description" rows="4" required placeholder="Describe the role and responsibilities" class="w-full rounded-lg border border-brand-200 px-4 py-2.5 outline-none transition focus:border-gold-500 focus:ring-2 focus:ring-gold-500/20">{{ old('description') }}</textarea></div>
                    <div><label for="qualifications" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">Qualifications</label><textarea id="qualifications" name="qualifications" rows="3" placeholder="Skills, education, and experience required" class="w-full rounded-lg border border-brand-200 px-4 py-2.5 outline-none transition focus:border-gold-500 focus:ring-2 focus:ring-gold-500/20">{{ old('qualifications') }}</textarea></div>
                    <div class="grid gap-4 sm:grid-cols-2"><div><label for="vacancy_count" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">Vacancies</label><input id="vacancy_count" type="number" name="vacancy_count" value="{{ old('vacancy_count', 1) }}" min="1" required class="w-full rounded-lg border border-brand-200 px-4 py-2.5 outline-none focus:border-gold-500 focus:ring-2 focus:ring-gold-500/20"></div><div><label for="valid_until" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">Valid until</label><input id="valid_until" type="date" name="valid_until" value="{{ old('valid_until') }}" min="{{ now()->addDay()->toDateString() }}" required class="w-full rounded-lg border border-brand-200 px-4 py-2.5 outline-none focus:border-gold-500 focus:ring-2 focus:ring-gold-500/20"></div></div>
                    <label class="flex items-center gap-2 text-sm text-brand-600"><input type="checkbox" name="accepts_disability" value="1" class="h-4 w-4 rounded border-brand-300 text-gold-600"> Accepts applicants with disabilities</label>
                    <div><label for="disability_type" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">Disability type <span class="font-normal normal-case text-brand-400">(optional)</span></label><input id="disability_type" name="disability_type" value="{{ old('disability_type') }}" maxlength="100" class="w-full rounded-lg border border-brand-200 px-4 py-2.5 outline-none focus:border-gold-500 focus:ring-2 focus:ring-gold-500/20"></div>
                    <button type="submit" class="w-full rounded-lg px-5 py-3 font-semibold text-white transition hover:-translate-y-0.5" style="background-color: #1b2739;">Submit for review</button>
                </form>
            </section>
        </div>
    </div>
</div>
@endsection
