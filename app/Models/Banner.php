<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    const STATUS_PENDING  = 'pending_payment';
    const STATUS_ACTIVE   = 'active';
    const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'user_id', 'payment_id', 'title', 'contact',
        'image_path', 'url', 'status', 'starts_at', 'expires_at',
    ];

    protected $casts = [
        'starts_at'  => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
    public function payment(): BelongsTo { return $this->belongsTo(Payment::class); }

    public function getImageUrlAttribute(): string
    {
        return Storage::url($this->image_path);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && now()->between($this->starts_at, $this->expires_at);
    }

    public function scopeActive($q)
    {
        return $q->where('status', self::STATUS_ACTIVE)
                 ->where('starts_at', '<=', now())
                 ->where('expires_at', '>=', now());
    }
}
