@extends('layouts.employer')

@section('title', 'Referred Candidates & Applicants - Employer Portal')

@section('content')
<div x-data="{ 
    interviewModal: false, 
    notQualifiedModal: false,
    resignationModal: false,
    offerModal: false,
    viewModal: false,
    resignationAction: 'approve',
    selectedAppId: null, 
    selectedName: '',
    selectedApp: null,
    offerSalary: '',
    offerStartDate: '',
    offerNotes: '',
    openView(app) {
        this.selectedApp = app;
        this.viewModal = true;
    },
    openInterview(id, name) {
        this.selectedAppId = id;
        this.selectedName = name;
        this.interviewModal = true;
    },
    openNotQualified(id, name) {
        this.selectedAppId = id;
        this.selectedName = name;
        this.notQualifiedModal = true;
    },
    openResignation(id, name, action) {
        this.selectedAppId = id;
        this.selectedName = name;
        this.resignationAction = action;
        this.resignationModal = true;
    },
    openOffer(id, name, salary = '', startDate = '', notes = '') {
        this.selectedAppId = id;
        this.selectedName = name;
        this.offerSalary = salary;
        this.offerStartDate = startDate;
        this.offerNotes = notes;
        this.offerModal = true;
    }
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="w-full max-w-[1680px] mx-auto space-y-8">

        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    DMDP Talent Evaluation Hub
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Candidate Evaluations</h1>
                <p class="text-sm text-slate-300">Review certified jobseekers endorsed by DMDP Placement Officers or direct applicants. Schedule interviews, send formal job offers, or mark candidates as Not Qualified.</p>
            </div>

            <div class="shrink-0 bg-white/10 backdrop-blur rounded-2xl p-5 border border-white/10 text-center min-w-[160px]">
                <span class="text-xs font-bold text-emerald-300 uppercase tracking-wider">Total Candidates</span>
                <p class="text-3xl font-black text-emerald-400 mt-0.5">{{ $stats['total'] ?? $referredApplicants->total() }}</p>
                <span class="text-[10px] text-slate-300">{{ $stats['hired'] ?? 0 }} Hired &bull; {{ $stats['not_qualified'] ?? 0 }} Not Qualified</span>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
        @endif
        @if(session('info'))
        <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-xs font-bold flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('info') }}</span>
        </div>
        @endif
        @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2">
            <svg class="w-4 h-4 text-rose-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        <!-- Metric Summary Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-6 gap-3">
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
                <span class="text-[11px] font-bold text-slate-400 uppercase">Total Roster</span>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-amber-200 bg-amber-50/20 shadow-sm">
                <span class="text-[11px] font-bold text-amber-700 uppercase">Awaiting Action</span>
                <p class="text-2xl font-black text-amber-700 mt-1">{{ $stats['pending'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-emerald-200 bg-emerald-50/20 shadow-sm">
                <span class="text-[11px] font-bold text-emerald-700 uppercase">Interviewing</span>
                <p class="text-2xl font-black text-emerald-700 mt-1">{{ $stats['interview'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-blue-200 bg-blue-50/20 shadow-sm">
                <span class="text-[11px] font-bold text-blue-700 uppercase">Offers Extended</span>
                <p class="text-2xl font-black text-blue-700 mt-1">{{ $stats['offered'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-emerald-200 bg-emerald-50/20 shadow-sm">
                <span class="text-[11px] font-bold text-emerald-700 uppercase">Hired Candidates</span>
                <p class="text-2xl font-black text-emerald-700 mt-1">{{ $stats['hired'] ?? 0 }}</p>
            </div>
        </div>

        <!-- Filter Tabs & Controls -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-1.5 bg-white p-1.5 rounded-2xl border border-slate-200 shadow-sm text-xs font-bold">
                <a href="{{ route('employer.referred-jobseekers', array_merge(request()->except('page', 'status'), ['status' => 'all'])) }}"
                    class="px-3 py-1.5 rounded-xl transition-colors {{ (!request()->has('status') || request()->status === 'all') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    All Candidates
                </a>
                <a href="{{ route('employer.referred-jobseekers', array_merge(request()->except('page'), ['status' => 'pending'])) }}"
                    class="px-3 py-1.5 rounded-xl transition-colors {{ request()->status === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    Pending ({{ $stats['pending'] ?? 0 }})
                </a>
                <a href="{{ route('employer.referred-jobseekers', array_merge(request()->except('page'), ['status' => 'interview'])) }}"
                    class="px-3 py-1.5 rounded-xl transition-colors {{ request()->status === 'interview' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    Interview ({{ $stats['interview'] ?? 0 }})
                </a>
                <a href="{{ route('employer.referred-jobseekers', array_merge(request()->except('page'), ['status' => 'offered'])) }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl transition-colors {{ request()->status === 'offered' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    <span>Offered ({{ $stats['offered'] ?? 0 }})</span>
                </a>
                <a href="{{ route('employer.referred-jobseekers', array_merge(request()->except('page'), ['status' => 'hired'])) }}"
                    class="px-3 py-1.5 rounded-xl transition-colors {{ request()->status === 'hired' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    Hired ({{ $stats['hired'] ?? 0 }})
                </a>

                @if(($stats['resignation_requested'] ?? 0) > 0)
                <a href="{{ route('employer.referred-jobseekers', array_merge(request()->except('page'), ['status' => 'resignation_requested'])) }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl transition-colors {{ request()->status === 'resignation_requested' ? 'bg-amber-600 text-white shadow-sm' : 'text-amber-800 bg-amber-50 border border-amber-300 hover:bg-amber-100 animate-pulse' }}">
                    <span>Resignations ({{ $stats['resignation_requested'] }})</span>
                </a>
                @endif
            </div>

            <form method="GET" action="{{ route('employer.referred-jobseekers') }}" class="flex flex-wrap items-center gap-2 max-w-lg w-full">
                @if(request()->filled('status'))
                <input type="hidden" name="status" value="{{ request()->status }}">
                @endif

                @if(isset($employerJobs) && count($employerJobs) > 0)
                <select name="job_id" onchange="this.form.submit()" class="rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-800 bg-white focus:border-emerald-500 focus:outline-none">
                    <option value="">All Job Vacancies</option>
                    @foreach($employerJobs as $ej)
                    <option value="{{ $ej->job_id }}" {{ request('job_id') == $ej->job_id ? 'selected' : '' }}>
                        {{ $ej->title }}
                    </option>
                    @endforeach
                </select>
                @endif

                <div class="relative flex-1 min-w-[160px]">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search candidate name..."
                        class="w-full rounded-xl border border-slate-200 pl-8 pr-3 py-2 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none">
                    <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold transition-colors">
                    Filter
                </button>
            </form>
        </div>

        <!-- Referred Applicants Grid -->
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($referredApplicants as $app)
                @php
                $jobseeker = $app->jobseeker;
                $job = $app->jobPosting;
                $fullName = trim(($jobseeker->first_name ?? '') . ' ' . ($jobseeker->last_name ?? ''));

                $uploadedDocs = is_array($jobseeker->details?->training_certificates)
                ? $jobseeker->details->training_certificates
                : (json_decode($jobseeker->details?->training_certificates ?? '[]', true) ?: []);

                $employerSafeDocs = [];
                $completedCertificates = [];

                foreach ($uploadedDocs as $idx => $doc) {
                $cat = strtolower($doc['category'] ?? '');

                if (in_array($cat, ['valid_id', 'pwd_id'])) continue;

                if ($cat === 'certificate') {
                $certName = $doc['course_title'] ?? ($doc['name'] ?? null);
                if ($certName) {
                $completedCertificates[] = $certName;
                }
                continue;
                }

                $employerSafeDocs[] = [
                'index' => $idx,
                'name' => $doc['name'] ?? 'Document',
                'category' => $cat,
                'label' => ucfirst(str_replace('_', ' ', $cat ?: 'document')),
                ];
                }

                $storedExperience = is_array($jobseeker->details?->work_experience)
                    ? $jobseeker->details->work_experience
                    : (json_decode($jobseeker->details?->work_experience ?? '[]', true) ?: []);
                if (!is_array($storedExperience)) {
                    $storedExperience = [];
                }
                $experienceEntries = $storedExperience['entries'] ?? [];
                if ($experienceEntries === [] && $storedExperience && !array_key_exists('entries', $storedExperience) && !array_is_list($storedExperience)) {
                    $experienceEntries = [$storedExperience];
                }
                $normalizeEmploymentType = function ($value) {
                    $normalized = strtolower(trim((string) $value));
                    $aliases = [
                        'full time' => 'Full-time', 'full-time' => 'Full-time', 'fulltime' => 'Full-time', 'regular' => 'Full-time',
                        'part time' => 'Part-time', 'part-time' => 'Part-time',
                        'contract' => 'Contract/Project-based', 'project-based' => 'Contract/Project-based', 'contract/project-based' => 'Contract/Project-based',
                        'internship' => 'Internship/OJT', 'ojt' => 'Internship/OJT', 'internship/ojt' => 'Internship/OJT',
                        'freelance' => 'Freelance', 'temporary' => 'Temporary', 'temp' => 'Temporary',
                    ];

                    return $aliases[$normalized] ?? '';
                };
                $workHistory = array_values(array_map(function ($experience) use ($normalizeEmploymentType) {
                    $storedPosition = $experience['position'] ?? '';
                    $storedTitle = $experience['job_title'] ?? $experience['title'] ?? '';
                    $employmentType = $experience['employment_type'] ?? '';
                    if (!$employmentType) {
                        $employmentType = $normalizeEmploymentType($storedTitle)
                            ?: $normalizeEmploymentType($storedPosition);
                    }
                    $position = $normalizeEmploymentType($storedPosition)
                        ? ($storedTitle && !$normalizeEmploymentType($storedTitle) ? $storedTitle : '')
                        : ($storedPosition ?: ($storedTitle && !$normalizeEmploymentType($storedTitle) ? $storedTitle : ''));
                    $startDate = !empty($experience['start_date']) ? \Carbon\Carbon::parse($experience['start_date']) : null;
                    $endDate = !empty($experience['end_date']) ? \Carbon\Carbon::parse($experience['end_date']) : null;
                    $duration = '';
                    if ($startDate) {
                        $dateDiff = $startDate->diff($endDate ?: now());
                        $durationParts = [];
                        if ($dateDiff->y > 0) $durationParts[] = $dateDiff->y . ' ' . ($dateDiff->y === 1 ? 'year' : 'years');
                        if ($dateDiff->m > 0) $durationParts[] = $dateDiff->m . ' ' . ($dateDiff->m === 1 ? 'month' : 'months');
                        $duration = $durationParts ? implode(' ', $durationParts) : 'Less than a month';
                    }

                    return [
                        'employer' => $experience['employer'] ?? $experience['company'] ?? '',
                        'employer_address' => $experience['employer_address'] ?? '',
                        'employer_reference' => $experience['employer_reference'] ?? $experience['reference'] ?? '',
                        'employer_contact_number' => $experience['employer_contact_number'] ?? '',
                        'employer_contact_email' => $experience['employer_contact_email'] ?? '',
                        'position' => $position,
                        'employment_type' => $employmentType,
                        'job_title' => $employmentType,
                        'start_date' => $startDate?->format('M j, Y') ?? '',
                        'end_date' => $endDate?->format('M j, Y') ?? '',
                        'duration' => $duration,
                        'responsibilities' => $experience['responsibilities'] ?? $experience['description'] ?? '',
                    ];
                }, array_filter($experienceEntries, 'is_array')));

                $appData = [
                'id' => $app->application_id,
                'name' => $fullName ?: 'Candidate #' . $app->application_id,
                'email' => $jobseeker->email ?? 'N/A',
                'phone' => $jobseeker->mobile_number ?? '09XX-XXX-XXXX',
                'job_title' => $job->title ?? 'General Vacancy',
                'status' => $app->status,
                'resignation_status' => $app->resignation_status,
                'applied_at' => $app->created_at ? $app->created_at->format('M d, Y') : 'N/A',
                'hired_date' => $app->hired_date,
                'skills' => $jobseeker->skills ? $jobseeker->skills->pluck('skill_name')->toArray() : [],
                'work_experience' => $workHistory,
                'jpo_notes' => $app->jpo_notes,
                'docs' => $employerSafeDocs,
                'completed_certificates' => $completedCertificates,
                ];
                @endphp
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all flex flex-col justify-between gap-5">

                    <div class="space-y-4">
                        <!-- Top Header -->
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-center gap-3.5">
                                <div class="h-12 w-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-emerald-400 flex items-center justify-center text-white text-lg font-black shrink-0 shadow-sm">
                                    {{ strtoupper(substr($jobseeker->first_name ?? 'C', 0, 1)) }}
                                </div>
                                <div>
                                    <h3 class="font-black text-slate-900 text-base leading-tight">
                                        {{ $fullName ?: 'Candidate #' . $app->application_id }}
                                    </h3>
                                    <p class="text-xs text-slate-400">{{ $jobseeker->email ?? 'N/A' }} &bull; {{ $jobseeker->mobile_number ?? '09XX-XXX-XXXX' }}</p>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div>
                                @if($app->status === 'hired')
                                @if($app->resignation_status === 'requested')
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-extrabold bg-amber-100 text-amber-900 border border-amber-300 shadow-sm animate-pulse">Resignation Requested</span>
                                @elseif($app->resignation_status === 'approved')
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-extrabold bg-slate-100 text-slate-700 border border-slate-300 shadow-sm">Resigned / Released</span>
                                @else
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-sm">Hired</span>
                                @endif
                                @elseif($app->status === 'offered')
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-extrabold bg-blue-100 text-blue-800 border border-blue-300 shadow-sm animate-pulse">Offer Extended</span>
                                @elseif($app->status === 'declined')
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-extrabold bg-slate-100 text-slate-700 border border-slate-300 shadow-sm">Offer Declined</span>
                                @elseif($app->status === 'rejected')
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-extrabold bg-rose-100 text-rose-800 border border-rose-300 shadow-sm">Not Qualified</span>
                                @elseif($app->status === 'interview')
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-sm">Interview Scheduled</span>
                                @else
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-300">Pending Review</span>
                                @endif
                            </div>
                        </div>

                        <!-- Applied Position -->
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-slate-500 font-semibold">Opening Applied:</span>
                            <span class="font-bold text-slate-900">{{ $job->title ?? 'General Vacancy' }}</span>
                        </div>

                        <!-- Skills -->
                        @if($jobseeker->skills && $jobseeker->skills->count() > 0)
                        <div class="space-y-1">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Verified Skills:</span>
                            <div class="flex flex-wrap gap-1">
                                @foreach($jobseeker->skills->take(4) as $skill)
                                <span class="rounded-lg bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700">{{ $skill->skill_name }}</span>
                                @endforeach
                                @if($jobseeker->skills->count() > 4)
                                <span class="text-[11px] text-slate-500 self-center font-medium">+{{ $jobseeker->skills->count() - 4 }} more</span>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <!-- ✅ NEW SQUARE VIEW BUTTON -->
                            <button type="button" @click='openView(@json($appData))'
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span>View</span>
                            </button>

                            @if($app->status === 'pending' || $app->status === 'reviewed')
                            <button type="button" @click="openInterview({{ $app->application_id }}, '{{ addslashes($fullName) }}')"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold transition-colors">
                                <span>Interview</span>
                            </button>

                            <button type="button" @click="openOffer({{ $app->application_id }}, '{{ addslashes($fullName) }}')"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-black shadow-md">
                                <span>Extend Offer</span>
                            </button>

                            <button type="button" @click="openNotQualified({{ $app->application_id }}, '{{ addslashes($fullName) }}')"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition-colors">
                                <span>Not Qualified</span>
                            </button>
                            @endif
                        </div>
                    </div>

                </div>
                @empty
                <div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-400">
                    No candidates found matching the selected criteria. When jobseekers apply or are endorsed by the DMDP Placement Officer, they appear here.
                </div>
                @endforelse
            </div>

            <div class="pt-4">
                {{ $referredApplicants->links() }}
            </div>
        </div>

    </div>

    <!-- ============================================================ -->
    <!-- VIEW PROFILE MODAL                                            -->
    <!-- ============================================================ -->
    <div x-show="viewModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto overscroll-contain bg-slate-900/70 backdrop-blur-sm">
        <div class="flex min-h-full items-center justify-center p-3 sm:p-6">
        <div @click.away="viewModal = false" class="flex h-[calc(100dvh-1.5rem)] w-full max-w-5xl flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl sm:h-[calc(100dvh-3rem)]">

            <template x-if="selectedApp">
                <div class="flex min-h-0 flex-1 flex-col">
                    <!-- Header -->
                    <div class="flex shrink-0 items-start justify-between gap-4 border-b border-slate-200 bg-white px-5 py-4 sm:px-7">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-lg font-black text-white">
                                <span x-text="selectedApp.name ? selectedApp.name.charAt(0).toUpperCase() : 'C'"></span>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900 sm:text-xl" x-text="selectedApp.name"></h3>
                                <p class="mt-1 text-xs text-slate-500" x-text="selectedApp.email + ' · ' + selectedApp.phone"></p>
                                <p class="mt-0.5 text-[11px] text-slate-400"><span x-text="selectedApp.job_title"></span> · Applied <span x-text="selectedApp.applied_at"></span></p>
                            </div>
                        </div>
                        <button type="button" @click="viewModal = false" aria-label="Close applicant details" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-slate-200 text-xl font-bold text-slate-500 hover:bg-slate-50 hover:text-slate-900">&times;</button>
                    </div>

                    <div class="min-h-0 flex-1 space-y-6 overflow-y-auto overscroll-contain px-5 py-5 sm:px-7">

                    <!-- Skills -->
                    <div class="space-y-2">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-500">Verified Skills</h4>
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="(s, i) in selectedApp.skills" :key="i">
                                <span class="rounded-lg bg-emerald-50 border border-emerald-200 px-2.5 py-1 text-xs font-bold text-emerald-800" x-text="s"></span>
                            </template>
                            <template x-if="selectedApp.skills.length === 0">
                                <span class="text-xs text-slate-400 italic">No skills listed.</span>
                            </template>
                        </div>
                    </div>

                    <!-- Work Experience -->
                    <section class="space-y-4">
                        <div class="flex flex-wrap items-end justify-between gap-2 border-b border-slate-200 pb-2">
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-700">Work Experience</h4>
                            <span class="text-[11px] font-semibold text-slate-400" x-text="selectedApp.work_experience.length + (selectedApp.work_experience.length === 1 ? ' record' : ' records')"></span>
                        </div>
                        <template x-if="selectedApp.work_experience.length === 0">
                            <div class="rounded-lg border border-dashed border-slate-300 px-4 py-6 text-center text-xs text-slate-400">No work experience listed.</div>
                        </template>
                        <div class="space-y-3">
                            <template x-for="(experience, index) in selectedApp.work_experience" :key="index">
                                <article class="rounded-lg border border-slate-200 bg-white p-4 sm:p-5">
                                    <div class="grid gap-4 sm:grid-cols-[minmax(0,1fr)_auto]">
                                        <div class="flex min-w-0 items-start gap-3">
                                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-slate-100 text-xs font-black text-slate-600" x-text="String(index + 1).padStart(2, '0')"></span>
                                            <div class="min-w-0">
                                                <h5 class="text-sm font-bold text-slate-900" x-text="experience.position || 'Position not listed'"></h5>
                                                <p class="mt-0.5 text-xs font-semibold text-slate-700" x-text="experience.employer || 'Employer not listed'"></p>
                                                <p x-show="experience.employment_type" class="mt-1 text-[11px] text-slate-500" x-text="'Employment type: ' + experience.employment_type"></p>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3 sm:min-w-[220px] sm:text-right">
                                            <div>
                                                <span class="block text-[9px] font-bold uppercase text-slate-400">Started</span>
                                                <span class="text-[11px] font-semibold text-slate-700" x-text="experience.start_date || 'Not listed'"></span>
                                            </div>
                                            <div>
                                                <span class="block text-[9px] font-bold uppercase text-slate-400">Ended</span>
                                                <span class="text-[11px] font-semibold text-slate-700" x-text="experience.end_date || 'Present'"></span>
                                            </div>
                                            <div class="col-span-2 sm:text-right">
                                                <span class="block text-[9px] font-bold uppercase text-slate-400">Duration</span>
                                                <span class="text-[11px] font-semibold text-slate-700" x-text="experience.duration || 'Not calculated'"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div x-show="experience.employer_address || experience.employer_reference || experience.employer_contact_number || experience.employer_contact_email || experience.responsibilities" class="mt-4 border-t border-slate-100 pt-3">
                                        <p x-show="experience.employer_address" class="text-[11px] text-slate-500"><span class="font-bold text-slate-600">Employer address:</span> <span x-text="experience.employer_address"></span></p>
                                        <p x-show="experience.employer_reference" class="text-[11px] text-slate-500"><span class="font-bold text-slate-600">Employer reference:</span> <span x-text="experience.employer_reference"></span></p>
                                        <p x-show="experience.employer_contact_number" class="text-[11px] text-slate-500"><span class="font-bold text-slate-600">Contact number:</span> <span x-text="experience.employer_contact_number"></span></p>
                                        <p x-show="experience.employer_contact_email" class="text-[11px] text-slate-500"><span class="font-bold text-slate-600">Contact email:</span> <span x-text="experience.employer_contact_email"></span></p>
                                        <div x-show="experience.responsibilities" class="mt-2">
                                            <span class="block text-[9px] font-bold uppercase tracking-wider text-slate-400">Responsibilities & Achievements</span>
                                            <p class="mt-1 whitespace-pre-line text-xs leading-relaxed text-slate-700" x-text="experience.responsibilities"></p>
                                        </div>
                                    </div>
                                </article>
                            </template>
                        </div>
                    </section>

                    <!-- JPO Endorsement Note -->
                    <template x-if="selectedApp.jpo_notes">
                        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-xs">
                            <p class="font-bold text-emerald-900 mb-1">DMDP Endorsement Note</p>
                            <p class="text-emerald-800 leading-relaxed" x-text="selectedApp.jpo_notes"></p>
                        </div>
                    </template>

                    <!-- Resume & Attachments (with inline preview) -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-500">Resume & Attachments</h4>

                        <template x-if="selectedApp.docs.length === 0">
                            <p class="text-xs text-slate-400 italic">No attachments provided.</p>
                        </template>

                        <div class="space-y-2">
                            <template x-for="(doc, idx) in selectedApp.docs" :key="idx">
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 overflow-hidden">
                                    <div class="flex items-center justify-between p-3 bg-white border-b border-slate-100">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <div>
                                                <p class="text-xs font-bold text-slate-800" x-text="doc.label"></p>
                                                <p class="text-[10px] text-slate-500" x-text="doc.name"></p>
                                            </div>
                                        </div>
                                        <a :href="'/employer/applicants/' + selectedApp.id + '/documents/' + doc.index" target="_blank"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-bold transition-colors">
                                            <span>View Full</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="h-96 bg-white">
                                        <iframe :src="'/employer/applicants/' + selectedApp.id + '/documents/' + doc.index"
                                            class="w-full h-full border-0"></iframe>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Completed Certificates (names only) -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-500">Certificates Completed</h4>

                        <template x-if="selectedApp.completed_certificates.length === 0">
                            <p class="text-xs text-slate-400 italic">No completed certificates.</p>
                        </template>

                        <template x-if="selectedApp.completed_certificates.length > 0">
                            <div class="flex flex-wrap gap-2">
                                <template x-for="(cert, i) in selectedApp.completed_certificates" :key="i">
                                    <span class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 border border-emerald-200 px-3 py-1.5 text-xs font-bold text-emerald-900">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        <span x-text="cert"></span>
                                    </span>
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- Footer -->
                    </div>

                    <div class="flex shrink-0 items-center justify-end border-t border-slate-200 bg-slate-50 px-5 py-3 sm:px-7">
                        <button type="button" @click="viewModal = false" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100">
                            Close
                        </button>
                    </div>
                </div>
            </template>
        </div>
        </div>
    </div>

    <!-- 1. Schedule Interview Modal -->
    <div x-show="interviewModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="interviewModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Candidate Interview</span>
                    <h3 class="text-xl font-black text-slate-900 mt-0.5">Schedule Interview with <span x-text="selectedName"></span></h3>
                </div>
                <button @click="interviewModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <form :action="'/employer/applicants/' + selectedAppId + '/status'" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="action" value="interview">

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Interview Date & Time *</label>
                    <input type="datetime-local" name="interview_schedule" min="{{ date('Y-m-d\TH:i') }}" required
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Interview Format *</label>
                    <select name="interview_mode" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none">
                        <option value="online">Online (Video Meeting)</option>
                        <option value="onsite">On-site (Company Office)</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Meeting Link or Office Location *</label>
                    <input type="text" name="interview_location" required placeholder="e.g. https://meet.google.com/xyz or 5th Flr IT Tower"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none">
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="interviewModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-lg">Send Interview Invitation &rarr;</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. Mark as Not Qualified Modal -->
    <div x-show="notQualifiedModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="notQualifiedModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-rose-600 uppercase tracking-wider">Evaluation Outcome</span>
                    <h3 class="text-xl font-black text-slate-900 mt-0.5">Mark <span x-text="selectedName"></span> as Not Qualified</h3>
                </div>
                <button @click="notQualifiedModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <form :action="'/employer/applicants/' + selectedAppId + '/status'" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="action" value="not_qualified">
                <p class="text-xs text-slate-600 leading-relaxed">This candidate will be marked as <span class="font-bold text-rose-700">Not Qualified</span>.</p>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Reason / Remarks</label>
                    <textarea name="remarks" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-rose-500 focus:outline-none">Candidate does not meet the specified qualifications for this role.</textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="notQualifiedModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-black shadow-lg">Confirm: Not Qualified &rarr;</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. Resignation Response Modal -->
    <div x-show="resignationModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="resignationModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider" :class="resignationAction === 'approve' ? 'text-emerald-700' : 'text-rose-700'" x-text="resignationAction === 'approve' ? 'Approve Employee Resignation' : 'Decline Resignation Request'"></span>
                    <h3 class="text-xl font-black text-slate-900 mt-0.5"><span x-text="resignationAction === 'approve' ? 'Release ' : 'Decline Request from '"></span><span x-text="selectedName"></span></h3>
                </div>
                <button @click="resignationModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <form :action="'/employer/applicants/' + selectedAppId + '/resignation'" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="action" :value="resignationAction">

                <div x-show="resignationAction === 'approve'" class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-950">
                    <p class="font-extrabold mb-1">Resignation Clearance Impact:</p>
                    <p class="leading-relaxed">Approving will update their profile to <strong>Unemployed</strong>, allowing them to apply for other jobs.</p>
                </div>
                <div x-show="resignationAction === 'reject'" class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-950">
                    <p class="font-extrabold mb-1">Request Declined:</p>
                    <p class="leading-relaxed">The candidate will remain <strong>Employed</strong> at your company.</p>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Remarks (Optional)</label>
                    <textarea name="remarks" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="resignationModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit" :class="resignationAction === 'approve' ? 'bg-emerald-600 hover:bg-emerald-500' : 'bg-rose-600 hover:bg-rose-500'" class="px-7 py-2.5 rounded-xl text-white text-xs font-black shadow-lg transition-all">
                        <span x-text="resignationAction === 'approve' ? 'Confirm Resignation Approval &rarr;' : 'Confirm Decline &rarr;'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 4. Extend Job Offer Modal -->
    <div x-show="offerModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="offerModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">Formal Employment Offer</span>
                    <h3 class="text-xl font-black text-slate-900 mt-0.5">Extend Job Offer to <span x-text="selectedName"></span></h3>
                </div>
                <button @click="offerModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
            </div>

            <form :action="'/employer/applicants/' + selectedAppId + '/status'" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="action" value="offer">

                <div class="p-3.5 rounded-2xl bg-blue-50/80 border border-blue-200 text-xs text-blue-950">
                    <p class="font-bold mb-1">How Offer &amp; Acceptance Works:</p>
                    <p class="leading-relaxed text-[11px] text-blue-900">The candidate receives your offer in their portal. Once they click <strong>Accept</strong>, their profile becomes Employed at your company.</p>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Offered Monthly Salary (₱)</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-3 text-xs font-bold text-slate-400">₱</span>
                        <input type="number" step="0.01" min="0" name="offer_salary" x-model="offerSalary" placeholder="e.g. 25000"
                            class="w-full rounded-2xl border border-slate-200 pl-8 pr-4 py-3 text-xs text-slate-900 focus:border-blue-500 focus:outline-none">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Target Start Date</label>
                    <input type="date" name="offer_start_date" x-model="offerStartDate" min="{{ date('Y-m-d') }}"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-blue-500 focus:outline-none">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Offer Message (Optional)</label>
                    <textarea name="offer_notes" x-model="offerNotes" rows="3" placeholder="Welcome to the team! Includes HMO, 13th month pay..."
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-xs text-slate-900 focus:border-blue-500 focus:outline-none"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="offerModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-black shadow-lg">Send Official Job Offer &rarr;</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection