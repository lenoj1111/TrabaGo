<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jobseeker extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jobseekers';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'jobseeker_id';

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
        'first_name',
        'last_name',
        'middle_name',
        'birth_date',
        'sex',
        'civil_status',
        'citizenship',
        'mobile_number',
        'email',
        'employment_status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the user associated with the jobseeker.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the jobseeker details.
     */
    public function details()
    {
        return $this->hasOne(JobseekerDetail::class, 'jobseeker_id', 'jobseeker_id');
    }

    /**
     * Get the jobseeker skills.
     */
    public function skills()
    {
        return $this->hasMany(JobseekerSkill::class, 'jobseeker_id', 'jobseeker_id');
    }

    /**
     * Get the job preferences.
     */
    public function preferences()
    {
        return $this->hasOne(JobPreference::class, 'jobseeker_id', 'jobseeker_id');
    }

    /**
     * Get the social status.
     */
    public function socialStatus()
    {
        return $this->hasOne(SocialStatus::class, 'jobseeker_id', 'jobseeker_id');
    }

    /**
     * Get the job applications.
     */
    public function applications()
    {
        return $this->hasMany(JobApplication::class, 'jobseeker_id', 'jobseeker_id');
    }

    /**
     * Get the training enrollments.
     */
    public function enrollments()
    {
        return $this->hasMany(TrainingEnrollment::class, 'jobseeker_id', 'jobseeker_id');
    }
}