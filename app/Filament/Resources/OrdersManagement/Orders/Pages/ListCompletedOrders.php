<?php

namespace App\Filament\Resources\OrdersManagement\Orders\Pages;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;

class ListCompletedOrders extends OrderListPage
{
    protected static ?int $navigationSort = 6;

    public static function getNavigationLabel(): string
    {
        return __('filament.orders_management.orders.completed');
    }

    public function getTitle(): string
    {
        return __('filament.orders_management.orders.completed');
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('status', OrderStatus::DELIVERED);
    }
}
