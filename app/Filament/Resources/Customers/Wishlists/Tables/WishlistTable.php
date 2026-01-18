<?php

namespace App\Filament\Resources\Customers\Wishlists\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class WishlistTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with([
                'user',
                'productVariant.product',
                'productVariant.defaultImage',
            ]))
            ->columns([
                ImageColumn::make('productVariant.defaultImage.path')
                    ->label(__('validation.attributes.image'))
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder.png'))
                    ->size(50),

                TextColumn::make('user.name')
                    ->label(__('validation.attributes.user'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('user.email')
                    ->label(__('validation.attributes.email'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('productVariant.product.name')
                    ->label(__('validation.attributes.product'))
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn (Model $record): ?string => $record->productVariant->product->name ?? null)
                    ->weight('medium'),

                TextColumn::make('productVariant.sku')
                    ->label(__('validation.attributes.sku'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('productVariant.price')
                    ->label(__('validation.attributes.price'))
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
                SelectFilter::make('user_id')
                    ->label(__('validation.attributes.user'))
                    ->relationship('user', 'name')
                    ->preload()
                    ->multiple()
                    ->native(false),

                SelectFilter::make('product_id')
                    ->label(__('validation.attributes.product'))
                    ->relationship('productVariant.product', 'name')
                    ->preload()
                    ->multiple()
                    ->native(false),

                \Filament\Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label(__('filament.filters.created_from')),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label(__('filament.filters.created_until')),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn ($query, $date) => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn ($query, $date) => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                Action::make('view_product')
                    ->label(__('actions.open_website'))
                    ->icon(\Filament\Support\Icons\Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn ($record) => $record->productVariant?->product?->slug
                        ? route('store.products.show', $record->productVariant->product->slug)
                        : null)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->productVariant?->product?->slug !== null),
                Action::make('view_product_in_admin')
                    ->label(__('validation.attributes.product'))
                    ->icon(\Filament\Support\Icons\Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn ($record) => $record->productVariant?->product?->slug
                        ? route('filament.admin.resources.products.view', $record->productVariant->product->slug)
                        : null)
                    ->visible(fn ($record) => $record->productVariant?->product?->slug !== null),
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
