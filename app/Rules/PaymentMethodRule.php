<?php

namespace App\Rules;

use App\Enums\PaymentMethod;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PaymentMethodRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! in_array($value, array_column(PaymentMethod::cases(), 'value'), true)) {
            $fail('طريقة الدفع غير صالحة.');
        }
    }
}
