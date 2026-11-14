<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingProgram extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'training_programs';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'training_id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'trainer_id',
        'title',
        'training_type',
        'duration_months',
        'location',
        'is_archived',
        'training_start_date',
        'training_end_date',
        'trainee_capacity',
        'description',
        'auto_generate_certificate',
        'skills',
        'cert_content',
        'passing_score',
        'demographic_requirements',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'auto_generate_certificate' => 'boolean',
            'duration_months' => 'integer',
            'trainee_capacity' => 'integer',
            'passing_score' => 'integer',
            'demographic_requirements' => 'array',
            'cert_content' => 'array',
        ];
    }

    public function demographicEligibilityIssues(Jobseeker $jobseeker): array
    {
        $requirements = $this->demographic_requirements ?? [];
        $issues = [];

        if (!empty($requirements['gender']) && strcasecmp(trim((string) $jobseeker->sex), $requirements['gender']) !== 0) {
            $issues[] = 'This course is limited to ' . strtolower($requirements['gender']) . ' applicants.';
        }

        $minimumAge = $requirements['min_age'] ?? null;
        $maximumAge = $requirements['max_age'] ?? null;
        if ($minimumAge !== null || $maximumAge !== null) {
            $age = $jobseeker->birth_date?->age;
            if ($age === null) {
                $issues[] = 'Your birth date is required to confirm the course age requirement.';
            } elseif (($minimumAge !== null && $age < $minimumAge) || ($maximumAge !== null && $age > $maximumAge)) {
                $range = $minimumAge !== null && $maximumAge !== null
                    ? "ages {$minimumAge}-{$maximumAge}"
                    : ($minimumAge !== null ? "age {$minimumAge} or older" : "age {$maximumAge} or younger");
                $issues[] = "This course is limited to applicants {$range}.";
            }
        }

        $requiredEducation = $requirements['education_levels'] ?? [];
        if ($requiredEducation) {
            $education = $jobseeker->details?->education ?? [];
            if (is_string($education)) {
                $education = json_decode($education, true) ?: [];
            }
            $attainment = is_array($education) ? ($education['level'] ?? '') : '';
            $matchesEducation = collect($requiredEducation)->contains(
                fn($level) => strcasecmp(trim((string) $level), trim((string) $attainment)) === 0
            );

            if (!$matchesEducation) {
                $issues[] = 'Your recorded educational attainment does not meet this course requirement.';
            }
        }

        return $issues;
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the trainer for this training program.
     */
    public function trainer()
    {
        return $this->belongsTo(UserProfile::class, 'trainer_id', 'profile_id');
    }

    /**
     * Get the topics for this training program.
     */
    public function topics()
    {
        return $this->hasMany(TrainingTopic::class, 'training_id', 'training_id');
    }

    /**
     * Get the trainer-created assessments for this training program.
     */
    public function assessments()
    {
        return $this->hasMany(TrainingAssessment::class, 'training_id', 'training_id');
    }

    /**
     * Get the enrollments for this training program.
     */
    public function enrollments()
    {
        return $this->hasMany(TrainingEnrollment::class, 'training_id', 'training_id');
    }
}
