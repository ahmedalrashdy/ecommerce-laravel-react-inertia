<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ItemCondition: int implements HasLabel
{
    case SEALED = 1;      // جديد
    case OPEN_BOX = 2;    // مفتوح وسليم
    case DAMAGED = 3;     // تالف
    case WRONG_ITEM = 4;  // منتج خطأ

    public function getLabel(): string
    {
        return match ($this) {
            self::SEALED => __('enums.item_condition.sealed'),
            self::OPEN_BOX => __('enums.item_condition.open_box'),
            self::DAMAGED => __('enums.item_condition.damaged'),
            self::WRONG_ITEM => __('enums.item_condition.wrong_item'),
        };
    }
}
