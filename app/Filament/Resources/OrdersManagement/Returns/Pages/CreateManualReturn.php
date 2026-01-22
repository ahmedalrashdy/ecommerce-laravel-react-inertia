<?php

namespace App\Filament\Resources\OrdersManagement\Returns\Pages;

use App\Data\Returns\AdminReturnCreationData;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\ReturnStatus;
use App\Filament\Resources\OrdersManagement\Returns\ReturnOrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Returns\AdminReturnCreationService;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Throwable;

class CreateManualReturn extends Page
{
    protected static string $resource = ReturnOrderResource::class;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.resources.orders-management.returns.pages.create-manual-return';

    /**
     * @var array<string, mixed> | null
     */
    public array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'return_scope' => 'full',
            'status' => ReturnStatus::REQUESTED->value,
            'items' => [],
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
                Section::make(__('filament.returns.manual_return_title'))
                    ->schema([
                        Select::make('order_id')
                            ->label(__('validation.attributes.order_number'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->options(fn (): array => $this->orderOptions())
                            ->live()
                            ->helperText(__('filament.returns.delivered_only_helper'))
                            ->afterStateUpdated(function (Get $get, Set $set): void {
                                $set('items', []);
                            })
                            ->rule(function () {
                                return function (string $attribute, mixed $value, \Closure $fail): void {
                                    if (! $value) {
                                        return;
                                    }

                                    $order = Order::find($value);
                                    if ($order && $order->type === OrderType::RETURN_SHIPMENT) {
                                        $fail(__('validation.return_order_reshipment_not_allowed'));
                                    }
                                };
                            }),

                        Radio::make('return_scope')
                            ->label(__('filament.returns.return_scope_label'))
                            ->options([
                                'full' => __('filament.returns.return_scope_full'),
                                'partial' => __('filament.returns.return_scope_partial'),
                            ])
                            ->default('full')
                            ->required()
                            ->live(),

                        Select::make('status')
                            ->label(__('validation.attributes.status'))
                            ->options([
                                ReturnStatus::REQUESTED->value => ReturnStatus::REQUESTED->getLabel(),
                                ReturnStatus::APPROVED->value => ReturnStatus::APPROVED->getLabel(),
                            ])
                            ->required()
                            ->native(false),

                        Textarea::make('reason')
                            ->label(__('validation.attributes.reason'))
                            ->rows(3)
                            ->required(fn (Get $get): bool => $get('return_scope') === 'full'),

                        Section::make(__('filament.returns.full_return_items'))
                            ->schema([
                                Placeholder::make('full_return_items_table')
                                    ->content(fn (Get $get): HtmlString => $this->fullReturnItemsTable($get('order_id'))),
                            ])
                            ->visible(fn (Get $get): bool => $get('return_scope') === 'full' && filled($get('order_id'))),

                        Repeater::make('items')
                            ->label(__('filament.returns.items'))
                            ->schema([
                                Select::make('order_item_id')
                                    ->label(__('filament.returns.return_item'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->options(fn (Get $get): array => $this->orderItemOptions($get('../../order_id')))
                                    ->disabled(fn (Get $get): bool => blank($get('../../order_id')))
                                    ->live()
                                    ->afterStateUpdated(function (?int $state, Set $set): void {
                                        $quantity = $state ? OrderItem::find($state)?->quantity : null;
                                        if ($quantity) {
                                            $set('quantity', $quantity);
                                        }
                                    }),

                                TextInput::make('quantity')
                                    ->label(__('validation.attributes.quantity'))
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(function (Get $get): ?int {
                                        $itemId = $get('order_item_id');
                                        if (! $itemId) {
                                            return null;
                                        }

                                        return OrderItem::find($itemId)?->quantity;
                                    })
                                    ->required()
                                    ->rule(function (Get $get) {
                                        return function (string $attribute, $value, \Closure $fail) use ($get): void {
                                            $itemId = $get('order_item_id');
                                            if (! $itemId) {
                                                return;
                                            }

                                            $orderItem = OrderItem::find($itemId);
                                            if (! $orderItem) {
                                                return;
                                            }

                                            $quantity = (int) $value;
                                            if ($quantity <= 0 || $quantity > $orderItem->quantity) {
                                                $fail(__('validation.insufficient_stock_for_variant', [
                                                    'sku' => $orderItem->product_sku,
                                                    'available' => $orderItem->quantity,
                                                ]));
                                            }
                                        };
                                    }),

                                TextInput::make('reason')
                                    ->label(__('validation.attributes.reason'))
                                    ->required(),
                            ])
                            ->columns(3)
                            ->minItems(fn (Get $get): int => $get('return_scope') === 'partial' ? 1 : 0)
                            ->defaultItems(0)
                            ->required(fn (Get $get): bool => $get('return_scope') === 'partial')
                            ->visible(fn (Get $get): bool => $get('return_scope') === 'partial')
                            ->rule(function (Repeater $component): \Closure {
                                return function (string $attribute, mixed $value, \Closure $fail) use ($component): void {
                                    $data = $component->getLivewire()->data ?? [];
                                    if (($data['return_scope'] ?? 'full') !== 'partial') {
                                        return;
                                    }

                                    $items = $component->getState() ?? [];
                                    $grouped = collect($items)
                                        ->filter(fn (array $item): bool => ! empty($item['order_item_id']))
                                        ->groupBy(fn (array $item): int => (int) $item['order_item_id']);

                                    foreach ($grouped as $orderItemId => $groupItems) {
                                        $orderItem = OrderItem::find($orderItemId);
                                        if (! $orderItem) {
                                            continue;
                                        }

                                        $requested = $groupItems->sum(fn (array $item): int => (int) ($item['quantity'] ?? 0));
                                        if ($requested > $orderItem->quantity) {
                                            $fail(__('validation.return_items_quantity_exceeds', [
                                                'sku' => $orderItem->product_sku,
                                                'available' => $orderItem->quantity,
                                                'requested' => $requested,
                                            ]));
                                            break;
                                        }
                                    }
                                };
                            }),
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
            ->livewireSubmitHandler('create')
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
            Action::make('create')
                ->label(__('actions.create'))
                ->submit('create'),
        ];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }

    public function create(): void
    {
        $data = $this->form->getState();
        $returnScope = $data['return_scope'] ?? 'full';
        $reason = isset($data['reason']) ? (string) $data['reason'] : null;

        try {
            $items = collect($data['items'] ?? []);
            if ($returnScope === 'full') {
                $items = $this->buildFullReturnItems((int) $data['order_id'], $reason);
            }

            $payload = new AdminReturnCreationData(
                orderId: (int) $data['order_id'],
                status: ReturnStatus::from($data['status']),
                reason: $reason,
                items: $items,
            );

            $returnOrder = app(AdminReturnCreationService::class)->create($payload, auth()->user());

            Notification::make()
                ->title(__('filament.returns.manual_return_created'))
                ->success()
                ->send();

            $this->redirect(ReturnOrderResource::getUrl('view', ['record' => $returnOrder]));
        } catch (Throwable $exception) {
            Notification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();
        }
    }

    private function orderOptions(): array
    {
        return Order::query()
            ->where('status', OrderStatus::DELIVERED)
            ->orderByDesc('id')
            ->limit(100)
            ->get()
            ->mapWithKeys(fn (Order $order): array => [
                $order->id => $order->order_number,
            ])
            ->all();
    }

    private function orderItemOptions(?int $orderId): array
    {
        if (! $orderId) {
            return [];
        }

        return OrderItem::query()
            ->where('order_id', $orderId)
            ->get()
            ->mapWithKeys(fn (OrderItem $item): array => [
                $item->id => trim($item->product_name.' - '.$item->product_sku),
            ])
            ->all();
    }

    private function buildFullReturnItems(int $orderId, ?string $reason): \Illuminate\Support\Collection
    {
        return OrderItem::query()
            ->where('order_id', $orderId)
            ->get()
            ->map(fn (OrderItem $item): array => [
                'order_item_id' => $item->id,
                'quantity' => $item->quantity,
                'reason' => $reason ?? '',
            ]);
    }

    private function fullReturnItemsTable(?int $orderId): HtmlString
    {
        if (! $orderId) {
            return new HtmlString('<div class="text-sm text-gray-500">'.e(__('filament.returns.full_return_items_empty')).'</div>');
        }

        $rows = OrderItem::query()
            ->where('order_id', $orderId)
            ->get()
            ->map(fn (OrderItem $item): string => sprintf(
                '<tr class="border-t border-gray-200 dark:border-gray-700">'.
                    '<td class="px-3 py-2">%s</td>'.
                    '<td class="px-3 py-2">%s</td>'.
                    '<td class="px-3 py-2 text-center">%d</td>'.
                '</tr>',
                e($item->product_name ?? '-'),
                e($item->product_sku ?? '-'),
                $item->quantity
            ))
            ->all();

        if ($rows === []) {
            return new HtmlString('<div class="text-sm text-gray-500">'.e(__('filament.returns.full_return_items_empty')).'</div>');
        }

        $table = sprintf(
            '<div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">'.
                '<table class="min-w-full text-sm">'.
                    '<thead class="bg-gray-50 dark:bg-gray-800/60">'.
                        '<tr>'.
                            '<th class="px-3 py-2 text-start font-medium text-gray-700 dark:text-gray-200">%s</th>'.
                            '<th class="px-3 py-2 text-start font-medium text-gray-700 dark:text-gray-200">%s</th>'.
                            '<th class="px-3 py-2 text-center font-medium text-gray-700 dark:text-gray-200">%s</th>'.
                        '</tr>'.
                    '</thead>'.
                    '<tbody>%s</tbody>'.
                '</table>'.
            '</div>',
            e(__('filament.returns.full_return_items_product')),
            e(__('filament.returns.full_return_items_sku')),
            e(__('filament.returns.full_return_items_quantity')),
            implode('', $rows)
        );

        return new HtmlString($table);
    }
}
