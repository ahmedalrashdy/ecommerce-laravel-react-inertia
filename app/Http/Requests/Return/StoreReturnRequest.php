<?php

namespace App\Http\Requests\Return;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReturnRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $order = $this->route('order');

        if ($order instanceof Order) {
            return $this->user()?->id === $order->user_id;
        }

        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $order = $this->route('order');

        if ($order instanceof Order) {
            $this->merge(['order_id' => $order->id]);
        }
    }

    public function rules(): array
    {
        $order = $this->route('order');

        return [
            'order_id' => [
                'required',
                'integer',
                // يجب أن يكون الطلب يخص المستخدم الحالي
                Rule::exists('orders', 'id')->where('user_id', auth()->id()),
            ],
            'return_type' => ['required', 'string', Rule::in(['full', 'partial'])],
            'reason' => [
                'nullable',
                'string',
                'max:500',
                Rule::requiredIf($this->input('return_type') === 'full'),
            ],

            // العناصر المراد إرجاعها
            'items' => ['array'],
            'items.*.order_item_id' => [
                'required',
                'integer',
                Rule::exists('order_items', 'id')
                    ->where('order_id', $order?->id),
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.reason' => ['required', 'string', 'min:3', 'max:500'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('return_type') !== 'partial') {
                return;
            }

            $items = $this->input('items');

            if (! is_array($items) || count($items) === 0) {
                $validator->errors()->add('items', 'يجب اختيار عناصر للإرجاع الجزئي.');
            }
        });
    }
}
