<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Wishlist;

class WishlistObserver
{
    /**
     * Handle the Wishlist "created" event.
     */
    public function created(Wishlist $wishlist): void
    {
        $wishlist->product()->increment('favorites_count');
    }

    /**
     * Handle the Wishlist "updated" event.
     */
    public function updated(Wishlist $wishlist): void
    {
        $productVariantIdChanged = $wishlist->wasChanged('product_variant_id');

        if ($productVariantIdChanged) {
            $oldProductVariantId = $wishlist->getOriginal('product_variant_id');
            if ($oldProductVariantId) {
                $this->decrementProductFavoritesCount($oldProductVariantId);
            }
            $wishlist->product()->increment('favorites_count');
        }
    }

    /**
     * Handle the Wishlist "deleted" event.
     */
    public function deleted(Wishlist $wishlist): void
    {
        $wishlist->product()->decrement('favorites_count');
    }

    /**
     * Handle the Wishlist "restored" event.
     */
    public function restored(Wishlist $wishlist): void
    {
        $wishlist->product()->increment('favorites_count');
    }

    /**
     * Handle the Wishlist "force deleted" event.
     */
    public function forceDeleted(Wishlist $wishlist): void
    {
        $wishlist->product()->decrement('favorites_count');
    }

    /**
     * Decrement favorites_count for the product.
     */
    protected function decrementProductFavoritesCount(int $productVariantId): void
    {
        $productVariant = ProductVariant::find($productVariantId);

        if (! $productVariant || ! $productVariant->product_id) {
            return;
        }

        $product = Product::find($productVariant->product_id);

        if ($product && $product->favorites_count > 0) {
            $product->decrement('favorites_count');
        }
    }
}
