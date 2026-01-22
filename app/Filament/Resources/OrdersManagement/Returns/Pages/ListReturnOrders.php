<?php

namespace App\Filament\Resources\OrdersManagement\Returns\Pages;

use BackedEnum;
use Filament\Support\Icons\Heroicon;

class ListReturnOrders extends ReturnListPage
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationLabel = null;

    protected static ?string $title = null;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUturnLeft;

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('filament.orders_management.returns.all');
    }

    public function getTitle(): string
    {
        return __('filament.orders_management.returns.all');
    }
}
