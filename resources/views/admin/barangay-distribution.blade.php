@extends('layouts.admin')

@section('title', 'Cebu City Barangay Distribution - DMDP Admin Portal')

@section('content')
<div class="min-h-screen bg-slate-50/70 px-4 py-6 sm:px-6 lg:px-8" x-data="{ rationaleModalOpen: false }">
    <div class="w-full max-w-[1680px] mx-auto space-y-6">
        
        <!-- HEADER HERO -->
        <header class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-slate-900 to-emerald-950 p-6 sm:p-8 text-white shadow-lg border border-slate-800">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-semibold text-emerald-300 border border-emerald-500/30">
                            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Live Database Sync
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-300 border border-slate-700">
                            80 Official Cebu City Barangays
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-500/20 px-3 py-1 text-xs font-semibold text-blue-300 border border-blue-500/30">
                            RA 10173 Protected
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                        Barangay Labor & Demographic Distribution
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                        Macro-level workforce intelligence across all 80 Cebu City barangays for DMDP strategic planning, City Council reports, and mobile skills extension.
                    </p>
                </div>

                <!-- Action Toolbar -->
                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <a href="{{ route('admin.barangay-distribution.export', request()->query()) }}" 
                       class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white px-4 py-2.5 text-xs font-bold shadow-md shadow-emerald-950/40 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Export CSV</span>
                    </a>

                    <button @click="rationaleModalOpen = true" 
                            type="button"
                            class="inline-flex items-center gap-2 rounded-xl bg-slate-800 hover:bg-slate-700 active:scale-95 text-slate-200 border border-slate-700 px-3.5 py-2.5 text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400">
                        <svg class="h-4 w-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Policy Guide</span>
                    </button>

                    <button onclick="window.print()" 
                            type="button"
                            class="inline-flex items-center gap-2 rounded-xl bg-slate-800 hover:bg-slate-700 active:scale-95 text-slate-200 border border-slate-700 px-3.5 py-2.5 text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400"
                            title="Print this dashboard">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Print</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- MACRO SUMMARY KPI CARDS (100% REAL DATABASE DATA) -->
        <section aria-label="Workforce Summary KPIs" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- 1. Total Registered -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:border-slate-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-600">Total Registered</span>
                    <div class="h-9 w-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shadow-inner" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($kpis['total_registered']) }}</p>
                    <div class="mt-2.5 flex items-center justify-between text-xs text-slate-600 pt-2 border-t border-slate-100">
                        <span>North: <strong class="text-slate-900 font-bold">{{ number_format($kpis['north_registered']) }}</strong></span>
                        <span>South: <strong class="text-slate-900 font-bold">{{ number_format($kpis['south_registered']) }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- 2. Actively Seeking -->
            <div class="rounded-2xl border border-amber-200/80 bg-amber-50/40 p-5 shadow-sm hover:border-amber-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-800">Actively Seeking</span>
                    <div class="h-9 w-9 rounded-xl bg-amber-100 text-amber-900 flex items-center justify-center shadow-inner" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-3xl font-black text-amber-950 tracking-tight">{{ number_format($kpis['total_seeking']) }}</p>
                    <div class="mt-2.5 flex items-center justify-between text-xs text-amber-900 pt-2 border-t border-amber-200/60 font-medium">
                        <span>City Unemployment:</span>
                        <strong class="font-bold text-amber-950">{{ $kpis['overall_unemployment_rate'] }}%</strong>
                    </div>
                </div>
            </div>

            <!-- 3. Employed / Placed -->
            <div class="rounded-2xl border border-emerald-200/80 bg-emerald-50/40 p-5 shadow-sm hover:border-emerald-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Employed / Placed</span>
                    <div class="h-9 w-9 rounded-xl bg-emerald-100 text-emerald-900 flex items-center justify-center shadow-inner" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-3xl font-black text-emerald-950 tracking-tight">{{ number_format($kpis['total_employed']) }}</p>
                    <div class="mt-2.5 flex items-center justify-between text-xs text-emerald-900 pt-2 border-t border-emerald-200/60 font-medium">
                        <span>Placement Rate:</span>
                        <strong class="font-bold text-emerald-950">{{ $kpis['overall_employment_rate'] }}%</strong>
                    </div>
                </div>
            </div>

            <!-- 4. Vulnerable Groups -->
            <div class="rounded-2xl border border-purple-200/80 bg-purple-50/40 p-5 shadow-sm hover:border-purple-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-purple-800">Inclusivity Coverage</span>
                    <div class="h-9 w-9 rounded-xl bg-purple-100 text-purple-900 flex items-center justify-center shadow-inner" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-3xl font-black text-purple-950 tracking-tight">{{ number_format($kpis['total_pwd']) }} <span class="text-xs font-bold text-purple-800">PWDs</span></p>
                    <div class="mt-2.5 flex items-center justify-between text-xs text-purple-900 pt-2 border-t border-purple-200/60 font-medium">
                        <span>4Ps Beneficiaries:</span>
                        <strong class="font-bold text-purple-950">{{ number_format($kpis['total_4ps']) }}</strong>
                    </div>
                </div>
            </div>

        </section>

        <!-- TALENT HUBS & GEOGRAPHIC SUMMARY -->
        <section aria-label="Talent Hubs and Classifications" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            
            <!-- Real Talent Hubs -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Current Talent Hubs</h2>
                        <p class="text-xs text-slate-600">Barangays with registered candidates active in the database</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        {{ count($top10_registered) }} Active Cluster{{ count($top10_registered) === 1 ? '' : 's' }}
                    </span>
                </div>

                @if(count($top10_registered) > 0)
                    <div class="space-y-2.5 pt-1">
                        @php
                            $maxVal = max(array_column($top10_registered, 'registered') ?: [1]);
                        @endphp
                        @foreach($top10_registered as $b)
                            @php
                                $fillPct = max(10, round(($b['registered'] / $maxVal) * 100));
                            @endphp
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 hover:border-slate-300 transition-colors">
                                <div class="flex items-center justify-between text-xs mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-900 text-sm">{{ $b['name'] }}</span>
                                        <span class="text-xs text-slate-500 font-medium">({{ $b['district'] }})</span>
                                    </div>
                                    <span class="font-black text-slate-900 bg-white px-2.5 py-1 rounded-lg border border-slate-200 shadow-sm text-xs">
                                        {{ $b['registered'] }} candidate{{ $b['registered'] > 1 ? 's' : '' }}
                                    </span>
                                </div>
                                <div class="h-2 w-full rounded-full bg-slate-200 overflow-hidden">
                                    <div class="h-full rounded-full bg-emerald-600 transition-all duration-300" style="width: {{ $fillPct }}%"></div>
                                </div>
                                <div class="mt-2.5 text-xs text-slate-600 flex items-center justify-between">
                                    <span class="truncate max-w-[280px]">Top Skills: <strong class="text-slate-800 font-semibold">{{ $b['top_skill'] }}</strong></span>
                                    <span class="text-slate-500 text-xs font-medium">Zip {{ $b['zip'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-10 text-center text-slate-500 text-xs">
                        <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400 mb-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <p class="font-bold text-slate-700 text-sm">No candidate residences logged yet</p>
                        <p class="mt-1 max-w-sm mx-auto">As jobseekers complete their profile address, their barangay will appear here automatically.</p>
                    </div>
                @endif
            </div>

            <!-- Geographic & Classification Summary -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-3 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Geographic Classifications</h2>
                            <p class="text-xs text-slate-600">Cebu City municipal division breakdown</p>
                        </div>
                        <span class="text-xs font-bold text-slate-700 bg-slate-100 border border-slate-200 px-2.5 py-0.5 rounded-md">
                            80 Total Barangays
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-3">
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">District 1 (North)</span>
                            <p class="text-xl font-black text-slate-900 mt-1">46 Barangays</p>
                            <p class="text-xs text-slate-600 mt-0.5 font-medium">{{ $kpis['north_registered'] }} registered talent</p>
                        </div>

                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">District 2 (South)</span>
                            <p class="text-xl font-black text-slate-900 mt-1">34 Barangays</p>
                            <p class="text-xs text-slate-600 mt-0.5 font-medium">{{ $kpis['south_registered'] }} registered talent</p>
                        </div>

                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span>Urban / Lowland</span>
                            </span>
                            <p class="text-xl font-black text-slate-900 mt-1">54 Barangays</p>
                            <p class="text-xs text-slate-600 mt-0.5">Commercial & tech centers</p>
                        </div>

                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21l7-14 4 8 2-4 3 10"/></svg>
                                <span>Mountain / Upland</span>
                            </span>
                            <p class="text-xl font-black text-slate-900 mt-1">26 Barangays</p>
                            <p class="text-xs text-slate-600 mt-0.5">Agri & rural communities</p>
                        </div>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-blue-50/80 border border-blue-200 text-xs text-blue-950 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <svg class="h-4 w-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span class="leading-relaxed"><strong>Data Privacy Act (RA 10173):</strong> Calibrated macro view prevents unauthorized demographic harvesting.</span>
                    </div>
                    <button @click="rationaleModalOpen = true" class="text-xs font-bold text-blue-800 underline hover:text-blue-950 shrink-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 rounded">
                        View Details
                    </button>
                </div>
            </div>

        </section>

        <!-- SEARCH, FILTERS & QUICK CHIPS -->
        <section aria-label="Search and Filter Controls" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-4">
            
            <!-- Quick Filter Chips for Instant 1-Click Filtering -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mr-1">Quick Filter:</span>
                
                <a href="{{ route('admin.barangay-distribution') }}" 
                   class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 {{ empty($filters['district']) && empty($filters['terrain']) && empty($filters['talent_only']) ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    All Barangays (80)
                </a>

                <a href="{{ route('admin.barangay-distribution', array_merge($filters, ['district' => 'north', 'terrain' => '', 'talent_only' => ''])) }}" 
                   class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 {{ ($filters['district'] ?? '') === 'north' ? 'bg-emerald-700 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    North District (46)
                </a>

                <a href="{{ route('admin.barangay-distribution', array_merge($filters, ['district' => 'south', 'terrain' => '', 'talent_only' => ''])) }}" 
                   class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 {{ ($filters['district'] ?? '') === 'south' ? 'bg-blue-700 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    South District (34)
                </a>

                <a href="{{ route('admin.barangay-distribution', array_merge($filters, ['terrain' => 'urban', 'district' => '', 'talent_only' => ''])) }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 {{ ($filters['terrain'] ?? '') === 'urban' ? 'bg-slate-800 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Urban (54)</span>
                </a>

                <a href="{{ route('admin.barangay-distribution', array_merge($filters, ['terrain' => 'mountain', 'district' => '', 'talent_only' => ''])) }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 {{ ($filters['terrain'] ?? '') === 'mountain' ? 'bg-amber-700 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21l7-14 4 8 2-4 3 10"/></svg>
                    <span>Mountain (26)</span>
                </a>

                <a href="{{ route('admin.barangay-distribution', array_merge($filters, ['talent_only' => '1'])) }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 {{ !empty($filters['talent_only']) ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-900 border border-emerald-200 hover:bg-emerald-100' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>With Registered Talent</span>
                </a>

                @if(!empty($filters['search']) || !empty($filters['district']) || !empty($filters['terrain']) || !empty($filters['talent_only']) || !empty($filters['sort']))
                    <a href="{{ route('admin.barangay-distribution') }}" class="text-xs font-bold text-rose-600 hover:text-rose-800 hover:underline ml-auto flex items-center gap-1.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 rounded">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Clear All Filters</span>
                    </a>
                @endif
            </div>

            <!-- Search Input and Dropdown Controls -->
            <form action="{{ route('admin.barangay-distribution') }}" method="GET" class="pt-3 border-t border-slate-100 space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                    
                    <!-- Search Input -->
                    <div class="lg:col-span-5 space-y-1">
                        <label for="filter-search" class="text-xs font-bold text-slate-700">Search by Name or Skill</label>
                        <div class="relative">
                            <input id="filter-search"
                                   type="text" 
                                   name="search" 
                                   value="{{ $filters['search'] ?? '' }}" 
                                   placeholder="Type barangay name or skill..." 
                                   class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:border-emerald-600 focus:outline-none focus:ring-1 focus:ring-emerald-600 transition-colors">
                            @if(!empty($filters['search']))
                                <a href="{{ route('admin.barangay-distribution', array_merge($filters, ['search' => ''])) }}" 
                                   class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded"
                                   aria-label="Clear search input">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- District Filter -->
                    <div class="lg:col-span-3 space-y-1">
                        <label for="filter-district" class="text-xs font-bold text-slate-700">District Filter</label>
                        <select id="filter-district" name="district" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-xs text-slate-900 focus:bg-white focus:border-emerald-600 focus:outline-none focus:ring-1 focus:ring-emerald-600 transition-colors">
                            <option value="">All Districts (80)</option>
                            <option value="north" {{ ($filters['district'] ?? '') === 'north' ? 'selected' : '' }}>District 1 (North - 46)</option>
                            <option value="south" {{ ($filters['district'] ?? '') === 'south' ? 'selected' : '' }}>District 2 (South - 34)</option>
                        </select>
                    </div>

                    <!-- Terrain Filter -->
                    <div class="lg:col-span-2 space-y-1">
                        <label for="filter-terrain" class="text-xs font-bold text-slate-700">Terrain Filter</label>
                        <select id="filter-terrain" name="terrain" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-xs text-slate-900 focus:bg-white focus:border-emerald-600 focus:outline-none focus:ring-1 focus:ring-emerald-600 transition-colors">
                            <option value="">All Terrains</option>
                            <option value="urban" {{ ($filters['terrain'] ?? '') === 'urban' ? 'selected' : '' }}>Urban / Lowland</option>
                            <option value="mountain" {{ ($filters['terrain'] ?? '') === 'mountain' ? 'selected' : '' }}>Mountain / Upland</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="lg:col-span-2 flex items-center gap-2">
                        <button type="submit" class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-emerald-600 active:scale-95 text-white text-xs font-bold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                            Apply
                        </button>
                        <a href="{{ route('admin.barangay-distribution') }}" class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-700 text-xs font-bold transition-all text-center focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-500" title="Reset Filters">
                            Reset
                        </a>
                    </div>

                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between text-xs text-slate-600 pt-2 gap-2 border-t border-slate-100">
                    @if($items instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <span>Showing <strong class="text-slate-900 font-bold">{{ $items->total() > 0 ? "{$items->firstItem()} – {$items->lastItem()}" : '0' }}</strong> of <strong class="text-slate-900 font-bold">{{ $items->total() }}</strong> Cebu City Barangays (Page {{ $items->currentPage() }} of {{ $items->lastPage() }})</span>
                    @else
                        <span>Showing <strong class="text-slate-900 font-bold">{{ count($items) }}</strong> of <strong class="text-slate-900 font-bold">{{ $total_barangays }}</strong> Cebu City Barangays</span>
                    @endif
                    
                    <div class="flex items-center gap-2 self-end sm:self-auto">
                        <label for="filter-sort" class="text-xs text-slate-600 font-semibold">Sort by:</label>
                        <select id="filter-sort" name="sort" onchange="this.form.submit()" class="rounded-lg border border-slate-300 bg-slate-50 px-2.5 py-1 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-emerald-600">
                            <option value="registered_desc" {{ ($filters['sort'] ?? '') === 'registered_desc' ? 'selected' : '' }}>Workforce (High to Low)</option>
                            <option value="registered_asc" {{ ($filters['sort'] ?? '') === 'registered_asc' ? 'selected' : '' }}>Workforce (Low to High)</option>
                            <option value="name_asc" {{ ($filters['sort'] ?? '') === 'name_asc' ? 'selected' : '' }}>Barangay Name (A - Z)</option>
                            <option value="seeking_desc" {{ ($filters['sort'] ?? '') === 'seeking_desc' ? 'selected' : '' }}>Seeking Work (High to Low)</option>
                        </select>
                    </div>
                </div>
            </form>
        </section>

        <!-- MASTER BARANGAY DATA TABLE -->
        <section aria-label="Cebu City Barangay Labor Matrix" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Cebu City Barangay Labor Matrix</h2>
                    <p class="text-xs text-slate-600">Live database counts for all municipal barangays (20 per page)</p>
                </div>
                <span class="text-xs font-bold text-slate-700 bg-white border border-slate-200 px-3 py-1 rounded-full shadow-sm">
                    {{ $items instanceof \Illuminate\Pagination\LengthAwarePaginator ? $items->total() : count($items) }} Locations Listed
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs" role="table">
                    <thead class="bg-slate-100/90 text-xs font-bold uppercase tracking-wider text-slate-700 border-b border-slate-200">
                        <tr>
                            <th scope="col" class="py-3.5 px-4">Barangay Name</th>
                            <th scope="col" class="py-3.5 px-3">District</th>
                            <th scope="col" class="py-3.5 px-3">Terrain</th>
                            <th scope="col" class="py-3.5 px-3 text-center">Registered</th>
                            <th scope="col" class="py-3.5 px-3 text-center">Seeking</th>
                            <th scope="col" class="py-3.5 px-3 text-center">Employed</th>
                            <th scope="col" class="py-3.5 px-3 text-center">Unemployed %</th>
                            <th scope="col" class="py-3.5 px-3 text-center">PWD / 4Ps</th>
                            <th scope="col" class="py-3.5 px-4">Top Recorded Skills</th>
                            <th scope="col" class="py-3.5 px-3 text-center">DMDP Priority</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($items as $b)
                            <tr class="hover:bg-slate-50 transition-colors {{ $b['registered'] > 0 ? 'bg-emerald-50/30' : '' }}">
                                
                                <!-- Barangay Name -->
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    <div class="flex items-center gap-2.5">
                                        <div class="h-8 w-8 rounded-lg {{ $b['is_north'] ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }} flex items-center justify-center font-black text-xs shrink-0 shadow-sm" aria-hidden="true">
                                            {{ substr($b['name'], 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-900 text-sm leading-tight block">{{ $b['name'] }}</span>
                                            <span class="text-xs text-slate-500 font-normal">Zip {{ $b['zip'] }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- District -->
                                <td class="py-3.5 px-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold {{ $b['is_north'] ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-blue-50 text-blue-800 border border-blue-200' }}">
                                        {{ $b['district'] }}
                                    </span>
                                </td>

                                <!-- Terrain -->
                                <td class="py-3.5 px-3 text-slate-700">
                                    <span class="inline-flex items-center gap-1.5 text-xs">
                                        @if($b['is_mountain'])
                                            <svg class="w-3.5 h-3.5 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21l7-14 4 8 2-4 3 10"/></svg>
                                        @else
                                            <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        @endif
                                        <span>{{ $b['terrain'] }}</span>
                                    </span>
                                </td>

                                <!-- Registered Talent -->
                                <td class="py-3.5 px-3 text-center">
                                    @if($b['registered'] > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-900 border border-emerald-300">
                                            {{ $b['registered'] }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 font-normal">0</span>
                                    @endif
                                </td>

                                <!-- Actively Seeking -->
                                <td class="py-3.5 px-3 text-center">
                                    @if($b['seeking'] > 0)
                                        <span class="font-bold text-amber-900 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">{{ $b['seeking'] }}</span>
                                    @else
                                        <span class="text-slate-400 font-normal">0</span>
                                    @endif
                                </td>

                                <!-- Employed -->
                                <td class="py-3.5 px-3 text-center">
                                    @if($b['employed'] > 0)
                                        <span class="font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">{{ $b['employed'] }}</span>
                                    @else
                                        <span class="text-slate-400 font-normal">0</span>
                                    @endif
                                </td>

                                <!-- Unemployment % -->
                                <td class="py-3.5 px-3 text-center">
                                    @if($b['registered'] > 0)
                                        <span class="font-bold {{ $b['unemployment_rate'] > 0 ? 'text-amber-900' : 'text-slate-700' }}">
                                            {{ $b['unemployment_rate'] }}%
                                        </span>
                                    @else
                                        <span class="text-slate-400 font-normal">-</span>
                                    @endif
                                </td>

                                <!-- PWD / 4Ps -->
                                <td class="py-3.5 px-3 text-center text-xs">
                                    @if($b['pwd_count'] > 0 || $b['four_ps_count'] > 0)
                                        <span class="font-bold text-purple-800">{{ $b['pwd_count'] }} PWD</span>
                                        <span class="text-slate-400">/</span>
                                        <span class="font-bold text-slate-700">{{ $b['four_ps_count'] }} 4Ps</span>
                                    @else
                                        <span class="text-slate-400 font-normal">-</span>
                                    @endif
                                </td>

                                <!-- Skills -->
                                <td class="py-3.5 px-4">
                                    @if(!empty($b['skills_array']) && count($b['skills_array']) > 0)
                                        <div class="flex flex-wrap gap-1 max-w-[220px]">
                                            @foreach($b['skills_array'] as $skill)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                                                    {{ $skill }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-xs italic">
                                            None recorded
                                        </span>
                                    @endif
                                </td>

                                <!-- Priority -->
                                <td class="py-3.5 px-3 text-center">
                                    @if($b['priority'] === 'High')
                                        <span class="px-2.5 py-1 rounded-md text-xs font-bold uppercase bg-rose-50 text-rose-800 border border-rose-200">
                                            High
                                        </span>
                                    @elseif($b['priority'] === 'Moderate')
                                        <span class="px-2.5 py-1 rounded-md text-xs font-bold uppercase bg-amber-50 text-amber-900 border border-amber-200">
                                            Moderate
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-md text-xs font-medium text-slate-600 bg-slate-100 border border-slate-200">
                                            Normal
                                        </span>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-14 text-center text-slate-500">
                                    <div class="max-w-sm mx-auto space-y-3">
                                        <div class="h-12 w-12 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto text-slate-400" aria-hidden="true">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                        </div>
                                        <p class="font-bold text-slate-800 text-base">No barangays match the search criteria</p>
                                        <p class="text-xs text-slate-500 leading-relaxed">Try adjusting your keyword filter or clearing current district filters.</p>
                                        <a href="{{ route('admin.barangay-distribution') }}" class="inline-block mt-2 px-4 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-emerald-600 transition-colors shadow-md">
                                            Reset All Filters
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/60 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-600 gap-2">
                <span>Displaying <strong class="text-slate-900 font-bold">{{ $items instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($items->total() > 0 ? "{$items->firstItem()} – {$items->lastItem()}" : '0') : count($items) }}</strong> of <strong class="text-slate-900 font-bold">{{ $items instanceof \Illuminate\Pagination\LengthAwarePaginator ? $items->total() : count($items) }}</strong> records &bull; Live database synchronization</span>
                <span class="font-medium text-slate-500">DMDP Cebu City Administration</span>
            </div>

            @if($items instanceof \Illuminate\Pagination\LengthAwarePaginator && $items->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-white">
                    {{ $items->links() }}
                </div>
            @endif
        </section>

    </div>

    <!-- CAPSTONE DEFENSE RATIONALE & POLICY MODAL -->
    <div x-show="rationaleModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
         @keydown.escape.window="rationaleModalOpen = false"
         role="dialog"
         aria-modal="true"
         aria-labelledby="modal-policy-title">
        
        <!-- Backdrop -->
        <div x-show="rationaleModalOpen" 
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="rationaleModalOpen = false" 
             class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm"></div>

        <!-- Modal Dialog -->
        <div x-show="rationaleModalOpen" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative max-w-3xl w-full max-h-[85vh] bg-white rounded-2xl shadow-2xl border border-slate-200 flex flex-col overflow-hidden z-10">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between border-b border-slate-800">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">TrabaGo Governance & Policy</span>
                    <h2 id="modal-policy-title" class="text-base sm:text-lg font-black text-white">Barangay Distribution Access Control & RA 10173</h2>
                </div>
                <button @click="rationaleModalOpen = false" 
                        type="button"
                        class="p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400"
                        aria-label="Close dialog">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Content (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-6 space-y-4 text-slate-700 text-xs sm:text-sm leading-relaxed">
                
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                    <span class="text-[10px] font-black uppercase text-slate-500 tracking-wider">Capstone Study Title</span>
                    <p class="font-extrabold text-slate-900 mt-1 leading-snug">
                        TrabaGo: A Web- and Mobile-Based Integrated Manpower Referral, Training, and Employer Accreditation Management System for Job Seekers of the Department of Manpower Development and Placement in Cebu City
                    </p>
                </div>

                <div class="space-y-1.5 text-xs">
                    <h3 class="font-bold text-slate-900 text-sm">1. Executive Summary & Core Principle</h3>
                    <p class="text-slate-600 leading-relaxed">
                        In the TrabaGo platform, the Barangay Distribution module provides city-wide macro-level visualization across Cebu City’s 80 barangays. Limiting access primarily to the System Administrator / DMDP Director level is based on the <strong>Principle of Least Privilege (PoLP)</strong>, <strong>Role-Based Access Control (RBAC)</strong> architecture, and compliance with the <strong>Philippine Data Privacy Act of 2012 (RA 10173)</strong>.
                    </p>
                </div>

                <div class="space-y-2 text-xs">
                    <h3 class="font-bold text-slate-900 text-sm">2. Strategic Policy vs. Operational Execution</h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-100 font-bold text-slate-700">
                                <tr>
                                    <th class="p-2.5">Role</th>
                                    <th class="p-2.5">Scope</th>
                                    <th class="p-2.5">Access Rationale</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr class="bg-emerald-50/30">
                                    <td class="p-2.5 font-bold text-slate-900">System Admin / DMDP Executive</td>
                                    <td class="p-2.5 text-emerald-800 font-semibold">Macro Strategic</td>
                                    <td class="p-2.5 text-slate-600">Allocating municipal training budgets, submitting quarterly City Council reports, and deploying mobile facilities.</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 font-bold text-slate-900">Job Placement Officer (JPO)</td>
                                    <td class="p-2.5 text-blue-800 font-semibold">Micro Operational</td>
                                    <td class="p-2.5 text-slate-600">Conducts candidate evaluations and referrals per individual without needing comparative citywide budget control.</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 font-bold text-slate-900">Accredited Employer</td>
                                    <td class="p-2.5 text-amber-800 font-semibold">Recruitment</td>
                                    <td class="p-2.5 text-slate-600">Restricted to candidate skills and qualifications to eliminate geographic hiring discrimination across barangays.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-blue-50/80 border border-blue-200 text-xs space-y-1">
                    <h4 class="font-bold text-blue-950">RA 10173 Data Minimization Compliance</h4>
                    <p class="text-blue-900 leading-relaxed">
                        Section 11 mandates that personal and demographic data collected must be adequate, relevant, and not excessive. Restricting high-level demographic indexes prevents unauthorized data harvesting and protects vulnerable barangay populations.
                    </p>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3 bg-slate-50 border-t border-slate-200 flex justify-end">
                <button @click="rationaleModalOpen = false" 
                        type="button"
                        class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-500">
                    Close Guide
                </button>
            </div>

        </div>
    </div>

</div>
@endsection
