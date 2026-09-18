@extends('layouts.public')

@section('title', 'Contact Us - DMDP Cebu City & TrabaGo Portal')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="{
    faqOpen: null,
    toggleFaq(index) {
        this.faqOpen = this.faqOpen === index ? null : index;
    }
}">

    <!-- Hero -->
    <div class="bg-green-950 py-12 sm:py-16">
        <div class="max-w-5xl mx-auto px-5 text-center space-y-3">
            <p class="text-xs font-semibold text-green-400 uppercase tracking-widest">Official Help & Support Center</p>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                We're Here to Help You Connect
            </h1>
            <p class="text-sm text-green-100/70 max-w-2xl mx-auto leading-relaxed">
                Have questions about job placement, employer accreditation, skills training, or inclusive employment? Connect directly with the Cebu City DMDP.
            </p>
            <div class="pt-2 flex flex-wrap items-center justify-center gap-3 text-xs font-medium text-green-300/80">
                <span class="bg-green-900/50 px-3 py-1.5 rounded-full">Cebu City Hall Annex</span>
                <span class="bg-green-900/50 px-3 py-1.5 rounded-full">Mon – Fri: 8:00 AM – 5:00 PM</span>
                <span class="bg-green-900/50 px-3 py-1.5 rounded-full">24-48h Response Time</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-5 py-10 space-y-10">

        <!-- Flash Success -->
        @if(session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 p-4 flex items-start gap-3 text-sm font-medium text-green-800">
                <span class="text-green-600 font-bold text-lg">✓</span>
                <div>
                    <p class="font-bold">Inquiry Sent</p>
                    <p class="text-xs text-green-700 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left: Contact Info -->
            <div class="lg:col-span-5 space-y-5">
                
                <!-- Core Contact -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6 space-y-4">
                    <div class="border-b border-gray-100 pb-3">
                        <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Direct Access</p>
                        <h2 class="text-lg font-extrabold text-gray-900 mt-0.5">DMDP Placement Division</h2>
                    </div>

                    <div class="space-y-3 text-sm">
                        <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                            <p class="font-semibold text-gray-900 text-xs">Physical Headquarters</p>
                            <p class="text-gray-600 text-xs mt-0.5">2nd Floor, Cebu City Hall Annex Building, M.C. Briones St., Cebu City, Philippines 6000</p>
                        </div>
                        <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                            <p class="font-semibold text-gray-900 text-xs">Official Telephone & Hotlines</p>
                            <p class="text-gray-600 text-xs mt-0.5">(032) 888-1234 (Main) · (032) 412-5678 (Placement)</p>
                            <p class="text-green-700 font-semibold text-xs mt-0.5">+63 917 123 4567 (SMS & Hotline)</p>
                        </div>
                        <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                            <p class="font-semibold text-gray-900 text-xs">Email</p>
                            <p class="text-gray-600 text-xs mt-0.5">dmdp.cebucity@gmail.com</p>
                            <p class="text-green-700 font-semibold text-xs mt-0.5">support@trabago.cebucity.gov.ph</p>
                        </div>
                        <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                            <p class="font-semibold text-gray-900 text-xs">Service Schedule</p>
                            <p class="text-gray-600 text-xs mt-0.5">Monday to Friday: 8:00 AM – 5:00 PM</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">Closed on Saturdays, Sundays, and Philippine Public Holidays</p>
                        </div>
                    </div>
                </div>

                <!-- Department Directory -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6 space-y-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Department Directory</p>
                    <div class="space-y-2 text-sm">
                        <div class="p-3 rounded-xl bg-green-50 border border-green-100 flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-900 text-xs">Job Placement Office (JPO)</p>
                                <span class="text-green-700 text-[11px]">jpo@trabago.cebucity.gov.ph</span>
                            </div>
                            <span class="text-[10px] font-semibold bg-green-100 text-green-800 px-2 py-0.5 rounded-full">Referrals</span>
                        </div>
                        <div class="p-3 rounded-xl bg-green-50 border border-green-100 flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-900 text-xs">PESD Supervision Division</p>
                                <span class="text-green-700 text-[11px]">pesd@trabago.cebucity.gov.ph</span>
                            </div>
                            <span class="text-[10px] font-semibold bg-green-100 text-green-800 px-2 py-0.5 rounded-full">Accreditations</span>
                        </div>
                        <div class="p-3 rounded-xl bg-green-50 border border-green-100 flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-900 text-xs">Skills Training Center</p>
                                <span class="text-green-700 text-[11px]">training@trabago.cebucity.gov.ph</span>
                            </div>
                            <span class="text-[10px] font-semibold bg-green-100 text-green-800 px-2 py-0.5 rounded-full">Certificates</span>
                        </div>
                        <div class="p-3 rounded-xl bg-green-50 border border-green-100 flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-900 text-xs">Inclusive & PWD Assistance</p>
                                <span class="text-green-700 text-[11px]">pwd.support@cebucity.gov.ph</span>
                            </div>
                            <span class="text-[10px] font-semibold bg-green-100 text-green-800 px-2 py-0.5 rounded-full">Accessibility</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Form -->
            <div class="lg:col-span-7">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 sm:p-8 space-y-5">
                    <div class="border-b border-gray-100 pb-4">
                        <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Send a Direct Message</p>
                        <h2 class="text-xl font-extrabold text-gray-900 mt-1">Inquiry & Support Form</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Specify your details and inquiry topic so our officers can assist you quickly.</p>
                    </div>

                    @if ($errors->any())
                        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-800">
                            <p class="font-bold text-xs">Please correct the following:</p>
                            <ul class="list-disc list-inside text-xs mt-1 space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- Inquiry Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">How Can We Help You? <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                @php $inquiryTypes = [
                                    ['value' => 'general', 'label' => 'General Info'],
                                    ['value' => 'jobseeker', 'label' => 'Jobseeker Help'],
                                    ['value' => 'employer', 'label' => 'Accreditation'],
                                    ['value' => 'training', 'label' => 'Skills Training'],
                                    ['value' => 'pwd', 'label' => 'PWD Inclusive'],
                                    ['value' => 'technical', 'label' => 'Tech Support'],
                                ]; @endphp
                                @foreach($inquiryTypes as $type)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="inquiry_type" value="{{ $type['value'] }}" class="peer sr-only" {{ old('inquiry_type', 'general') === $type['value'] ? 'checked' : '' }}>
                                        <div class="p-3 rounded-xl border border-gray-200 text-center transition-all peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-900 peer-checked:font-semibold hover:bg-gray-50 text-sm text-gray-600">
                                            {{ $type['label'] }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Name & Email -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                                <input type="text" name="full_name" value="{{ old('full_name') }}" required placeholder="e.g. Maria Santos"
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                                <input type="email" name="email" value="{{ old('email') }}" required placeholder="e.g. maria@example.com"
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                            </div>
                        </div>

                        <!-- Phone & Subject -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Number <span class="text-gray-400 text-xs font-normal">(Optional)</span></label>
                                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="e.g. 0917 123 4567"
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Subject <span class="text-red-500">*</span></label>
                                <input type="text" name="subject" value="{{ old('subject') }}" required placeholder="e.g. Employer Registration Inquiry"
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                            </div>
                        </div>

                        <!-- Message -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Message <span class="text-red-500">*</span></label>
                            <textarea name="message" rows="5" required placeholder="Please provide clear details regarding your inquiry..."
                                      class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition leading-relaxed">{{ old('message') }}</textarea>
                        </div>

                        <!-- Submit -->
                        <div class="pt-3 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <p class="text-[11px] text-gray-400 max-w-sm">
                                Your information is processed in accordance with the Philippine Data Privacy Act of 2012 (RA 10173).
                            </p>
                            <button type="submit" class="px-6 py-3 rounded-xl bg-green-600 hover:bg-green-700 text-white font-semibold text-sm transition-colors">
                                Send Inquiry →
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- FAQ -->
        <div class="bg-white border border-gray-200 rounded-2xl p-6 sm:p-10 space-y-5">
            <div class="text-center max-w-2xl mx-auto">
                <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Quick Answers</p>
                <h2 class="text-2xl font-extrabold text-gray-900 mt-1">Frequently Asked Questions</h2>
                <p class="text-sm text-gray-500 mt-1">Common questions about TrabaGo and DMDP programs</p>
            </div>

            <div class="max-w-3xl mx-auto divide-y divide-gray-100">
                @php $faqs = [
                    ['q' => 'How do Employers get officially accredited with DMDP Cebu City?', 'a' => "Employers submit their business registration (SEC/DTI) and Mayor's/Business Permit via the Employer Portal. The documents are first verified by the Job Placement Officer (JPO), endorsed by the PESD Supervisor, and finalized with official accreditation by the DMDP Administrator."],
                    ['q' => 'Are skills training courses free for registered jobseekers?', 'a' => 'Yes! All vocational training modules and laboratory certifications provided through TrabaGo are 100% free of charge, sponsored by the City of Cebu. Upon achieving ≥80% score on module assessments, digital certificates are awarded.'],
                    ['q' => 'How does TrabaGo support Persons with Disabilities (PWDs)?', 'a' => 'TrabaGo actively tags PWD-inclusive job vacancies and allows jobseekers to indicate disability accommodation preferences. Our JPOs provide dedicated assistance to ensure matched workplaces provide accessibility adjustments.'],
                    ['q' => 'How does the AI Cosine-Similarity Matching work?', 'a' => 'Our Skill Matching algorithm compares the vectorized skills profile and preferences of a jobseeker against the technical requirements of active employer job listings, computing a compatibility percentage to fast-track qualified endorsements.'],
                ]; @endphp
                @foreach($faqs as $i => $faq)
                    <div class="py-3">
                        <button @click="toggleFaq({{ $i + 1 }})" class="w-full flex items-center justify-between text-left py-2 text-sm font-semibold text-gray-900 hover:text-green-700 transition-colors">
                            <span>{{ $faq['q'] }}</span>
                            <span class="text-green-600 text-base ml-3 shrink-0" x-text="faqOpen === {{ $i + 1 }} ? '−' : '+'"></span>
                        </button>
                        <div x-show="faqOpen === {{ $i + 1 }}" x-cloak class="pb-2 text-sm text-gray-600 leading-relaxed pr-6">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
@endsection
