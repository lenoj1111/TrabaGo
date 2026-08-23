@extends('layouts.lmo')

@section('title', 'LMO Profile - Labor Market Info Officer')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-3xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex items-center gap-5">
            <div class="h-16 w-16 rounded-2xl bg-gradient-to-tr from-emerald-800 to-teal-600 flex items-center justify-center text-white text-2xl font-black shrink-0">
                👁️
            </div>
            <div>
                <h1 class="text-2xl font-black">{{ $profile->full_name ?? 'Labor Market Info Officer' }}</h1>
                <p class="text-xs text-slate-300">{{ $user->email }} &bull; Staff Role: {{ strtoupper($user->role) }}</p>
                <p class="text-xs text-emerald-300 font-semibold">{{ $profile->office ?? 'Labor Market Intelligence Division - Cebu City DMDP' }}</p>
            </div>
        </div>

        <!-- Profile Form -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-lg font-black text-slate-900">Officer Profile Information</h2>
                <p class="text-xs text-slate-500">Figure 13: Manage officer contact details and official labor market division designation.</p>
            </div>

            <form action="{{ route('lmo.profile.update') }}" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Full Name *</label>
                    <input type="text" name="full_name" value="{{ $profile->full_name ?? '' }}" required
                           class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Official Contact Number</label>
                    <input type="text" name="phone" value="{{ $profile->phone ?? '' }}"
                           class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Department / Office</label>
                    <input type="text" name="office" value="{{ $profile->office ?? 'Labor Market Intelligence Division - Cebu City DMDP' }}"
                           class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="rounded-xl bg-emerald-600 hover:bg-emerald-500 px-8 py-3 text-xs font-black text-white shadow-lg shadow-emerald-600/30">
                        Save Officer Profile
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
