<?php

namespace App\Services\Products;

use App\Data\Basic\AttributeValueData;
use App\Data\Basic\ProductAttributeData;
use App\Data\Basic\ProductDetailsData;
use App\Data\Basic\ProductVariantData;
use App\Models\Product;
use Illuminate\Support\Collection;

class ProductDetailsService
{
    public function getProductDetailsWithTargetVariant(Product $product, array $filters = [])
    {
        return ProductDetailsData::fromModel($product, $filters);
    }

    /**
     * الخوارزمية الأساسية لمعالجة السمات وتحديد القيم المتاحة والمختارة
     */
    public function resolveProductAttributes(Product $product, array $filters = []): Collection
    {
        // 1. تحديد القيم التي يرغب المستخدم بها 
        $requestedValueIds = !empty($filters['values'])
            ? collect($filters['values'])
            : $product->defaultVariant?->attributeValues->pluck('id') ?? collect();

        // 2. جلب السمات مجمعة ومرتبة 
        $attributes = $this->groupedAttributes($product);

        // 3. "حوض" المتغيرات المتاحة: نبدأ بجميع متغيرات المنتج
        // في كل دورة، سيقل هذا العدد بناءً على الاختيارات السابقة
        $availableVariantsPool = $product->variants;

        // 4. Forward Checking algorthim
        foreach ($attributes as $attribute) {


            $validValueIdsInCurrentPool = $availableVariantsPool
                ->pluck('attributeValues')
                ->flatten()
                ->where('attribute_id', $attribute->id)
                ->pluck('id')
                ->unique();

            // تحديث حالة enabled لكل قيمة داخل السمة
            $attribute->values->transform(function (AttributeValueData $val) use ($validValueIdsInCurrentPool) {
                $val->enabled = $validValueIdsInCurrentPool->contains($val->id);
                return $val;
            });

            // ب. تحديد القيمة المختارة (Selected Value)
            // نحاول العثور على القيمة التي طلبها المستخدم لهذه السمة
            $candidateValue = $attribute->values->first(function ($val) use ($requestedValueIds) {
                return $requestedValueIds->contains($val->id);
            });

            // ج. منطق حل التعارض (Conflict Resolution / Smart Selection)
            // إذا لم يحدد المستخدم قيمة، أو القيمة المحددة غير متاحة (enabled=false)
            // نقوم باختيار أول قيمة متاحة تلقائياً
            if (!$candidateValue || !$candidateValue->enabled) {
                $candidateValue = $attribute->values->firstWhere('enabled', true);
            }

            // د. تعيين القيمة المختارة للسمة
            $attribute->selectedValue = $candidateValue;

            // هـ. تقليص "حوض" المتغيرات للدورة القادمة (Filtering)
            // نحتفظ فقط بالمتغيرات التي تحتوي على القيمة التي تم اعتمادها الآن
            if ($candidateValue) {
                $availableVariantsPool = $availableVariantsPool->filter(function ($variant) use ($candidateValue) {
                    return $variant->attributeValues->contains('id', $candidateValue->id);
                });
            }
        }

        return $attributes;
    }

    /**
     * إيجاد المتغير النهائي بناءً على السمات المعالجة
     */
    public function currentVariant(Product $product, Collection $attributes): ?ProductVariantData
    {
        // نجمع المعرفات التي تم اختيارها نهائياً من مصفوفة السمات
        $finalSelectedIds = $attributes
            ->pluck('selectedValue.id')
            ->filter() // إزالة القيم الفارغة إن وجدت
            ->sort()
            ->values();

        // نبحث عن المتغير الذي يطابق هذه التركيبة من القيم
        $variantModel = $product->variants->first(function ($variant) use ($finalSelectedIds) {
            $variantValueIds = $variant->attributeValues->pluck('id')->sort()->values();
            return $finalSelectedIds->diff($variantValueIds)->isEmpty() &&
                $variantValueIds->diff($finalSelectedIds)->isEmpty();
        });

        // إذا لم نجد (نظرياً لا يجب أن يحدث بسبب الخوارزمية أعلاه)، نرجع الافتراضي
        $targetVariant = $variantModel ?? $product->defaultVariant;

        return $targetVariant ? ProductVariantData::from($targetVariant) : null;
    }

    /**
     * @return Collection<int, ProductAttributeData>
     */
    private function groupedAttributes(Product $product): Collection
    {
        $product->loadMissing('variants.attributeValues.attribute');

        return $product->variants->pluck("attributeValues")
            ->flatten()
            ->groupBy('attribute_id')
            ->map(function ($values, $attributeId) {
                $attributeModel = $values->first()->attribute;

                return ProductAttributeData::from([
                    'id' => $attributeId,
                    'name' => $attributeModel->name,
                    'type' => $attributeModel->type,
                    'selectedValue' => null,
                    'valuesCount' => $values->unique('id')->count(),
                    'values' => $values
                        ->unique('id')
                        ->map(fn($value) => AttributeValueData::from([
                            'id' => $value->id,
                            'value' => $value->value,
                            'colorCode' => $value->color_code,
                            'enabled' => false,
                        ]))
                        ->sortBy('id')
                        ->values(),
                ]);
            })
            ->sortBy("valuesCount")
            ->values();
    }
}