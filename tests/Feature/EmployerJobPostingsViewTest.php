<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmployerJobPostingsViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_postings_page_renders_with_functional_buttons_and_no_emojis(): void
    {
        $user = User::create([
            'email' => 'employer@cebutech.com',
            'password' => Hash::make('secret123'),
            'role' => 'employer',
            'status' => 'active',
            'is_approved' => 1,
        ]);

        $employer = Employer::create([
            'user_id' => $user->user_id,
            'company_name' => 'Cebu Tech Solutions Inc.',
            'is_accredited' => 1,
        ]);

        $job = JobPosting::create([
            'employer_id' => $employer->employer_id,
            'title' => "Baker's Assistant & IT Specialist",
            'description' => "We're looking for enthusiastic candidates.",
            'qualifications' => "Bachelor's Degree in CS\nDriver's License",
            'vacancy_count' => 2,
            'status' => 'approved',
            'created_by' => 'employer',
        ]);

        $response = $this->actingAs($user)->get(route('employer.job-postings'));
        $response->assertStatus(200);

        $content = $response->getContent();

        // 1. Verify View and Edit button handlers are hooked up via job ID
        $this->assertStringContainsString("openView({$job->job_id})", $content);
        $this->assertStringContainsString("openEdit({$job->job_id})", $content);
        $this->assertStringContainsString('jobPostingsManager()', $content);

        // 2. Verify NO emojis are rendered anywhere in the HTML
        preg_match_all('/[\x{1F300}-\x{1FAD6}\x{2600}-\x{27BF}\x{1F600}-\x{1F64F}\x{1F680}-\x{1F6FF}\x{2300}-\x{23FF}\x{2B50}]/u', $content, $matches);
        $this->assertEmpty($matches[0], 'Found unexpected emojis in HTML: ' . implode(' ', $matches[0]));
    }

    public function test_job_postings_show_page_renders_with_no_emojis(): void
    {
        $user = User::create([
            'email' => 'employer@cebutech.com',
            'password' => Hash::make('secret123'),
            'role' => 'employer',
            'status' => 'active',
            'is_approved' => 1,
        ]);

        $employer = Employer::create([
            'user_id' => $user->user_id,
            'company_name' => 'Cebu Tech Solutions Inc.',
            'is_accredited' => 1,
        ]);

        $job = JobPosting::create([
            'employer_id' => $employer->employer_id,
            'title' => "Senior Systems Engineer",
            'description' => "Design enterprise architecture.",
            'vacancy_count' => 1,
            'status' => 'approved',
            'created_by' => 'employer',
        ]);

        $response = $this->actingAs($user)->get(route('employer.job-postings.show', $job->job_id));
        $response->assertStatus(200);

        $content = $response->getContent();

        // Verify NO emojis are rendered anywhere on the show page
        preg_match_all('/[\x{1F300}-\x{1FAD6}\x{2600}-\x{27BF}\x{1F600}-\x{1F64F}\x{1F680}-\x{1F6FF}\x{2300}-\x{23FF}\x{2B50}]/u', $content, $matches);
        $this->assertEmpty($matches[0], 'Found unexpected emojis in Show HTML: ' . implode(' ', $matches[0]));
    }
}
