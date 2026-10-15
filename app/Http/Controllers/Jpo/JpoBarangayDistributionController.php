<?php

namespace App\Http\Controllers\Jpo;

use App\Http\Controllers\Controller;
use App\Services\BarangayDistributionService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class JpoBarangayDistributionController extends Controller
{
    protected BarangayDistributionService $distributionService;

    public function __construct(BarangayDistributionService $distributionService)
    {
        $this->distributionService = $distributionService;
    }

    /**
     * Display the JPO Operational Read-Only Barangay Distribution summary.
     * Purpose: Barangay Job Fair Planning & BESD Coordination under RA 10173 PoLP guidelines.
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('search'),
            'district' => $request->input('district'),
            'terrain' => $request->input('terrain'),
            'priority' => $request->input('priority'),
            'sort' => $request->input('sort', 'seeking_desc'), // JPO defaults to sorting by actively seeking candidates for referrals
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

        return view('jpo.barangay-distribution', array_merge($data, [
            'filters' => $filters,
            'barangayNames' => BarangayDistributionService::getBarangayNames(),
        ]));
    }
}
