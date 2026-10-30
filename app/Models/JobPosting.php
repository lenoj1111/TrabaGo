<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class JobPosting extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'job_postings';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'job_id';

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
        'employer_id',
        'admin_id',
        'title',
        'description',
        'job_type',
        'location',
        'salary_range',
        'benefits',
        'other_instructions',
        'qualifications',
        'skills',
        'vacancy_count',
        'valid_until',
        'accepts_disability',
        'disability_type',
        'status',
        'created_by',
        'created_at',
        'approved_at',
    ];

    /**
     * Helper accessor to get skills formatted as an array of items for bullet points.
     */
    public function getSkillsListAttribute(): array
    {
        if (empty($this->skills)) {
            return [];
        }
        $lines = preg_split('/[\r\n]+/', (string)$this->skills);
        return array_values(array_filter(array_map('trim', $lines)));
    }

    /**
     * Helper accessor to get qualifications formatted as an array of items for bullet points.
     */
    public function getQualificationsListAttribute(): array
    {
        if (empty($this->qualifications)) {
            return [];
        }
        $lines = preg_split('/[\r\n]+/', (string)$this->qualifications);
        return array_values(array_filter(array_map('trim', $lines)));
    }

    /**
     * Helper accessor to get benefits formatted as an array of items for bullet points.
     */
    public function getBenefitsListAttribute(): array
    {
        if (empty($this->benefits)) {
            return [];
        }
        $lines = preg_split('/[\r\n]+/', (string)$this->benefits);
        return array_values(array_filter(array_map('trim', $lines)));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'accepts_disability' => 'boolean',
            'vacancy_count' => 'integer',
            'valid_until' => 'date',
            'created_at' => 'date',
            'approved_at' => 'date',
        ];
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the employer that owns the job posting.
     */
    public function employer()
    {
        return $this->belongsTo(Employer::class, 'employer_id', 'employer_id');
    }

    /**
     * Get the admin who approved the job posting.
     */
    public function admin()
    {
        return $this->belongsTo(UserProfile::class, 'admin_id', 'profile_id');
    }

    /**
     * Get the applications for this job posting.
     */
    public function applications()
    {
        return $this->hasMany(JobApplication::class, 'job_id', 'job_id');
    }

    /**
     * Scope a query to only include active and unexpired job postings for jobseekers.
     */
    public function scopeAvailableForJobseekers(Builder $query)
    {
        return $query->where('status', 'approved')
            ->where(function ($q) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', now()->toDateString());
            });
    }

    /**
     * Check if the job posting is expired.
     */
    public function isExpired(): bool
    {
        if (!$this->valid_until) {
            return false;
        }

        return $this->valid_until->format('Y-m-d') < now()->toDateString();
    }
}