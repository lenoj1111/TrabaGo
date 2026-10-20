@extends('layouts.jpo')

@section('title', 'Barangay Distribution (BESD Coordination) - Placement Officer')

@section('content')
<div class="min-h-screen bg-slate-50/70 px-4 py-6 sm:px-6 lg:px-8">
    <div class="w-full max-w-[1680px] mx-auto space-y-6">
        
        <!-- HEADER HERO -->
        <header class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-slate-900 to-emerald-950 p-6 sm:p-8 text-white shadow-lg border border-slate-800">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-semibold text-emerald-300 border border-emerald-500/30">
                            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            JPO Operational Referral Console
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-300 border border-slate-700">
                            80 Official Barangays (BESD Desks)
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-500/20 px-3 py-1 text-xs font-semibold text-blue-300 border border-blue-500/30">
                            RA 10173 Read-Only Scope
                        </span>
                    </div>
                    
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                        Barangay Distribution & BESD Coordination
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                        Operational summary view calibrated for organizing localized <strong>Barangay Job Fairs</strong>, coordinating with <strong>Barangay Employment Service Desks (BESD)</strong>, and targeting active candidate referrals across Cebu City.
                    </p>
                </div>

                <!-- Action Button linking to Referral Desk -->
                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <a href="{{ route('jpo.evaluations.jobseekers') }}" 
                       class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white px-4 py-2.5 text-xs font-bold shadow-md shadow-emerald-950/40 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>Evaluate Jobseekers Desk</span>
                    </a>
                </div>
            </div>
        </header>

        <!-- OPERATIONAL PRIVACY & RBAC CALLOUT -->
        <div class="rounded-2xl border border-blue-200 bg-blue-50/80 p-4 sm:p-5 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 text-xs">
            <div class="flex items-start gap-3">
                <div class="h-8 w-8 rounded-lg bg-blue-600 text-white flex items-center justify-center shrink-0 text-sm shadow-sm font-bold" aria-hidden="true">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-blue-950">Principle of Least Privilege (PoLP) & RA 10173 Compliance</h2>
                    <p class="text-xs text-blue-900 mt-0.5 leading-relaxed">
                        As a Job Placement Officer, your access is calibrated for <strong>micro-level referral matching</strong> and BESD coordination. In compliance with Data Minimization standards, administrative data export privileges and municipal budget planning controls remain exclusive to the DMDP Directorate.
                    </p>
                </div>
            </div>
            <span class="shrink-0 text-xs font-bold text-blue-800 bg-white px-3 py-1.5 rounded-xl border border-blue-200 shadow-sm">
                Scope: Operational Read-Only
            </span>
        </div>

        <!-- OPERATIONAL KPI CARDS (100% REAL DATABASE DATA) -->
        <section aria-label="Operational Workforce KPIs" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- 1. Total Active Candidates Seeking Jobs -->
            <div class="rounded-2xl border border-amber-200/80 bg-amber-50/40 p-5 shadow-sm hover:border-amber-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-800">Active Candidates Seeking Work</span>
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

            <!-- 2. Total Candidate Pool across 80 Barangays -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:border-slate-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-600">Total Registered Talent Pool</span>
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

            <!-- 3. Inclusive Placement Candidates (PWD & 4Ps) -->
            <div class="rounded-2xl border border-purple-200/80 bg-purple-50/40 p-5 shadow-sm hover:border-purple-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-purple-800">Inclusivity Matching</span>
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

            <!-- 4. Placement Rate -->
            <div class="rounded-2xl border border-emerald-200/80 bg-emerald-50/40 p-5 shadow-sm hover:border-emerald-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Placed in Employment</span>
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

        </section>

        <!-- SEARCH, FILTERS & QUICK CHIPS -->
        <section aria-label="Search and Filter Controls" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-4">
            
            <!-- Quick Filter Chips for Instant 1-Click Filtering -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mr-1">Quick Filter:</span>
                
                <a href="{{ route('jpo.barangay-distribution') }}" 
                   class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 {{ empty($filters['district']) && empty($filters['terrain']) && empty($filters['talent_only']) ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    All Barangays (80)
                </a>

                <a href="{{ route('jpo.barangay-distribution', array_merge($filters, ['district' => 'north', 'terrain' => '', 'talent_only' => ''])) }}" 
                   class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 {{ ($filters['district'] ?? '') === 'north' ? 'bg-emerald-700 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    North District (46)
                </a>

                <a href="{{ route('jpo.barangay-distribution', array_merge($filters, ['district' => 'south', 'terrain' => '', 'talent_only' => ''])) }}" 
                   class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 {{ ($filters['district'] ?? '') === 'south' ? 'bg-blue-700 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    South District (34)
                </a>

                <a href="{{ route('jpo.barangay-distribution', array_merge($filters, ['terrain' => 'urban', 'district' => '', 'talent_only' => ''])) }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 {{ ($filters['terrain'] ?? '') === 'urban' ? 'bg-slate-800 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Urban (54)</span>
                </a>

                <a href="{{ route('jpo.barangay-distribution', array_merge($filters, ['terrain' => 'mountain', 'district' => '', 'talent_only' => ''])) }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 {{ ($filters['terrain'] ?? '') === 'mountain' ? 'bg-amber-700 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21l7-14 4 8 2-4 3 10"/></svg>
                    <span>Mountain (26)</span>
                </a>

                <a href="{{ route('jpo.barangay-distribution', array_merge($filters, ['talent_only' => '1'])) }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 {{ !empty($filters['talent_only']) ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-900 border border-emerald-200 hover:bg-emerald-100' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>With Active Candidates</span>
                </a>

                @if(!empty($filters['search']) || !empty($filters['district']) || !empty($filters['terrain']) || !empty($filters['talent_only']) || !empty($filters['sort']))
                    <a href="{{ route('jpo.barangay-distribution') }}" class="text-xs font-bold text-rose-600 hover:text-rose-800 hover:underline ml-auto flex items-center gap-1.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 rounded">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Clear All Filters</span>
                    </a>
                @endif
            </div>

            <!-- Search Input and Dropdown Controls -->
            <form action="{{ route('jpo.barangay-distribution') }}" method="GET" class="pt-3 border-t border-slate-100 space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                    
                    <!-- Search input -->
                    <div class="lg:col-span-5 space-y-1">
                        <label for="jpo-filter-search" class="text-xs font-bold text-slate-700">Search by Name or Skill</label>
                        <div class="relative">
                            <input id="jpo-filter-search"
                                   type="text" 
                                   name="search" 
                                   value="{{ $filters['search'] ?? '' }}" 
                                   placeholder="Search barangay name or candidate skills..." 
                                   class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:border-emerald-600 focus:outline-none focus:ring-1 focus:ring-emerald-600 transition-colors">
                            @if(!empty($filters['search']))
                                <a href="{{ route('jpo.barangay-distribution', array_merge($filters, ['search' => ''])) }}" 
                                   class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded"
                                   aria-label="Clear search input">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- District Filter -->
                    <div class="lg:col-span-3 space-y-1">
                        <label for="jpo-filter-district" class="text-xs font-bold text-slate-700">District Filter</label>
                        <select id="jpo-filter-district" name="district" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-xs text-slate-900 focus:bg-white focus:border-emerald-600 focus:outline-none focus:ring-1 focus:ring-emerald-600 transition-colors">
                            <option value="">All Districts (80)</option>
                            <option value="north" {{ ($filters['district'] ?? '') === 'north' ? 'selected' : '' }}>District 1 (North - 46)</option>
                            <option value="south" {{ ($filters['district'] ?? '') === 'south' ? 'selected' : '' }}>District 2 (South - 34)</option>
                        </select>
                    </div>

                    <!-- Terrain Filter -->
                    <div class="lg:col-span-2 space-y-1">
                        <label for="jpo-filter-terrain" class="text-xs font-bold text-slate-700">Terrain Filter</label>
                        <select id="jpo-filter-terrain" name="terrain" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-xs text-slate-900 focus:bg-white focus:border-emerald-600 focus:outline-none focus:ring-1 focus:ring-emerald-600 transition-colors">
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
                        <a href="{{ route('jpo.barangay-distribution') }}" class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-700 text-xs font-bold transition-all text-center focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-500" title="Reset Filters">
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
                        <label for="jpo-filter-sort" class="text-xs text-slate-600 font-semibold">Sort by:</label>
                        <select id="jpo-filter-sort" name="sort" onchange="this.form.submit()" class="rounded-lg border border-slate-300 bg-slate-50 px-2.5 py-1 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-emerald-600">
                            <option value="seeking_desc" {{ ($filters['sort'] ?? '') === 'seeking_desc' ? 'selected' : '' }}>Most Active Seekers (Priority)</option>
                            <option value="registered_desc" {{ ($filters['sort'] ?? '') === 'registered_desc' ? 'selected' : '' }}>Workforce Pool (High to Low)</option>
                            <option value="name_asc" {{ ($filters['sort'] ?? '') === 'name_asc' ? 'selected' : '' }}>Barangay Name (A - Z)</option>
                            <option value="pwd_desc" {{ ($filters['sort'] ?? '') === 'pwd_desc' ? 'selected' : '' }}>PWD Seekers (High to Low)</option>
                        </select>
                    </div>
                </div>
            </form>
        </section>

        <!-- OPERATIONAL BESD TABLE -->
        <section aria-label="BESD Referral Planning Matrix" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/60">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">BESD Referral & Job Fair Planning Matrix</h2>
                    <p class="text-xs text-slate-600">Barangay candidate supply density for localized job matching and interview schedules (20 per page)</p>
                </div>
                <span class="text-xs font-bold text-slate-700 bg-white border border-slate-200 px-3 py-1 rounded-full shadow-sm">
                    {{ $items instanceof \Illuminate\Pagination\LengthAwarePaginator ? $items->total() : count($items) }} Barangays Listed
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs" role="table">
                    <thead class="bg-slate-100/90 text-xs font-bold uppercase tracking-wider text-slate-700 border-b border-slate-200">
                        <tr>
                            <th scope="col" class="py-3.5 px-4">Barangay Name</th>
                            <th scope="col" class="py-3.5 px-3">District</th>
                            <th scope="col" class="py-3.5 px-3 text-center">Available Seekers</th>
                            <th scope="col" class="py-3.5 px-3 text-center">Total Pool</th>
                            <th scope="col" class="py-3.5 px-3 text-center">PWD Seekers</th>
                            <th scope="col" class="py-3.5 px-4">Top Candidate Skills</th>
                            <th scope="col" class="py-3.5 px-3">BESD Recommendation</th>
                            <th scope="col" class="py-3.5 px-4 text-center">Referral Action</th>
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
                                            <span class="text-xs text-slate-500 font-normal">{{ $b['terrain'] }} &bull; Zip {{ $b['zip'] }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- District -->
                                <td class="py-3.5 px-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold {{ $b['is_north'] ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-blue-50 text-blue-800 border border-blue-200' }}">
                                        {{ $b['district'] }}
                                    </span>
                                </td>

                                <!-- Available Seekers -->
                                <td class="py-3.5 px-3 text-center">
                                    @if($b['seeking'] > 0)
                                        <span class="font-bold text-amber-900 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">{{ $b['seeking'] }}</span>
                                    @else
                                        <span class="text-slate-400 font-normal">0</span>
                                    @endif
                                </td>

                                <!-- Total Pool -->
                                <td class="py-3.5 px-3 text-center">
                                    @if($b['registered'] > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-900 border border-emerald-300">
                                            {{ $b['registered'] }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 font-normal">0</span>
                                    @endif
                                </td>

                                <!-- PWD Seekers -->
                                <td class="py-3.5 px-3 text-center text-xs">
                                    @if($b['pwd_count'] > 0)
                                        <span class="px-2 py-0.5 rounded-md text-xs font-bold bg-purple-50 text-purple-800 border border-purple-200">
                                            {{ $b['pwd_count'] }} PWD
                                        </span>
                                    @else
                                        <span class="text-slate-400 font-normal">0</span>
                                    @endif
                                </td>

                                <!-- Top Skills -->
                                <td class="py-3.5 px-4">
                                    @if(!empty($b['skills_array']) && count($b['skills_array']) > 0)
                                        <div class="flex flex-wrap gap-1 max-w-[200px]">
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

                                <!-- Operational Recommendation -->
                                <td class="py-3.5 px-3">
                                    @if($b['seeking'] >= 5)
                                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-rose-800">
                                            <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span>Schedule BESD Job Fair</span>
                                        </span>
                                    @elseif($b['is_mountain'] && $b['seeking'] > 0)
                                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-800">
                                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                            <span>Mobile Outreach & Matching</span>
                                        </span>
                                    @elseif($b['seeking'] > 0)
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-800">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            <span>Direct Vacancy Endorsement</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span>On-Demand Referral</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Referral Link -->
                                <td class="py-3.5 px-4 text-center">
                                    <a href="{{ route('jpo.evaluations.jobseekers', ['search' => $b['name']]) }}" 
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-emerald-600 active:scale-95 text-white font-bold text-xs transition-all shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                                        <span>Refer</span> &rarr;
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-14 text-center text-slate-500">
                                    <div class="max-w-sm mx-auto space-y-3">
                                        <div class="h-12 w-12 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto text-slate-400" aria-hidden="true">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                        </div>
                                        <p class="font-bold text-slate-800 text-base">No barangays match the search criteria</p>
                                        <p class="text-xs text-slate-500 leading-relaxed">Try adjusting your keyword filter or clearing current district filters.</p>
                                        <a href="{{ route('jpo.barangay-distribution') }}" class="inline-block mt-2 px-4 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-emerald-600 transition-colors shadow-md">
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
                <span class="font-medium text-slate-500">DMDP JPO Operations</span>
            </div>

            @if($items instanceof \Illuminate\Pagination\LengthAwarePaginator && $items->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-white">
                    {{ $items->links() }}
                </div>
            @endif
        </section>

    </div>
</div>
@endsection
