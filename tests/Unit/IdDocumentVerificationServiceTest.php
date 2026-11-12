<?php

namespace Tests\Unit;

use App\Models\Jobseeker;
use App\Services\IdDocumentVerificationService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IdDocumentVerificationServiceTest extends TestCase
{
    public function test_matching_name_and_birth_date_verifies_id(): void
    {
        Http::fake([
            'http://127.0.0.1:5000/scan' => Http::response([
                'text' => ['JUAN DELA CRUZ', 'DATE OF BIRTH 12/25/1990'],
            ]),
        ]);

        $result = $this->verifyId();

        $this->assertSame('verified', $result['status']);
        $this->assertNull($result['rejection_reason']);
    }

    public function test_unreadable_id_is_sent_for_manual_review(): void
    {
        Http::fake([
            'http://127.0.0.1:5000/scan' => Http::response(['text' => []]),
        ]);

        $result = $this->verifyId();

        $this->assertSame('under_review', $result['status']);
        $this->assertNotNull($result['rejection_reason']);
    }

    public function test_day_month_year_birth_date_is_detected_across_ocr_lines(): void
    {
        Http::fake([
            'http://127.0.0.1:5000/scan' => Http::response([
                'text' => ['JUAN DELA CRUZ', 'DATE OF BIRTH', '25 DECEMBER 1990'],
            ]),
        ]);

        $result = $this->verifyId();

        $this->assertSame('verified', $result['status']);
    }

    public function test_day_first_numeric_birth_date_is_detected(): void
    {
        Http::fake([
            'http://127.0.0.1:5000/scan' => Http::response([
                'text' => ['JUAN DELA CRUZ', 'DOB 25/12/1990'],
            ]),
        ]);

        $result = $this->verifyId();

        $this->assertSame('verified', $result['status']);
    }

    public function test_profile_name_mismatch_rejects_id(): void
    {
        Http::fake([
            'http://127.0.0.1:5000/scan' => Http::response([
                'text' => ['MARIA SANTOS', 'DATE OF BIRTH 12/25/1990'],
            ]),
        ]);

        $result = $this->verifyId();

        $this->assertSame('rejected', $result['status']);
        $this->assertStringContainsString('name', strtolower($result['rejection_reason']));
    }

    private function verifyId(): array
    {
        $imagePath = tempnam(sys_get_temp_dir(), 'trabago-id-');
        $this->assertNotFalse($imagePath);
        file_put_contents($imagePath, 'test image');

        try {
            $jobseeker = new Jobseeker();
            $jobseeker->first_name = 'Juan';
            $jobseeker->middle_name = 'Dela';
            $jobseeker->last_name = 'Cruz';
            $jobseeker->birth_date = '1990-12-25';

            return (new IdDocumentVerificationService())->verify($jobseeker, $imagePath);
        } finally {
            if (is_file($imagePath)) {
                unlink($imagePath);
            }
        }
    }
}
