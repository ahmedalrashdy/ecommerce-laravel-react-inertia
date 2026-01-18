<?php

namespace App\Services\Cart;

use App\Data\Basic\CartData;
use App\Data\Basic\CartItemData;
use App\Exceptions\OutOfStockException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\Inventory\InventoryService;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function __construct(public InventoryService $inventoryService) {}

    public function getOrCreateCart(?User $user, string $sesstionId): Cart
    {
        if ($user) {
            return Cart::firstOrCreate(['user_id' => $user->id]);
        }

        return Cart::firstOrCreate(['session_id' => $sesstionId]);
    }

    public function getCartVariantIds(?User $user, string $sesstionId)
    {
        return $this->getOrCreateCart($user, $sesstionId)
            ->items()->pluck('quantity', 'product_variant_id')->toArray();
    }

    /**
     * Calculate totals server-side based on selection
     */
    public function cartSummary(Cart $cart): CartData
    {
        $items = $cart->items()
            ->with([
                'productVariant.defaultImage',
                'productVariant.product',
                'productVariant.attributeValues.attribute',
            ])
            ->orderBy('id', 'desc')
            ->get();

        $subtotal = Money::of(0, 'USD');
        $selectedCount = 0;

        foreach ($items as $item) {
            if ($item->is_selected && $item->productVariant->quantity > 0) {
                $unitPrice = Money::of($item->productVariant->price, 'USD');
                $linePrice = $unitPrice->multipliedBy($item->quantity, RoundingMode::HALF_UP);
                $subtotal = $subtotal->plus($linePrice);
                $selectedCount++;
            }
        }

        // Check if all items are selected (for "Select All" checkbox state)
        $isAllSelected = $items->isNotEmpty() && $items->every(fn ($i) => $i->is_selected && $i->productVariant->quantity > 0);

        return CartData::from([
            'id' => $cart->id,
            'items' => CartItemData::collect($items),
            'itemsCount' => $items->sum('quantity'),
            'selectedCount' => $selectedCount,
            'subtotal' => $subtotal->getAmount()->toScale(2)->__toString(),
            'formattedSubtotal' => \App\Data\Casts\MoneyCast::formatMoney($subtotal),
            'isAllSelected' => $isAllSelected,
        ]);
    }

    /**
     * Add item to cart
     *
     * @throws OutOfStockException
     */
    public function addItem(Cart $cart, ProductVariant $variant, int $quantity = 1): void
    {
        // Check stock availability
        $this->inventoryService->checkStock($variant, $quantity);

        // Check if item already exists in cart
        $existingItem = $cart->items()
            ->where('product_variant_id', $variant->id)
            ->first();

        if ($existingItem) {
            return;
        } else {
            // Create new cart item
            $cart->items()->create([
                'product_variant_id' => $variant->id,
                'quantity' => $quantity,
                'is_selected' => true,
            ]);
        }
    }

    /**
     * Remove item from cart
     */
    public function removeItem(Cart $cart, int $cartItemId): void
    {
        $cartItem = $cart->items()->findOrFail($cartItemId);
        $cartItem->delete();
    }

    /**
     * Update item quantity
     *
     * @throws OutOfStockException
     */
    public function updateQuantity(Cart $cart, ProductVariant $variant, int $quantity)
    {
        // Check stock availability
        $this->inventoryService->checkStock($variant, $quantity);
        $cart->items()->where('product_variant_id', $variant->id)
            ->update(['quantity' => $quantity]);

    }

    /**
     * Toggle item selection
     */
    public function toggleSelection(Cart $cart, int $cartItemId)
    {
        $cartItem = $cart->items()->findOrFail($cartItemId);
        $cartItem->update(['is_selected' => ! $cartItem->is_selected]);
    }

    /**
     * Toggle all items selection
     */
    public function toggleAll(Cart $cart, bool $selectAll)
    {
        $cart->items()->update(['is_selected' => $selectAll]);
    }

    public function claimSessionCart(User $user, string $sessionId): void
    {
        if ($sessionId === '') {
            return;
        }

        $sessionCart = Cart::where('session_id', $sessionId)->first();

        if (! $sessionCart) {
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $user->id]);

        if ($sessionCart->id === $userCart->id) {
            return;
        }

        DB::transaction(function () use ($sessionCart, $userCart): void {
            $sessionItems = $sessionCart->items()
                ->get(['id', 'product_variant_id', 'quantity', 'is_selected']);

            if ($sessionItems->isEmpty()) {
                $sessionCart->delete();

                return;
            }

            $sessionItemMap = $sessionItems->keyBy('product_variant_id');
            $variantIds = $sessionItemMap->keys()->all();

            $userItems = $userCart->items()
                ->whereIn('product_variant_id', $variantIds)
                ->get(['id', 'product_variant_id', 'quantity', 'is_selected'])
                ->keyBy('product_variant_id');

            $overlappingIds = $userItems->keys()->all();
            $newVariantIds = array_values(array_diff($variantIds, $overlappingIds));

            if ($overlappingIds) {
                $quantityCases = 'CASE';
                $selectionCases = 'CASE';

                foreach ($overlappingIds as $variantId) {
                    $sessionItem = $sessionItemMap->get($variantId);
                    $quantityCases .= sprintf(
                        ' WHEN product_variant_id = %d THEN quantity + %d',
                        $variantId,
                        $sessionItem->quantity
                    );
                    $selectionCases .= sprintf(
                        ' WHEN product_variant_id = %d THEN CASE WHEN is_selected = 1 OR %d = 1 THEN 1 ELSE 0 END',
                        $variantId,
                        (int) $sessionItem->is_selected
                    );
                }

                $quantityCases .= ' ELSE quantity END';
                $selectionCases .= ' ELSE is_selected END';

                CartItem::query()
                    ->where('cart_id', $userCart->id)
                    ->whereIn('product_variant_id', $overlappingIds)
                    ->update([
                        'quantity' => DB::raw($quantityCases),
                        'is_selected' => DB::raw($selectionCases),
                    ]);

                $sessionCart->items()
                    ->whereIn('product_variant_id', $overlappingIds)
                    ->delete();
            }

            if ($newVariantIds) {
                $sessionCart->items()
                    ->whereIn('product_variant_id', $newVariantIds)
                    ->update(['cart_id' => $userCart->id]);
            }

            $sessionCart->refresh();

            if (! $sessionCart->items()->exists()) {
                $sessionCart->delete();
            }
        });
    }
}
