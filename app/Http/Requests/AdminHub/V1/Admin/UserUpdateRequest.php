<?php

namespace App\Http\Requests\AdminHub\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_group_id' => ['required', 'integer', 'exists:admin_hub_user_groups,id'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'name' => ['required', 'string', 'max:16'],
            'status' => ['required', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'user_group_id' => '群組 ID',
            'avatar' => '頭像',
            'name' => '姓名',
            'status' => '狀態',
        ];
    }
}
