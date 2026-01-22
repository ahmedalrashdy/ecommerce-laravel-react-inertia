<?php

namespace App\Filament\Resources\OrdersManagement\Returns\Pages;

use App\Enums\ReturnStatus;
use Illuminate\Database\Eloquent\Builder;

class ListReceivedReturns extends ReturnListPage
{
    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('filament.orders_management.returns.received');
    }

    public function getTitle(): string
    {
        return __('filament.orders_management.returns.received');
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('status', ReturnStatus::RECEIVED);
    }
}
