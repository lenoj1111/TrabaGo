<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PlacementReportsAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $jpoUser;
    protected User $employerUser;
    protected User $otherEmployerUser;
    protected Employer $employer;
    protected Employer $otherEmployer;
    protected int $reportId;
    protected int $otherReportId;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Admin
        $this->adminUser = User::create([
            'email' => 'admin@trabago.gov.ph',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'is_approved' => 1,
        ]);

        // 2. Create JPO
        $this->jpoUser = User::create([
            'email' => 'jpo@trabago.gov.ph',
            'password' => Hash::make('password'),
            'role' => 'jpo',
            'status' => 'active',
            'is_approved' => 1,
        ]);
        $jpoProfileId = DB::table('user_profiles')->insertGetId([
            'user_id' => $this->jpoUser->user_id,
            'full_name' => 'John JPO Officer',
            'office' => 'DMDP Cebu',
        ]);

        // 3. Create Employer 1
        $this->employerUser = User::create([
            'email' => 'hr@cebucorp.com',
            'password' => Hash::make('password'),
            'role' => 'employer',
            'status' => 'active',
            'is_approved' => 1,
        ]);
        $this->employer = Employer::create([
            'user_id' => $this->employerUser->user_id,
            'company_name' => 'Cebu IT Solutions Inc',
            'is_accredited' => 1,
        ]);

        // 4. Create Employer 2
        $this->otherEmployerUser = User::create([
            'email' => 'hr@othercorp.com',
            'password' => Hash::make('password'),
            'role' => 'employer',
            'status' => 'active',
            'is_approved' => 1,
        ]);
        $this->otherEmployer = Employer::create([
            'user_id' => $this->otherEmployerUser->user_id,
            'company_name' => 'Other Business Group',
            'is_accredited' => 1,
        ]);

        // 5. Create Placement Report for Employer 1
        $reportData = [
            'month' => '2026-09',
            'company_name' => 'Cebu IT Solutions Inc',
            'total_hired' => 1,
            'hired_list' => [
                [
                    'jobseeker_name' => 'Juan Dela Cruz',
                    'position' => 'Junior Web Developer',
                    'hired_date' => '2026-09-01',
                    'referred_by_jpo' => 'Yes',
                ]
            ],
            'notes' => 'Q3 hires completed.',
            'submitted_at' => now()->toIso8601String(),
        ];

        $this->reportId = DB::table('placement_reports')->insertGetId([
            'employer_id' => $this->employer->employer_id,
            'jpo_id' => $jpoProfileId,
            'report_type' => 'employer_monthly',
            'report_month' => '2026-09-01',
            'report_data' => json_encode($reportData),
            'status' => 'submitted_to_jpo',
            'jpo_evaluated' => 0,
        ]);
    }

    public function test_employer_can_view_own_placement_reports_and_print_view(): void
    {
        $response = $this->actingAs($this->employerUser)
            ->get(route('employer.placement-reports'));

        $response->assertStatus(200)
            ->assertSee('Monthly Placement Reports')
            ->assertSee('Cebu IT Solutions Inc');

        $showResponse = $this->actingAs($this->employerUser)
            ->get(route('employer.placement-reports.show', $this->reportId));

        $showResponse->assertStatus(200)
            ->assertSee('Juan Dela Cruz')
            ->assertSee('Junior Web Developer')
            ->assertSee('Monthly Employer Placement Report');
    }

    public function test_employer_cannot_view_other_employer_placement_report(): void
    {
        $response = $this->actingAs($this->otherEmployerUser)
            ->get(route('employer.placement-reports.show', $this->reportId));

        $response->assertStatus(404);
    }

    public function test_jpo_can_view_placement_reports_and_inspect_details(): void
    {
        $response = $this->actingAs($this->jpoUser)
            ->get(route('jpo.evaluations.placement-reports'));

        $response->assertStatus(200)
            ->assertSee('Evaluate Placement Reports')
            ->assertSee('Cebu IT Solutions Inc');

        $showResponse = $this->actingAs($this->jpoUser)
            ->get(route('jpo.evaluations.placement-reports.show', $this->reportId));

        $showResponse->assertStatus(200)
            ->assertSee('Cebu IT Solutions Inc')
            ->assertSee('Juan Dela Cruz')
            ->assertSee('Junior Web Developer');
    }

    public function test_admin_can_view_placement_reports_directory_and_report_details(): void
    {
        $directoryResponse = $this->actingAs($this->adminUser)
            ->get(route('admin.placement-reports.index'));

        $directoryResponse->assertStatus(200)
            ->assertSee('Placement Reports Directory')
            ->assertSee('Cebu IT Solutions Inc');

        $showResponse = $this->actingAs($this->adminUser)
            ->get(route('admin.placement-reports.show', $this->reportId));

        $showResponse->assertStatus(200)
            ->assertSee('Cebu IT Solutions Inc')
            ->assertSee('Juan Dela Cruz')
            ->assertSee('Junior Web Developer');
    }
}
