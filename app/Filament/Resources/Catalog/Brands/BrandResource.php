<?php

namespace App\Filament\Resources\Catalog\Brands;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Catalog\Brands\Pages\CreateBrand;
use App\Filament\Resources\Catalog\Brands\Pages\EditBrand;
use App\Filament\Resources\Catalog\Brands\Pages\ListBrands;
use App\Filament\Resources\Catalog\Brands\Schemas\BrandForm;
use App\Filament\Resources\Catalog\Brands\Tables\BrandsTable;
use App\Models\Brand;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Catalog;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = null;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.brands.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.brands.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.brands.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return BrandForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BrandsTable::configure($table);
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
            'index' => ListBrands::route('/'),
            'create' => CreateBrand::route('/create'),
            'edit' => EditBrand::route('/{record}/edit'),
        ];
    }
}
