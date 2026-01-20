<?php

namespace App\Services\Orders;

use App\Exceptions\OutOfStockException;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\Cart\CartService;
use App\Services\Inventory\InventoryService;

class OrderReorderService
{
    public function __construct(
        protected CartService $cartService,
        protected InventoryService $inventoryService
    ) {}

    /**
     * @return array{added: int, skipped: int}
     */
    public function reorder(Order $order, User $user, string $sessionId): array
    {
        $order->loadMissing('items');

        $cart = $this->cartService->getOrCreateCart($user, $sessionId);
        $added = 0;
        $skipped = 0;

        foreach ($order->items as $item) {
            $variant = ProductVariant::find($item->product_variant_id);

            if (! $variant || $variant->quantity < 1) {
                $skipped++;

                continue;
            }

            $quantityToAdd = min($item->quantity, $variant->quantity);

            if ($quantityToAdd < 1) {
                $skipped++;

                continue;
            }

            $existingItem = $cart->items()
                ->where('product_variant_id', $variant->id)
                ->first();

            if ($existingItem) {
                $originalQuantity = $existingItem->quantity;
                $targetQuantity = min($originalQuantity + $quantityToAdd, $variant->quantity);

                if ($targetQuantity === $originalQuantity) {
                    $skipped++;

                    continue;
                }

                $existingItem->update([
                    'quantity' => $targetQuantity,
                    'is_selected' => true,
                ]);

                $added += $targetQuantity - $originalQuantity;

                continue;
            }

            try {
                $this->inventoryService->checkStock($variant, $quantityToAdd);
            } catch (OutOfStockException) {
                $skipped++;

                continue;
            }

            $cart->items()->create([
                'product_variant_id' => $variant->id,
                'quantity' => $quantityToAdd,
                'is_selected' => true,
            ]);

            $added += $quantityToAdd;
        }

        return ['added' => $added, 'skipped' => $skipped];
    }
}
