<?php

namespace App\Filament\Resources\OrdersManagement\Returns\Pages;

use App\Enums\ReturnStatus;
use Illuminate\Database\Eloquent\Builder;

class ListCompletedReturns extends ReturnListPage
{
    protected static ?int $navigationSort = 7;

    public static function getNavigationLabel(): string
    {
        return __('filament.orders_management.returns.completed');
    }

    public function getTitle(): string
    {
        return __('filament.orders_management.returns.completed');
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('status', ReturnStatus::COMPLETED);
    }
}
