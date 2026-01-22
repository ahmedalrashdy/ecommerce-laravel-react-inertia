<?php

namespace App\Filament\Resources\OrdersManagement\Orders\Pages;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Filament\Resources\OrdersManagement\Orders\OrderResource;
use App\Models\Order;
use App\Services\Payments\RefundService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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

class ManualRefundOrder extends Page
{
    use InteractsWithRecord;

    protected static string $resource = OrderResource::class;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.resources.orders-management.orders.pages.manual-refund-order';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->mountCanAuthorizeAccess();

        abort_unless($this->getRecord()->status === OrderStatus::CANCELLED, 403);
        abort_unless(in_array($this->getRecord()->payment_status, [PaymentStatus::PAID, PaymentStatus::REFUND_PENDING], true), 403);

        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $this->form->fill([
            'amount' => $this->remainingRefundableAmount($this->getRecord()),
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
                Section::make(__('filament.orders.manual_refund_title'))
                    ->schema([
                        TextInput::make('amount')
                            ->label(__('validation.attributes.amount'))
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->required(),

                        Textarea::make('note')
                            ->label(__('validation.attributes.comment'))
                            ->rows(3)
                            ->required(),
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
            ->livewireSubmitHandler('processRefund')
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
            Action::make('process_refund')
                ->label(__('actions.process_manual_refund'))
                ->submit('processRefund'),

            Action::make('skip_refund')
                ->label(__('actions.skip_refund'))
                ->color('gray')
                ->url(fn (): string => OrderResource::getUrl('view', ['record' => $this->getRecord()])),
        ];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }

    public function processRefund(): void
    {
        $data = $this->form->getState();
        $amount = (float) $data['amount'];

        if ($amount <= 0) {
            Notification::make()
                ->title(__('filament.orders.refund_amount_invalid'))
                ->danger()
                ->send();

            return;
        }

        try {
            app(RefundService::class)->processManualRefund(
                $this->getRecord(),
                $data['note'],
                $amount
            );

            Notification::make()
                ->title(__('filament.orders.refund_processed'))
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

    private function remainingRefundableAmount(Order $order): float
    {
        $refundedAmount = $order->transactions()
            ->where('type', \App\Enums\TransactionType::Refund)
            ->where('status', \App\Enums\TransactionStatus::Success)
            ->get()
            ->sum(fn ($transaction): float => (float) $transaction->amount);

        $remaining = (float) $order->grand_total - $refundedAmount;

        return max(0, $remaining);
    }
}
