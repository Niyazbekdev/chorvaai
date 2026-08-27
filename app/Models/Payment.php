<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    const TYPE_SUBSCRIPTION = 'subscription';
    const TYPE_BANNER       = 'banner';

    const PROVIDER_PAYME = 'payme';
    const PROVIDER_CLICK = 'click';

    const STATUS_PENDING   = 'pending';
    const STATUS_PAID      = 'paid';
    const STATUS_FAILED    = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id', 'type', 'amount', 'provider',
        'status', 'provider_transaction_id', 'meta', 'paid_at',
    ];

    protected $casts = [
        'meta'    => 'array',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool   { return $this->status === self::STATUS_PENDING; }
    public function isPaid(): bool      { return $this->status === self::STATUS_PAID; }
    public function isCancelled(): bool { return $this->status === self::STATUS_CANCELLED; }

    /** Amount in tiyins for Payme (1 UZS = 100 tiyins) */
    public function amountInTiyins(): int
    {
        return $this->amount * 100;
    }

    public function scopePending($q)    { return $q->where('status', self::STATUS_PENDING); }
    public function scopePaid($q)       { return $q->where('status', self::STATUS_PAID); }
}
