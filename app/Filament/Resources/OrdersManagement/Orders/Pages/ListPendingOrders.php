<?php

namespace App\Filament\Resources\OrdersManagement\Orders\Pages;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;

class ListPendingOrders extends OrderListPage
{
    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('filament.orders_management.orders.pending');
    }

    public function getTitle(): string
    {
        return __('filament.orders_management.orders.pending');
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('status', OrderStatus::PENDING);
    }
}
