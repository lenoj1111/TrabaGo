<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlacementReport extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'placement_reports';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'report_id';

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
        'jpo_id',
        'employer_id',
        'report_type',
        'report_month',
        'report_data',
        'status',
        'jpo_evaluated',
        'jpo_evaluated_at',
        'jpo_remarks',
        'admin_remarks',
        'approved_by',
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
            'report_month' => 'date',
            'report_data' => 'array',
            'jpo_evaluated' => 'boolean',
            'jpo_evaluated_at' => 'datetime',
            'approved_at' => 'date',
        ];
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the JPO who created the report.
     */
    public function jpo()
    {
        return $this->belongsTo(UserProfile::class, 'jpo_id', 'profile_id');
    }

    /**
     * Get the employer for this report.
     */
    public function employer()
    {
        return $this->belongsTo(Employer::class, 'employer_id', 'employer_id');
    }

    /**
     * Get the admin who approved the report.
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }
}