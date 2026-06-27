<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
    ];

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

    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'favorites')->withPivot('created_at');
    }
}
