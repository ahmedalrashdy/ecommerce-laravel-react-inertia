<?php

namespace App\Services\Wishlist;

use App\Data\Basic\WishlistData;
use App\Data\Basic\WishlistItemData;
use App\Models\ProductVariant;
use App\Models\User;

class WishlistService
{
    public function wishlistVariantIds(User $user): array
    {
        return $user->wishlists()->pluck('product_variant_id')->toArray();
    }

    public function wishlistDropdownSummary(User $user): WishlistData
    {
        $items = $user->wishlists()
            ->with([
                'productVariant.defaultImage',
                'productVariant.product',
                'productVariant.attributeValues.attribute',
            ])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $itemsCount = $user->wishlists()->count();

        return WishlistData::from([
            'items' => WishlistItemData::collect($items),
            'itemsCount' => $itemsCount,
        ]);
    }

    public function toggleItem(User $user, ProductVariant $variant): bool
    {
        $item = $user->wishlists()->firstOrCreate([
            'product_variant_id' => $variant->id,
        ]);
        $created = $item->wasRecentlyCreated;
        if (! $created) {
            $item->delete();
        }

        return $created;
    }
}
