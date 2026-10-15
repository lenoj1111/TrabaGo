@php
    $user = Auth::user();
    $unreadCount = $user ? \Illuminate\Support\Facades\Cache::remember('notif_count_' . $user->user_id, 10, fn() => \App\Models\Notification::where('user_id', $user->user_id)->where('is_read', false)->count()) : 0;
    $initial = strtoupper(substr($user->full_name ?? ($user->email ?? 'U'), 0, 1));
@endphp

<header x-data="{ 
    mobileOpen: false, 
    profileOpen: false, 
    unreadCount: {{ $unreadCount }} 
}" 
x-init="
    if (window.Echo && {{ $user ? $user->user_id : 'null' }}) {
        window.Echo.private('App.Models.User.{{ $user ? $user->user_id : '' }}')
            .listen('.notification.created', (e) => {
                unreadCount++;
                if (typeof toastr !== 'undefined') {
                    toastr.info(e.notification.message, e.notification.title);
                }
            });
    }
"
class="sticky top-0 z-40 bg-white border-b border-slate-200 shadow-xs">
    <div class="w-full max-w-[1680px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            
            <!-- Logo & Brand -->
            <div class="flex items-center gap-8">
                <a href="{{ route('jobseeker.home') }}" class="flex items-center gap-2.5 group">
                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white font-black text-sm shadow-md shadow-emerald-950/40 group-hover:scale-105 transition-transform">
                        T
                    </div>
                    <div class="flex flex-col">
                        <span class="font-extrabold text-base text-slate-900 tracking-tight leading-none group-hover:text-emerald-600 transition-colors">
                            Traba<span class="text-emerald-600">Go</span>
                        </span>
                        <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase leading-none mt-1">DMDP JobMatch</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-1">
                    <a href="{{ route('jobseeker.home') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('jobseeker.home') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('jobseeker.jobs') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-1.5 {{ request()->routeIs('jobseeker.jobs*') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <span>Jobs & AI Match</span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-black bg-emerald-600 text-white shadow-xs">AI</span>
                    </a>
                    <a href="{{ route('jobseeker.applications') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('jobseeker.applications*') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Applications
                    </a>
                    <a href="{{ route('jobseeker.training') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('jobseeker.training*') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Training & Skills
                    </a>
                </nav>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-2">

                <!-- Notifications Bell -->
                <a href="{{ route('jobseeker.notifications') }}" 
                   class="relative p-2 rounded-xl text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500"
                   title="Notifications">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span x-show="unreadCount > 0"
                          x-text="unreadCount > 9 ? '9+' : unreadCount"
                          class="absolute top-1 right-1 flex h-4 min-w-[16px] px-1 items-center justify-center rounded-full bg-emerald-600 text-[10px] font-black text-white shadow-xs">
                    </span>
                </a>

                <!-- Profile Dropdown -->
                <div class="relative" @click.away="profileOpen = false">
                    <button @click="profileOpen = !profileOpen" 
                            type="button" 
                            class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-slate-100 transition-colors text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                        <div class="h-8 w-8 rounded-lg bg-slate-900 text-white font-bold text-xs flex items-center justify-center shadow-sm">
                            {{ $initial }}
                        </div>
                        <div class="hidden lg:flex flex-col">
                            <span class="text-xs font-bold text-slate-900 leading-none truncate max-w-[120px]">
                                {{ $user->full_name ?? explode('@', $user->email)[0] }}
                            </span>
                            <span class="text-[10px] text-slate-500 leading-none mt-0.5">Jobseeker</span>
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
                         class="absolute right-0 mt-2 w-56 rounded-2xl bg-white p-2 shadow-xl border border-slate-200 focus:outline-none z-50">
                        
                        <div class="px-3 py-2 bg-slate-50 rounded-xl mb-1 border border-slate-100">
                            <p class="text-xs font-bold text-slate-900 truncate">{{ $user->full_name }}</p>
                            <p class="text-[11px] text-slate-500 truncate">{{ $user->email }}</p>
                        </div>

                        <a href="{{ route('jobseeker.profile', ['tab' => 'view']) }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-medium text-slate-700 hover:bg-slate-100 hover:text-emerald-700 transition-colors">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            My Profile
                        </a>

                        <a href="{{ route('jobseeker.profile', ['tab' => 'edit']) }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-medium text-slate-700 hover:bg-slate-100 hover:text-emerald-700 transition-colors">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Update Profile
                        </a>

                        <a href="{{ route('jobseeker.profile', ['tab' => 'security']) }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-medium text-slate-700 hover:bg-slate-100 hover:text-emerald-700 transition-colors">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Reset Password
                        </a>

                        <a href="{{ route('jobseeker.documents') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-medium text-slate-700 hover:bg-slate-100 hover:text-emerald-700 transition-colors">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            My Documents Hub
                        </a>

                        <a href="{{ route('jobseeker.applications') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-medium text-slate-700 hover:bg-slate-100 hover:text-emerald-700 transition-colors">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Track Applications
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

                <!-- Mobile Hamburger Button -->
                <button @click="mobileOpen = !mobileOpen" 
                        class="md:hidden p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Drawer -->
        <div x-show="mobileOpen" x-cloak class="md:hidden border-t border-slate-200 py-3 space-y-1">
            <a href="{{ route('jobseeker.home') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('jobseeker.home') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60' : 'text-slate-600 hover:bg-slate-100' }}">
                Dashboard
            </a>
            <a href="{{ route('jobseeker.jobs') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('jobseeker.jobs*') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60' : 'text-slate-600 hover:bg-slate-100' }}">
                Jobs & AI Match
            </a>
            <a href="{{ route('jobseeker.applications') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('jobseeker.applications*') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60' : 'text-slate-600 hover:bg-slate-100' }}">
                Applications
            </a>
            <a href="{{ route('jobseeker.training') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('jobseeker.training*') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60' : 'text-slate-600 hover:bg-slate-100' }}">
                Training & Skills
            </a>
            <a href="{{ route('jobseeker.documents') }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('jobseeker.documents*') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60' : 'text-slate-600 hover:bg-slate-100' }}">
                Document Hub
            </a>
            <a href="{{ route('jobseeker.notifications') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('jobseeker.notifications*') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>Notifications</span>
                <span x-show="unreadCount > 0"
                      x-text="unreadCount > 9 ? '9+' : unreadCount"
                      class="px-2 py-0.5 text-[10px] bg-emerald-600 text-white rounded-full font-black"></span>
            </a>
            <a href="{{ route('jobseeker.profile', ['tab' => 'view']) }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('jobseeker.profile') && request('tab', 'view') === 'view' ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60' : 'text-slate-600 hover:bg-slate-100' }}">
                My Profile
            </a>
            <a href="{{ route('jobseeker.profile', ['tab' => 'edit']) }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('jobseeker.profile') && request('tab') === 'edit' ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60' : 'text-slate-600 hover:bg-slate-100' }}">
                Update Profile
            </a>
            <a href="{{ route('jobseeker.profile', ['tab' => 'security']) }}" class="block px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('jobseeker.profile') && request('tab') === 'security' ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60' : 'text-slate-600 hover:bg-slate-100' }}">
                Reset Password
            </a>
        </div>
    </div>
</header>