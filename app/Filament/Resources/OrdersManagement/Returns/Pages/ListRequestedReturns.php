<?php

namespace App\Filament\Resources\OrdersManagement\Returns\Pages;

use App\Enums\ReturnStatus;
use Illuminate\Database\Eloquent\Builder;

class ListRequestedReturns extends ReturnListPage
{
    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('filament.orders_management.returns.requested');
    }

    public function getTitle(): string
    {
        return __('filament.orders_management.returns.requested');
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('status', ReturnStatus::REQUESTED);
    }
}
