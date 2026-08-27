<?php

namespace App\Services;

use App\Models\Banner;
use App\Models\Payment;
use Illuminate\Support\Facades\Storage;

class BannerService
{
    /** Called by PaymentService after payment confirmed */
    public function activate(Payment $payment): void
    {
        $bannerId = $payment->meta['banner_id'] ?? null;
        if (!$bannerId) {
            return;
        }

        $banner = Banner::find($bannerId);
        if (!$banner || $banner->status !== Banner::STATUS_PENDING) {
            return;
        }

        $now = now();
        $banner->update([
            'payment_id' => $payment->id,
            'status'     => Banner::STATUS_ACTIVE,
            'starts_at'  => $now,
            'expires_at' => $now->copy()->addDays(30),
        ]);
    }

    /** Archive expired banners — called by scheduled command */
    public function archiveExpired(): int
    {
        return Banner::where('status', Banner::STATUS_ACTIVE)
            ->where('expires_at', '<', now())
            ->update(['status' => Banner::STATUS_ARCHIVED]);
    }

    /** Reactivate an archived banner (new payment made) */
    public function reactivate(Banner $banner, Payment $payment): void
    {
        $now = now();
        $banner->update([
            'payment_id' => $payment->id,
            'status'     => Banner::STATUS_ACTIVE,
            'starts_at'  => $now,
            'expires_at' => $now->copy()->addDays(30),
        ]);
    }

    public function store(array $data, $imageFile, int $userId): Banner
    {
        $path = $imageFile->store('banners', 'public');

        return Banner::create([
            'user_id'    => $userId,
            'title'      => $data['title'],
            'contact'    => $data['contact'],
            'url'        => $data['url'] ?? null,
            'image_path' => $path,
            'status'     => Banner::STATUS_PENDING,
        ]);
    }

    public function delete(Banner $banner): void
    {
        Storage::disk('public')->delete($banner->image_path);
        $banner->delete();
    }
}
