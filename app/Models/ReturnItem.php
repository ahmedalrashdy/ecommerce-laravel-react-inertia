<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function returnOrder(): BelongsTo
    {
        return $this->belongsTo(ReturnOrder::class, 'return_id');
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(ReturnItemInspection::class);
    }
}
