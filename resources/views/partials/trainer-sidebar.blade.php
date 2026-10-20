<!-- Sidebar Header -->
<div class="h-16 shrink-0 px-6 flex items-center justify-between border-b border-slate-800 bg-slate-900">
    <a href="{{ route('trainer.dashboard') }}" class="flex items-center gap-3 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 rounded-lg">
        <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white font-black text-sm shadow-md shadow-emerald-950/40 group-hover:scale-105 transition-transform">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        <div class="flex flex-col">
            <span class="font-extrabold text-base text-white tracking-tight leading-none group-hover:text-emerald-400 transition-colors">
                Traba<span class="text-emerald-400">Go</span>
            </span>
            <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase leading-none mt-1">Skills Trainer</span>
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
        <a href="{{ route('trainer.dashboard') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('trainer.dashboard*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('trainer.dashboard*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </div>
        </a>
    </div>

    <!-- Group: TRAINING & SKILLS -->
    <div class="space-y-1">
        <p class="px-3 text-xs font-bold uppercase tracking-wider text-slate-400">Training & Skills</p>
        
        <!-- Training Courses Tab -->
        <a href="{{ route('trainer.courses') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('trainer.courses*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('trainer.courses*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span>Training Courses</span>
            </div>
        </a>

        <!-- Skills Enrollment Tab -->
        <a href="{{ route('trainer.skills.enrollment') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('trainer.skills.enrollment*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('trainer.skills.enrollment*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
                <span>Skills Enrollment</span>
            </div>
        </a>

        <!-- Enrolled Job Seekers Tab -->
        <a href="{{ route('trainer.enrollments.index') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('trainer.enrollments*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('trainer.enrollments*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span>Enrolled Job Seekers</span>
            </div>
            @if($pendingEnrollmentsCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ request()->routeIs('trainer.enrollments*') ? 'bg-white text-emerald-900' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' }}">
                    {{ $pendingEnrollmentsCount }}
                </span>
            @endif
        </a>

        <!-- Collaborators Tab -->
        <a href="{{ route('trainer.collaborators') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('trainer.collaborators*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('trainer.collaborators*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Collaborators</span>
            </div>
        </a>
    </div>

    <!-- Group: ACCOUNT & SETTINGS -->
    <div class="space-y-1">
        <p class="px-3 text-xs font-bold uppercase tracking-wider text-slate-400">Account & System</p>
        
        <!-- Notifications Tab -->
        <a href="{{ route('trainer.notifications') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('trainer.notifications*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('trainer.notifications*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span>Notifications</span>
            </div>
            @if($trainerUnreadCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-600 text-white shadow-sm">
                    {{ $trainerUnreadCount > 9 ? '9+' : $trainerUnreadCount }}
                </span>
            @endif
        </a>

        <!-- Trainer Profile Tab -->
        <a href="{{ route('trainer.profile') }}" 
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 {{ request()->routeIs('trainer.profile*') ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('trainer.profile*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Trainer Profile</span>
            </div>
        </a>
    </div>

</div>

<!-- Sidebar Bottom: Trainer User Info & Logout -->
<div class="p-3 border-t border-slate-800 bg-slate-900 mt-auto shrink-0">
    <div class="flex items-center justify-between gap-2 p-2 rounded-xl bg-slate-800/70 border border-slate-700/60">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="h-8 w-8 rounded-lg bg-emerald-600 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-sm">
                TRN
            </div>
            <div class="flex flex-col min-w-0">
                <span class="text-xs font-bold text-white truncate max-w-[130px]">{{ Auth::user()->email ?? 'trainer@trabago.com' }}</span>
                <span class="text-[10px] text-slate-400 leading-tight">Skills Trainer</span>
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
