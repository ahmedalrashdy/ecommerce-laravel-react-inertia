<?php

namespace App\Data\Basic;

use App\Models\Order;
use Brick\Money\Money;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class OrderSummaryData extends Data
{
    public function __construct(
        public string $formattedSubtotal,
        public string $formattedTaxAmount,
        public string $formattedShippingCost,
        public string $formattedDiscountAmount,
        public string $formattedGrandTotal,
    ) {}

    public static function fromModel(Order $order): self
    {
        $subtotal = Money::of($order->subtotal, 'USD');
        $taxAmount = Money::of($order->tax_amount, 'USD');
        $shippingCost = Money::of($order->shipping_cost, 'USD');
        $discountAmount = Money::of($order->discount_amount, 'USD');
        $grandTotal = Money::of($order->grand_total, 'USD');

        return self::from([
            'formattedSubtotal' => \App\Data\Casts\MoneyCast::formatMoney($subtotal),
            'formattedTaxAmount' => \App\Data\Casts\MoneyCast::formatMoney($taxAmount),
            'formattedShippingCost' => \App\Data\Casts\MoneyCast::formatMoney($shippingCost),
            'formattedDiscountAmount' => \App\Data\Casts\MoneyCast::formatMoney($discountAmount),
            'formattedGrandTotal' => \App\Data\Casts\MoneyCast::formatMoney($grandTotal),
        ]);
    }
}
