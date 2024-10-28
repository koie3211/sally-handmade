<?php

namespace App\Http\Requests\AdminHub\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserGroupStoreRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:16'],
            'level' => ['required', 'integer', 'min:0', 'max:65000'],
            'status' => ['required', 'boolean'],
            'roles' => ['required', 'array'],
            'roles.*' => ['distinct', 'exists:admin_hub_roles,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '名稱',
            'level' => '等級',
            'status' => '狀態',
            'roles' => '授權',
            'roles.*' => '授權',
        ];
    }
}
