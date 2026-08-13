<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_profiles';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'profile_id';

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
        'full_name',
        'phone',
        'position',
        'department',
        'office',
        'specialization',
        'area',
        'trainer_type',
        'partner_institution',
        'is_trainer_approved',
        'trainer_approved_by',
        'trainer_approved_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_trainer_approved' => 'boolean',
            'trainer_approved_at' => 'date',
        ];
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the user associated with the profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the trainer who approved this profile.
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'trainer_approved_by', 'user_id');
    }

    /**
     * Get the training programs for this trainer.
     */
    public function trainingPrograms()
    {
        return $this->hasMany(TrainingProgram::class, 'trainer_id', 'profile_id');
    }

    /**
     * Get the JPO assessments.
     */
    public function jpoAssessments()
    {
        return $this->hasMany(JpoAssessment::class, 'jpo_id', 'profile_id');
    }

    /**
     * Get the placement reports.
     */
    public function placementReports()
    {
        return $this->hasMany(PlacementReport::class, 'jpo_id', 'profile_id');
    }
}