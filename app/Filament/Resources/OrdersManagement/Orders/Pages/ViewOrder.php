<?php

namespace App\Filament\Resources\OrdersManagement\Orders\Pages;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Filament\Resources\OrdersManagement\Orders\OrderResource;
use App\Models\Order;
use App\Services\Orders\OrderStatusService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Throwable;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('mark_processing')
                ->label(__('actions.mark_processing'))
                ->icon('heroicon-o-play')
                ->color('warning')
                ->visible(fn (Order $record): bool => $record->status === OrderStatus::PENDING)
                ->disabled(fn (Order $record): bool => $record->payment_status !== PaymentStatus::PAID)
                ->requiresConfirmation()
                ->action(function (Order $record): void {
                    $this->handleTransition($record, OrderStatus::PROCESSING);
                }),

            Action::make('mark_shipped')
                ->label(__('actions.mark_shipped'))
                ->icon('heroicon-o-truck')
                ->color('primary')
                ->visible(fn (Order $record): bool => $record->status === OrderStatus::PROCESSING)
                ->disabled(fn (Order $record): bool => $record->payment_status !== PaymentStatus::PAID)
                ->form([
                    TextInput::make('tracking_number')
                        ->label(__('validation.attributes.tracking_number'))
                        ->default(fn (Order $record): ?string => $record->tracking_number)
                        ->maxLength(255)
                        ->required(),
                ])
                ->action(function (Order $record, array $data): void {
                    $this->handleTransition(
                        $record,
                        OrderStatus::SHIPPED,
                        attributes: ['tracking_number' => $data['tracking_number']],
                    );
                }),

            Action::make('mark_delivered')
                ->label(__('actions.mark_delivered'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (Order $record): bool => $record->status === OrderStatus::SHIPPED)
                ->disabled(fn (Order $record): bool => $record->payment_status !== PaymentStatus::PAID)
                ->requiresConfirmation()
                ->action(function (Order $record): void {
                    $this->handleTransition($record, OrderStatus::DELIVERED);
                }),

            Action::make('cancel_order')
                ->label(__('actions.cancel'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (Order $record): bool => ! in_array(
                    $record->status,
                    [OrderStatus::SHIPPED, OrderStatus::DELIVERED, OrderStatus::CANCELLED],
                    true
                ))
                ->url(fn (Order $record): string => OrderResource::getUrl('cancel', ['record' => $record])),

            Action::make('manual_refund')
                ->label(__('actions.refund_manual'))
                ->icon('heroicon-o-banknotes')
                ->color('primary')
                ->visible(fn (Order $record): bool => $record->status === OrderStatus::CANCELLED
                    && in_array($record->payment_status, [PaymentStatus::PAID, PaymentStatus::REFUND_PENDING], true))
                ->url(fn (Order $record): string => OrderResource::getUrl('manual-refund', ['record' => $record])),

        ];
    }

    private function handleTransition(Order $order, OrderStatus $status, array $attributes = []): void
    {
        try {
            app(OrderStatusService::class)->transition(
                $order,
                $status,
                comment: __('filament.orders.status_changed', ['status' => $status->getLabel()]),
                actor: auth()->user(),
                attributes: $attributes,
            );

            Notification::make()
                ->title(__('filament.orders.status_changed', ['status' => $status->getLabel()]))
                ->success()
                ->send();
        } catch (Throwable $exception) {
            Notification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();
        }
    }
}
