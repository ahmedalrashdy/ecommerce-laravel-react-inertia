<?php

namespace App\Services\Checkout;

use App\Data\Orders\AddressData;
use App\Data\Orders\OrderCalculationData;
use App\Services\Checkout\Calculators\ShippingCalculatorService;
use App\Services\Checkout\Calculators\TaxCalculatorService;
use Brick\Money\Money;
use Illuminate\Support\Collection;

class PricingService
{
    public function __construct(
        protected TaxCalculatorService $taxCalculator,
        protected ShippingCalculatorService $shippingCalculator
    ) {}

    /**
     * @param  Collection  $items  Collection of OrderItemData
     */
    public function calculateTotal(Collection $items, ?AddressData $shippingAddress): OrderCalculationData
    {
        $subtotal = $this->sumLineTotals($items);

        $shippingCost = $this->shippingCalculator->calculate($items, $shippingAddress);

        $discountAmount = Money::zero('USD');

        $taxAmount = $this->taxCalculator->calculate($items, $shippingAddress);

        $grandTotal = $subtotal
            ->plus($shippingCost)
            ->plus($taxAmount)
            ->minus($discountAmount);

        return new OrderCalculationData(
            subtotal: $this->toDecimal($subtotal),
            tax_amount: $this->toDecimal($taxAmount),
            shipping_cost: $this->toDecimal($shippingCost),
            discount_amount: $this->toDecimal($discountAmount),
            grand_total: $this->toDecimal($grandTotal->isNegative() ? Money::zero('USD') : $grandTotal),
            formatted_subtotal: \App\Data\Casts\MoneyCast::formatMoney($subtotal),
            formatted_tax_amount: \App\Data\Casts\MoneyCast::formatMoney($taxAmount),
            formatted_shipping_cost: \App\Data\Casts\MoneyCast::formatMoney($shippingCost),
            formatted_discount_amount: \App\Data\Casts\MoneyCast::formatMoney($discountAmount),
            formatted_grand_total: \App\Data\Casts\MoneyCast::formatMoney(
                $grandTotal->isNegative() ? Money::zero('USD') : $grandTotal
            )
        );
    }

    protected function sumLineTotals(Collection $items): Money
    {
        return $items->reduce(function (Money $carry, $item) {
            $lineTotal = Money::of($item->unit_price, 'USD')->multipliedBy($item->quantity);

            return $carry->plus($lineTotal);
        }, Money::zero('USD'));
    }

    protected function toDecimal(Money $money): string
    {
        return $money->getAmount()->toScale(2)->__toString();
    }
}
