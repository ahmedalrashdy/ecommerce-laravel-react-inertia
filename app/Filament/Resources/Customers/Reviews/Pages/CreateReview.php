<?php

namespace App\Filament\Resources\Customers\Reviews\Pages;

use App\Filament\Resources\Customers\Reviews\ReviewResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReview extends CreateRecord
{
    protected static string $resource = ReviewResource::class;
}
