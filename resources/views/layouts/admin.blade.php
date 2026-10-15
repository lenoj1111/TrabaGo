@php
    $adminUnreadCount = Auth::check() 
        ? \Illuminate\Support\Facades\Cache::remember('notif_count_' . Auth::id(), 10, fn() => \Illuminate\Support\Facades\DB::table('notifications')->where('user_id', Auth::id())->where('is_read', 0)->count())
        : 0;
    
    $pendingApprovalsCount = \Illuminate\Support\Facades\Cache::remember('admin_pending_approvals_count', 3, function () {
        try {
            return (int) \Illuminate\Support\Facades\DB::table('job_postings')->where('status', 'pending')->count()
                + (int) \Illuminate\Support\Facades\DB::table('users')->where('role', 'trainer')->where(function ($q) {
                    $q->where('is_approved', 0)->orWhere('status', 'pending');
                })->count()
                + (int) \Illuminate\Support\Facades\DB::table('employer_accreditation')->whereIn('status', ['jpo_approved', 'supervisor_approved', 'submitted_to_jpo'])->count()
                + (int) \Illuminate\Support\Facades\DB::table('placement_reports')->whereIn('status', ['jpo_evaluated', 'submitted_to_jpo', 'pending'])->count()
                + (int) \Illuminate\Support\Facades\DB::table('jobseekers')
                    ->join('users', 'jobseekers.user_id', '=', 'users.user_id')
                    ->where('users.role', 'jobseeker')
                    ->where(function ($q) {
                        $q->where('users.is_approved', 0)
                          ->orWhere('users.account_status', 'pending_review')
                          ->orWhere('users.account_status', 'unverified')
                          ->orWhereNull('users.account_status');
                    })->count();
        } catch (\Throwable $e) {
            return 0;
        }
    });
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - TrabaGo DMDP</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Vite CSS & JS (Alpine is bundled in app.js) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        ::selection { background-color: #059669; color: #ffffff; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #059669; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased" x-data="{ sidebarOpen: false, profileMenuOpen: false }">

    <!-- DESKTOP PERMANENT SIDEBAR (ALWAYS OPEN & FIXED ON SCREEN) -->
    <aside class="hidden lg:fixed lg:inset-y-0 lg:z-40 lg:flex lg:w-64 lg:flex-col bg-slate-900 text-slate-300 border-r border-slate-800">
        @include('partials.admin-sidebar', ['isMobile' => false])
    </aside>

    <!-- MOBILE RESPONSIVE DRAWER OVERLAY (< lg) -->
    <div x-show="sidebarOpen" x-cloak class="lg:hidden fixed inset-0 z-50 flex">
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false" 
             class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs"></div>

        <aside x-show="sidebarOpen" 
               x-transition:enter="transition ease-out duration-300 transform"
               x-transition:enter-start="-translate-x-full shadow-none"
               x-transition:enter-end="translate-x-0 shadow-2xl"
               x-transition:leave="transition ease-in duration-200 transform"
               x-transition:leave-start="translate-x-0 shadow-2xl"
               x-transition:leave-end="-translate-x-full shadow-none"
               @keydown.escape.window="sidebarOpen = false"
               class="relative flex-1 flex flex-col max-w-xs sm:max-w-sm w-full bg-slate-900 text-slate-300 border-r border-slate-800 shadow-2xl z-50">
            @include('partials.admin-sidebar', ['isMobile' => true])
        </aside>
    </div>

    <!-- MAIN APPLICATION CONTAINER -->
    <div class="lg:pl-64 flex flex-col min-h-screen w-full">
        
        <!-- Top Navigation Bar -->
        <header class="sticky top-0 z-30 flex h-16 shrink-0 items-center justify-between border-b border-slate-200 bg-white px-4 sm:px-6 lg:px-8">
            
            <!-- Left: Mobile Menu Trigger Button & Branding -->
            <div class="flex items-center gap-4">
                
                <!-- Mobile Menu Button (Only on small screens) -->
                <button @click="sidebarOpen = true" 
                        type="button" 
                        class="lg:hidden flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-emerald-600 text-white transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 group">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <span class="text-xs font-bold uppercase tracking-wider">Menu</span>
                </button>
                
                <!-- Brand Tag -->
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 group">
                    <div class="h-8 w-8 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-bold text-xs shadow-sm group-hover:scale-105 transition-transform">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-sm text-slate-900 tracking-tight leading-none group-hover:text-emerald-600 transition-colors">
                            Traba<span class="text-emerald-600">Go</span>
                        </span>
                        <span class="text-[9px] font-semibold text-slate-500 tracking-wider uppercase leading-none mt-0.5">Admin Command</span>
                    </div>
                </a>

                <!-- Breadcrumb -->
                <div class="hidden md:flex items-center gap-2 pl-2 border-l border-slate-200">
                    <span class="text-xs text-slate-400">Admin Command</span>
                    <span class="text-slate-300">/</span>
                    <h2 class="text-xs font-bold text-slate-900 tracking-tight">@yield('title', 'Admin Dashboard')</h2>
                </div>
            </div>

            <!-- Right: Status, Notifications & Profile -->
            <div class="flex items-center gap-2">
                
                <!-- Status Badge -->
                <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-[11px] font-semibold text-emerald-800">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>System Operational</span>
                </div>

                <!-- Notifications Bell -->
                <a href="{{ route('admin.notifications') }}" 
                   class="relative p-2 rounded-xl text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500"
                   title="Notifications">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($adminUnreadCount > 0)
                        <span class="absolute top-1 right-1 flex h-4 min-w-[16px] px-1 items-center justify-center rounded-full bg-rose-600 text-[10px] font-bold text-white shadow-sm">
                            {{ $adminUnreadCount > 9 ? '9+' : $adminUnreadCount }}
                        </span>
                    @endif
                </a>

                <!-- Profile Dropdown -->
                <div class="relative" @click.away="profileMenuOpen = false">
                    <button @click="profileMenuOpen = !profileMenuOpen" type="button" class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-slate-100 transition-colors text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                        <div class="h-8 w-8 rounded-lg bg-slate-900 text-white font-bold text-xs flex items-center justify-center shadow-sm">
                            {{ strtoupper(substr(Auth::user()->email ?? 'A', 0, 1)) }}
                        </div>
                        <div class="hidden md:flex flex-col">
                            <span class="text-xs font-bold text-slate-900 leading-none truncate max-w-[140px]">{{ Auth::user()->email ?? 'admin@trabago.com' }}</span>
                            <span class="text-[10px] text-slate-500 leading-none mt-0.5">Super Administrator</span>
                        </div>
                        <svg class="hidden md:block h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="profileMenuOpen" x-cloak class="absolute right-0 mt-2 w-56 rounded-2xl bg-white p-2 shadow-xl border border-slate-200 focus:outline-none z-50">
                        <div class="px-3 py-2 bg-slate-50 rounded-xl mb-1 border border-slate-100">
                            <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->email ?? 'admin@trabago.com' }}</p>
                            <p class="text-[10px] text-emerald-700 font-semibold">DMDP Super Admin</p>
                        </div>
                        
                        <a href="{{ route('admin.profile') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-medium text-slate-700 hover:bg-slate-100 hover:text-emerald-700 transition-colors">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Edit Profile
                        </a>

                        <a href="{{ route('admin.reports') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-medium text-slate-700 hover:bg-slate-100 hover:text-emerald-700 transition-colors">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Platform Reports
                        </a>

                        <div class="border-t border-slate-100 my-1"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-medium text-rose-600 hover:bg-rose-50 transition-colors">
                                <svg class="h-4 w-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>

            </div>

        </header>

        <!-- Flash Toasts -->
        <div class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-md w-full px-4 pointer-events-none">
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                     class="pointer-events-auto flex items-center justify-between gap-3 rounded-2xl bg-slate-900 text-white p-4 shadow-xl border border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <span class="h-7 w-7 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <p class="text-xs font-semibold">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-slate-400 hover:text-white">&times;</button>
                </div>
            @endif
            @if (session('error') || (isset($errors) && $errors->any()))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)" 
                     class="pointer-events-auto flex items-center justify-between gap-3 rounded-2xl bg-slate-900 text-white p-4 shadow-xl border border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <span class="h-7 w-7 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center font-bold text-xs">!</span>
                        <p class="text-xs font-semibold">{{ session('error') ?: (isset($errors) ? $errors->first() : '') }}</p>
                    </div>
                    <button @click="show = false" class="text-slate-400 hover:text-white">&times;</button>
                </div>
            @endif
        </div>

        <!-- Main Content -->
        <main class="flex-1">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="mt-auto border-t border-slate-200 bg-white py-6">
            <div class="w-full max-w-[1680px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-900">DMDP TrabaGo</span>
                    <span>&copy; {{ date('Y') }} Central Administration & Systems Management Division.</span>
                </div>
                <div class="flex items-center gap-6">
                    <a href="{{ route('admin.approvals.index') }}" class="hover:text-emerald-700 font-semibold transition-colors">Approvals Queue</a>
                    <a href="{{ route('admin.users.index') }}" class="hover:text-emerald-700 font-semibold transition-colors">Staff Accounts</a>
                    <a href="{{ route('admin.reports') }}" class="hover:text-emerald-700 font-semibold transition-colors">Platform Analytics</a>
                </div>
            </div>
        </footer>

    </div>

    @stack('scripts')
</body>
</html>