<?php

namespace App\Filament\Resources\OrdersManagement\Returns\Pages;

use App\Enums\NavigationGroup;
use App\Filament\Resources\OrdersManagement\Returns\ReturnOrderResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use UnitEnum;

abstract class ReturnListPage extends ListRecords
{
    protected static string $resource = ReturnOrderResource::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::ReturnsManagement;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_manual_return')
                ->label(__('actions.create_manual_return'))
                ->icon('heroicon-o-plus-circle')
                ->url(ReturnOrderResource::getUrl('create-manual')),
        ];
    }
}
