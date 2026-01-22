<?php

namespace App\Filament\Resources\OrdersManagement\Returns\Schemas;

use App\Enums\RefundMethod;
use App\Enums\ReturnStatus;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReturnOrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.returns.return_data'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('return_number')
                                    ->label(__('validation.attributes.return_number'))
                                    ->weight('bold')
                                    ->copyable(),

                                TextEntry::make('order.order_number')
                                    ->label(__('validation.attributes.order_number'))
                                    ->badge()
                                    ->color('info')
                                    ->placeholder('—'),

                                TextEntry::make('user.name')
                                    ->label(__('validation.attributes.user'))
                                    ->badge()
                                    ->color('info')
                                    ->placeholder('—'),

                                TextEntry::make('status')
                                    ->label(__('validation.attributes.status'))
                                    ->badge()
                                    ->formatStateUsing(fn (ReturnStatus $state): string => $state->getLabel())
                                    ->color(fn (ReturnStatus $state): string => match ($state) {
                                        ReturnStatus::REQUESTED => 'warning',
                                        ReturnStatus::APPROVED => 'info',
                                        ReturnStatus::SHIPPED_BACK => 'primary',
                                        ReturnStatus::RECEIVED => 'primary',
                                        ReturnStatus::INSPECTED => 'warning',
                                        ReturnStatus::COMPLETED => 'success',
                                        ReturnStatus::REJECTED => 'danger',
                                    }),

                                TextEntry::make('reason')
                                    ->label(__('validation.attributes.reason'))
                                    ->placeholder('—')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make(__('filament.returns.financial_info'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('refund_method')
                                    ->label(__('validation.attributes.refund_method'))
                                    ->badge()
                                    ->formatStateUsing(fn (?RefundMethod $state): string => $state?->getLabel() ?? '—')
                                    ->color('info')
                                    ->placeholder('—'),

                                TextEntry::make('refund_amount')
                                    ->label(__('validation.attributes.refund_amount'))
                                    ->formatStateUsing(fn ($state): ?string => \App\Data\Casts\MoneyCast::format($state))
                                    ->badge()
                                    ->color('success'),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make(__('filament.returns.logistics'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('tracking_number')
                                    ->label(__('validation.attributes.tracking_number'))
                                    ->placeholder('—'),

                                TextEntry::make('shipping_label_url')
                                    ->label(__('validation.attributes.shipping_label_url'))
                                    ->url(fn (?string $state): ?string => $state)
                                    ->placeholder('—'),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make(__('filament.returns.inspection'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('inspected_at')
                                    ->label(__('validation.attributes.inspected_at'))
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('—'),

                                TextEntry::make('inspectedBy.name')
                                    ->label(__('validation.attributes.inspected_by'))
                                    ->badge()
                                    ->color('info')
                                    ->placeholder('—'),

                                TextEntry::make('admin_notes')
                                    ->label(__('validation.attributes.admin_notes'))
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
                                    ->dateTime('d/m/Y H:i'),

                                TextEntry::make('updated_at')
                                    ->label(__('validation.attributes.updated_at'))
                                    ->dateTime('d/m/Y H:i'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
