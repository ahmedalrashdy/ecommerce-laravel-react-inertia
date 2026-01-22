<?php

namespace App\Filament\Resources\OrdersManagement\Orders\Pages;

use App\Data\Orders\CancelOrderData;
use App\Enums\CancelRefundOption;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Filament\Resources\OrdersManagement\Orders\OrderResource;
use App\Services\Orders\OrderCancellationService;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Throwable;

class CancelOrder extends Page
{
    use InteractsWithRecord;

    protected static string $resource = OrderResource::class;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.resources.orders-management.orders.pages.cancel-order';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->mountCanAuthorizeAccess();

        abort_unless(! in_array($this->getRecord()->status, [OrderStatus::SHIPPED, OrderStatus::DELIVERED, OrderStatus::CANCELLED], true), 403);

        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $this->form->fill([
            'refund_option' => $this->shouldShowRefundOptions()
                ? CancelRefundOption::AUTO->value
                : CancelRefundOption::LATER->value,
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.orders.cancel_order_title'))
                    ->schema([
                        Placeholder::make('cancel_warning')
                            ->label(__('filament.orders.cancel_order_warning_title'))
                            ->content(__('filament.orders.cancel_order_warning_body')),

                        Placeholder::make('order_number')
                            ->label(__('validation.attributes.order_number'))
                            ->content(fn (): string => $this->getRecord()->order_number),

                        Textarea::make('reason')
                            ->label(__('validation.attributes.cancellation_reason'))
                            ->required()
                            ->maxLength(255),

                        Hidden::make('refund_option')
                            ->default(CancelRefundOption::LATER->value)
                            ->visible(fn (): bool => ! $this->shouldShowRefundOptions()),

                        Radio::make('refund_option')
                            ->label(__('filament.orders.refund_option_label'))
                            ->options(CancelRefundOption::options())
                            ->required()
                            ->visible(fn (): bool => $this->shouldShowRefundOptions()),
                    ])
                    ->columns(1),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
            ]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('cancel')
            ->footer([
                Actions::make($this->getFormActions())
                    ->alignment($this->getFormActionsAlignment())
                    ->fullWidth($this->hasFullWidthFormActions())
                    ->sticky($this->areFormActionsSticky())
                    ->key('form-actions'),
            ]);
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('confirm')
                ->label(__('actions.confirm_cancellation'))
                ->submit('cancel')
                ->color('danger'),

            Action::make('back')
                ->label(__('actions.cancel'))
                ->url(fn (): string => OrderResource::getUrl('view', ['record' => $this->getRecord()])),
        ];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }

    public function cancel(): void
    {
        $data = $this->form->getState();

        try {
            $refundOption = CancelRefundOption::from($data['refund_option'] ?? CancelRefundOption::LATER->value);

            $result = app(OrderCancellationService::class)->cancel(
                $this->getRecord(),
                new CancelOrderData(
                    reason: $data['reason'],
                    cancelledBy: auth()->user(),
                    refundOption: $refundOption,
                ),
            );

            if ($refundOption === CancelRefundOption::MANUAL && $result->refundRequired) {
                Notification::make()
                    ->title(__('filament.orders.order_cancelled'))
                    ->success()
                    ->send();

                $this->redirect(OrderResource::getUrl('manual-refund', ['record' => $this->getRecord()]));

                return;
            }

            if ($refundOption === CancelRefundOption::AUTO && $result->refundRequired && ! $result->autoRefundSucceeded) {
                Notification::make()
                    ->title(__('filament.orders.order_cancelled'))
                    ->success()
                    ->send();

                Notification::make()
                    ->title(__('filament.orders.refund_failed'))
                    ->warning()
                    ->send();

                $this->redirect(OrderResource::getUrl('manual-refund', ['record' => $this->getRecord()]));

                return;
            }

            Notification::make()
                ->title(__('filament.orders.order_cancelled'))
                ->success()
                ->send();

            $this->redirect(OrderResource::getUrl('view', ['record' => $this->getRecord()]));
        } catch (Throwable $exception) {
            Notification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();
        }
    }

    private function shouldShowRefundOptions(): bool
    {
        return in_array(
            $this->getRecord()->payment_status,
            [PaymentStatus::PAID, PaymentStatus::REFUND_PENDING, PaymentStatus::PARTIALLY_REFUNDED],
            true
        );
    }
}
