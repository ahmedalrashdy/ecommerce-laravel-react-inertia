<?php

namespace App\Filament\Resources\Catalog\Products\Schemas;

use App\Enums\ProductStatus;
use App\Models\Product;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.products.basic_info'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ImageEntry::make('defaultVariant.defaultImage.path')
                                    ->label(__('validation.attributes.image'))
                                    ->circular()
                                    ->defaultImageUrl(url('/images/placeholder.png'))
                                    ->columnSpan(1),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label(__('validation.attributes.name'))
                                            ->weight('bold')
                                            ->size('lg')
                                            ->columnSpanFull(),

                                        TextEntry::make('category.name')
                                            ->label(__('validation.attributes.category_id'))
                                            ->badge()
                                            ->color('info'),

                                        TextEntry::make('brand.name')
                                            ->label(__('validation.attributes.brand_id'))
                                            ->badge()
                                            ->color('gray')
                                            ->placeholder('—'),

                                        TextEntry::make('slug')
                                            ->label(__('validation.attributes.slug'))
                                            ->copyable()
                                            ->copyMessage(__('filament.messages.slug_copied'))
                                            ->columnSpanFull(),

                                        TextEntry::make('description')
                                            ->label(__('validation.attributes.description'))
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpan(2),
                            ]),
                    ]),

                Section::make(__('filament.products.specifications'))
                    ->schema([
                        RepeatableEntry::make('specifications')
                            ->label(__('filament.products.specifications'))
                            ->schema([
                                TextEntry::make('key')
                                    ->hiddenLabel(),
                                TextEntry::make('value')
                                    ->hiddenLabel(),
                            ])
                            ->columns(2)
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ]),

                Section::make(__('filament.products.status_visibility'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('status')
                                    ->label(__('validation.attributes.status'))
                                    ->badge()
                                    ->formatStateUsing(fn (ProductStatus $state): string => $state->getLabel())
                                    ->color(fn (ProductStatus $state): string => match ($state) {
                                        ProductStatus::Published => 'success',
                                        ProductStatus::Draft => 'gray',
                                        ProductStatus::Archived => 'danger',
                                    }),

                                IconEntry::make('featured')
                                    ->label(__('validation.attributes.featured'))
                                    ->boolean(),

                                TextEntry::make('variants_count')
                                    ->label(__('filament.products.variants_count'))
                                    ->numeric()
                                    ->badge()
                                    ->color('info'),
                            ]),
                    ]),

                Section::make(__('filament.products.statistics'))
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('sales_count')
                                    ->label(__('validation.attributes.sales_count'))
                                    ->numeric()
                                    ->badge()
                                    ->color('success'),

                                TextEntry::make('favorites_count')
                                    ->label(__('validation.attributes.favorites_count'))
                                    ->numeric()
                                    ->badge()
                                    ->color('warning'),

                                TextEntry::make('rating_avg')
                                    ->label(__('validation.attributes.rating_avg'))
                                    ->numeric()
                                    ->badge()
                                    ->color('primary'),

                                TextEntry::make('reviews_count')
                                    ->label(__('validation.attributes.reviews_count'))
                                    ->numeric()
                                    ->badge()
                                    ->color('info'),
                            ]),
                    ]),

                Section::make(__('validation.attributes.timestamps'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('validation.attributes.created_at'))
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('—'),

                                TextEntry::make('updated_at')
                                    ->label(__('validation.attributes.updated_at'))
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('—'),

                                TextEntry::make('deleted_at')
                                    ->label(__('validation.attributes.deleted_at'))
                                    ->dateTime('d/m/Y H:i')
                                    ->visible(fn (Product $record): bool => $record->deleted_at != null)
                                    ->placeholder('—')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}
