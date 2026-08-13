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
        'description',
    ];

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
     * Get the enrollments for this training program.
     */
    public function enrollments()
    {
        return $this->hasMany(TrainingEnrollment::class, 'training_id', 'training_id');
    }
}