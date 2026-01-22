<?php

namespace App\Filament\Resources\OrdersManagement\Returns\RelationManagers;

use App\Models\ReturnItem;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
            ->modifyQueryUsing(fn ($query) => $query->with('inspections'))
            ->columns([
                TextColumn::make('orderItem.product_name')
                    ->label(__('validation.attributes.product'))
                    ->weight('bold')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('orderItem.product_sku')
                    ->label(__('validation.attributes.sku'))
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('quantity')
                    ->label(__('validation.attributes.quantity'))
                    ->numeric()
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('reason')
                    ->label(__('validation.attributes.reason'))
                    ->wrap()
                    ->placeholder('—'),

                TextColumn::make('inspections_summary')
                    ->label(__('filament.returns.inspections_summary'))
                    ->getStateUsing(function (ReturnItem $record): string {
                        if ($record->inspections->isEmpty()) {
                            return '—';
                        }

                        return $record->inspections
                            ->map(function ($inspection): string {
                                $condition = $inspection->condition?->getLabel() ?? '—';
                                $resolution = $inspection->resolution?->getLabel() ?? '—';

                                return "{$condition} / {$resolution} × {$inspection->quantity}";
                            })
                            ->implode(' | ');
                    })
                    ->wrap()
                    ->placeholder('—'),
            ])
            ->recordActions([])
            ->headerActions([])
            ->toolbarActions([])
            ->defaultSort('id', 'desc');
    }
}
