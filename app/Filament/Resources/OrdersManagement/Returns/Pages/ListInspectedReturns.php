<?php

namespace App\Filament\Resources\OrdersManagement\Returns\Pages;

use App\Enums\ReturnStatus;
use Illuminate\Database\Eloquent\Builder;

class ListInspectedReturns extends ReturnListPage
{
    protected static ?int $navigationSort = 6;

    public static function getNavigationLabel(): string
    {
        return __('filament.orders_management.returns.inspected');
    }

    public function getTitle(): string
    {
        return __('filament.orders_management.returns.inspected');
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('status', ReturnStatus::INSPECTED);
    }
}
