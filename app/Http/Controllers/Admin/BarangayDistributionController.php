<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BarangayDistributionService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BarangayDistributionController extends Controller
{
    protected BarangayDistributionService $distributionService;

    public function __construct(BarangayDistributionService $distributionService)
    {
        $this->distributionService = $distributionService;
    }

    /**
     * Display the City-Wide Barangay Distribution macro-analytics dashboard.
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('search'),
            'district' => $request->input('district'),
            'terrain' => $request->input('terrain'),
            'priority' => $request->input('priority'),
            'talent_only' => $request->boolean('talent_only') || $request->input('talent_only') == '1',
            'sort' => $request->input('sort', 'registered_desc'),
        ];

        $data = $this->distributionService->getBarangayDistributionData($filters);

        // Paginate barangay records to 20 per page
        $perPage = 20;
        $page = (int) $request->input('page', 1);
        $allItems = collect($data['items']);
        $paginatedItems = new LengthAwarePaginator(
            $allItems->forPage($page, $perPage)->values(),
            $allItems->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $data['items'] = $paginatedItems;

        return view('admin.barangay-distribution', array_merge($data, [
            'filters' => $filters,
            'barangayNames' => BarangayDistributionService::getBarangayNames(),
        ]));
    }

    /**
     * Export the Barangay Labor & Demographic Distribution matrix as CSV for the Cebu City Council & DMDP.
     */
    public function export(Request $request): StreamedResponse
    {
        $filters = [
            'search' => $request->input('search'),
            'district' => $request->input('district'),
            'terrain' => $request->input('terrain'),
            'priority' => $request->input('priority'),
            'sort' => $request->input('sort', 'registered_desc'),
        ];

        $data = $this->distributionService->getBarangayDistributionData($filters);
        $items = $data['items'];
        $kpis = $data['kpis'];

        $filename = 'TrabaGo_DMDP_Cebu_City_Barangay_Distribution_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($items, $kpis) {
            $handle = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Official Document Header
            fputcsv($handle, ['DEPARTMENT OF MANPOWER DEVELOPMENT AND PLACEMENT (DMDP) - CEBU CITY HALL']);
            fputcsv($handle, ['TrabaGo Platform: City-Wide Macro-Level Barangay Demographic & Labor Distribution Report']);
            fputcsv($handle, ['Generated At:', now()->toDayDateTimeString()]);
            fputcsv($handle, ['Compliance Standard:', 'Philippine Data Privacy Act of 2012 (RA 10173) & Principle of Least Privilege (PoLP)']);
            fputcsv($handle, []);

            // Summary KPIs
            fputcsv($handle, ['MACRO SUMMARY METRICS']);
            fputcsv($handle, ['Total Registered Jobseekers across 80 Barangays:', number_format($kpis['total_registered'])]);
            fputcsv($handle, ['Total Actively Seeking (Unemployed):', number_format($kpis['total_seeking'])]);
            fputcsv($handle, ['Total Employed / Hired:', number_format($kpis['total_employed'])]);
            fputcsv($handle, ['City-Wide Placement Rate:', $kpis['overall_employment_rate'] . '%']);
            fputcsv($handle, ['Total Registered PWD Jobseekers:', number_format($kpis['total_pwd'])]);
            fputcsv($handle, ['Total 4Ps Household Beneficiaries:', number_format($kpis['total_4ps'])]);
            fputcsv($handle, ['Total Annual Training Budget Allocation Index (PHP):', 'PHP ' . number_format($kpis['total_budget_recommended'], 2)]);
            fputcsv($handle, []);

            // Table Columns
            fputcsv($handle, [
                'Barangay Name',
                'District',
                'Terrain Classification',
                'Postal Code',
                'Total Registered',
                'Actively Seeking',
                'Employed / Placed',
                'Employment Rate (%)',
                'Unemployment Rate (%)',
                'PWD Jobseekers',
                '4Ps Beneficiaries',
                'Priority Sector Cluster',
                'Top Skill Specialization',
                'Recommended Mobile Facility Deployment',
                'Recommended Training Budget Allocation (PHP)'
            ]);

            foreach ($items as $item) {
                fputcsv($handle, [
                    $item['name'],
                    $item['district'],
                    $item['terrain'],
                    $item['zip'],
                    $item['registered'],
                    $item['seeking'],
                    $item['employed'],
                    $item['employment_rate'] . '%',
                    $item['unemployment_rate'] . '%',
                    $item['pwd_count'],
                    $item['four_ps_count'],
                    $item['priority'],
                    $item['top_skill'],
                    $item['mobile_unit'],
                    'PHP ' . number_format($item['recommended_budget'], 2)
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
