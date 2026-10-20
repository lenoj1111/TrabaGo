@extends('layouts.public')

@section('title', 'Contact Us - DMDP Cebu City | TrabaGo')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-slate-50 via-emerald-50/20 to-slate-50 py-8 md:py-14" x-data="{
    faqOpen: 1,
    toggleFaq(index) {
        this.faqOpen = this.faqOpen === index ? null : index;
    }
}">

    <div class="max-w-5xl mx-auto px-4 sm:px-6">

        <!-- Hero Header -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-950 via-emerald-900 to-slate-950 text-white shadow-xl shadow-emerald-950/20 p-6 sm:p-8 md:p-10 mb-8 border border-emerald-800/40">
            <!-- Decorative Glow Accents -->
            <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>
            <div class="absolute left-1/3 -bottom-16 w-56 h-56 rounded-full bg-emerald-400/10 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 text-center max-w-2xl mx-auto space-y-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-400/20 shadow-xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Official Help & Support Center
                </span>
                
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-white tracking-tight">
                    We're Here to Assist You
                </h1>
                
                <p class="text-emerald-100/80 text-xs sm:text-sm sm:leading-relaxed">
                    Have questions about job referrals, employer accreditation, free vocational training, or inclusive PWD placement? Connect directly with the Cebu City DMDP Placement Office.
                </p>

                <div class="pt-3 flex flex-wrap items-center justify-center gap-2.5 text-xs text-emerald-200/80">
                    <span class="bg-emerald-900/50 border border-emerald-800/50 px-3 py-1 rounded-full flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        2F Cebu City Hall Annex
                    </span>
                    <span class="bg-emerald-900/50 border border-emerald-800/50 px-3 py-1 rounded-full flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Mon – Fri: 8:00 AM – 5:00 PM
                    </span>
                    <span class="bg-emerald-900/50 border border-emerald-800/50 px-3 py-1 rounded-full flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        24–48h Response Time
                    </span>
                </div>
            </div>
        </div>

        <!-- Success Flash -->
        @if(session('success'))
            <div class="mb-8 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm flex items-start gap-3 shadow-xs">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <p class="font-bold">Inquiry Successfully Transmitted</p>
                    <p class="text-xs text-emerald-700 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
            
            <!-- Left: Contact Details & Department Directory -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- Core Contact Card -->
                <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-7 space-y-5 shadow-xs">
                    <div class="border-b border-slate-100 pb-3">
                        <p class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Direct Channels</p>
                        <h2 class="text-lg font-black text-slate-900 mt-0.5">DMDP Placement Division</h2>
                    </div>

                    <div class="space-y-3.5 text-xs">
                        <!-- Headquarters -->
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100/80 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <span class="font-bold text-slate-900 block">Physical Headquarters</span>
                                <span class="text-slate-600 leading-normal block mt-0.5">2nd Floor, Cebu City Hall Annex Building, M.C. Briones St., Cebu City, 6000</span>
                            </div>
                        </div>

                        <!-- Telephone & Hotline -->
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100/80 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <span class="font-bold text-slate-900 block">Official Telephones</span>
                                <a href="tel:0328881234" class="text-emerald-700 font-semibold hover:underline block mt-0.5">(032) 888-1234 (Main Annex)</a>
                                <span class="text-slate-600 block">(032) 412-5678 (Direct Placement)</span>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100/80 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <span class="font-bold text-slate-900 block">Official Support Emails</span>
                                <a href="mailto:dmdp@cebucity.gov.ph" class="text-emerald-700 font-semibold hover:underline block mt-0.5">dmdp@cebucity.gov.ph</a>
                                <a href="mailto:support@trabago.cebucity.gov.ph" class="text-slate-600 hover:text-slate-900 block">support@trabago.cebucity.gov.ph</a>
                            </div>
                        </div>

                        <!-- Schedule -->
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100/80 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <span class="font-bold text-slate-900 block">Operating Hours</span>
                                <span class="text-slate-600 block mt-0.5">Monday to Friday: 8:00 AM – 5:00 PM</span>
                                <span class="text-slate-400 text-[11px] block">Closed on Weekends and Philippine National / City Holidays</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Division Directory -->
                <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-7 space-y-3.5 shadow-xs">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Key Department Contacts</p>
                    <div class="space-y-2 text-xs">
                        <div class="p-3 rounded-2xl bg-emerald-50/60 border border-emerald-100 flex items-center justify-between">
                            <div>
                                <p class="font-bold text-slate-900">Job Placement Office (JPO)</p>
                                <span class="text-emerald-800 text-[11px]">jpo@trabago.cebucity.gov.ph</span>
                            </div>
                            <span class="text-[10px] font-bold bg-emerald-200/70 text-emerald-900 px-2.5 py-0.5 rounded-full">Referrals</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-emerald-50/60 border border-emerald-100 flex items-center justify-between">
                            <div>
                                <p class="font-bold text-slate-900">PESD Supervision Division</p>
                                <span class="text-emerald-800 text-[11px]">pesd@trabago.cebucity.gov.ph</span>
                            </div>
                            <span class="text-[10px] font-bold bg-emerald-200/70 text-emerald-900 px-2.5 py-0.5 rounded-full">Accreditations</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-emerald-50/60 border border-emerald-100 flex items-center justify-between">
                            <div>
                                <p class="font-bold text-slate-900">Vocational Training Center</p>
                                <span class="text-emerald-800 text-[11px]">training@trabago.cebucity.gov.ph</span>
                            </div>
                            <span class="text-[10px] font-bold bg-emerald-200/70 text-emerald-900 px-2.5 py-0.5 rounded-full">Courses</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-emerald-50/60 border border-emerald-100 flex items-center justify-between">
                            <div>
                                <p class="font-bold text-slate-900">Inclusive & PWD Desk</p>
                                <span class="text-emerald-800 text-[11px]">pwd.support@cebucity.gov.ph</span>
                            </div>
                            <span class="text-[10px] font-bold bg-emerald-200/70 text-emerald-900 px-2.5 py-0.5 rounded-full">Accessibility</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right: Inquiry Form -->
            <div class="lg:col-span-7">
                <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-9 space-y-6 shadow-xs">
                    <div class="border-b border-slate-100 pb-4">
                        <p class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Send an Official Inquiry</p>
                        <h2 class="text-2xl font-black text-slate-900 mt-1">Direct Message Form</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Please indicate your concern to route your inquiry directly to the right officer.</p>
                    </div>

                    @if ($errors->any())
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-800">
                            <p class="font-bold">Please check the following items:</p>
                            <ul class="list-disc list-inside mt-1 space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4" id="contactForm">
                        @csrf

                        <!-- Inquiry Type Chips -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Inquiry Topic <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                @php $inquiryTypes = [
                                    ['value' => 'general', 'label' => 'General Inquiry', 'icon' => 'info'],
                                    ['value' => 'jobseeker', 'label' => 'Jobseeker Support', 'icon' => 'user'],
                                    ['value' => 'employer', 'label' => 'Accreditation', 'icon' => 'building'],
                                    ['value' => 'training', 'label' => 'Skills Training', 'icon' => 'academic-cap'],
                                    ['value' => 'pwd', 'label' => 'PWD Inclusion', 'icon' => 'accessibility'],
                                    ['value' => 'technical', 'label' => 'Technical Support', 'icon' => 'cog'],
                                ]; @endphp
                                @foreach($inquiryTypes as $type)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="inquiry_type" value="{{ $type['value'] }}" class="peer sr-only" {{ old('inquiry_type', 'general') === $type['value'] ? 'checked' : '' }}>
                                        <div class="p-2.5 rounded-xl border border-slate-200 text-center transition-all duration-150 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-950 peer-checked:ring-2 peer-checked:ring-emerald-500/20 hover:bg-slate-50 text-xs font-semibold text-slate-700 flex items-center justify-center gap-1.5 select-none">
                                            @if($type['icon'] === 'info')
                                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @elseif($type['icon'] === 'user')
                                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            @elseif($type['icon'] === 'building')
                                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            @elseif($type['icon'] === 'academic-cap')
                                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                                            @elseif($type['icon'] === 'accessibility')
                                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="4" r="2"/><path d="M15.5 13.5A4.5 4.5 0 0 1 11 18a4.5 4.5 0 0 1-4.5-4.5A4.5 4.5 0 0 1 11 9c.4 0 .78.06 1.14.16L13.5 6.5C12.7 6.18 11.87 6 11 6a7.5 7.5 0 0 0-7.5 7.5A7.5 7.5 0 0 0 11 21a7.5 7.5 0 0 0 7.42-6.5h-2.92z"/><path d="M19 10h-4.5l-1.3-2.6A2 2 0 0 0 11.4 6.2L8.5 7.6 9.4 9.4l2.1-1 2 4.1V17h2v-4.5l1.6-1.6H19v-2z"/></svg>
                                            @elseif($type['icon'] === 'cog')
                                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            @endif
                                            <span>{{ $type['label'] }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Name & Email -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="full_name">
                                    Full Name <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required placeholder="e.g. Maria Santos"
                                       class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="email">
                                    Email Address <span class="text-rose-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="e.g. maria@example.com"
                                       class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition">
                            </div>
                        </div>

                        <!-- Phone & Subject -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="phone">
                                    Mobile Number <span class="text-slate-400 font-normal lowercase">(optional)</span>
                                </label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="e.g. 0917 123 4567"
                                       class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="subject">
                                    Subject <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required placeholder="e.g. Employer Accreditation Requirements"
                                       class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition">
                            </div>
                        </div>

                        <!-- Message -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="message">
                                Detailed Message <span class="text-rose-500">*</span>
                            </label>
                            <textarea id="message" name="message" rows="5" required placeholder="Please provide specific details regarding your question or application..."
                                      class="w-full px-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition leading-relaxed">{{ old('message') }}</textarea>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <p class="text-[11px] text-slate-400 max-w-sm leading-normal">
                                Your information is protected under the Data Privacy Act of 2012 (RA 10173).
                            </p>
                            <button type="submit" id="submitInquiryBtn" class="px-7 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-sm transition-all shadow-md shadow-emerald-600/25 flex items-center justify-center gap-2">
                                <span>Send Message</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- FAQ Section -->
        <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-10 space-y-6 shadow-xs">
            <div class="text-center max-w-xl mx-auto space-y-1">
                <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Common Inquiries</p>
                <h2 class="text-2xl font-black text-slate-900">Frequently Asked Questions</h2>
                <p class="text-xs text-slate-500">Quick answers regarding TrabaGo and DMDP city services</p>
            </div>

            <div class="max-w-3xl mx-auto divide-y divide-slate-100">
                @php $faqs = [
                    ['q' => 'How do Employers get officially accredited with DMDP Cebu City?', 'a' => "Employers submit their business registration (SEC/DTI) and current Mayor's/Business Permit via the Employer Registration portal. The credentials are investigated and validated by the Job Placement Officer (JPO), endorsed by the PESD Supervisor, and finalized with official accreditation by the DMDP Administrator."],
                    ['q' => 'Are skills training courses free for registered jobseekers?', 'a' => 'Yes! All vocational training modules and laboratory certifications provided through TrabaGo are 100% free of charge, sponsored by the City of Cebu. Upon achieving a passing mark of ≥80% on module assessments, digital certificates are awarded.'],
                    ['q' => 'How does TrabaGo support Persons with Disabilities (PWDs)?', 'a' => 'TrabaGo actively tags PWD-inclusive job vacancies and allows jobseekers to indicate disability accommodation preferences. Our JPOs provide dedicated assistance to ensure matched workplaces provide accessibility adjustments under RA 10524.'],
                    ['q' => 'How does the AI Skill Matching work?', 'a' => 'Our Skill Matching algorithm compares the vectorized skills profile and preferences of a jobseeker against the technical requirements of active employer job listings, computing a compatibility percentage to fast-track qualified endorsements.'],
                ]; @endphp
                @foreach($faqs as $i => $faq)
                    <div class="py-3.5">
                        <button @click="toggleFaq({{ $i + 1 }})" class="w-full flex items-center justify-between text-left py-2 text-sm font-bold text-slate-900 hover:text-emerald-700 transition-colors">
                            <span>{{ $faq['q'] }}</span>
                            <span class="text-emerald-600 text-lg ml-3 shrink-0 font-bold" x-text="faqOpen === {{ $i + 1 }} ? '−' : '+'"></span>
                        </button>
                        <div x-show="faqOpen === {{ $i + 1 }}" x-cloak class="pb-2 pt-1 text-xs text-slate-600 leading-relaxed pr-6">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
    const submitBtn = document.getElementById('submitInquiryBtn');
    if (form && submitBtn) {
        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Transmitting Message...
            `;
        });
    }
});
</script>
@endpush
@endsection
