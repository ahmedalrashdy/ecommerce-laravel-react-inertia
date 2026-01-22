<?php

namespace App\Filament\Resources\OrdersManagement\Orders\Pages;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;

class ListCancelledOrders extends OrderListPage
{
    protected static ?int $navigationSort = 7;

    public static function getNavigationLabel(): string
    {
        return __('filament.orders_management.orders.cancelled');
    }

    public function getTitle(): string
    {
        return __('filament.orders_management.orders.cancelled');
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('status', OrderStatus::CANCELLED);
    }
}
