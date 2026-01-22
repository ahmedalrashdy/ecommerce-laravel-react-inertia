<?php

namespace App\Filament\Resources\OrdersManagement\Orders\Pages;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;

class ListShippedOrders extends OrderListPage
{
    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('filament.orders_management.orders.shipped');
    }

    public function getTitle(): string
    {
        return __('filament.orders_management.orders.shipped');
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('status', OrderStatus::SHIPPED);
    }
}
