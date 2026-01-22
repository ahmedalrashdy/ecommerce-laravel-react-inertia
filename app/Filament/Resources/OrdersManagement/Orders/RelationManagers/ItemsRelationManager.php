<?php

namespace App\Filament\Resources\OrdersManagement\Orders\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product_name')
                    ->label(__('validation.attributes.product'))
                    ->getStateUsing(fn ($record): string => $record->product_variant_snapshot['product']['name'] ?? '—')
                    ->weight('bold')
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->where(
                        'product_variant_snapshot->product->name',
                        'like',
                        "%{$search}%"
                    ))
                    ->sortable(query: fn (Builder $query, string $direction): Builder => self::orderBySnapshot(
                        $query,
                        '$.product.name',
                        $direction
                    )),

                TextColumn::make('product_sku')
                    ->label(__('validation.attributes.sku'))
                    ->getStateUsing(fn ($record): string => $record->product_variant_snapshot['variant']['sku'] ?? '—')
                    ->badge()
                    ->color('gray')
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->where(
                        'product_variant_snapshot->variant->sku',
                        'like',
                        "%{$search}%"
                    ))
                    ->sortable(query: fn (Builder $query, string $direction): Builder => self::orderBySnapshot(
                        $query,
                        '$.variant.sku',
                        $direction
                    )),

                TextColumn::make('attributes_list')
                    ->label(__('filament.products.attributes'))
                    ->getStateUsing(fn ($record): string => self::formatAttributes(
                        $record->attributes_list
                            ?? $record->product_variant_snapshot['variant']['attributes']
                            ?? []
                    ))
                    ->wrap()
                    ->toggleable(),

                TextColumn::make('price')
                    ->label(__('validation.attributes.price'))
                    ->formatStateUsing(fn ($state): ?string => \App\Data\Casts\MoneyCast::format($state))
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label(__('validation.attributes.quantity'))
                    ->numeric()
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('discount_amount')
                    ->label(__('validation.attributes.discount_amount'))
                    ->formatStateUsing(fn ($state): ?string => \App\Data\Casts\MoneyCast::format($state))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('item_total')
                    ->label(__('filament.orders.item_total'))
                    ->getStateUsing(function ($record): float {
                        $price = (float) $record->price;
                        $discount = (float) $record->discount_amount;
                        $total = ($price * $record->quantity) - $discount;

                        return max(0, $total);
                    })
                    ->formatStateUsing(fn ($state): ?string => \App\Data\Casts\MoneyCast::format($state))
                    ->badge()
                    ->color('success'),
            ])
            ->recordActions([])
            ->headerActions([])
            ->toolbarActions([])
            ->defaultSort('id', 'desc');
    }

    private static function orderBySnapshot(Builder $query, string $path, string $direction): Builder
    {
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        if ($query->getConnection()->getDriverName() === 'sqlite') {
            return $query->orderByRaw("json_extract(product_variant_snapshot, '{$path}') {$direction}");
        }

        return $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(product_variant_snapshot, '{$path}')) {$direction}");
    }

    /**
     * @param  iterable<int, array<string, mixed>>|null  $attributes
     */
    private static function formatAttributes(?iterable $attributes): string
    {
        if (! $attributes) {
            return '—';
        }

        $parts = [];

        foreach ($attributes as $attribute) {
            if (! is_array($attribute)) {
                continue;
            }

            $name = trim((string) ($attribute['name'] ?? $attribute['attribute_name'] ?? $attribute['attribute'] ?? ''));
            $value = trim((string) ($attribute['value'] ?? $attribute['label'] ?? ''));

            if (! $name && ! $value) {
                continue;
            }

            $parts[] = trim($name.': '.$value);
        }

        return $parts === [] ? '—' : implode(', ', $parts);
    }
}
