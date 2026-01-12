<?php

namespace App\Filament\Resources\Customers\Reviews\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('validation.attributes.user'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('product.name')
                    ->label(__('validation.attributes.product'))
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn (Model $record): ?string => $record->product->name ?? null),

                TextColumn::make('rating')
                    ->label(__('validation.attributes.rating'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '1', '2' => 'danger',
                        '3' => 'warning',
                        '4', '5' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => $state.' ⭐')
                    ->sortable(),

                TextColumn::make('comment')
                    ->label(__('validation.attributes.comment'))
                    ->limit(50)
                    ->tooltip(fn (Model $record): ?string => $record->comment)
                    ->wrap()
                    ->toggleable(),

                IconColumn::make('is_approved')
                    ->label(__('validation.attributes.is_approved'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('validation.attributes.created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('validation.attributes.updated_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_approved')
                    ->label(__('validation.attributes.is_approved'))
                    ->options([
                        true => __('filament.reviews.approved'),
                        false => __('filament.reviews.pending'),
                    ])
                    ->native(false),

                SelectFilter::make('rating')
                    ->label(__('validation.attributes.rating'))
                    ->options([
                        1 => '1 ⭐',
                        2 => '2 ⭐',
                        3 => '3 ⭐',
                        4 => '4 ⭐',
                        5 => '5 ⭐',
                    ])
                    ->native(false),
            ])
            ->recordActions([
                EditAction::make()
                    ->label(__('actions.edit')),
                DeleteAction::make()
                    ->label(__('actions.delete')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label(__('filament.reviews.approve'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['is_approved' => true]))
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('reject')
                        ->label(__('filament.reviews.reject'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['is_approved' => false]))
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make()
                        ->label(__('actions.delete')),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
