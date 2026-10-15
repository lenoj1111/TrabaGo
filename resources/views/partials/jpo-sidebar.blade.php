<!-- Sidebar Header -->
<div class="h-16 shrink-0 px-6 flex items-center justify-between border-b border-slate-800 bg-slate-900">
    <a href="{{ route('jpo.dashboard') }}" class="flex items-center gap-3 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 rounded-lg">
        <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white font-black text-sm shadow-md shadow-emerald-950/40 group-hover:scale-105 transition-transform">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <div class="flex flex-col">
            <span class="font-extrabold text-base text-white tracking-tight leading-none group-hover:text-emerald-400 transition-colors">
                Traba<span class="text-emerald-400">Go</span>
            </span>
            <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase leading-none mt-1">Placement Officer</span>
        </div>
    </a>
    
    @if(!empty($isMobile))
        <!-- Close Button for Mobile Drawer -->
        <button @click="sidebarOpen = false" type="button" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400" title="Close Menu (Esc)">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    @endif
</div>

<!-- Scrollable Navigation Tabs List -->
<div class="flex-1 overflow-y-auto px-4 py-4 space-y-5">
    
    <!-- Group: OVERVIEW -->
    <div class="space-y-1">
        <p class="px-3 text-xs font-bold uppercase tracking-wider text-slate-400">Overview</p>
        
        <!-- Dashboard Tab -->
        <a href="{{ route('jpo.dashboard') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('jpo.dashboard*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('jpo.dashboard*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </div>
        </a>
    </div>

    <!-- Group: EVALUATIONS & AUDITS -->
    <div class="space-y-1">
        <p class="px-3 text-xs font-bold uppercase tracking-wider text-slate-400">Evaluations & Audits</p>
        
        <!-- Evaluate Jobseekers Tab -->
        <a href="{{ route('jpo.evaluations.jobseekers') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('jpo.evaluations.jobseekers*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('jpo.evaluations.jobseekers*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>Evaluate Jobseekers</span>
            </div>
        </a>

        <!-- Accreditation Papers Tab -->
        <a href="{{ route('jpo.evaluations.accreditations') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('jpo.evaluations.accreditations*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('jpo.evaluations.accreditations*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Accreditation Papers</span>
            </div>
            @if($pendingAccreditationsCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ request()->routeIs('jpo.evaluations.accreditations*') ? 'bg-white text-emerald-900' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' }}">
                    {{ $pendingAccreditationsCount }}
                </span>
            @endif
        </a>

        <!-- Placement Reports Tab -->
        <a href="{{ route('jpo.evaluations.placement-reports') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('jpo.evaluations.placement-reports*') || request()->routeIs('jpo.placement-reports*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('jpo.evaluations.placement-reports*') || request()->routeIs('jpo.placement-reports*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Placement Reports</span>
            </div>
        </a>

        <!-- Barangay Distribution (BESD) Tab -->
        <a href="{{ route('jpo.barangay-distribution') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('jpo.barangay-distribution*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('jpo.barangay-distribution*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Barangay Distribution</span>
            </div>
            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold {{ request()->routeIs('jpo.barangay-distribution*') ? 'bg-emerald-800 text-emerald-100' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' }}">
                BESD
            </span>
        </a>
    </div>

    <!-- Group: ACCOUNT & SYSTEM -->
    <div class="space-y-1">
        <p class="px-3 text-xs font-bold uppercase tracking-wider text-slate-400">Account & System</p>
        
        <!-- Notifications Tab -->
        <a href="{{ route('jpo.notifications') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('jpo.notifications*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('jpo.notifications*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span>Notifications</span>
            </div>
            @if($jpoUnreadCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-600 text-white">
                    {{ $jpoUnreadCount > 9 ? '9+' : $jpoUnreadCount }}
                </span>
            @endif
        </a>

        <!-- Officer Profile Tab -->
        <a href="{{ route('jpo.profile') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('jpo.profile*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('jpo.profile*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Officer Profile</span>
            </div>
        </a>
    </div>

</div>

<!-- Sidebar Bottom: Officer User Info & Logout -->
<div class="p-3 border-t border-slate-800 bg-slate-900 mt-auto shrink-0">
    <div class="flex items-center justify-between gap-2 p-2 rounded-xl bg-slate-800/70 border border-slate-700/60">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="h-8 w-8 rounded-lg bg-emerald-600 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-sm">
                JPO
            </div>
            <div class="flex flex-col min-w-0">
                <span class="text-xs font-bold text-white truncate max-w-[130px]">{{ Auth::user()->email ?? 'jpo@trabago.com' }}</span>
                <span class="text-[10px] text-slate-400 leading-tight">Placement Officer</span>
            </div>
        </div>
        
        <form action="{{ route('logout') }}" method="POST" class="shrink-0">
            @csrf
            <button type="submit" title="Log Out" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-700 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </button>
        </form>
    </div>
</div>
