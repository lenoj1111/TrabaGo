<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JpoAssessment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jpo_assessments';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'assessment_id';

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
        'application_id',
        'jpo_id',
        'recommendation',
        'remarks',
        'referral_date',
        'referral_notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'referral_date' => 'date',
        ];
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the job application for this assessment.
     */
    public function jobApplication()
    {
        return $this->belongsTo(JobApplication::class, 'application_id', 'application_id');
    }

    /**
     * Get the JPO who made the assessment.
     */
    public function jpo()
    {
        return $this->belongsTo(UserProfile::class, 'jpo_id', 'profile_id');
    }
}