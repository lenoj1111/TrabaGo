<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingEnrollment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'training_enrollments';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'enrollment_id';

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
        'jobseeker_id',
        'training_id',
        'training_type',
        'status',
        'current_topic',
        'start_date',
        'end_date',
        'lab_remarks',
        'answers',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'answers' => 'array',
        ];
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the jobseeker for this enrollment.
     */
    public function jobseeker()
    {
        return $this->belongsTo(Jobseeker::class, 'jobseeker_id', 'jobseeker_id');
    }

    /**
     * Get the training program for this enrollment.
     */
    public function trainingProgram()
    {
        return $this->belongsTo(TrainingProgram::class, 'training_id', 'training_id');
    }
}