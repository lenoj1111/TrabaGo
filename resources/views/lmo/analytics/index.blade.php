@extends('layouts.lmo')

@section('title', 'Market Insights & Analytics - Labor Market Info Officer')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 rounded-full bg-emerald-400/20 px-3.5 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Figure 13: Labor Market Analysis
                </div>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Market Intelligence & Analytics</h1>
                <p class="text-sm text-slate-300">Empirical labor market analysis, skill supply distribution, and monthly employment placement trends across Cebu City.</p>
            </div>
        </div>

        <!-- Monthly Hiring Placement Trends Card -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-base font-black text-slate-900">Monthly Jobseeker Placement & Hiring Trends</h2>
                    <p class="text-xs text-slate-500">Verified successful placements aggregated by hire month.</p>
                </div>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
                    📈 Recent 6 Months
                </span>
            </div>

            @if(count($monthlyHiredTrends) > 0)
                @php
                    $maxTotal = max(collect($monthlyHiredTrends)->pluck('total')->toArray() ?: [1]);
                    $maxTotal = $maxTotal > 0 ? $maxTotal : 1;
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 pt-2">
                    @foreach($monthlyHiredTrends as $trend)
                        @php
                            $monthLabel = date('M Y', strtotime($trend->hire_month . '-01'));
                            $percentage = min(100, round(($trend->total / $maxTotal) * 100));
                        @endphp
                        <div class="p-4 rounded-2xl bg-gradient-to-b from-slate-50 to-emerald-50/30 border border-slate-100 flex flex-col justify-between gap-3">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ $monthLabel }}</span>
                            <div>
                                <p class="text-2xl font-black text-emerald-900">{{ $trend->total }}</p>
                                <p class="text-[11px] text-emerald-700 font-semibold">Hired Candidates</p>
                            </div>
                            <div class="w-full bg-slate-200/70 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 text-center rounded-2xl bg-slate-50 border border-dashed border-slate-200">
                    <p class="text-xs text-slate-400 font-medium">No candidate placements recorded with verified hire dates yet.</p>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Skill Distribution -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
                <h2 class="text-base font-black text-slate-900 border-b border-slate-100 pb-3">Top Verified Skills Among Jobseekers</h2>
                <div class="space-y-3">
                    @forelse($skillDistribution as $s)
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 hover:bg-emerald-50/50 transition-colors">
                            <span class="text-xs font-bold text-slate-800">{{ $s->skill_name }}</span>
                            <span class="text-xs font-black text-emerald-700 bg-emerald-100/60 px-2.5 py-0.5 rounded-full">{{ $s->total }} individuals</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">No skills analyzed yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- Employment Status Demographics -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
                <h2 class="text-base font-black text-slate-900 border-b border-slate-100 pb-3">Employment Readiness Breakdown</h2>
                <div class="space-y-3">
                    @forelse($employmentByStatus as $st)
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 hover:bg-teal-50/50 transition-colors">
                            <span class="text-xs font-bold text-slate-800">{{ $st->status_name }}</span>
                            <span class="text-xs font-black text-teal-700 bg-teal-100/60 px-2.5 py-0.5 rounded-full">{{ $st->total }} jobseekers</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">No data available.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
