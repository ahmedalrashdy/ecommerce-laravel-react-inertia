<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CancelRefundOption: string implements HasLabel
{
    case AUTO = 'auto';
    case MANUAL = 'manual';
    case LATER = 'later';

    public function getLabel(): string
    {
        return match ($this) {
            self::AUTO => __('filament.orders.refund_options.auto'),
            self::MANUAL => __('filament.orders.refund_options.manual'),
            self::LATER => __('filament.orders.refund_options.later'),
        };
    }

    public static function options(): array
    {
        return [
            self::AUTO->value => self::AUTO->getLabel(),
            self::MANUAL->value => self::MANUAL->getLabel(),
            self::LATER->value => self::LATER->getLabel(),
        ];
    }
}
