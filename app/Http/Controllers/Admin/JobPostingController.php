<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobPostingController extends Controller
{
    /**
     * Display a listing of job postings.
     */
    public function index(Request $request)
    {
        $query = DB::table('job_postings as jp')
            ->leftJoin('employers as e', 'jp.employer_id', '=', 'e.employer_id')
            ->leftJoin('users as u', 'e.user_id', '=', 'u.user_id')
            ->select(
                'jp.job_id',
                'jp.title',
                'jp.description',
                'jp.vacancy_count',
                'jp.valid_until',
                'jp.status',
                'jp.created_by',
                'jp.created_at',
                'jp.approved_at',
                'jp.employer_id',
                DB::raw("CASE WHEN jp.employer_id IS NULL THEN 'DMDP' ELSE e.company_name END as company_name"),
                'u.email as employer_email'
            )
            ->selectRaw('(SELECT COUNT(*) FROM job_applications WHERE job_id = jp.job_id) as applications_count');

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('jp.status', $request->status);
        }

        // Filter by created_by
        if ($request->has('created_by') && $request->created_by != '') {
            $query->where('jp.created_by', $request->created_by);
        }

        // Search by title or company
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('jp.title', 'LIKE', "%{$search}%")
                  ->orWhere('e.company_name', 'LIKE', "%{$search}%")
                  ->orWhere('jp.job_id', 'LIKE', "%{$search}%");
            });
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('jp.created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('jp.created_at', '<=', $request->date_to);
        }

        $jobPostings = $query->orderBy('jp.created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Get statistics
        $stats = [
            'total' => DB::table('job_postings')->count(),
            'pending' => DB::table('job_postings')->where('status', 'pending')->count(),
            'approved' => DB::table('job_postings')->where('status', 'approved')->count(),
            'rejected' => DB::table('job_postings')->where('status', 'rejected')->count(),
            'closed' => DB::table('job_postings')->where('status', 'closed')->count(),
            'expired' => DB::table('job_postings')
                ->where('status', 'approved')
                ->where('valid_until', '<', now()->toDateString())
                ->count(),
        ];

        return view('admin.jobpostings.index', compact('jobPostings', 'stats'));
    }

    /**
     * Show the form for creating a new job posting.
     */
    public function create()
    {
        // Get all accredited employers
        $employers = DB::table('employers as e')
            ->join('users as u', 'e.user_id', '=', 'u.user_id')
            ->where('e.is_accredited', 1)
            ->where('u.status', 'active')
            ->select('e.employer_id', 'e.company_name', 'u.email')
            ->orderBy('e.company_name')
            ->get();

        return view('admin.jobpostings.create', compact('employers'));
    }

    /**
     * Store a newly created job posting.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employer_id' => 'nullable|exists:employers,employer_id',
            'title' => 'required|string|max:150',
            'description' => 'required|string',
            'qualifications' => 'nullable|string',
            'vacancy_count' => 'required|integer|min:1',
            'valid_until' => 'required|date|after:today',
            'accepts_disability' => 'boolean',
            'disability_type' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Get admin profile from user_profiles
        $adminProfile = DB::table('user_profiles')
            ->where('user_id', Auth::id())
            ->first();

        if (!$adminProfile) {
            return redirect()->back()
                ->with('error', 'Admin profile not found. Please contact support.')
                ->withInput();
        }

        // If employer_id is empty, set to null (DMDP)
        $employerId = $request->employer_id ?: null;

        DB::table('job_postings')->insertGetId([
            'employer_id' => $employerId,
            'admin_id' => $adminProfile->profile_id,
            'title' => $request->title,
            'description' => $request->description,
            'qualifications' => $request->qualifications,
            'vacancy_count' => $request->vacancy_count,
            'valid_until' => $request->valid_until,
            'accepts_disability' => $request->accepts_disability ?? 0,
            'disability_type' => $request->disability_type,
            'status' => 'approved',
            'created_by' => 'admin',
            'created_at' => now()->toDateString(),
            'approved_at' => now()->toDateString(),
        ]);

        return redirect()->route('admin.job-postings')
            ->with('success', 'Job posting created and approved successfully.');
    }

    /**
     * Display the specified job posting.
     */
    public function show(int $id)
    {
        $jobPosting = DB::table('job_postings as jp')
            ->leftJoin('employers as e', 'jp.employer_id', '=', 'e.employer_id')
            ->leftJoin('users as u', 'e.user_id', '=', 'u.user_id')
            ->select(
                'jp.*',
                DB::raw("CASE WHEN jp.employer_id IS NULL THEN 'DMDP' ELSE e.company_name END as company_name"),
                'e.is_accredited',
                'u.email as employer_email'
            )
            ->where('jp.job_id', $id)
            ->first();

        if (!$jobPosting) {
            return redirect()->route('admin.job-postings')
                ->with('error', 'Job posting not found.');
        }

        // Get applications for this job
        $applications = DB::table('job_applications as ja')
            ->join('jobseekers as js', 'ja.jobseeker_id', '=', 'js.jobseeker_id')
            ->join('users as u', 'js.user_id', '=', 'u.user_id')
            ->select(
                'ja.*',
                'js.first_name',
                'js.last_name',
                'js.middle_name',
                'js.email as jobseeker_email',
                'js.mobile_number'
            )
            ->where('ja.job_id', $id)
            ->orderBy('ja.created_at', 'desc')
            ->get();

        return view('admin.jobpostings.show', compact('jobPosting', 'applications'));
    }

    /**
     * Show the form for editing the specified job posting.
     */
    public function edit(int $id)
    {
        $jobPosting = DB::table('job_postings')
            ->where('job_id', $id)
            ->first();

        if (!$jobPosting) {
            return redirect()->route('admin.job-postings')
                ->with('error', 'Job posting not found.');
        }

        // Get all accredited employers
        $employers = DB::table('employers as e')
            ->join('users as u', 'e.user_id', '=', 'u.user_id')
            ->where('e.is_accredited', 1)
            ->where('u.status', 'active')
            ->select('e.employer_id', 'e.company_name', 'u.email')
            ->orderBy('e.company_name')
            ->get();

        return view('admin.jobpostings.edit', compact('jobPosting', 'employers'));
    }

    /**
     * Update the specified job posting.
     */
    public function update(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'employer_id' => 'nullable|exists:employers,employer_id',
            'title' => 'required|string|max:150',
            'description' => 'required|string',
            'qualifications' => 'nullable|string',
            'vacancy_count' => 'required|integer|min:1',
            'valid_until' => 'required|date|after:today',
            'accepts_disability' => 'boolean',
            'disability_type' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $existing = DB::table('job_postings')->where('job_id', $id)->first();
        if (!$existing) {
            return redirect()->back()->with('error', 'Job posting not found.');
        }

        // If employer_id is empty, set to null (DMDP)
        $employerId = $request->employer_id ?: null;

        DB::table('job_postings')
            ->where('job_id', $id)
            ->update([
                'employer_id' => $employerId,
                'title' => $request->title,
                'description' => $request->description,
                'qualifications' => $request->qualifications,
                'vacancy_count' => $request->vacancy_count,
                'valid_until' => $request->valid_until,
                'accepts_disability' => $request->accepts_disability ?? 0,
                'disability_type' => $request->disability_type,
            ]);

        return redirect()->route('admin.job-postings')
            ->with('success', 'Job posting updated successfully.');
    }

    /**
     * Remove the specified job posting.
     */
    public function destroy(int $id)
    {
        DB::table('job_postings')->where('job_id', $id)->delete();
        
        return redirect()->route('admin.job-postings')
            ->with('success', 'Job posting deleted successfully.');
    }

    /**
     * Approve a job posting.
     */
    public function approve(int $id)
    {
        $jobPosting = DB::table('job_postings')
            ->where('job_id', $id)
            ->first();

        if (!$jobPosting) {
            return response()->json(['error' => 'Job posting not found.'], 404);
        }

        if ($jobPosting->status !== 'pending') {
            return response()->json(['error' => 'Job posting is not pending.'], 400);
        }

        DB::table('job_postings')
            ->where('job_id', $id)
            ->update([
                'status' => 'approved',
                'approved_at' => now()->toDateString(),
            ]);

        return response()->json(['success' => 'Job posting approved successfully.']);
    }

    /**
     * Reject a job posting.
     */
    public function reject(Request $request, int $id)
    {
        $jobPosting = DB::table('job_postings')
            ->where('job_id', $id)
            ->first();

        if (!$jobPosting) {
            return redirect()->back()->with('error', 'Job posting not found.');
        }

        if ($jobPosting->status !== 'pending') {
            return redirect()->back()->with('error', 'Job posting is not pending.');
        }

        DB::table('job_postings')
            ->where('job_id', $id)
            ->update([
                'status' => 'rejected',
            ]);

        return redirect()->route('admin.job-postings')
            ->with('success', 'Job posting rejected successfully.');
    }

    /**
     * Close a job posting.
     */
    public function close(int $id)
    {
        $jobPosting = DB::table('job_postings')
            ->where('job_id', $id)
            ->first();

        if (!$jobPosting) {
            return response()->json(['error' => 'Job posting not found.'], 404);
        }

        if (!in_array($jobPosting->status, ['approved', 'pending'])) {
            return response()->json(['error' => 'Only approved or pending jobs can be closed.'], 400);
        }

        DB::table('job_postings')
            ->where('job_id', $id)
            ->update([
                'status' => 'closed',
            ]);

        return response()->json(['success' => 'Job posting closed successfully.']);
    }

    /**
     * Bulk action for job postings.
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_ids' => 'required|array',
            'job_ids.*' => 'exists:job_postings,job_id',
            'action' => 'required|in:approve,reject,delete,close',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $jobIds = $request->job_ids;
        $action = $request->action;
        $count = count($jobIds);
        $message = '';

        switch ($action) {
            case 'approve':
                DB::table('job_postings')
                    ->whereIn('job_id', $jobIds)
                    ->where('status', 'pending')
                    ->update([
                        'status' => 'approved',
                        'approved_at' => now()->toDateString(),
                    ]);
                $message = "{$count} job postings approved successfully.";
                break;

            case 'reject':
                DB::table('job_postings')
                    ->whereIn('job_id', $jobIds)
                    ->where('status', 'pending')
                    ->update(['status' => 'rejected']);
                $message = "{$count} job postings rejected successfully.";
                break;

            case 'close':
                DB::table('job_postings')
                    ->whereIn('job_id', $jobIds)
                    ->whereIn('status', ['approved', 'pending'])
                    ->update(['status' => 'closed']);
                $message = "{$count} job postings closed successfully.";
                break;

            case 'delete':
                DB::table('job_postings')
                    ->whereIn('job_id', $jobIds)
                    ->delete();
                $message = "{$count} job postings deleted successfully.";
                break;
        }

        return response()->json(['success' => $message]);
    }

    /**
     * Export job postings to CSV.
     */
    public function export(Request $request)
    {
        $query = DB::table('job_postings as jp')
            ->leftJoin('employers as e', 'jp.employer_id', '=', 'e.employer_id')
            ->select(
                'jp.job_id',
                'jp.title',
                DB::raw("CASE WHEN jp.employer_id IS NULL THEN 'DMDP' ELSE e.company_name END as company_name"),
                'jp.vacancy_count',
                'jp.valid_until',
                'jp.status',
                'jp.created_by',
                'jp.created_at',
                'jp.approved_at'
            )
            ->selectRaw('(SELECT COUNT(*) FROM job_applications WHERE job_id = jp.job_id) as applications_count');

        if ($request->has('status') && $request->status != '') {
            $query->where('jp.status', $request->status);
        }

        $jobPostings = $query->orderBy('jp.created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="job_postings_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($jobPostings) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Job ID', 'Title', 'Company', 'Vacancies', 'Valid Until', 
                'Status', 'Created By', 'Created At', 'Approved At', 'Applications'
            ]);

            foreach ($jobPostings as $job) {
                fputcsv($file, [
                    $job->job_id,
                    $job->title,
                    $job->company_name ?? 'DMDP',
                    $job->vacancy_count,
                    $job->valid_until,
                    ucfirst($job->status),
                    ucfirst($job->created_by),
                    $job->created_at,
                    $job->approved_at ?? 'N/A',
                    $job->applications_count,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}