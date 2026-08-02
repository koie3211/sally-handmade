<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('order')->check();
    }

    public function rules(): array
    {
        return [
            'shop_name' => ['required', 'string', 'max:120'],
            'image' => ['nullable', 'image', 'max:5120'],
        ];
    }
}
