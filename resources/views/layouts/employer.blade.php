@php
    $employerUnreadCount = Auth::check() ? \Illuminate\Support\Facades\DB::table('notifications')->where('user_id', Auth::id())->where('is_read', 0)->count() : 0;
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Employer Portal - TrabaGo DMDP')</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        ::selection { background-color: #16a34a; color: #ffffff; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f3f4f6; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #16a34a; }
    </style>
    @stack('styles')
</head>
<body class="h-full flex flex-col bg-gray-50 text-gray-900 antialiased">

    <!-- Employer Header / Navigation -->
    <header x-data="{ mobileOpen: false, profileOpen: false }" class="sticky top-0 z-40 bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo & Brand Tag -->
                <div class="flex items-center gap-6 xl:gap-8">
                    <a href="{{ route('employer.dashboard') }}" class="flex items-center gap-2.5 group shrink-0">
                        <div class="h-9 w-9 rounded-lg bg-green-600 flex items-center justify-center text-white font-bold text-sm">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-base text-gray-900 tracking-tight leading-none group-hover:text-green-600 transition-colors">
                                Traba<span class="text-green-600">Go</span>
                            </span>
                            <span class="text-[10px] font-semibold text-gray-500 tracking-wider uppercase leading-none mt-1">Employer Portal</span>
                        </div>
                    </a>

                    <!-- Nav links -->
                    <nav class="hidden md:flex items-center gap-1">
                        <a href="{{ route('employer.dashboard') }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('employer.dashboard*') || request()->routeIs('employer.home') ? 'bg-green-50 text-green-700 font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('employer.job-postings') }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('employer.job-postings*') ? 'bg-green-50 text-green-700 font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            Job Postings
                        </a>
                        <a href="{{ route('employer.accreditation') }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('employer.accreditation*') ? 'bg-green-50 text-green-700 font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            Accreditation
                        </a>
                        <a href="{{ route('employer.referred-jobseekers') }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('employer.referred-jobseekers*') || request()->routeIs('employer.applications*') ? 'bg-green-50 text-green-700 font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            Referred Candidates
                        </a>
                        <a href="{{ route('employer.placement-reports') }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('employer.placement-reports*') ? 'bg-green-50 text-green-700 font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            Placement Reports
                        </a>
                    </nav>
                </div>

                <!-- Right Actions: Notifications + Profile Dropdown -->
                <div class="flex items-center gap-2">
                    
                    <!-- Notification Bell Icon -->
                    <a href="{{ route('employer.notifications') }}" 
                       class="relative p-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-colors {{ request()->routeIs('employer.notifications*') ? 'bg-green-50 text-green-700' : '' }}"
                       title="Notifications">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if($employerUnreadCount > 0)
                            <span class="absolute top-1 right-1 flex h-4 min-w-[16px] px-1 items-center justify-center rounded-full bg-green-600 text-[10px] font-bold text-white">
                                {{ $employerUnreadCount > 9 ? '9+' : $employerUnreadCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Profile Dropdown -->
                    <div class="relative" @click.away="profileOpen = false">
                        <button @click="profileOpen = !profileOpen" type="button" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 transition-colors text-left focus:outline-none">
                            <div class="h-8 w-8 rounded-lg bg-gray-900 text-white font-bold text-xs flex items-center justify-center">
                                {{ strtoupper(substr(Auth::user()->email ?? 'E', 0, 1)) }}
                            </div>
                            <div class="hidden lg:flex flex-col">
                                <span class="text-xs font-semibold text-gray-900 leading-none truncate max-w-[120px]">{{ Auth::user()->email }}</span>
                                <span class="text-[10px] text-gray-500 leading-none mt-0.5">Verified Employer</span>
                            </div>
                            <svg class="hidden sm:block h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="profileOpen" x-cloak class="absolute right-0 mt-2 w-56 rounded-xl bg-white p-1.5 shadow-lg border border-gray-200 focus:outline-none z-50">
                            <div class="px-3 py-2 bg-gray-50 rounded-lg mb-1">
                                <p class="text-xs font-bold text-gray-900 truncate">{{ Auth::user()->email }}</p>
                                <p class="text-[10px] text-green-700 font-semibold">DMDP Corporate Partner</p>
                            </div>
                            
                            <a href="{{ route('employer.profile') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-100 hover:text-green-700 transition-colors {{ request()->routeIs('employer.profile*') ? 'bg-green-50 text-green-700 font-bold' : '' }}">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                Company Profile
                            </a>

                            <a href="{{ route('employer.placement-reports') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-100 hover:text-green-700 transition-colors">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                Placement Reports
                            </a>

                            <a href="{{ route('employer.accreditation') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-100 hover:text-green-700 transition-colors">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Accreditation Status
                            </a>
                            
                            <div class="border-t border-gray-100 my-1"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Mobile Hamburger -->
                    <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>

            </div>

            <!-- Mobile Drawer -->
            <div x-show="mobileOpen" x-cloak class="md:hidden border-t border-gray-200 py-2 space-y-1">
                <a href="{{ route('employer.dashboard') }}" class="block px-3 py-2 rounded-lg text-xs font-semibold {{ request()->routeIs('employer.dashboard*') ? 'bg-green-50 text-green-700 font-bold' : 'text-gray-600 hover:bg-gray-100' }}">Dashboard</a>
                <a href="{{ route('employer.job-postings') }}" class="block px-3 py-2 rounded-lg text-xs font-semibold {{ request()->routeIs('employer.job-postings*') ? 'bg-green-50 text-green-700 font-bold' : 'text-gray-600 hover:bg-gray-100' }}">Job Postings</a>
                <a href="{{ route('employer.applications') }}" class="block px-3 py-2 rounded-lg text-xs font-semibold {{ request()->routeIs('employer.applications*') ? 'bg-green-50 text-green-700 font-bold' : 'text-gray-600 hover:bg-gray-100' }}">Applicants</a>
                <a href="{{ route('employer.accreditation') }}" class="block px-3 py-2 rounded-lg text-xs font-semibold {{ request()->routeIs('employer.accreditation*') ? 'bg-green-50 text-green-700 font-bold' : 'text-gray-600 hover:bg-gray-100' }}">Accreditation</a>
                <a href="{{ route('employer.placement-reports') }}" class="block px-3 py-2 rounded-lg text-xs font-semibold {{ request()->routeIs('employer.placement-reports*') ? 'bg-green-50 text-green-700 font-bold' : 'text-gray-600 hover:bg-gray-100' }}">Placement Reports</a>
                <a href="{{ route('employer.notifications') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-semibold {{ request()->routeIs('employer.notifications*') ? 'bg-green-50 text-green-700 font-bold' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span>Notifications</span>
                    @if($employerUnreadCount > 0)
                        <span class="px-1.5 py-0.5 text-[10px] bg-green-600 text-white rounded-full font-bold">{{ $employerUnreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('employer.profile') }}" class="block px-3 py-2 rounded-lg text-xs font-semibold {{ request()->routeIs('employer.profile*') ? 'bg-green-50 text-green-700 font-bold' : 'text-gray-600 hover:bg-gray-100' }}">Edit Profile</a>
            </div>
        </div>
    </header>

    <!-- Flash Toasts -->
    <div class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-md w-full px-4 pointer-events-none">
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 class="pointer-events-auto flex items-center justify-between gap-3 rounded-xl bg-gray-900 text-white p-4 shadow-lg border border-gray-800">
                <p class="text-xs font-medium">{{ session('success') }}</p>
                <button @click="show = false" class="text-gray-400 hover:text-white">&times;</button>
            </div>
        @endif
        @if (session('error') || (isset($errors) && $errors->any()))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)" 
                 class="pointer-events-auto flex items-center justify-between gap-3 rounded-xl bg-gray-900 text-white p-4 shadow-lg border border-gray-800">
                <p class="text-xs font-medium">{{ session('error') ?: (isset($errors) ? $errors->first() : '') }}</p>
                <button @click="show = false" class="text-gray-400 hover:text-white">&times;</button>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-gray-200 bg-white py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-gray-500">
            <div class="flex items-center gap-2">
                <span class="font-bold text-gray-900">DMDP TrabaGo</span>
                <span>&copy; {{ date('Y') }} Cebu City Department of Manpower Development and Placement.</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="{{ route('employer.job-postings') }}" class="hover:text-green-700 font-medium">Post a Job</a>
                <a href="{{ route('employer.applications') }}" class="hover:text-green-700 font-medium">Review Candidates</a>
                <a href="{{ route('employer.accreditation') }}" class="hover:text-green-700 font-medium">Accreditation</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
