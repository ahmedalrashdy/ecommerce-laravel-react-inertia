<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Observers\OrderObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(OrderObserver::class)]
class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_number',
        'parent_order_id',
        'type',
        'status',
        'payment_method',
        'payment_status',
        'idempotency_key',
        'shipping_address_snapshot',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'shipping_cost',
        'grand_total',
        'paid_at',
        'cancelled_at',
        'cancellation_reason',
        'tracking_number',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'type' => OrderType::class,
            'payment_method' => PaymentMethod::class,
            'payment_status' => PaymentStatus::class,
            'shipping_address_snapshot' => 'array',
            'subtotal' => 'string',
            'discount_amount' => 'string',
            'tax_amount' => 'string',
            'shipping_cost' => 'string',
            'grand_total' => 'string',
            'paid_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(OrderHistory::class);
    }

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public function stockMovements(): MorphMany
    {
        return $this->morphMany(StockMovement::class, 'sourceable');
    }
}
