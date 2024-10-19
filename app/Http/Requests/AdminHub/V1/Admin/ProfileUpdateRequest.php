<?php

namespace App\Http\Requests\AdminHub\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
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
            'avatar' => ['nullable', 'image', 'max:2048'],
            'current_password' => ['required_with:password', 'current_password:adminhub'],
            'password' => ['required_with:current_password', 'string', 'confirmed'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '姓名',
            'avatar' => '頭像',
            'current_password' => '當前密碼',
            'password' => '密碼',
        ];
    }
}
