<?php

namespace App\Http\Requests\Registrar\V1;

use Illuminate\Foundation\Http\FormRequest;

class RegistrarLoginRequest extends FormRequest
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
