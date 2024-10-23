<?php

namespace App\Http\Requests\AdminHub\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PermissionStoreRequest extends FormRequest
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
            'resource' => ['required', 'max:16', 'alpha:ascii', 'unique:admin_hub_permissions,resource'],
            'action' => ['required', 'array'],
            'action.create' => ['required', 'boolean'],
            'action.read' => ['required', 'boolean'],
            'action.update' => ['required', 'boolean'],
            'action.delete' => ['required', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '名稱',
            'resource' => '資源名稱',
            'action' => '動作',
            'action.create' => '新增',
            'action.read' => '瀏覽',
            'action.update' => '編輯',
            'action.delete' => '刪除',
        ];
    }
}
