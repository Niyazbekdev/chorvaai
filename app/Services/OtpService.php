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
        OtpVerification::where('phone', $phone)->delete();

        $code = (string) random_int(100000, 999999);

        OtpVerification::create([
            'phone'      => $phone,
            'code'       => hash('sha256', $code),
            'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
            'attempts'   => 0,
            'resend_at'  => now()->addSeconds(self::RESEND_COOLDOWN),
        ]);

        return $code;
    }

    public function verify(string $phone, string $code): array
    {
        $otp = OtpVerification::where('phone', $phone)->latest()->first();

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

    public function canResend(string $phone): array
    {
        $otp = OtpVerification::where('phone', $phone)->latest()->first();

        if (!$otp || $otp->canResend()) {
            return ['can' => true, 'seconds' => 0];
        }

        return ['can' => false, 'seconds' => $otp->resendSecondsLeft()];
    }
}
