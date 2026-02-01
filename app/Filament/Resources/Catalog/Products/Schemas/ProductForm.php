<?php

namespace App\Filament\Resources\Catalog\Products\Schemas;

use App\Enums\ProductStatus;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
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
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Product Data')
                    ->tabs([
                        self::basicInfo(),
                        self::specifications(),
                        self::firstVariant(),
                        self::statusAndVisibility(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function basicInfo()
    {
        return Tab::make('General Info')
            ->label(__('filament.products.basic_info'))
            ->components([
                Grid::make()
                    ->components([
                        TextInput::make('name')
                            ->label(__('validation.attributes.name'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Select::make('category_id')
                            ->label(__('validation.attributes.category_id'))
                            ->relationship('category', 'name')
                            ->required()
                            ->preload()
                            ->columnSpan(1),

                        Select::make('brand_id')
                            ->label(__('validation.attributes.brand_id'))
                            ->relationship('brand', 'name')
                            ->preload()
                            ->columnSpan(1),

                        Textarea::make('description')
                            ->label(__('validation.attributes.description'))
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function statusAndVisibility()
    {
        return Tab::make('Status & Visibility')
            ->label(__('filament.products.status_visibility'))
            ->schema([
                Grid::make(3)
                    ->schema([
                        Toggle::make('featured')
                            ->label(__('validation.attributes.featured'))
                            ->default(false)
                            ->helperText(__('filament.products.featured_helper')),
                        Select::make('status')
                            ->label(__('validation.attributes.status'))
                            ->options(ProductStatus::class)
                            ->default(ProductStatus::Draft)
                            ->required()
                            ->helperText(fn () => new \Illuminate\Support\HtmlString(__('filament.products.status_helper_html'))),
                    ]),
            ]);
    }

    public static function specifications()
    {
        return Tab::make('Specifications')
            ->label(__('filament.products.specifications'))
            ->schema([
                Section::make()
                    ->schema([
                        KeyValue::make('specifications')
                            ->label('مواصفات المنتج')
                            ->keyLabel('الخاصية')
                            ->valueLabel('القيمة')
                            ->addActionLabel('إضافة خاصية')
                            ->editableKeys()
                            ->editableValues()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function firstVariant()
    {
        return Tab::make('First Variant')
            ->label(__('filament.products.first_variant'))
            ->hiddenOn('edit')
            ->components([
                Section::make()
                    ->relationship('defaultVariant')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextInput::make('sku')
                                    ->label(__('validation.attributes.sku'))
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                TextInput::make('price')
                                    ->label(__('validation.attributes.price'))
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('$')
                                    ->columnSpan(1),

                                TextInput::make('compare_at_price')
                                    ->label(__('validation.attributes.compare_at_price'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('$')
                                    ->rule(function (Get $get) {
                                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                                            $price = $get('price');
                                            if ($price !== null && $value <= $price) {
                                                $fail(__('filament.products.compare_at_price_greater_than_price'));
                                            }
                                        };
                                    })
                                    ->columnSpan(1)
                                    ->helperText(__('filament.products.compare_at_price_helper')),
                                TextInput::make('quantity')
                                    ->label(__('validation.attributes.quantity'))
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->columnSpan(1),
                                Toggle::make('is_default')
                                    ->default(true)
                                    ->label(__('filament.products.default_variant'))
                                    ->disabled()
                                    ->dehydrated(true)
                                    ->afterStateHydrated(fn ($component) => $component->state(true))
                                    ->required()
                                    ->helperText(__('filament.products.default_variant_helper')),
                                static::attributeValues(),
                                static::variantImages(),
                            ]),
                    ]),
            ]);
    }

    public static function attributeValues()
    {
        return Repeater::make('attributeValues')
            ->relationship('attributeValuesRaw')
            ->label(__('filament.products.attribute_values'))
            ->schema([
                Select::make('attribute_id')
                    ->label(__('validation.attributes.attribute'))
                    ->options(fn () => Attribute::pluck('name', 'id'))
                    ->required()
                    ->distinct()
                    ->live()
                    ->native(false),
                Select::make('attribute_value_id')
                    ->label(__('validation.attributes.value'))
                    ->options(function (Get $get) {
                        $attributeId = $get('attribute_id');
                        if (! $attributeId) {
                            return [];
                        }

                        return AttributeValue::where('attribute_id', $attributeId)
                            ->pluck('value', 'id');
                    })
                    ->required()
                    ->native(false),
            ])
            ->columns(2)
            ->minItems(1)
            ->defaultItems(1)
            ->itemLabel(fn ($state) => Attribute::find($state['attribute_id'] ?? null)?->name ?? __('filament.products.attribute'))
            ->columnSpanFull()
            ->helperText(__('filament.products.attribute_values_helper'));
    }

    public static function variantImages()
    {
        return Repeater::make('images')
            ->relationship('images')
            ->label(__('validation.attributes.images'))
            ->schema([
                FileUpload::make('path')
                    ->label(__('validation.attributes.image'))
                    ->image()->directory('product-variants')
                    ->visibility('public')
                    ->maxSize(5120)->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/webp'])->required()->columnSpan(1),
                TextInput::make('alt_text')->label(__('validation.attributes.alt_text'))->maxLength(255)->columnSpan(1),
            ])->columns(3)
            ->minItems(1)
            ->defaultItems(1)
            ->itemLabel(fn ($state) => __('filament.products.image').' '.(($state['display_order'] ?? 0) + 1))
            ->columnSpanFull()->helperText(__('filament.products.variant_images_helper'))
            ->orderable('display_order');
    }
}
