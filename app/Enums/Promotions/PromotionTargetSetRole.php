<?php

namespace App\Enums\Promotions;

use Filament\Support\Contracts\HasLabel;

enum PromotionTargetSetRole: int implements HasLabel
{
    case ELIGIBLE = 1;
    case BUY = 2;
    case GET = 3;
    case BUNDLE_REQUIRED = 4;
    case BUNDLE_OPTIONAL = 5;

    public function getLabel(): string
    {
        return match ($this) {
            self::ELIGIBLE => __('enums.promotion_target_set_role.eligible'),
            self::BUY => __('enums.promotion_target_set_role.buy'),
            self::GET => __('enums.promotion_target_set_role.get'),
            self::BUNDLE_REQUIRED => __('enums.promotion_target_set_role.bundle_required'),
            self::BUNDLE_OPTIONAL => __('enums.promotion_target_set_role.bundle_optional'),
        };
    }
}
