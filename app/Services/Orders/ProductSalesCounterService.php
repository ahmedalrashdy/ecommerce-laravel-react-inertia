<?php

namespace App\Services\Orders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReturnItem;
use Illuminate\Support\Facades\DB;

class ProductSalesCounterService
{
    public function incrementForOrder(Order $order): void
    {
        DB::transaction(function () use ($order): void {
            $items = OrderItem::query()
                ->select('product_id', DB::raw('SUM(quantity) as quantity'))
                ->where('order_id', $order->id)
                ->groupBy('product_id')
                ->get();

            $deltas = $this->buildDeltas($items);

            $this->incrementSalesCounts($deltas);
        });
    }

    public function decrementForReturn(\App\Models\ReturnOrder $returnOrder): void
    {
        DB::transaction(function () use ($returnOrder): void {
            $items = ReturnItem::query()
                ->select('order_items.product_id', DB::raw('SUM(return_items.quantity) as quantity'))
                ->join('order_items', 'return_items.order_item_id', '=', 'order_items.id')
                ->where('return_items.return_id', $returnOrder->id)
                ->groupBy('order_items.product_id')
                ->get();

            $deltas = $this->buildDeltas($items);

            $this->decrementSalesCounts($deltas);
        });
    }

    public function decrementForCancelledOrder(Order $order): void
    {
        DB::transaction(function () use ($order): void {
            $items = OrderItem::query()
                ->select('product_id', DB::raw('SUM(quantity) as quantity'))
                ->where('order_id', $order->id)
                ->groupBy('product_id')
                ->get();

            $deltas = $this->buildDeltas($items);

            $this->decrementSalesCounts($deltas);
        });
    }

    /**
     * @param  \Illuminate\Support\Collection<int, object>  $items
     * @return array<int, int>
     */
    private function buildDeltas($items): array
    {
        return $items
            ->filter(fn ($item) => $item->product_id !== null && (int) $item->quantity > 0)
            ->mapWithKeys(fn ($item) => [(int) $item->product_id => (int) $item->quantity])
            ->toArray();
    }

    /**
     * @param  array<int, int>  $deltas
     */
    private function incrementSalesCounts(array $deltas): void
    {
        if ($deltas === []) {
            return;
        }

        $ids = array_keys($deltas);
        $caseSql = 'CASE id ';
        $bindings = [];

        foreach ($deltas as $productId => $quantity) {
            $caseSql .= 'WHEN ? THEN sales_count + ? ';
            $bindings[] = $productId;
            $bindings[] = $quantity;
        }

        $caseSql .= 'ELSE sales_count END';
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $bindings = array_merge($bindings, $ids);

        DB::update(
            'UPDATE products SET sales_count = '.$caseSql.' WHERE id IN ('.$placeholders.')',
            $bindings
        );
    }

    /**
     * @param  array<int, int>  $deltas
     */
    private function decrementSalesCounts(array $deltas): void
    {
        if ($deltas === []) {
            return;
        }

        $ids = array_keys($deltas);
        $caseSql = 'CASE id ';
        $bindings = [];

        foreach ($deltas as $productId => $quantity) {
            $caseSql .= 'WHEN ? THEN CASE WHEN sales_count < ?  THEN 0 ELSE sales_count - ? END ';
            $bindings[] = $productId;
            $bindings[] = $quantity;
            $bindings[] = $quantity;
        }

        $caseSql .= 'ELSE sales_count END';
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $bindings = array_merge($bindings, $ids);

        DB::update(
            'UPDATE products SET sales_count = '.$caseSql.' WHERE id IN ('.$placeholders.')',
            $bindings
        );
    }
}
