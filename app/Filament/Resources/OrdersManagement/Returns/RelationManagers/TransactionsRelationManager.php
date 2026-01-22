<?php

namespace App\Filament\Resources\OrdersManagement\Returns\RelationManagers;

use App\Enums\PaymentMethod;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->where('type', TransactionType::Refund))
            ->columns([
                TextColumn::make('type')
                    ->label(__('validation.attributes.type'))
                    ->badge()
                    ->formatStateUsing(fn (TransactionType $state): string => $state->getLabel())
                    ->color(fn (TransactionType $state): string => match ($state) {
                        TransactionType::Payment => 'success',
                        TransactionType::Refund => 'warning',
                    })
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('validation.attributes.status'))
                    ->badge()
                    ->formatStateUsing(fn (TransactionStatus $state): string => $state->getLabel())
                    ->color(fn (TransactionStatus $state): string => match ($state) {
                        TransactionStatus::Pending => 'warning',
                        TransactionStatus::Success => 'success',
                        TransactionStatus::Failed => 'danger',
                        TransactionStatus::Cancelled => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label(__('validation.attributes.payment_method'))
                    ->badge()
                    ->formatStateUsing(fn (PaymentMethod $state): string => $state->getLabel())
                    ->color('info')
                    ->sortable(),

                TextColumn::make('amount')
                    ->label(__('validation.attributes.amount'))
                    ->formatStateUsing(fn ($state): ?string => \App\Data\Casts\MoneyCast::format($state))
                    ->sortable(),

                TextColumn::make('currency')
                    ->label(__('validation.attributes.currency'))
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('transaction_ref')
                    ->label(__('validation.attributes.transaction_ref'))
                    ->copyable()
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
