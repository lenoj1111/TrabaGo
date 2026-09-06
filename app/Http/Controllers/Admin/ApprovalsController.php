<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\EmployerAccreditation;
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
        // 1. Pending Job Postings with employer & accreditation details
        $pendingJobs = JobPosting::with(['employer.accreditation'])->where('status', 'pending')->latest()->get();

        foreach ($pendingJobs as $job) {
            if (!$job->employer) {
                $job->employer_accreditation_badge = 'DMDP Official';
                $job->employer_accreditation_state = 'accredited';
            } elseif ($job->employer->is_accredited || ($job->employer->accreditation && $job->employer->accreditation->status === 'admin_approved')) {
                $job->employer_accreditation_badge = 'Accredited';
                $job->employer_accreditation_state = 'accredited';
            } elseif ($job->employer->accreditation && in_array($job->employer->accreditation->status, ['submitted_to_jpo', 'jpo_approved', 'supervisor_approved', 'manual_review', 'pending'])) {
                $job->employer_accreditation_badge = 'Pending';
                $job->employer_accreditation_state = 'pending';
            } else {
                $job->employer_accreditation_badge = 'Not Accredited';
                $job->employer_accreditation_state = 'not_accredited';
            }
        }

        // 2. Pending Employer Accreditations (recommended by JPO or direct)
        $pendingAccreditations = DB::table('employer_accreditation')
            ->join('employers', 'employer_accreditation.employer_id', '=', 'employers.employer_id')
            ->select('employer_accreditation.*', 'employers.company_name')
            ->whereIn('employer_accreditation.status', ['jpo_approved', 'supervisor_approved', 'submitted_to_jpo'])
            ->latest('submitted_at')
            ->get();

        // 3. Pending Placement Reports (evaluated by JPO)
        $pendingPlacementReports = DB::table('placement_reports')
            ->join('employers', 'placement_reports.employer_id', '=', 'employers.employer_id')
            ->select('placement_reports.*', 'employers.company_name')
            ->whereIn('placement_reports.status', ['jpo_evaluated', 'submitted_to_jpo', 'pending'])
            ->latest('report_month')
            ->get();

        // 4. Pending Collaborator Trainer Accounts
        $pendingTrainers = DB::table('users')
            ->join('user_profiles', 'users.user_id', '=', 'user_profiles.user_id')
            ->select(
                'users.*',
                'user_profiles.full_name',
                'user_profiles.position',
                'user_profiles.office',
                'user_profiles.specialization',
                'user_profiles.partner_institution'
            )
            ->where('users.role', 'trainer')
            ->where(function ($q) {
                $q->where('users.is_approved', 0)
                  ->orWhere('users.status', 'pending');
            })
            ->latest('users.created_at')
            ->get();

        return view('admin.approvals.index', compact('pendingJobs', 'pendingAccreditations', 'pendingPlacementReports', 'pendingTrainers'));
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

        // Enforce: Admin cannot accredit an employer without the recommending approval of the JPO
        if (!$accreditation->jpo_reviewed || !in_array($accreditation->status, ['jpo_approved', 'supervisor_approved'])) {
            return redirect()->back()
                ->withErrors([
                    'error' => 'Cannot accredit employer without the recommending approval of the JPO. JPO evaluation is required first.'
                ])
                ->with('error', 'Cannot accredit employer without the recommending approval of the JPO. JPO evaluation is required first.');
        }

        // Enforce: Admin and JPO cannot recommend or approve accreditation if required documents are incomplete
        if (($accreditation->document_status ?? 'pending') !== 'complete') {
            $reason = !empty($accreditation->document_incomplete_reason) 
                ? " (Reason: {$accreditation->document_incomplete_reason})" 
                : "";
            return redirect()->back()
                ->withErrors([
                    'error' => "Cannot accredit employer: Required legal documents are marked as " . ucfirst($accreditation->document_status ?? 'pending') . "{$reason}. Employer documents must be verified as Complete first."
                ])
                ->with('error', "Cannot accredit employer: Required legal documents are incomplete or unverified.");
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

    public function placementReports(Request $request)
    {
        $statusFilter = $request->input('status', 'all');
        $searchQuery = $request->input('search', '');

        $query = DB::table('placement_reports')
            ->join('employers', 'placement_reports.employer_id', '=', 'employers.employer_id')
            ->select('placement_reports.*', 'employers.company_name');

        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('placement_reports.status', $statusFilter);
        }

        if ($searchQuery) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('employers.company_name', 'LIKE', '%' . $searchQuery . '%')
                  ->orWhere('placement_reports.report_id', 'LIKE', '%' . $searchQuery . '%');
            });
        }

        $reports = $query->latest('placement_reports.report_id')->paginate(15)->withQueryString();

        $stats = [
            'total' => DB::table('placement_reports')->count(),
            'submitted_to_jpo' => DB::table('placement_reports')->where('status', 'submitted_to_jpo')->count(),
            'pending_admin' => DB::table('placement_reports')->where('status', 'jpo_evaluated')->count(),
            'approved' => DB::table('placement_reports')->where('status', 'approved')->count(),
            'rejected' => DB::table('placement_reports')->where('status', 'rejected')->count(),
        ];

        return view('admin.placement-reports.index', compact('reports', 'stats', 'statusFilter', 'searchQuery'));
    }

    public function showPlacementReport($id)
    {
        $report = DB::table('placement_reports')
            ->join('employers', 'placement_reports.employer_id', '=', 'employers.employer_id')
            ->where('placement_reports.report_id', $id)
            ->select('placement_reports.*', 'employers.company_name')
            ->first();

        if (!$report) {
            abort(404, 'Placement report not found.');
        }

        return view('reports.placement-printable', compact('report'));
    }

    public function printAccreditation($id)
    {
        $accreditation = EmployerAccreditation::with(['employer.user', 'employer.jobPostings'])->findOrFail($id);
        $employer = $accreditation->employer;
        $jobPostings = $employer->jobPostings ?? collect();

        return view('reports.establishment-registration-printable', compact('accreditation', 'employer', 'jobPostings'));
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
            ->selectRaw('(SELECT COUNT(*) FROM training_enrollments WHERE jobseeker_id = jobseekers.jobseeker_id) as trainings_count')
            ->selectRaw('(SELECT COUNT(*) FROM training_enrollments WHERE jobseeker_id = jobseekers.jobseeker_id AND certificate_issued = 1) as certs_count')
            ->selectRaw('(SELECT COUNT(*) FROM job_applications WHERE jobseeker_id = jobseekers.jobseeker_id AND referred_by_jpo = 1) as jpo_referrals_count')
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

        if ($request->filled('stage')) {
            $stage = $request->stage;
            if ($stage === 'in_training') {
                $query->whereRaw("(SELECT COUNT(*) FROM training_enrollments WHERE jobseeker_id = jobseekers.jobseeker_id AND status = 'in_progress') > 0");
            } elseif ($stage === 'certified') {
                $query->whereRaw('(SELECT COUNT(*) FROM training_enrollments WHERE jobseeker_id = jobseekers.jobseeker_id AND certificate_issued = 1) > 0');
            } elseif ($stage === 'applied') {
                $query->whereRaw('(SELECT COUNT(*) FROM job_applications WHERE jobseeker_id = jobseekers.jobseeker_id) > 0');
            } elseif ($stage === 'jpo_referred') {
                $query->whereRaw('(SELECT COUNT(*) FROM job_applications WHERE jobseeker_id = jobseekers.jobseeker_id AND referred_by_jpo = 1) > 0');
            } elseif ($stage === 'hired') {
                $query->whereRaw("(SELECT COUNT(*) FROM job_applications WHERE jobseeker_id = jobseekers.jobseeker_id AND status = 'hired') > 0");
            }
        }

        if ($request->has('pwd_only') && $request->pwd_only == 1) {
            $query->where('social_status.is_pwd', 1);
        }

        $jobseekers = $query->orderBy('jobseekers.jobseeker_id', 'desc')->paginate(15)->withQueryString();

        $totalJobseekers = DB::table('jobseekers')->count();
        $pwdJobseekers = DB::table('social_status')->where('is_pwd', 1)->count();
        $employedJobseekers = DB::table('job_applications')->where('status', 'hired')->distinct('jobseeker_id')->count();
        $inTrainingJobseekers = DB::table('training_enrollments')->where('status', 'in_progress')->distinct('jobseeker_id')->count();
        $certifiedJobseekers = DB::table('training_enrollments')->where('certificate_issued', 1)->distinct('jobseeker_id')->count();

        return view('admin.jobseekers.index', compact(
            'jobseekers', 
            'totalJobseekers', 
            'pwdJobseekers', 
            'employedJobseekers',
            'inTrainingJobseekers',
            'certifiedJobseekers'
        ));
    }

    // =========================================================================
    // PILLAR 4: COLLABORATOR TRAINER ACCOUNT APPROVAL
    // =========================================================================

    public function approveTrainer(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'status' => 'active',
            'is_approved' => 1,
        ]);

        DB::table('user_profiles')->where('user_id', $id)->update([
            'is_trainer_approved' => 1,
            'trainer_approved_by' => Auth::id(),
            'trainer_approved_at' => now(),
        ]);

        Notification::create([
            'user_id' => $user->user_id,
            'title' => 'Trainer Account Approved!',
            'message' => "Congratulations! Your trainer collaborator account has been officially approved by the Administrator. You may now log in to the Skills Trainer portal.",
            'type' => 'approval',
            'is_read' => false,
            'related_id' => $user->user_id,
        ]);

        return redirect()->back()->with('success', "Collaborator trainer '{$user->email}' approved successfully.");
    }

    public function rejectTrainer(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'status' => 'inactive',
            'is_approved' => 0,
        ]);

        DB::table('user_profiles')->where('user_id', $id)->update([
            'is_trainer_approved' => 0,
        ]);

        return redirect()->back()->with('info', "Collaborator trainer account for '{$user->email}' was rejected.");
    }
}
