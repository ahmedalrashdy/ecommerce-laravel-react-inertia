<?php

namespace App\Enums\Promotions;

use Filament\Support\Contracts\HasLabel;

enum PromotionTargetType: int implements HasLabel
{
    case PRODUCT = 1;
    case CATEGORY = 2;
    case BRAND = 3;
    case VARIANT = 4;

    public function getLabel(): string
    {
        return match ($this) {
            self::PRODUCT => __('enums.promotion_target_type.product'),
            self::CATEGORY => __('enums.promotion_target_type.category'),
            self::BRAND => __('enums.promotion_target_type.brand'),
            self::VARIANT => __('enums.promotion_target_type.variant'),
        };
    }
}
