<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('order')->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:64', 'unique:order_members,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => '這個名字已經在名單裡了',
        ];
    }
}
