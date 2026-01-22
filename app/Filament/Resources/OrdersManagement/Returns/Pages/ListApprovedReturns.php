<?php

namespace App\Filament\Resources\OrdersManagement\Returns\Pages;

use App\Enums\ReturnStatus;
use Illuminate\Database\Eloquent\Builder;

class ListApprovedReturns extends ReturnListPage
{
    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('filament.orders_management.returns.approved');
    }

    public function getTitle(): string
    {
        return __('filament.orders_management.returns.approved');
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('status', ReturnStatus::APPROVED);
    }
}
