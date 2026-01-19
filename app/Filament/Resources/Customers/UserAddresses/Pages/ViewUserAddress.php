<?php

namespace App\Filament\Resources\Customers\UserAddresses\Pages;

use App\Filament\Resources\Customers\UserAddresses\UserAddressResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewUserAddress extends ViewRecord
{
    protected static string $resource = UserAddressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
