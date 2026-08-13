<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostingRestriction extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posting_restrictions';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'restriction_id';

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
        'employer_id',
        'restriction_start_date',
        'restriction_end_date',
        'reason',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'restriction_start_date' => 'date',
            'restriction_end_date' => 'date',
        ];
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the employer for this restriction.
     */
    public function employer()
    {
        return $this->belongsTo(Employer::class, 'employer_id', 'employer_id');
    }
}