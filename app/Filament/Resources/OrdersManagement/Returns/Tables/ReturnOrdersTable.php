<?php

namespace App\Filament\Resources\OrdersManagement\Returns\Tables;

use App\Enums\RefundMethod;
use App\Enums\ReturnStatus;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReturnOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('return_number')
                    ->label(__('validation.attributes.return_number'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('order.order_number')
                    ->label(__('validation.attributes.order_number'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->placeholder('—'),

                TextColumn::make('user.name')
                    ->label(__('validation.attributes.user'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->placeholder('—'),

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
                    })
                    ->sortable(),

                TextColumn::make('refund_method')
                    ->label(__('validation.attributes.refund_method'))
                    ->badge()
                    ->formatStateUsing(fn (?RefundMethod $state): string => $state?->getLabel() ?? '—')
                    ->color('gray')
                    ->placeholder('—'),

                TextColumn::make('refund_amount')
                    ->label(__('validation.attributes.refund_amount'))
                    ->formatStateUsing(fn ($state): ?string => \App\Data\Casts\MoneyCast::format($state))
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('created_at')
                    ->label(__('validation.attributes.created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('validation.attributes.status'))
                    ->options(ReturnStatus::class)
                    ->native(false),

                SelectFilter::make('refund_method')
                    ->label(__('validation.attributes.refund_method'))
                    ->options(RefundMethod::class)
                    ->native(false),

                SelectFilter::make('user_id')
                    ->label(__('validation.attributes.user'))
                    ->relationship('user', 'name')
                    ->preload()
                    ->multiple(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label(__('actions.view')),
            ])
            ->toolbarActions([])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->with(['order', 'user']));
    }
}
