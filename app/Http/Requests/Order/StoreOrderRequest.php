<?php

namespace App\Http\Requests\Order;

use App\Rules\OwnsAddress;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_address_id' => [
                'required',
                new OwnsAddress,
            ],
            'selected_items' => ['required', 'array', 'min:1'],
            'selected_items.*' => ['integer', 'exists:product_variants,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
