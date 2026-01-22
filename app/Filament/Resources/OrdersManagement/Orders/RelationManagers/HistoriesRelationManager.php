<?php

namespace App\Filament\Resources\OrdersManagement\Orders\RelationManagers;

use App\Enums\OrderStatus;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'history';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->label(__('validation.attributes.status'))
                    ->badge()
                    ->formatStateUsing(fn (OrderStatus $state): string => $state->getLabel())
                    ->color(fn (OrderStatus $state): string => match ($state) {
                        OrderStatus::PENDING => 'warning',
                        OrderStatus::PROCESSING => 'primary',
                        OrderStatus::SHIPPED => 'primary',
                        OrderStatus::DELIVERED => 'success',
                        OrderStatus::CANCELLED => 'danger',
                        OrderStatus::RETURNED => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('comment')
                    ->label(__('validation.attributes.comment'))
                    ->limit(60)
                    ->wrap()
                    ->placeholder('—'),

                IconColumn::make('is_visible_to_user')
                    ->label(__('validation.attributes.is_visible_to_user'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('actor_type')
                    ->label(__('validation.attributes.actor_type'))
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '—')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('actor_id')
                    ->label(__('validation.attributes.actor_id'))
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
            ->defaultSort('created_at', 'desc');
    }
}
