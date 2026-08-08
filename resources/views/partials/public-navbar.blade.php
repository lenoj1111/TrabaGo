<header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-brand-100">
    <div class="max-w-6xl mx-auto px-5 h-16 flex items-center justify-between">
        <a href="{{ route('home') }}">
            @include('components.logo')
        </a>

        <nav class="hidden md:flex items-center gap-8">
            <a href="{{ route('home') }}" 
               class="text-sm font-semibold transition-colors duration-150 text-brand-900 hover:text-brand-700">
                Home
            </a>
            <a href="/contact" 
               class="text-sm font-semibold transition-colors duration-150 text-brand-400 hover:text-brand-700">
                Contact Us
            </a>
        </nav>

        <div class="hidden md:flex items-center gap-3">
            <a href="/jobseeker/login"
               class="text-sm font-semibold text-brand-900 hover:text-brand-700 px-3 py-2 transition-colors">
                Log in
            </a>
            <a href="/jobseeker/register"
               class="text-sm font-semibold text-white bg-brand-800 hover:bg-brand-900 rounded-md px-4 py-2.5 shadow-card transition-all duration-150 hover:shadow-card-hover hover:-translate-y-0.5">
                Register
            </a>
        </div>

        <button class="md:hidden text-brand-900" id="mobile-menu-button">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    {{-- Mobile Menu --}}
    <div class="md:hidden border-t border-brand-100 px-5 py-4 flex-col gap-3 bg-white hidden" id="mobile-menu">
        <a href="{{ route('home') }}" 
           class="text-sm font-semibold transition-colors duration-150 text-brand-900 hover:text-brand-700 block py-2">
            Home
        </a>
        <a href="/contact" 
           class="text-sm font-semibold transition-colors duration-150 text-brand-400 hover:text-brand-700 block py-2">
            Contact Us
        </a>
        <a href="/jobseeker/login" 
           class="text-sm font-semibold text-brand-900 block py-2">
            Log in
        </a>
        <a href="/jobseeker/register"
           class="text-sm font-semibold text-white bg-brand-800 rounded-md px-4 py-2 text-center block">
            Register
        </a>
    </div>
</header>

@push('scripts')
<script>
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
        menu.classList.toggle('flex');
    });
</script>
@endpush