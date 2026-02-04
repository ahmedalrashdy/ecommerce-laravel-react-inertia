<?php

namespace App\Enums\Promotions;

use Filament\Support\Contracts\HasLabel;

enum PromotionType: int implements HasLabel
{
    case AUTOMATIC = 1;
    case COUPON_REQUIRED = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::AUTOMATIC => __('enums.promotion_type.automatic'),
            self::COUPON_REQUIRED => __('enums.promotion_type.coupon_required'),
        };
    }
}
