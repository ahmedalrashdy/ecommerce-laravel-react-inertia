<?php

namespace App\Filament\Resources\OrdersManagement\Orders\Schemas;

use App\Enums\PaymentMethod;
use App\Enums\TransactionStatus;
use App\Models\ProductVariant;
use App\Models\UserAddress;
use App\Services\Orders\ManualOrderService;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use RuntimeException;

class ManualOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('manual-order')
                    ->tabs([
                        Tab::make('manual-order-details')
                            ->label(__('filament.orders.manual_order'))
                            ->schema([
                                Section::make(__('filament.orders.manual_order'))
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Select::make('user_id')
                                                    ->label(__('validation.attributes.user'))
                                                    ->relationship('user', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(function (?int $state, Set $set, Get $get): void {
                                                        $set('shipping_address_id', null);
                                                        $set('shipping_address', []);

                                                        if (! $state) {
                                                            return;
                                                        }

                                                        $defaultShipping = ManualOrderService::defaultShippingAddress($state);
                                                        if ($defaultShipping) {
                                                            $set('shipping_address_id', $defaultShipping->id);
                                                            self::fillAddress($defaultShipping, $set, 'shipping_address');
                                                        }
                                                    }),

                                                Textarea::make('notes')
                                                    ->label(__('validation.attributes.notes'))
                                                    ->rows(3)
                                                    ->columnSpanFull(),
                                            ]),
                                    ])
                                    ->columns(3),

                                Section::make(__('filament.orders.addresses'))
                                    ->schema([
                                        Select::make('shipping_address_id')
                                            ->label(__('validation.attributes.shipping_address'))
                                            ->options(fn (Get $get): array => ManualOrderService::addressOptions($get('user_id')))
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->disabled(fn (Get $get): bool => ! $get('user_id'))
                                            ->required()
                                            ->afterStateUpdated(function (?int $state, Set $set): void {
                                                $address = $state ? UserAddress::find($state) : null;
                                                self::fillAddress($address, $set, 'shipping_address');
                                            })
                                            ->createOptionForm(self::addressCreateForm())
                                            ->createOptionUsing(function (array $data, Select $component): int {
                                                $userId = $component->getLivewire()->data['user_id'] ?? null;

                                                if (! $userId) {
                                                    throw new RuntimeException(__('validation.manual_order_user_required_for_address'));
                                                }

                                                return UserAddress::create(array_merge($data, [
                                                    'user_id' => $userId,
                                                ]))->getKey();
                                            }),
                                    ]),
                            ]),

                        Tab::make('manual-order-items')
                            ->label(__('filament.orders.order_items'))
                            ->schema([
                                Section::make(__('filament.orders.order_items'))
                                    ->schema([
                                        Repeater::make('items')
                                            ->schema([
                                                Select::make('product_variant_id')
                                                    ->label(__('validation.attributes.product_variant'))
                                                    ->options(self::variantOptions())
                                                    ->searchable()
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(function (?int $state, Set $set, Get $get): void {
                                                        $variant = $state ? ProductVariant::find($state) : null;
                                                        if (! $variant) {
                                                            return;
                                                        }

                                                        $set('unit_price', (float) $variant->price);
                                                        if (! $get('quantity')) {
                                                            $set('quantity', 1);
                                                        }
                                                        self::updateTransactionAmount($set, $get);
                                                    }),

                                                TextInput::make('unit_price')
                                                    ->label(__('validation.attributes.price'))
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->live()
                                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::updateTransactionAmount($set, $get))
                                                    ->required(),

                                                TextInput::make('discount_amount')
                                                    ->label(__('validation.attributes.discount_amount'))
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->default(0)
                                                    ->live()
                                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::updateTransactionAmount($set, $get)),

                                                TextInput::make('quantity')
                                                    ->label(__('validation.attributes.quantity'))
                                                    ->numeric()
                                                    ->minValue(1)
                                                    ->default(1)
                                                    ->live()
                                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::updateTransactionAmount($set, $get))
                                                    ->rule(function (Get $get) {
                                                        return function (string $attribute, $value, \Closure $fail) use ($get): void {
                                                            $variantId = $get('product_variant_id');
                                                            if (! $variantId) {
                                                                return;
                                                            }

                                                            $variant = ProductVariant::find($variantId);
                                                            if (! $variant) {
                                                                return;
                                                            }

                                                            $quantity = (int) $value;
                                                            if ($quantity <= 0 || $variant->quantity < $quantity) {
                                                                $fail(__('validation.insufficient_stock_for_variant', [
                                                                    'sku' => $variant->sku,
                                                                    'available' => $variant->quantity,
                                                                ]));
                                                            }
                                                        };
                                                    })
                                                    ->required(),
                                            ])
                                            ->columns(4)
                                            ->minItems(1)
                                            ->defaultItems(0)
                                            ->live()
                                            ->afterStateUpdated(fn (Set $set, Get $get) => self::updateTransactionAmount($set, $get)),

                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('discount_amount')
                                                    ->label(__('validation.attributes.discount_amount'))
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->default(0)
                                                    ->live()
                                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::updateTransactionAmount($set, $get)),

                                                TextInput::make('tax_amount')
                                                    ->label(__('validation.attributes.tax_amount'))
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->default(0)
                                                    ->live()
                                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::updateTransactionAmount($set, $get)),

                                                TextInput::make('shipping_cost')
                                                    ->label(__('validation.attributes.shipping_cost'))
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->default(0)
                                                    ->live()
                                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::updateTransactionAmount($set, $get)),
                                            ]),

                                        Section::make(__('filament.orders.financial_summary'))
                                            ->schema([
                                                Grid::make(2)
                                                    ->schema([
                                                        Placeholder::make('summary_subtotal')
                                                            ->label(__('validation.attributes.subtotal'))
                                                            ->content(fn (Get $get): string => ManualOrderService::formatMoney(
                                                                ManualOrderService::calculateSubtotal($get('items') ?? [])
                                                            )),

                                                        Placeholder::make('summary_grand_total')
                                                            ->label(__('validation.attributes.grand_total'))
                                                            ->content(fn (Get $get): string => ManualOrderService::formatMoney(
                                                                ManualOrderService::calculateGrandTotal(
                                                                    $get('items') ?? [],
                                                                    (float) ($get('discount_amount') ?? 0),
                                                                    (float) ($get('tax_amount') ?? 0),
                                                                    (float) ($get('shipping_cost') ?? 0)
                                                                )
                                                            )),
                                                    ]),
                                            ]),
                                    ]),
                            ]),

                        Tab::make('manual-order-transactions')
                            ->label(__('filament.orders.transactions'))
                            ->schema([
                                Section::make(__('filament.orders.transactions'))
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Hidden::make('transaction.payment_method')
                                                    ->default(PaymentMethod::BANK_TRANSFER),

                                                Hidden::make('transaction.status')
                                                    ->default(TransactionStatus::Success),

                                                TextInput::make('transaction.amount')
                                                    ->label(__('validation.attributes.amount'))
                                                    ->numeric()
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->afterStateHydrated(fn (Set $set, Get $get) => self::updateTransactionAmount($set, $get)),

                                                TextInput::make('transaction.transaction_ref')
                                                    ->label(__('validation.attributes.transaction_ref'))
                                                    ->maxLength(255)
                                                    ->required(),

                                                Textarea::make('transaction.note')
                                                    ->label(__('validation.attributes.comment'))
                                                    ->rows(3)
                                                    ->columnSpanFull(),
                                            ]),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    private static function addressCreateForm(): array
    {
        return [
            TextInput::make('contact_person')
                ->label(__('validation.attributes.contact_person'))
                ->required(),
            TextInput::make('contact_phone')
                ->label(__('validation.attributes.contact_phone'))
                ->required(),
            TextInput::make('address_line_1')
                ->label(__('validation.attributes.address_line_1'))
                ->required(),
            TextInput::make('address_line_2')
                ->label(__('validation.attributes.address_line_2')),
            TextInput::make('city')
                ->label(__('validation.attributes.city')),
            TextInput::make('state')
                ->label(__('validation.attributes.state')),
            TextInput::make('postal_code')
                ->label(__('validation.attributes.postal_code')),
            TextInput::make('country')
                ->label(__('validation.attributes.country')),
            Toggle::make('is_default_shipping')
                ->label(__('validation.attributes.is_default_shipping'))
                ->default(false),
        ];
    }

    private static function fillAddress(?UserAddress $address, Set $set, string $prefix): void
    {
        if (! $address) {
            $set("{$prefix}.contact_person", null);
            $set("{$prefix}.contact_phone", null);
            $set("{$prefix}.address_line_1", null);
            $set("{$prefix}.address_line_2", null);
            $set("{$prefix}.city", null);
            $set("{$prefix}.state", null);
            $set("{$prefix}.postal_code", null);
            $set("{$prefix}.country", null);

            return;
        }

        $set("{$prefix}.contact_person", $address->contact_person);
        $set("{$prefix}.contact_phone", $address->contact_phone);
        $set("{$prefix}.address_line_1", $address->address_line_1);
        $set("{$prefix}.address_line_2", $address->address_line_2);
        $set("{$prefix}.city", $address->city);
        $set("{$prefix}.state", $address->state);
        $set("{$prefix}.postal_code", $address->postal_code);
        $set("{$prefix}.country", $address->country);
    }

    private static function updateTransactionAmount(Set $set, Get $get): void
    {
        $total = ManualOrderService::calculateGrandTotal(
            $get('items') ?? [],
            (float) ($get('discount_amount') ?? 0),
            (float) ($get('tax_amount') ?? 0),
            (float) ($get('shipping_cost') ?? 0)
        );

        $set('transaction.amount', (float) $total->getAmount()->__toString());
    }

    private static function variantOptions(): array
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
