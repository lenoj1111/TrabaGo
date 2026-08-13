<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employer extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employers';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'employer_id';

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
        'user_id',
        'company_name',
        'is_accredited',
        'accredited_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_accredited' => 'boolean',
            'accredited_at' => 'date',
        ];
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the user associated with the employer.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the job postings for this employer.
     */
    public function jobPostings()
    {
        return $this->hasMany(JobPosting::class, 'employer_id', 'employer_id');
    }

    /**
     * Get the accreditation for this employer.
     */
    public function accreditation()
    {
        return $this->hasOne(EmployerAccreditation::class, 'employer_id', 'employer_id');
    }

    /**
     * Get the posting restrictions for this employer.
     */
    public function restrictions()
    {
        return $this->hasMany(PostingRestriction::class, 'employer_id', 'employer_id');
    }

    /**
     * Get the placement reports for this employer.
     */
    public function placementReports()
    {
        return $this->hasMany(PlacementReport::class, 'employer_id', 'employer_id');
    }
}