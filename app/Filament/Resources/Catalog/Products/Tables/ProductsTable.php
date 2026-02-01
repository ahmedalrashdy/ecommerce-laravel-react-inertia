<?php

namespace App\Filament\Resources\Catalog\Products\Tables;

use App\Enums\ProductStatus;
use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query, $livewire): Builder {
                $search = trim((string) ($livewire->tableSearch ?? ''));
                $with = ['category', 'brand', 'defaultVariant.defaultImage'];
                if ($search !== '') {
                    $with[] = 'variants';
                }

                return $query->with($with);
            })
            ->columns([
                ImageColumn::make('defaultVariant.defaultImage.path')
                    ->label(__('validation.attributes.image'))
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder.png'))
                    ->size(50),

                TextColumn::make('name')
                    ->label(__('validation.attributes.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage(__('filament.messages.slug_copied')),

                TextColumn::make('display_variant_sku')
                    ->label(__('validation.attributes.sku'))
                    ->getStateUsing(function (Product $record): ?string {
                        $search = trim((string) (\Livewire\Livewire::current()?->tableSearch ?? ''));
                        if ($search !== '' && $record->relationLoaded('variants')) {
                            $variant = $record->variants
                                ->filter(fn ($v) => str_contains(strtolower($v->sku), strtolower($search)))
                                ->sortByDesc('is_default')
                                ->sortBy('id')
                                ->first();

                            return $variant?->sku ?? $record->defaultVariant?->sku;
                        }

                        return $record->defaultVariant?->sku;
                    })
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->whereHas(
                        'variants',
                        fn (Builder $q): Builder => $q->where('sku', 'like', "%{$search}%")
                    ))
                    ->badge()
                    ->color('gray')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('category.name')
                    ->label(__('validation.attributes.category_id'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('brand.name')
                    ->label(__('validation.attributes.brand_id'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('status')
                    ->label(__('validation.attributes.status'))
                    ->badge()
                    ->formatStateUsing(fn (ProductStatus $state): string => $state->getLabel())
                    ->color(fn (ProductStatus $state): string => match ($state) {
                        ProductStatus::Published => 'success',
                        ProductStatus::Draft => 'gray',
                        ProductStatus::Archived => 'danger',
                    })
                    ->sortable(),

                ToggleColumn::make('featured')
                    ->label(__('validation.attributes.featured'))
                    ->sortable(),

                TextColumn::make('variants_count')
                    ->label(__('filament.products.variants_count'))
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('sales_count')
                    ->label(__('validation.attributes.sales_count'))
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('favorites_count')
                    ->label(__('validation.attributes.favorites_count'))
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('rating_avg')
                    ->label(__('validation.attributes.rating_avg'))
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('reviews_count')
                    ->label(__('validation.attributes.reviews_count'))
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),

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
                    ->options(ProductStatus::class)
                    ->multiple(),

                SelectFilter::make('category_id')
                    ->label(__('validation.attributes.category_id'))
                    ->relationship('category', 'name')
                    ->preload()
                    ->multiple(),

                SelectFilter::make('brand_id')
                    ->label(__('validation.attributes.brand_id'))
                    ->relationship('brand', 'name')
                    ->preload()
                    ->multiple(),

                TernaryFilter::make('featured')
                    ->label(__('filament.filters.featured'))
                    ->placeholder(__('filament.filters.all'))
                    ->trueLabel(__('filament.filters.featured_only'))
                    ->falseLabel(__('filament.filters.not_featured_only')),
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
