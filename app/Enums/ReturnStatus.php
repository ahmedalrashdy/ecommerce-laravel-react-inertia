<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ReturnStatus: int implements HasLabel
{
    case REQUESTED = 1;
    case APPROVED = 2;
    case SHIPPED_BACK = 3;
    case RECEIVED = 4;
    case INSPECTED = 5;
    case COMPLETED = 6;
    case REJECTED = 7;

    public function getLabel(): string
    {
        return match ($this) {
            self::REQUESTED => __('enums.return_status.requested'),
            self::APPROVED => __('enums.return_status.approved'),
            self::SHIPPED_BACK => __('enums.return_status.shipped_back'),
            self::RECEIVED => __('enums.return_status.received'),
            self::INSPECTED => __('enums.return_status.inspected'),
            self::COMPLETED => __('enums.return_status.completed'),
            self::REJECTED => __('enums.return_status.rejected'),
        };
    }
}
