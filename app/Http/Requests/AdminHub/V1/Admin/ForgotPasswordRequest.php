<?php

namespace App\Http\Requests\AdminHub\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc,dns', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'Email',
        ];
    }
}
