<?php

namespace App\Filament\Resources\OrdersManagement\Orders\Pages;

use BackedEnum;
use Filament\Support\Icons\Heroicon;

class ListOrders extends OrderListPage
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationLabel = null;

    protected static ?string $title = null;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('filament.orders_management.orders.all');
    }

    public function getTitle(): string
    {
        return __('filament.orders_management.orders.all');
    }
}
