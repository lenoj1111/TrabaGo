@extends('layouts.employer')

@section('title', 'Employer Homepage')

@section('content')
<div class="min-h-[calc(100vh-4rem)]">
    <section class="relative overflow-hidden" style="background: linear-gradient(135deg, #1b2739 0%, #33455e 58%, #405673 100%);">
        <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 22px 22px;"></div>
        <div class="absolute right-[-70px] top-[-100px] h-72 w-72 rounded-full border border-gold-500/20"></div>
        <div class="absolute left-0 top-0 h-1.5 w-full" style="background: linear-gradient(90deg, #1b2739 0 33%, #b3894a 33% 66%, #ce1126 66%);" ></div>
        <div class="relative mx-auto flex max-w-7xl flex-col justify-between gap-8 px-5 py-12 md:flex-row md:items-end md:py-16">
            <div>
                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gold-300">
                    <span class="h-2 w-2 rounded-full bg-green-400"></span> Employer workspace
                </div>
                <h1 class="max-w-2xl text-4xl font-bold leading-tight text-white md:text-5xl">Build your next great team.</h1>
                <p class="mt-4 max-w-xl text-base leading-relaxed text-brand-200">Manage opportunities, connect with qualified talent, and keep your company moving forward with TrabaGo.</p>
            </div>
            <a href="{{ route('employer.job-postings') }}" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-gold-500 px-5 py-3 font-semibold text-white shadow-lg shadow-gold-500/20 transition hover:-translate-y-0.5 hover:bg-gold-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Create a job posting
            </a>
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-5 py-8 md:py-10">
        <div class="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-brand-100 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between"><p class="text-sm font-semibold text-brand-500">Active postings</p><span class="rounded-lg bg-blue-50 p-2 text-blue-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></span></div>
                <p class="mt-4 text-3xl font-bold text-brand-900">{{ $activePostings ?? 0 }}</p><p class="mt-1 text-xs text-brand-400">Ready to attract applicants</p>
            </div>
            <div class="rounded-xl border border-brand-100 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between"><p class="text-sm font-semibold text-brand-500">Total applications</p><span class="rounded-lg bg-green-50 p-2 text-green-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-6H7v6m10 0H7"/></svg></span></div>
                <p class="mt-4 text-3xl font-bold text-brand-900">{{ $totalApplications ?? 0 }}</p><p class="mt-1 text-xs text-brand-400">Applications to review</p>
            </div>
            <div class="rounded-xl border border-brand-100 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between"><p class="text-sm font-semibold text-brand-500">Shortlisted</p><span class="rounded-lg bg-gold-50 p-2 text-gold-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></span></div>
                <p class="mt-4 text-3xl font-bold text-brand-900">{{ $shortlistedApplications ?? 0 }}</p><p class="mt-1 text-xs text-brand-400">Candidates progressing</p>
            </div>
            <div class="rounded-xl border border-brand-100 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between"><p class="text-sm font-semibold text-brand-500">Accreditation</p><span class="rounded-lg bg-red-50 p-2 text-red-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622C17.176 19.29 21 14.591 21 9c0-.695-.06-1.376-.174-2.04z"/></svg></span></div>
                <p class="mt-4 text-xl font-bold {{ ($employer->is_accredited ?? false) ? 'text-green-600' : 'text-red-600' }}">{{ ($employer->is_accredited ?? false) ? 'Accredited' : 'Pending' }}</p><p class="mt-1 text-xs text-brand-400">{{ ($employer->is_accredited ?? false) ? 'Your company is verified' : 'Complete your verification' }}</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <section class="rounded-xl border border-brand-100 bg-white p-6 shadow-sm lg:col-span-2">
                <div class="flex items-start justify-between gap-4"><div><p class="text-xs font-bold uppercase tracking-widest text-gold-600">Get started</p><h2 class="mt-1 text-xl font-bold text-brand-900">Your employer checklist</h2></div><span class="text-sm font-semibold text-brand-400">{{ ($employer->is_accredited ?? false) ? '3 of 3' : '1 of 3' }} complete</span></div>
                <div class="mt-5 h-2 overflow-hidden rounded-full bg-brand-100"><div class="h-full rounded-full bg-gold-500 {{ ($employer->is_accredited ?? false) ? 'w-full' : 'w-1/3' }}"></div></div>
                <div class="mt-6 divide-y divide-brand-100">
                    <a href="{{ route('employer.profile') }}" class="flex items-center justify-between gap-4 py-4 first:pt-0"><span class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-full bg-green-50 text-green-600">&#10003;</span><span><strong class="block text-sm text-brand-900">Create your company profile</strong><small class="text-xs text-brand-400">Tell jobseekers what makes your company different.</small></span></span><span class="text-brand-300">&#8594;</span></a>
                    <a href="{{ route('employer.accreditation') }}" class="flex items-center justify-between gap-4 py-4"><span class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-full {{ ($employer->is_accredited ?? false) ? 'bg-green-50 text-green-600' : 'bg-gold-50 text-gold-600' }}">{{ ($employer->is_accredited ?? false) ? '&#10003;' : '2' }}</span><span><strong class="block text-sm text-brand-900">{{ ($employer->is_accredited ?? false) ? 'Accreditation approved' : 'Submit accreditation documents' }}</strong><small class="text-xs text-brand-400">{{ ($employer->is_accredited ?? false) ? 'Your company can now use employer tools.' : 'Get verified to unlock employer tools.' }}</small></span></span><span class="text-brand-300">&#8594;</span></a>
                    <a href="{{ route('employer.job-postings') }}" class="flex items-center justify-between gap-4 py-4 last:pb-0"><span class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-50 text-brand-500">{{ ($employer->is_accredited ?? false) ? '&#10003;' : '3' }}</span><span><strong class="block text-sm text-brand-900">Publish your first opportunity</strong><small class="text-xs text-brand-400">Reach jobseekers across Cebu City.</small></span></span><span class="text-brand-300">&#8594;</span></a>
                </div>
            </section>

            <section class="rounded-xl border border-brand-100 bg-white p-6 shadow-sm"><div class="flex items-center justify-between"><div><p class="text-xs font-bold uppercase tracking-widest text-gold-600">Shortcuts</p><h2 class="mt-1 text-xl font-bold text-brand-900">Quick actions</h2></div><span class="rounded-full bg-brand-50 p-2 text-brand-500"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></span></div><div class="mt-5 space-y-3"><a href="{{ route('employer.job-postings') }}" class="flex items-center gap-3 rounded-lg border border-brand-100 p-3 text-sm font-semibold text-brand-700 transition hover:border-gold-400 hover:bg-gold-50"><span class="text-lg text-gold-600">+</span> Post a new job</a><a href="{{ route('employer.applications') }}" class="flex items-center gap-3 rounded-lg border border-brand-100 p-3 text-sm font-semibold text-brand-700 transition hover:border-gold-400 hover:bg-gold-50"><span class="text-lg text-gold-600">&#8599;</span> Review applications</a><a href="{{ route('employer.profile') }}" class="flex items-center gap-3 rounded-lg border border-brand-100 p-3 text-sm font-semibold text-brand-700 transition hover:border-gold-400 hover:bg-gold-50"><span class="text-lg text-gold-600">&#9673;</span> Update company profile</a></div></section>
        </div>

        <div class="mt-6 rounded-xl border border-gold-200 bg-gold-50/70 p-5"><div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"><div><p class="text-sm font-bold text-brand-900">{{ ($employer->is_accredited ?? false) ? 'Your employer account is verified' : 'Need help getting started?' }}</p><p class="mt-1 text-sm text-brand-600">{{ ($employer->is_accredited ?? false) ? 'You can now publish opportunities and connect with local talent.' : 'Complete your accreditation so you can begin connecting with local talent.' }}</p></div><a href="{{ route('employer.accreditation') }}" class="inline-flex items-center justify-center rounded-lg bg-brand-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800">{{ ($employer->is_accredited ?? false) ? 'View status' : 'View accreditation' }} <span class="ml-2">&#8594;</span></a></div></div>
    </div>
</div>
@endsection
