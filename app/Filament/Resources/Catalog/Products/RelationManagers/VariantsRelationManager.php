<?php

namespace App\Filament\Resources\Catalog\Products\RelationManagers;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantAttribute;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\ValidationException;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    public function form(Schema $schema): Schema
    {
        $ownerRecord = $this->getOwnerRecord();
        $attributes = self::attributes($ownerRecord);
        $isEditing = $schema->getOperation() == 'edit';

        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->schema([
                        ...self::getBasicFieldsSchema($isEditing),
                        self::variantImages(),
                        ...self::attributeValues($attributes, $ownerRecord, $isEditing),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sku')
            ->columns(self::getVariantTableColumns())
            ->filters([])
            ->headerActions([
                CreateAction::make()
                    ->label(__('filament.products.add_variant'))
                    ->modalWidth('4xl')
                    ->mutateDataUsing(function (array $data): array {
                        $this->validateVariantAttributeData($data);

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->label(__('actions.edit'))
                    ->modalWidth('4xl')
                    ->mutateDataUsing(function (array $data, ProductVariant $record): array {
                        $this->validateVariantAttributeData($data, $record);

                        return $data;
                    }),
                DeleteAction::make()
                    ->label(__('actions.delete')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('actions.delete')),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    protected function validateVariantAttributeData(array $data, ?ProductVariant $variant = null): void
    {
        $attributeValues = collect($data['attributeValues'] ?? []);
        $errors = [];
        $seenAttributes = [];

        foreach ($attributeValues as $key => $item) {
            $attributeId = $item['attribute_id'] ?? null;

            if (! $attributeId) {
                continue;
            }

            if (isset($seenAttributes[$attributeId])) {
                $errors["attributeValues.{$key}.attribute_id"] = __('filament.products.variant_attribute_duplicate');
                $errors["attributeValues.{$seenAttributes[$attributeId]}.attribute_id"] = __('filament.products.variant_attribute_duplicate');

                continue;
            }

            $seenAttributes[$attributeId] = $key;
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        $attributePairs = $attributeValues
            ->filter(fn ($item) => filled($item['attribute_id'] ?? null) && filled($item['attribute_value_id'] ?? null))
            ->mapWithKeys(fn ($item) => [(int) $item['attribute_id'] => (int) $item['attribute_value_id']]);

        if ($attributePairs->isEmpty() || $attributeValues->count() !== $attributePairs->count()) {
            return;
        }

        if ($variant instanceof ProductVariant) {
            $existingAttributes = $variant->attributeValuesRaw()
                ->pluck('attribute_id', 'id');

            foreach ($attributeValues->values() as $index => $item) {
                $itemId = $item['id'] ?? null;
                if (! $itemId || ! $existingAttributes->has($itemId)) {
                    continue;
                }

                if ((int) $item['attribute_id'] !== (int) $existingAttributes->get($itemId)) {
                    $errors["attributeValues.{$index}.attribute_id"] = __('filament.products.variant_attribute_immutable');
                }
            }
        }

        $product = $this->getOwnerRecord();

        if ($product) {
            $variantIds = $product->variants()
                ->when($variant, fn ($query) => $query->whereKeyNot($variant->id))
                ->pluck('id');

            if ($variantIds->isNotEmpty() && $this->hasDuplicateVariantAttributes($variantIds, $attributePairs)) {
                $errors['attributeValues.0.attribute_value_id'] = __('filament.products.variant_attributes_duplicate');
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    protected static function hasDuplicateVariantAttributes($variantIds, $attributePairs): bool
    {
        $pairCount = $attributePairs->count();

        return ProductVariantAttribute::query()
            ->whereIn('product_variant_id', $variantIds)
            ->where(function ($query) use ($attributePairs) {
                foreach ($attributePairs as $attributeId => $attributeValueId) {
                    $query->orWhere(function ($subQuery) use ($attributeId, $attributeValueId) {
                        $subQuery->where('attribute_id', $attributeId)
                            ->where('attribute_value_id', $attributeValueId);
                    });
                }
            })
            ->select('product_variant_id')
            ->groupBy('product_variant_id')
            ->havingRaw('COUNT(*) = ?', [$pairCount])
            ->exists();
    }

    public static function attributes(Product $product)
    {
        $data = $product->defaultVariant?->attributeValues;
        $attrIds = $data?->pluck('attribute_id') ?? [];

        return Attribute::whereIn('id', $attrIds)
            ->select(['name', 'id'])->get();

    }

    /**
     * Get basic fields schema (SKU, Price, Quantity, etc.)
     */
    protected static function getBasicFieldsSchema(bool $isEditing = false): array
    {
        return [
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
                        ->disabled($isEditing)
                        ->columnSpan(1),
                    Toggle::make('is_default')
                        ->default(false)
                        ->disabled(fn ($state) => $state === true)

                        ->label(__('filament.products.default_variant'))
                        ->helperText(function ($state) {
                            if ($state === true) {
                                return __('filament.products.default_variant_locked_helper');
                            }
                        }),
                ]),
        ];
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
            ->columnSpanFull()->helperText(__('filament.products.variant_images_helper'))->orderable('display_order');
    }

    /**
     * Get attribute values schema
     */
    public static function attributeValues($attributes, $ownerRecord, bool $isEditing)
    {
        if ($attributes->isEmpty()) {
            return [];
        }
        $options = $attributes->pluck('name', 'id');

        return [
            Repeater::make('attributeValues')
                ->relationship('attributeValuesRaw')
                ->label(__('filament.products.attribute_values'))
                ->schema([
                    Select::make('attribute_id')
                        ->label(__('validation.attributes.attribute'))
                        ->options($options)
                        ->required()
                        ->distinct()
                        ->dehydrated(true)
                        ->disabled($isEditing)
                        ->rule(function (?ProductVariantAttribute $record) {
                            return function (string $attribute, $value, \Closure $fail) use ($record) {
                                if ($record === null) {
                                    return;
                                }

                                if ((int) $value !== (int) $record->attribute_id) {
                                    $fail(__('filament.products.variant_attribute_immutable'));
                                }
                            };
                        }),
                    Select::make('attribute_value_id')
                        ->label(__('validation.attributes.value'))
                        ->options(function (Get $get) {
                            $attributeId = $get('attribute_id');

                            return AttributeValue::where('attribute_id', $attributeId)
                                ->pluck('value', 'id');
                        })
                        ->required()
                        ->native(false)
                        ->rule(function (Get $get, ?ProductVariantAttribute $record) use ($ownerRecord) {
                            return function (string $attribute, $value, \Closure $fail) use ($get, $ownerRecord, $record) {
                                if (! $ownerRecord || ! $value) {
                                    return;
                                }

                                $attributeId = $get('attribute_id');
                                if (! $attributeId) {
                                    return;
                                }

                                $attributeValues = collect($get('../../attributeValues') ?? []);
                                $attributePairs = $attributeValues
                                    ->filter(fn ($item) => filled($item['attribute_id'] ?? null) && filled($item['attribute_value_id'] ?? null))
                                    ->mapWithKeys(fn ($item) => [(int) $item['attribute_id'] => (int) $item['attribute_value_id']]);

                                if ($attributePairs->isEmpty() || $attributeValues->count() !== $attributePairs->count()) {
                                    return;
                                }

                                $variantIds = $ownerRecord->variants()
                                    ->when($record, fn ($query) => $query->where('id', '!=', $record->product_variant_id))
                                    ->pluck('id');

                                if ($variantIds->isNotEmpty() && self::hasDuplicateVariantAttributes($variantIds, $attributePairs)) {
                                    $fail(__('filament.products.variant_attributes_duplicate'));
                                }
                            };
                        }),
                ])
                ->columns(2)
                ->minItems($attributes->count())
                ->defaultItems($attributes->count())
                ->columnSpanFull()
                ->helperText(__('filament.products.variant_attributes_fixed_helper'))
                ->reorderable(false)
                ->addable(false)
                ->distinct()
                ->deletable(false),
        ];
    }

    /**
     * Get variant table columns
     */
    protected static function getVariantTableColumns(): array
    {
        return [
            ImageColumn::make('images.path')
                ->label(__('validation.attributes.images'))
                ->circular()
                ->limit(1)
                ->defaultImageUrl(url('/images/placeholder.png')),

            TextColumn::make('sku')
                ->label(__('validation.attributes.sku'))
                ->searchable()
                ->sortable()
                ->weight('bold'),

            TextColumn::make('price')
                ->label(__('validation.attributes.price'))
                ->formatStateUsing(fn ($state): ?string => \App\Data\Casts\MoneyCast::format($state))
                ->sortable(),

            TextColumn::make('compare_at_price')
                ->label(__('validation.attributes.compare_at_price'))
                ->formatStateUsing(fn ($state): ?string => \App\Data\Casts\MoneyCast::format($state))
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('quantity')
                ->label(__('validation.attributes.quantity'))
                ->numeric()
                ->sortable()
                ->badge()
                ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),

            TextColumn::make('is_default')
                ->label(__('filament.products.is_default'))
                ->formatStateUsing(fn ($state): string => $state ? __('actions.yes') : __('actions.no'))
                ->badge()
                ->color(fn ($state): string => $state ? 'success' : 'gray')
                ->sortable(),

            TextColumn::make('attributeValues.value')
                ->label(__('filament.products.attribute_values'))
                ->badge()
                ->separator(','),
        ];
    }
}
