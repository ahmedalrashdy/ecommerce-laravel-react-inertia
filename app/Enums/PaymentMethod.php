<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: int implements HasLabel
{
    case PENDING = 0;
    case CREDIT_CARD = 1;
    case MADA = 2;
    case APPLE_PAY = 3;
    case BANK_TRANSFER = 4;

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => __('enums.payment_method.pending'),
            self::CREDIT_CARD => __('enums.payment_method.credit_card'),
            self::MADA => __('enums.payment_method.mada'),
            self::APPLE_PAY => __('enums.payment_method.apple_pay'),
            self::BANK_TRANSFER => __('enums.payment_method.bank_transfer'),
        };
    }
}
