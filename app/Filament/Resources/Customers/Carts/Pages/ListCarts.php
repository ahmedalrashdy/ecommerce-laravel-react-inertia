<?php

namespace App\Filament\Resources\Customers\Carts\Pages;

use App\Filament\Resources\Customers\Carts\CartResource;
use Filament\Resources\Pages\ListRecords;

class ListCarts extends ListRecords
{
    protected static string $resource = CartResource::class;
}
