<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('order')->check();
    }

    public function rules(): array
    {
        return [
            'menu_id' => ['nullable', 'integer', 'exists:order_menus,id'],
            'shop_name' => ['nullable', 'string', 'max:120'],
            'image' => ['nullable', 'image', 'max:5120'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (! $this->filled('menu_id') && ! $this->filled('shop_name')) {
                    $validator->errors()->add('shop_name', '請選擇既有菜單，或填寫飲料店名稱');
                }
            },
        ];
    }
}
