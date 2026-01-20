<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TransactionStatus: int implements HasLabel
{
    case Pending = 0;
    case Success = 1;
    case Failed = 2;
    case Cancelled = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => __('enums.transaction_status.pending'),
            self::Success => __('enums.transaction_status.success'),
            self::Failed => __('enums.transaction_status.failed'),
            self::Cancelled => __('enums.transaction_status.cancelled'),
        };
    }

    public static function options(): array
    {
        return [
            self::Pending->value => self::Pending->getLabel(),
            self::Success->value => self::Success->getLabel(),
            self::Failed->value => self::Failed->getLabel(),
            self::Cancelled->value => self::Cancelled->getLabel(),
        ];
    }
}
