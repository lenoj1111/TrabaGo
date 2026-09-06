<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\EmployerAccreditation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmployerDocumentaryRequirementsTest extends TestCase
{
    use RefreshDatabase;

    protected User $employerUser;
    protected Employer $employer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->employerUser = User::create([
            'email' => 'accreditation_employer@example.com',
            'password' => Hash::make('password123'),
            'role' => 'employer',
            'status' => 'active',
            'is_approved' => 1,
        ]);

        $this->employer = Employer::create([
            'user_id' => $this->employerUser->user_id,
            'company_name' => 'Cebu Global Ventures Corp',
            'industry' => 'Information Technology',
            'company_address' => 'IT Park, Lahug, Cebu City',
            'contact_person' => 'Maria Santos',
            'contact_phone' => '09170001122',
            'is_accredited' => 0,
        ]);
    }

    public function test_employer_can_view_accreditation_page_with_official_requirements(): void
    {
        $response = $this->actingAs($this->employerUser)->get(route('employer.accreditation'));

        $response->assertStatus(200);
        $response->assertSee('Original and other documents, when applicable, should be presented for validation.');
        $response->assertSee('Required Documents and Validity Period');
        $response->assertSee('BIR Certificate of Registration (Form 2303)');
        $response->assertSee('SEC Registration or DTI Registration');
        $response->assertSee('PhilJobNet Proof of Registration');
        $response->assertSee('Updated Job Vacancies (use prescribed form)');
        $response->assertSee('DOLE License/DO 174');
        $response->assertSee('DMW License');
        $response->assertSee('DMW Approved and Validated Job Orders');
        $response->assertSee('Letter of Intent');
    }

    public function test_employer_can_upload_all_nine_documentary_requirements(): void
    {
        Storage::fake('public');

        $payload = [
            'bir_2303' => UploadedFile::fake()->create('bir_2303.pdf', 100, 'application/pdf'),
            'sec_dti_registration' => UploadedFile::fake()->create('sec_registration.pdf', 100, 'application/pdf'),
            'mayors_permit' => UploadedFile::fake()->create('mayors_permit_2026.pdf', 100, 'application/pdf'),
            'philjobnet_proof' => UploadedFile::fake()->create('philjobnet_cert.pdf', 100, 'application/pdf'),
            'updated_job_vacancies' => UploadedFile::fake()->create('job_vacancies_form.pdf', 100, 'application/pdf'),
            'dole_license' => UploadedFile::fake()->create('dole_license.pdf', 100, 'application/pdf'),
            'dmw_license' => UploadedFile::fake()->create('dmw_license.pdf', 100, 'application/pdf'),
            'dmw_job_orders' => UploadedFile::fake()->create('dmw_job_orders.pdf', 100, 'application/pdf'),
            'letter_of_intent' => UploadedFile::fake()->create('letter_of_intent.pdf', 100, 'application/pdf'),
        ];

        $response = $this->actingAs($this->employerUser)->post(route('employer.accreditation.submit'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $accreditation = EmployerAccreditation::where('employer_id', $this->employer->employer_id)->first();
        $this->assertNotNull($accreditation);

        $docs = is_array($accreditation->documents) ? $accreditation->documents : json_decode($accreditation->documents, true);

        $this->assertArrayHasKey('bir_2303', $docs);
        $this->assertArrayHasKey('sec_dti_registration', $docs);
        $this->assertArrayHasKey('mayors_permit', $docs);
        $this->assertArrayHasKey('philjobnet_proof', $docs);
        $this->assertArrayHasKey('updated_job_vacancies', $docs);
        $this->assertArrayHasKey('dole_license', $docs);
        $this->assertArrayHasKey('dmw_license', $docs);
        $this->assertArrayHasKey('dmw_job_orders', $docs);
        $this->assertArrayHasKey('letter_of_intent', $docs);

        // Verify storage exists for files
        Storage::disk('public')->assertExists($docs['bir_2303']['path']);
        Storage::disk('public')->assertExists($docs['mayors_permit']['path']);
    }

    public function test_employer_registration_page_contains_official_documentary_notice(): void
    {
        $response = $this->get(route('employer.register'));

        $response->assertStatus(200);
        $response->assertSee('Original and other documents, when applicable, should be presented for validation.');
        $response->assertSee('Required Documents and Validity Period');
        $response->assertSee('BIR Certificate of Registration (Form 2303)');
        $response->assertSee('SEC Registration or DTI Registration');
    }

    public function test_printable_establishment_form_renders_with_requirements_checklist(): void
    {
        $docs = [
            'bir_2303' => ['path' => 'accreditations/test/bir.pdf', 'original_name' => 'bir.pdf'],
            'sec_dti_registration' => ['path' => 'accreditations/test/sec.pdf', 'original_name' => 'sec.pdf'],
            'mayors_permit' => ['path' => 'accreditations/test/permit.pdf', 'original_name' => 'permit.pdf'],
            'philjobnet_proof' => ['path' => 'accreditations/test/philjobnet.pdf', 'original_name' => 'philjobnet.pdf'],
            'updated_job_vacancies' => ['path' => 'accreditations/test/vacancies.pdf', 'original_name' => 'vacancies.pdf'],
            'dole_license' => ['path' => 'accreditations/test/dole.pdf', 'original_name' => 'dole.pdf'],
        ];

        $acc = EmployerAccreditation::create([
            'employer_id' => $this->employer->employer_id,
            'status' => 'submitted_to_jpo',
            'document_status' => 'pending',
            'documents' => $docs,
        ]);

        $response = $this->actingAs($this->employerUser)->get(route('employer.accreditation.print'));

        $response->assertStatus(200);
        $response->assertSee('Original and other documents, when applicable, should be presented for validation.');
        $response->assertSee('BIR Certificate of Registration (Form 2303)');
        $response->assertSee('SEC Registration or DTI Registration');
        $response->assertSee('PhilJobNet Proof of Registration');
    }
}
