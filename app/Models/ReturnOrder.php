<?php

namespace App\Models;

use App\Enums\RefundMethod;
use App\Enums\ReturnStatus;
use App\Observers\ReturnOrderObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[ObservedBy(ReturnOrderObserver::class)]
class ReturnOrder extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $guarded = [];

    protected $casts = [
        'status' => ReturnStatus::class,
        'refund_method' => RefundMethod::class,
        'inspected_at' => 'datetime',
        'refund_amount' => 'string',
    ];

    public function getRouteKeyName(): string
    {
        return 'return_number';
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(ReturnHistory::class, 'return_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'order_id', 'order_id');
    }

    public function stockMovements(): MorphMany
    {
        return $this->morphMany(StockMovement::class, 'sourceable');
    }

    public function inspectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }
}
