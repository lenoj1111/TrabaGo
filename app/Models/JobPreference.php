<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPreference extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'job_preferences';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'preference_id';

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
        'occupation1',
        'occupation2',
        'occupation3',
        'industry1',
        'industry2',
        'industry3',
        'preferred_location',
        'salary_expectation',
    ];

    /**
     * Get the jobseeker that owns the preference.
     */
    public function jobseeker()
    {
        return $this->belongsTo(Jobseeker::class, 'jobseeker_id', 'jobseeker_id');
    }
}