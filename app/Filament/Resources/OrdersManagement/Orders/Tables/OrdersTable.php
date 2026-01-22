<?php

namespace App\Filament\Resources\OrdersManagement\Orders\Tables;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Filament\Resources\OrdersManagement\Orders\Pages\ListCancelledOrders;
use App\Filament\Resources\OrdersManagement\Orders\Pages\ListCompletedOrders;
use App\Filament\Resources\OrdersManagement\Orders\Pages\ListPendingOrders;
use App\Filament\Resources\OrdersManagement\Orders\Pages\ListProcessingOrders;
use App\Filament\Resources\OrdersManagement\Orders\Pages\ListShippedOrders;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

use function Filament\Support\original_request;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label(__('validation.attributes.order_number'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('user.name')
                    ->label(__('validation.attributes.user'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label(__('validation.attributes.status'))
                    ->badge()
                    ->formatStateUsing(fn (OrderStatus $state): string => $state->getLabel())
                    ->color(fn (OrderStatus $state): string => match ($state) {
                        OrderStatus::PENDING => 'warning',
                        OrderStatus::PROCESSING => 'primary',
                        OrderStatus::SHIPPED => 'primary',
                        OrderStatus::DELIVERED => 'success',
                        OrderStatus::CANCELLED => 'danger',
                        OrderStatus::RETURNED => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('payment_status')
                    ->label(__('validation.attributes.payment_status'))
                    ->badge()
                    ->formatStateUsing(fn (PaymentStatus $state): string => $state->getLabel())
                    ->color(fn (PaymentStatus $state): string => match ($state) {
                        PaymentStatus::PENDING => 'warning',
                        PaymentStatus::PAID => 'success',
                        PaymentStatus::FAILED => 'danger',
                        PaymentStatus::REFUNDED => 'gray',
                        PaymentStatus::REFUND_PENDING => 'warning',
                        PaymentStatus::PARTIALLY_REFUNDED => 'info',
                    })
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label(__('validation.attributes.payment_method'))
                    ->badge()
                    ->formatStateUsing(fn (PaymentMethod $state): string => $state->getLabel())
                    ->color('info')
                    ->sortable(),

                TextColumn::make('grand_total')
                    ->label(__('validation.attributes.grand_total'))
                    ->formatStateUsing(fn ($state): ?string => \App\Data\Casts\MoneyCast::format($state))
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('created_at')
                    ->label(__('validation.attributes.created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters(self::filtersForCurrentRoute())
            ->recordActions([
                ViewAction::make()
                    ->label(__('actions.view')),
            ])
            ->toolbarActions([])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->with('user'));
    }

    public static function filters(): array
    {

        return [
            SelectFilter::make('status')
                ->label(__('validation.attributes.status'))
                ->options(OrderStatus::class)
                ->native(false),

            SelectFilter::make('payment_status')
                ->label(__('validation.attributes.payment_status'))
                ->options(PaymentStatus::class)
                ->native(false),

            SelectFilter::make('payment_method')
                ->label(__('validation.attributes.payment_method'))
                ->options(PaymentMethod::class)
                ->native(false),

            SelectFilter::make('user_id')
                ->label(__('validation.attributes.user'))
                ->relationship('user', 'name')
                ->preload()
                ->multiple(),

            Filter::make('created_at')
                ->form([
                    \Filament\Forms\Components\DatePicker::make('created_from')
                        ->label(__('filament.filters.created_from')),
                    \Filament\Forms\Components\DatePicker::make('created_until')
                        ->label(__('filament.filters.created_until')),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['created_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['created_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                }),
        ];
    }

    public static function filtersForCurrentRoute(): array
    {
        $filters = self::filters();

        if (
            original_request()->routeIs(
                ListPendingOrders::getRouteName(),
                ListProcessingOrders::getRouteName(),
                ListShippedOrders::getRouteName(),
                ListCompletedOrders::getRouteName(),
                ListCancelledOrders::getRouteName(),
            )
        ) {
            $filters = self::filtersWithout($filters, ['status']);
        }

        return $filters;
    }

    /**
     * @param  array<BaseFilter>  $filters
     * @param  array<string>  $names
     * @return array<BaseFilter>
     */
    private static function filtersWithout(array $filters, array $names): array
    {
        return array_values(array_filter(
            $filters,
            function (BaseFilter $filter) use ($names): bool {
                return ! in_array($filter->getName(), $names, true);
            },
        ));
    }
}
