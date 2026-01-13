<?php

namespace App\Filament\Resources\Catalog\Products;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Catalog\Products\Pages\CreateProduct;
use App\Filament\Resources\Catalog\Products\Pages\EditProduct;
use App\Filament\Resources\Catalog\Products\Pages\ListProducts;
use App\Filament\Resources\Catalog\Products\Pages\ViewProduct;
use App\Filament\Resources\Catalog\Products\Schemas\ProductForm;
use App\Filament\Resources\Catalog\Products\Schemas\ProductInfolist;
use App\Filament\Resources\Catalog\Products\Tables\ProductsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Catalog;

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\VariantsRelationManager::class,
            RelationManagers\ReviewsRelationManager::class,
            RelationManagers\WishlistsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'view' => ViewProduct::route('/{record}'),
            'edit' => EditProduct::route('/{record}/edit'),
            'add-attribute' => Pages\AddProductAttribute::route('/{record}/attributes/add'),
        ];
    }
}
