@php
    $user = Auth::user();
    $unreadCount = $user ? \App\Models\Notification::where('user_id', $user->user_id)->where('is_read', false)->count() : 0;
    $initial = strtoupper(substr($user->full_name ?? ($user->email ?? 'U'), 0, 1));
@endphp

<header x-data="{ mobileOpen: false, profileOpen: false }" class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-emerald-100 shadow-sm shadow-emerald-950/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            
            <!-- Logo & Brand -->
            <div class="flex items-center gap-8">
                <a href="{{ route('jobseeker.home') }}" class="flex items-center gap-2.5 group">
                    <div class="h-10 w-10 rounded-2xl bg-gradient-to-tr from-emerald-600 via-emerald-500 to-teal-400 flex items-center justify-center text-white font-black text-xl shadow-md shadow-emerald-500/25 group-hover:scale-105 transition-transform">
                        T
                    </div>
                    <div class="flex flex-col">
                        <span class="font-black text-lg text-slate-900 tracking-tight leading-none group-hover:text-emerald-600 transition-colors">
                            Traba<span class="text-emerald-600">Go</span>
                        </span>
                        <span class="text-[10px] font-bold text-emerald-600 tracking-widest uppercase leading-none mt-0.5">DMDP JobMatch</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links (Documents moved to Profile dropdown) -->
                <nav class="hidden md:flex items-center gap-1.5">
                    <a href="{{ route('jobseeker.home') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('jobseeker.home') ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200' : 'text-slate-600 hover:text-emerald-700 hover:bg-slate-50' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('jobseeker.jobs') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ request()->routeIs('jobseeker.jobs*') ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200' : 'text-slate-600 hover:text-emerald-700 hover:bg-slate-50' }}">
                        <span>Jobs & AI Match</span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-600 text-white shadow-xs">AI</span>
                    </a>
                    <a href="{{ route('jobseeker.applications') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('jobseeker.applications*') ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200' : 'text-slate-600 hover:text-emerald-700 hover:bg-slate-50' }}">
                        Applications
                    </a>
                    <a href="{{ route('jobseeker.training') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('jobseeker.training*') ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200' : 'text-slate-600 hover:text-emerald-700 hover:bg-slate-50' }}">
                        Skill Training
                    </a>
                </nav>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-3">

                <!-- Notifications Bell -->
                <a href="{{ route('jobseeker.notifications') }}" 
                   class="relative p-2 rounded-xl text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 transition-colors"
                   title="Notifications">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    @if($unreadCount > 0)
                        <span class="absolute top-1.5 right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-emerald-600 text-[10px] font-extrabold text-white shadow-sm ring-2 ring-white">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </a>

                <!-- Profile Dropdown -->
                <div class="relative" @click.away="profileOpen = false">
                    <button @click="profileOpen = !profileOpen" 
                            type="button" 
                            class="flex items-center gap-2.5 p-1 rounded-xl hover:bg-emerald-50 transition-colors text-left focus:outline-none">
                        <div class="h-9 w-9 rounded-xl bg-slate-900 text-white font-bold text-xs flex items-center justify-center ring-2 ring-emerald-500 shadow-sm">
                            {{ $initial }}
                        </div>
                        <div class="hidden lg:flex flex-col">
                            <span class="text-xs font-bold text-slate-900 leading-none truncate max-w-[120px]">
                                {{ $user->full_name ?? explode('@', $user->email)[0] }}
                            </span>
                            <span class="text-[10px] font-semibold text-emerald-700 leading-none mt-0.5">Jobseeker</span>
                        </div>
                        <svg class="hidden sm:block h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="profileOpen" 
                         x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-60 rounded-2xl bg-white p-2 shadow-2xl border border-emerald-100 focus:outline-none z-50">
                        
                        <div class="px-3 py-2.5 bg-emerald-50/70 rounded-xl mb-1 border border-emerald-100/60">
                            <p class="text-xs font-bold text-slate-900">{{ $user->full_name }}</p>
                            <p class="text-[11px] text-slate-500 truncate">{{ $user->email }}</p>
                        </div>

                        <a href="{{ route('jobseeker.profile') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            My Profile & Skills
                        </a>

                        <a href="{{ route('jobseeker.documents') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            My Documents Hub
                        </a>

                        <a href="{{ route('jobseeker.applications') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Track Applications
                        </a>

                        <a href="{{ route('jobseeker.notifications') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors">
                            <span class="flex items-center gap-2.5">
                                <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                Notifications
                            </span>
                            @if($unreadCount > 0)
                                <span class="px-1.5 py-0.5 text-[10px] bg-emerald-600 text-white rounded-full font-black">{{ $unreadCount }}</span>
                            @endif
                        </a>

                        <div class="border-t border-slate-100 my-1"></div>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors">
                                <svg class="h-4 w-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Mobile Hamburger Button -->
                <button @click="mobileOpen = !mobileOpen" 
                        class="md:hidden p-2 rounded-xl text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 transition-colors">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Drawer -->
        <div x-show="mobileOpen" x-cloak class="md:hidden border-t border-emerald-100 py-3 space-y-1">
            <a href="{{ route('jobseeker.home') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('jobseeker.home') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600 hover:bg-slate-50' }}">
                Dashboard
            </a>
            <a href="{{ route('jobseeker.jobs') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('jobseeker.jobs*') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600 hover:bg-slate-50' }}">
                Jobs & AI Match
            </a>
            <a href="{{ route('jobseeker.applications') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('jobseeker.applications*') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600 hover:bg-slate-50' }}">
                Applications
            </a>
            <a href="{{ route('jobseeker.training') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('jobseeker.training*') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600 hover:bg-slate-50' }}">
                Training Courses
            </a>
            <a href="{{ route('jobseeker.documents') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('jobseeker.documents*') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600 hover:bg-slate-50' }}">
                Document Hub
            </a>
            <a href="{{ route('jobseeker.notifications') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('jobseeker.notifications*') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600 hover:bg-slate-50' }}">
                <span>Notifications</span>
                @if($unreadCount > 0)
                    <span class="px-2 py-0.5 text-[10px] bg-emerald-600 text-white rounded-full font-bold">{{ $unreadCount }}</span>
                @endif
            </a>
            <a href="{{ route('jobseeker.profile') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('jobseeker.profile') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600 hover:bg-slate-50' }}">
                My Profile & Skills
            </a>
        </div>
    </div>
</header>