<?php

namespace App\Filament\Resources\OrdersManagement\Returns\Pages;

use App\Enums\ItemCondition;
use App\Enums\ReturnResolution;
use App\Enums\ReturnStatus;
use App\Filament\Resources\OrdersManagement\Returns\ReturnOrderResource;
use App\Models\ProductVariant;
use App\Models\ReturnItem;
use App\Services\Returns\ReturnInspectionWorkflowService;
use Brick\Money\Money;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
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
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Throwable;

class InspectReturnOrder extends Page
{
    use InteractsWithRecord;

    protected static string $resource = ReturnOrderResource::class;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.resources.orders-management.returns.pages.inspect-return-order';

    /**
     * @var array<string, mixed> | null
     */
    public array $data = [];

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->mountCanAuthorizeAccess();

        abort_unless($this->getRecord()->status === ReturnStatus::RECEIVED, 403);

        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $items = $this->getRecord()->items()
            ->with('orderItem.productVariant.product')
            ->get()
            ->map(fn (ReturnItem $item): array => [
                'return_item_id' => $item->id,
                'product_name' => $item->orderItem->product_name,
                'product_sku' => $item->orderItem->product_sku,
                'return_quantity' => $item->quantity,
                'unit_price' => (float) $item->orderItem->price,
                'product_variant_id' => $item->orderItem->product_variant_id,
                'inspections' => [
                    [
                        'condition' => ItemCondition::SEALED->value,
                        'resolution' => ReturnResolution::REFUND->value,
                        'quantity' => $item->quantity,
                        'refund_amount' => (float) $item->orderItem->price * $item->quantity,
                        'note' => null,
                    ],
                ],
            ])
            ->all();

