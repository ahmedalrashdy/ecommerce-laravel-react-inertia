<?php

namespace App\Filament\Resources\InventoryStock\ProductVariants\Pages;

use App\Filament\Resources\InventoryStock\ProductVariants\ProductVariantResource;
use Filament\Resources\Pages\ListRecords;

class ListProductVariants extends ListRecords
{
    protected static string $resource = ProductVariantResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
