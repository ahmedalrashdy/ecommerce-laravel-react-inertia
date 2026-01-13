<?php

namespace App\Filament\Resources\Catalog\Products\Pages;

use App\Filament\Resources\Catalog\Products\ProductResource;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductVariant;
use App\Models\ProductVariantAttribute;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AddProductAttribute extends Page implements HasForms
{
    use InteractsWithForms;
    use InteractsWithRecord;

    protected static string $resource = ProductResource::class;

    protected string $view = 'filament.resources.catalog.products.pages.add-product-attribute';

    public ?array $data = [];

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->form->fill([
            'variants' => $this->buildVariantsState(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('filament.products.add_attribute'))
                    ->schema([
                        Select::make('attribute_id')
                            ->label(__('validation.attributes.attribute'))
                            ->options(fn () => $this->getAvailableAttributeOptions())
                            ->required()
                            ->live()
                            ->native(false)
                            ->afterStateUpdated(function (Set $set): void {
                                $set('variants', $this->buildVariantsState());
                            })
                            ->rule(function () {
                                return function (string $attribute, $value, \Closure $fail): void {
                                    if (! $value) {
                                        return;
                                    }

                                    if ($this->productHasAttribute($value)) {
                                        $fail(__('filament.products.attribute_already_exists'));
                                    }
                                };
                            }),
                        Repeater::make('variants')
                            ->label(__('filament.products.variants'))
                            ->schema([
                                Hidden::make('variant_id')
                                    ->dehydrated(true),
                                TextInput::make('attributes_summary')
                                    ->label(__('filament.products.variant_attributes'))
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->columnSpanFull(),
                                TextInput::make('sku')
                                    ->label(__('validation.attributes.sku'))
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('price')
                                    ->label(__('validation.attributes.price'))
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('quantity')
                                    ->label(__('validation.attributes.quantity'))
                                    ->disabled()
                                    ->dehydrated(false),
                                Select::make('attribute_value_id')
                                    ->label(__('validation.attributes.value'))
                                    ->options(function (Get $get) {
                                        $attributeId = $get('../../attribute_id');
                                        if (! $attributeId) {
                                            return [];
                                        }

                                        return AttributeValue::where('attribute_id', $attributeId)
                                            ->pluck('value', 'id');
                                    })
                                    ->required()
                                    ->native(false)
                                    ->disabled(fn (Get $get): bool => blank($get('../../attribute_id'))),
                            ])
                            ->columns(4)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $attributeId = $data['attribute_id'] ?? null;
        $variants = collect($data['variants'] ?? []);

        $this->validateVariantAttributeValues($attributeId, $variants);

        DB::transaction(function () use ($attributeId, $variants): void {
            foreach ($variants as $variantData) {
                ProductVariantAttribute::create([
                    'product_variant_id' => $variantData['variant_id'],
                    'attribute_id' => $attributeId,
                    'attribute_value_id' => $variantData['attribute_value_id'],
                ]);
            }
        });

        Notification::make()
            ->success()
            ->title(__('filament.products.attribute_added'))
            ->send();

        $this->redirect(ProductResource::getUrl('view', ['record' => $this->record]));
    }

    protected function validateVariantAttributeValues(?int $attributeId, Collection $variants): void
    {
        $errors = [];

        if (! $attributeId) {
            $errors['attribute_id'] = __('validation.required');
        }

        if ($attributeId && $this->productHasAttribute($attributeId)) {
            $errors['attribute_id'] = __('filament.products.attribute_already_exists');
        }

        if ($variants->isEmpty()) {
            $errors['variants'] = __('filament.products.variants_required');
        }

        foreach ($variants->values() as $index => $variant) {
            if (blank($variant['attribute_value_id'] ?? null)) {
                $errors["variants.{$index}.attribute_value_id"] = __('validation.required');
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        $variantIds = $variants->pluck('variant_id')->filter();
        $existingAttributes = ProductVariantAttribute::query()
            ->whereIn('product_variant_id', $variantIds)
            ->get()
            ->groupBy('product_variant_id');

        $signatures = [];

        foreach ($variants->values() as $index => $variant) {
            $variantId = $variant['variant_id'] ?? null;
            if (! $variantId) {
                continue;
            }

            $pairs = $existingAttributes->get($variantId, collect())
                ->mapWithKeys(fn (ProductVariantAttribute $item) => [$item->attribute_id => $item->attribute_value_id])
                ->all();

            $pairs[$attributeId] = (int) $variant['attribute_value_id'];

            ksort($pairs);

            $signature = collect($pairs)
                ->map(fn ($valueId, $attrId) => $attrId.':'.$valueId)
                ->implode('|');

            $signatures[$signature][] = $index;
        }

        foreach ($signatures as $indices) {
            if (count($indices) <= 1) {
                continue;
            }

            foreach ($indices as $index) {
                $errors["variants.{$index}.attribute_value_id"] = __('filament.products.attribute_values_combination_unique');
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    protected function buildVariantsState(): array
    {
        return $this->record->variants()
            ->with('attributeValues.attribute')
            ->orderBy('is_default', 'desc')
            ->orderBy('id')
            ->get()
            ->map(fn (ProductVariant $variant) => [
                'variant_id' => $variant->id,
                'sku' => $variant->sku,
                'price' => $variant->price,
                'quantity' => $variant->quantity,
                'attribute_value_id' => null,
                'attributes_summary' => $this->formatVariantAttributes($variant),
            ])
            ->all();
    }

    protected function formatVariantAttributes(ProductVariant $variant): string
    {
        return $variant->attributeValues
            ->map(function (AttributeValue $value): string {
                $attributeName = $value->attribute?->name ?? __('filament.products.attribute');

                return $attributeName.': '.$value->value;
            })
            ->implode(', ');
    }

    protected function getAvailableAttributeOptions(): array
    {
        $existingAttributeIds = $this->getExistingAttributeIds();

        return Attribute::query()
            ->when($existingAttributeIds->isNotEmpty(), fn ($query) => $query->whereNotIn('id', $existingAttributeIds))
            ->pluck('name', 'id')
            ->all();
    }

    protected function getExistingAttributeIds(): Collection
    {
        $variantIds = $this->record->variants()->pluck('id');

        return ProductVariantAttribute::query()
            ->whereIn('product_variant_id', $variantIds)
            ->distinct()
            ->pluck('attribute_id');
    }

    protected function productHasAttribute(int $attributeId): bool
    {
        return $this->getExistingAttributeIds()
            ->contains($attributeId);
    }
}
