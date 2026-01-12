<?php

namespace App\Observers;

use App\Enums\StockMovementType;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Filament\Notifications\Notification;

class ProductVariantObserver
{
    /**
     * Handle the ProductVariant "created" event.
     */
    public function created(ProductVariant $variant): void
    {
        if ($variant->product_id) {
            $variant->product()->increment('variants_count');
        }

        if ($variant->quantity > 0) {
            $user = auth()->user();

            StockMovement::create([
                'product_variant_id' => $variant->id,
                'type' => StockMovementType::SUPPLIER_RESTOCK,
                'quantity' => $variant->quantity,
                'quantity_before' => 0,
                'quantity_after' => $variant->quantity,
                'sourceable_type' => $user ? get_class($user) : null,
                'sourceable_id' => $user?->id,
                'description' => null,
            ]);
        }
    }

    /**
     * Handle the ProductVariant "deleted" event.
     */
    public function deleting(ProductVariant $variant): bool
    {
        $product = $variant->product;
        if ($product->variants()->count() <= 1) {
            Notification::make()
                ->title(__('validation.can_not_delete_product_must_have_at_least_one_variant'))
                ->danger()
                ->send();

            return false;
        }

        return true;
    }

    /**
     * Handle the ProductVariant "deleted" event.
     */
    public function deleted(ProductVariant $variant): void
    {
        if ($variant->product_id) {
            $variant->product()->decrement('variants_count');
        }
    }
}
