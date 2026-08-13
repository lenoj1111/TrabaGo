<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'qualifications',
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
}