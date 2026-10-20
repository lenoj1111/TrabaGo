<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

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
        'email',
        'phone_number',
        'password',
        'role',
        'status',
        'is_approved',
        'is_contact_verified',
        'account_status',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'is_approved' => 'boolean',
            'is_contact_verified' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Alias attribute id to user_id for Laravel standard auth interfaces.
     */
    public function getIdAttribute(): ?int
    {
        return $this->attributes['user_id'] ?? null;
    }

    /**
     * Get user display name from UserProfile or Jobseeker.
     */
    public function getNameAttribute(): ?string
    {
        return $this->profile?->full_name 
            ?? ($this->jobseeker ? trim($this->jobseeker->first_name . ' ' . $this->jobseeker->last_name) : null);
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the jobseeker associated with the user.
     */
    public function jobseeker()
    {
        return $this->hasOne(Jobseeker::class, 'user_id', 'user_id');
    }

    /**
     * Get the employer associated with the user.
     */
    public function employer()
    {
        return $this->hasOne(Employer::class, 'user_id', 'user_id');
    }

    /**
     * Get the user profile associated with the user.
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'user_id');
    }

    /**
     * Get the notifications for the user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id', 'user_id');
    }

    /**
     * Get the audit logs for the user.
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id', 'user_id');
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Get user full name from relations or fallback to email.
     */
    public function getFullNameAttribute(): string
    {
        if ($this->jobseeker) {
            $name = trim("{$this->jobseeker->first_name} {$this->jobseeker->last_name}");
            if (!empty($name)) {
                return $name;
            }
        }
        if ($this->profile && !empty($this->profile->full_name)) {
            return $this->profile->full_name;
        }
        if ($this->employer && !empty($this->employer->company_name)) {
            return $this->employer->company_name;
        }
        return $this->email;
    }

    /**
     * Check if the user is a jobseeker.
     */
    public function isJobseeker(): bool
    {
        return $this->role === 'jobseeker';
    }

    /**
     * Check if the user is an employer.
     */
    public function isEmployer(): bool
    {
        return $this->role === 'employer';
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a supervisor (Role removed).
     */
    public function isSupervisor(): bool
    {
        return false;
    }

    /**
     * Check if the user is a JPO.
     */
    public function isJpo(): bool
    {
        return $this->role === 'jpo';
    }

    /**
     * Check if the user is a trainer.
     */
    public function isTrainer(): bool
    {
        return $this->role === 'trainer';
    }

    /**
     * Check if the user is a LMO (Role removed, responsibilities handled by Admin).
     */
    public function isLmo(): bool
    {
        return false;
    }

    /**
     * Check if the user is approved.
     */
    public function isApproved(): bool
    {
        return (bool) $this->is_approved;
    }

    /**
     * Check if the user is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the user's contact information (email or phone) is verified via OTP.
     */
    public function isContactVerified(): bool
    {
        return (bool) $this->is_contact_verified;
    }

    /**
     * Check if the user's account is fully verified.
     */
    public function isAccountVerified(): bool
    {
        return ($this->account_status === 'verified') || ($this->is_approved && $this->isActive());
    }

    /**
     * Check if the user's account is pending review.
     */
    public function isPendingReview(): bool
    {
        return $this->account_status === 'pending_review' || ($this->status === 'pending' && !$this->is_approved);
    }

    /**
     * Check if the user's account is unverified.
     */
    public function isUnverified(): bool
    {
        return $this->account_status === 'unverified' && !$this->isAccountVerified();
    }
}