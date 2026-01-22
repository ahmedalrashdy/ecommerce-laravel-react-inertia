<?php

namespace App\Filament\Resources\InventoryStock\StockMovements\Tables;

use App\Enums\StockMovementType;
use App\Models\Order;
use App\Models\ReturnOrder;
use App\Models\StockMovement;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class StockMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['productVariant.product', 'sourceable']))
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('validation.attributes.created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('sku')
                    ->label(__('validation.attributes.sku'))
                    ->getStateUsing(fn (StockMovement $record): string => $record->productVariant?->sku ?? '—')
                    ->badge()
                    ->color('gray')
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->whereHas(
                        'productVariant',
                        fn (Builder $variantQuery): Builder => $variantQuery->where('sku', 'like', "%{$search}%")
                    ))
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->join(
                        'product_variants',
                        'stock_movements.product_variant_id',
                        '=',
                        'product_variants.id'
                    )->orderBy('product_variants.sku', $direction)->select('stock_movements.*')),

                TextColumn::make('product')
                    ->label(__('validation.attributes.product'))
                    ->getStateUsing(fn (StockMovement $record): string => $record->productVariant?->product?->name ?? '—')
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->whereHas(
                        'productVariant.product',
                        fn (Builder $productQuery): Builder => $productQuery->where('name', 'like', "%{$search}%")
                    ))
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->join(
                        'product_variants',
                        'stock_movements.product_variant_id',
                        '=',
                        'product_variants.id'
                    )->join('products', 'product_variants.product_id', '=', 'products.id')
                        ->orderBy('products.name', $direction)
                        ->select('stock_movements.*')),

                TextColumn::make('type')
                    ->label(__('validation.attributes.type'))
                    ->badge()
                    ->formatStateUsing(fn (StockMovementType $state): string => $state->getLabel()),

                TextColumn::make('quantity')
                    ->label(__('validation.attributes.quantity'))
                    ->getStateUsing(fn (StockMovement $record): string => $record->quantity > 0
                        ? '+'.$record->quantity
                        : (string) $record->quantity
                    )
                    ->badge()
                    ->color(fn (StockMovement $record): string => $record->quantity >= 0 ? 'success' : 'danger')
                    ->sortable(),

                TextColumn::make('quantity_before')
                    ->label(__('validation.attributes.quantity_before'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('quantity_after')
                    ->label(__('validation.attributes.quantity_after'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('source')
                    ->label(__('filament.inventory_stock.columns.source'))
                    ->getStateUsing(fn (StockMovement $record): string => self::formatSource($record))
                    ->wrap()
                    ->toggleable(),

                TextColumn::make('description')
                    ->label(__('validation.attributes.description'))
                    ->placeholder('—')
                    ->wrap()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }

    private static function formatSource(StockMovement $record): string
    {
        $source = $record->sourceable;

        if (! $source) {
            return '—';
        }

        if ($source instanceof User) {
            return trim(__('filament.inventory_stock.source.user').': '.$source->name);
        }

        if ($source instanceof Order) {
            return trim(__('filament.inventory_stock.source.order').': '.$source->order_number);
        }

        if ($source instanceof ReturnOrder) {
            return trim(__('filament.inventory_stock.source.return').': '.$source->return_number);
        }

        $label = Str::headline(class_basename($source));

        return trim($label.': '.$source->getKey());
    }
}
