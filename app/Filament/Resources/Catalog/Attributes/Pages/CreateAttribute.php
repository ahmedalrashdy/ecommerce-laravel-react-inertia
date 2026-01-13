<?php

namespace App\Filament\Resources\Catalog\Attributes\Pages;

use App\Filament\Resources\Catalog\Attributes\AttributeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttribute extends CreateRecord
{
    protected static string $resource = AttributeResource::class;
}
