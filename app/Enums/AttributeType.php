<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AttributeType: int implements HasLabel
{
    case Text = 1;
    case Color = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::Text => __('enums.attribute_type.text'),
            self::Color => __('enums.attribute_type.color'),
        };
    }
}
