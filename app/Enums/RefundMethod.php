<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum RefundMethod: int implements HasLabel
{
    case ORIGINAL = 1;
    case WALLET = 2;
    case BANK_TRANSFER = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::ORIGINAL => __('enums.refund_method.original'),
            self::WALLET => __('enums.refund_method.wallet'),
            self::BANK_TRANSFER => __('enums.refund_method.bank_transfer'),
        };
    }
}
