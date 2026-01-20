<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

// حالة الدفع  تمثل الحالة  الحالية لطلب حالياً
enum PaymentStatus: int implements HasLabel
{
    case PENDING = 0;
    case PAID = 1;
    case FAILED = 2;
    case REFUNDED = 3;
    case REFUND_PENDING = 4;
    case PARTIALLY_REFUNDED = 5;

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => __('enums.payment_status.pending'),
            self::PAID => __('enums.payment_status.paid'),
            self::FAILED => __('enums.payment_status.failed'),
            self::REFUNDED => __('enums.payment_status.refunded'),
            self::REFUND_PENDING => __('enums.payment_status.refund_pending'),
            self::PARTIALLY_REFUNDED => __('enums.payment_status.partially_refunded'),
        };
    }

    public static function options(): array
    {
        return [
            self::PENDING->value => self::PENDING->getLabel(),
            self::PAID->value => self::PAID->getLabel(),
            self::FAILED->value => self::FAILED->getLabel(),
            self::REFUNDED->value => self::REFUNDED->getLabel(),
            self::REFUND_PENDING->value => self::REFUND_PENDING->getLabel(),
            self::PARTIALLY_REFUNDED->value => self::PARTIALLY_REFUNDED->getLabel(),
        ];
    }
}
