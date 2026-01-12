<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CategoryStatus: int implements HasLabel
{
    case Draft = 0;
    case Published = 1;
    case Archived = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => __('enums.category_status.draft'),
            self::Published => __('enums.category_status.published'),
            self::Archived => __('enums.category_status.archived'),
        };
    }

    public static function options(): array
    {
        return [
            self::Draft->value => self::Draft->getLabel(),
            self::Published->value => self::Published->getLabel(),
            self::Archived->value => self::Archived->getLabel(),
        ];
    }
}
