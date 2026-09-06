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
    public $timestamps = true;

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
        'jpo_notes',
        'jpo_evaluated_at',
        'jpo_id',
        'interview_schedule',
        'interview_mode',
        'interview_location',
        'interview_status',
        'jobseeker_response',
        'hired_date',
        'offered_at',
        'offer_salary',
        'offer_start_date',
        'offer_notes',
        'declined_at',
        'decline_reason',
        'resignation_status',
        'resignation_reason',
        'resignation_requested_at',
        'resignation_approved_at',
        'resignation_remarks',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::saved(function (JobApplication $application) {
            $jobseeker = $application->jobseeker;
            if (!$jobseeker) {
                return;
            }

            if ($application->status === 'hired') {
                if ($application->resignation_status === 'approved') {
                    // Check if jobseeker has any other active hired job
                    $hasOtherHired = $jobseeker->applications()
                        ->where('application_id', '!=', $application->application_id)
                        ->where('status', 'hired')
                        ->where(function ($q) {
                            $q->whereNull('resignation_status')
                              ->orWhere('resignation_status', '!=', 'approved');
                        })
                        ->exists();

                    if (!$hasOtherHired) {
                        $jobseeker->update([
                            'employment_status' => 'Unemployed',
                            'hired_company' => null,
                        ]);
                    }
                } else {
                    $company = $application->jobPosting?->employer?->company_name;
                    $updates = ['employment_status' => 'Employed'];
                    if ($company) {
                        $updates['hired_company'] = $company;
                    }
                    $jobseeker->update($updates);
                }
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'referred_by_jpo' => 'boolean',
            'jpo_evaluated_at' => 'datetime',
            'interview_schedule' => 'datetime',
            'hired_date' => 'date',
            'offered_at' => 'datetime',
            'offer_salary' => 'decimal:2',
            'offer_start_date' => 'date',
            'declined_at' => 'datetime',
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