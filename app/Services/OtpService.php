<?php

namespace App\Services;

use App\Models\OtpVerification;

class OtpService
{
    private const OTP_TTL_MINUTES  = 5;
    private const RESEND_COOLDOWN  = 60;  // seconds
    private const MAX_ATTEMPTS     = 3;

    public function generate(string $phone): string
    {
        return $this->generateFor('phone', $phone);
    }

    public function generateForEmail(string $email): string
    {
        return $this->generateFor('email', $email);
    }

    public function verify(string $phone, string $code): array
    {
        return $this->verifyFor('phone', $phone, $code);
    }

    public function verifyEmail(string $email, string $code): array
    {
        return $this->verifyFor('email', $email, $code);
    }

    public function canResend(string $phone): array
    {
        return $this->canResendFor('phone', $phone);
    }

    public function canResendEmail(string $email): array
    {
        return $this->canResendFor('email', $email);
    }

    private function generateFor(string $column, string $value): string
    {
        OtpVerification::where($column, $value)->delete();

        $code = (string) random_int(100000, 999999);

        OtpVerification::create([
            $column      => $value,
            'code'       => hash('sha256', $code),
            'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
            'attempts'   => 0,
            'resend_at'  => now()->addSeconds(self::RESEND_COOLDOWN),
        ]);

        return $code;
    }

    private function verifyFor(string $column, string $value, string $code): array
    {
        $otp = OtpVerification::where($column, $value)->latest()->first();

        if (!$otp) {
            return ['ok' => false, 'error' => 'Kod topilmadi. Qaytadan yuborish tugmasini bosing.'];
        }

        if ($otp->isExpired()) {
            $otp->delete();
            return ['ok' => false, 'error' => 'Kod muddati tugagan. Qaytadan yuboring.'];
        }

        if ($otp->tooManyAttempts()) {
            $otp->delete();
            return ['ok' => false, 'error' => 'Urinishlar soni oshib ketdi. Qaytadan yuboring.'];
        }

        if (!hash_equals($otp->code, hash('sha256', $code))) {
            $otp->increment('attempts');
            $left = self::MAX_ATTEMPTS - $otp->attempts;
            return [
                'ok'    => false,
                'error' => "Noto'g'ri kod. " . ($left > 0 ? "$left ta urinish qoldi." : "Qaytadan yuboring."),
            ];
        }

        $otp->delete();
        return ['ok' => true];
    }

    private function canResendFor(string $column, string $value): array
    {
        $otp = OtpVerification::where($column, $value)->latest()->first();

        if (!$otp || $otp->canResend()) {
            return ['can' => true, 'seconds' => 0];
        }

        return ['can' => false, 'seconds' => $otp->resendSecondsLeft()];
    }
}
