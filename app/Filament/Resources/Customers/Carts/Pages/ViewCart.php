<?php

namespace App\Filament\Resources\Customers\Carts\Pages;

use App\Filament\Resources\Customers\Carts\CartResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCart extends ViewRecord
{
    protected static string $resource = CartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label(__('actions.delete')),
        ];
    }
}
