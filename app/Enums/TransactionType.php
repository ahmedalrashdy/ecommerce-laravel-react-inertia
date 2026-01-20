<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TransactionType: int implements HasLabel
{
    case Payment = 0;
    case Refund = 1;

    public function getLabel(): string
    {
        return match ($this) {
            self::Payment => __('enums.transaction_type.payment'),
            self::Refund => __('enums.transaction_type.refund'),
        };
    }

    public static function options(): array
    {
        return [
            self::Payment->value => self::Payment->getLabel(),
            self::Refund->value => self::Refund->getLabel(),
        ];
    }
}
