@php
    $adminUnreadCount = Auth::check() ? \Illuminate\Support\Facades\DB::table('notifications')->where('user_id', Auth::id())->where('is_read', 0)->count() : 0;
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        ::selection { background-color: #059669; color: #ffffff; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f8fafc; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #10b981; }
    </style>
    @stack('styles')
</head>
<body class="h-full flex flex-col bg-slate-50 text-slate-900 antialiased">

    <!-- Admin Header / Navigation -->
    <header x-data="{ mobileOpen: false, profileOpen: false }" class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-emerald-100 shadow-sm shadow-emerald-950/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo & Admin Tag -->
                <div class="flex items-center gap-6 xl:gap-8">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 group shrink-0">
                        <div class="h-10 w-10 rounded-2xl bg-gradient-to-tr from-slate-900 via-emerald-800 to-teal-600 flex items-center justify-center text-white font-black text-xl shadow-md shadow-emerald-900/30 group-hover:scale-105 transition-transform">
                            🛡️
                        </div>
                        <div class="flex flex-col">
                            <span class="font-black text-lg text-slate-900 tracking-tight leading-none group-hover:text-emerald-600 transition-colors">
                                Traba<span class="text-emerald-600">Go</span>
                            </span>
                            <span class="text-[10px] font-bold text-emerald-700 tracking-widest uppercase leading-none mt-0.5">Administration Portal</span>
                        </div>
                    </a>

                    <!-- Nav links -->
                    <nav class="hidden xl:flex items-center gap-1">
                        <a href="{{ route('admin.dashboard') }}" 
                           class="px-3 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200' : 'text-slate-600 hover:text-emerald-700 hover:bg-slate-50' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('admin.approvals.index') }}" 
                           class="px-3 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.approvals*') ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200' : 'text-slate-600 hover:text-emerald-700 hover:bg-slate-50' }}">
                            Manage Approvals
                        </a>
                        <a href="{{ route('admin.users.index') }}" 
                           class="px-3 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.users*') ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200' : 'text-slate-600 hover:text-emerald-700 hover:bg-slate-50' }}">
                            Employee Accounts
                        </a>
                        <a href="{{ route('admin.jobseekers.index') }}" 
                           class="px-3 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.jobseekers*') ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200' : 'text-slate-600 hover:text-emerald-700 hover:bg-slate-50' }}">
                            Jobseekers
                        </a>
                        <a href="{{ route('admin.job-postings') }}" 
                           class="px-3 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.job-postings*') || request()->routeIs('admin.job-postings-list*') ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200' : 'text-slate-600 hover:text-emerald-700 hover:bg-slate-50' }}">
                            Job Postings
                        </a>
                        <a href="{{ route('admin.employers') }}" 
                           class="px-3 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.employers*') ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200' : 'text-slate-600 hover:text-emerald-700 hover:bg-slate-50' }}">
                            Employers
                        </a>
                    </nav>
                </div>

                <!-- Right Actions: Notifications + Profile Dropdown -->
                <div class="flex items-center gap-3">
                    
                    <!-- Notification Bell -->
                    <a href="{{ route('admin.notifications') }}" 
                       class="relative p-2 rounded-xl text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 transition-colors"
                       title="Notifications">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if($adminUnreadCount > 0)
                            <span class="absolute top-1.5 right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-rose-600 text-[10px] font-extrabold text-white shadow-sm ring-2 ring-white">
                                {{ $adminUnreadCount > 9 ? '9+' : $adminUnreadCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Profile Dropdown -->
                    <div class="relative" @click.away="profileOpen = false">
                        <button @click="profileOpen = !profileOpen" type="button" class="flex items-center gap-2.5 p-1 rounded-xl hover:bg-emerald-50 transition-colors text-left focus:outline-none">
                            <div class="h-9 w-9 rounded-xl bg-slate-900 text-white font-bold text-xs flex items-center justify-center ring-2 ring-emerald-500 shadow-sm">
                                {{ strtoupper(substr(Auth::user()->email ?? 'A', 0, 1)) }}
                            </div>
                            <div class="hidden lg:flex flex-col">
                                <span class="text-xs font-bold text-slate-900 leading-none truncate max-w-[140px]">{{ Auth::user()->email ?? 'admin@trabago.com' }}</span>
                                <span class="text-[10px] font-semibold text-emerald-700 leading-none mt-0.5">Super Administrator</span>
                            </div>
                            <svg class="hidden sm:block h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="profileOpen" x-cloak class="absolute right-0 mt-2 w-60 rounded-2xl bg-white p-2 shadow-2xl border border-emerald-100 focus:outline-none z-50">
                            <div class="px-3 py-2.5 bg-emerald-50/70 rounded-xl mb-1.5 border border-emerald-100/60">
                                <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->email ?? 'admin@trabago.com' }}</p>
                                <p class="text-[10px] text-emerald-700 font-bold">DMDP Super Admin</p>
                            </div>
                            
                            <a href="{{ route('admin.profile') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors {{ request()->routeIs('admin.profile*') ? 'bg-emerald-50 text-emerald-800' : '' }}">
                                <span>👤</span> Edit Profile
                            </a>

                            <a href="{{ route('admin.notifications') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors {{ request()->routeIs('admin.notifications*') ? 'bg-emerald-50 text-emerald-800' : '' }}">
                                <span class="flex items-center gap-2.5">
                                    <span>🔔</span> Notifications
                                </span>
                                @if($adminUnreadCount > 0)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-700">
                                        {{ $adminUnreadCount }}
                                    </span>
                                @endif
                            </a>
                            
                            <a href="{{ route('admin.reports') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors {{ request()->routeIs('admin.reports*') ? 'bg-emerald-50 text-emerald-800' : '' }}">
                                <span>📈</span> Platform Reports
                            </a>

                            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors">
                                <span>👥</span> Manage Accounts
                            </a>
                            
                            <a href="{{ route('admin.approvals.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors">
                                <span>✅</span> Approvals Center
                            </a>

                            <div class="border-t border-slate-100 my-1"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors">
                                    <span>🚪</span> Log Out
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Mobile Hamburger -->
                    <button @click="mobileOpen = !mobileOpen" class="xl:hidden p-2 rounded-xl text-slate-600 hover:text-emerald-700 hover:bg-emerald-50">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>

            </div>

            <!-- Mobile Drawer -->
            <div x-show="mobileOpen" x-cloak class="xl:hidden border-t border-emerald-100 py-3 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600' }}">Dashboard</a>
                <a href="{{ route('admin.approvals.index') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('admin.approvals*') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600' }}">Manage Approvals</a>
                <a href="{{ route('admin.users.index') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('admin.users*') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600' }}">Employee Accounts</a>
                <a href="{{ route('admin.jobseekers.index') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('admin.jobseekers*') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600' }}">Jobseekers</a>
                <a href="{{ route('admin.job-postings') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('admin.job-postings*') || request()->routeIs('admin.job-postings-list*') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600' }}">Job Postings</a>
                <a href="{{ route('admin.employers') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('admin.employers*') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600' }}">Employers</a>
                <a href="{{ route('admin.reports') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('admin.reports*') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600' }}">Reports</a>
                <a href="{{ route('admin.notifications') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('admin.notifications*') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600' }}">
                    <span>Notifications</span>
                    @if($adminUnreadCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-600 text-white">{{ $adminUnreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.profile') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('admin.profile*') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600' }}">Edit Profile</a>
            </div>
        </div>
    </header>

    <!-- Flash Toasts -->
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
        @if (session('error') || $errors->any())
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)" 
                 class="pointer-events-auto flex items-center justify-between gap-3 rounded-2xl bg-rose-950 text-white p-4 shadow-2xl border border-rose-600/40">
                <div class="flex items-center gap-2.5">
                    <span class="h-8 w-8 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center font-black">!</span>
                    <p class="text-xs font-bold">{{ session('error') ?: $errors->first() }}</p>
                </div>
                <button @click="show = false" class="text-rose-300 hover:text-white">&times;</button>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-200 bg-white py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-900">DMDP TrabaGo</span>
                <span>&copy; {{ date('Y') }} Central Administration & Systems Management Division.</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="{{ route('admin.approvals.index') }}" class="hover:text-emerald-700 font-semibold">Approvals Queue</a>
                <a href="{{ route('admin.users.index') }}" class="hover:text-emerald-700 font-semibold">Staff Accounts</a>
                <a href="{{ route('admin.reports') }}" class="hover:text-emerald-700 font-semibold">Platform Analytics</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>