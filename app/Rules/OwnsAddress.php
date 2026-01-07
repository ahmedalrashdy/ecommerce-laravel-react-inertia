<?php

namespace App\Rules;

use App\Models\UserAddress;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OwnsAddress implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = UserAddress::where('id', $value)
            ->where('user_id', auth()->id())
            ->exists();

        if (! $exists) {
            $fail(__('العنوان المحدد غير صالح أو لا يخص المستخدم.'));
        }
    }
}
