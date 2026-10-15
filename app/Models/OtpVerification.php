<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    protected $table = 'otp_verifications';

    protected $fillable = [
        'identifier',
        'otp_code',
        'type',
        'expires_at',
        'verified_at',
        'attempts',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'attempts' => 'integer',
    ];

    /**
     * Check if the OTP is currently valid.
     */
    public function isValid(): bool
    {
        return is_null($this->verified_at) && $this->expires_at->isFuture() && $this->attempts < 5;
    }
}
