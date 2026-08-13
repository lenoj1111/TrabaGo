<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobseekerDetail extends Model
{
    protected $table = 'jobseeker_details';
    protected $primaryKey = 'detail_id';
    public $timestamps = false;

    protected $fillable = [
        'jobseeker_id',
        'address',
        'education',
        'work_experience',
        'eligibility',
        'language_proficiency',
        'training_certificates',
    ];

    protected function casts(): array
    {
        return [
            'address' => 'array',
            'education' => 'array',
            'work_experience' => 'array',
            'eligibility' => 'array',
            'language_proficiency' => 'array',
            'training_certificates' => 'array',
        ];
    }

    public function jobseeker()
    {
        return $this->belongsTo(Jobseeker::class, 'jobseeker_id', 'jobseeker_id');
    }
}