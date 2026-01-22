<?php

namespace App\Filament\Resources\InventoryStock\ProductVariants;

use App\Enums\NavigationGroup;
use App\Filament\Resources\InventoryStock\ProductVariants\Pages\ListProductVariants;
use App\Filament\Resources\InventoryStock\ProductVariants\Tables\ProductVariantsTable;
use App\Models\ProductVariant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProductVariantResource extends Resource
{
    protected static ?string $model = ProductVariant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Operations;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = null;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?string $recordTitleAttribute = 'sku';

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.product_variants.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.product_variants.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.product_variants.plural_model_label');
    }

    public static function table(Table $table): Table
    {
        return ProductVariantsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductVariants::route('/'),
        ];
    }
}
