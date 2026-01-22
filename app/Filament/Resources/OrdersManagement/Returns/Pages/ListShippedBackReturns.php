<?php

namespace App\Filament\Resources\OrdersManagement\Returns\Pages;

use App\Enums\ReturnStatus;
use Illuminate\Database\Eloquent\Builder;

class ListShippedBackReturns extends ReturnListPage
{
    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('filament.orders_management.returns.shipped_back');
    }

    public function getTitle(): string
    {
        return __('filament.orders_management.returns.shipped_back');
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('status', ReturnStatus::SHIPPED_BACK);
    }
}
