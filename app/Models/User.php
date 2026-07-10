<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Database\Factories\UserFactory;
use App\Models\Conversation;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'password',
        'phone_verified_at',
        'avatar',
        'role_id',
    ];

    public function isAdmin(): bool
    {
        return $this->role?->slug === 'admin';
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar ? Storage::url($this->avatar) : '';
    }

    protected function casts(): array
    {
        return [
            'phone_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function hasVerifiedPhone(): bool
    {
        return !is_null($this->phone_verified_at);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'favorites')->withPivot('created_at');
    }

    public function buyerConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'buyer_id');
    }

    public function sellerConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'seller_id');
    }
}
