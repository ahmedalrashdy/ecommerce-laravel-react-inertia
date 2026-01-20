<?php

namespace App\Services\Checkout\Calculators;

use App\Data\Orders\AddressData;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Illuminate\Support\Collection;

class TaxCalculatorService
{
    protected string $taxRate = '0';

    public function calculate(Collection $items, ?AddressData $address = null): Money
    {
        $taxableAmount = $items->reduce(function (Money $carry, $item) {
            $lineTotal = Money::of($item->unit_price, 'USD')->multipliedBy($item->quantity);

            return $carry->plus($lineTotal);
        }, Money::zero('USD'));

        return $taxableAmount->multipliedBy($this->taxRate, RoundingMode::HALF_UP);
    }
}
