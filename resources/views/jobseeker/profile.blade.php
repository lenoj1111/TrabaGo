@extends('layouts.jobseeker')

@section('title', 'My Profile & Skills Matrix - TrabaGo')

@section('content')
<div x-data="{
    skills: ({{ json_encode($skills) }} || []).map(s => (typeof s === 'object' && s !== null) ? (s.skill_name || '') : String(s)).filter(s => s && s.trim() !== '' && s !== '[object Object]' && s !== 'object Object'),
    newSkill: '',
    addSkill(skillName) {
        let raw = (skillName !== undefined && skillName !== null && typeof skillName === 'string') ? skillName : this.newSkill;
        let trimmed = String(raw || '').trim();
        if (trimmed && trimmed !== '[object Object]' && !this.skills.includes(trimmed)) {
            this.skills.push(trimmed);
            this.newSkill = '';
        }
    },
    removeSkill(index) {
        this.skills.splice(index, 1);
    }
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-5xl space-y-8">
        
        <!-- Profile Banner Card in Emerald Theme -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="h-20 w-20 rounded-3xl bg-gradient-to-tr from-emerald-600 via-emerald-500 to-teal-400 flex items-center justify-center text-white text-3xl font-black shadow-lg shadow-emerald-500/30 ring-4 ring-white/10 shrink-0">
                    {{ strtoupper(substr($user->full_name ?? ($user->email ?? 'U'), 0, 1)) }}
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl sm:text-3xl font-black">{{ $user->full_name }}</h1>
                        <span class="rounded-full bg-emerald-400/20 border border-emerald-400/30 px-2.5 py-0.5 text-[11px] font-bold text-emerald-300">
                            Verified Jobseeker
                        </span>
                    </div>
                    <p class="text-xs text-slate-300">{{ $user->email }} &bull; Member since {{ $user->created_at ? $user->created_at->format('M Y') : '2026' }}</p>
                    <p class="text-xs text-emerald-300 font-semibold">{{ $jobseeker->employment_status ?? 'Actively Seeking Employment' }}</p>
                </div>
            </div>

            <!-- Profile Strength Widget -->
            <div class="shrink-0 bg-white/10 backdrop-blur rounded-2xl p-5 border border-white/10 text-center min-w-[140px]">
                <span class="text-xs font-bold text-emerald-300 uppercase tracking-wider">Profile Strength</span>
                <p class="text-3xl font-black text-emerald-400 mt-0.5">{{ $profileStrength ?? 50 }}%</p>
                <span class="text-[10px] text-slate-300">{{ count($skills) }} Skills Active</span>
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- 1. INTERACTIVE SKILLS MATRIX TAGGER (CRITICAL FOR MATCHING) -->
        <!-- =================================================================== -->
        <section class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-100">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800 border border-emerald-200">
                        <span class="h-2 w-2 rounded-full bg-emerald-600 animate-pulse"></span>
                        AI Vector Skills Matrix
                    </span>
                    <h2 class="text-xl font-black text-slate-900 mt-1">My Skills Profile</h2>
                    <p class="text-xs text-slate-500">Add all skills you possess. The Cosine Similarity matching engine uses this to calculate match scores for all job postings.</p>
                </div>

                <span class="text-xs font-bold text-emerald-800 self-start sm:self-auto" x-text="skills.length + ' Skills Active'"></span>
            </div>

            <form action="{{ route('jobseeker.profile.update_skills') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Add Skill Input Field -->
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <input type="text" 
                               x-model="newSkill" 
                               @keydown.enter.prevent="addSkill()"
                               placeholder="Type a skill (e.g. PHP, Customer Service, React, Welding, SQL)..." 
                               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 placeholder-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    </div>
                    <button type="button" 
                            @click="addSkill()"
                            class="rounded-2xl bg-slate-900 hover:bg-emerald-600 text-white px-6 py-3 text-xs font-bold transition-colors shrink-0">
                        + Add Skill
                    </button>
                </div>

                <!-- Hidden inputs for form submission -->
                <template x-for="(s, idx) in skills" :key="idx">
                    <input type="hidden" name="skills[]" :value="s">
                </template>

                <!-- Active Skills Chip Pool in Emerald Theme -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Your Active Skills:</label>
                    <div class="flex flex-wrap gap-2 min-h-[50px] p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <template x-for="(s, idx) in skills" :key="idx">
                            <span class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 border border-emerald-300 px-3 py-1.5 text-xs font-bold text-emerald-950 shadow-2xs group hover:bg-emerald-100 transition-colors">
                                <span x-text="s"></span>
                                <button type="button" @click="removeSkill(idx)" class="text-emerald-700 hover:text-rose-600 font-bold ml-1 text-sm leading-none">&times;</button>
                            </span>
                        </template>
                        <p x-show="skills.length === 0" class="text-xs text-slate-400 italic">No skills added yet. Add some above or click popular suggestions below!</p>
                    </div>
                </div>

                <!-- Suggested Popular In-Demand Skills in Cebu -->
                <div class="space-y-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Quick-Add In-Demand Cebu Skills:</span>
                    <div class="flex flex-wrap gap-1.5">
                        @php
                            $suggested = [
                                'Customer Service', 'Communication', 'English Fluency', 'Technical Support',
                                'PHP', 'Laravel', 'JavaScript', 'React', 'HTML/CSS', 'MySQL', 'SQL Server',
                                'Administrative Support', 'Data Entry', 'Bookkeeping', 'Sales', 'Graphic Design',
                                'Food Service', 'Barista', 'Welding', 'Automotive', 'Warehouse Management'
                            ];
                        @endphp
                        @foreach($suggested as $sug)
                            <button type="button" 
                                    @click="addSkill('{{ $sug }}')"
                                    :disabled="skills.includes('{{ $sug }}')"
                                    class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:border-emerald-400 hover:bg-emerald-50 hover:text-emerald-800 disabled:opacity-30 disabled:cursor-not-allowed transition-all">
                                + {{ $sug }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Save Skills Button -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <p class="text-[11px] text-slate-400">Match scores across all job openings update immediately after saving.</p>
                    <button type="submit" class="rounded-xl bg-emerald-600 hover:bg-emerald-500 px-8 py-3 text-xs font-black text-white shadow-lg shadow-emerald-600/30 transition-all hover:scale-105">
                        Save Skills Matrix
                    </button>
                </div>
            </form>
        </section>

        <!-- =================================================================== -->
        <!-- 2. OFFICIAL TRAINING CERTIFICATES & CREDENTIALS -->
        <!-- =================================================================== -->
        <section class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-100">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-800 border border-amber-200">
                        ⭐ Official DMDP Credentials
                    </span>
                    <h2 class="text-xl font-black text-slate-900 mt-1">My Certificates of Completion</h2>
                    <p class="text-xs text-slate-500">Government-verified certifications earned through accredited DMDP vocational courses.</p>
                </div>

                <a href="{{ route('jobseeker.documents') }}" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 hover:text-emerald-800 self-start sm:self-auto">
                    Go to Document Hub &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($certificates ?? [] as $cert)
                    <div class="p-5 rounded-2xl bg-gradient-to-tr from-slate-950 to-emerald-950 text-white flex flex-col justify-between gap-4 shadow-md border border-emerald-500/20">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="rounded-full bg-emerald-400/20 text-emerald-300 border border-emerald-400/30 px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider">
                                    {{ ucfirst($cert->training_type ?: 'Online') }} Track
                                </span>
                                <span class="text-[10px] font-mono text-emerald-200 font-bold">{{ $cert->certificate_no }}</span>
                            </div>
                            <h3 class="text-base font-black text-white leading-snug">{{ $cert->course_title }}</h3>
                            <p class="text-[11px] text-slate-300">Awarded on {{ date('F d, Y', strtotime($cert->certificate_issued_at ?? now())) }}</p>
                        </div>

                        <div class="pt-3 border-t border-emerald-800/60 flex items-center justify-between">
                            <span class="text-[10px] font-bold text-emerald-400 flex items-center gap-1">
                                ✓ Verified Credential
                            </span>
                            <a href="{{ route('jobseeker.certificates.preview', $cert->enrollment_id) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-extrabold transition-all shadow-sm">
                                🖨️ View & Download PDF
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-200 p-8 text-center text-slate-400 space-y-2">
                        <div class="text-3xl">🎓</div>
                        <p class="text-xs font-bold text-slate-600">No certificates earned yet</p>
                        <p class="text-[11px] text-slate-400">Complete vocational training programs and pass competency assessments to earn official certificates.</p>
                        <div class="pt-2">
                            <a href="{{ route('jobseeker.training') }}" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 hover:underline">
                                Browse Vocational Courses &rarr;
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- =================================================================== -->
        <!-- 3. PERSONAL DETAILS & CONTACT INFORMATION -->
        <!-- =================================================================== -->
        <section class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="pb-4 border-b border-slate-100">
                <h2 class="text-xl font-black text-slate-900">Personal Information</h2>
                <p class="text-xs text-slate-500">Keep your contact details up to date so Cebu employers can reach out directly.</p>
            </div>

            <form action="{{ route('jobseeker.profile.update_info') }}" method="POST" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $jobseeker->first_name ?? '') }}" required
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $jobseeker->last_name ?? '') }}" required
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Mobile Number</label>
                        <input type="text" name="mobile_number" value="{{ old('mobile_number', $jobseeker->mobile_number ?? '') }}" placeholder="09123456789"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">City / Barangay Address</label>
                        <input type="text" name="address" value="{{ old('address', is_array($details->address ?? null) ? ($details->address['city'] ?? ($details->address['full'] ?? 'Cebu City')) : ($details->address ?? 'Cebu City')) }}"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Professional Bio / Summary</label>
                    <textarea name="bio" rows="3" placeholder="Brief summary of your background, experience, and vocational training..."
                              class="w-full rounded-xl border border-slate-200 p-4 text-xs text-slate-900 focus:border-emerald-500 focus:ring-emerald-400">{{ old('bio', $details->bio ?? '') }}</textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="rounded-xl bg-slate-900 hover:bg-emerald-600 text-white px-6 py-2.5 text-xs font-bold transition-colors">
                        Save Personal Info
                    </button>
                </div>
            </form>
        </section>

    </div>
</div>
@endsection
