<?php

namespace App\Filament\Resources\OrdersManagement\Orders\RelationManagers;

use App\Enums\StockMovementType;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StockMovementsRelationManager extends RelationManager
{
    protected static string $relationship = 'stockMovements';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('productVariant.product'))
            ->columns([
                TextColumn::make('productVariant.product.name')
                    ->label(__('validation.attributes.product'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('productVariant.sku')
                    ->label(__('validation.attributes.sku'))
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('type')
                    ->label(__('validation.attributes.type'))
                    ->badge()
                    ->formatStateUsing(fn (StockMovementType $state): string => $state->getLabel())
                    ->color('info')
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label(__('validation.attributes.quantity'))
                    ->numeric()
                    ->badge()
                    ->sortable(),

                TextColumn::make('quantity_before')
                    ->label(__('filament.stock_movements.quantity_before'))
                    ->numeric()
                    ->toggleable(),

                TextColumn::make('quantity_after')
                    ->label(__('filament.stock_movements.quantity_after'))
                    ->numeric()
                    ->toggleable(),

                TextColumn::make('description')
                    ->label(__('validation.attributes.description'))
                    ->wrap()
                    ->limit(60)
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label(__('validation.attributes.created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([])
            ->headerActions([])
            ->toolbarActions([])
            ->defaultSort('id', 'desc');
    }
}
