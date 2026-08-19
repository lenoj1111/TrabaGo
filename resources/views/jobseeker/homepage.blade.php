@extends('layouts.jobseeker')

@section('title', 'Jobseeker Homepage')

@section('content')
<div class="min-h-screen bg-slate-50 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <section class="relative isolate overflow-hidden rounded-2xl bg-slate-900 px-6 py-8 shadow-xl sm:px-10 sm:py-10">
            <div class="absolute inset-y-0 right-0 -z-10 w-1/2 opacity-30" style="background: radial-gradient(circle at center, #c79a4b 0, transparent 65%);"></div>
            <div class="absolute -right-16 -top-24 -z-10 h-64 w-64 rounded-full border border-amber-300/20"></div>
            <div class="absolute -bottom-40 right-24 -z-10 h-80 w-80 rounded-full border border-white/10"></div>

            <div class="relative flex flex-col justify-between gap-8 lg:flex-row lg:items-end">
                <div class="max-w-2xl">
                    <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-amber-300/30 bg-amber-300/10 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-amber-200">
                        <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                        Jobseeker workspace
                    </div>
                    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-5xl">Find work that moves you forward.</h1>
                    <p class="mt-4 max-w-xl text-base leading-7 text-slate-300">Welcome back to TrabaGo. Explore opportunities, keep your applications moving, and build your next chapter.</p>
                </div>
                <a href="{{ route('jobseeker.profile') }}" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-amber-500 px-5 py-3 text-sm font-bold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:-translate-y-0.5 hover:bg-amber-400">
                    Complete your profile
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>
        </section>

        <section class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-slate-500">Available jobs</p>
                    <span class="rounded-lg bg-blue-50 p-2 text-blue-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8 5-8-5m16 0l-8-4-8 4m16 0v10l-8 4-8-4V7" /></svg>
                    </span>
                </div>
                <p class="mt-4 text-3xl font-bold text-slate-900">25</p>
                <p class="mt-1 text-xs font-medium text-emerald-600">New opportunities this week</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-slate-500">Active applications</p>
                    <span class="rounded-lg bg-emerald-50 p-2 text-emerald-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 12c0 5.591 3.824 10.291 9 11.622C17.176 22.291 21 17.591 21 12c0-1.425-.248-2.792-.7-4.016z" /></svg>
                    </span>
                </div>
                <p class="mt-4 text-3xl font-bold text-slate-900">4</p>
                <p class="mt-1 text-xs font-medium text-slate-500">Keep your momentum going</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-slate-500">Skills trainings</p>
                    <span class="rounded-lg bg-amber-50 p-2 text-amber-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    </span>
                </div>
                <p class="mt-4 text-3xl font-bold text-slate-900">3</p>
                <p class="mt-1 text-xs font-medium text-slate-500">Grow skills employers need</p>
            </div>
        </section>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-amber-600">Curated for you</p>
                        <h2 class="mt-1 text-2xl font-bold text-slate-900">Recent job openings</h2>
                    </div>
                    <span class="hidden text-sm font-semibold text-slate-400 sm:block">Cebu City</span>
                </div>

                <div class="mt-6 divide-y divide-slate-100">
                    <article class="flex flex-col gap-4 py-4 first:pt-0 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-start gap-4">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-sm font-bold text-blue-700">ABC</div>
                            <div>
                                <h3 class="font-bold text-slate-900">Web Developer</h3>
                                <p class="mt-1 text-sm text-slate-500">ABC Company <span class="mx-1 text-slate-300">|</span> Cebu City</p>
                                <span class="mt-2 inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Full-time</span>
                            </div>
                        </div>
                        <button type="button" class="self-start rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-900 hover:text-slate-900 sm:self-auto">View opening</button>
                    </article>
                    <article class="flex flex-col gap-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-start gap-4">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-sm font-bold text-rose-700">XYZ</div>
                            <div>
                                <h3 class="font-bold text-slate-900">Graphic Designer</h3>
                                <p class="mt-1 text-sm text-slate-500">XYZ Solutions <span class="mx-1 text-slate-300">|</span> Mandaue City</p>
                                <span class="mt-2 inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">Hybrid</span>
                            </div>
                        </div>
                        <button type="button" class="self-start rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-900 hover:text-slate-900 sm:self-auto">View opening</button>
                    </article>
                    <article class="flex flex-col gap-4 py-4 last:pb-0 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-start gap-4">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-sm font-bold text-violet-700">CG</div>
                            <div>
                                <h3 class="font-bold text-slate-900">Office Staff</h3>
                                <p class="mt-1 text-sm text-slate-500">City Government <span class="mx-1 text-slate-300">|</span> Cebu City</p>
                                <span class="mt-2 inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">New listing</span>
                            </div>
                        </div>
                        <button type="button" class="self-start rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-900 hover:text-slate-900 sm:self-auto">View opening</button>
                    </article>
                </div>
            </section>

            <aside class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-widest text-amber-600">Your progress</p>
                <h2 class="mt-1 text-2xl font-bold text-slate-900">Profile strength</h2>
                <div class="mt-6 flex items-end justify-between">
                    <p class="text-4xl font-bold text-slate-900">70<span class="text-xl text-slate-400">%</span></p>
                    <p class="text-sm font-semibold text-amber-600">Almost there</p>
                </div>
                <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100">
                    <div class="h-full w-[70%] rounded-full bg-amber-500"></div>
                </div>
                <p class="mt-5 text-sm leading-6 text-slate-500">A complete profile helps employers understand your experience and skills faster.</p>
                <a href="{{ route('jobseeker.profile') }}" class="mt-6 inline-flex w-full items-center justify-center rounded-lg bg-slate-900 px-4 py-3 text-sm font-bold text-white transition hover:bg-slate-700">Update profile</a>
            </aside>
        </div>
    </div>
</div>
@endsection