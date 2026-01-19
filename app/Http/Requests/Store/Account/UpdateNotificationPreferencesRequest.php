<?php

namespace App\Http\Requests\Store\Account;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationPreferencesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'marketing_email' => ['required', 'boolean'],
            'marketing_sms' => ['required', 'boolean'],
            'marketing_whatsapp' => ['required', 'boolean'],
            'marketing_call' => ['required', 'boolean'],
        ];
    }
}
