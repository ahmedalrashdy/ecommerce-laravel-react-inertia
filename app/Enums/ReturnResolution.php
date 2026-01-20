<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ReturnResolution: int implements HasLabel
{
    case REFUND = 1;
    case REPLACEMENT = 2;
    case REJECT = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::REFUND => __('enums.return_resolution.refund'),
            self::REPLACEMENT => __('enums.return_resolution.replacement'),
            self::REJECT => __('enums.return_resolution.reject'),
        };
    }
}
