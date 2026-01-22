<?php

namespace App\Filament\Resources\OrdersManagement\Returns\Pages;

use App\Enums\ReturnStatus;
use Illuminate\Database\Eloquent\Builder;

class ListRejectedReturns extends ReturnListPage
{
    protected static ?int $navigationSort = 8;

    public static function getNavigationLabel(): string
    {
        return __('filament.orders_management.returns.rejected');
    }

    public function getTitle(): string
    {
        return __('filament.orders_management.returns.rejected');
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('status', ReturnStatus::REJECTED);
    }
}
