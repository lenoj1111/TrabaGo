<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobseekerSkill extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jobseeker_skills';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'skill_id';

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
        'skill_name',
        'skill_type',
    ];

    /**
     * Get the jobseeker that owns the skill.
     */
    public function jobseeker()
    {
        return $this->belongsTo(Jobseeker::class, 'jobseeker_id', 'jobseeker_id');
    }
}