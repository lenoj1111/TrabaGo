<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\JobApplication;
use App\Models\Jobseeker;
use App\Models\JobseekerSkill;
use App\Services\BarangayDistributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReportsController extends Controller
{
    public function index()
    {
        $stats = [
            'total_jobs' => DB::table('job_postings')->count(),
            'pending_jobs' => DB::table('job_postings')->where('status', 'pending')->count(),
            'approved' => DB::table('job_postings')->where('status', 'approved')->count(),
            'rejected' => DB::table('job_postings')->where('status', 'rejected')->count(),
            'closed' => DB::table('job_postings')->where('status', 'closed')->count(),
            'total_employers' => DB::table('employers')->count(),
            'total_jobseekers' => DB::table('jobseekers')->count(),
            'total_applications' => DB::table('job_applications')->count(),
            'admin_count' => DB::table('users')->where('role', 'admin')->count(),
            'staff_count' => DB::table('users')->whereIn('role', ['jpo', 'trainer'])->count(),
            'total_placement_reports' => DB::table('placement_reports')->count(),
            'approved_placements' => DB::table('placement_reports')->where('status', 'approved')->count(),
        ];

        $applicationStatuses = DB::table('job_applications')
            ->select('status', DB::raw('COUNT(*) AS total'))
            ->groupBy('status')
            ->orderBy('status')
            ->get();
        $trainingStatuses = DB::table('training_enrollments')
            ->select('status', DB::raw('COUNT(*) AS total'))
            ->groupBy('status')
            ->orderBy('status')
            ->get();
        $skillTypes = DB::table('jobseeker_skills')
            ->select('skill_type', DB::raw('COUNT(*) AS total'))
            ->groupBy('skill_type')
            ->pluck('total', 'skill_type');

        $monthlyLabels = collect(range(11, 0))->map(fn($offset) => now()->startOfMonth()->subMonths($offset));
        $monthlyCounts = function (string $table, string $dateColumn, ?string $status = null) use ($monthlyLabels) {
            $start = $monthlyLabels->first()->toDateString();
            $dates = DB::table($table)
                ->whereNotNull($dateColumn)
                ->whereDate($dateColumn, '>=', $start)
                ->when($status, fn($query) => $query->where('status', $status))
                ->pluck($dateColumn);

            $counts = $dates->map(fn($date) => Carbon::parse($date)->format('Y-m'))->countBy();
            return $monthlyLabels->map(fn($month) => (int) ($counts[$month->format('Y-m')] ?? 0))->values();
        };

        $charts = [
            'application_statuses' => $applicationStatuses,
            'training_statuses' => $trainingStatuses,
            'skill_types' => [
                'technical' => (int) ($skillTypes['technical'] ?? 0),
                '21st_century' => (int) ($skillTypes['21st_century'] ?? 0),
            ],
            'monthly' => [
                'labels' => $monthlyLabels->map(fn($month) => $month->format('M Y'))->values(),
                'applications' => $monthlyCounts('job_applications', 'created_at'),
                'hires' => $monthlyCounts('job_applications', 'hired_date', 'hired'),
                'training_starts' => $monthlyCounts('training_enrollments', 'start_date'),
            ],
        ];

        return view('admin.reports', compact('stats', 'charts'));
    }

    public function printable(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:jobseekers,employers,hired,specialized-skills,barangay-skills,training,application-funnel,hiring-trends',
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'barangay' => ['nullable', Rule::in(array_merge(BarangayDistributionService::getBarangayNames(), ['__unassigned__']))],
            'skill_type' => 'nullable|in:all,technical,21st_century',
            'export' => 'nullable|boolean',
        ]);

        $type = $validated['type'];
        $from = $validated['from'] ?? null;
        $to = $validated['to'] ?? null;
        $barangayFilter = $validated['barangay'] ?? '';
        $skillType = $validated['skill_type'] ?? 'all';
        $rows = collect();

        if ($type === 'jobseekers') {
            $jobseekers = Jobseeker::with(['user', 'details', 'skills'])->get();
            $rows = $jobseekers
                ->filter(function ($jobseeker) use ($from, $to, $barangayFilter) {
                    $createdAt = $jobseeker->user?->created_at;
                    $address = $jobseeker->details?->address ?? [];
                    $barangay = trim((string) ($address['barangay'] ?? $address['present_barangay'] ?? ''));

                    $matchesBarangay = $barangayFilter === '__unassigned__'
                        ? $barangay === ''
                        : (!$barangayFilter || strcasecmp($barangay, $barangayFilter) === 0);

                    return (!$from || ($createdAt && $createdAt->toDateString() >= $from))
                        && (!$to || ($createdAt && $createdAt->toDateString() <= $to))
                        && $matchesBarangay;
                })
                ->map(function ($jobseeker) {
                    $address = $jobseeker->details?->address ?? [];

                    return [
                        'name' => trim(($jobseeker->first_name ?? '') . ' ' . ($jobseeker->last_name ?? '')) ?: 'Name not recorded',
                        'barangay' => $address['barangay'] ?? $address['present_barangay'] ?? 'Not recorded',
                        'employment_status' => $jobseeker->employment_status ?: 'Not recorded',
                        'skills' => $jobseeker->skills->pluck('skill_name')->filter()->unique()->sort()->implode(', ') ?: 'None recorded',
                        'registered' => $jobseeker->user?->created_at?->format('Y-m-d') ?? 'Not recorded',
                    ];
                })->values();
        } elseif ($type === 'employers') {
            $employers = Employer::with(['user', 'jobPostings'])->get();
            $rows = $employers
                ->filter(function ($employer) use ($from, $to) {
                    $createdAt = $employer->user?->created_at;

                    return (!$from || ($createdAt && $createdAt->toDateString() >= $from))
                        && (!$to || ($createdAt && $createdAt->toDateString() <= $to));
                })
                ->map(fn($employer) => [
                    'company' => $employer->company_name,
                    'accreditation' => $employer->is_accredited ? 'Accredited' : 'Not accredited',
                    'accredited_date' => $employer->accredited_at?->format('Y-m-d') ?? 'Not recorded',
                    'job_postings' => $employer->jobPostings->count(),
                    'registered' => $employer->user?->created_at?->format('Y-m-d') ?? 'Not recorded',
                ])->values();
        } elseif ($type === 'hired') {
            $applications = JobApplication::with(['jobseeker.details', 'jobPosting.employer'])
                ->where('status', 'hired')
                ->when($from, fn($query) => $query->whereDate('hired_date', '>=', $from))
                ->when($to, fn($query) => $query->whereDate('hired_date', '<=', $to))
                ->get();

            $rows = $applications->map(function ($application) {
                $jobseeker = $application->jobseeker;
                $address = $jobseeker?->details?->address ?? [];

                return [
                    'jobseeker' => trim(($jobseeker?->first_name ?? '') . ' ' . ($jobseeker?->last_name ?? '')) ?: 'Name not recorded',
                    'barangay' => $address['barangay'] ?? $address['present_barangay'] ?? 'Not recorded',
                    'company' => $application->jobPosting?->employer?->company_name ?? 'Not recorded',
                    'position' => $application->jobPosting?->title ?? 'Not recorded',
                    'hired_date' => $application->hired_date?->format('Y-m-d') ?? 'Not recorded',
                    'resignation_status' => $application->resignation_status ?: 'Active / not recorded',
                ];
            })->filter(fn($row) => !$barangayFilter
                || ($barangayFilter === '__unassigned__' && $row['barangay'] === 'Not recorded')
                || strcasecmp($row['barangay'], $barangayFilter) === 0)->values();
        } elseif ($type === 'training') {
            $rows = DB::table('training_enrollments')
                ->join('training_programs', 'training_programs.training_id', '=', 'training_enrollments.training_id')
                ->when($from, fn($query) => $query->whereDate('training_enrollments.start_date', '>=', $from))
                ->when($to, fn($query) => $query->whereDate('training_enrollments.start_date', '<=', $to))
                ->select(
                    'training_programs.title as program',
                    'training_enrollments.training_type',
                    'training_enrollments.status'
                )
                ->selectRaw('COUNT(*) AS enrollment_count')
                ->selectRaw('SUM(CASE WHEN training_enrollments.certificate_issued = 1 THEN 1 ELSE 0 END) AS certificates_issued')
                ->selectRaw('SUM(CASE WHEN training_enrollments.passed = 1 THEN 1 ELSE 0 END) AS passed_count')
                ->groupBy('training_programs.title', 'training_enrollments.training_type', 'training_enrollments.status')
                ->orderBy('training_programs.title')
                ->orderBy('training_enrollments.status')
                ->get()
                ->map(fn($row) => [
                    'program' => $row->program,
                    'training_type' => str_replace('_', ' ', ucfirst($row->training_type)),
                    'status' => ucfirst(str_replace('_', ' ', $row->status)),
                    'enrollments' => (int) $row->enrollment_count,
                    'passed' => (int) $row->passed_count,
                    'certificates' => (int) $row->certificates_issued,
                ]);
        } elseif ($type === 'application-funnel') {
            $applications = DB::table('job_applications')
                ->when($from, fn($query) => $query->whereDate('created_at', '>=', $from))
                ->when($to, fn($query) => $query->whereDate('created_at', '<=', $to))
                ->select('status')
                ->selectRaw('COUNT(*) AS total')
                ->groupBy('status')
                ->orderBy('status')
                ->get();
            $applicationTotal = max((int) $applications->sum('total'), 1);
            $rows = $applications->map(fn($row) => [
                'stage' => ucfirst(str_replace('_', ' ', $row->status)),
                'applications' => (int) $row->total,
                'share' => round(((int) $row->total / $applicationTotal) * 100, 1) . '%',
            ]);
        } elseif ($type === 'hiring-trends') {
            $hiredDates = DB::table('job_applications')
                ->where('status', 'hired')
                ->whereNotNull('hired_date')
                ->when($from, fn($query) => $query->whereDate('hired_date', '>=', $from))
                ->when($to, fn($query) => $query->whereDate('hired_date', '<=', $to))
                ->pluck('hired_date');
            $rows = $hiredDates
                ->map(fn($date) => Carbon::parse($date)->format('Y-m'))
                ->countBy()
                ->sortKeys()
                ->map(fn($count, $month) => [
                    'month' => Carbon::createFromFormat('Y-m', $month)->format('F Y'),
                    'hires' => $count,
                ])->values();
        } else {
            $skills = JobseekerSkill::with(['jobseeker.details', 'jobseeker.user'])
                ->when($skillType !== 'all', fn($query) => $query->where('skill_type', $skillType))
                ->whereHas('jobseeker.user', function ($query) use ($from, $to) {
                    $query->when($from, fn($users) => $users->whereDate('created_at', '>=', $from))
                        ->when($to, fn($users) => $users->whereDate('created_at', '<=', $to));
                })
                ->get();

            $rows = $skills
                ->map(function ($skill) {
                    $address = $skill->jobseeker?->details?->address ?? [];
                    $barangay = trim((string) ($address['barangay'] ?? $address['present_barangay'] ?? ''));

                    return [
                        'skill' => $skill->skill_name,
                        'category' => $skill->skill_type === '21st_century' ? '21st Century' : 'Technical',
                        'barangay' => $barangay ?: 'Not recorded',
                        'jobseeker_id' => $skill->jobseeker_id,
                    ];
                })
                ->filter(fn($row) => !$barangayFilter
                    || ($barangayFilter === '__unassigned__' && $row['barangay'] === 'Not recorded')
                    || strcasecmp($row['barangay'], $barangayFilter) === 0)
                ->groupBy(fn($row) => $row['category'] . '|' . $row['skill'] . '|' . $row['barangay'])
                ->map(function ($group) {
                    return [
                        'skill' => $group->first()['skill'],
                        'category' => $group->first()['category'],
                        'barangay' => $group->first()['barangay'],
                        'jobseekers' => $group->pluck('jobseeker_id')->unique()->count(),
                    ];
                })
                ->when($type === 'specialized-skills', fn($items) => $items->groupBy(fn($row) => $row['category'] . '|' . $row['skill'])->map(function ($group) use ($barangayFilter) {
                    return [
                        'skill' => $group->first()['skill'],
                        'category' => $group->first()['category'],
                        'barangay' => $barangayFilter ?: 'All barangays',
                        'jobseekers' => $group->sum('jobseekers'),
                    ];
                }))
                ->sortByDesc('jobseekers')
                ->values();
        }

        $reportTitles = [
            'jobseekers' => 'Registered Jobseekers',
            'employers' => 'Employer Registry',
            'hired' => 'Hired Jobseekers',
            'specialized-skills' => 'Specialized Skills Inventory',
            'barangay-skills' => 'Barangay Skills Distribution',
            'training' => 'Training Enrollment & Completion Summary',
            'application-funnel' => 'Application Pipeline Summary',
            'hiring-trends' => 'Monthly Hiring Trends',
        ];

        if ($request->boolean('export')) {
            return $this->downloadCsv($rows, $type, $reportTitles[$type]);
        }

        return view('reports.admin-printable', [
            'title' => $reportTitles[$type],
            'type' => $type,
            'rows' => $rows,
            'filters' => compact('from', 'to', 'barangayFilter', 'skillType'),
            'barangayNames' => BarangayDistributionService::getBarangayNames(),
            'generatedAt' => Carbon::now(),
        ]);
    }

    private function downloadCsv($rows, string $type, string $title)
    {
        $columns = match ($type) {
            'training' => ['program', 'training_type', 'status', 'enrollments', 'passed', 'certificates'],
            'application-funnel' => ['stage', 'applications', 'share'],
            'hiring-trends' => ['month', 'hires'],
            'employers' => ['company', 'accreditation', 'accredited_date', 'job_postings', 'registered'],
            'hired' => ['jobseeker', 'barangay', 'company', 'position', 'hired_date', 'resignation_status'],
            'jobseekers' => ['name', 'barangay', 'employment_status', 'skills', 'registered'],
            default => ['skill', 'category', 'barangay', 'jobseekers'],
        };

        $callback = function () use ($rows, $columns, $title) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, [$title]);
            fputcsv($handle, ['Generated at', now()->toDateTimeString()]);
            fputcsv($handle, array_map(fn($column) => ucwords(str_replace('_', ' ', $column)), $columns));

            foreach ($rows as $row) {
                $values = [];
                foreach ($columns as $column) {
                    $value = (string) ($row[$column] ?? '');
                    $values[] = preg_match('/^\s*[=+@\-]/', $value) ? "'" . $value : $value;
                }
                fputcsv($handle, $values);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, 'trabago_' . str_replace('-', '_', $type) . '_' . now()->format('Ymd_His') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
