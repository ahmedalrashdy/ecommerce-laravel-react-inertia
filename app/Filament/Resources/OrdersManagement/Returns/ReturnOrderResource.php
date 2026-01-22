<?php

namespace App\Filament\Resources\OrdersManagement\Returns;

use App\Enums\NavigationGroup;
use App\Filament\Resources\OrdersManagement\Returns\Pages\CreateManualReturn;
use App\Filament\Resources\OrdersManagement\Returns\Pages\InspectReturnOrder;
use App\Filament\Resources\OrdersManagement\Returns\Pages\ListApprovedReturns;
use App\Filament\Resources\OrdersManagement\Returns\Pages\ListCompletedReturns;
use App\Filament\Resources\OrdersManagement\Returns\Pages\ListInspectedReturns;
use App\Filament\Resources\OrdersManagement\Returns\Pages\ListReceivedReturns;
use App\Filament\Resources\OrdersManagement\Returns\Pages\ListRejectedReturns;
use App\Filament\Resources\OrdersManagement\Returns\Pages\ListRequestedReturns;
use App\Filament\Resources\OrdersManagement\Returns\Pages\ListReturnOrders;
use App\Filament\Resources\OrdersManagement\Returns\Pages\ListShippedBackReturns;
use App\Filament\Resources\OrdersManagement\Returns\Pages\ViewReturnOrder;
use App\Filament\Resources\OrdersManagement\Returns\RelationManagers\HistoriesRelationManager;
use App\Filament\Resources\OrdersManagement\Returns\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\OrdersManagement\Returns\RelationManagers\StockMovementsRelationManager;
use App\Filament\Resources\OrdersManagement\Returns\RelationManagers\TransactionsRelationManager;
use App\Filament\Resources\OrdersManagement\Returns\Schemas\ReturnOrderInfolist;
use App\Filament\Resources\OrdersManagement\Returns\Tables\ReturnOrdersTable;
use App\Models\ReturnOrder;
use BackedEnum;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

use function Filament\Support\original_request;

class ReturnOrderResource extends Resource
{
    protected static ?string $model = ReturnOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUturnLeft;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::ReturnsManagement;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = null;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?string $recordTitleAttribute = 'return_number';

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.returns.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.returns.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.returns.plural_model_label');
    }

    public static function infolist(Schema $schema): Schema
    {
        return ReturnOrderInfolist::configure($schema);
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make(__('filament.resources.returns.navigation_label'))
                ->group(NavigationGroup::ReturnsManagement)
                ->icon(Heroicon::OutlinedArrowUturnLeft)
                ->isActiveWhen(fn (): bool => original_request()->routeIs(static::getRouteBaseName().'.*'))
                ->sort(1)
                ->url(ListReturnOrders::getUrl()),

            NavigationItem::make(__('filament.orders_management.returns.requested'))
                ->group(NavigationGroup::ReturnsManagement)
                ->icon(Heroicon::OutlinedInbox)
                ->isActiveWhen(fn (): bool => original_request()->routeIs(ListRequestedReturns::getRouteName()))
                ->sort(2)
                ->url(ListRequestedReturns::getUrl()),

            NavigationItem::make(__('filament.orders_management.returns.approved'))
                ->group(NavigationGroup::ReturnsManagement)
                ->icon(Heroicon::OutlinedCheckBadge)
                ->isActiveWhen(fn (): bool => original_request()->routeIs(ListApprovedReturns::getRouteName()))
                ->sort(3)
                ->url(ListApprovedReturns::getUrl()),

            NavigationItem::make(__('filament.orders_management.returns.shipped_back'))
                ->group(NavigationGroup::ReturnsManagement)
                ->icon(Heroicon::OutlinedTruck)
                ->isActiveWhen(fn (): bool => original_request()->routeIs(ListShippedBackReturns::getRouteName()))
                ->sort(4)
                ->url(ListShippedBackReturns::getUrl()),

            NavigationItem::make(__('filament.orders_management.returns.received'))
                ->group(NavigationGroup::ReturnsManagement)
                ->icon(Heroicon::OutlinedArchiveBox)
                ->isActiveWhen(fn (): bool => original_request()->routeIs(ListReceivedReturns::getRouteName()))
                ->sort(5)
                ->url(ListReceivedReturns::getUrl()),

            NavigationItem::make(__('filament.orders_management.returns.inspected'))
                ->group(NavigationGroup::ReturnsManagement)
                ->icon(Heroicon::OutlinedMagnifyingGlass)
                ->isActiveWhen(fn (): bool => original_request()->routeIs(ListInspectedReturns::getRouteName()))
                ->sort(6)
                ->url(ListInspectedReturns::getUrl()),

            NavigationItem::make(__('filament.orders_management.returns.completed'))
                ->group(NavigationGroup::ReturnsManagement)
                ->icon(Heroicon::OutlinedCheckCircle)
                ->isActiveWhen(fn (): bool => original_request()->routeIs(ListCompletedReturns::getRouteName()))
                ->sort(7)
                ->url(ListCompletedReturns::getUrl()),

            NavigationItem::make(__('filament.orders_management.returns.rejected'))
                ->group(NavigationGroup::ReturnsManagement)
                ->icon(Heroicon::OutlinedXCircle)
                ->isActiveWhen(fn (): bool => original_request()->routeIs(ListRejectedReturns::getRouteName()))
                ->sort(8)
                ->url(ListRejectedReturns::getUrl()),
        ];
    }

    public static function table(Table $table): Table
    {
        return ReturnOrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
            HistoriesRelationManager::class,
            TransactionsRelationManager::class,
            StockMovementsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReturnOrders::route('/'),
            'requested' => ListRequestedReturns::route('/requested'),
            'approved' => ListApprovedReturns::route('/approved'),
            'shipped-back' => ListShippedBackReturns::route('/shipped-back'),
            'received' => ListReceivedReturns::route('/received'),
            'inspected' => ListInspectedReturns::route('/inspected'),
            'completed' => ListCompletedReturns::route('/completed'),
            'rejected' => ListRejectedReturns::route('/rejected'),
            'create-manual' => CreateManualReturn::route('/create-manual'),
            'inspect' => InspectReturnOrder::route('/{record}/inspect'),
            'view' => ViewReturnOrder::route('/{record}'),
        ];
    }
}
