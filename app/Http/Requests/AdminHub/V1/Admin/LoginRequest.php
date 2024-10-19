<?php

namespace App\Http\Requests\AdminHub\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'account' => '帳號',
            'password' => '密碼',
            'remember' => '記住我',
        ];
    }
}
