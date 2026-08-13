<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'job_applications';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'application_id';

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
        'job_id',
        'jobseeker_id',
        'status',
        'referred_by_jpo',
        'interview_schedule',
        'interview_mode',
        'interview_location',
        'interview_status',
        'jobseeker_response',
        'hired_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'referred_by_jpo' => 'boolean',
            'interview_schedule' => 'datetime',
            'hired_date' => 'date',
        ];
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the job posting for this application.
     */
    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class, 'job_id', 'job_id');
    }

    /**
     * Get the jobseeker for this application.
     */
    public function jobseeker()
    {
        return $this->belongsTo(Jobseeker::class, 'jobseeker_id', 'jobseeker_id');
    }

    /**
     * Get the JPO assessment for this application.
     */
    public function jpoAssessment()
    {
        return $this->hasOne(JpoAssessment::class, 'application_id', 'application_id');
    }
}