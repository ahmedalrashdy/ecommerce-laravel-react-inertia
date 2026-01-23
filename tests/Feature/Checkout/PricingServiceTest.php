<?php

namespace Tests\Feature\Checkout;

use App\Data\Orders\OrderItemData;
use App\Services\Checkout\PricingService;
use Illuminate\Support\Collection;
use Tests\TestCase;

class PricingServiceTest extends TestCase
{
    public function test_calculate_total_uses_unit_price_and_quantity(): void
    {
        $items = new Collection([
            new OrderItemData(
                product_variant_id: 1,
                product_id: 1,
                name: 'Item One',
                sku: 'SKU-1',
                unit_price: '200.00',
                quantity: 2,
                options: [],
            ),
            new OrderItemData(
                product_variant_id: 2,
                product_id: 2,
                name: 'Item Two',
                sku: 'SKU-2',
                unit_price: '100.00',
                quantity: 1,
                options: [],
            ),
        ]);

        $result = app(PricingService::class)->calculateTotal($items, null);

        $this->assertSame('500.00', $result->subtotal);
        $this->assertSame('75.00', $result->tax_amount);
        $this->assertSame('50.00', $result->shipping_cost);
        $this->assertSame('0.00', $result->discount_amount);
        $this->assertSame('625.00', $result->grand_total);
    }
}
