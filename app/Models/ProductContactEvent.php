<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductContactEvent extends Model
{
    public $timestamps = false;

    protected $fillable = ['product_id', 'viewer_id', 'seller_id', 'type', 'ip_address'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function viewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'viewer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
