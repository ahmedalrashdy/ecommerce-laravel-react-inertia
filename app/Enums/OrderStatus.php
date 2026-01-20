<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum OrderStatus: int implements HasLabel
{
    case PENDING = 0;
    case PROCESSING = 2;
    case SHIPPED = 3;
    case DELIVERED = 4;
    case CANCELLED = 5;
    case RETURNED = 6;

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => __('enums.order_status.pending'),
            self::PROCESSING => __('enums.order_status.processing'),
            self::SHIPPED => __('enums.order_status.shipped'),
            self::DELIVERED => __('enums.order_status.delivered'),
            self::CANCELLED => __('enums.order_status.cancelled'),
            self::RETURNED => __('enums.order_status.returned'),
        };
    }
}
