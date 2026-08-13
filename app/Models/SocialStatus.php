<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialStatus extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'social_status';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'status_id';

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
        'is_4ps',
        'household_id',
        'is_ofw',
        'is_pwd',
        'pwd_type',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_4ps' => 'boolean',
            'is_ofw' => 'boolean',
            'is_pwd' => 'boolean',
        ];
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the jobseeker associated with the social status.
     */
    public function jobseeker()
    {
        return $this->belongsTo(Jobseeker::class, 'jobseeker_id', 'jobseeker_id');
    }
}