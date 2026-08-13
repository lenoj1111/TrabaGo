<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployerAccreditation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employer_accreditation';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'accreditation_id';

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
        'documents',
        'ocr_raw_text',
        'ocr_classified_document_type',
        'ocr_extracted_fields',
        'ocr_confidence_score',
        'ocr_validation_status',
        'auto_approved_at',
        'jpo_reviewed',
        'jpo_reviewed_at',
        'jpo_remarks',
        'admin_approved',
        'admin_approved_at',
        'submitted_at',
        'approved_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'documents' => 'array',
            'ocr_extracted_fields' => 'array',
            'ocr_confidence_score' => 'float',
            'jpo_reviewed' => 'boolean',
            'admin_approved' => 'boolean',
            'auto_approved_at' => 'datetime',
            'jpo_reviewed_at' => 'datetime',
            'submitted_at' => 'date',
            'approved_at' => 'date',
        ];
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the employer for this accreditation.
     */
    public function employer()
    {
        return $this->belongsTo(Employer::class, 'employer_id', 'employer_id');
    }
}