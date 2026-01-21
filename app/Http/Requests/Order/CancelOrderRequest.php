<?php

namespace App\Http\Requests\Order;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class CancelOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');

        if ($order instanceof Order) {
            return $this->user()?->id === $order->user_id;
        }

        return false;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('reason')) {
            $this->merge(['reason' => 'إلغاء الطلب من قبل العميل']);
        }
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ];
    }
}
