<?php

namespace App\Services\Checkout\Calculators;

use App\Data\Orders\AddressData;
use Brick\Money\Money;
use Illuminate\Support\Collection;

class ShippingCalculatorService
{
    public function calculate(Collection $items, ?AddressData $address): Money
    {
        $baseCost = Money::of('0', 'USD');
        $subtotal = $items->reduce(function (Money $carry, $item) {
            $lineTotal = Money::of($item->unit_price, 'USD')->multipliedBy($item->quantity);

            return $carry->plus($lineTotal);
        }, Money::zero('USD'));

        if ($subtotal->isGreaterThan(Money::of('0', 'USD'))) {
            return Money::zero('USD');
        }

        return $baseCost;
    }
}
