@extends('layouts.trainer')

@section('title', 'Manage Certificate - ' . $course->title)

@section('content')
<div x-data="{ 
    certTitle: @js($course->cert_content['title'] ?? 'CERTIFICATE OF TRAINING'),
    issueDate: @js($course->cert_content['issue_date'] ?? '1st day of ' . date('F, Y')),
    competencies: @js(implode(" \n", $course->cert_content['competencies'] ?? [])),
    get previewCompetencies() {
    return this.competencies.split('\n').filter(line => line.trim() !== '');
    }
    }" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">

    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
            <a href="{{ route('trainer.courses') }}" class="hover:text-emerald-700 transition-colors">Courses Catalog</a>
            <span>/</span>
            <span class="text-slate-900 font-bold truncate">{{ $course->title }}</span>
            <span>/</span>
            <span class="text-slate-900 font-bold">Certificate Template</span>
        </div>

        <!-- Split Screen -->
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2">

                <!-- LEFT: Form -->
                <div class="p-8 lg:p-12 border-b lg:border-b-0 lg:border-r border-slate-200 bg-slate-50/50">
                    <h1 class="text-2xl font-black text-slate-900 mb-2">Manage Certificate Content</h1>
                    <p class="text-sm text-slate-500 mb-8">Customize the exact text that will appear on the certificate for <strong>{{ $course->title }}</strong>.</p>

                    <form action="{{ route('trainer.courses.certificate-content.update', $course->training_id) }}" method="POST">
                        @csrf
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Certificate Title</label>
                                <input type="text" name="certificate_title" x-model="certTitle" required
                                    class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 font-bold text-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Competencies (One per line)</label>
                                <textarea name="competencies" rows="12" x-model="competencies" required
                                    class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 font-mono text-sm leading-relaxed"></textarea>
                                <p class="text-xs text-slate-400 mt-2">If you enter 10 or more, the certificate will automatically switch to a 2-column layout.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Issue Date (Given this...)</label>
                                <input type="text" name="issue_date" x-model="issueDate" required
                                    class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            </div>
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-xl shadow-lg transition-colors">
                                Save Certificate Content
                            </button>
                        </div>
                    </form>
                </div>

                <!-- RIGHT: Live Preview (Exact Replica) -->
                <div class="p-8 lg:p-12 bg-slate-800 flex flex-col items-center justify-center relative overflow-hidden">
                    <span class="absolute top-6 left-6 text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2 z-10">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span> Live Preview
                    </span>

                    <!-- Scaled A4 Portrait -->
                    <div class="w-full max-w-[450px] aspect-[210/297] relative shadow-2xl bg-white mt-4 shrink-0 overflow-hidden">

                        <!-- The Border Image Background -->
                        <div class="absolute inset-0 z-0"
                            style="background-image: url('{{ asset('images/border-design.png') }}'); background-size: 100% 100%; background-position: center; background-repeat: no-repeat;">
                        </div>

                        <!-- Inner Content -->
                        <div class="relative z-10 h-full w-full flex flex-col justify-between text-center"
                            style="font-family: 'Montserrat', sans-serif; font-weight: 500; padding-top: 12%; padding-left: 12%; padding-right: 12%; padding-bottom: 12%;">

                            <!-- Header (Logos & Official Text) -->
                            <div>
                                <div class="flex justify-center gap-3 mb-2">
                                    <img src="{{ asset('images/cebu.png') }}" class="h-12 w-12 object-contain" alt="Cebu">
                                    <img src="{{ asset('images/dmdp.png') }}" class="h-12 w-12 object-contain" alt="DMDP">
                                </div>
                                <h2 style="font-family: 'Playfair Display', serif; font-size: 12px; color: #0a1d3f; margin: 0; font-weight: 600;">Republic of the Philippines</h2>
                                <h2 style="font-family: 'Playfair Display', serif; font-size: 14px; color: #0a1d3f; margin: 0; font-weight: 600;">City of Cebu</h2>
                                <h2 style="font-family: 'Playfair Display', serif; font-size: 13px; color: #0a1d3f; margin: 0; font-weight: 700;">Department of Manpower Development and Placement</h2>
                            </div>

                            <!-- Title & Name -->
                            <div class="mt-2">
                                <h1 style="font-family: 'Cinzel', serif; font-size: 26px; color: #0a1d3f; font-weight: 800; letter-spacing: 1.5px; margin: 10px 0; text-transform: uppercase;" x-text="certTitle"></h1>
                                <p style="font-family: 'Playfair Display', serif; font-style: italic; font-size: 12px; color: #0a1d3f; margin-bottom: 10px; font-weight: 500;">is awarded to</p>

                                <div class="bg-slate-50 border-y border-slate-200 py-2 my-2 mx-6">
                                    <h2 style="font-family: 'Cinzel', serif; font-size: 24px; color: #0a1d3f; font-weight: 800; letter-spacing: 2px; margin: 0; text-transform: uppercase;">JOHN DOE</h2>
                                </div>
                            </div>

                            <!-- Body -->
                            <div class="mt-2">
                                <p style="font-size: 10px; color: #334155; margin-bottom: 6px;">for having completed the competency based training program in</p>
                                <h3 style="font-family: 'Cinzel', serif; font-size: 16px; color: #0a1d3f; font-weight: 800; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 1px;">
                                    <strong>{{ $course->title }}</strong>
                                </h3>
                                <p style="font-size: 9px; color: #334155; margin-bottom: 12px;">conducted on <strong style="color: #0a1d3f; font-weight: 800;">{{ $course->training_start_date ? \Carbon\Carbon::parse($course->training_start_date)->format('M d') : 'N/A' }} to {{ $course->training_end_date ? \Carbon\Carbon::parse($course->training_end_date)->format('M d, Y') : 'N/A' }}</strong> and satisfactorily achieved the requirements of<br>the following units of competency:</p>

                                <!-- Dynamic Competencies List -->

                                <!-- 1-Column Layout (if less than 10) -->
                                <ul x-show="previewCompetencies.length < 10" class="text-left inline-block text-[9px] leading-tight text-slate-700 max-h-[140px] overflow-hidden" style="list-style-type: none; padding: 0;">
                                    <template x-for="(comp, index) in previewCompetencies" :key="index">
                                        <li class="relative pl-4 mb-0.5">
                                            <span class="absolute left-0 text-[#0a1d3f]">✦</span>
                                            <span x-text="comp"></span>
                                        </li>
                                    </template>
                                </ul>

                                <!-- 2-Column Layout (if 10 or more) -->
                                <div x-show="previewCompetencies.length >= 10" class="flex justify-between w-full gap-4 text-left mt-2 text-[8px] leading-tight text-slate-700 max-h-[140px] overflow-hidden">
                                    <div class="w-1/2">
                                        <template x-for="(comp, index) in previewCompetencies.slice(0, Math.ceil(previewCompetencies.length / 2))" :key="index">
                                            <div class="relative pl-3 mb-0.5">
                                                <span class="absolute left-0 text-[#0a1d3f]">✦</span>
                                                <span x-text="comp"></span>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="w-1/2">
                                        <template x-for="(comp, index) in previewCompetencies.slice(Math.ceil(previewCompetencies.length / 2))" :key="index">
                                            <div class="relative pl-3 mb-0.5">
                                                <span class="absolute left-0 text-[#0a1d3f]">✦</span>
                                                <span x-text="comp"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="mt-4">
                                <p style="font-family: 'Playfair Display', serif; font-size: 10px; color: #334155; margin-bottom: 15px; font-weight: 500;">Given this <strong style="font-family: 'Montserrat', sans-serif; font-weight: 800; color: #0a1d3f;" x-text="issueDate"></strong> at Cebu City, Philippines</p>
                                <div>
                                    <h3 style="font-family: 'Cinzel', serif; font-size: 14px; color: #0a1d3f; font-weight: 800; margin: 0; text-transform: uppercase;">ANTHONY V. AGUHAR, PhD</h3>
                                    <p style="font-size: 9px; color: #0a1d3f; margin: 0; font-weight: 600;">CG Acting Department Head (PESO Manager)</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection