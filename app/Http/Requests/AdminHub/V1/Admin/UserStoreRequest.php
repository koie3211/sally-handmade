<?php

namespace App\Http\Requests\AdminHub\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
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
            'account' => ['required', 'string', 'max:16', 'unique:admin_hub_users,account'],
            'name' => ['required', 'string', 'max:16'],
            'email' => ['required', 'email:rfc,dns', 'max:100', 'unique:admin_hub_users,email'],
            'status' => ['required', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'user_group_id' => '群組 ID',
            'avatar' => '頭像',
            'account' => '帳號',
            'name' => '姓名',
            'email' => 'Email',
            'status' => '狀態',
        ];
    }
}
