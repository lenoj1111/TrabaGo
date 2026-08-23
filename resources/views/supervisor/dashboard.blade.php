@extends('layouts.supervisor')

@section('title', 'Supervisor Command Center - TrabaGo DMDP')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Public Employment Service Division (PESD)
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Supervisor Accreditation Oversight</h1>
                <p class="text-sm text-slate-300">Oversee employer accreditation papers evaluated and forwarded by Job Placement Officers. Approve and endorse verified credentials to the Admin for official accreditation.</p>
            </div>

            <div class="shrink-0 bg-white/10 backdrop-blur rounded-2xl p-5 border border-white/10 text-center min-w-[150px]">
                <span class="text-xs font-bold text-emerald-300 uppercase tracking-wider">Accredited Employers</span>
                <p class="text-4xl font-black text-emerald-400 mt-0.5">{{ $totalAccreditedEmployers }}</p>
                <span class="text-[10px] text-slate-300">Active in Cebu City</span>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Awaiting Supervisor Endorsement</span>
                    <p class="text-3xl font-black text-amber-600">{{ $pendingSupervisorReviews }}</p>
                    <p class="text-xs text-slate-500">Evaluated and recommended by Job Placement Officers</p>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-amber-50 text-amber-800 border border-amber-200 flex items-center justify-center text-2xl font-black">
                    ⏳
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Supervisor Endorsed</span>
                    <p class="text-3xl font-black text-emerald-700">{{ $endorsedBySupervisor }}</p>
                    <p class="text-xs text-slate-500">Successfully forwarded to Admin for final authorization</p>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center justify-center text-2xl font-black">
                    🏛️
                </div>
            </div>

        </div>

        <!-- Pending Queue from JPO -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-lg font-black text-slate-900">Accreditation Papers Queue (From JPO)</h2>
                    <p class="text-xs text-slate-500">Figure 11: Employer &rarr; JPO &rarr; PESD Supervisor &rarr; Admin</p>
                </div>
                <a href="{{ route('supervisor.accreditations') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900">View Full Queue &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($pendingQueue as $item)
                    <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-black text-slate-900 text-sm">{{ $item->company_name }}</span>
                                <span class="rounded-full bg-blue-100 text-blue-800 border border-blue-200 px-2 py-0.5 text-[10px] font-bold">
                                    JPO Recommended
                                </span>
                            </div>
                            <p class="text-xs text-slate-500">JPO Remarks: {{ $item->jpo_remarks ?: 'Verified legal documents.' }}</p>
                            <span class="text-[10px] text-slate-400">Reviewed by JPO on {{ $item->jpo_reviewed_at ? date('M d, Y h:i A', strtotime($item->jpo_reviewed_at)) : 'Recently' }}</span>
                        </div>

                        <a href="{{ route('supervisor.accreditations') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-md shadow-emerald-600/20 transition-all">
                            Review & Endorse &rarr;
                        </a>
                    </div>
                @empty
                    <p class="py-12 text-center text-xs text-slate-400 italic">No accreditation papers currently waiting for PESD Supervisor endorsement.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
