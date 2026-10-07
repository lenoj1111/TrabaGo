@php
    $trainerUnreadCount = Auth::check()
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
    <title>@yield('title', 'Skills Trainer - TrabaGo DMDP')</title>

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
        .tr-sidebar {
            transition: width 300ms cubic-bezier(0.4, 0, 0.2, 1),
                        transform 300ms cubic-bezier(0.4, 0, 0.2, 1);
        }
        .tr-sidebar-label {
            transition: opacity 180ms ease, transform 180ms ease;
            white-space: nowrap;
        }
        .tr-sidebar--collapsed .tr-sidebar-label {
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
      x-data="trainerSidebar()"
      x-init="init()">

    {{-- ============================================================ --}}
    {{-- MOBILE BACKDROP                                               --}}
    {{-- ============================================================ --}}
    <div id="tr-backdrop"
         onclick="closeTrSidebar()"
         class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden"></div>

    {{-- ============================================================ --}}
    {{-- SIDEBAR                                                       --}}
    {{-- ============================================================ --}}
    <aside id="tr-sidebar"
           :class="collapsed ? 'lg:w-20 tr-sidebar--collapsed' : 'lg:w-64'"
           class="tr-sidebar fixed inset-y-0 left-0 z-40 w-64 bg-slate-950 text-slate-100
                  flex flex-col border-r border-slate-800
                  -translate-x-full lg:translate-x-0">

        {{-- Brand --}}
        <div class="flex items-center justify-between gap-3 px-4 h-16 border-b border-slate-800 shrink-0">
            <a href="{{ route('trainer.dashboard') }}" class="flex items-center gap-3 min-w-0">
                <div class="h-10 w-10 rounded-2xl bg-gradient-to-tr from-emerald-950 via-emerald-800 to-teal-600
                            flex items-center justify-center text-white text-xl shadow-md shadow-emerald-900/30 shrink-0">
                    🎓
                </div>
                <div class="flex flex-col leading-tight min-w-0 tr-sidebar-label">
                    <span class="font-black text-base text-white tracking-tight">
                        Traba<span class="text-emerald-400">Go</span>
                    </span>
                    <span class="text-[10px] font-bold text-emerald-400 tracking-widest uppercase">Skills Trainer</span>
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
            <p class="tr-sidebar-label px-3 pt-2 pb-1 text-[10px] font-bold uppercase tracking-widest text-slate-500">
                Main
            </p>

            {{-- Dashboard --}}
            <a href="{{ route('trainer.dashboard') }}"
               class="nav-item {{ request()->routeIs('trainer.dashboard*') ? 'nav-item-active' : '' }}"
               :title="collapsed ? 'Dashboard' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="tr-sidebar-label flex-1">Dashboard</span>
            </a>

            {{-- Training Courses --}}
            <a href="{{ route('trainer.courses') }}"
               class="nav-item {{ request()->routeIs('trainer.courses*') ? 'nav-item-active' : '' }}"
               :title="collapsed ? 'Training Courses' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                </svg>
                <span class="tr-sidebar-label flex-1">Training Courses</span>
            </a>

            {{-- Skills Enrollment --}}
            <a href="{{ route('trainer.skills.enrollment') }}"
               class="nav-item {{ request()->routeIs('trainer.skills.enrollment*') ? 'nav-item-active' : '' }}"
               :title="collapsed ? 'Skills Enrollment' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
                <span class="tr-sidebar-label flex-1">Skills Enrollment</span>
            </a>

            {{-- Enrolled Job Seekers --}}
            <a href="{{ route('trainer.enrollments.index') }}"
               class="nav-item {{ request()->routeIs('trainer.enrollments*') ? 'nav-item-active' : '' }}"
               :title="collapsed ? 'Enrolled Job Seekers' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="tr-sidebar-label flex-1">Enrolled Job Seekers</span>
            </a>

            {{-- Collaborators --}}
            <a href="{{ route('trainer.collaborators') }}"
               class="nav-item {{ request()->routeIs('trainer.collaborators*') ? 'nav-item-active' : '' }}"
               :title="collapsed ? 'Collaborators' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span class="tr-sidebar-label flex-1">Collaborators</span>
            </a>

            {{-- ============ COMMUNICATION ============ --}}
            <p class="tr-sidebar-label px-3 pt-5 pb-1 text-[10px] font-bold uppercase tracking-widest text-slate-500">
                Communication
            </p>

            {{-- Notifications --}}
            <a href="{{ route('trainer.notifications') }}"
               class="nav-item {{ request()->routeIs('trainer.notifications*') ? 'nav-item-active' : '' }}"
               :title="collapsed ? 'Notifications' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="tr-sidebar-label flex-1">Notifications</span>

                @if($trainerUnreadCount > 0)
                    <span class="tr-sidebar-label px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-600 text-white">
                        {{ $trainerUnreadCount > 9 ? '9+' : $trainerUnreadCount }}
                    </span>
                @endif
            </a>
        </nav>

        {{-- User Footer --}}
        <div class="border-t border-slate-800 p-3 shrink-0">
            <div class="flex items-center gap-3 px-2 py-2 rounded-lg bg-slate-900/60">
                <div class="w-9 h-9 rounded-full bg-emerald-500 flex items-center justify-center
                            text-slate-950 text-sm font-bold shrink-0">
                    {{ strtoupper(substr(Auth::user()->email ?? 'T', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0 tr-sidebar-label">
                    <p class="text-xs font-bold text-white truncate">{{ Auth::user()->email ?? 'Trainer' }}</p>
                    <p class="text-[10px] text-slate-400 uppercase tracking-wider">Skills Trainer</p>
                </div>
            </div>

            <a href="{{ route('trainer.profile') }}"
               class="mt-2 w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold
                      text-slate-300 hover:bg-slate-800 hover:text-white transition-colors
                      {{ request()->routeIs('trainer.profile*') ? 'bg-slate-800 text-white' : '' }}"
               :title="collapsed ? 'Edit Profile' : ''">
                <span class="tr-sidebar-label">👤 Edit Profile</span>
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
                    <span class="tr-sidebar-label">Log Out</span>
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
                            onclick="openTrSidebar()"
                            class="lg:hidden p-2 -ml-1 rounded-lg hover:bg-slate-100 text-slate-700 shrink-0"
                            aria-label="Open menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    {{-- Mobile brand --}}
                    <a href="{{ route('trainer.dashboard') }}" class="lg:hidden flex items-center gap-2 group shrink-0">
                        <div class="h-8 w-8 rounded-xl bg-gradient-to-tr from-emerald-950 via-emerald-800 to-teal-600
                                    flex items-center justify-center text-white text-base shadow-sm">🎓</div>
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
                    <a href="{{ route('trainer.notifications') }}"
                       class="relative p-2 rounded-xl text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 transition-colors"
                       title="Notifications">
                        <i class="bi bi-bell text-lg"></i>
                        @if($trainerUnreadCount > 0)
                            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-600 text-[10px] font-black text-white ring-2 ring-white">
                                {{ $trainerUnreadCount > 9 ? '9+' : $trainerUnreadCount }}
                            </span>
                        @endif
                    </a>

                    {{-- Profile dropdown --}}
                    <div class="relative" x-data="{ profileOpen: false }" @click.away="profileOpen = false">
                        <button @click="profileOpen = !profileOpen" type="button"
                                class="flex items-center gap-2 p-1 rounded-xl hover:bg-emerald-50 transition-colors focus:outline-none">
                            <div class="h-8 w-8 rounded-xl bg-slate-900 text-white font-bold text-xs flex items-center justify-center ring-2 ring-emerald-500 shadow-sm">
                                {{ strtoupper(substr(Auth::user()->email ?? 'T', 0, 1)) }}
                            </div>
                            <i class="bi bi-chevron-down text-xs text-slate-400 hidden sm:inline-block"></i>
                        </button>

                        <div x-show="profileOpen" x-cloak
                             class="absolute right-0 mt-2 w-60 rounded-2xl bg-white p-2 shadow-2xl border border-emerald-100 z-50">
                            <div class="px-3 py-2.5 bg-emerald-50/70 rounded-xl mb-1.5 border border-emerald-100/60">
                                <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->email }}</p>
                                <p class="text-[10px] text-emerald-700 font-bold">DMDP Trainer Account</p>
                            </div>
                            <a href="{{ route('trainer.profile') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors">
                                <i class="bi bi-person"></i> Edit Profile
                            </a>
                            <a href="{{ route('trainer.notifications') }}"
                               class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors">
                                <span class="flex items-center gap-2.5"><i class="bi bi-bell"></i> Notifications</span>
                                @if($trainerUnreadCount > 0)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-700">{{ $trainerUnreadCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('trainer.courses') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors">
                                <i class="bi bi-journal-bookmark"></i> Course Modules
                            </a>
                            <a href="{{ route('trainer.skills.enrollment') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors">
                                <i class="bi bi-bullseye"></i> Skills Enrollment
                            </a>
                            <a href="{{ route('trainer.collaborators') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors">
                                <i class="bi bi-people"></i> Collaborators
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
                    <span>&copy; {{ date('Y') }} Skills Training & Certification Division.</span>
                </div>
                <div class="flex items-center gap-6">
                    <a href="{{ route('trainer.enrollments.index') }}" class="hover:text-emerald-700 font-semibold">Manage Enrollments</a>
                    <a href="{{ route('trainer.courses') }}" class="hover:text-emerald-700 font-semibold">Course Modules</a>
                </div>
            </div>
        </footer>
    </div>

    {{-- ============================================================ --}}
    {{-- SCRIPTS                                                       --}}
    {{-- ============================================================ --}}
    <script>
        function trainerSidebar() {
            return {
                collapsed: false,
                openGroups: {},

                init() {
                    try {
                        const saved = localStorage.getItem('tr_sidebar_collapsed');
                        if (saved === '1') this.collapsed = true;
                    } catch (e) {}

                    this.$watch('collapsed', (val) => {
                        try { localStorage.setItem('tr_sidebar_collapsed', val ? '1' : '0'); } catch (e) {}
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

        function openTrSidebar() {
            document.getElementById('tr-sidebar').classList.remove('-translate-x-full');
            document.getElementById('tr-backdrop').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeTrSidebar() {
            document.getElementById('tr-sidebar').classList.add('-translate-x-full');
            document.getElementById('tr-backdrop').classList.add('hidden');
            document.body.style.overflow = '';
        }

        window.addEventListener('resize', function () {
            if (window.innerWidth >= 1024) closeTrSidebar();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeTrSidebar();
        });
    </script>

    @stack('scripts')
</body>
</html>