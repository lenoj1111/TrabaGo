<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\EmployerAccreditation;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployerPortalController extends Controller
{
    private function canonicalAccreditationDocumentKey(string $key): string
    {
        $key = preg_replace('/_previous_.+$/', '', $key) ?? $key;

        return match ($key) {
            'sec_dti_registration' => 'sec_dti',
            'updated_job_vacancies' => 'job_vacancies_form',
            'business_permit' => 'mayors_permit',
            default => $key,
        };
    }

    private function mergeAccreditationDocuments(array $previous, array $incoming): array
    {
        $incomingByType = [];
        foreach ($incoming as $key => $document) {
            $canonicalKey = $this->canonicalAccreditationDocumentKey((string) $key);
            if (!isset($incomingByType[$canonicalKey]) || $key === $canonicalKey) {
                $incomingByType[$canonicalKey] = $document;
            }
        }

        foreach ($incomingByType as $canonicalKey => $document) {
            $newPath = is_array($document) ? ($document['path'] ?? null) : $document;
            $oldDocuments = [];

            foreach ($previous as $key => $oldDocument) {
                if (
                    $this->canonicalAccreditationDocumentKey((string) $key) === $canonicalKey
                    && !str_contains((string) $key, '_previous_')
                ) {
                    $oldDocuments[(string) $key] = $oldDocument;
                }
            }

            $seenPaths = [];
            foreach ($oldDocuments as $key => $oldDocument) {
                $oldPath = is_array($oldDocument) ? ($oldDocument['path'] ?? null) : $oldDocument;
                if (!is_string($oldPath) || $oldPath === $newPath || isset($seenPaths[$oldPath])) {
                    unset($previous[$key]);
                    continue;
                }

                $seenPaths[$oldPath] = true;
                $historyKey = $canonicalKey . '_previous_' . now()->format('YmdHis');
                while (array_key_exists($historyKey, $previous)) {
                    $historyKey .= '_old';
                }

                $previous[$historyKey] = is_array($oldDocument)
                    ? array_merge($oldDocument, [
                        'revision_status' => 'previous',
                        'replaced_at' => now()->toIso8601String(),
                    ])
                    : [
                        'path' => $oldDocument,
                        'revision_status' => 'previous',
                        'replaced_at' => now()->toIso8601String(),
                    ];
                unset($previous[$key]);
            }

            $document = is_array($document)
                ? array_merge($document, ['revision_status' => 'new'])
                : ['path' => $document, 'revision_status' => 'new'];
            $previous[$canonicalKey] = $document;

            foreach (
                [
                    'sec_dti' => ['sec_dti_registration'],
                    'job_vacancies_form' => ['updated_job_vacancies'],
                    'mayors_permit' => ['business_permit'],
                ][$canonicalKey] ?? [] as $alias
            ) {
                $previous[$alias] = $document;
            }
        }

        return $previous;
    }

    private function getOrCreateEmployer(): Employer
    {
        $user = Auth::user();
        $employer = Employer::where('user_id', $user->user_id)->first();

        if (!$employer) {
            $companyName = $user->full_name ?? (explode('@', $user->email)[0] . ' Corp');
            $employer = Employer::create([
                'user_id' => $user->user_id,
                'company_name' => $companyName,
                'is_accredited' => 0,
            ]);
        }

        return $employer;
    }

    private function employerIsAccredited(Employer $employer): bool
    {
        if ($employer->is_accredited) {
            return true;
        }

        $accreditation = DB::table('employer_accreditation')
            ->where('employer_id', $employer->employer_id)
            ->first();

        return $accreditation && $accreditation->status === 'admin_approved';
    }

    public function homepage()
    {
        $employer = $this->getOrCreateEmployer();
        $user = Auth::user();

        $jobStats = JobPosting::where('employer_id', $employer->employer_id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
            ")->first();
        $totalJobs = (int) ($jobStats->total ?? 0);
        $approvedJobs = (int) ($jobStats->approved ?? 0);
        $pendingJobs = (int) ($jobStats->pending ?? 0);

        $jobIds = JobPosting::where('employer_id', $employer->employer_id)->pluck('job_id');

        $totalApplicants = 0;
        $referredCount = 0;
        $interviewCount = 0;
        $hiredCount = 0;
        $pendingScreeningCount = 0;
        $monthlyApplicantTrends = [];

        if ($jobIds->isNotEmpty()) {
            $appStats = JobApplication::whereIn('job_id', $jobIds)
                ->where('status', '!=', 'jpo_pending') // <-- ADDED THIS LINE
                ->selectRaw("
                    COUNT(*) as total,
                    SUM(CASE WHEN referred_by_jpo = 1 THEN 1 ELSE 0 END) as referred,
                    SUM(CASE WHEN status = 'interview' THEN 1 ELSE 0 END) as interview,
                    SUM(CASE WHEN status = 'hired' THEN 1 ELSE 0 END) as hired,
                    SUM(CASE WHEN status IN ('pending', 'reviewed') THEN 1 ELSE 0 END) as pending_screening
                ")->first();

            $totalApplicants = (int) ($appStats->total ?? 0);
            $referredCount = (int) ($appStats->referred ?? 0);
            $interviewCount = (int) ($appStats->interview ?? 0);
            $hiredCount = (int) ($appStats->hired ?? 0);
            $pendingScreeningCount = (int) ($appStats->pending_screening ?? 0);

            $sixMonthsAgo = now()->subMonths(5)->startOfMonth();
            $trendRows = JobApplication::whereIn('job_id', $jobIds)
                ->where('created_at', '>=', $sixMonthsAgo)
                ->selectRaw("
                    CONVERT(varchar(7), created_at, 120) as yrmo,
                    COUNT(*) as total_apps,
                    SUM(CASE WHEN status = 'hired' THEN 1 ELSE 0 END) as hires_count
                ")
                ->groupBy(DB::raw("CONVERT(varchar(7), created_at, 120)"))
                ->get()
                ->keyBy('yrmo');

            for ($i = 5; $i >= 0; $i--) {
                $monthDate = now()->subMonths($i);
                $yrmo = $monthDate->format('Y-m');
                $label = $monthDate->format('M Y');
                $row = $trendRows->get($yrmo);

                $monthlyApplicantTrends[] = [
                    'month' => $label,
                    'applications' => (int) ($row->total_apps ?? 0),
                    'hires' => (int) ($row->hires_count ?? 0),
                ];
            }
        } else {
            for ($i = 5; $i >= 0; $i--) {
                $monthlyApplicantTrends[] = [
                    'month' => now()->subMonths($i)->format('M Y'),
                    'applications' => 0,
                    'hires' => 0,
                ];
            }
        }

        $topJobs = JobPosting::where('employer_id', $employer->employer_id)
            ->withCount(['applications' => function ($q) {
                $q->where('status', '!=', 'jpo_pending'); // <-- ADD THIS
            }])
            ->orderByDesc('applications_count')
            ->take(5)
            ->get();
        $accreditation = DB::table('employer_accreditation')->where('employer_id', $employer->employer_id)->first();
        $placementReports = DB::table('placement_reports')->where('employer_id', $employer->employer_id)->get();

        $recentApplicants = JobApplication::with(['jobseeker', 'jobPosting'])
            ->whereIn('job_id', $jobIds)
            ->where('status', '!=', 'jpo_pending') // <-- ADD THIS
            ->latest()
            ->take(5)
            ->get();

        return view('employer.homepage', compact(
            'employer',
            'user',
            'totalJobs',
            'approvedJobs',
            'pendingJobs',
            'totalApplicants',
            'referredCount',
            'interviewCount',
            'hiredCount',
            'pendingScreeningCount',
            'topJobs',
            'monthlyApplicantTrends',
            'accreditation',
            'placementReports',
            'recentApplicants'
        ));
    }

    // =========================================================================
    // 1. MANAGE JOB POSTINGS
    // =========================================================================

    public function jobPostings(Request $request)
    {
        $employer = $this->getOrCreateEmployer();
        $accreditation = DB::table('employer_accreditation')->where('employer_id', $employer->employer_id)->first();

        $query = JobPosting::where('employer_id', $employer->employer_id);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('qualifications', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        $jobs = $query->withCount(['applications' => function ($q) {
            $q->where('status', '!=', 'jpo_pending'); // Hides JPO pending count from employer
        }])->latest('job_id')->paginate(10)->withQueryString();

        $stats = [
            'total' => JobPosting::where('employer_id', $employer->employer_id)->count(),
            'approved' => JobPosting::where('employer_id', $employer->employer_id)->where('status', 'approved')->count(),
            'pending' => JobPosting::where('employer_id', $employer->employer_id)->where('status', 'pending')->count(),
            'rejected' => JobPosting::where('employer_id', $employer->employer_id)->where('status', 'rejected')->count(),
            'closed' => JobPosting::where('employer_id', $employer->employer_id)->where('status', 'closed')->count(),
        ];

        return view('employer.job-postings', compact('employer', 'jobs', 'stats', 'accreditation'));
    }

    public function createJobPosting()
    {
        $employer = $this->getOrCreateEmployer();

        if (Auth::user() && !Auth::user()->is_approved) {
            return redirect()->route('employer.job-postings')
                ->with('error', 'Your employer account is pending approval. You must be approved before creating a job posting.');
        }

        if (!$this->employerIsAccredited($employer)) {
            return redirect()->route('employer.accreditation')->with('error', 'You must be accredited before creating a job posting.');
        }

        return redirect()->route('employer.job-postings', ['create' => 1]);
    }

    public function storeJobPosting(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isAccountVerified()) {
            return redirect()->route('employer.accreditation')
                ->with('error', 'Your company is not yet accredited. Please submit your required business documents and wait for Admin/JPO accreditation before posting job openings.');
        }

        $employer = $this->getOrCreateEmployer();

        if (Auth::user() && !Auth::user()->is_approved) {
            return redirect()->route('employer.job-postings')
                ->with('error', 'Your employer account is pending approval. You must be approved before creating a job posting.');
        }

        if (!$this->employerIsAccredited($employer)) {
            return redirect()->route('employer.accreditation')->with('error', 'You must be accredited before creating a job posting.');
        }

        $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'required|string',
            'job_type' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'salary_range' => 'nullable|string|max:150',
            'benefits' => 'nullable|string',
            'other_instructions' => 'nullable|string',
            'qualifications' => 'nullable|string',
            'skills' => 'nullable|string',
            'vacancy_count' => 'required|integer|min:1',
            'valid_until' => ['nullable', 'date', 'after_or_equal:today'],
            'accepts_disability' => 'nullable|boolean',
            'disability_type' => 'nullable|string|max:100',
        ], [
            'valid_until.after_or_equal' => 'The valid until date cannot be in the past.',
        ]);

        $job = JobPosting::create([
            'employer_id' => $employer->employer_id,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'job_type' => $request->input('job_type'),
            'location' => $request->input('location'),
            'salary_range' => $request->input('salary_range'),
            'benefits' => $request->input('benefits'),
            'other_instructions' => $request->input('other_instructions'),
            'qualifications' => $request->input('qualifications'),
            'skills' => $request->input('skills'),
            'vacancy_count' => $request->input('vacancy_count', 1),
            'valid_until' => $request->input('valid_until', now()->addMonths(2)->toDateString()),
            'accepts_disability' => $request->boolean('accepts_disability'),
            'disability_type' => $request->input('disability_type'),
            'status' => 'pending',
            'created_by' => 'employer',
            'created_at' => now()->toDateString(),
        ]);

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->user_id,
                'title' => 'New Job Posting Pending Approval',
                'message' => "Employer {$employer->company_name} submitted a new job opening '{$job->title}' for review.",
                'type' => 'manual_review',
                'is_read' => false,
                'related_id' => $job->job_id,
            ]);
        }

        return redirect()->route('employer.job-postings')->with('success', 'Job posting created successfully and forwarded to the Admin for approval.');
    }

    public function showJobPosting(int $id)
    {
        $employer = $this->getOrCreateEmployer();

        $job = JobPosting::with([
            'applications' => function ($query) {
                // Hide unreferred JPO candidates from the employer
                $query->where('status', '!=', 'jpo_pending');
            },
            'applications.jobseeker',
            'applications.jobseeker.skills'
        ])
            ->where('employer_id', $employer->employer_id)
            ->findOrFail($id);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($job);
        }

        return view('employer.job-postings-show', compact('employer', 'job'));
    }

    public function editJobPosting(int $id)
    {
        $employer = $this->getOrCreateEmployer();
        $job = JobPosting::where('employer_id', $employer->employer_id)->findOrFail($id);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($job);
        }

        return redirect()->route('employer.job-postings', ['edit_id' => $id]);
    }

    public function updateJobPosting(Request $request, int $id)
    {
        $employer = $this->getOrCreateEmployer();
        $job = JobPosting::where('employer_id', $employer->employer_id)->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'required|string',
            'job_type' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'salary_range' => 'nullable|string|max:150',
            'benefits' => 'nullable|string',
            'other_instructions' => 'nullable|string',
            'qualifications' => 'nullable|string',
            'skills' => 'nullable|string',
            'vacancy_count' => 'required|integer|min:1',
            'valid_until' => ['nullable', 'date', 'after_or_equal:today'],
            'accepts_disability' => 'nullable|boolean',
            'disability_type' => 'nullable|string|max:100',
        ], [
            'valid_until.after_or_equal' => 'The valid until date cannot be in the past.',
        ]);

        $status = $job->status;
        if ($job->status === 'rejected') {
            $status = 'pending';
        }

        $job->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'job_type' => $request->input('job_type'),
            'location' => $request->input('location'),
            'salary_range' => $request->input('salary_range'),
            'benefits' => $request->input('benefits'),
            'other_instructions' => $request->input('other_instructions'),
            'qualifications' => $request->input('qualifications'),
            'skills' => $request->input('skills'),
            'vacancy_count' => $request->input('vacancy_count'),
            'valid_until' => $request->input('valid_until'),
            'accepts_disability' => $request->boolean('accepts_disability'),
            'disability_type' => $request->input('disability_type'),
            'status' => $status,
        ]);

        return redirect()->route('employer.job-postings')->with('success', 'Job posting updated successfully.');
    }

    public function destroyJobPosting(int $id)
    {
        $employer = $this->getOrCreateEmployer();
        $job = JobPosting::where('employer_id', $employer->employer_id)->findOrFail($id);

        $applicationsCount = JobApplication::where('job_id', $job->job_id)->count();
        if ($applicationsCount > 0) {
            $job->update(['status' => 'closed']);
            return redirect()->route('employer.job-postings')->with('info', "Job posting '{$job->title}' has active applications, so it has been closed rather than removed.");
        }

        $jobTitle = $job->title;
        $job->delete();

        return redirect()->route('employer.job-postings')->with('success', "Job posting '{$jobTitle}' deleted successfully.");
    }

    public function closeJobPosting(int $id)
    {
        $employer = $this->getOrCreateEmployer();
        $job = JobPosting::where('employer_id', $employer->employer_id)->findOrFail($id);

        $job->update(['status' => 'closed']);

        return redirect()->route('employer.job-postings')->with('success', "Job posting '{$job->title}' marked as closed.");
    }

    // =========================================================================
    // 2. ACCREDITATION
    // =========================================================================

    public function accreditation()
    {
        $employer = $this->getOrCreateEmployer();
        $accreditation = DB::table('employer_accreditation')->where('employer_id', $employer->employer_id)->first();

        return view('employer.accreditation', compact('employer', 'accreditation'));
    }

    public function submitAccreditation(Request $request)
    {
        $employer = $this->getOrCreateEmployer();

        try {
            $request->validate([
                'bir_2303' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp,bmp|max:25600',
                'sec_dti' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp,bmp|max:25600',
                'sec_dti_registration' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp,bmp|max:25600',
                'mayors_permit' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp,bmp|max:25600',
                'business_permit' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp,bmp|max:25600',
                'philjobnet_proof' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp,bmp|max:25600',
                'job_vacancies_form' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp,bmp|max:25600',
                'updated_job_vacancies' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp,bmp|max:25600',
                'dole_license' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp,bmp|max:25600',
                'dmw_license' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp,bmp|max:25600',
                'dmw_job_orders' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp,bmp|max:25600',
                'letter_of_intent' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp,bmp|max:25600',
                'company_profile' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp,bmp|max:25600',
                'documents.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp,bmp|max:25600',
            ], [
                'max' => 'The uploaded file exceeds the allowed 25MB limit. Please upload a smaller file.',
                'mimes' => 'The document must be a file of type: PDF, DOC, DOCX, JPG, PNG, or WEBP.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Document upload failed validation: ' . implode(' ', $e->validator->errors()->all()));
        }

        Storage::disk('public')->makeDirectory('accreditation_docs');

        $docs = [];
        $files = [
            'bir_2303',
            'sec_dti',
            'sec_dti_registration',
            'mayors_permit',
            'business_permit',
            'philjobnet_proof',
            'job_vacancies_form',
            'updated_job_vacancies',
            'dole_license',
            'dmw_license',
            'dmw_job_orders',
            'letter_of_intent',
            'company_profile',
        ];

        foreach ($files as $fileKey) {
            $file = $request->file($fileKey)
                ?? $request->file("documents.{$fileKey}")
                ?? (is_array($request->file('documents')) ? ($request->file('documents')[$fileKey] ?? null) : null);

            if ($file && $file->isValid()) {
                try {
                    $path = $file->store('accreditation_docs', 'public');
                    $docEntry = [
                        'original_name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'uploaded_at' => now()->toIso8601String(),
                        'revision_status' => 'new',
                    ];
                    $docs[$fileKey] = $docEntry;

                    if ($fileKey === 'sec_dti') {
                        $docs['sec_dti_registration'] = $docEntry;
                    } elseif ($fileKey === 'sec_dti_registration') {
                        $docs['sec_dti'] = $docEntry;
                    } elseif ($fileKey === 'job_vacancies_form') {
                        $docs['updated_job_vacancies'] = $docEntry;
                    } elseif ($fileKey === 'updated_job_vacancies') {
                        $docs['job_vacancies_form'] = $docEntry;
                    }
                } catch (\Throwable $err) {
                    return redirect()->back()
                        ->with('error', "Could not store file {$file->getClientOriginalName()}: " . $err->getMessage());
                }
            }
        }

        $existing = DB::table('employer_accreditation')->where('employer_id', $employer->employer_id)->first();

        if (empty($docs) && (!$existing || empty($existing->documents) || $existing->documents === '[]')) {
            return redirect()->back()
                ->with('error', 'Please choose at least one document to upload before submitting.');
        }

        if ($existing) {
            $prevDocs = is_array($existing->documents) ? $existing->documents : json_decode($existing->documents ?? '[]', true);
            $mergedDocs = $this->mergeAccreditationDocuments($prevDocs ?: [], $docs);

            DB::table('employer_accreditation')->where('employer_id', $employer->employer_id)->update([
                'documents' => json_encode($mergedDocs),
                'status' => 'submitted_to_jpo',
                'document_status' => 'pending',
                'jpo_reviewed' => 0,
                'supervisor_approved' => 0,
                'admin_approved' => 0,
                'submitted_at' => now()->toDateString(),
            ]);
        } else {
            $docs = $this->mergeAccreditationDocuments([], $docs);
            DB::table('employer_accreditation')->insert([
                'employer_id' => $employer->employer_id,
                'documents' => json_encode($docs),
                'status' => 'submitted_to_jpo',
                'document_status' => 'pending',
                'jpo_reviewed' => 0,
                'supervisor_approved' => 0,
                'admin_approved' => 0,
                'submitted_at' => now()->toDateString(),
            ]);
        }

        $user = Auth::user();
        if ($user && $user->account_status === 'unverified') {
            DB::table('users')->where('user_id', $user->user_id)->update([
                'account_status' => 'pending_review',
                'updated_at' => now(),
            ]);
        }

        $jpos = User::where('role', 'jpo')->get();
        foreach ($jpos as $jpo) {
            Notification::create([
                'user_id' => $jpo->user_id,
                'title' => 'Employer Accreditation Documents Submitted',
                'message' => "Employer '{$employer->company_name}' submitted/updated accreditation documents for your evaluation.",
                'type' => 'manual_review',
                'is_read' => false,
                'related_id' => $employer->employer_id,
            ]);
        }

        return redirect()->route('employer.accreditation')
            ->with('success', 'Accreditation documents uploaded successfully and forwarded to the Job Placement Officer (JPO) for evaluation.');
    }

    // =========================================================================
    // 3. REFERRED JOBSEEKERS
    // =========================================================================

    public function referredJobseekers(Request $request)
    {
        $employer = $this->getOrCreateEmployer();
        $jobIds = JobPosting::where('employer_id', $employer->employer_id)->pluck('job_id');

        // 1. Start query and HIDE jpo_pending, rejected, and training_recommended
        $query = JobApplication::with(['jobseeker.skills', 'jobseeker.details', 'jobPosting'])
            ->whereIn('job_id', $jobIds)
            ->whereNotIn('status', ['jpo_pending', 'rejected', 'training_recommended']);

        if ($request->filled('job_id')) {
            $query->where('job_id', $request->input('job_id'));
        }

        // 2. Filter logic
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $status = $request->input('status');
            if ($status === 'not_qualified') {
                $query->where('status', 'rejected');
            } elseif ($status === 'resignation_requested') {
                $query->where('resignation_status', 'requested');
            } elseif ($status === 'pending') {
                $query->whereIn('status', ['pending', 'reviewed']); // Ensure it explicitly grabs these
            } else {
                $query->where('status', $status);
            }
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('jobseeker', function ($jq) use ($search) {
                    $jq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('jobPosting', function ($jq) use ($search) {
                    $jq->where('title', 'like', "%{$search}%");
                });
            });
        }

        $referredApplicants = $query->latest('application_id')->paginate(15)->withQueryString();

        $stats = [
            'total' => JobApplication::whereIn('job_id', $jobIds)
                ->whereNotIn('status', ['jpo_pending', 'rejected', 'training_recommended'])
                ->count(),
            'pending' => JobApplication::whereIn('job_id', $jobIds)->whereIn('status', ['pending', 'reviewed'])->count(),
            'interview' => JobApplication::whereIn('job_id', $jobIds)->where('status', 'interview')->count(),
            'offered' => JobApplication::whereIn('job_id', $jobIds)->where('status', 'offered')->count(),
            'hired' => JobApplication::whereIn('job_id', $jobIds)->where('status', 'hired')->count(),

            // REMOVED: not_qualified count since it's a JPO decision
            'not_qualified' => 0,

            'resignation_requested' => JobApplication::whereIn('job_id', $jobIds)->where('resignation_status', 'requested')->count(),
        ];

        $employerJobs = JobPosting::where('employer_id', $employer->employer_id)->select('job_id', 'title')->get();

        return view('employer.referred-jobseekers', compact('employer', 'referredApplicants', 'stats', 'employerJobs'));
    }

    public function viewApplicantDocument(int $id, int $documentIndex)
    {
        $employer = $this->getOrCreateEmployer();
        $application = JobApplication::with('jobseeker.details')
            ->whereHas('jobPosting', fn($query) => $query->where('employer_id', $employer->employer_id))
            ->findOrFail($id);

        $jobseeker = $application->jobseeker;
        abort_unless($jobseeker && $jobseeker->details, 404);

        $documents = $jobseeker->details->training_certificates;
        if (!is_array($documents)) {
            $documents = json_decode($documents ?? '[]', true) ?: [];
        }
        abort_unless(isset($documents[$documentIndex]) && is_array($documents[$documentIndex]), 404);

        $document = $documents[$documentIndex];
        if (($document['category'] ?? null) === 'certificate' && !empty($document['enrollment_id'])) {
            $enrollment = DB::table('training_enrollments')
                ->join('jobseekers', 'training_enrollments.jobseeker_id', '=', 'jobseekers.jobseeker_id')
                ->join('training_programs', 'training_enrollments.training_id', '=', 'training_programs.training_id')
                ->where('training_enrollments.enrollment_id', $document['enrollment_id'])
                ->where('training_enrollments.jobseeker_id', $jobseeker->jobseeker_id)
                ->where('training_enrollments.certificate_issued', 1)
                ->select(
                    'training_enrollments.*',
                    'jobseekers.first_name',
                    'jobseekers.last_name',
                    'training_programs.title as course_title'
                )
                ->first();

            abort_unless($enrollment, 404, 'Certificate not found or not yet issued.');

            return view('trainer.certificates.preview', [
                'enrollment' => $enrollment,
                'backUrl' => route('employer.referred-jobseekers'),
            ]);
        }

        $disk = Storage::disk('public');
        foreach (['file_path', 'path', 'file_url', 'url'] as $pathKey) {
            $candidate = $document[$pathKey] ?? null;
            if (!is_string($candidate) || trim($candidate) === '') {
                continue;
            }

            $urlPath = parse_url($candidate, PHP_URL_PATH);
            $path = is_string($urlPath) && $urlPath !== '' ? $urlPath : $candidate;
            $path = rawurldecode(str_replace('\\', '/', $path));
            if (($storagePosition = strrpos($path, '/storage/')) !== false) {
                $path = substr($path, $storagePosition + strlen('/storage/'));
            }
            $path = ltrim($path, '/');
            foreach (['storage/app/public/', 'app/public/', 'storage/', 'public/'] as $prefix) {
                if (str_starts_with($path, $prefix)) {
                    $path = substr($path, strlen($prefix));
                    break;
                }
            }

            $segments = explode('/', $path);
            $isApplicantDocument = str_starts_with($path, 'documents/');
            $isResume = str_starts_with($path, 'resumes/');
            $isLegacyId = str_starts_with($path, 'ids/') || str_starts_with($path, 'valid_ids/');
            if (
                ($isApplicantDocument || $isResume || $isLegacyId)
                && !in_array('..', $segments, true)
                && !in_array('.', $segments, true)
                && $disk->exists($path)
            ) {
                return $disk->response($path, $document['name'] ?? basename($path), [], 'inline');
            }
        }

        abort(404, 'Document file not found on storage.');
    }

    /**
     * Update applicant status.
     *
     * IMPORTANT: The 'hire' action is intentionally NOT supported here.
     * Hiring can ONLY be finalized when the jobseeker accepts a formal job offer
     * from their own dashboard (JobseekerPortalController::acceptOffer).
     * Employers may only: schedule interviews, send offers, or mark not qualified.
     */
    public function updateApplicantStatus(Request $request, int $id)
    {
        $employer = $this->getOrCreateEmployer();
        $jobIds = JobPosting::where('employer_id', $employer->employer_id)->pluck('job_id');

        $application = JobApplication::whereIn('job_id', $jobIds)->findOrFail($id);

        $action = $request->input('action');

        if ($action === 'interview') {
            $request->validate([
                'interview_schedule' => 'required|date',
                'interview_mode' => 'required|in:online,onsite',
                'interview_location' => 'required|string|max:255',
            ]);

            $application->update([
                'status' => 'interview',
                'interview_schedule' => $request->input('interview_schedule'),
                'interview_mode' => $request->input('interview_mode'),
                'interview_location' => $request->input('interview_location'),
                'interview_status' => 'scheduled',
                'jobseeker_response' => 'pending',
            ]);

            $jobseekerUser = $application->jobseeker->user ?? null;
            if ($jobseekerUser) {
                Notification::create([
                    'user_id' => $jobseekerUser->user_id,
                    'title' => 'Interview Scheduled!',
                    'message' => "{$employer->company_name} scheduled an interview for '{$application->jobPosting->title}' on " . date('M d, Y h:i A', strtotime($request->input('interview_schedule'))),
                    'type' => 'interview',
                    'is_read' => false,
                    'related_id' => $application->application_id,
                ]);
            }

            return redirect()->back()->with('success', 'Interview scheduled and invitation sent to applicant.');
        }

        if ($action === 'offer') {
            $request->validate([
                'offer_salary' => 'nullable|numeric|min:0',
                'offer_start_date' => 'nullable|date',
                'offer_notes' => 'nullable|string|max:1000',
            ]);

            $application->update([
                'status' => 'offered',
                'offered_at' => now(),
                'offer_salary' => $request->input('offer_salary'),
                'offer_start_date' => $request->input('offer_start_date'),
                'offer_notes' => $request->input('offer_notes'),
            ]);

            $jobseekerUser = $application->jobseeker->user ?? null;
            if ($jobseekerUser) {
                $salaryTxt = $request->filled('offer_salary') ? ' with an offered salary of ₱' . number_format($request->input('offer_salary'), 2) : '';
                Notification::create([
                    'user_id' => $jobseekerUser->user_id,
                    'title' => 'Job Offer Received!',
                    'message' => "Congratulations! {$employer->company_name} has extended you a formal Job Offer for the '{$application->jobPosting->title}' position{$salaryTxt}. Please review and respond in your Applications dashboard.",
                    'type' => 'approval',
                    'is_read' => false,
                    'related_id' => $application->application_id,
                ]);
            }

            return redirect()->back()->with('success', "Formal job offer sent to {$application->jobseeker->first_name} {$application->jobseeker->last_name}!");
        }

        if ($action === 'not_qualified' || $action === 'reject') {
            $remarks = $request->input('remarks') ?: ($request->input('notes') ?: 'Candidate does not meet the specified qualifications for this role.');

            $application->update([
                'status' => 'rejected',
                'jpo_notes' => $remarks,
            ]);

            $jobseekerUser = $application->jobseeker->user ?? null;
            if ($jobseekerUser) {
                Notification::create([
                    'user_id' => $jobseekerUser->user_id,
                    'title' => 'Application Status: Not Qualified',
                    'message' => "{$employer->company_name} reviewed your application for '{$application->jobPosting->title}' and marked it as Not Qualified. Reason: {$remarks}",
                    'type' => 'manual_review',
                    'is_read' => false,
                    'related_id' => $application->application_id,
                ]);
            }

            return redirect()->back()->with('info', "Candidate '{$application->jobseeker->first_name} {$application->jobseeker->last_name}' marked as Not Qualified.");
        }

        // Legacy or unauthorized actions (including 'hire') are rejected outright.
        return redirect()->back()->with('error', 'Invalid action. Hires are only finalized when the candidate accepts a formal job offer.');
    }

    public function respondResignation(Request $request, int $id)
    {
        $employer = $this->getOrCreateEmployer();
        $jobIds = JobPosting::where('employer_id', $employer->employer_id)->pluck('job_id');

        $application = JobApplication::whereIn('job_id', $jobIds)
            ->with(['jobseeker', 'jobPosting'])
            ->findOrFail($id);

        $request->validate([
            'action' => 'required|in:approve,reject',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $action = $request->input('action');
        $remarks = $request->input('remarks');

        if ($action === 'approve') {
            $application->update([
                'resignation_status' => 'approved',
                'resignation_approved_at' => now(),
                'resignation_remarks' => $remarks,
            ]);

            if ($application->jobseeker) {
                $application->jobseeker->update([
                    'employment_status' => 'Unemployed',
                    'hired_company' => null,
                ]);
            }

            $jobseekerUser = $application->jobseeker?->user;
            if ($jobseekerUser) {
                Notification::create([
                    'user_id' => $jobseekerUser->user_id,
                    'title' => 'Resignation Approved',
                    'message' => "Your resignation request for '{$application->jobPosting->title}' has been approved by {$employer->company_name}. Your profile has been updated to Unemployed, and you may now apply for other job opportunities.",
                    'type' => 'approval',
                    'is_read' => false,
                    'related_id' => $application->application_id,
                ]);
            }

            return redirect()->back()->with('success', "Resignation request for {$application->jobseeker->first_name} {$application->jobseeker->last_name} has been approved. The candidate is now marked as Unemployed.");
        } else {
            $application->update([
                'resignation_status' => 'rejected',
                'resignation_remarks' => $remarks,
            ]);

            $jobseekerUser = $application->jobseeker?->user;
            if ($jobseekerUser) {
                Notification::create([
                    'user_id' => $jobseekerUser->user_id,
                    'title' => 'Resignation Request Declined',
                    'message' => "Your resignation request for '{$application->jobPosting->title}' was declined by {$employer->company_name}." . ($remarks ? " Reason: {$remarks}" : ''),
                    'type' => 'manual_review',
                    'is_read' => false,
                    'related_id' => $application->application_id,
                ]);
            }

            return redirect()->back()->with('info', "Resignation request for {$application->jobseeker->first_name} {$application->jobseeker->last_name} was declined.");
        }
    }

    // =========================================================================
    // 4. PLACEMENT REPORTS
    // =========================================================================

    public function placementReports()
    {
        $employer = $this->getOrCreateEmployer();
        $jobIds = JobPosting::where('employer_id', $employer->employer_id)->pluck('job_id');

        $reports = DB::table('placement_reports')->where('employer_id', $employer->employer_id)->latest('report_id')->paginate(10);
        $hiredApplicants = JobApplication::with(['jobseeker', 'jobPosting'])
            ->whereIn('job_id', $jobIds)
            ->where('status', 'hired')
            ->get();

        return view('employer.placement-reports', compact('employer', 'reports', 'hiredApplicants'));
    }

    public function generatePlacementReport(Request $request)
    {
        $employer = $this->getOrCreateEmployer();
        $jobIds = JobPosting::where('employer_id', $employer->employer_id)->pluck('job_id');

        $month = $request->input('report_month', now()->format('Y-m'));
        $hiredApplicants = JobApplication::with(['jobseeker', 'jobPosting'])
            ->whereIn('job_id', $jobIds)
            ->where('status', 'hired')
            ->get();

        $reportData = [
            'month' => $month,
            'company_name' => $employer->company_name,
            'total_hired' => $hiredApplicants->count(),
            'hired_list' => $hiredApplicants->map(function ($app) {
                return [
                    'jobseeker_name' => $app->jobseeker->full_name ?? ($app->jobseeker->first_name . ' ' . $app->jobseeker->last_name),
                    'position' => $app->jobPosting->title ?? 'N/A',
                    'hired_date' => $app->hired_date ?: now()->toDateString(),
                    'referred_by_jpo' => $app->referred_by_jpo ? 'Yes' : 'Direct',
                ];
            })->toArray(),
            'notes' => $request->input('notes', 'Monthly placement report for Cebu City DMDP.'),
            'submitted_at' => now()->toIso8601String(),
        ];

        $jpo = DB::table('user_profiles')->join('users', 'user_profiles.user_id', '=', 'users.user_id')->where('users.role', 'jpo')->first();
        $jpoProfileId = $jpo ? $jpo->profile_id : 1;

        try {
            $reportId = DB::table('placement_reports')->insertGetId([
                'employer_id' => $employer->employer_id,
                'jpo_id' => $jpoProfileId,
                'report_type' => 'employer_monthly',
                'report_month' => $month . '-01',
                'report_data' => json_encode($reportData),
                'status' => 'submitted_to_jpo',
                'jpo_evaluated' => 0,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if (DB::getDriverName() === 'sqlsrv' && str_contains($e->getMessage(), 'CHECK constraint')) {
                $constraints = DB::select("SELECT name FROM sys.check_constraints WHERE parent_object_id = OBJECT_ID('placement_reports') AND (definition LIKE '%status%' OR name LIKE '%placement%statu%')");
                foreach ($constraints as $c) {
                    DB::statement("ALTER TABLE [placement_reports] DROP CONSTRAINT [{$c->name}]");
                }
                DB::statement("ALTER TABLE [placement_reports] ADD CONSTRAINT [CK_placement_reports_status] CHECK ([status] IN ('pending', 'submitted_to_jpo', 'jpo_evaluated', 'approved', 'rejected'))");

                $reportId = DB::table('placement_reports')->insertGetId([
                    'employer_id' => $employer->employer_id,
                    'jpo_id' => $jpoProfileId,
                    'report_type' => 'employer_monthly',
                    'report_month' => $month . '-01',
                    'report_data' => json_encode($reportData),
                    'status' => 'submitted_to_jpo',
                    'jpo_evaluated' => 0,
                ]);
            } else {
                throw $e;
            }
        }

        $jpos = User::where('role', 'jpo')->get();
        foreach ($jpos as $jUser) {
            Notification::create([
                'user_id' => $jUser->user_id,
                'title' => 'New Placement Report Received',
                'message' => "Employer '{$employer->company_name}' submitted monthly placement report for {$month}.",
                'type' => 'manual_review',
                'is_read' => false,
                'related_id' => $reportId,
            ]);
        }

        return redirect()->route('employer.placement-reports')->with('success', 'Monthly placement report generated and sent to the Job Placement Officer (JPO) for evaluation.');
    }

    public function showPlacementReport(int $id)
    {
        $employer = $this->getOrCreateEmployer();
        $report = DB::table('placement_reports')
            ->join('employers', 'placement_reports.employer_id', '=', 'employers.employer_id')
            ->where('placement_reports.report_id', $id)
            ->where('placement_reports.employer_id', $employer->employer_id)
            ->select('placement_reports.*', 'employers.company_name')
            ->first();

        if (!$report) {
            abort(404, 'Placement report not found or unauthorized.');
        }

        return view('reports.placement-printable', compact('report'));
    }

    public function printAccreditation()
    {
        $employer = $this->getOrCreateEmployer();
        $accreditation = EmployerAccreditation::where('employer_id', $employer->employer_id)->first();
        $jobPostings = $employer->jobPostings ?? collect();

        return view('reports.establishment-registration-printable', compact('accreditation', 'employer', 'jobPostings'));
    }

    // =========================================================================
    // 5. PROFILE & SETTINGS
    // =========================================================================

    public function profile()
    {
        $employer = $this->getOrCreateEmployer();
        $user = Auth::user();
        $profile = $user->profile ?: new UserProfile(['user_id' => $user->user_id]);
        $accreditation = DB::table('employer_accreditation')->where('employer_id', $employer->employer_id)->first();

        return view('employer.profile', compact('employer', 'user', 'profile', 'accreditation'));
    }

    public function updateProfile(Request $request)
    {
        $employer = $this->getOrCreateEmployer();
        $user = Auth::user();

        $request->validate([
            'company_name' => 'required|string|max:150',
            'full_name' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:150',
            'office' => 'nullable|string|max:150',
            'specialization' => 'nullable|string|max:150',
        ]);

        $employer->update(['company_name' => $request->input('company_name')]);

        $profile = $user->profile ?: new UserProfile(['user_id' => $user->user_id]);
        $profile->full_name = $request->input('full_name', $profile->full_name);
        $profile->phone = $request->input('phone', $profile->phone);
        $profile->position = $request->input('position', $profile->position);
        $profile->department = $request->input('department', $profile->department);
        $profile->office = $request->input('office', $profile->office);
        $profile->specialization = $request->input('specialization', $profile->specialization);
        $profile->save();

        return redirect()->route('employer.profile')->with('success', 'Company and representative profile updated successfully.');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The new password must be at least 8 characters in length.',
        ]);

        $user = User::findOrFail(Auth::id());

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('employer.profile', ['tab' => 'security'])
                ->withErrors(['current_password' => 'The provided current password does not match your account password.'])
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        Notification::create([
            'user_id' => $user->user_id,
            'title' => 'Password Reset Successfully',
            'message' => 'Your employer account password was recently updated. If you did not initiate this change, please contact DMDP administrator immediately.',
            'type' => 'manual_review',
            'is_read' => false,
        ]);

        return redirect()->route('employer.profile', ['tab' => 'security'])
            ->with('success', 'Your password has been successfully reset and updated.');
    }

    // =========================================================================
    // 6. NOTIFICATIONS
    // =========================================================================

    public function notifications()
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->user_id)
            ->latest('created_at')
            ->paginate(15);

        $unreadCount = Notification::where('user_id', $user->user_id)
            ->where('is_read', false)
            ->count();

        return view('employer.notifications', compact('notifications', 'user', 'unreadCount'));
    }

    public function markNotificationRead(int $id)
    {
        Notification::where('notification_id', $id)
            ->where('user_id', Auth::id())
            ->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    public function markAllNotificationsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
