<?php

namespace App\Filament\Resources\InventoryStock\StockMovements;

use App\Enums\NavigationGroup;
use App\Filament\Resources\InventoryStock\StockMovements\Pages\ListStockMovements;
use App\Filament\Resources\InventoryStock\StockMovements\Tables\StockMovementsTable;
use App\Models\StockMovement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Operations;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = null;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.stock_movements.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.stock_movements.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.stock_movements.plural_model_label');
    }

    public static function table(Table $table): Table
    {
        return StockMovementsTable::configure($table);
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
            'index' => ListStockMovements::route('/'),
        ];
    }
}
