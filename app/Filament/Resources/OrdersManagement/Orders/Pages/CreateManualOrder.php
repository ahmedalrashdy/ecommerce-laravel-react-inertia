<?php

namespace App\Filament\Resources\OrdersManagement\Orders\Pages;

use App\Filament\Resources\OrdersManagement\Orders\OrderResource;
use App\Filament\Resources\OrdersManagement\Orders\Schemas\ManualOrderForm;
use App\Services\Orders\ManualOrderService;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class CreateManualOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    public function getTitle(): string
    {
        return __('filament.orders.manual_order');
    }

    public function getBreadcrumb(): string
    {
        return __('filament.orders.manual_order');
    }

    public function form(Schema $schema): Schema
    {
        return ManualOrderForm::configure($schema);
    }

    protected function handleRecordCreation(array $data): Model
    {
        return app(ManualOrderService::class)->create($data, auth()->user());
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('filament.orders.manual_order_created');
    }
}
