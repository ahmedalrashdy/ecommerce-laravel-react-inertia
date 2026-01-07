<?php

namespace App\Rules;

use App\Enums\OrderStatus;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OrderStatusRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! in_array($value, array_column(OrderStatus::cases(), 'value'), true)) {
            $fail('حالة الطلب المحددة غير صالحة.');
        }
    }
}
