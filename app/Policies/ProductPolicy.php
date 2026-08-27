<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use App\Services\SubscriptionService;

class ProductPolicy
{
    public function create(User $user): bool
    {
        return app(SubscriptionService::class)->canPost($user);
    }

    public function update(User $user, Product $product): bool
    {
        return $user->id === $product->user_id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->id === $product->user_id;
    }
}
