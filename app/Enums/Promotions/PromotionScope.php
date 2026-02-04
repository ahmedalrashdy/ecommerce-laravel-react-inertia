<?php

namespace App\Enums\Promotions;

use Filament\Support\Contracts\HasLabel;

enum PromotionScope: int implements HasLabel
{
    case ORDER = 1;
    case ITEM = 2;
    case SHIPPING = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::ORDER => __('enums.promotion_scope.order'),
            self::ITEM => __('enums.promotion_scope.item'),
            self::SHIPPING => __('enums.promotion_scope.shipping'),
        };
    }
}
