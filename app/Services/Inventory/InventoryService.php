<?php

namespace App\Services\Inventory;

use App\Enums\StockMovementType;
use App\Exceptions\OutOfStockException;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * إنقاص المخزون (للبيع، التالف)
     *
     * @param  ProductVariant  $variant  المنتج
     * @param  int  $quantity  الكمية المراد خصمها (موجبة)
     * @param  StockMovementType  $type  سبب الخصم (Sale, Waste)
     * @param  Model|null  $source  المصدر (Order, Admin User)
     * @param  string|null  $description  ملاحظات
     */
    public function decreaseStock(
        ProductVariant $variant,
        int $quantity,
        StockMovementType $type,
        ?Model $source = null,
        ?string $description = null
    ): StockMovement {
        if ($quantity <= 0) {
            throw new Exception('الكمية يجب أن تكون أكبر من صفر');
        }

        return DB::transaction(function () use ($variant, $quantity, $type, $source, $description) {

            $variant = ProductVariant::lockForUpdate()->find($variant->id);

            if ($variant->quantity < $quantity) {
                throw new Exception("الكمية غير متوفرة للمنتج: {$variant->sku}. المتوفر: {$variant->quantity}, المطلوب: {$quantity}");
            }

            $quantityBefore = $variant->quantity;

            $variant->decrement('quantity', $quantity);

            $quantityAfter = $variant->quantity;

            return StockMovement::create([
                'product_variant_id' => $variant->id,
                'type' => $type,
                'quantity' => -1 * $quantity, // تخزينها بالسالب
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'sourceable_type' => $source ? get_class($source) : null,
                'sourceable_id' => $source ? $source->id : null,
                'description' => $description,
            ]);

        });
    }

    /**
     * زيادة المخزون (للتوريد، الإلغاء، الاسترجاع)
     */
    public function increaseStock(
        ProductVariant $variant,
        int $quantity,
        StockMovementType $type,
        ?Model $source = null,
        ?string $description = null
    ): StockMovement {
        if ($quantity <= 0) {
            throw new Exception('الكمية يجب أن تكون أكبر من صفر');
        }

        return DB::transaction(function () use ($variant, $quantity, $type, $source, $description) {

            $variant = ProductVariant::lockForUpdate()->find($variant->id);

            $quantityBefore = $variant->quantity;

            $variant->increment('quantity', $quantity);

            $quantityAfter = $variant->quantity;

            return StockMovement::create([
                'product_variant_id' => $variant->id,
                'type' => $type,
                'quantity' => $quantity, // موجب
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'sourceable_type' => $source ? get_class($source) : null,
                'sourceable_id' => $source ? $source->id : null,
                'description' => $description,
            ]);
        });
    }

    /**
     * تعديل المخزون يدوياً (للتسويات الجردية)
     * هذه الدالة ذكية: تحدد هل هي زيادة أم نقص بناءً على الفرق
     *
     * @param  int  $newQuantity  الكمية الجديدة المطلوبة
     */
    public function adjustStock(
        ProductVariant $variant,
        int $newQuantity,
        ?Model $adminUser = null,
        ?string $reason = null
    ): StockMovement {

        return DB::transaction(function () use ($variant, $newQuantity, $adminUser, $reason) {
            $variant = ProductVariant::lockForUpdate()->find($variant->id);

            $currentQty = $variant->quantity;
            $diff = $newQuantity - $currentQty;

            if ($diff == 0) {
                throw new Exception('الكمية الجديدة مطابقة للكمية الحالية، لا يوجد تغيير.');
            }

            // إذا كان الفرق موجب -> زيادة، سالب -> نقص
            $type = StockMovementType::ADJUSTMENT;

            // تحديث مباشر
            $variant->update(['quantity' => $newQuantity]);

            return StockMovement::create([
                'product_variant_id' => $variant->id,
                'type' => $type,
                'quantity' => $diff, // سيخزن الفرق (مثلاً +5 أو -3)
                'quantity_before' => $currentQty,
                'quantity_after' => $newQuantity,
                'sourceable_type' => $adminUser ? get_class($adminUser) : null,
                'sourceable_id' => $adminUser ? $adminUser->id : null,
                'description' => $reason ?? 'تسوية يدوية للمخزون',
            ]);
        });
    }

    /**
     * Validate if requested quantity is available without modifying stock.
     *
     * @throws OutOfStockException
     */
    public function checkStock(ProductVariant $variant, int $quantity): void
    {
        if ($variant->quantity < $quantity) {
            throw new OutOfStockException($variant, $quantity);
        }
    }
}
