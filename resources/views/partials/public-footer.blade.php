<footer class="bg-brand-950 text-brand-200">
    <div class="max-w-6xl mx-auto px-5 py-12 grid grid-cols-1 sm:grid-cols-3 gap-10">
        <div>
            @include('components.logo', ['dark' => true])
            <p class="mt-4 text-sm text-brand-300 leading-relaxed">
                The official manpower referral, training, and employer management
                platform of the Department of Manpower Development and Placement,
                Cebu City.
            </p>
        </div>
        <div>
            <p class="text-white font-semibold text-sm mb-3">Quick links</p>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a></li>
                <li><a href="/jobseeker/register" class="hover:text-white transition-colors">Register as Job Seeker</a></li>
                <li><a href="/employer/register" class="hover:text-white transition-colors">Register as Employer</a></li>
                <li><a href="/contact" class="hover:text-white transition-colors">Contact Us</a></li>
            </ul>
        </div>
        <div>
            <p class="text-white font-semibold text-sm mb-3">Office</p>
            <ul class="space-y-2 text-sm text-brand-300">
                <li class="flex gap-2">
                    <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    2F Cebu City Hall Annex, Cebu City, 6000
                </li>
                <li class="flex gap-2">
                    <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    (032) 888-1234
                </li>
                <li class="flex gap-2">
                    <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    dmdp@cebucity.gov.ph
                </li>
            </ul>
        </div>
    </div>
    <div class="border-t border-brand-900 py-5 text-center text-xs text-brand-400">
        © 2026 Department of Manpower Development and Placement — Cebu City. All rights reserved.
    </div>
</footer>