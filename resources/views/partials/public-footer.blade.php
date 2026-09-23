<footer class="bg-green-950 text-green-100/80">
    <div class="max-w-5xl mx-auto px-5 py-12 grid grid-cols-1 sm:grid-cols-3 gap-10">
        <div class="space-y-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                    T
                </div>
                <div>
                    <span class="font-bold text-white text-base">Traba<span class="text-green-400">Go</span></span>
                    <span class="block text-[9px] font-semibold text-green-400/80 tracking-wider uppercase">DMDP Cebu City</span>
                </div>
            </div>
            <p class="text-xs text-green-200/60 leading-relaxed max-w-xs">
                The official manpower referral, vocational training, and employer recruitment
                platform of the Department of Manpower Development and Placement, Cebu City.
            </p>
        </div>
        <div>
            <p class="text-xs font-semibold text-green-300 uppercase tracking-wider mb-3">Navigation</p>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('home') }}" class="text-green-200/70 hover:text-white transition-colors">Home</a></li>
                <li><a href="{{ route('jobs.index') }}" class="text-green-200/70 hover:text-white transition-colors">Explore Jobs</a></li>
                <li><a href="{{ route('contact') }}" class="text-green-200/70 hover:text-white transition-colors">Contact</a></li>
                <li><a href="{{ route('jobseeker.register') }}" class="text-green-200/70 hover:text-white transition-colors">Register as Jobseeker</a></li>
                <li><a href="{{ route('employer.register') }}" class="text-green-200/70 hover:text-white transition-colors">Register as Employer</a></li>
                <li><a href="{{ route('login') }}" class="text-green-200/70 hover:text-white transition-colors">Account Login</a></li>
            </ul>
        </div>
        <div>
            <p class="text-xs font-semibold text-green-300 uppercase tracking-wider mb-3">DMDP Office</p>
            <ul class="space-y-2 text-sm text-green-200/70">
                <li>2F Cebu City Hall Annex Building, Cebu City, 6000</li>
                <li>(032) 888-1234 / (032) 412-0000</li>
                <li>dmdp@cebucity.gov.ph</li>
            </ul>
        </div>
    </div>
    <div class="border-t border-green-900 py-4 text-center text-xs text-green-300/50">
        &copy; {{ date('Y') }} Department of Manpower Development and Placement (DMDP) — Cebu City Government.
    </div>
</footer>