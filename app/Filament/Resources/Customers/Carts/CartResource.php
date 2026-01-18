<?php

namespace App\Filament\Resources\Customers\Carts;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Customers\Carts\Pages\ListCarts;
use App\Filament\Resources\Customers\Carts\Pages\ViewCart;
use App\Filament\Resources\Customers\Carts\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\Customers\Carts\Schemas\CartInfolist;
use App\Filament\Resources\Customers\Carts\Tables\CartsTable;
use App\Models\Cart;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CartResource extends Resource
{
    protected static ?string $model = Cart::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Customers;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = null;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.carts.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.carts.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.carts.plural_model_label');
    }

    public static function infolist(Schema $schema): Schema
    {
        return CartInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CartsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCarts::route('/'),
            'view' => ViewCart::route('/{record}'),
        ];
    }
}
