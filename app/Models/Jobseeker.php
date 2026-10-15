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
        'hired_company',
    ];

    /**
     * Check if the jobseeker is currently employed.
     */
    public function isEmployed(): bool
    {
        // Check for active hired application without approved resignation
        $hasActiveHired = $this->applications()
            ->where('status', 'hired')
            ->where(function ($q) {
                $q->whereNull('resignation_status')
                  ->orWhere('resignation_status', '!=', 'approved');
            })
            ->exists();

        if ($hasActiveHired) {
            return true;
        }

        if (strtolower($this->employment_status ?? '') === 'unemployed') {
            return false;
        }

        if (strtolower($this->employment_status ?? '') === 'employed') {
            // If flagged as employed but all hired applications have approved resignations, treat as not employed
            if ($this->applications()->where('status', 'hired')->exists()) {
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Get the active hired application.
     */
    public function activeHiredApplication()
    {
        return $this->hasOne(JobApplication::class, 'jobseeker_id', 'jobseeker_id')
            ->where('status', 'hired')
            ->where(function ($q) {
                $q->whereNull('resignation_status')
                  ->orWhere('resignation_status', '!=', 'approved');
            })
            ->latestOfMany('application_id');
    }

    /**
     * Get the company that currently employs the jobseeker.
     */
    public function getHiredCompanyAttribute(): ?string
    {
        if (!$this->isEmployed()) {
            return null;
        }

        if (!empty($this->attributes['hired_company'])) {
            return $this->attributes['hired_company'];
        }

        $hiredApp = $this->activeHiredApplication;
        if ($hiredApp && $hiredApp->jobPosting && $hiredApp->jobPosting->employer) {
            return $hiredApp->jobPosting->employer->company_name;
        }

        return null;
    }

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

    // =============================================
    // PROFILE & DOCUMENT COMPLETION / VERIFICATION
    // =============================================

    /**
     * Get all uploaded documents in the jobseeker's Document Hub.
     */
    public function getDocumentsList(): array
    {
        $details = $this->details;
        if (!$details || empty($details->training_certificates)) {
            return [];
        }
        return is_array($details->training_certificates) 
            ? $details->training_certificates 
            : (json_decode($details->training_certificates, true) ?: []);
    }

    /**
     * Check if a specific document category exists in the Document Hub.
     */
    public function hasDocument(string $category): bool
    {
        $canonical = ($category === 'certs') ? 'certificate' : $category;
        foreach ($this->getDocumentsList() as $doc) {
            $cat = ($doc['category'] ?? '') === 'certs' ? 'certificate' : ($doc['category'] ?? '');
            if ($cat === $canonical) {
                // A rejected document must be re-uploaded and cannot fulfill requirements
                if (($doc['status'] ?? 'under_review') === 'rejected') {
                    continue;
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Get list of missing profile details and required documents.
     */
    public function getMissingRequirements(): array
    {
        $missing = [];

        // 1. Core Profile Details
        if (empty(trim($this->first_name ?? '')) || empty(trim($this->last_name ?? ''))) {
            $missing['name'] = 'Full Name';
        }
        if (empty($this->birth_date)) {
            $missing['birth_date'] = 'Birth Date';
        }
        if (empty(trim($this->sex ?? ''))) {
            $missing['sex'] = 'Sex / Gender';
        }
        if (empty(trim($this->civil_status ?? ''))) {
            $missing['civil_status'] = 'Civil Status';
        }
        if (empty(trim($this->mobile_number ?? ''))) {
            $missing['mobile_number'] = 'Mobile Number';
        }

        // Residential Address
        $details = $this->details;
        $address = [];
        if ($details && !empty($details->address)) {
            $address = is_array($details->address) ? $details->address : (json_decode($details->address, true) ?: []);
        }
        $brgy = trim($address['barangay'] ?? ($address['present_barangay'] ?? ($this->current_barangay ?? '')));
        if (empty($brgy)) {
            $missing['barangay'] = 'Barangay Address';
        }

        // Educational Background
        $education = $details ? $details->education : null;
        $hasEdu = false;
        if (!empty($education)) {
            $eduArr = is_array($education) ? $education : (json_decode($education, true) ?: []);
            if (is_array($eduArr)) {
                $hasEdu = !empty($eduArr['school']) || !empty($eduArr['course']) || !empty($eduArr['level']) || count($eduArr) > 0;
            } else {
                $hasEdu = !empty(trim((string)$education));
            }
        }
        if (!$hasEdu) {
            $missing['education'] = 'Educational Background';
        }

        // Skills Check
        if ($this->skills()->count() === 0) {
            $missing['skills'] = 'At least 1 Skill listed';
        }

        // 2. Required Documents (Document Hub)
        // All documents are optional except for Government-Issued Valid ID
        if (!$this->hasDocument('valid_id')) {
            $missing['valid_id'] = 'Government-Issued Valid ID (Upload in Document Hub)';
        }

        return $missing;
    }

    /**
     * Check if profile and documents are 100% complete.
     */
    public function isProfileAndDocumentsComplete(): bool
    {
        return empty($this->getMissingRequirements());
    }

    /**
     * Check if jobseeker is officially tagged as Verified.
     * Rule: Can ONLY be tagged as verified if profile/document is completed AND approved!
     */
    public function isVerified(): bool
    {
        return (bool) ($this->user && $this->user->is_approved && $this->isProfileAndDocumentsComplete());
    }
}