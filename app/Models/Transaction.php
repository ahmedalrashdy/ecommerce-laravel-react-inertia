<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class Transaction extends Model
{
    protected $guarded = [];

    protected $casts = [
        'gateway_response' => 'array',
        'type' => TransactionType::class,
        'payment_method' => PaymentMethod::class,
        'status' => TransactionStatus::class,
        'amount' => 'string',
    ];

    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new RuntimeException('لا يمكن تعديل سجل معاملة بعد إنشائه.');
        });

        static::deleting(function (): void {
            throw new RuntimeException('لا يمكن حذف سجل معاملة بعد إنشائه.');
        });
    }
}
