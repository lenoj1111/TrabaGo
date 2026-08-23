<?php

namespace App\Services;

use App\Models\JobPosting;
use App\Models\Jobseeker;
use App\Models\TrainingProgram;

class SkillMatchingService
{
    /**
     * Normalize a skill string for comparison (lowercase, trimmed).
     */
    public function normalizeSkill(string $skill): string
    {
        return strtolower(trim($skill));
    }

    /**
     * Extract skills array from a JobPosting model.
     * Checks qualifications lines, requirements, or common keywords.
     */
    public function getJobSkills(JobPosting $job): array
    {
        $skills = [];

        if (!empty($job->qualifications)) {
            $lines = preg_split('/[\r\n,;]+/', $job->qualifications);
            foreach ($lines as $line) {
                $cleaned = trim(preg_replace('/^[\s\-\*\•\d\.\)]+/', '', $line));
                if (!empty($cleaned) && strlen($cleaned) >= 2) {
                    $skills[] = $cleaned;
                }
            }
        }

        // Fallback or defaults if qualifications text is too short
        if (empty($skills) && !empty($job->title)) {
            $titleWords = explode(' ', $job->title);
            $skills = array_filter($titleWords, fn($w) => strlen($w) > 3);
        }

        return array_values(array_unique($skills));
    }

    /**
     * Extract skills array from a Jobseeker model.
     */
    public function getJobseekerSkills(?Jobseeker $jobseeker): array
    {
        if (!$jobseeker) {
            return [];
        }

        // 1. From jobseeker_skills relation
        $skills = $jobseeker->skills ? $jobseeker->skills->pluck('skill_name')->toArray() : [];

        // 2. From jobseeker details json if any (ONLY from verified course_title or actual skill items, NOT file names like .pdf, .docx, etc.)
        if ($jobseeker->details && !empty($jobseeker->details->training_certificates)) {
            $certs = is_array($jobseeker->details->training_certificates) 
                ? $jobseeker->details->training_certificates 
                : json_decode($jobseeker->details->training_certificates, true);
            if (is_array($certs)) {
                foreach ($certs as $cert) {
                    if (is_string($cert)) {
                        // Skip if it is a raw file path or filename
                        if (!preg_match('/\.(pdf|docx?|jpe?g|png|txt|csv)$/i', $cert)) {
                            $skills[] = $cert;
                        }
                    } elseif (is_array($cert)) {
                        if (!empty($cert['course_title'])) {
                            $skills[] = $cert['course_title'];
                        } elseif (!empty($cert['skill_name'])) {
                            $skills[] = $cert['skill_name'];
                        } elseif (!empty($cert['name'])) {
                            $name = $cert['name'];
                            // Do NOT add file names (e.g. resume.pdf, offer sheet.pdf) as skills
                            if (!preg_match('/\.(pdf|docx?|jpe?g|png|txt|csv)$/i', $name) && !in_array(strtolower($name), ['resume', 'valid_id', 'pwd_id', 'document'])) {
                                $skills[] = $name;
                            }
                        }
                    }
                }
            }
        }

        // Clean and sanitize skills
        $cleanedSkills = [];
        foreach ($skills as $skill) {
            $s = trim((string)$skill);
            if (empty($s) || $s === '[object Object]' || $s === 'object Object') {
                continue;
            }
            if (preg_match('/\.(pdf|docx?|jpe?g|png|txt|csv)$/i', $s)) {
                continue;
            }
            $cleanedSkills[] = $s;
        }

        return array_values(array_unique($cleanedSkills));
    }

