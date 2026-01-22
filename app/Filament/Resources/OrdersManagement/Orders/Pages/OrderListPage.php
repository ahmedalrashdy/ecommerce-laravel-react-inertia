<?php

namespace App\Filament\Resources\OrdersManagement\Orders\Pages;

use App\Enums\NavigationGroup;
use App\Filament\Resources\OrdersManagement\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use UnitEnum;

abstract class OrderListPage extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::OrdersManagement;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_manual_order')
                ->label(__('actions.create_manual_order'))
                ->icon('heroicon-o-plus-circle')
                ->url(OrderResource::getUrl('create')),
        ];
    }
}
