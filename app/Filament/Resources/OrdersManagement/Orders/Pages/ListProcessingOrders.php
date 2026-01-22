<?php

namespace App\Filament\Resources\OrdersManagement\Orders\Pages;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;

class ListProcessingOrders extends OrderListPage
{
    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('filament.orders_management.orders.processing');
    }

    public function getTitle(): string
    {
        return __('filament.orders_management.orders.processing');
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('status', OrderStatus::PROCESSING);
    }
}
