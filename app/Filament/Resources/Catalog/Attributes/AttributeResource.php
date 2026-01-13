<?php

namespace App\Filament\Resources\Catalog\Attributes;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Catalog\Attributes\Pages\CreateAttribute;
use App\Filament\Resources\Catalog\Attributes\Pages\EditAttribute;
use App\Filament\Resources\Catalog\Attributes\Pages\ListAttributes;
use App\Filament\Resources\Catalog\Attributes\Schemas\AttributeForm;
use App\Filament\Resources\Catalog\Attributes\Tables\AttributesTable;
use App\Models\Attribute;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AttributeResource extends Resource
{
    protected static ?string $model = Attribute::class;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Catalog;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static ?string $navigationLabel = null;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.attributes.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.attributes.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.attributes.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return AttributeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttributesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ValuesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttributes::route('/'),
            'create' => CreateAttribute::route('/create'),
            'edit' => EditAttribute::route('/{record}/edit'),
        ];
    }
}
