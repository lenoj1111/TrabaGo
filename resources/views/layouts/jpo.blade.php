@php
    $jpoUnreadCount = Auth::check()
        ? \Illuminate\Support\Facades\DB::table('notifications')
            ->where('user_id', Auth::id())
            ->where('is_read', 0)
            ->count()
        : 0;
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'JPO Command Center - TrabaGo DMDP')</title>

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        ::selection { background-color: #059669; color: #ffffff; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #10b981; }

        /* Sidebar width transition */
        .jpo-sidebar {
            transition: width 300ms cubic-bezier(0.4, 0, 0.2, 1),
                        transform 300ms cubic-bezier(0.4, 0, 0.2, 1);
        }
        .jpo-sidebar-label {
            transition: opacity 180ms ease, transform 180ms ease;
            white-space: nowrap;
        }
        .jpo-sidebar--collapsed .jpo-sidebar-label {
            opacity: 0;
            transform: translateX(-6px);
            pointer-events: none;
        }

        /* Nav items */
        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #cbd5e1;
            transition: background-color 150ms ease, color 150ms ease;
        }
        .nav-item:hover {
            background-color: #1e293b;
            color: #ffffff;
        }
        .nav-item-active {
            background-color: #10b981;
            color: #020617 !important;
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.25);
        }
        .nav-item-active:hover {
            background-color: #10b981;
            color: #020617 !important;
        }

        .subnav-item {
            display: block;
            padding: 0.4rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: #94a3b8;
            transition: background-color 150ms ease, color 150ms ease;
        }
        .subnav-item:hover {
            background-color: #1e293b;
            color: #e2e8f0;
        }
        .subnav-item-active {
            background-color: #1e293b;
            color: #10b981;
            font-weight: 700;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-slate-50 text-slate-900 antialiased"
      x-data="jpoSidebar()"
      x-init="init()">

    {{-- ============================================================ --}}
    {{-- MOBILE BACKDROP                                               --}}
    {{-- ============================================================ --}}
    <div id="jpo-backdrop"
         onclick="closeJpoSidebar()"
         class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden"></div>

    {{-- ============================================================ --}}
    {{-- SIDEBAR                                                       --}}
    {{-- ============================================================ --}}
    <aside id="jpo-sidebar"
           :class="collapsed ? 'lg:w-20 jpo-sidebar--collapsed' : 'lg:w-64'"
           class="jpo-sidebar fixed inset-y-0 left-0 z-40 w-64 bg-slate-950 text-slate-100
                  flex flex-col border-r border-slate-800
                  -translate-x-full lg:translate-x-0">

        {{-- Brand --}}
        <div class="flex items-center justify-between gap-3 px-4 h-16 border-b border-slate-800 shrink-0">
            <a href="{{ route('jpo.dashboard') }}" class="flex items-center gap-3 min-w-0">
                <div class="h-10 w-10 rounded-2xl bg-gradient-to-tr from-slate-900 via-emerald-800 to-teal-600
                            flex items-center justify-center text-white text-xl shadow-md shadow-emerald-900/30 shrink-0">
                    📋
                </div>
                <div class="flex flex-col leading-tight min-w-0 jpo-sidebar-label">
                    <span class="font-black text-base text-white tracking-tight">
                        Traba<span class="text-emerald-400">Go</span>
                    </span>
                    <span class="text-[10px] font-bold text-emerald-400 tracking-widest uppercase">JPO Command</span>
                </div>
            </a>

            {{-- Collapse toggle (desktop) --}}
            <button type="button"
                    @click="toggleCollapse()"
                    class="hidden lg:flex items-center justify-center w-7 h-7 rounded-lg
                           text-slate-400 hover:text-emerald-400 hover:bg-slate-800 transition-colors shrink-0"
                    :title="collapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                <svg class="w-4 h-4 transition-transform duration-300"
                     :class="collapsed ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

            {{-- ============ MAIN ============ --}}
            <p class="jpo-sidebar-label px-3 pt-2 pb-1 text-[10px] font-bold uppercase tracking-widest text-slate-500">
                Main
            </p>

            {{-- Dashboard --}}
            <a href="{{ route('jpo.dashboard') }}"
               class="nav-item {{ request()->routeIs('jpo.dashboard*') ? 'nav-item-active' : '' }}"
               :title="collapsed ? 'Dashboard' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="jpo-sidebar-label flex-1">Dashboard</span>
            </a>

            {{-- ============ EVALUATIONS ============ --}}
            <p class="jpo-sidebar-label px-3 pt-5 pb-1 text-[10px] font-bold uppercase tracking-widest text-slate-500">
                Evaluations
            </p>

            {{-- Evaluate Jobseekers --}}
            <a href="{{ route('jpo.evaluations.jobseekers') }}"
               class="nav-item {{ request()->routeIs('jpo.evaluations.jobseekers*') ? 'nav-item-active' : '' }}"
               :title="collapsed ? 'Evaluate Jobseekers' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="jpo-sidebar-label flex-1">Evaluate Jobseekers</span>
            </a>

            {{-- Accreditation Papers --}}
            <a href="{{ route('jpo.evaluations.accreditations') }}"
               class="nav-item {{ request()->routeIs('jpo.evaluations.accreditations*') ? 'nav-item-active' : '' }}"
               :title="collapsed ? 'Accreditation Papers' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="jpo-sidebar-label flex-1">Accreditation Papers</span>
            </a>

            {{-- Placement Reports --}}
            <a href="{{ route('jpo.evaluations.placement-reports') }}"
               class="nav-item {{ request()->routeIs('jpo.evaluations.placement-reports*') ? 'nav-item-active' : '' }}"
               :title="collapsed ? 'Placement Reports' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 17v-6m4 6V7m4 10v-3M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <span class="jpo-sidebar-label flex-1">Placement Reports</span>
            </a>

            {{-- ============ COMMUNICATION ============ --}}
            <p class="jpo-sidebar-label px-3 pt-5 pb-1 text-[10px] font-bold uppercase tracking-widest text-slate-500">
                Communication
            </p>

            {{-- Notifications --}}
            <a href="{{ route('jpo.notifications') }}"
               class="nav-item {{ request()->routeIs('jpo.notifications*') ? 'nav-item-active' : '' }}"
               :title="collapsed ? 'Notifications' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="jpo-sidebar-label flex-1">Notifications</span>

                @if($jpoUnreadCount > 0)
                    <span class="jpo-sidebar-label px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-600 text-white">
                        {{ $jpoUnreadCount > 9 ? '9+' : $jpoUnreadCount }}
                    </span>
                @endif
            </a>
        </nav>

        {{-- User Footer --}}
        <div class="border-t border-slate-800 p-3 shrink-0">
            <div class="flex items-center gap-3 px-2 py-2 rounded-lg bg-slate-900/60">
                <div class="w-9 h-9 rounded-full bg-emerald-500 flex items-center justify-center
                            text-slate-950 text-sm font-bold shrink-0">
                    {{ strtoupper(substr(Auth::user()->email ?? 'J', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0 jpo-sidebar-label">
                    <p class="text-xs font-bold text-white truncate">{{ Auth::user()->email ?? 'Officer' }}</p>
                    <p class="text-[10px] text-slate-400 uppercase tracking-wider">Placement Officer</p>
                </div>
            </div>

            <a href="{{ route('jpo.profile') }}"
               class="mt-2 w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold
                      text-slate-300 hover:bg-slate-800 hover:text-white transition-colors
                      {{ request()->routeIs('jpo.profile*') ? 'bg-slate-800 text-white' : '' }}"
               :title="collapsed ? 'Officer Profile' : ''">
                <span class="jpo-sidebar-label">👤 Officer Profile</span>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold
                               text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 transition-colors"
                        :title="collapsed ? 'Log Out' : ''">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span class="jpo-sidebar-label">Log Out</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ============================================================ --}}
    {{-- MAIN CONTENT                                                  --}}
    {{-- ============================================================ --}}
    <div :class="collapsed ? 'lg:pl-20' : 'lg:pl-64'"
         class="transition-all duration-300 min-h-screen flex flex-col">

        {{-- Topbar --}}
        <header class="sticky top-0 z-20 bg-white/95 backdrop-blur-md border-b border-emerald-100 shadow-sm">
            <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16 gap-3">

                <div class="flex items-center gap-3 min-w-0">
                    {{-- ☰ Mobile hamburger — LEFT of logo --}}
                    <button type="button"
                            onclick="openJpoSidebar()"
                            class="lg:hidden p-2 -ml-1 rounded-lg hover:bg-slate-100 text-slate-700 shrink-0"
                            aria-label="Open menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    {{-- Mobile brand --}}
                    <a href="{{ route('jpo.dashboard') }}" class="lg:hidden flex items-center gap-2 group shrink-0">
                        <div class="h-8 w-8 rounded-xl bg-gradient-to-tr from-slate-900 via-emerald-800 to-teal-600
                                    flex items-center justify-center text-white text-base shadow-sm">📋</div>
                        <span class="font-black text-base text-slate-900 tracking-tight">
                            Traba<span class="text-emerald-600">Go</span>
                        </span>
                    </a>

                    {{-- Desktop page title --}}
                    <h1 class="hidden lg:block text-base sm:text-lg font-black text-slate-900 truncate">
                        @yield('title', 'Dashboard')
                    </h1>
                </div>

                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    <span class="hidden md:inline text-xs text-slate-500 font-semibold">
                        {{ now()->format('D, M d, Y') }}
                    </span>

                    {{-- Notification bell --}}
                    <a href="{{ route('jpo.notifications') }}"
                       class="relative p-2 rounded-xl text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 transition-colors"
                       title="Notifications">
                        <i class="bi bi-bell text-lg"></i>
                        @if($jpoUnreadCount > 0)
                            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-600 text-[10px] font-black text-white ring-2 ring-white">
                                {{ $jpoUnreadCount > 9 ? '9+' : $jpoUnreadCount }}
                            </span>
                        @endif
                    </a>

                    {{-- Profile dropdown --}}
                    <div class="relative" x-data="{ profileOpen: false }" @click.away="profileOpen = false">
                        <button @click="profileOpen = !profileOpen" type="button"
                                class="flex items-center gap-2 p-1 rounded-xl hover:bg-emerald-50 transition-colors focus:outline-none">
                            <div class="h-8 w-8 rounded-xl bg-slate-900 text-white font-bold text-xs flex items-center justify-center ring-2 ring-emerald-500 shadow-sm">
                                {{ strtoupper(substr(Auth::user()->email ?? 'J', 0, 1)) }}
                            </div>
                            <i class="bi bi-chevron-down text-xs text-slate-400 hidden sm:inline-block"></i>
                        </button>

                        <div x-show="profileOpen" x-cloak
                             class="absolute right-0 mt-2 w-60 rounded-2xl bg-white p-2 shadow-2xl border border-emerald-100 z-50">
                            <div class="px-3 py-2.5 bg-emerald-50/70 rounded-xl mb-1.5 border border-emerald-100/60">
                                <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->email }}</p>
                                <p class="text-[10px] text-emerald-700 font-bold">DMDP Staff Account</p>
                            </div>
                            <a href="{{ route('jpo.profile') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors">
                                <i class="bi bi-person"></i> Officer Profile & Security
                            </a>
                            <a href="{{ route('jpo.notifications') }}"
                               class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors">
                                <span class="flex items-center gap-2.5"><i class="bi bi-bell"></i> Notifications</span>
                                @if($jpoUnreadCount > 0)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-700">{{ $jpoUnreadCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('jpo.evaluations.placement-reports') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors">
                                <i class="bi bi-graph-up"></i> Placement Reports
                            </a>
                            <div class="border-t border-slate-100 my-1"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors">
                                    <i class="bi bi-box-arrow-right"></i> Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash Toasts --}}
        <div class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-md w-full px-4 pointer-events-none">
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     class="pointer-events-auto flex items-center justify-between gap-3 rounded-2xl bg-slate-900 text-white p-4 shadow-2xl border border-emerald-500/40">
                    <div class="flex items-center gap-2.5">
                        <span class="h-8 w-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-black">✓</span>
                        <p class="text-xs font-bold">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-slate-400 hover:text-white">&times;</button>
                </div>
            @endif
            @if (session('error') || (isset($errors) && $errors->any()))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)"
                     class="pointer-events-auto flex items-center justify-between gap-3 rounded-2xl bg-rose-950 text-white p-4 shadow-2xl border border-rose-600/40">
                    <div class="flex items-center gap-2.5">
                        <span class="h-8 w-8 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center font-black">!</span>
                        <p class="text-xs font-bold">{{ session('error') ?: (isset($errors) ? $errors->first() : '') }}</p>
                    </div>
                    <button @click="show = false" class="text-rose-300 hover:text-white">&times;</button>
                </div>
            @endif
        </div>

        {{-- Page Content --}}
        <main class="flex-1 min-w-0 overflow-x-hidden">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="mt-auto border-t border-slate-200 bg-white py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-900">DMDP TrabaGo</span>
                    <span>&copy; {{ date('Y') }} Cebu City Department of Manpower Development and Placement.</span>
                </div>
                <div class="flex items-center gap-6">
                    <a href="{{ route('jpo.evaluations.jobseekers') }}" class="hover:text-emerald-700 font-semibold">Jobseeker Referrals</a>
                    <a href="{{ route('jpo.evaluations.accreditations') }}" class="hover:text-emerald-700 font-semibold">Accreditation Reviews</a>
                    <a href="{{ route('jpo.evaluations.placement-reports') }}" class="hover:text-emerald-700 font-semibold">Placement Reports</a>
                </div>
            </div>
        </footer>
    </div>

    {{-- ============================================================ --}}
    {{-- SCRIPTS                                                       --}}
    {{-- ============================================================ --}}
    <script>
        function jpoSidebar() {
            return {
                collapsed: false,
                openGroups: {},

                init() {
                    try {
                        const saved = localStorage.getItem('jpo_sidebar_collapsed');
                        if (saved === '1') this.collapsed = true;
                    } catch (e) {}

                    this.$watch('collapsed', (val) => {
                        try { localStorage.setItem('jpo_sidebar_collapsed', val ? '1' : '0'); } catch (e) {}
                    });
                },

                toggleCollapse() {
                    this.collapsed = !this.collapsed;
                },

                toggleGroup(name) {
                    if (this.collapsed) {
                        this.collapsed = false;
                        this.openGroups[name] = true;
                    } else {
                        this.openGroups[name] = !this.openGroups[name];
                    }
                }
            };
        }

        function openJpoSidebar() {
            document.getElementById('jpo-sidebar').classList.remove('-translate-x-full');
            document.getElementById('jpo-backdrop').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeJpoSidebar() {
            document.getElementById('jpo-sidebar').classList.add('-translate-x-full');
            document.getElementById('jpo-backdrop').classList.add('hidden');
            document.body.style.overflow = '';
        }

        window.addEventListener('resize', function () {
            if (window.innerWidth >= 1024) closeJpoSidebar();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeJpoSidebar();
        });
    </script>

    @stack('scripts')
</body>
</html>