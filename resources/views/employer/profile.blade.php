@extends('layouts.employer')

@section('title', 'Company Profile - Employer Portal')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-3xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex items-center gap-5">
            <div class="h-16 w-16 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white text-2xl font-black shrink-0">
                🏢
            </div>
            <div>
                <h1 class="text-2xl font-black">{{ $employer->company_name }}</h1>
                <p class="text-xs text-slate-300">{{ $user->email }} &bull; Employer ID #{{ $employer->employer_id }}</p>
                <div class="mt-1">
                    @if($employer->is_accredited)
                        <span class="rounded-full bg-emerald-400/20 border border-emerald-400/30 px-2.5 py-0.5 text-[10px] font-bold text-emerald-300">
                            ✓ Officially Accredited Employer
                        </span>
                    @else
                        <span class="rounded-full bg-amber-400/20 border border-amber-400/30 px-2.5 py-0.5 text-[10px] font-bold text-amber-300">
                            Accreditation in Progress
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Profile Form -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-lg font-black text-slate-900">Company Information</h2>
                <p class="text-xs text-slate-500">Update company credentials and official contact information.</p>
            </div>

            <form action="{{ route('employer.profile.update') }}" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Company / Enterprise Name *</label>
                    <input type="text" name="company_name" value="{{ $employer->company_name }}" required
                           class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Official Contact Number</label>
                    <input type="text" name="phone" placeholder="e.g. (032) 234-5678 / 09XX-XXX-XXXX"
                           class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Office / Headquarters Address</label>
                    <input type="text" name="office_address" placeholder="e.g. IT Park, Lahug, Cebu City"
                           class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="rounded-xl bg-emerald-600 hover:bg-emerald-500 px-8 py-3 text-xs font-black text-white shadow-lg shadow-emerald-600/30">
                        Save Company Profile
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
