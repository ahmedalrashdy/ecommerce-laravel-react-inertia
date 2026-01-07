<?php

namespace App\Data\Casts;

use Brick\Money\Money;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class MoneyCast implements Cast
{
    public static function format(mixed $value, string $currency = 'USD'): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof Money) {
            return self::formatMoney($value);
        }

        $amount = is_string($value) ? trim($value) : (string) $value;
        if (! preg_match('/^-?\d+(?:\.\d+)?$/', $amount)) {
            return null;
        }

        $isNegative = str_starts_with($amount, '-');
        if ($isNegative) {
            $amount = substr($amount, 1);
        }

        [$integerPart, $fractionPart] = array_pad(explode('.', $amount, 2), 2, '');
        $integerPart = ltrim($integerPart, '0');
        $integerPart = $integerPart === '' ? '0' : $integerPart;

        $formattedInteger = preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', $integerPart);

        $fractionPart = substr($fractionPart, 0, 2);
        $fractionPart = str_pad($fractionPart, 2, '0');
        $fractionPart = rtrim($fractionPart, '0');

        $symbol = $currency === 'USD' ? '$' : $currency.' ';
        $formatted = $symbol.$formattedInteger;

        if ($fractionPart !== '') {
            $formatted .= '.'.$fractionPart;
        }

        return $isNegative ? '-'.$formatted : $formatted;
    }

    public static function formatMoney(Money $money): string
    {
        return self::format($money->getAmount()->__toString(), $money->getCurrency()->getCurrencyCode()) ?? '';
    }

    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        return self::format($value);
    }
}