        $this->form->fill([
            'items' => $items,
            'replacement_items' => [],
            'transaction_reference' => null,
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
                Wizard::make([
                    Step::make(__('filament.returns.inspection_step'))
                        ->schema([
                            Repeater::make('items')
                                ->schema([
                                    Hidden::make('return_item_id'),
                                    Hidden::make('product_name'),
                                    Hidden::make('product_sku'),
                                    Hidden::make('unit_price'),
                                    Hidden::make('product_variant_id'),
                                    Hidden::make('return_quantity'),

                                    Placeholder::make('product')
                                        ->label(__('validation.attributes.product'))
                                        ->content(fn (Get $get): string => (string) $get('product_name')),

                                    Placeholder::make('sku')
                                        ->label(__('validation.attributes.sku'))
                                        ->content(fn (Get $get): string => (string) $get('product_sku')),

                                    Placeholder::make('return_quantity_display')
                                        ->label(__('validation.attributes.quantity'))
                                        ->content(fn (Get $get): string => (string) $get('return_quantity')),

                                    Repeater::make('inspections')
                                        ->schema([
                                            Select::make('condition')
                                                ->label(__('validation.attributes.inspection_condition'))
                                                ->options($this->itemConditionOptions())
                                                ->required()
                                                ->live()
                                                ->afterStateUpdated(function ($state, Set $set): void {
                                                    if ($state === ItemCondition::WRONG_ITEM->value) {
                                                        $set('resolution', ReturnResolution::REJECT->value);
                                                    }
                                                }),

                                            Select::make('resolution')
                                                ->label(__('validation.attributes.resolution'))
                                                ->options($this->returnResolutionOptions())
                                                ->required()
                                                ->live()
                                                ->afterStateUpdated(function ($state, Set $set, Get $get): void {
                                                    $resolution = (int) $state;
                                                    $shouldSetRefund = in_array($resolution, [
                                                        ReturnResolution::REFUND->value,
                                                        ReturnResolution::REPLACEMENT->value,
                                                    ], true);

                                                    if ($shouldSetRefund && blank($get('refund_amount'))) {
                                                        $unitPrice = (float) $get('../../unit_price');
                                                        $quantity = (int) $get('quantity');
                                                        $set('refund_amount', $unitPrice * $quantity);
                                                    }

                                                    if (! $shouldSetRefund) {
                                                        $set('refund_amount', null);
                                                    }
                                                }),

                                            TextInput::make('quantity')
                                                ->label(__('validation.attributes.quantity'))
                                                ->numeric()
                                                ->minValue(1)
                                                ->maxValue(fn (Get $get): int => $this->maxQuantityForInspection($get))
                                                ->required()
                                                ->live()
                                                ->afterStateUpdated(function (Set $set, Get $get): void {
                                                    $resolution = (int) $get('resolution');
                                                    $shouldSetRefund = in_array($resolution, [
                                                        ReturnResolution::REFUND->value,
                                                        ReturnResolution::REPLACEMENT->value,
                                                    ], true);

                                                    if ($shouldSetRefund && blank($get('refund_amount'))) {
                                                        $unitPrice = (float) $get('../../unit_price');
                                                        $quantity = (int) $get('quantity');
                                                        $set('refund_amount', $unitPrice * $quantity);
                                                    }
                                                }),

                                            TextInput::make('refund_amount')
                                                ->label(__('validation.attributes.refund_amount'))
                                                ->numeric()
                                                ->minValue(0)
                                                ->visible(fn (Get $get): bool => in_array((int) $get('resolution'), [
                                                    ReturnResolution::REFUND->value,
                                                    ReturnResolution::REPLACEMENT->value,
                                                ], true))
                                                ->required(fn (Get $get): bool => in_array((int) $get('resolution'), [
                                                    ReturnResolution::REFUND->value,
                                                    ReturnResolution::REPLACEMENT->value,
                                                ], true))
                                                ->dehydrated(fn (Get $get): bool => in_array((int) $get('resolution'), [
                                                    ReturnResolution::REFUND->value,
                                                    ReturnResolution::REPLACEMENT->value,
                                                ], true)),

                                            Textarea::make('note')
                                                ->label(__('validation.attributes.inspection_note'))
                                                ->rows(2)
                                                ->columnSpanFull(),
                                        ])
                                        ->columns(4)
                                        ->defaultItems(1)
                                        ->minItems(1)
                                        ->required()
                                        ->live(),
                                    // ->addAction(fn(Action $action): Action => $action
                                    //     ->label(__('actions.add_inspection'))
                                    //     // ->disabled(fn(Repeater $component): bool => $this->remainingQuantityFromComponent($component) <= 0)
                                    //     ->action(function (Repeater $component): void {
                                    //         $this->addInspectionItem($component);
                                    //     })),
                                ])
                                ->columns(2)
                                ->addable(false)
                                ->deletable(false)
                                ->reorderable(false)
                                ->defaultItems(0)
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set): void {
                                    $this->syncDerivedSteps($get, $set);
                                })
                                ->rule(function (Repeater $component): \Closure {
                                    return function (string $attribute, mixed $value, \Closure $fail) use ($component): void {
                                        $items = $component->getState() ?? [];

                                        foreach ($items as $item) {
                                            $returnQuantity = (int) ($item['return_quantity'] ?? 0);
                                            $total = collect($item['inspections'] ?? [])
                                                ->sum(fn (array $inspection): int => (int) ($inspection['quantity'] ?? 0));

                                            if ($returnQuantity !== $total) {
                                                $fail(__('validation.return_inspection_quantity_mismatch', [
                                                    'expected' => $returnQuantity,
                                                ]));
                                                break;
                                            }
                                        }
                                    };
                                }),
                        ]),

                    Step::make(__('filament.returns.replacement_step'))
                        ->schema([
                            Repeater::make('replacement_items')
                                ->schema([
                                    Select::make('product_variant_id')
                                        ->label(__('validation.attributes.product_variant'))
                                        ->options($this->variantOptions())
                                        ->searchable()
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function (?int $state, Set $set): void {
                                            if (! $state) {
                                                return;
                                            }

                                            $variant = ProductVariant::find($state);
                                            if (! $variant) {
                                                return;
                                            }

                                            $set('unit_price', (float) $variant->price);
                                        }),

                                    TextInput::make('quantity')
                                        ->label(__('validation.attributes.quantity'))
                                        ->numeric()
                                        ->live(onBlur: true)
                                        ->minValue(1)
                                        ->required(),

                                    TextInput::make('unit_price')
                                        ->label(__('validation.attributes.price'))
                                        ->numeric()
                                        ->minValue(0)
                                        ->required(),
                                ])
                                ->columns(3)
                                ->defaultItems(0)
                                ->minItems(1)
                                ->required(),
                        ])
                        ->visible(fn (Get $get): bool => $this->hasReplacementItems($get('items'))),

                    Step::make(__('filament.returns.reshipment_step'))
                        ->schema([
                            Section::make(__('filament.returns.reshipment_items'))
                                ->schema([
                                    Placeholder::make('reshipment_items')
                                        ->content(fn (Get $get): HtmlString => $this->reshipmentItemsTable($get('items'))),
                                ]),
                        ])
                        ->visible(fn (Get $get): bool => $this->hasRejectedItems($get('items'))),

                    Step::make(__('filament.returns.transaction_step'))
                        ->schema([
                            Placeholder::make('transaction_summary')
                                ->label(__('filament.returns.transaction_summary'))
                                ->content(fn (Get $get): HtmlString => $this->transactionSummaryTable($get('items'), $get('replacement_items'))),

                            Placeholder::make('settlement_details')
                                ->label(__('filament.returns.settlement_details_title'))
                                ->content(fn (Get $get): HtmlString => $this->settlementDetailsTable($get('transaction_reference'))),

                            TextInput::make('transaction_reference')
                                ->label(__('filament.returns.settlement_reference'))
                                ->required(fn (Get $get): bool => $this->hasFinancialDifference($get('items'), $get('replacement_items')))
                                ->maxLength(255),
                        ])
                        ->visible(fn (Get $get): bool => $this->hasFinancialDifference($get('items'), $get('replacement_items'))),
                ])->submitAction(new HtmlString('')),
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
            ->livewireSubmitHandler('save')
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
            Action::make('save')
                ->label(__('actions.save'))
                ->submit('save')
                ->color('primary'),

            Action::make('back')
                ->label(__('actions.cancel'))
                ->url(fn (): string => ReturnOrderResource::getUrl('view', ['record' => $this->getRecord()])),
        ];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }

    public function save(): void
    {
        $data = $this->form->getState();

        try {
            app(ReturnInspectionWorkflowService::class)->process(
                $this->getRecord(),
                $data,
                auth()->user()
            );

            Notification::make()
                ->title(__('filament.returns.status_changed', ['status' => ReturnStatus::COMPLETED->getLabel()]))
                ->success()
                ->send();

            $this->redirect(ReturnOrderResource::getUrl('view', ['record' => $this->getRecord()]));
        } catch (Throwable $exception) {
            Notification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();
        }
    }

    private function syncDerivedSteps(Get $get, Set $set): void
    {
        $items = $get('items') ?? [];
        $replacementItems = $get('replacement_items') ?? [];

        if ($replacementItems === [] && $this->hasReplacementItems($items)) {
            $set('replacement_items', $this->replacementItemsFromInspections($items));
        }
    }

    private function hasReplacementItems(array $items): bool
    {
        return collect($items)
            ->flatMap(fn (array $item): array => $item['inspections'] ?? [])
            ->contains(fn (array $inspection): bool => (int) ($inspection['resolution'] ?? 0) === ReturnResolution::REPLACEMENT->value);
    }

    private function hasRejectedItems(array $items): bool
    {
        return collect($items)
            ->flatMap(fn (array $item): array => $item['inspections'] ?? [])
            ->contains(fn (array $inspection): bool => (int) ($inspection['resolution'] ?? 0) === ReturnResolution::REJECT->value);
    }

    private function maxQuantityForInspection(Get $get): int
    {
        $returnQuantity = (int) $get('../../return_quantity');
        $inspections = collect($get('../../inspections') ?? []);
        $currentQuantity = (int) $get('quantity');
        $total = $inspections->sum(fn (array $inspection): int => (int) ($inspection['quantity'] ?? 0));
        $remaining = $returnQuantity - $total + $currentQuantity;

        return max(0, $remaining);
    }

    private function remainingQuantityFromComponent(Repeater $component): int
    {
        $statePath = $component->getStatePath();
        $parentPath = Str::beforeLast($statePath, '.inspections');
        $data = $component->getLivewire()->data;
        $returnQuantity = (int) data_get($data, "{$parentPath}.return_quantity", 0);
        $inspections = data_get($data, "{$parentPath}.inspections", []);
        $total = collect($inspections)->sum(fn (array $inspection): int => (int) ($inspection['quantity'] ?? 0));

        return max(0, $returnQuantity - $total);
    }

    private function addInspectionItem(Repeater $component): void
    {
        $statePath = $component->getStatePath();
        $parentPath = Str::beforeLast($statePath, '.inspections');
        $data = $component->getLivewire()->data;
        $returnQuantity = (int) data_get($data, "{$parentPath}.return_quantity", 0);
        $unitPrice = (float) data_get($data, "{$parentPath}.unit_price", 0);
        $state = $component->getState() ?? [];
        $total = collect($state)->sum(fn (array $inspection): int => (int) ($inspection['quantity'] ?? 0));
        $remaining = $returnQuantity - $total;

        if ($remaining <= 0) {
            return;
        }

        $state[(string) Str::uuid()] = [
            'condition' => ItemCondition::SEALED->value,
            'resolution' => ReturnResolution::REFUND->value,
            'quantity' => $remaining,
            'refund_amount' => $unitPrice * $remaining,
            'note' => null,
        ];

        $component->state($state);
    }

    private function hasFinancialDifference(array $items, array $replacementItems): bool
    {
        $refundTotal = $this->refundTotalFromItems($items);
        $replacementTotal = $this->replacementTotalFromItems($replacementItems);

        return $refundTotal->isGreaterThan(Money::zero('USD'))
            || ! $refundTotal->isEqualTo($replacementTotal);
    }

    private function refundTotalFromItems(array $items): Money
    {
        return collect($items)
            ->reduce(function (Money $carry, array $item): Money {
                $unitPrice = Money::of((string) ($item['unit_price'] ?? 0), 'USD');

                $itemTotal = collect($item['inspections'] ?? [])
                    ->filter(fn (array $inspection): bool => (int) ($inspection['resolution'] ?? 0) === ReturnResolution::REFUND->value)
                    ->reduce(function (Money $lineTotal, array $inspection) use ($unitPrice): Money {
                        $refundAmount = $inspection['refund_amount'] ?? null;
                        if ($refundAmount !== null) {
                            return $lineTotal->plus(Money::of((string) $refundAmount, 'USD'));
                        }

                        return $lineTotal->plus($unitPrice->multipliedBy((int) ($inspection['quantity'] ?? 0)));
                    }, Money::zero('USD'));

                return $carry->plus($itemTotal);
            }, Money::zero('USD'));
    }

    private function replacementTotalFromItems(array $replacementItems): Money
    {
        return collect($replacementItems)
            ->reduce(function (Money $carry, array $item): Money {
                $unitPrice = Money::of((string) ($item['unit_price'] ?? 0), 'USD');
                $quantity = (int) ($item['quantity'] ?? 0);

                return $carry->plus($unitPrice->multipliedBy($quantity));
            }, Money::zero('USD'));
    }

    private function replacementItemsFromInspections(array $items): array
    {
        $itemsToReplace = collect($items)
            ->flatMap(function (array $item): array {
                $variantId = $item['product_variant_id'] ?? null;
                $unitPrice = (float) ($item['unit_price'] ?? 0);

                if (! $variantId) {
                    return [];
                }

                return collect($item['inspections'] ?? [])
                    ->filter(fn (array $inspection): bool => (int) ($inspection['resolution'] ?? 0) === ReturnResolution::REPLACEMENT->value)
                    ->map(fn (array $inspection): array => [
                        'product_variant_id' => $variantId,
                        'quantity' => (int) ($inspection['quantity'] ?? 0),
                        'unit_price' => $unitPrice,
                    ])
                    ->all();
            });

        return $itemsToReplace
            ->groupBy('product_variant_id')
            ->map(function (Collection $group): array {
                $first = $group->first();

                return [
                    'product_variant_id' => $first['product_variant_id'],
                    'quantity' => $group->sum('quantity'),
                    'unit_price' => $first['unit_price'],
                ];
            })
            ->values()
            ->all();
    }

    private function reshipmentItemsTable(array $items): HtmlString
    {
        $rows = collect($items)
            ->flatMap(function (array $item): array {
                $productName = $item['product_name'] ?? '-';
                $productSku = $item['product_sku'] ?? '-';

                return collect($item['inspections'] ?? [])
                    ->filter(fn (array $inspection): bool => (int) ($inspection['resolution'] ?? 0) === ReturnResolution::REJECT->value)
                    ->map(fn (array $inspection): string => sprintf(
                        '<tr class="border-t border-gray-200 dark:border-gray-700">'.
                        '<td class="px-3 py-2">%s</td>'.
                        '<td class="px-3 py-2">%s</td>'.
                        '<td class="px-3 py-2 text-center">%d</td>'.
                        '</tr>',
                        e($productName),
                        e($productSku),
                        (int) ($inspection['quantity'] ?? 0)
                    ))
                    ->all();
            })
            ->all();

        if ($rows === []) {
            return new HtmlString('<div class="text-sm text-gray-500">'.e(__('filament.returns.reshipment_items_empty')).'</div>');
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

    private function transactionSummaryTable(array $items, array $replacementItems): HtmlString
    {
        $refundTotal = $this->refundTotalFromItems($items);
        $replacementTotal = $this->replacementTotalFromItems($replacementItems);
        $difference = $replacementTotal->minus($refundTotal);
        $refundDisplay = number_format((float) $refundTotal->getAmount()->__toString(), 2);
        $replacementDisplay = number_format((float) $replacementTotal->getAmount()->__toString(), 2);
        $netAmount = $difference->abs();
        $netDisplay = number_format((float) $netAmount->getAmount()->__toString(), 2);
        $netLabel = $difference->isPositive()
            ? __('filament.returns.net_due_from_customer')
            : __('filament.returns.net_due_to_customer');

        $table = sprintf(
            '<div class="space-y-4">'.
            '<div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">'.
            '<div class="border-b border-gray-200 bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700 dark:border-gray-700 dark:bg-gray-800/60 dark:text-gray-200">%s</div>'.
            '<table class="min-w-full text-sm">'.
            '<tbody>'.
            '<tr class="border-t border-gray-200 dark:border-gray-700">'.
            '<td class="px-3 py-2 font-medium text-gray-700 dark:text-gray-200">%s</td>'.
            '<td class="px-3 py-2 text-end">%s</td>'.
            '</tr>'.
            '<tr class="border-t border-gray-200 dark:border-gray-700">'.
            '<td class="px-3 py-2 font-medium text-gray-700 dark:text-gray-200">%s</td>'.
            '<td class="px-3 py-2 text-end">%s</td>'.
            '</tr>'.
            '</tbody>'.
            '</table>'.
            '</div>'.
            '<div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">'.
            '<div class="border-b border-gray-200 bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700 dark:border-gray-700 dark:bg-gray-800/60 dark:text-gray-200">%s</div>'.
            '<table class="min-w-full text-sm">'.
            '<tbody>'.
            '<tr class="border-t border-gray-200 dark:border-gray-700">'.
            '<td class="px-3 py-2 font-medium text-gray-700 dark:text-gray-200">%s</td>'.
            '<td class="px-3 py-2 text-end">%s</td>'.
            '</tr>'.
            '</tbody>'.
            '</table>'.
            '</div>',
            e(__('filament.returns.financial_movements_title')),
            e(__('filament.returns.amount_refunded_to_customer')),
            e($refundDisplay),
            e(__('filament.returns.amount_due_from_customer')),
            e($replacementDisplay),
            e(__('filament.returns.net_result_title')),
            e($difference->isZero() ? __('filament.returns.no_financial_difference') : $netLabel),
            e($difference->isZero() ? __('filament.returns.no_financial_difference_value') : $netDisplay)
        );

        return new HtmlString($table);
    }

    private function settlementDetailsTable(?string $reference): HtmlString
    {
        $referenceValue = $reference ?: '—';

        $table = sprintf(
            '<div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">'.
            '<div class="border-b border-gray-200 bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700 dark:border-gray-700 dark:bg-gray-800/60 dark:text-gray-200">%s</div>'.
            '<table class="min-w-full text-sm">'.
            '<tbody>'.
            '<tr class="border-t border-gray-200 dark:border-gray-700">'.
            '<td class="px-3 py-2 font-medium text-gray-700 dark:text-gray-200">%s</td>'.
            '<td class="px-3 py-2 text-end">%s</td>'.
            '</tr>'.
            '<tr class="border-t border-gray-200 dark:border-gray-700">'.
            '<td class="px-3 py-2 font-medium text-gray-700 dark:text-gray-200">%s</td>'.
            '<td class="px-3 py-2 text-end">%s</td>'.
            '</tr>'.
            '</tbody>'.
            '</table>'.
            '</div>',
            e(__('filament.returns.settlement_details_title')),
            e(__('filament.returns.settlement_type_label')),
            e(__('filament.returns.settlement_type_value')),
            e(__('filament.returns.settlement_reference')),
            e($referenceValue)
        );

        return new HtmlString($table);
    }

    private function itemConditionOptions(): array
    {
        return collect(ItemCondition::cases())
            ->mapWithKeys(fn (ItemCondition $status): array => [
                $status->value => $status->getLabel(),
            ])
            ->all();
    }

    private function returnResolutionOptions(): array
    {
        return collect(ReturnResolution::cases())
            ->mapWithKeys(fn (ReturnResolution $status): array => [
                $status->value => $status->getLabel(),
            ])
            ->all();
    }

    private function variantOptions(): array
    {
        return ProductVariant::query()
            ->with('product')
            ->get()
            ->mapWithKeys(fn (ProductVariant $variant): array => [
                $variant->id => $variant->product->name.' - '.$variant->sku,
            ])
            ->all();
    }
}
