<?php

namespace App\Filament\Resources\Customers\Wishlists;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Customers\Wishlists\Pages\ListWishlists;
use App\Filament\Resources\Customers\Wishlists\Tables\WishlistTable;
use App\Models\Wishlist;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class WishlistResource extends Resource
{
    protected static ?string $model = Wishlist::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Customers;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = null;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.wishlists.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.wishlists.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.wishlists.plural_model_label');
    }

    public static function table(Table $table): Table
    {
        return WishlistTable::configure($table);
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
            'index' => ListWishlists::route('/'),
        ];
    }
}
