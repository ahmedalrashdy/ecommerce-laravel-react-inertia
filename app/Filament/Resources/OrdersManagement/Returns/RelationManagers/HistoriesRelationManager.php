<?php

namespace App\Filament\Resources\OrdersManagement\Returns\RelationManagers;

use App\Enums\ReturnStatus;
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
                    ->formatStateUsing(fn (ReturnStatus $state): string => $state->getLabel())
                    ->color(fn (ReturnStatus $state): string => match ($state) {
                        ReturnStatus::REQUESTED => 'warning',
                        ReturnStatus::APPROVED => 'info',
                        ReturnStatus::SHIPPED_BACK => 'primary',
                        ReturnStatus::RECEIVED => 'primary',
                        ReturnStatus::INSPECTED => 'warning',
                        ReturnStatus::COMPLETED => 'success',
                        ReturnStatus::REJECTED => 'danger',
                    }),

                TextColumn::make('comment')
                    ->label(__('validation.attributes.comment'))
                    ->wrap()
                    ->placeholder('—'),

                IconColumn::make('is_visible_to_user')
                    ->label(__('validation.attributes.is_visible_to_user'))
                    ->boolean(),

                TextColumn::make('actor_type')
                    ->label(__('validation.attributes.actor'))
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '—')
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label(__('validation.attributes.created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->recordActions([])
            ->headerActions([])
            ->toolbarActions([])
            ->defaultSort('id', 'desc');
    }
}
