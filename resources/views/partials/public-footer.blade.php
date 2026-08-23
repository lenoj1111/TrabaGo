<footer class="bg-[#022c22] text-emerald-100/80 border-t border-emerald-900/60">
    <div class="max-w-6xl mx-auto px-5 py-14 grid grid-cols-1 sm:grid-cols-3 gap-10">
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-tr from-emerald-600 via-emerald-500 to-teal-400 rounded-2xl flex items-center justify-center text-white font-black text-xl shadow-md shadow-emerald-500/25">
                    💼
                </div>
                <div class="flex flex-col">
                    <h2 class="font-extrabold text-white leading-none text-lg tracking-tight">
                        Traba<span class="text-emerald-400">Go</span>
                    </h2>
                    <small class="text-[10px] font-bold text-emerald-400 tracking-wider uppercase mt-0.5">DMDP Cebu City</small>
                </div>
            </div>
            <p class="text-xs text-emerald-200/80 leading-relaxed max-w-sm">
                The official manpower referral, vocational training assessment, and employer recruitment
                platform of the Department of Manpower Development and Placement, Cebu City.
            </p>
        </div>
        <div>
            <p class="text-white font-black text-xs uppercase tracking-wider mb-3.5 text-emerald-300">Quick Navigation</p>
            <ul class="space-y-2.5 text-xs">
                <li><a href="{{ route('home') }}" class="hover:text-emerald-300 transition-colors">Home Portal</a></li>
                <li><a href="{{ route('jobseeker.home') }}" class="hover:text-emerald-300 transition-colors">Explore Active Jobs</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-emerald-300 transition-colors">Contact Helpdesk</a></li>
                <li><a href="{{ route('jobseeker.register') }}" class="hover:text-emerald-300 transition-colors">Register as Jobseeker</a></li>
                <li><a href="{{ route('employer.register') }}" class="hover:text-emerald-300 transition-colors">Register as Employer</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-emerald-300 transition-colors">Account Login</a></li>
            </ul>
        </div>
        <div>
            <p class="text-white font-black text-xs uppercase tracking-wider mb-3.5 text-emerald-300">DMDP Placement Office</p>
            <ul class="space-y-2.5 text-xs text-emerald-200/80">
                <li class="flex items-start gap-2">
                    <span class="text-emerald-400 mt-0.5">📍</span>
                    <span>2F Cebu City Hall Annex Building, Cebu City, 6000</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-emerald-400">📞</span>
                    <span>(032) 888-1234 / (032) 412-0000</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-emerald-400">✉️</span>
                    <span>dmdp@cebucity.gov.ph</span>
                </li>
            </ul>
        </div>
    </div>
    <div class="border-t border-emerald-900/80 py-5 text-center text-[11px] text-emerald-300/70">
        &copy; {{ date('Y') }} Department of Manpower Development and Placement (DMDP) — Cebu City Government.
    </div>
</footer>