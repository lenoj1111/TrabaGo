<header class="sticky top-0 z-40 backdrop-blur-md bg-white/95 border-b border-slate-200/80 transition-all">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-2 group">
            @include('components.logo')
        </a>

        <nav class="hidden md:flex items-center gap-6">
            <a href="{{ route('home') }}" 
               class="text-xs font-bold uppercase tracking-wider py-1.5 px-3 rounded-lg transition-all {{ request()->routeIs('home') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                Home
            </a>
            <a href="{{ route('jobs.index') }}" 
               class="text-xs font-bold uppercase tracking-wider py-1.5 px-3 rounded-lg transition-all {{ request()->routeIs('jobs.*') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                Explore Jobs
            </a>
            <a href="{{ route('contact') }}" 
               class="text-xs font-bold uppercase tracking-wider py-1.5 px-3 rounded-lg transition-all {{ request()->routeIs('contact') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                Contact
            </a>
        </nav>

        <div class="hidden md:flex items-center gap-2.5">
            <a href="{{ route('login') }}"
               class="text-xs font-bold text-slate-700 hover:text-slate-900 px-3.5 py-2 rounded-xl hover:bg-slate-100 transition-colors">
                Sign In
            </a>
            <a href="{{ route('register') }}"
               class="text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:scale-95 rounded-xl px-4 py-2 transition-all shadow-md shadow-emerald-600/20">
                Get Started &rarr;
            </a>
        </div>

        <button class="md:hidden text-slate-700 p-2 rounded-xl hover:bg-slate-100 transition-colors" id="mobile-menu-button" aria-label="Toggle navigation menu">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    {{-- Mobile Menu --}}
    <div class="md:hidden border-t border-slate-100 px-5 py-3 flex-col gap-1 bg-white shadow-lg hidden" id="mobile-menu">
        <a href="{{ route('home') }}" 
           class="text-sm font-semibold {{ request()->routeIs('home') ? 'text-emerald-700 bg-emerald-50' : 'text-slate-700' }} block px-3 py-2 rounded-lg">Home</a>
        <a href="{{ route('jobs.index') }}" 
           class="text-sm font-semibold {{ request()->routeIs('jobs.*') ? 'text-emerald-700 bg-emerald-50' : 'text-slate-700' }} block px-3 py-2 rounded-lg">Explore Jobs</a>
        <a href="{{ route('contact') }}" 
           class="text-sm font-semibold {{ request()->routeIs('contact') ? 'text-emerald-700 bg-emerald-50' : 'text-slate-700' }} block px-3 py-2 rounded-lg">Contact</a>
        <div class="border-t border-slate-100 my-2 pt-2 flex flex-col gap-1.5">
            <a href="{{ route('login') }}" 
               class="text-sm font-semibold text-slate-700 block px-3 py-2 rounded-lg hover:bg-slate-50">Sign In</a>
            <a href="{{ route('register') }}"
               class="text-sm font-bold text-white bg-emerald-600 rounded-xl px-4 py-2.5 text-center block shadow-md shadow-emerald-600/20">
                Get Started &rarr;
            </a>
        </div>
    </div>
</header>

@push('scripts')
<script>
    const btn = document.getElementById('mobile-menu-button');
    if (btn) {
        btn.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
            menu.classList.toggle('flex');
        });
    }
</script>
@endpush