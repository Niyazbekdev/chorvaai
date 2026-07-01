<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'price', 'image', 'images',
        'category_id', 'user_id', 'color_id',
        'gender', 'contact_phone',
        'age', 'weight', 'region_id', 'city_id', 'status_id',
        'latitude', 'longitude', 'views_count',
    ];

    protected $casts = [
        'price'      => 'integer',
        'age'        => 'integer',
        'weight'     => 'integer',
        'latitude'   => 'float',
        'longitude'  => 'float',
        'views_count'=> 'integer',
        'images'     => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function contactEvents(): HasMany
    {
        return $this->hasMany(ProductContactEvent::class);
    }

    public function sale(): HasOne
    {
        return $this->hasOne(Sale::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0, '.', ' ') . " so'm";
    }

    public function getGalleryAttribute(): array
    {
        $images = $this->images ?? [];
        if (empty($images) && $this->image) {
            return [$this->image];
        }
        return array_values(array_filter($images));
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        $gallery = $this->gallery;
        return $gallery ? Storage::url($gallery[0]) : null;
    }

    public function isFavoritedBy(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }
        return $this->favorites()->where('user_id', $userId)->exists();
    }

    // Checks attributes first so withCount('favorites') result is not overridden by a query.
    public function getFavoritesCountAttribute(): int
    {
        return (int) ($this->attributes['favorites_count'] ?? $this->favorites()->count());
    }

    public function getPhoneViewsCountAttribute(): int
    {
        return (int) ($this->attributes['phone_views_count'] ?? $this->contactEvents()->where('type', 'phone_view')->count());
    }

    public function getConversationsCountAttribute(): int
    {
        return (int) ($this->attributes['conversations_count'] ?? $this->conversations()->count());
    }

    public function isSold(): bool
    {
        return $this->status?->name === 'Sotildi';
    }
}
