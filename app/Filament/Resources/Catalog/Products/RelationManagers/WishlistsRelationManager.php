<?php

namespace App\Filament\Resources\Catalog\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WishlistsRelationManager extends RelationManager
{
    protected static string $relationship = 'wishlists';

    protected static ?string $title = null;

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament.resources.wishlists.plural_model_label');
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with([
                'user',
                'productVariant.defaultImage',
            ]))
            ->columns([
                ImageColumn::make('productVariant.defaultImage.path')
                    ->label(__('validation.attributes.image'))
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder.png'))
                    ->size(40),

                TextColumn::make('user.name')
                    ->label(__('validation.attributes.user'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('user.email')
                    ->label(__('validation.attributes.email'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('productVariant.sku')
                    ->label(__('validation.attributes.sku'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label(__('validation.attributes.created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label(__('actions.view')),
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
