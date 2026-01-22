<?php

namespace App\Filament\Resources\InventoryStock\ProductVariants\Tables;

use App\Enums\StockMovementType;
use App\Models\ProductVariant;
use App\Services\Inventory\InventoryService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

class ProductVariantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['product', 'attributeValues.attribute']))
            ->columns([
                TextColumn::make('sku')
                    ->label(__('validation.attributes.sku'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('product.name')
                    ->label(__('validation.attributes.product'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('attributes_list')
                    ->label(__('filament.products.attributes'))
                    ->getStateUsing(fn (ProductVariant $record): string => self::formatAttributes($record))
                    ->wrap()
                    ->toggleable(),

                TextColumn::make('quantity')
                    ->label(__('validation.attributes.quantity'))
                    ->numeric()
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('price')
                    ->label(__('validation.attributes.price'))
                    ->formatStateUsing(fn ($state): ?string => \App\Data\Casts\MoneyCast::format($state))
                    ->sortable(),

                TextColumn::make('compare_at_price')
                    ->label(__('validation.attributes.compare_at_price'))
                    ->formatStateUsing(fn ($state): ?string => \App\Data\Casts\MoneyCast::format($state))
                    ->placeholder('—')
                    ->toggleable(),

                IconColumn::make('is_default')
                    ->label(__('validation.attributes.is_default'))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('supplier_restock')
                        ->label(__('filament.inventory_stock.actions.supplier_restock'))
                        ->schema([
                            TextInput::make('quantity')
                                ->label(__('validation.attributes.quantity'))
                                ->numeric()
                                ->minValue(1)
                                ->required(),
                            Textarea::make('description')
                                ->label(__('validation.attributes.description'))
                                ->rows(3),
                        ])
                        ->action(function (ProductVariant $record, array $data): void {
                            try {
                                app(InventoryService::class)->increaseStock(
                                    $record,
                                    (int) $data['quantity'],
                                    StockMovementType::SUPPLIER_RESTOCK,
                                    auth()->user(),
                                    $data['description'] ?? null
                                );

                                Notification::make()
                                    ->title(__('filament.inventory_stock.messages.restocked'))
                                    ->success()
                                    ->send();
                            } catch (Throwable $exception) {
                                Notification::make()
                                    ->title($exception->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Action::make('adjustment')
                        ->label(__('filament.inventory_stock.actions.adjustment'))
                        ->schema([
                            TextInput::make('new_quantity')
                                ->label(__('filament.inventory_stock.fields.new_quantity'))
                                ->numeric()
                                ->minValue(0)
                                ->required(),
                            Textarea::make('description')
                                ->label(__('validation.attributes.description'))
                                ->rows(3),
                        ])
                        ->action(function (ProductVariant $record, array $data): void {
                            try {
                                app(InventoryService::class)->adjustStock(
                                    $record,
                                    (int) $data['new_quantity'],
                                    auth()->user(),
                                    $data['description'] ?? null
                                );

                                Notification::make()
                                    ->title(__('filament.inventory_stock.messages.adjusted'))
                                    ->success()
                                    ->send();
                            } catch (Throwable $exception) {
                                Notification::make()
                                    ->title($exception->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Action::make('waste')
                        ->label(__('filament.inventory_stock.actions.waste'))
                        ->schema([
                            TextInput::make('quantity')
                                ->label(__('validation.attributes.quantity'))
                                ->numeric()
                                ->minValue(1)
                                ->required(),
                            Textarea::make('description')
                                ->label(__('validation.attributes.description'))
                                ->rows(3),
                        ])
                        ->requiresConfirmation()
                        ->action(function (ProductVariant $record, array $data): void {
                            try {
                                app(InventoryService::class)->decreaseStock(
                                    $record,
                                    (int) $data['quantity'],
                                    StockMovementType::WASTE,
                                    auth()->user(),
                                    $data['description'] ?? null
                                );

                                Notification::make()
                                    ->title(__('filament.inventory_stock.messages.wasted'))
                                    ->success()
                                    ->send();
                            } catch (Throwable $exception) {
                                Notification::make()
                                    ->title($exception->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->toolbarActions([
                //
            ]);
    }

    private static function formatAttributes(ProductVariant $record): string
    {
        $attributes = $record->attributeValues->map(function ($value): string {
            $name = trim((string) ($value->attribute?->name ?? ''));
            $label = trim((string) ($value->value ?? ''));

            if (! $name && ! $label) {
                return '';
            }

            return trim($name.': '.$label);
        })->filter()->values();

        return $attributes->isEmpty() ? '—' : $attributes->implode(', ');
    }
}
