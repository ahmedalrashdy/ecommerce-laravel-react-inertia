<?php

namespace App\Filament\Resources\Catalog\Categories\Tables;

use App\Enums\CategoryStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label(__('validation.attributes.image_path'))
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder.png'))
                    ->size(50),

                TextColumn::make('name')
                    ->label(__('validation.attributes.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('parent.name')
                    ->label(__('validation.attributes.parent_id'))
                    ->sortable()
                    ->toggleable()
                    ->placeholder('—'),

                TextColumn::make('slug')
                    ->label(__('validation.attributes.slug'))
                    ->searchable()
                    ->copyable()
                    ->copyMessage(__('filament.messages.slug_copied'))
                    ->toggleable(),

                TextColumn::make('status')
                    ->label(__('validation.attributes.status'))
                    ->badge()
                    ->formatStateUsing(fn (CategoryStatus $state): string => $state->getLabel())
                    ->color(fn (CategoryStatus $state): string => match ($state) {
                        CategoryStatus::Published => 'success',
                        CategoryStatus::Draft => 'gray',
                        CategoryStatus::Archived => 'danger',
                    })
                    ->sortable(),

                TextColumn::make('products_count')
                    ->label(__('validation.attributes.products_count'))
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info'),

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
                SelectFilter::make('status')
                    ->label(__('filament.filters.status'))
                    ->options(CategoryStatus::class)
                    ->multiple(),

                SelectFilter::make('parent_id')
                    ->label(__('validation.attributes.parent_id'))
                    ->relationship('parent', 'name')
                    ->preload(),
            ])
            ->recordActions([
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
