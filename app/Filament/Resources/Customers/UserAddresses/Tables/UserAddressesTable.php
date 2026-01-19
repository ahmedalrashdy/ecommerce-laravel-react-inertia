<?php

namespace App\Filament\Resources\Customers\UserAddresses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UserAddressesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('user'))
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('validation.attributes.user'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('contact_person')
                    ->label(__('validation.attributes.contact_person'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('contact_phone')
                    ->label(__('validation.attributes.contact_phone'))
                    ->searchable()
                    ->copyable(),

                TextColumn::make('address_line_1')
                    ->label(__('validation.attributes.address_line_1'))
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->address_line_1)
                    ->wrap(),

                TextColumn::make('city')
                    ->label(__('validation.attributes.city'))
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('country')
                    ->label(__('validation.attributes.country'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_default_shipping')
                    ->label(__('validation.attributes.is_default_shipping'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

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
                SelectFilter::make('user_id')
                    ->label(__('validation.attributes.user'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label(__('actions.view')),
                EditAction::make()
                    ->label(__('actions.edit')),
                DeleteAction::make()
                    ->label(__('actions.delete')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('actions.delete')),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
