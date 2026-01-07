<?php

namespace App\Rules;

use App\Enums\PaymentStatus;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PaymentStatusRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! in_array($value, array_column(PaymentStatus::cases(), 'value'), true)) {
            $fail('حالة الدفع المحددة غير صالحة.');
        }
    }
}
