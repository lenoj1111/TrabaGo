<header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-emerald-100 shadow-sm shadow-emerald-950/5">
    <div class="max-w-6xl mx-auto px-5 h-16 flex items-center justify-between">
        <a href="{{ route('home') }}">
            @include('components.logo')
        </a>

        <nav class="hidden md:flex items-center gap-8">
            <a href="{{ route('home') }}" 
               class="text-xs font-bold transition-colors {{ request()->routeIs('home') ? 'text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl' : 'text-slate-600 hover:text-emerald-700' }}">
                Home
            </a>
            <a href="/jobseeker/home" 
               class="text-xs font-bold text-slate-600 hover:text-emerald-700 transition-colors">
                Explore Jobs
            </a>
            <a href="{{ route('contact') }}" 
               class="text-xs font-bold transition-colors {{ request()->routeIs('contact') ? 'text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl' : 'text-slate-600 hover:text-emerald-700' }}">
                Contact Us
            </a>
        </nav>

        <div class="hidden md:flex items-center gap-3">
            <a href="{{ route('login') }}"
               class="text-xs font-bold text-slate-700 hover:text-emerald-700 px-3.5 py-2 rounded-xl hover:bg-emerald-50 transition-colors">
                Log In
            </a>
            <a href="{{ route('jobseeker.register') }}"
               class="text-xs font-black text-white bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 rounded-xl px-4 py-2.5 shadow-md shadow-emerald-600/25 transition-all hover:scale-105">
                Register &rarr;
            </a>
        </div>

        <button class="md:hidden text-slate-800 p-2 rounded-xl hover:bg-emerald-50" id="mobile-menu-button">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    {{-- Mobile Menu --}}
    <div class="md:hidden border-t border-emerald-100 px-5 py-4 flex-col gap-2 bg-white hidden" id="mobile-menu">
        <a href="{{ route('home') }}" 
           class="text-xs font-bold text-slate-800 hover:text-emerald-700 block py-2">
            Home
        </a>
        <a href="{{ route('jobseeker.home') }}" 
           class="text-xs font-bold text-slate-800 hover:text-emerald-700 block py-2">
            Explore Jobs
        </a>
        <a href="{{ route('contact') }}" 
           class="text-xs font-bold {{ request()->routeIs('contact') ? 'text-emerald-700 font-black' : 'text-slate-700 hover:text-emerald-700' }} block py-2">
            Contact Us
        </a>
        <div class="border-t border-slate-100 my-2 pt-2 flex flex-col gap-2">
            <a href="{{ route('login') }}" 
               class="text-xs font-bold text-slate-700 block py-2">
                Log In
            </a>
            <a href="{{ route('jobseeker.register') }}"
               class="text-xs font-black text-white bg-gradient-to-r from-emerald-600 to-teal-500 rounded-xl px-4 py-2.5 text-center block shadow-md shadow-emerald-600/25">
                Register as Jobseeker
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