    /**
     * Calculate Cosine Similarity between user's skills and a job's required skills.
     *
     * @param array $userSkills
     * @param array $jobSkills
     * @return array
     */
    public function calculateMatch(array $userSkills, array $jobSkills): array
    {
        if (empty($jobSkills)) {
            return [
                'score' => 100,
                'percentage' => 100,
                'tier' => 'Excellent Match',
                'badgeClass' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                'dotColor' => 'bg-emerald-500',
                'matchedSkills' => $userSkills,
                'missingSkills' => [],
                'matchedCount' => count($userSkills),
                'totalRequired' => 0,
            ];
        }

        if (empty($userSkills)) {
            return [
                'score' => 0,
                'percentage' => 0,
                'tier' => 'Low Match',
                'badgeClass' => 'bg-slate-100 text-slate-700 border-slate-300',
                'dotColor' => 'bg-slate-400',
                'matchedSkills' => [],
                'missingSkills' => $jobSkills,
                'matchedCount' => 0,
                'totalRequired' => count($jobSkills),
            ];
        }

        // Normalize skill sets
        $normalizedUser = array_map([$this, 'normalizeSkill'], $userSkills);
        $normalizedJob = array_map([$this, 'normalizeSkill'], $jobSkills);

        // Build combined vocabulary
        $vocab = array_values(array_unique(array_merge($normalizedUser, $normalizedJob)));

        // Create vectors
        $userVec = [];
        $jobVec = [];

        foreach ($vocab as $word) {
            // Check direct match or substring match
            $inUser = false;
            foreach ($normalizedUser as $uSkill) {
                if ($uSkill === $word || str_contains($word, $uSkill) || str_contains($uSkill, $word)) {
                    $inUser = true;
                    break;
                }
            }

            $inJob = false;
            foreach ($normalizedJob as $jSkill) {
                if ($jSkill === $word || str_contains($word, $jSkill) || str_contains($jSkill, $word)) {
                    $inJob = true;
                    break;
                }
            }

            $userVec[] = $inUser ? 1 : 0;
            $jobVec[] = $inJob ? 1 : 0;
        }

        // Cosine Similarity calculation
        $dotProduct = 0;
        $magUserSq = 0;
        $magJobSq = 0;

        for ($i = 0; $i < count($vocab); $i++) {
            $dotProduct += $userVec[$i] * $jobVec[$i];
            $magUserSq += $userVec[$i] * $userVec[$i];
            $magJobSq += $jobVec[$i] * $jobVec[$i];
        }

        $magnitude = sqrt($magUserSq) * sqrt($magJobSq);
        $similarity = $magnitude > 0 ? ($dotProduct / $magnitude) : 0;
        $percentage = (int) round($similarity * 100);

        // Classify matched and missing skills (referencing original strings)
        $matchedSkills = [];
        $missingSkills = [];

        foreach ($jobSkills as $origJobSkill) {
            $normJ = $this->normalizeSkill($origJobSkill);
            $matched = false;
            foreach ($userSkills as $origUserSkill) {
                $normU = $this->normalizeSkill($origUserSkill);
                if ($normU === $normJ || str_contains($normJ, $normU) || str_contains($normU, $normJ)) {
                    $matched = true;
                    break;
                }
            }

            if ($matched) {
                $matchedSkills[] = $origJobSkill;
            } else {
                $missingSkills[] = $origJobSkill;
            }
        }

        // Tier classification
        if ($percentage >= 90) {
            $tier = 'Excellent Match';
            $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            $dotColor = 'bg-emerald-500';
        } elseif ($percentage >= 70) {
            $tier = 'High Match';
            $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
            $dotColor = 'bg-blue-500';
        } elseif ($percentage >= 50) {
            $tier = 'Moderate Match';
            $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
            $dotColor = 'bg-amber-500';
        } else {
            $tier = 'Low Match';
            $badgeClass = 'bg-slate-100 text-slate-700 border-slate-200';
            $dotColor = 'bg-slate-400';
        }

        return [
            'score' => $percentage,
            'percentage' => $percentage,
            'tier' => $tier,
            'badgeClass' => $badgeClass,
            'dotColor' => $dotColor,
            'matchedSkills' => array_values(array_unique($matchedSkills)),
            'missingSkills' => array_values(array_unique($missingSkills)),
            'matchedCount' => count($matchedSkills),
            'totalRequired' => count($jobSkills),
        ];
    }

    /**
     * Rank a collection of jobs by Cosine Similarity match for a jobseeker.
     */
    public function rankJobsForJobseeker(iterable $jobs, ?Jobseeker $jobseeker): array
    {
        $userSkills = $this->getJobseekerSkills($jobseeker);
        $ranked = [];

        foreach ($jobs as $job) {
            $jobSkills = $this->getJobSkills($job);
            $matchResult = $this->calculateMatch($userSkills, $jobSkills);

            $ranked[] = [
                'job' => $job,
                'job_id' => $job->job_id,
                'match' => $matchResult,
                'skills' => $jobSkills,
            ];
        }

        // Sort descending by percentage score
        usort($ranked, function ($a, $b) {
            if ($a['match']['percentage'] === $b['match']['percentage']) {
                return $b['job']->job_id <=> $a['job']->job_id;
            }
            return $b['match']['percentage'] <=> $a['match']['percentage'];
        });

        return $ranked;
    }
}
