<?php

namespace App\Filament\Resources\Catalog\Categories;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Catalog\Categories\Pages\CreateCategory;
use App\Filament\Resources\Catalog\Categories\Pages\EditCategory;
use App\Filament\Resources\Catalog\Categories\Pages\ListCategories;
use App\Filament\Resources\Catalog\Categories\Schemas\CategoryForm;
use App\Filament\Resources\Catalog\Categories\Tables\CategoriesTable;
use App\Models\Category;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Catalog;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolder;

    protected static ?string $navigationLabel = null;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.categories.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.categories.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.categories.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return CategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoriesTable::configure($table);
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
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
