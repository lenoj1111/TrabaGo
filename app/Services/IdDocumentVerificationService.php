<?php

namespace App\Services;

use App\Models\Jobseeker;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IdDocumentVerificationService
{
    /**
     * @return array{status: 'verified'|'rejected'|'under_review', rejection_reason: ?string}
     */
    public function verify(Jobseeker $jobseeker, string $absolutePath): array
    {
        $ocrText = $this->readText($absolutePath);
        if ($ocrText === null) {
            return [
                'status' => 'under_review',
                'rejection_reason' => 'Automatic ID reading was unavailable. Your ID was submitted for manual review.',
            ];
        }

        $registeredName = trim(implode(' ', array_filter([
            $jobseeker->first_name,
            $jobseeker->middle_name,
            $jobseeker->last_name,
        ])));

        if ($registeredName === '') {
            return [
                'status' => 'under_review',
                'rejection_reason' => 'Your profile name is incomplete. Your ID was submitted for manual review.',
            ];
        }

        $normalizedOcr = $this->normalizeText($ocrText);
        foreach (explode(' ', $this->normalizeText($registeredName)) as $namePart) {
            if ($namePart !== '' && !preg_match('/\b' . preg_quote($namePart, '/') . '\b/', $normalizedOcr)) {
                return [
                    'status' => 'rejected',
                    'rejection_reason' => 'The name on this ID does not match your registered profile name.',
                ];
            }
        }

        if (!$jobseeker->birth_date) {
            return [
                'status' => 'under_review',
                'rejection_reason' => 'Your profile date of birth is missing. Your ID was submitted for manual review.',
            ];
        }

        $registeredDob = $jobseeker->birth_date->format('Y-m-d');
        $ocrDob = $this->extractDate($ocrText);

        if ($ocrDob === null) {
            return [
                'status' => 'under_review',
                'rejection_reason' => 'The date of birth could not be read automatically. Your ID was submitted for manual review.',
            ];
        }

        if ($ocrDob !== $registeredDob) {
            return [
                'status' => 'rejected',
                'rejection_reason' => 'The date of birth on this ID does not match your registered profile.',
            ];
        }

        return ['status' => 'verified', 'rejection_reason' => null];
    }

    private function readText(string $absolutePath): ?string
    {
        if (!is_file($absolutePath)) {
            Log::error('ID OCR failed: image file is missing.');
            return null;
        }

        $image = fopen($absolutePath, 'r');
        if ($image === false) {
            Log::error('ID OCR failed: image file could not be opened.');
            return null;
        }

        try {
            $response = Http::timeout(90)
                ->attach('image', $image, basename($absolutePath))
                ->post(config('services.ocr.url', 'http://127.0.0.1:5000/scan'));

            if (!$response->successful()) {
                Log::error('ID OCR server returned an unsuccessful response.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $lines = $response->json('text');
            if (!is_array($lines)) {
                Log::error('ID OCR server returned an invalid response format.');
                return null;
            }

            $text = trim(implode("\n", array_filter($lines, 'is_string')));
            return $text === '' ? null : $text;
        } catch (\Throwable $exception) {
            Log::error('ID OCR request failed: ' . $exception->getMessage());
            return null;
        } finally {
            fclose($image);
        }
    }

    private function normalizeText(string $text): string
    {
        $normalized = strtoupper($text);
        $normalized = preg_replace('/[^A-Z\s]/', ' ', $normalized) ?? '';
        return trim(preg_replace('/\s+/', ' ', $normalized) ?? '');
    }

    private function extractDate(string $text): ?string
    {
        $months = [
            'JANUARY' => '01', 'JAN' => '01',
            'FEBRUARY' => '02', 'FEB' => '02',
            'MARCH' => '03', 'MAR' => '03',
            'APRIL' => '04', 'APR' => '04',
            'MAY' => '05',
            'JUNE' => '06', 'JUN' => '06',
            'JULY' => '07', 'JUL' => '07',
            'AUGUST' => '08', 'AUG' => '08',
            'SEPTEMBER' => '09', 'SEP' => '09',
            'OCTOBER' => '10', 'OCT' => '10',
            'NOVEMBER' => '11', 'NOV' => '11',
            'DECEMBER' => '12', 'DEC' => '12',
        ];

        $upper = strtoupper($text);
        $monthPattern = implode('|', array_keys($months));
        if (preg_match('/\b(' . $monthPattern . ')\s+(\d{1,2})[,\s]+(\d{4})\b/', $upper, $match)) {
            return $this->makeDate((int) $match[3], (int) $months[$match[1]], (int) $match[2]);
        }

        if (preg_match('/\b(\d{4})[\/\-.](\d{1,2})[\/\-.](\d{1,2})\b/', $upper, $match)) {
            return $this->makeDate((int) $match[1], (int) $match[2], (int) $match[3]);
        }

        if (preg_match('/\b(\d{1,2})[\/\-.](\d{1,2})[\/\-.](\d{4})\b/', $upper, $match)) {
            return $this->makeDate((int) $match[3], (int) $match[1], (int) $match[2]);
        }

        return null;
    }

    private function makeDate(int $year, int $month, int $day): ?string
    {
        if ($year < 1900 || $year > 2100 || !checkdate($month, $day, $year)) {
            return null;
        }

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }
}
