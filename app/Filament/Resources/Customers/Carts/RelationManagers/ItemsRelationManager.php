<?php

namespace App\Filament\Resources\Customers\Carts\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('product_variant_id')
                                    ->label(__('validation.attributes.product_variant'))
                                    ->relationship('productVariant', 'sku', fn ($query) => $query->with('product'))
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->product->name.' - '.$record->sku)
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false)
                                    ->disabled(fn ($record) => $record !== null)
                                    ->helperText(fn ($record) => $record ? __('filament.carts.cannot_change_variant') : null),

                                TextInput::make('quantity')
                                    ->label(__('validation.attributes.quantity'))
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->default(1),
                            ]),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['productVariant.product', 'productVariant.defaultImage']))
            ->columns([
                ImageColumn::make('productVariant.defaultImage.path')
                    ->label(__('validation.attributes.image'))
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder.png'))
                    ->size(50),

                TextColumn::make('productVariant.product.name')
                    ->label(__('validation.attributes.product'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->productVariant->product->name ?? null),

                TextColumn::make('productVariant.sku')
                    ->label(__('validation.attributes.sku'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('productVariant.price')
                    ->label(__('validation.attributes.price'))
                    ->formatStateUsing(fn ($state): ?string => \App\Data\Casts\MoneyCast::format($state))
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label(__('validation.attributes.quantity'))
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('subtotal')
                    ->label(__('filament.carts.subtotal'))
                    ->getStateUsing(fn ($record) => $record->productVariant->price * $record->quantity)
                    ->formatStateUsing(fn ($state): ?string => \App\Data\Casts\MoneyCast::format($state))
                    ->badge()
                    ->color('success')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('filament.carts.add_item'))
                    ->modalHeading(__('filament.carts.add_item')),
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
            ->defaultSort('id', 'desc')
            ->emptyStateHeading(__('filament.carts.no_items'))
            ->emptyStateDescription(__('filament.carts.no_items_description'));
    }
}
