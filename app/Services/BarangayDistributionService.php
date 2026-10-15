<?php

namespace App\Services;

use App\Models\Jobseeker;
use Illuminate\Support\Facades\DB;

class BarangayDistributionService
{
    /**
     * Master registry of all 80 official Cebu City Barangays with district & terrain classification.
     */
    protected static array $barangaysMaster = [
        // DISTRICT 1 (NORTH DISTRICT) - 46 Barangays
        ['name' => 'Adlaon', 'district' => 'District 1 (North)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Agsungot', 'district' => 'District 1 (North)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Apas', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Bacayan', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Banilad', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Binaliw', 'district' => 'District 1 (North)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Budlaan', 'district' => 'District 1 (North)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Busay', 'district' => 'District 1 (North)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Cambinocot', 'district' => 'District 1 (North)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Capitol Site', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Carreta', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Cogon Ramos', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Day-as', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Ermita', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Guba', 'district' => 'District 1 (North)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Hipodromo', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Kalubihan', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Kamagayan', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Kamputhaw', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Kasambagan', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Lahug', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Lorega San Miguel', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Lusaran', 'district' => 'District 1 (North)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Luz', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Mabini', 'district' => 'District 1 (North)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Mabolo', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Malubog', 'district' => 'District 1 (North)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Pahina Central', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Parian', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Paril', 'district' => 'District 1 (North)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Pit-os', 'district' => 'District 1 (North)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Pulangbato', 'district' => 'District 1 (North)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Sambag I', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Sambag II', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'San Antonio', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'San Jose', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'San Roque', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Santa Cruz', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Santo Niño', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Sirao', 'district' => 'District 1 (North)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'T. Padilla', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Talamban', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Taptap', 'district' => 'District 1 (North)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Tejero', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Tinago', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Zapatera', 'district' => 'District 1 (North)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],

        // DISTRICT 2 (SOUTH DISTRICT) - 34 Barangays
        ['name' => 'Babag', 'district' => 'District 2 (South)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Basak Pardo', 'district' => 'District 2 (South)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Basak San Nicolas', 'district' => 'District 2 (South)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Bonbon', 'district' => 'District 2 (South)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Buhisan', 'district' => 'District 2 (South)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Bulacao', 'district' => 'District 2 (South)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Buot-Taup', 'district' => 'District 2 (South)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Calamba', 'district' => 'District 2 (South)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Cogon Pardo', 'district' => 'District 2 (South)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Duljo Fatima', 'district' => 'District 2 (South)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Guadalupe', 'district' => 'District 2 (South)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Inayawan', 'district' => 'District 2 (South)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Kalunasan', 'district' => 'District 2 (South)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Kinasang-an', 'district' => 'District 2 (South)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Labangon', 'district' => 'District 2 (South)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Mambaling', 'district' => 'District 2 (South)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Pahina San Nicolas', 'district' => 'District 2 (South)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Pamutan', 'district' => 'District 2 (South)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Pasil', 'district' => 'District 2 (South)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Poblacion Pardo', 'district' => 'District 2 (South)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Pung-ol Sibugay', 'district' => 'District 2 (South)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Punta Princesa', 'district' => 'District 2 (South)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Quiot', 'district' => 'District 2 (South)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'San Nicolas Proper', 'district' => 'District 2 (South)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Sapangdaku', 'district' => 'District 2 (South)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Sawang Calero', 'district' => 'District 2 (South)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Sinsin', 'district' => 'District 2 (South)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Suba', 'district' => 'District 2 (South)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Sudlon I', 'district' => 'District 2 (South)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Sudlon II', 'district' => 'District 2 (South)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Tabunan', 'district' => 'District 2 (South)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Tagba-o', 'district' => 'District 2 (South)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
        ['name' => 'Tisa', 'district' => 'District 2 (South)', 'terrain' => 'Urban / Lowland', 'zip' => '6000'],
        ['name' => 'Toong', 'district' => 'District 2 (South)', 'terrain' => 'Mountain / Upland', 'zip' => '6000'],
    ];

    /**
     * Get aggregated dataset from LIVE database records only (Zero mock data).
     */
    public function getBarangayDistributionData(array $filters = []): array
    {
        // 1 & 2. Aggregate real jobseekers to barangays (cached for 20s as clean array)
        $liveCounts = \Illuminate\Support\Facades\Cache::remember('barangay_distribution_live_counts', 20, function () {
            $liveJobseekers = Jobseeker::with(['details', 'socialStatus', 'skills', 'applications'])->get();
            $counts = [];

            foreach ($liveJobseekers as $js) {
                $addr = $js->details ? $js->details->address : null;
                $brgyRaw = '';
                if (is_array($addr)) {
                    $brgyRaw = trim($addr['barangay'] ?? ($addr['present_barangay'] ?? ''));
                }

                // Match against official master list of 80 Cebu City barangays
                $matchedName = null;
                if (!empty($brgyRaw)) {
                    foreach (self::$barangaysMaster as $b) {
                        if (strcasecmp($b['name'], $brgyRaw) === 0 || stripos($brgyRaw, $b['name']) !== false) {
                            $matchedName = $b['name'];
                            break;
                        }
                    }
                }

                $key = $matchedName ?: (!empty($brgyRaw) ? $brgyRaw : 'Unassigned / Pending Profile');

                if (!isset($counts[$key])) {
                    $counts[$key] = [
                        'registered' => 0,
                        'seeking' => 0,
                        'employed' => 0,
                        'pwd' => 0,
                        'four_ps' => 0,
                        'skills' => [],
                    ];
                }

                $counts[$key]['registered']++;

                // Real employment status
                $isEmployed = strtolower($js->employment_status ?? '') === 'employed'
                    || ($js->applications && $js->applications->where('status', 'hired')->isNotEmpty());

                if ($isEmployed) {
                    $counts[$key]['employed']++;
                } else {
                    $counts[$key]['seeking']++;
                }

                // Real vulnerable classifications
                if ($js->socialStatus && !empty($js->socialStatus->is_pwd)) {
                    $counts[$key]['pwd']++;
                }
                if ($js->socialStatus && !empty($js->socialStatus->is_4ps)) {
                    $counts[$key]['four_ps']++;
                }

                // Real candidate skills
                if ($js->skills) {
                    foreach ($js->skills as $sk) {
                        $sName = trim($sk->skill_name);
                        if ($sName !== '') {
                            $counts[$key]['skills'][$sName] = ($counts[$key]['skills'][$sName] ?? 0) + 1;
                        }
                    }
                }
            }
            return $counts;
        });

        // 3. Assemble full 80 barangays list using ONLY real counts
        $items = [];
        $totalRegistered = 0;
        $totalSeeking = 0;
        $totalEmployed = 0;
        $totalPwd = 0;
        $total4Ps = 0;
        $northRegistered = 0;
        $southRegistered = 0;
        $uplandCount = 0;
        $lowlandCount = 0;

        foreach (self::$barangaysMaster as $master) {
            $live = $liveCounts[$master['name']] ?? [
                'registered' => 0,
                'seeking' => 0,
                'employed' => 0,
                'pwd' => 0,
                'four_ps' => 0,
                'skills' => [],
            ];

            $reg = $live['registered'];
            $seek = $live['seeking'];
            $emp = $live['employed'];
            $pwd = $live['pwd'];
            $fourPs = $live['four_ps'];

            $empRate = $reg > 0 ? round(($emp / $reg) * 100, 1) : 0;
            $unempRate = $reg > 0 ? round(($seek / $reg) * 100, 1) : 0;

            // Real Top skills from candidates in this barangay
            $topSkill = 'None recorded';
            $skillsArray = [];
            if (!empty($live['skills'])) {
                arsort($live['skills']);
                $skillsArray = array_slice(array_keys($live['skills']), 0, 3);
                $topSkill = implode(', ', $skillsArray);
            }

            $isMountain = str_contains($master['terrain'], 'Mountain');

            // Strategic intervention based on real counts
            $priority = 'Normal';
            if ($seek >= 5) {
                $priority = 'High';
            } elseif ($seek >= 1) {
                $priority = 'Moderate';
            }

            $mobileUnit = $seek > 0
                ? ($isMountain ? 'Upland Skills & Livelihood Mobile Van' : 'Urban Community Outreach Unit')
                : 'Monitoring / On-Demand';

            // Real budget recommendation based on actual active seekers (PHP 5,000 per seeker)
            $recommendedBudget = $seek * 5000;

            $item = [
                'name' => $master['name'],
                'district' => $master['district'],
                'is_north' => str_contains($master['district'], 'North'),
                'terrain' => $master['terrain'],
                'is_mountain' => $isMountain,
                'zip' => $master['zip'],
                'registered' => $reg,
                'seeking' => $seek,
                'employed' => $emp,
                'employment_rate' => $empRate,
                'unemployment_rate' => $unempRate,
                'pwd_count' => $pwd,
                'four_ps_count' => $fourPs,
                'top_skill' => $topSkill,
                'skills_array' => $skillsArray,
                'mobile_unit' => $mobileUnit,
                'priority' => $priority,
                'recommended_budget' => $recommendedBudget,
            ];

            $totalRegistered += $reg;
            $totalSeeking += $seek;
            $totalEmployed += $emp;
            $totalPwd += $pwd;
            $total4Ps += $fourPs;

            if ($item['is_north']) {
                $northRegistered += $reg;
            } else {
                $southRegistered += $reg;
            }

            if ($item['is_mountain']) {
                $uplandCount++;
            } else {
                $lowlandCount++;
            }

            $items[] = $item;
        }

        // Handle Unassigned / Pending Address jobseekers so sum of counts equals total DB jobseekers
        foreach ($liveCounts as $name => $live) {
            $isMaster = false;
            foreach (self::$barangaysMaster as $b) {
                if ($b['name'] === $name) {
                    $isMaster = true;
                    break;
                }
            }
            if (!$isMaster && $live['registered'] > 0) {
                $reg = $live['registered'];
                $seek = $live['seeking'];
                $emp = $live['employed'];
                $pwd = $live['pwd'];
                $fourPs = $live['four_ps'];

                $topSkill = 'None recorded';
                $skillsArray = [];
                if (!empty($live['skills'])) {
                    arsort($live['skills']);
                    $skillsArray = array_slice(array_keys($live['skills']), 0, 3);
                    $topSkill = implode(', ', $skillsArray);
                }

                $items[] = [
                    'name' => $name,
                    'district' => 'Unspecified',
                    'is_north' => false,
                    'terrain' => 'Pending Profile',
                    'is_mountain' => false,
                    'zip' => '6000',
                    'registered' => $reg,
                    'seeking' => $seek,
                    'employed' => $emp,
                    'employment_rate' => $reg > 0 ? round(($emp / $reg) * 100, 1) : 0,
                    'unemployment_rate' => $reg > 0 ? round(($seek / $reg) * 100, 1) : 0,
                    'pwd_count' => $pwd,
                    'four_ps_count' => $fourPs,
                    'top_skill' => $topSkill,
                    'skills_array' => $skillsArray,
                    'mobile_unit' => 'Profile Verification',
                    'priority' => $seek > 0 ? 'Moderate' : 'Normal',
                    'recommended_budget' => $seek * 5000,
                ];

                $totalRegistered += $reg;
                $totalSeeking += $seek;
                $totalEmployed += $emp;
                $totalPwd += $pwd;
                $total4Ps += $fourPs;
            }
        }

        // Apply Search & Filters if present
        $filteredItems = $items;

        if (!empty($filters['search'])) {
            $s = strtolower(trim($filters['search']));
            $filteredItems = array_filter($filteredItems, function ($i) use ($s) {
                return str_contains(strtolower($i['name']), $s) || str_contains(strtolower($i['top_skill']), $s);
            });
        }

        if (!empty($filters['district'])) {
            $d = $filters['district'];
            if ($d === 'north') {
                $filteredItems = array_filter($filteredItems, fn($i) => $i['is_north']);
            } elseif ($d === 'south') {
                $filteredItems = array_filter($filteredItems, fn($i) => !$i['is_north'] && str_contains($i['district'], 'South'));
            }
        }

        if (!empty($filters['terrain'])) {
            $t = $filters['terrain'];
            if ($t === 'mountain') {
                $filteredItems = array_filter($filteredItems, fn($i) => $i['is_mountain']);
            } elseif ($t === 'urban') {
                $filteredItems = array_filter($filteredItems, fn($i) => !$i['is_mountain']);
            }
        }

        if (!empty($filters['priority'])) {
            $p = $filters['priority'];
            $filteredItems = array_filter($filteredItems, fn($i) => strcasecmp($i['priority'], $p) === 0);
        }

        if (!empty($filters['talent_only'])) {
            $filteredItems = array_filter($filteredItems, fn($i) => $i['registered'] > 0);
        }

        // Sorting
        $sort = $filters['sort'] ?? 'registered_desc';
        usort($filteredItems, function ($a, $b) use ($sort) {
            return match ($sort) {
                'name_asc' => strcmp($a['name'], $b['name']),
                'name_desc' => strcmp($b['name'], $a['name']),
                'registered_asc' => $a['registered'] <=> $b['registered'],
                'seeking_desc' => $b['seeking'] <=> $a['seeking'],
                'unemployment_desc' => $b['unemployment_rate'] <=> $a['unemployment_rate'],
                'pwd_desc' => $b['pwd_count'] <=> $a['pwd_count'],
                'budget_desc' => $b['recommended_budget'] <=> $a['recommended_budget'],
                default => $b['registered'] <=> $a['registered'], // registered_desc
            };
        });

        // Top Barangays with real registered talent
        $realRegisteredItems = array_filter($items, fn($i) => $i['registered'] > 0);
        usort($realRegisteredItems, fn($a, $b) => $b['registered'] <=> $a['registered']);
        $top10Registered = array_slice($realRegisteredItems, 0, 10);

        // Top Hotspots with active seekers
        $realSeekingItems = array_filter($items, fn($i) => $i['seeking'] > 0);
        usort($realSeekingItems, fn($a, $b) => $b['seeking'] <=> $a['seeking']);
        $top10Unemployment = array_slice($realSeekingItems, 0, 10);

        // Mountain priorities with active seekers
        $mountainPriorities = array_filter($items, fn($i) => $i['is_mountain'] && $i['seeking'] > 0);
        usort($mountainPriorities, fn($a, $b) => $b['seeking'] <=> $a['seeking']);
        $topMountainPriorities = array_slice($mountainPriorities, 0, 5);

        $overallEmploymentRate = $totalRegistered > 0 ? round(($totalEmployed / $totalRegistered) * 100, 1) : 0;
        $overallUnemploymentRate = $totalRegistered > 0 ? round(($totalSeeking / $totalRegistered) * 100, 1) : 0;

        return [
            'items' => array_values($filteredItems),
            'raw_count' => count($filteredItems),
            'total_barangays' => count(self::$barangaysMaster),
            'kpis' => [
                'total_registered' => $totalRegistered,
                'total_seeking' => $totalSeeking,
                'total_employed' => $totalEmployed,
                'overall_employment_rate' => $overallEmploymentRate,
                'overall_unemployment_rate' => $overallUnemploymentRate,
                'total_pwd' => $totalPwd,
                'total_4ps' => $total4Ps,
                'north_registered' => $northRegistered,
                'south_registered' => $southRegistered,
                'upland_count' => $uplandCount,
                'lowland_count' => $lowlandCount,
                'live_jobseekers_count' => $totalRegistered,
                'total_budget_recommended' => array_sum(array_column($items, 'recommended_budget')),
            ],
            'top10_registered' => array_values($top10Registered),
            'top10_unemployment' => array_values($top10Unemployment),
            'top_mountain_priorities' => array_values($topMountainPriorities),
        ];
    }

    /**
     * Get official master list of barangay names for dropdown selection.
     */
    public static function getBarangayNames(): array
    {
        $names = array_column(self::$barangaysMaster, 'name');
        sort($names);
        return $names;
    }
}
