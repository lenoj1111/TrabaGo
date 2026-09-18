@php
    $trainerUnreadCount = Auth::check() ? \Illuminate\Support\Facades\DB::table('notifications')->where('user_id', Auth::id())->where('is_read', 0)->count() : 0;
    
    $pendingEnrollmentsCount = 0;
    try {
        $pendingEnrollmentsCount = \Illuminate\Support\Facades\DB::table('training_enrollments')->whereIn('status', ['enrolled', 'pending'])->count();
    } catch (\Throwable $e) {
        $pendingEnrollmentsCount = 0;
    }
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Skills Trainer') - TrabaGo DMDP</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        ::selection { background-color: #16a34a; color: #ffffff; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #16a34a; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased" x-data="{ sidebarOpen: false, profileMenuOpen: false }">

    <!-- POP-UP SIDEBAR DRAWER OVERLAY -->
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-50 flex">
        
        <!-- Dimmed Backdrop -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false" 
             class="fixed inset-0 bg-gray-900/60"></div>

        <!-- Slide-out Sidebar Panel -->
        <aside x-show="sidebarOpen" 
               x-transition:enter="transition ease-out duration-300 transform"
               x-transition:enter-start="-translate-x-full shadow-none"
               x-transition:enter-end="translate-x-0 shadow-xl"
               x-transition:leave="transition ease-in duration-200 transform"
               x-transition:leave-start="translate-x-0 shadow-xl"
               x-transition:leave-end="-translate-x-full shadow-none"
               @keydown.escape.window="sidebarOpen = false"
               class="relative flex-1 flex flex-col max-w-xs sm:max-w-sm w-full bg-gray-900 text-gray-300 border-r border-gray-800 shadow-xl z-50">
            
            <!-- Sidebar Header -->
            <div class="h-16 shrink-0 px-6 flex items-center justify-between border-b border-gray-800 bg-gray-900">
                <a href="{{ route('trainer.dashboard') }}" class="flex items-center gap-2.5 group">
                    <div class="h-9 w-9 rounded-lg bg-green-600 flex items-center justify-center text-white font-bold text-sm">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-base text-white tracking-tight leading-none group-hover:text-green-400 transition-colors">
                            Traba<span class="text-green-400">Go</span>
                        </span>
                        <span class="text-[10px] font-semibold text-gray-400 tracking-wider uppercase leading-none mt-1">Skills Trainer</span>
                    </div>
                </a>
                
                <!-- Close Button -->
                <button @click="sidebarOpen = false" type="button" class="p-1.5 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800 transition-colors focus:outline-none" title="Close Menu (Esc)">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Scrollable Navigation Tabs List -->
            <div class="flex-1 overflow-y-auto px-4 py-4 space-y-5">
                
                <!-- Group: OVERVIEW -->
                <div class="space-y-1">
                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Overview</p>
                    
                    <!-- Dashboard Tab -->
                    <a href="{{ route('trainer.dashboard') }}" 
                       class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('trainer.dashboard*') ? 'bg-green-600 text-white font-bold' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                        <div class="flex items-center gap-3">
                            <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('trainer.dashboard*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span>Dashboard</span>
                        </div>
                    </a>
                </div>

                <!-- Group: TRAINING & SKILLS -->
                <div class="space-y-1">
                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Training & Skills</p>
                    
                    <!-- Training Courses Tab -->
                    <a href="{{ route('trainer.courses') }}" 
                       class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('trainer.courses*') ? 'bg-green-600 text-white font-bold' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                        <div class="flex items-center gap-3">
                            <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('trainer.courses*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span>Training Courses</span>
                        </div>
                    </a>

                    <!-- Skills Enrollment Tab -->
                    <a href="{{ route('trainer.skills.enrollment') }}" 
                       class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('trainer.skills.enrollment*') ? 'bg-green-600 text-white font-bold' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                        <div class="flex items-center gap-3">
                            <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('trainer.skills.enrollment*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                            <span>Skills Enrollment</span>
                        </div>
                    </a>

                    <!-- Enrolled Job Seekers Tab -->
                    <a href="{{ route('trainer.enrollments.index') }}" 
                       class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('trainer.enrollments*') ? 'bg-green-600 text-white font-bold' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                        <div class="flex items-center gap-3">
                            <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('trainer.enrollments*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span>Enrolled Job Seekers</span>
                        </div>
                        @if($pendingEnrollmentsCount > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ request()->routeIs('trainer.enrollments*') ? 'bg-white text-green-800' : 'bg-green-500/20 text-green-300 border border-green-500/30' }}">
                                {{ $pendingEnrollmentsCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Collaborators Tab -->
                    <a href="{{ route('trainer.collaborators') }}" 
                       class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('trainer.collaborators*') ? 'bg-green-600 text-white font-bold' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                        <div class="flex items-center gap-3">
                            <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('trainer.collaborators*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span>Collaborators</span>
                        </div>
                    </a>
                </div>

                <!-- Group: ACCOUNT & SETTINGS -->
                <div class="space-y-1">
                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Account & System</p>
                    
                    <!-- Notifications Tab -->
                    <a href="{{ route('trainer.notifications') }}" 
                       class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('trainer.notifications*') ? 'bg-green-600 text-white font-bold' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                        <div class="flex items-center gap-3">
                            <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('trainer.notifications*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span>Notifications</span>
                        </div>
                        @if($trainerUnreadCount > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-600 text-white">
                                {{ $trainerUnreadCount > 9 ? '9+' : $trainerUnreadCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Profile Tab -->
                    <a href="{{ route('trainer.profile') }}" 
                       class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('trainer.profile*') ? 'bg-green-600 text-white font-bold' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                        <div class="flex items-center gap-3">
                            <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('trainer.profile*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Trainer Profile</span>
                        </div>
                    </a>
                </div>

            </div>

            <!-- Sidebar Bottom: User Info & Logout -->
            <div class="p-3 border-t border-gray-800 bg-gray-900">
                <div class="flex items-center justify-between gap-2 p-2 rounded-xl bg-gray-800/80 border border-gray-700/60">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="h-8 w-8 rounded-lg bg-green-600 text-white font-bold text-xs flex items-center justify-center shrink-0">
                            TRN
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="text-xs font-bold text-white truncate max-w-[130px]">{{ Auth::user()->email ?? 'trainer@trabago.com' }}</span>
                            <span class="text-[10px] text-gray-400 leading-tight">Skills Trainer</span>
                        </div>
                    </div>
                    
                    <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                        @csrf
                        <button type="submit" title="Log Out" class="p-1.5 rounded-lg text-gray-400 hover:text-red-400 hover:bg-gray-700 transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

        </aside>
    </div>

    <!-- MAIN APPLICATION CONTAINER -->
    <div class="flex flex-col min-h-screen w-full">
        
        <!-- Top Navigation Bar -->
        <header class="sticky top-0 z-40 flex h-16 shrink-0 items-center justify-between border-b border-gray-200 bg-white px-4 sm:px-6 lg:px-8">
            
            <!-- Left: Pop-up Menu Trigger Button & Branding -->
            <div class="flex items-center gap-4">
                
                <!-- Pop-up Menu Button -->
                <button @click="sidebarOpen = true" 
                        type="button" 
                        class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gray-900 hover:bg-green-600 text-white transition-colors focus:outline-none group">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <span class="text-xs font-bold uppercase tracking-wider">Menu</span>
                </button>
                
                <!-- Brand Tag -->
                <a href="{{ route('trainer.dashboard') }}" class="flex items-center gap-2 group">
                    <div class="h-8 w-8 rounded-lg bg-green-600 flex items-center justify-center text-white font-bold text-xs">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-sm text-gray-900 tracking-tight leading-none group-hover:text-green-600 transition-colors">
                            Traba<span class="text-green-600">Go</span>
                        </span>
                        <span class="text-[9px] font-semibold text-gray-500 tracking-wider uppercase leading-none mt-0.5">Skills Trainer</span>
                    </div>
                </a>

                <!-- Breadcrumb -->
                <div class="hidden md:flex items-center gap-2 pl-2 border-l border-gray-200">
                    <span class="text-xs text-gray-400">Section</span>
                    <span class="text-gray-300">/</span>
                    <h2 class="text-xs font-bold text-gray-900 tracking-tight">@yield('title', 'Skills Trainer Command Center')</h2>
                </div>
            </div>

            <!-- Right: Status, Notifications & Profile -->
            <div class="flex items-center gap-2">
                
                <!-- Status Badge -->
                <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 border border-green-200 text-[11px] font-semibold text-green-700">
                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                    <span>Trainer Active</span>
                </div>

                <!-- Notifications Bell -->
                <a href="{{ route('trainer.notifications') }}" 
                   class="relative p-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-colors"
                   title="Notifications">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($trainerUnreadCount > 0)
                        <span class="absolute top-1 right-1 flex h-4 min-w-[16px] px-1 items-center justify-center rounded-full bg-red-600 text-[10px] font-bold text-white">
                            {{ $trainerUnreadCount > 9 ? '9+' : $trainerUnreadCount }}
                        </span>
                    @endif
                </a>

                <!-- Profile Dropdown -->
                <div class="relative" @click.away="profileMenuOpen = false">
                    <button @click="profileMenuOpen = !profileMenuOpen" type="button" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 transition-colors text-left focus:outline-none">
                        <div class="h-8 w-8 rounded-lg bg-gray-900 text-white font-bold text-xs flex items-center justify-center">
                            TRN
                        </div>
                        <div class="hidden md:flex flex-col">
                            <span class="text-xs font-bold text-gray-900 leading-none truncate max-w-[140px]">{{ Auth::user()->email ?? 'trainer@trabago.com' }}</span>
                            <span class="text-[10px] text-gray-500 leading-none mt-0.5">Skills Trainer</span>
                        </div>
                        <svg class="hidden md:block h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="profileMenuOpen" x-cloak class="absolute right-0 mt-2 w-56 rounded-xl bg-white p-1.5 shadow-lg border border-gray-200 focus:outline-none z-50">
                        <div class="px-3 py-2 bg-gray-50 rounded-lg mb-1">
                            <p class="text-xs font-bold text-gray-900 truncate">{{ Auth::user()->email ?? 'trainer@trabago.com' }}</p>
                            <p class="text-[10px] text-green-700 font-semibold">DMDP Skills Trainer</p>
                        </div>
                        
                        <a href="{{ route('trainer.profile') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-100 hover:text-green-700 transition-colors">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Edit Profile
                        </a>

                        <a href="{{ route('trainer.courses') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-100 hover:text-green-700 transition-colors">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            Course Modules
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

            </div>

        </header>

        <!-- Flash Toasts -->
        <div class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-md w-full px-4 pointer-events-none">
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                     class="pointer-events-auto flex items-center justify-between gap-3 rounded-xl bg-gray-900 text-white p-4 shadow-lg border border-gray-800">
                    <div class="flex items-center gap-2.5">
                        <span class="h-6 w-6 rounded-lg bg-green-500/20 text-green-400 flex items-center justify-center font-bold text-xs">✓</span>
                        <p class="text-xs font-medium">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-gray-400 hover:text-white">&times;</button>
                </div>
            @endif
            @if (session('error') || (isset($errors) && $errors->any()))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)" 
                     class="pointer-events-auto flex items-center justify-between gap-3 rounded-xl bg-gray-900 text-white p-4 shadow-lg border border-gray-800">
                    <div class="flex items-center gap-2.5">
                        <span class="h-6 w-6 rounded-lg bg-red-500/20 text-red-400 flex items-center justify-center font-bold text-xs">!</span>
                        <p class="text-xs font-medium">{{ session('error') ?: (isset($errors) ? $errors->first() : '') }}</p>
                    </div>
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
                    <span>&copy; {{ date('Y') }} Skills Training & Certification Division.</span>
                </div>
                <div class="flex items-center gap-6">
                    <a href="{{ route('trainer.enrollments.index') }}" class="hover:text-green-700 font-medium">Manage Enrollments</a>
                    <a href="{{ route('trainer.courses') }}" class="hover:text-green-700 font-medium">Course Modules</a>
                </div>
            </div>
        </footer>

    </div>

    @stack('scripts')
</body>
</html>
