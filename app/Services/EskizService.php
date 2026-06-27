<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EskizService
{
    private const BASE_URL = 'https://notify.eskiz.uz/api';
    private const FROM     = '4546';

    private function token(): ?string
    {
        return Cache::remember('eskiz_token', now()->addDays(29), function () {
            $response = Http::asForm()->post(self::BASE_URL . '/auth/login', [
                'email'    => config('services.eskiz.email'),
                'password' => config('services.eskiz.password'),
            ]);

            if ($response->successful()) {
                return $response->json('data.token');
            }

            Log::error('Eskiz login failed', ['body' => $response->body()]);
            return null;
        });
    }

    public function send(string $phone, string $message): bool
    {
        $token = $this->token();

        if (!$token) return false;

        $phone = preg_replace('/\D/', '', $phone);

        $response = Http::withToken($token)
            ->asForm()
            ->post(self::BASE_URL . '/message/sms/send', [
                'mobile_phone' => $phone,
                'message'      => $message,
                'from'         => self::FROM,
                'callback_url' => '',
            ]);

        if ($response->status() === 401) {
            Cache::forget('eskiz_token');
            return $this->send($phone, $message);
        }

        if (!$response->successful()) {
            Log::error('Eskiz SMS failed', ['phone' => $phone, 'body' => $response->body()]);
            return false;
        }

        return true;
    }
}
