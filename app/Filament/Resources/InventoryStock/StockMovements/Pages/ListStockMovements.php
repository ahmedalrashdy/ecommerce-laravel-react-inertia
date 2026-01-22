<?php

namespace App\Filament\Resources\InventoryStock\StockMovements\Pages;

use App\Filament\Resources\InventoryStock\StockMovements\StockMovementResource;
use Filament\Resources\Pages\ListRecords;

class ListStockMovements extends ListRecords
{
    protected static string $resource = StockMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
