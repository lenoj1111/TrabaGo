<header class="sticky top-0 z-40 bg-white border-b border-gray-100">
    <div class="max-w-5xl mx-auto px-5 h-14 flex items-center justify-between">
        <a href="{{ route('home') }}">
            @include('components.logo')
        </a>

        <nav class="hidden md:flex items-center gap-7">
            <a href="{{ route('home') }}" 
               class="text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'text-green-700 font-semibold' : 'text-gray-500 hover:text-gray-900' }}">
                Home
            </a>
            <a href="{{ route('jobs.index') }}" 
               class="text-sm font-medium transition-colors {{ request()->routeIs('jobs.*') ? 'text-green-700 font-semibold' : 'text-gray-500 hover:text-gray-900' }}">
                Explore Jobs
            </a>
            <a href="{{ route('contact') }}" 
               class="text-sm font-medium transition-colors {{ request()->routeIs('contact') ? 'text-green-700 font-semibold' : 'text-gray-500 hover:text-gray-900' }}">
                Contact
            </a>
        </nav>

        <div class="hidden md:flex items-center gap-2">
            <a href="{{ route('login') }}"
               class="text-sm font-medium text-gray-600 hover:text-gray-900 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                Log In
            </a>
            <a href="{{ route('register') }}"
               class="text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-lg px-4 py-2 transition-colors">
                Get Started
            </a>
        </div>

        <button class="md:hidden text-gray-600 p-2 rounded-lg hover:bg-gray-50" id="mobile-menu-button">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    {{-- Mobile Menu --}}
    <div class="md:hidden border-t border-gray-100 px-5 py-3 flex-col gap-1 bg-white hidden" id="mobile-menu">
        <a href="{{ route('home') }}" 
           class="text-sm font-medium text-gray-700 hover:text-green-700 block py-2">Home</a>
        <a href="{{ route('jobs.index') }}" 
           class="text-sm font-medium {{ request()->routeIs('jobs.*') ? 'text-green-700 font-semibold' : 'text-gray-700 hover:text-green-700' }} block py-2">Explore Jobs</a>
        <a href="{{ route('contact') }}" 
           class="text-sm font-medium {{ request()->routeIs('contact') ? 'text-green-700 font-semibold' : 'text-gray-700 hover:text-green-700' }} block py-2">Contact</a>
        <div class="border-t border-gray-100 my-2 pt-2 flex flex-col gap-1">
            <a href="{{ route('login') }}" 
               class="text-sm font-medium text-gray-700 block py-2">Log In</a>
            <a href="{{ route('register') }}"
               class="text-sm font-semibold text-white bg-green-600 rounded-lg px-4 py-2 text-center block">
                Get Started
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