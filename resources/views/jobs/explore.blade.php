@extends('layouts.public')

@section('title', 'Explore Jobs - DMDP Cebu City | TrabaGo')

@section('content')
<div x-data="{
    detailsModalOpen: false,
    selectedJob: null,
    authModalOpen: false,
    authJobTitle: '',
    authJobCompany: '',
    authJobId: '',
    applyModalOpen: false,
    applyJobId: '',
    applyJobTitle: '',
    applyJobCompany: '',
    openDetails(job) {
        this.selectedJob = job;
        this.detailsModalOpen = true;
    },
    closeDetails() {
        this.detailsModalOpen = false;
        this.selectedJob = null;
    },
    handleApply(job) {
        @guest
            this.authJobTitle = job.title;
            this.authJobCompany = job.employer ? job.employer.company_name : 'Partner Employer';
            this.authJobId = job.job_id;
            this.authModalOpen = true;
            this.detailsModalOpen = false;
        @else
            @if(Auth::user()->role === 'jobseeker')
                this.applyJobId = job.job_id;
                this.applyJobTitle = job.title;
                this.applyJobCompany = job.employer ? job.employer.company_name : 'Partner Employer';
                this.applyModalOpen = true;
                this.detailsModalOpen = false;
            @else
                alert('You are currently signed in as an {{ ucfirst(Auth::user()->role) }}. Please sign in with a Jobseeker account to apply.');
            @endif
        @endguest
    },
    closeAuthModal() { this.authModalOpen = false; },
    closeApplyModal() { this.applyModalOpen = false; }
}" class="min-h-screen bg-gray-50 py-8 md:py-12">

    <div class="max-w-5xl mx-auto px-5">

        <!-- Hero & Search -->
        <div class="bg-green-950 rounded-2xl p-7 md:p-10 text-white mb-8">
            <div class="space-y-3 max-w-2xl">
                <p class="text-xs font-semibold text-green-400 uppercase tracking-widest">Verified Employment Opportunities</p>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                    Explore Jobs in <span class="text-green-400">Cebu City</span>
                </h1>
                <p class="text-green-100/70 text-sm leading-relaxed">
                    Browse pre-screened openings from accredited DMDP partner establishments.
                </p>
            </div>

            <!-- Search -->
            <form action="{{ route('jobs.index') }}" method="GET" class="mt-7 grid grid-cols-1 sm:grid-cols-12 gap-3 pt-5 border-t border-green-800/50">
                <div class="sm:col-span-5 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-green-300/60">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Job title, keywords, or company..." 
                           class="w-full pl-9 pr-4 py-2.5 rounded-lg bg-green-900/60 border border-green-800/50 text-white placeholder-green-300/50 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                </div>
                <div class="sm:col-span-4 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-green-300/60">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    </div>
                    <input type="text" name="location" value="{{ request('location') }}" placeholder="Location (e.g. Cebu City)" 
                           class="w-full pl-9 pr-4 py-2.5 rounded-lg bg-green-900/60 border border-green-800/50 text-white placeholder-green-300/50 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                </div>
                <div class="sm:col-span-3 flex gap-2">
                    <button type="submit" class="w-full py-2.5 rounded-lg bg-green-600 hover:bg-green-700 text-white font-semibold text-sm transition-colors">
                        Search
                    </button>
                    @if(request()->hasAny(['q', 'location', 'pwd_only', 'filter', 'sort']))
                        <a href="{{ route('jobs.index') }}" class="px-3 py-2.5 rounded-lg bg-green-900/50 hover:bg-green-800/60 text-white text-xs font-medium flex items-center justify-center transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            <!-- Filters -->
            <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                <span class="text-green-300/60 font-medium">Filters:</span>
                <a href="{{ route('jobs.index') }}" 
                   class="px-3 py-1 rounded-full text-xs font-medium transition {{ !request('pwd_only') && !request('filter') ? 'bg-white text-green-950' : 'bg-green-900/50 text-green-200 hover:bg-green-800/50' }}">
                    All
                </a>
                <a href="{{ route('jobs.index', array_merge(request()->query(), ['pwd_only' => 1])) }}" 
                   class="px-3 py-1 rounded-full text-xs font-medium transition {{ request('pwd_only') ? 'bg-white text-green-950' : 'bg-green-900/50 text-green-200 hover:bg-green-800/50' }}">
                    PWD-Inclusive
                </a>
            </div>
        </div>

        <!-- Header Count -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
            <div>
                <h2 class="text-lg font-extrabold text-gray-900">Available Positions</h2>
                <p class="text-xs text-gray-500 mt-0.5">Showing {{ $jobs->total() }} active verified position(s)</p>
            </div>
            <div class="text-xs text-gray-400">
                Sorted by: <span class="font-semibold text-gray-700">Most Recent</span>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="mb-5 rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800">
                ✓ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800">
                {{ session('error') }}
            </div>
        @endif
        @if(session('warning'))
            <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm font-medium text-amber-800">
                {{ session('warning') }}
            </div>
        @endif

        <!-- Job Grid -->
        @if($jobs->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-10">
                @foreach($jobs as $job)
                    <div class="bg-white border border-gray-200 hover:border-green-500 rounded-2xl p-6 transition-colors flex flex-col justify-between">
                        <div>
                            <!-- Header -->
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-lg bg-green-50 border border-green-100 flex items-center justify-center text-green-700 font-bold text-sm shrink-0">
                                        {{ substr($job->employer->company_name ?? 'P', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-600 truncate max-w-[200px]">
                                            {{ $job->employer->company_name ?? 'Partner Employer' }}
                                        </p>
                                        @if($job->employer && $job->employer->is_accredited)
                                            <span class="text-[10px] font-semibold text-green-700">✓ DMDP Accredited</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-1 shrink-0">
                                    <span class="text-[11px] font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                                        {{ $job->vacancy_count }} {{ Str::plural('Slot', $job->vacancy_count) }}
                                    </span>
                                    @if($job->accepts_disability)
                                        <span class="text-[10px] font-semibold text-green-700 bg-green-50 px-2 py-0.5 rounded-full" title="{{ $job->disability_type }}">
                                            PWD-Friendly
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Title -->
                            <h3 class="text-lg font-extrabold text-gray-900 mt-1">{{ $job->title }}</h3>

                            <!-- Description -->
                            <p class="text-gray-500 text-sm mt-2 leading-relaxed line-clamp-2">{{ $job->description }}</p>

                            <!-- Skills -->
                            @if(!empty($job->skill_tags))
                                <div class="flex flex-wrap gap-1.5 mt-3">
                                    @foreach(array_slice($job->skill_tags, 0, 3) as $skill)
                                        <span class="text-[11px] font-medium text-green-800 bg-green-50 px-2 py-0.5 rounded-full border border-green-100">{{ $skill }}</span>
                                    @endforeach
                                    @if(count($job->skill_tags) > 3)
                                        <span class="text-[11px] font-medium text-gray-500 bg-gray-50 px-2 py-0.5 rounded-full">+{{ count($job->skill_tags) - 3 }} more</span>
                                    @endif
                                </div>
                            @endif

                            <div class="border-t border-gray-100 my-4"></div>
                        </div>

                        <!-- Bottom: Meta & Buttons -->
                        <div>
                            <div class="flex items-center justify-between text-[11px] text-gray-400 mb-3">
                                <span>Cebu City, Region VII</span>
                                @if($job->valid_until)
                                    <span>Expires: {{ $job->valid_until->format('M d, Y') }}</span>
                                @else
                                    <span>Actively Hiring</span>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 gap-2.5">
                                <button type="button" 
                                        @click="openDetails({{ json_encode($job) }})"
                                        class="w-full py-2.5 px-4 rounded-lg border border-gray-200 hover:border-green-500 bg-white hover:bg-green-50 text-gray-700 font-medium text-sm transition-colors text-center">
                                    View Details
                                </button>
                                <button type="button" 
                                        @click="handleApply({{ json_encode($job) }})"
                                        class="w-full py-2.5 px-4 rounded-lg bg-green-600 hover:bg-green-700 text-white font-semibold text-sm transition-colors text-center">
                                    Apply Now
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mb-10">{{ $jobs->links() }}</div>
        @else
            <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center max-w-md mx-auto my-10">
                <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mx-auto text-xl mb-3">🔍</div>
                <h3 class="text-lg font-extrabold text-gray-900">No Jobs Found</h3>
                <p class="text-sm text-gray-500 mt-1">No vacancies match your current filters.</p>
                <div class="mt-4">
                    <a href="{{ route('jobs.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white font-medium text-sm transition-colors">
                        Clear Filters
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- ======= MODAL 1: JOB DETAILS ======= -->
    <div x-show="detailsModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div x-show="detailsModalOpen" 
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="closeDetails()" class="fixed inset-0 bg-black/50 transition-opacity"></div>

        <div class="flex min-h-screen items-center justify-center p-4">
            <div x-show="detailsModalOpen"
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl border border-gray-200 overflow-hidden">
                
                <template x-if="selectedJob">
                    <div>
                        <!-- Header -->
                        <div class="bg-green-950 p-6 sm:p-7 text-white relative">
                            <button @click="closeDetails()" type="button" class="absolute top-4 right-4 text-white/60 hover:text-white p-1.5 rounded-lg hover:bg-white/10 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            <div class="pr-8">
                                <span class="text-[10px] font-semibold text-green-400 uppercase tracking-wider">Job #<span x-text="selectedJob.job_id"></span></span>
                                <h3 class="text-xl sm:text-2xl font-extrabold text-white mt-1" x-text="selectedJob.title"></h3>
                                <p class="text-green-200/70 text-sm mt-1" x-text="selectedJob.employer ? selectedJob.employer.company_name : 'Partner Employer'"></p>
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="p-6 sm:p-7 space-y-5 max-h-[60vh] overflow-y-auto">
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                <div class="bg-gray-50 rounded-xl p-3">
                                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Slots</p>
                                    <p class="text-sm font-bold text-gray-900 mt-0.5" x-text="selectedJob.vacancy_count + ' Positions'"></p>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-3">
                                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Location</p>
                                    <p class="text-sm font-bold text-gray-900 mt-0.5">Cebu City</p>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-3 col-span-2 sm:col-span-1">
                                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Deadline</p>
                                    <p class="text-sm font-bold text-gray-900 mt-0.5" x-text="selectedJob.valid_until ? new Date(selectedJob.valid_until).toLocaleDateString() : 'Actively Hiring'"></p>
                                </div>
                            </div>

                            <template x-if="selectedJob.accepts_disability">
                                <div class="bg-green-50 border border-green-200 rounded-xl p-3 text-sm text-green-900">
                                    <p class="font-semibold">PWD-Inclusive Position</p>
                                    <p class="text-xs mt-0.5 text-green-700" x-text="selectedJob.disability_type || 'Accommodations available.'"></p>
                                </div>
                            </template>

                            <div>
                                <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Description</h4>
                                <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-700 leading-relaxed whitespace-pre-line" x-text="selectedJob.description"></div>
                            </div>

                            <template x-if="selectedJob.qualifications">
                                <div>
                                    <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Qualifications</h4>
                                    <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-700 leading-relaxed whitespace-pre-line" x-text="selectedJob.qualifications"></div>
                                </div>
                            </template>
                        </div>

                        <!-- Footer -->
                        <div class="bg-gray-50 border-t border-gray-100 p-5 flex flex-col sm:flex-row items-center justify-between gap-3">
                            <button @click="closeDetails()" type="button" class="w-full sm:w-auto px-4 py-2.5 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-100 transition">
                                Close
                            </button>
                            <button @click="handleApply(selectedJob)" type="button" class="w-full sm:w-auto px-5 py-2.5 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm font-semibold transition-colors">
                                Apply for this Position →
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- ======= MODAL 2: AUTH REQUIRED ======= -->
    <div x-show="authModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div x-show="authModalOpen" 
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="closeAuthModal()" class="fixed inset-0 bg-black/50 transition-opacity"></div>

        <div class="flex min-h-screen items-center justify-center p-4">
            <div x-show="authModalOpen"
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative bg-white rounded-2xl shadow-xl w-full max-w-md border border-gray-200 overflow-hidden">
                
                <!-- Header -->
                <div class="bg-green-950 p-6 text-white text-center relative">
                    <button @click="closeAuthModal()" type="button" class="absolute top-4 right-4 text-white/60 hover:text-white p-1.5 rounded-lg hover:bg-white/10 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center mx-auto mb-3 text-white font-bold text-lg">
                        🔐
                    </div>
                    <h3 class="text-xl font-extrabold text-white">Sign In to Apply</h3>
                    <p class="text-green-200/70 text-xs mt-1">
                        Applying for <strong class="text-green-300" x-text="authJobTitle"></strong> at <span x-text="authJobCompany"></span>
                    </p>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-4">
                    <p class="text-sm text-gray-500 text-center leading-relaxed">
                        Sign in or create a jobseeker account to submit your application.
                    </p>
                    <div class="space-y-2.5">
                        <a href="{{ route('login') }}" 
                           class="flex items-center justify-center w-full py-3 px-5 rounded-xl bg-green-600 hover:bg-green-700 text-white font-semibold text-sm transition-colors">
                            Sign In →
                        </a>
                        <a href="{{ route('jobseeker.register') }}" 
                           class="flex items-center justify-center w-full py-3 px-5 rounded-xl border-2 border-green-600 hover:bg-green-50 text-green-700 font-semibold text-sm transition-colors">
                            Create Jobseeker Account →
                        </a>
                    </div>
                    <div class="border-t border-gray-100 pt-3 text-center">
                        <p class="text-[11px] text-gray-400">100% Free Public Employment Service · DMDP Cebu City</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ======= MODAL 3: JOBSEEKER APPLY ======= -->
    @auth
        @if(Auth::user()->role === 'jobseeker')
            <div x-show="applyModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                <div x-show="applyModalOpen" @click="closeApplyModal()" class="fixed inset-0 bg-black/50 transition-opacity"></div>
                <div class="flex min-h-screen items-center justify-center p-4">
                    <div x-show="applyModalOpen" class="relative bg-white rounded-2xl shadow-xl w-full max-w-md border border-gray-200 overflow-hidden">
                        <form :action="'/jobseeker/jobs/' + applyJobId + '/apply'" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="bg-green-950 p-6 text-white relative">
                                <button @click="closeApplyModal()" type="button" class="absolute top-4 right-4 text-white/60 hover:text-white p-1.5 rounded-lg hover:bg-white/10 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                                <p class="text-xs font-semibold text-green-400 uppercase tracking-wider">Submit Application</p>
                                <h3 class="text-xl font-extrabold text-white mt-1" x-text="applyJobTitle"></h3>
                                <p class="text-green-200/70 text-xs mt-1" x-text="applyJobCompany"></p>
                            </div>
                            <div class="p-6 space-y-4">
                                <p class="text-sm text-gray-500">Your profile and skill certificates will be sent to the employer automatically.</p>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Attach Resume (Optional)</label>
                                    <input type="file" name="resume" accept=".pdf,.doc,.docx" 
                                           class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100 file:cursor-pointer">
                                </div>
                                <button type="submit" class="w-full py-3 px-5 rounded-xl bg-green-600 hover:bg-green-700 text-white font-semibold text-sm transition-colors">
                                    Submit Application →
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endauth

</div>
@endsection
