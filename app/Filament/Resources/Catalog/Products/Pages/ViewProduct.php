<?php

namespace App\Filament\Resources\Catalog\Products\Pages;

use App\Filament\Resources\Catalog\Products\ProductResource;
use App\Models\Attribute;
use App\Models\ProductVariantAttribute;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addAttribute')
                ->label(__('filament.products.add_attribute'))
                ->icon('heroicon-o-plus')
                ->url(fn () => ProductResource::getUrl('add-attribute', ['record' => $this->record])),
            Action::make('deleteAttribute')
                ->label(__('filament.products.delete_attribute'))
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('filament.products.delete_attribute'))
                ->form([
                    \Filament\Forms\Components\Select::make('attribute_id')
                        ->label(__('validation.attributes.attribute'))
                        ->options(fn () => $this->getProductAttributeOptions())
                        ->required()
                        ->native(false),
                ])
                ->action(function (array $data): void {
                    $attributeId = $data['attribute_id'] ?? null;

                    if (! $attributeId) {
                        return;
                    }

                    $variantIds = $this->record->variants()->pluck('id');

                    ProductVariantAttribute::query()
                        ->whereIn('product_variant_id', $variantIds)
                        ->where('attribute_id', $attributeId)
                        ->delete();

                    Notification::make()
                        ->success()
                        ->title(__('filament.products.attribute_deleted'))
                        ->send();
                }),
            EditAction::make(),
        ];
    }

    protected function getProductAttributeOptions(): array
    {
        $attributeIds = ProductVariantAttribute::query()
            ->whereIn('product_variant_id', $this->record->variants()->pluck('id'))
            ->distinct()
            ->pluck('attribute_id');

        if ($attributeIds->isEmpty()) {
            return [];
        }

        return Attribute::query()
            ->whereIn('id', $attributeIds)
            ->pluck('name', 'id')
            ->all();
    }
}
