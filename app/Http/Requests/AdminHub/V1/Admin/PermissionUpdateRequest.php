<?php

namespace App\Http\Requests\AdminHub\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PermissionUpdateRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:16'],
            'resource' => ['required', 'max:16', 'alpha:ascii', "unique:admin_hub_permissions,resource,{$this->permission->id}"],
            'action' => ['required', 'array'],
            'action.*' => ['distinct', 'in:create,read,update,delete'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '名稱',
            'resource' => '資源名稱',
            'action' => '動作',
            'action.*' => '動作',
        ];
    }
}
