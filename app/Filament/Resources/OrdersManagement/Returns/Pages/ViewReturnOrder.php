<?php

namespace App\Filament\Resources\OrdersManagement\Returns\Pages;

use App\Enums\ReturnStatus;
use App\Events\Returns\ReturnApproved;
use App\Events\Returns\ReturnReceived;
use App\Events\Returns\ReturnShippedBack;
use App\Filament\Resources\OrdersManagement\Returns\ReturnOrderResource;
use App\Models\ReturnOrder;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Throwable;

class ViewReturnOrder extends ViewRecord
{
    protected static string $resource = ReturnOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve_return')
                ->label(__('actions.approve_return'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (ReturnOrder $record): bool => $record->status === ReturnStatus::REQUESTED)
                ->requiresConfirmation()
                ->form([
                    Checkbox::make('notify_customer')
                        ->label(__('filament.orders.notify_customer'))
                        ->default(true),
                ])
                ->action(function (ReturnOrder $record, array $data): void {
                    $this->handleTransition(
                        $record,
                        ReturnStatus::APPROVED,
                        __('filament.returns.history_approved'),
                        notifyCustomer: (bool) ($data['notify_customer'] ?? true)
                    );
                }),

            Action::make('mark_shipped_back')
                ->label(__('actions.mark_shipped_back'))
                ->icon('heroicon-o-truck')
                ->color('primary')
                ->visible(fn (ReturnOrder $record): bool => $record->status === ReturnStatus::APPROVED)
                ->form([
                    TextInput::make('tracking_number')
                        ->label(__('validation.attributes.tracking_number'))
                        ->required()
                        ->maxLength(255),
                    Checkbox::make('notify_customer')
                        ->label(__('filament.orders.notify_customer'))
                        ->default(true),
                ])
                ->action(function (ReturnOrder $record, array $data): void {
                    $this->handleTransition(
                        $record,
                        ReturnStatus::SHIPPED_BACK,
                        __('filament.returns.history_shipped_back'),
                        ['tracking_number' => $data['tracking_number']],
                        (bool) ($data['notify_customer'] ?? true)
                    );
                }),

            Action::make('mark_received')
                ->label(__('actions.mark_received'))
                ->icon('heroicon-o-inbox')
                ->color('primary')
                ->visible(fn (ReturnOrder $record): bool => $record->status === ReturnStatus::SHIPPED_BACK)
                ->requiresConfirmation()
                ->form([
                    Checkbox::make('notify_customer')
                        ->label(__('filament.orders.notify_customer'))
                        ->default(true),
                ])
                ->action(function (ReturnOrder $record, array $data): void {
                    $this->handleTransition(
                        $record,
                        ReturnStatus::RECEIVED,
                        __('filament.returns.history_received'),
                        notifyCustomer: (bool) ($data['notify_customer'] ?? true)
                    );
                }),

            Action::make('inspect_return')
                ->label(__('actions.inspect_return'))
                ->icon('heroicon-o-clipboard-document-check')
                ->color('warning')
                ->visible(fn (ReturnOrder $record): bool => $record->status === ReturnStatus::RECEIVED)
                ->url(fn (ReturnOrder $record): string => ReturnOrderResource::getUrl('inspect', ['record' => $record])),

            Action::make('reject_return')
                ->label(__('actions.reject_return'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (ReturnOrder $record): bool => in_array(
                    $record->status,
                    [ReturnStatus::REQUESTED, ReturnStatus::APPROVED],
                    true
                ))
                ->form([
                    Textarea::make('reason')
                        ->label(__('validation.attributes.reason'))
                        ->rows(3)
                        ->required(),
                ])
                ->action(function (ReturnOrder $record, array $data): void {
                    $comment = __('filament.returns.history_rejected', ['reason' => $data['reason']]);

                    $this->handleTransition(
                        $record,
                        ReturnStatus::REJECTED,
                        $comment,
                        ['admin_notes' => $data['reason']]
                    );
                }),
        ];
    }

    private function handleTransition(
        ReturnOrder $returnOrder,
        ReturnStatus $status,
        string $comment,
        array $attributes = [],
        bool $notifyCustomer = true
    ): void {
        try {
            $returnOrder->update(array_merge($attributes, [
                'status' => $status,
            ]));

            $this->logHistory($returnOrder, $status, $comment);

            if ($notifyCustomer && $status === ReturnStatus::APPROVED) {
                event(new ReturnApproved($returnOrder));
            }

            if ($notifyCustomer && $status === ReturnStatus::SHIPPED_BACK) {
                event(new ReturnShippedBack($returnOrder));
            }

            if ($notifyCustomer && $status === ReturnStatus::RECEIVED) {
                event(new ReturnReceived($returnOrder));
            }

            Notification::make()
                ->title(__('filament.returns.status_changed', ['status' => $status->getLabel()]))
                ->success()
                ->send();
        } catch (Throwable $exception) {
            Notification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();
        }
    }

    private function logHistory(ReturnOrder $returnOrder, ReturnStatus $status, string $comment): void
    {
        $actor = auth()->user();

        $returnOrder->history()->create([
            'status' => $status,
            'comment' => $comment,
            'actor_type' => $actor?->getMorphClass(),
            'actor_id' => $actor?->getKey(),
        ]);
    }
}
