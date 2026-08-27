<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\User;
use App\Models\UserSubscription;

class SubscriptionService
{
    const FREE_LIMIT = 5;

    /** Activate subscription after successful payment */
    public function activate(Payment $payment): void
    {
        $now = now();
        UserSubscription::create([
            'user_id'    => $payment->user_id,
            'payment_id' => $payment->id,
            'starts_at'  => $now,
            'expires_at' => $now->copy()->addDays(30),
        ]);
    }

    /** Does user have an active paid subscription? */
    public function hasActive(User $user): bool
    {
        return UserSubscription::where('user_id', $user->id)
            ->where('expires_at', '>', now())
            ->exists();
    }

    /** How many ads user posted in current calendar month */
    public function monthlyCount(User $user): int
    {
        return $user->products()
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
    }

    /** Can user post a new ad? */
    public function canPost(User $user): bool
    {
        if ($this->hasActive($user)) {
            return true;
        }
        return $this->monthlyCount($user) < self::FREE_LIMIT;
    }

    /** How many free ads remain this month (null if subscribed) */
    public function freeRemaining(User $user): ?int
    {
        if ($this->hasActive($user)) {
            return null; // unlimited
        }
        return max(0, self::FREE_LIMIT - $this->monthlyCount($user));
    }
}
