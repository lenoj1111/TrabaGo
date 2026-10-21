<?php

namespace Tests\Unit;

use App\Models\JobPosting;
use App\Models\Jobseeker;
use App\Models\JobseekerDetail;
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

    public function test_job_required_skills_are_included_even_when_qualifications_are_present()
    {
        $service = new SkillMatchingService();
        $job = new JobPosting([
            'title' => 'Automotive Technician',
            'skills' => "Oil Change\nVehicle Inspection",
            'qualifications' => "High school diploma\nOil Change",
        ]);

        $this->assertSame(
            ['Oil Change', 'Vehicle Inspection', 'High school diploma'],
            $service->getJobSkills($job)
        );
    }

    public function test_skill_matrix_matches_skill_phrases_in_job_description_when_requirements_are_omitted()
    {
        $service = new SkillMatchingService();
        $job = new JobPosting([
            'title' => 'Oil Changer',
            'description' => 'Taga Oil Change',
        ]);

        $this->assertSame(['Oil Change'], $service->getJobSkills($job, ['Oil Change', 'Customer Service']));
    }

    public function test_partial_skill_names_do_not_match_inside_unrelated_words()
    {
        $service = new SkillMatchingService();

        $result = $service->calculateMatch(['SQL'], ['NoSQL']);

        $this->assertEmpty($result['matchedSkills']);
        $this->assertSame(['NoSQL'], $result['missingSkills']);
    }

    public function test_senior_high_school_education_matches_highschool_graduate_requirement()
    {
        $service = new SkillMatchingService();
        $jobseeker = new Jobseeker();
        $jobseeker->setRelation('skills', collect());
        $jobseeker->setRelation('details', new JobseekerDetail([
            'education' => ['level' => 'Senior High School'],
        ]));

        $userSkills = $service->getJobseekerSkills($jobseeker);
        $result = $service->calculateMatch($userSkills, ['Highschool Graduate']);

        $this->assertContains('Senior High School Graduate', $userSkills);
        $this->assertSame(['Highschool Graduate'], $result['matchedSkills']);
        $this->assertEmpty($result['missingSkills']);
    }
}
