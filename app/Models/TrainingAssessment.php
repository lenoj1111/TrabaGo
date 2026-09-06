<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingAssessment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'training_assessments';

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
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'training_id',
        'question',
        'question_type',
        'options',
        'correct_answer',
        'explanation',
        'points',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'options' => 'array',
            'correct_answer' => 'integer',
            'points' => 'integer',
        ];
    }

    /**
     * Get the training program associated with this assessment question.
     */
    public function trainingProgram()
    {
        return $this->belongsTo(TrainingProgram::class, 'training_id', 'training_id');
    }
}
