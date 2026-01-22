<?php

namespace App\Filament\Resources\OrdersManagement\Orders\Schemas;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.orders.order_data'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('order_number')
                                    ->label(__('validation.attributes.order_number'))
                                    ->weight('bold')
                                    ->copyable(),

                                TextEntry::make('user.name')
                                    ->label(__('validation.attributes.user'))
                                    ->badge()
                                    ->color('info')
                                    ->placeholder('—'),

                                TextEntry::make('status')
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
                                    }),

                                TextEntry::make('payment_status')
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
                                    }),

                                TextEntry::make('payment_method')
                                    ->label(__('validation.attributes.payment_method'))
                                    ->badge()
                                    ->formatStateUsing(fn (PaymentMethod $state): string => $state->getLabel())
                                    ->color('info'),

                                TextEntry::make('tracking_number')
                                    ->label(__('validation.attributes.tracking_number'))
                                    ->placeholder('—'),

                                TextEntry::make('paid_at')
                                    ->label(__('validation.attributes.paid_at'))
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('—'),

                                TextEntry::make('cancelled_at')
                                    ->label(__('validation.attributes.cancelled_at'))
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('—'),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make(__('filament.orders.financial_info'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('subtotal')
                                    ->label(__('validation.attributes.subtotal'))
                                    ->formatStateUsing(fn ($state): ?string => \App\Data\Casts\MoneyCast::format($state))
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('discount_amount')
                                    ->label(__('validation.attributes.discount_amount'))
                                    ->formatStateUsing(fn ($state): ?string => \App\Data\Casts\MoneyCast::format($state))
                                    ->badge()
                                    ->color('warning'),

                                TextEntry::make('tax_amount')
                                    ->label(__('validation.attributes.tax_amount'))
                                    ->formatStateUsing(fn ($state): ?string => \App\Data\Casts\MoneyCast::format($state))
                                    ->badge()
                                    ->color('primary'),

                                TextEntry::make('shipping_cost')
                                    ->label(__('validation.attributes.shipping_cost'))
                                    ->formatStateUsing(fn ($state): ?string => \App\Data\Casts\MoneyCast::format($state))
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('grand_total')
                                    ->label(__('validation.attributes.grand_total'))
                                    ->formatStateUsing(fn ($state): ?string => \App\Data\Casts\MoneyCast::format($state))
                                    ->badge()
                                    ->color('success'),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make(__('filament.orders.addresses'))
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextEntry::make('shipping_address_snapshot')
                                    ->label(__('filament.orders.shipping_address'))
                                    ->getStateUsing(fn (Order $record): string => self::formatAddress($record->shipping_address_snapshot))
                                    ->placeholder('—'),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make(__('filament.orders.additional_info'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('cancellation_reason')
                                    ->label(__('validation.attributes.cancellation_reason'))
                                    ->placeholder('—'),

                                TextEntry::make('notes')
                                    ->label(__('validation.attributes.notes'))
                                    ->placeholder('—')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make(__('validation.attributes.timestamps'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('validation.attributes.created_at'))
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('—'),

                                TextEntry::make('updated_at')
                                    ->label(__('validation.attributes.updated_at'))
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('—'),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }

    private static function formatAddress(?array $address): string
    {
        if (! $address) {
            return '—';
        }

        $parts = array_filter([
            $address['contact_person'] ?? null,
            $address['contact_phone'] ?? null,
            $address['address_line_1'] ?? null,
            $address['address_line_2'] ?? null,
            $address['city'] ?? null,
            $address['state'] ?? null,
            $address['postal_code'] ?? null,
            $address['country'] ?? null,
        ]);

        return $parts === [] ? '—' : implode(' • ', $parts);
    }
}
