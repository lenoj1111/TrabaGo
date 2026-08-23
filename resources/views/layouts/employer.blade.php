@php
    $employerUnreadCount = Auth::check() ? \Illuminate\Support\Facades\DB::table('notifications')->where('user_id', Auth::id())->where('is_read', 0)->count() : 0;
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Employer Portal - TrabaGo DMDP')</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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

    <!-- Employer Header / Navigation -->
    <header x-data="{ mobileOpen: false, profileOpen: false }" class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-emerald-100 shadow-sm shadow-emerald-950/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo & Brand Tag -->
                <div class="flex items-center gap-6 xl:gap-8">
                    <a href="{{ route('employer.dashboard') }}" class="flex items-center gap-2.5 group shrink-0">
                        <div class="h-10 w-10 rounded-2xl bg-gradient-to-tr from-teal-900 via-emerald-800 to-teal-600 flex items-center justify-center text-white font-black text-xl shadow-md shadow-emerald-900/30 group-hover:scale-105 transition-transform">
                            🏢
                        </div>
                        <div class="flex flex-col">
                            <span class="font-black text-lg text-slate-900 tracking-tight leading-none group-hover:text-emerald-600 transition-colors">
                                Traba<span class="text-emerald-600">Go</span>
                            </span>
                            <span class="text-[10px] font-bold text-emerald-700 tracking-widest uppercase leading-none mt-0.5">Employer Portal</span>
                        </div>
                    </a>

                    <!-- Nav links (Figure 9) -->
                    <nav class="hidden md:flex items-center gap-1">
                        <a href="{{ route('employer.dashboard') }}" 
                           class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('employer.dashboard*') || request()->routeIs('employer.home') ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200' : 'text-slate-600 hover:text-emerald-700 hover:bg-slate-50' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('employer.job-postings') }}" 
                           class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('employer.job-postings*') ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200' : 'text-slate-600 hover:text-emerald-700 hover:bg-slate-50' }}">
                            Job Postings
                        </a>
                        <a href="{{ route('employer.accreditation') }}" 
                           class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('employer.accreditation*') ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200' : 'text-slate-600 hover:text-emerald-700 hover:bg-slate-50' }}">
                            Accreditation
                        </a>
                        <a href="{{ route('employer.referred-jobseekers') }}" 
                           class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('employer.referred-jobseekers*') || request()->routeIs('employer.applications*') ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200' : 'text-slate-600 hover:text-emerald-700 hover:bg-slate-50' }}">
                            Referred Candidates
                        </a>
                        <a href="{{ route('employer.placement-reports') }}" 
                           class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('employer.placement-reports*') ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200' : 'text-slate-600 hover:text-emerald-700 hover:bg-slate-50' }}">
                            Placement Reports
                        </a>
                    </nav>
                </div>

                <!-- Right Actions: Notifications + Profile Dropdown -->
                <div class="flex items-center gap-3">
                    
                    <!-- Notification Bell Icon -->
                    <a href="{{ route('employer.notifications') }}" 
                       class="relative p-2 rounded-xl text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 transition-colors {{ request()->routeIs('employer.notifications*') ? 'bg-emerald-50 text-emerald-800' : '' }}"
                       title="Notifications">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if($employerUnreadCount > 0)
                            <span class="absolute top-1.5 right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-emerald-600 text-[10px] font-extrabold text-white shadow-sm ring-2 ring-white">
                                {{ $employerUnreadCount > 9 ? '9+' : $employerUnreadCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Profile Dropdown -->
                    <div class="relative" @click.away="profileOpen = false">
                        <button @click="profileOpen = !profileOpen" type="button" class="flex items-center gap-2.5 p-1 rounded-xl hover:bg-emerald-50 transition-colors text-left focus:outline-none">
                            <div class="h-9 w-9 rounded-xl bg-slate-900 text-white font-bold text-xs flex items-center justify-center ring-2 ring-emerald-500 shadow-sm">
                                {{ strtoupper(substr(Auth::user()->email ?? 'E', 0, 1)) }}
                            </div>
                            <div class="hidden lg:flex flex-col">
                                <span class="text-xs font-bold text-slate-900 leading-none truncate max-w-[120px]">{{ Auth::user()->email }}</span>
                                <span class="text-[10px] font-semibold text-emerald-700 leading-none mt-0.5">Verified Employer</span>
                            </div>
                            <svg class="hidden sm:block h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="profileOpen" x-cloak class="absolute right-0 mt-2 w-60 rounded-2xl bg-white p-2 shadow-2xl border border-emerald-100 focus:outline-none z-50">
                            <div class="px-3 py-2.5 bg-emerald-50/70 rounded-xl mb-1.5 border border-emerald-100/60">
                                <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->email }}</p>
                                <p class="text-[10px] text-emerald-700 font-bold">DMDP Corporate Partner</p>
                            </div>
                            
                            <a href="{{ route('employer.profile') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors {{ request()->routeIs('employer.profile*') ? 'bg-emerald-50 text-emerald-800' : '' }}">
                                <span>🏢</span> Edit Company Profile
                            </a>

                            <a href="{{ route('employer.notifications') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors {{ request()->routeIs('employer.notifications*') ? 'bg-emerald-50 text-emerald-800' : '' }}">
                                <span class="flex items-center gap-2.5">
                                    <span>🔔</span> Notifications
                                </span>
                                @if($employerUnreadCount > 0)
                                    <span class="px-1.5 py-0.5 text-[10px] bg-emerald-600 text-white rounded-full font-black">{{ $employerUnreadCount }}</span>
                                @endif
                            </a>

                            <a href="{{ route('employer.placement-reports') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors">
                                <span>📊</span> Placement Reports
                            </a>

                            <a href="{{ route('employer.accreditation') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors">
                                <span>🛡️</span> Accreditation Status
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
                    <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-xl text-slate-600 hover:text-emerald-700 hover:bg-emerald-50">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>

            </div>

            <!-- Mobile Drawer -->
            <div x-show="mobileOpen" x-cloak class="md:hidden border-t border-emerald-100 py-3 space-y-1">
                <a href="{{ route('employer.dashboard') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('employer.dashboard*') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600' }}">Dashboard</a>
                <a href="{{ route('employer.job-postings') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('employer.job-postings*') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600' }}">Job Postings</a>
                <a href="{{ route('employer.applications') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('employer.applications*') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600' }}">Applicants</a>
                <a href="{{ route('employer.accreditation') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('employer.accreditation*') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600' }}">Accreditation</a>
                <a href="{{ route('employer.placement-reports') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('employer.placement-reports*') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600' }}">Placement Reports</a>
                <a href="{{ route('employer.notifications') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('employer.notifications*') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600' }}">
                    <span>Notifications</span>
                    @if($employerUnreadCount > 0)
                        <span class="px-2 py-0.5 text-[10px] bg-emerald-600 text-white rounded-full font-bold">{{ $employerUnreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('employer.profile') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('employer.profile*') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600' }}">Edit Profile</a>
            </div>
        </div>
    </header>

    <!-- Flash Toasts -->
    <div class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-md w-full px-4 pointer-events-none">
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 class="pointer-events-auto flex items-center justify-between gap-3 rounded-xl bg-emerald-900 text-white p-4 shadow-xl border border-emerald-700">
                <p class="text-xs font-bold">{{ session('success') }}</p>
                <button @click="show = false" class="text-emerald-300">&times;</button>
            </div>
        @endif
        @if (session('error') || $errors->any())
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)" 
                 class="pointer-events-auto flex items-center justify-between gap-3 rounded-xl bg-rose-900 text-white p-4 shadow-xl border border-rose-700">
                <p class="text-xs font-bold">{{ session('error') ?: $errors->first() }}</p>
                <button @click="show = false" class="text-rose-300">&times;</button>
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
                <span>&copy; {{ date('Y') }} Cebu City Department of Manpower Development and Placement.</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="{{ route('employer.job-postings') }}" class="hover:text-emerald-700 font-semibold">Post a Job</a>
                <a href="{{ route('employer.applications') }}" class="hover:text-emerald-700 font-semibold">Review Candidates</a>
                <a href="{{ route('employer.accreditation') }}" class="hover:text-emerald-700 font-semibold">Accreditation</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
