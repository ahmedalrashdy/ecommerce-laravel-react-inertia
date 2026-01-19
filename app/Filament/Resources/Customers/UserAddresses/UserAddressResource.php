<?php

namespace App\Filament\Resources\Customers\UserAddresses;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Customers\UserAddresses\Pages\CreateUserAddress;
use App\Filament\Resources\Customers\UserAddresses\Pages\EditUserAddress;
use App\Filament\Resources\Customers\UserAddresses\Pages\ListUserAddresses;
use App\Filament\Resources\Customers\UserAddresses\Pages\ViewUserAddress;
use App\Filament\Resources\Customers\UserAddresses\Schemas\UserAddressForm;
use App\Filament\Resources\Customers\UserAddresses\Schemas\UserAddressInfolist;
use App\Filament\Resources\Customers\UserAddresses\Tables\UserAddressesTable;
use App\Models\UserAddress;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class UserAddressResource extends Resource
{
    protected static ?string $model = UserAddress::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Customers;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = null;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?string $recordTitleAttribute = 'address_line_1';

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.user_addresses.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.user_addresses.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.user_addresses.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return UserAddressForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserAddressInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserAddressesTable::configure($table);
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
            'index' => ListUserAddresses::route('/'),
            'create' => CreateUserAddress::route('/create'),
            'view' => ViewUserAddress::route('/{record}'),
            'edit' => EditUserAddress::route('/{record}/edit'),
        ];
    }
}
