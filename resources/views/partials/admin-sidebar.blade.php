<!-- Sidebar Header -->
<div class="h-16 shrink-0 px-6 flex items-center justify-between border-b border-slate-800 bg-slate-900">
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 rounded-lg">
        <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white font-black text-sm shadow-md shadow-emerald-950/40 group-hover:scale-105 transition-transform">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>
        <div class="flex flex-col">
            <span class="font-extrabold text-base text-white tracking-tight leading-none group-hover:text-emerald-400 transition-colors">
                Traba<span class="text-emerald-400">Go</span>
            </span>
            <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase leading-none mt-1">Admin Command</span>
        </div>
    </a>
    
    @if(!empty($isMobile))
        <!-- Close Button for Mobile -->
        <button @click="sidebarOpen = false" type="button" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400" title="Close Menu (Esc)">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    @endif
</div>

<!-- Scrollable Navigation Tabs List -->
<div class="flex-1 overflow-y-auto px-4 py-4 space-y-5">
    
    <!-- Group: MAIN OVERVIEW -->
    <div class="space-y-1">
        <p class="px-3 text-xs font-bold uppercase tracking-wider text-slate-400">Overview</p>
        
        <!-- Dashboard Tab -->
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </div>
        </a>

        <!-- Manage Approvals Tab -->
        <a href="{{ route('admin.approvals.index') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('admin.approvals*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('admin.approvals*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Manage Approvals</span>
            </div>
            @if($pendingApprovalsCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ request()->routeIs('admin.approvals*') ? 'bg-white text-emerald-900' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' }}">
                    {{ $pendingApprovalsCount }}
                </span>
            @endif
        </a>
    </div>

    <!-- Group: DIRECTORY & USERS -->
    <div class="space-y-1">
        <p class="px-3 text-xs font-bold uppercase tracking-wider text-slate-400">Directory & Access</p>
        
        <!-- Employee Accounts Tab -->
        <a href="{{ route('admin.users.index') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('admin.users*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('admin.users*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span>Employee Accounts</span>
            </div>
        </a>

        <!-- Jobseekers Tab -->
        <a href="{{ route('admin.jobseekers.index') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('admin.jobseekers*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('admin.jobseekers*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>Jobseekers</span>
            </div>
        </a>
    </div>

    <!-- Group: EMPLOYMENT & JOBS -->
    <div class="space-y-1">
        <p class="px-3 text-xs font-bold uppercase tracking-wider text-slate-400">Employment Hub</p>
        
        <!-- Job Postings Tab -->
        <a href="{{ route('admin.job-postings') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('admin.job-postings*') || request()->routeIs('admin.job-postings-list*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('admin.job-postings*') || request()->routeIs('admin.job-postings-list*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span>Job Postings</span>
            </div>
        </a>

        <!-- Employers Tab -->
        <a href="{{ route('admin.employers') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('admin.employers*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('admin.employers*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>Employers</span>
            </div>
        </a>
    </div>

    <!-- Group: REPORTS & ANALYTICS -->
    <div class="space-y-1">
        <p class="px-3 text-xs font-bold uppercase tracking-wider text-slate-400">Reports & Insights</p>
        
        <!-- Placement Reports Tab -->
        <a href="{{ route('admin.placement-reports.index') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('admin.placement-reports*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('admin.placement-reports*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Placement Reports</span>
            </div>
        </a>


        <!-- Barangay Distribution Tab -->
        <a href="{{ route('admin.barangay-distribution') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('admin.barangay-distribution*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('admin.barangay-distribution*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Barangay Distribution</span>
            </div>
        </a>

        <!-- Platform Reports Tab -->
        <a href="{{ route('admin.reports') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('admin.reports*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('admin.reports*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                </svg>
                <span>Platform Reports</span>
            </div>
        </a>
    </div>

    <!-- Group: SYSTEM & SETTINGS -->
    <div class="space-y-1">
        <p class="px-3 text-xs font-bold uppercase tracking-wider text-slate-400">Account & System</p>
        
        <!-- Notifications Tab -->
        <a href="{{ route('admin.notifications') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('admin.notifications*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('admin.notifications*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span>Notifications</span>
            </div>
            @if($adminUnreadCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-600 text-white">
                    {{ $adminUnreadCount > 9 ? '9+' : $adminUnreadCount }}
                </span>
            @endif
        </a>

        <!-- Profile Tab -->
        <a href="{{ route('admin.profile') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('admin.profile*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('admin.profile*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Admin Profile</span>
            </div>
        </a>
    </div>

</div>

<!-- Sidebar Bottom: Admin User Info & Logout -->
<div class="p-3 border-t border-slate-800 bg-slate-900 mt-auto shrink-0">
    <div class="flex items-center justify-between gap-2 p-2 rounded-xl bg-slate-800/70 border border-slate-700/60">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="h-8 w-8 rounded-lg bg-emerald-600 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-sm">
                {{ strtoupper(substr(Auth::user()->email ?? 'A', 0, 1)) }}
            </div>
            <div class="flex flex-col min-w-0">
                <span class="text-xs font-bold text-white truncate max-w-[130px]">{{ Auth::user()->email ?? 'admin@trabago.com' }}</span>
                <span class="text-[10px] text-slate-400 leading-tight">Super Administrator</span>
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
