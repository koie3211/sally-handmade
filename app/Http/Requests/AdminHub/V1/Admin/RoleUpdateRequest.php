<?php

namespace App\Http\Requests\AdminHub\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RoleUpdateRequest extends FormRequest
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
            'permissions' => ['required', 'array'],
            // 'permissions.*' => ['distinct', 'exists:admin_hub_permissions,id'],
            'permissions.*.create' => ['nullable', 'boolean'],
            'permissions.*.read' => ['nullable', 'boolean'],
            'permissions.*.update' => ['nullable', 'boolean'],
            'permissions.*.delete' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '名稱',
            'permissions' => '授權',
            'permissions.*' => '授權',
            'permissions.*.create' => '新增',
            'permissions.*.read' => '瀏覽',
            'permissions.*.update' => '編輯',
            'permissions.*.delete' => '刪除',
        ];
    }
}
