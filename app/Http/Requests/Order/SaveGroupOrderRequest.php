<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveGroupOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $group = $this->route('group');

        return [
            'member_id' => [
                'required',
                'integer',
                Rule::exists('order_group_members', 'id')->where('group_id', $group->id),
            ],
            'is_pass' => ['required', 'boolean'],
            'drink' => ['nullable', 'string', 'max:120'],
            'sugar' => ['nullable', 'string', 'max:64'],
            'ice' => ['nullable', 'string', 'max:64'],
        ];
    }
}
