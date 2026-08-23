<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\JobPosting;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalsController extends Controller
{
    public function index()
    {
        // 1. Pending Job Postings
        $pendingJobs = JobPosting::with('employer')->where('status', 'pending')->latest()->get();

        // 2. Pending Employer Accreditations (endorsed by Supervisor or direct)
        $pendingAccreditations = DB::table('employer_accreditation')
            ->join('employers', 'employer_accreditation.employer_id', '=', 'employers.employer_id')
            ->select('employer_accreditation.*', 'employers.company_name')
            ->whereIn('employer_accreditation.status', ['supervisor_approved', 'jpo_approved', 'submitted_to_jpo'])
            ->latest('submitted_at')
            ->get();

        // 3. Pending Placement Reports (evaluated by JPO)
        $pendingPlacementReports = DB::table('placement_reports')
            ->join('employers', 'placement_reports.employer_id', '=', 'employers.employer_id')
            ->select('placement_reports.*', 'employers.company_name')
            ->whereIn('placement_reports.status', ['jpo_evaluated', 'submitted_to_jpo', 'pending'])
            ->latest('report_month')
            ->get();

        return view('admin.approvals.index', compact('pendingJobs', 'pendingAccreditations', 'pendingPlacementReports'));
    }

    // =========================================================================
    // PILLAR 1: JOB POSTING APPROVAL
    // =========================================================================

    public function approveJobPosting($id)
    {
        $job = JobPosting::with('employer.user')->findOrFail($id);
        $job->update([
            'status' => 'approved',
            'approved_at' => now()->toDateString(),
        ]);

        // Notify employer
        if ($job->employer && $job->employer->user) {
            Notification::create([
                'user_id' => $job->employer->user->user_id,
                'title' => 'Job Posting Approved!',
                'message' => "Your job posting '{$job->title}' has been officially approved by the Admin and is now live for all jobseekers.",
                'type' => 'approval',
                'is_read' => false,
                'related_id' => $job->job_id,
            ]);
        }

        return redirect()->back()->with('success', "Job posting '{$job->title}' approved successfully.");
    }

    public function rejectJobPosting(Request $request, $id)
    {
        $job = JobPosting::with('employer.user')->findOrFail($id);
        $job->update(['status' => 'rejected']);

        if ($job->employer && $job->employer->user) {
            Notification::create([
                'user_id' => $job->employer->user->user_id,
                'title' => 'Job Posting Rejected',
                'message' => "Your job posting '{$job->title}' was not approved. Remarks: " . $request->input('remarks', 'Please revise and resubmit.'),
                'type' => 'rejection',
                'is_read' => false,
                'related_id' => $job->job_id,
            ]);
        }

        return redirect()->back()->with('info', "Job posting '{$job->title}' rejected.");
    }

    // =========================================================================
    // PILLAR 2: EMPLOYER ACCREDITATION FINAL APPROVAL
    // =========================================================================

    public function approveAccreditation(Request $request, $id)
    {
        $accreditation = DB::table('employer_accreditation')->where('accreditation_id', $id)->first();
        if (!$accreditation) {
            return redirect()->back()->withErrors(['error' => 'Accreditation record not found.']);
        }

        $remarks = $request->input('remarks', 'Officially accredited by DMDP City Administrator.');

        // Update accreditation table
        DB::table('employer_accreditation')->where('accreditation_id', $id)->update([
            'status' => 'admin_approved',
            'admin_approved' => 1,
            'admin_approved_at' => now(),
            'approved_at' => now()->toDateString(),
        ]);

        // Update employer table to mark accredited
        DB::table('employers')->where('employer_id', $accreditation->employer_id)->update([
            'is_accredited' => 1,
            'accredited_at' => now()->toDateString(),
        ]);

        // Notify employer
        $employer = Employer::with('user')->find($accreditation->employer_id);
        if ($employer && $employer->user) {
            Notification::create([
                'user_id' => $employer->user->user_id,
                'title' => 'Official Accreditation Granted!',
                'message' => "Congratulations! {$employer->company_name} is now officially accredited with the Cebu City Department of Manpower Development and Placement (DMDP).",
                'type' => 'approval',
                'is_read' => false,
                'related_id' => $employer->employer_id,
            ]);
        }

        return redirect()->back()->with('success', "Employer '{$employer->company_name}' successfully accredited.");
    }

    public function rejectAccreditation(Request $request, $id)
    {
        $accreditation = DB::table('employer_accreditation')->where('accreditation_id', $id)->first();
        if (!$accreditation) {
            return redirect()->back()->withErrors(['error' => 'Accreditation record not found.']);
        }

        DB::table('employer_accreditation')->where('accreditation_id', $id)->update([
            'status' => 'rejected',
            'admin_approved' => 0,
        ]);

        $employer = Employer::with('user')->find($accreditation->employer_id);
        if ($employer && $employer->user) {
            Notification::create([
                'user_id' => $employer->user->user_id,
                'title' => 'Accreditation Papers Need Revision',
                'message' => "Your accreditation application requires additional verification: " . $request->input('remarks', 'Please contact DMDP.'),
                'type' => 'rejection',
                'is_read' => false,
                'related_id' => $employer->employer_id,
            ]);
        }

        return redirect()->back()->with('info', "Accreditation returned for revision.");
    }

    // =========================================================================
    // PILLAR 3: PLACEMENT REPORT APPROVAL
    // =========================================================================

    public function approvePlacementReport(Request $request, $id)
    {
        $report = DB::table('placement_reports')->where('report_id', $id)->first();
        if (!$report) {
            return redirect()->back()->withErrors(['error' => 'Placement report not found.']);
        }

        $remarks = $request->input('remarks', 'Approved and archived for Cebu City official PESO/DMDP records.');

        DB::table('placement_reports')->where('report_id', $id)->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now()->toDateString(),
            'admin_remarks' => $remarks,
        ]);

        // Notify employer
        $employer = Employer::with('user')->find($report->employer_id);
        if ($employer && $employer->user) {
            Notification::create([
                'user_id' => $employer->user->user_id,
                'title' => 'Placement Report Approved',
                'message' => "Your monthly placement report for {$report->report_month} has been approved and logged into City Hall records.",
                'type' => 'approval',
                'is_read' => false,
                'related_id' => $report->report_id,
            ]);
        }

        return redirect()->back()->with('success', "Placement report approved and archived successfully.");
    }

    public function rejectPlacementReport(Request $request, $id)
    {
        DB::table('placement_reports')->where('report_id', $id)->update([
            'status' => 'rejected',
            'admin_remarks' => $request->input('remarks', 'Discrepancies found in placement records.'),
        ]);

        return redirect()->back()->with('info', "Placement report rejected.");
    }

    // =========================================================================
    // VIEW JOBSEEKER STATUS DIRECTORY
    // =========================================================================

    public function jobseekers(Request $request)
    {
        $query = DB::table('jobseekers')
            ->leftJoin('users', 'jobseekers.user_id', '=', 'users.user_id')
            ->leftJoin('social_status', 'jobseekers.jobseeker_id', '=', 'social_status.jobseeker_id')
            ->select(
                'jobseekers.*', 
                'users.email as user_email', 
                'users.status as account_status',
                'social_status.is_pwd',
                'social_status.pwd_type',
                'social_status.is_4ps'
            )
            ->selectRaw('(SELECT COUNT(*) FROM jobseeker_skills WHERE jobseeker_id = jobseekers.jobseeker_id) as skills_count')
            ->selectRaw('(SELECT COUNT(*) FROM job_applications WHERE jobseeker_id = jobseekers.jobseeker_id) as applications_count')
            ->selectRaw("(SELECT COUNT(*) FROM job_applications WHERE jobseeker_id = jobseekers.jobseeker_id AND status = 'hired') as hired_count");

        if ($request->has('search') && $request->search !== '') {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('jobseekers.first_name', 'like', "%{$s}%")
                  ->orWhere('jobseekers.last_name', 'like', "%{$s}%")
                  ->orWhere('users.email', 'like', "%{$s}%");
            });
        }

        if ($request->has('employment_status') && $request->employment_status !== '') {
            $query->where('jobseekers.employment_status', $request->employment_status);
        }

        if ($request->has('pwd_only') && $request->pwd_only == 1) {
            $query->where('social_status.is_pwd', 1);
        }

        $jobseekers = $query->paginate(15)->withQueryString();

        $totalJobseekers = DB::table('jobseekers')->count();
        $pwdJobseekers = DB::table('social_status')->where('is_pwd', 1)->count();
        $employedJobseekers = DB::table('job_applications')->where('status', 'hired')->distinct('jobseeker_id')->count();

        return view('admin.jobseekers.index', compact('jobseekers', 'totalJobseekers', 'pwdJobseekers', 'employedJobseekers'));
    }
}
