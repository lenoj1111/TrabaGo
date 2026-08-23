<?php

namespace Tests\Unit;

use App\Services\SkillMatchingService;
use PHPUnit\Framework\TestCase;

class SkillMatchingServiceTest extends TestCase
{
    public function test_perfect_match_returns_100_percent()
    {
        $service = new SkillMatchingService();
        $userSkills = ['PHP', 'Laravel', 'SQL'];
        $jobSkills = ['PHP', 'Laravel', 'SQL'];

        $result = $service->calculateMatch($userSkills, $jobSkills);

        $this->assertEquals(100, $result['percentage']);
        $this->assertEquals('Excellent Match', $result['tier']);
        $this->assertCount(3, $result['matchedSkills']);
        $this->assertEmpty($result['missingSkills']);
    }

    public function test_partial_match_returns_cosine_percentage()
    {
        $service = new SkillMatchingService();
        $userSkills = ['PHP', 'Laravel'];
        $jobSkills = ['PHP', 'Laravel', 'Vue.js', 'SQL'];

        $result = $service->calculateMatch($userSkills, $jobSkills);

        // Vector user: [1, 1, 0, 0] (len = sqrt(2)), Vector job: [1, 1, 1, 1] (len = sqrt(4) = 2)
        // Dot product: 2
        // Cosine = 2 / (sqrt(2) * 2) = 1 / sqrt(2) ≈ 0.7071 => 71%
        $this->assertEquals(71, $result['percentage']);
        $this->assertEquals('High Match', $result['tier']);
        $this->assertContains('PHP', $result['matchedSkills']);
        $this->assertContains('Laravel', $result['matchedSkills']);
        $this->assertContains('Vue.js', $result['missingSkills']);
        $this->assertContains('SQL', $result['missingSkills']);
    }

    public function test_empty_user_skills_returns_zero_percent()
    {
        $service = new SkillMatchingService();
        $userSkills = [];
        $jobSkills = ['React', 'TypeScript'];

        $result = $service->calculateMatch($userSkills, $jobSkills);

        $this->assertEquals(0, $result['percentage']);
        $this->assertEquals('Low Match', $result['tier']);
        $this->assertCount(2, $result['missingSkills']);
    }
}
