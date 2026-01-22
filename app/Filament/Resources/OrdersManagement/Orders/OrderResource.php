<?php

namespace App\Filament\Resources\OrdersManagement\Orders;

use App\Enums\NavigationGroup;
use App\Filament\Resources\OrdersManagement\Orders\Pages\CancelOrder;
use App\Filament\Resources\OrdersManagement\Orders\Pages\CreateManualOrder;
use App\Filament\Resources\OrdersManagement\Orders\Pages\ListCancelledOrders;
use App\Filament\Resources\OrdersManagement\Orders\Pages\ListCompletedOrders;
use App\Filament\Resources\OrdersManagement\Orders\Pages\ListOrders;
use App\Filament\Resources\OrdersManagement\Orders\Pages\ListPendingOrders;
use App\Filament\Resources\OrdersManagement\Orders\Pages\ListProcessingOrders;
use App\Filament\Resources\OrdersManagement\Orders\Pages\ListShippedOrders;
use App\Filament\Resources\OrdersManagement\Orders\Pages\ManualRefundOrder;
use App\Filament\Resources\OrdersManagement\Orders\Pages\ViewOrder;
use App\Filament\Resources\OrdersManagement\Orders\RelationManagers\HistoriesRelationManager;
use App\Filament\Resources\OrdersManagement\Orders\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\OrdersManagement\Orders\RelationManagers\StockMovementsRelationManager;
use App\Filament\Resources\OrdersManagement\Orders\RelationManagers\TransactionsRelationManager;
use App\Filament\Resources\OrdersManagement\Orders\Schemas\OrderInfolist;
use App\Filament\Resources\OrdersManagement\Orders\Tables\OrdersTable;
use App\Models\Order;
use BackedEnum;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

use function Filament\Support\original_request;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::OrdersManagement;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = null;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?string $recordTitleAttribute = 'order_number';

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.orders.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.orders.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.orders.plural_model_label');
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make(__('filament.resources.orders.navigation_label'))
                ->group(NavigationGroup::OrdersManagement)
                ->icon(Heroicon::OutlinedShoppingBag)
                ->isActiveWhen(fn (): bool => original_request()->routeIs(static::getRouteBaseName().'.*'))
                ->sort(1)
                ->url(ListOrders::getUrl()),

            NavigationItem::make(__('filament.orders_management.orders.pending'))
                ->group(NavigationGroup::OrdersManagement)
                ->icon(Heroicon::OutlinedClock)
                ->isActiveWhen(fn (): bool => original_request()->routeIs(ListPendingOrders::getRouteName()))
                ->sort(2)
                ->url(ListPendingOrders::getUrl()),

            NavigationItem::make(__('filament.orders_management.orders.processing'))
                ->group(NavigationGroup::OrdersManagement)
                ->icon(Heroicon::OutlinedArrowPath)
                ->isActiveWhen(fn (): bool => original_request()->routeIs(ListProcessingOrders::getRouteName()))
                ->sort(3)
                ->url(ListProcessingOrders::getUrl()),

            NavigationItem::make(__('filament.orders_management.orders.shipped'))
                ->group(NavigationGroup::OrdersManagement)
                ->icon(Heroicon::OutlinedTruck)
                ->isActiveWhen(fn (): bool => original_request()->routeIs(ListShippedOrders::getRouteName()))
                ->sort(4)
                ->url(ListShippedOrders::getUrl()),

            NavigationItem::make(__('filament.orders_management.orders.completed'))
                ->group(NavigationGroup::OrdersManagement)
                ->icon(Heroicon::OutlinedCheckCircle)
                ->isActiveWhen(fn (): bool => original_request()->routeIs(ListCompletedOrders::getRouteName()))
                ->sort(5)
                ->url(ListCompletedOrders::getUrl()),

            NavigationItem::make(__('filament.orders_management.orders.cancelled'))
                ->group(NavigationGroup::OrdersManagement)
                ->icon(Heroicon::OutlinedXCircle)
                ->isActiveWhen(fn (): bool => original_request()->routeIs(ListCancelledOrders::getRouteName()))
                ->sort(6)
                ->url(ListCancelledOrders::getUrl()),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return OrderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
            TransactionsRelationManager::class,
            HistoriesRelationManager::class,
            StockMovementsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'pending' => ListPendingOrders::route('/pending'),
            'processing' => ListProcessingOrders::route('/processing'),
            'shipped' => ListShippedOrders::route('/shipped'),
            'completed' => ListCompletedOrders::route('/completed'),
            'cancelled' => ListCancelledOrders::route('/cancelled'),
            'create' => CreateManualOrder::route('/create'),
            'view' => ViewOrder::route('/{record}'),
            'cancel' => CancelOrder::route('/{record}/cancel'),
            'manual-refund' => ManualRefundOrder::route('/{record}/manual-refund'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
