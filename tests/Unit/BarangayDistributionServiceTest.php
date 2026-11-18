<?php

namespace Tests\Unit;

use App\Models\Jobseeker;
use App\Models\JobseekerDetail;
use App\Models\JobseekerSkill;
use App\Models\User;
use App\Services\BarangayDistributionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarangayDistributionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_includes_both_skill_types_and_respects_skill_type_filter(): void
    {
        $user = User::create([
            'email' => 'jobseeker@example.com',
            'phone_number' => '09123456789',
            'password' => 'secret123',
            'role' => 'jobseeker',
            'status' => 'active',
            'is_approved' => true,
            'is_contact_verified' => true,
            'account_status' => 'active',
        ]);

        $jobseeker = Jobseeker::create([
            'user_id' => $user->user_id,
            'first_name' => 'Maria',
            'last_name' => 'Dela Cruz',
            'email' => 'maria@example.com',
            'employment_status' => 'unemployed',
        ]);

        JobseekerDetail::create([
            'jobseeker_id' => $jobseeker->jobseeker_id,
            'address' => ['barangay' => 'Lahug'],
        ]);

        JobseekerSkill::create([
            'jobseeker_id' => $jobseeker->jobseeker_id,
            'skill_name' => 'Excel',
            'skill_type' => 'technical',
        ]);

        JobseekerSkill::create([
            'jobseeker_id' => $jobseeker->jobseeker_id,
            'skill_name' => 'Communication',
            'skill_type' => '21st_century',
        ]);

        $service = new BarangayDistributionService();

        $allData = $service->getBarangayDistributionData();
        $lahugAll = collect($allData['items'])->firstWhere('name', 'Lahug');

        $this->assertNotNull($lahugAll);
        $this->assertStringContainsString('Excel', $lahugAll['top_skill']);
        $this->assertStringContainsString('Communication', $lahugAll['top_skill']);

        $technicalOnly = $service->getBarangayDistributionData(['skill_type' => 'technical']);
        $lahugTechnical = collect($technicalOnly['items'])->firstWhere('name', 'Lahug');

        $this->assertNotNull($lahugTechnical);
        $this->assertStringContainsString('Excel', $lahugTechnical['top_skill']);
        $this->assertStringNotContainsString('Communication', $lahugTechnical['top_skill']);
    }
}
