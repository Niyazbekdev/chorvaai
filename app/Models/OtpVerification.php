<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    protected $fillable = ['phone', 'code', 'expires_at', 'attempts', 'resend_at'];

    protected $casts = [
        'expires_at' => 'datetime',
        'resend_at'  => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function canResend(): bool
    {
        return is_null($this->resend_at) || $this->resend_at->isPast();
    }

    public function resendSecondsLeft(): int
    {
        if ($this->canResend()) return 0;
        return (int) now()->diffInSeconds($this->resend_at);
    }

    public function tooManyAttempts(): bool
    {
        return $this->attempts >= 3;
    }
}
