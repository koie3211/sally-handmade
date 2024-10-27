<?php

namespace App\Http\Requests\AdminHub\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PermissionSortRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['distinct', 'exists:admin_hub_permissions,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'ids' => '權限 ID',
            'ids.*' => '權限 ID',
        ];
    }
}
