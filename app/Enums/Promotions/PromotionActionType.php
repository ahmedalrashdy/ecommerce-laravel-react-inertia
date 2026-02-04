<?php

namespace App\Enums\Promotions;

use Filament\Support\Contracts\HasLabel;

enum PromotionActionType: int implements HasLabel
{
    case PERCENT_OFF = 1;
    case FIXED_AMOUNT_OFF = 2;
    case FREE_SHIPPING = 3;
    case BXGY_PERCENT_OFF = 4;
    case BXGY_FREE = 5;
    case BUNDLE_FIXED_PRICE = 6;
    case BUNDLE_PERCENT_OFF = 7;
    case TIERED_QTY_PERCENT = 8;
    case TIERED_QTY_AMOUNT = 9;
    case TIERED_SPEND_PERCENT = 10;
    case TIERED_SPEND_AMOUNT = 11;
    case FREE_GIFT = 12;

    public function getLabel(): string
    {
        return match ($this) {
            self::PERCENT_OFF => __('enums.promotion_action_type.percent_off'),
            self::FIXED_AMOUNT_OFF => __('enums.promotion_action_type.fixed_amount_off'),
            self::FREE_SHIPPING => __('enums.promotion_action_type.free_shipping'),
            self::BXGY_PERCENT_OFF => __('enums.promotion_action_type.bxgy_percent_off'),
            self::BXGY_FREE => __('enums.promotion_action_type.bxgy_free'),
            self::BUNDLE_FIXED_PRICE => __('enums.promotion_action_type.bundle_fixed_price'),
            self::BUNDLE_PERCENT_OFF => __('enums.promotion_action_type.bundle_percent_off'),
            self::TIERED_QTY_PERCENT => __('enums.promotion_action_type.tiered_qty_percent'),
            self::TIERED_QTY_AMOUNT => __('enums.promotion_action_type.tiered_qty_amount'),
            self::TIERED_SPEND_PERCENT => __('enums.promotion_action_type.tiered_spend_percent'),
            self::TIERED_SPEND_AMOUNT => __('enums.promotion_action_type.tiered_spend_amount'),
            self::FREE_GIFT => __('enums.promotion_action_type.free_gift'),
        };
    }
}
