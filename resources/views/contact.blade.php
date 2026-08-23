@extends('layouts.public')

@section('title', 'Contact Us - DMDP Cebu City & TrabaGo Portal')

@section('content')
<div class="min-h-screen bg-slate-50/70" x-data="{
    faqOpen: null,
    inquiryType: 'general',
    submitted: false,
    toggleFaq(index) {
        this.faqOpen = this.faqOpen === index ? null : index;
    }
}">

    <!-- ========================================================================= -->
    <!-- HERO HEADER -->
    <!-- ========================================================================= -->
    <div class="relative overflow-hidden py-14 sm:py-20 text-white" style="background: linear-gradient(135deg, #022c22 0%, #064e3b 50%, #047857 100%);">
        <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full bg-emerald-400/10 blur-3xl"></div>
        <div class="absolute -left-20 -top-20 w-80 h-80 rounded-full bg-teal-400/10 blur-3xl"></div>

        <div class="relative z-10 max-w-6xl mx-auto px-5 text-center space-y-4">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-emerald-400/30 text-xs font-black text-emerald-300">
                <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Official Help & Support Center
            </div>
            
            <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white">
                We're Here to Help You Connect
            </h1>
            
            <p class="text-sm sm:text-base text-emerald-100/90 max-w-2xl mx-auto leading-relaxed">
                Have questions about job placement, employer accreditation, skills training, or inclusive employment? Connect directly with the Cebu City Department of Manpower Development and Placement (DMDP).
            </p>

            <div class="pt-2 flex flex-wrap items-center justify-center gap-3 text-xs font-semibold text-emerald-200">
                <span class="flex items-center gap-1.5 bg-white/10 px-3.5 py-1.5 rounded-full backdrop-blur-sm border border-white/10">
                    🏛️ Cebu City Hall Annex
                </span>
                <span class="flex items-center gap-1.5 bg-white/10 px-3.5 py-1.5 rounded-full backdrop-blur-sm border border-white/10">
                    🕒 Mon – Fri: 8:00 AM – 5:00 PM
                </span>
                <span class="flex items-center gap-1.5 bg-white/10 px-3.5 py-1.5 rounded-full backdrop-blur-sm border border-white/10">
                    ⚡ 24-48h Response Time
                </span>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MAIN CONTACT SECTION -->
    <!-- ========================================================================= -->
    <div class="max-w-6xl mx-auto px-5 -mt-8 sm:-mt-10 relative z-20 pb-16 space-y-12">

        <!-- Flash Success Notification -->
        @if(session('success'))
            <div class="rounded-3xl bg-emerald-600 text-white p-6 shadow-xl flex items-start gap-4 animate-fade-in border border-emerald-400">
                <div class="h-10 w-10 rounded-2xl bg-white/20 flex items-center justify-center text-xl shrink-0">
                    ✓
                </div>
                <div class="flex-1 space-y-1">
                    <h2 class="font-black text-base">Inquiry Successfully Dispatched!</h2>
                    <p class="text-xs text-emerald-100 leading-relaxed">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- LEFT COLUMN: CONTACT CHANNELS & DIRECTORY (5 COLS) -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- Core Contact Info Box -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-emerald-100/80 shadow-sm space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Direct Access</span>
                        <h2 class="text-xl font-black text-slate-900 mt-0.5">DMDP Placement Division</h2>
                    </div>

                    <div class="space-y-4 text-xs">
                        <!-- Address -->
                        <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-slate-50/80 border border-slate-100 hover:border-emerald-200 transition-colors">
                            <div class="h-9 w-9 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-base shrink-0">
                                📍
                            </div>
                            <div class="space-y-0.5">
                                <span class="font-bold text-slate-900 block">Physical Headquarters</span>
                                <p class="text-slate-600 leading-relaxed text-[11px]">
                                    2nd Floor, Cebu City Hall Annex Building, M.C. Briones St., Cebu City, Philippines 6000
                                </p>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-slate-50/80 border border-slate-100 hover:border-emerald-200 transition-colors">
                            <div class="h-9 w-9 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center text-base shrink-0">
                                📞
                            </div>
                            <div class="space-y-0.5">
                                <span class="font-bold text-slate-900 block">Official Telephone & Hotlines</span>
                                <p class="text-slate-600 text-[11px]">(032) 888-1234 (Main Trunkline)</p>
                                <p class="text-slate-600 text-[11px]">(032) 412-5678 (Placement Division)</p>
                                <p class="text-emerald-700 font-bold text-[11px]">+63 917 123 4567 (SMS & Hotline)</p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-slate-50/80 border border-slate-100 hover:border-emerald-200 transition-colors">
                            <div class="h-9 w-9 rounded-xl bg-indigo-100 text-indigo-800 flex items-center justify-center text-base shrink-0">
                                ✉️
                            </div>
                            <div class="space-y-0.5">
                                <span class="font-bold text-slate-900 block">Official Electronic Mail</span>
                                <p class="text-slate-600 text-[11px]">dmdp.cebucity@gmail.com</p>
                                <p class="text-emerald-700 font-bold text-[11px]">support@trabago.cebucity.gov.ph</p>
                            </div>
                        </div>

                        <!-- Operating Hours -->
                        <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-slate-50/80 border border-slate-100 hover:border-emerald-200 transition-colors">
                            <div class="h-9 w-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-base shrink-0">
                                🕒
                            </div>
                            <div class="space-y-0.5">
                                <span class="font-bold text-slate-900 block">Public Service Schedule</span>
                                <p class="text-slate-600 text-[11px]">Monday to Friday: 8:00 AM – 5:00 PM</p>
                                <span class="text-[10px] text-slate-400 block font-medium">Closed on Saturdays, Sundays, and Philippine Public Holidays</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Departmental Routing Directory -->
                <div class="bg-white rounded-3xl p-6 border border-emerald-100/80 shadow-sm space-y-4">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Department Directory</h3>
                    
                    <div class="space-y-2.5 text-xs">
                        <div class="p-3 rounded-2xl bg-emerald-50/60 border border-emerald-200 flex items-center justify-between">
                            <div>
                                <p class="font-bold text-emerald-950">Job Placement Office (JPO)</p>
                                <span class="text-[11px] text-emerald-700">jpo@trabago.cebucity.gov.ph</span>
                            </div>
                            <span class="text-[10px] font-bold bg-emerald-200/80 text-emerald-900 px-2 py-0.5 rounded-md">Referrals</span>
                        </div>

                        <div class="p-3 rounded-2xl bg-teal-50/60 border border-teal-200 flex items-center justify-between">
                            <div>
                                <p class="font-bold text-teal-950">PESD Supervision Division</p>
                                <span class="text-[11px] text-teal-700">pesd@trabago.cebucity.gov.ph</span>
                            </div>
                            <span class="text-[10px] font-bold bg-teal-200/80 text-teal-900 px-2 py-0.5 rounded-md">Accreditations</span>
                        </div>

                        <div class="p-3 rounded-2xl bg-indigo-50/60 border border-indigo-200 flex items-center justify-between">
                            <div>
                                <p class="font-bold text-indigo-950">Skills Training Center</p>
                                <span class="text-[11px] text-indigo-700">training@trabago.cebucity.gov.ph</span>
                            </div>
                            <span class="text-[10px] font-bold bg-indigo-200/80 text-indigo-900 px-2 py-0.5 rounded-md">Certificates</span>
                        </div>

                        <div class="p-3 rounded-2xl bg-purple-50/60 border border-purple-200 flex items-center justify-between">
                            <div>
                                <p class="font-bold text-purple-950">Inclusive & PWD Assistance</p>
                                <span class="text-[11px] text-purple-700">pwd.support@cebucity.gov.ph</span>
                            </div>
                            <span class="text-[10px] font-bold bg-purple-200/80 text-purple-900 px-2 py-0.5 rounded-md">Accessibility</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: INTERACTIVE INQUIRY FORM (7 COLS) -->
            <div class="lg:col-span-7 space-y-6">
                
                <div class="bg-white rounded-3xl p-6 sm:p-10 border border-emerald-100 shadow-sm space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Send a Direct Message</span>
                        <h2 class="text-2xl font-black text-slate-900 mt-1">Inquiry & Support Form</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Please specify your details and inquiry topic so our officers can assist you quickly.</p>
                    </div>

                    @if ($errors->any())
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-800 space-y-1">
                            <span class="font-bold">Please correct the following:</span>
                            <ul class="list-disc list-inside text-[11px] space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- Inquiry Category Pills -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                How Can We Help You? <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="inquiry_type" value="general" class="peer sr-only" {{ old('inquiry_type', 'general') === 'general' ? 'checked' : '' }}>
                                    <div class="p-3 rounded-2xl border border-slate-200 text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-900 peer-checked:font-bold hover:bg-slate-50">
                                        <span class="text-base block">💬</span>
                                        <span class="text-[11px] leading-tight block mt-1">General Info</span>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="inquiry_type" value="jobseeker" class="peer sr-only" {{ old('inquiry_type') === 'jobseeker' ? 'checked' : '' }}>
                                    <div class="p-3 rounded-2xl border border-slate-200 text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-900 peer-checked:font-bold hover:bg-slate-50">
                                        <span class="text-base block">👤</span>
                                        <span class="text-[11px] leading-tight block mt-1">Jobseeker Help</span>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="inquiry_type" value="employer" class="peer sr-only" {{ old('inquiry_type') === 'employer' ? 'checked' : '' }}>
                                    <div class="p-3 rounded-2xl border border-slate-200 text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-900 peer-checked:font-bold hover:bg-slate-50">
                                        <span class="text-base block">🏢</span>
                                        <span class="text-[11px] leading-tight block mt-1">Accreditation</span>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="inquiry_type" value="training" class="peer sr-only" {{ old('inquiry_type') === 'training' ? 'checked' : '' }}>
                                    <div class="p-3 rounded-2xl border border-slate-200 text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-900 peer-checked:font-bold hover:bg-slate-50">
                                        <span class="text-base block">🎓</span>
                                        <span class="text-[11px] leading-tight block mt-1">Skills Training</span>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="inquiry_type" value="pwd" class="peer sr-only" {{ old('inquiry_type') === 'pwd' ? 'checked' : '' }}>
                                    <div class="p-3 rounded-2xl border border-slate-200 text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-900 peer-checked:font-bold hover:bg-slate-50">
                                        <span class="text-base block">♿</span>
                                        <span class="text-[11px] leading-tight block mt-1">PWD Inclusive</span>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="inquiry_type" value="technical" class="peer sr-only" {{ old('inquiry_type') === 'technical' ? 'checked' : '' }}>
                                    <div class="p-3 rounded-2xl border border-slate-200 text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-900 peer-checked:font-bold hover:bg-slate-50">
                                        <span class="text-base block">⚙️</span>
                                        <span class="text-[11px] leading-tight block mt-1">Tech Support</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Name & Email Row -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                    Your Full Name <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="full_name" value="{{ old('full_name') }}" required placeholder="e.g. Maria Santos"
                                       class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                    Email Address <span class="text-rose-500">*</span>
                                </label>
                                <input type="email" name="email" value="{{ old('email') }}" required placeholder="e.g. maria@example.com"
                                       class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            </div>
                        </div>

                        <!-- Phone & Subject Row -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                    Contact / Mobile Number <span class="text-slate-400 font-normal text-[11px]">(Optional)</span>
                                </label>
                                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="e.g. 0917 123 4567"
                                       class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                    Subject Title <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="subject" value="{{ old('subject') }}" required placeholder="e.g. Inquiry regarding Employer Registration"
                                       class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            </div>
                        </div>

                        <!-- Message Body -->
                        <div class="space-y-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Detailed Message / Question <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="message" rows="5" required placeholder="Please provide clear details regarding your inquiry, concerns, or requests for DMDP assistance..."
                                      class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400 leading-relaxed">{{ old('message') }}</textarea>
                        </div>

                        <!-- Privacy Notice & Submit Button -->
                        <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <p class="text-[11px] text-slate-400 max-w-sm">
                                🔒 Your information is processed in strict accordance with the Philippine Data Privacy Act of 2012 (RA 10173).
                            </p>

                            <button type="submit" class="px-8 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-black text-xs shadow-lg shadow-emerald-600/30 transition-all hover:scale-105 flex items-center justify-center gap-2">
                                <span>Send Inquiry to DMDP</span>
                                <span>&rarr;</span>
                            </button>
                        </div>

                    </form>
                </div>

            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- FREQUENTLY ASKED QUESTIONS (FAQ ACCORDION) -->
        <!-- ========================================================================= -->
        <div class="rounded-3xl bg-white border border-emerald-100/80 p-6 sm:p-10 shadow-sm space-y-6">
            <div class="text-center max-w-2xl mx-auto space-y-1">
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Quick Answers</span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900">Frequently Asked Questions</h2>
                <p class="text-xs text-slate-500">Common questions about TrabaGo and Cebu City DMDP programs</p>
            </div>

            <div class="max-w-4xl mx-auto divide-y divide-slate-100 space-y-3 pt-2">
                
                <!-- FAQ 1 -->
                <div class="pt-3">
                    <button @click="toggleFaq(1)" class="w-full flex items-center justify-between text-left py-3 text-sm font-bold text-slate-900 hover:text-emerald-700 transition-colors">
                        <span>How do Employers get officially accredited with DMDP Cebu City?</span>
                        <span class="text-emerald-600 text-base" x-text="faqOpen === 1 ? '−' : '+'"></span>
                    </button>
                    <div x-show="faqOpen === 1" x-cloak class="pb-4 text-xs text-slate-600 leading-relaxed pr-6">
                        Employers submit their business registration (SEC/DTI) and Mayor's/Business Permit via the Employer Portal. The documents are first verified by the Job Placement Officer (JPO), endorsed by the PESD Supervisor, and finalized with official accreditation by the DMDP Administrator.
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="pt-3">
                    <button @click="toggleFaq(2)" class="w-full flex items-center justify-between text-left py-3 text-sm font-bold text-slate-900 hover:text-emerald-700 transition-colors">
                        <span>Are skills training courses free for registered jobseekers?</span>
                        <span class="text-emerald-600 text-base" x-text="faqOpen === 2 ? '−' : '+'"></span>
                    </button>
                    <div x-show="faqOpen === 2" x-cloak class="pb-4 text-xs text-slate-600 leading-relaxed pr-6">
                        Yes! All vocational training modules and laboratory certifications provided through TrabaGo are 100% free of charge sponsored by the City of Cebu. Upon achieving $\ge 80\%$ score on module assessments, digital certificates are awarded.
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="pt-3">
                    <button @click="toggleFaq(3)" class="w-full flex items-center justify-between text-left py-3 text-sm font-bold text-slate-900 hover:text-emerald-700 transition-colors">
                        <span>How does TrabaGo support Persons with Disabilities (PWDs)?</span>
                        <span class="text-emerald-600 text-base" x-text="faqOpen === 3 ? '−' : '+'"></span>
                    </button>
                    <div x-show="faqOpen === 3" x-cloak class="pb-4 text-xs text-slate-600 leading-relaxed pr-6">
                        TrabaGo actively tags PWD-inclusive job vacancies and allows jobseekers to indicate disability accommodation preferences. Our JPOs provide dedicated assistance to ensure matched workplaces provide accessibility adjustments.
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="pt-3">
                    <button @click="toggleFaq(4)" class="w-full flex items-center justify-between text-left py-3 text-sm font-bold text-slate-900 hover:text-emerald-700 transition-colors">
                        <span>How does the AI Cosine-Similarity Matching work?</span>
                        <span class="text-emerald-600 text-base" x-text="faqOpen === 4 ? '−' : '+'"></span>
                    </button>
                    <div x-show="faqOpen === 4" x-cloak class="pb-4 text-xs text-slate-600 leading-relaxed pr-6">
                        Our Skill Matching algorithm compares the vectorized skills profile and preferences of a jobseeker against the technical requirements of active employer job listings, computing a compatibility percentage to fast-track qualified endorsements.
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>
@endsection
