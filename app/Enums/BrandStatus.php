<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BrandStatus: int implements HasLabel
{
    case Draft = 0;
    case Published = 1;
    case Archived = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => __('enums.brand_status.draft'),
            self::Published => __('enums.brand_status.published'),
            self::Archived => __('enums.brand_status.archived'),
        };
    }
}
