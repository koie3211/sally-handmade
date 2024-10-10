<?php

namespace App\Http\Requests\AdminHub\V1;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'email:rfc,dns'],
            'password' => ['required', 'string', 'confirmed'],
        ];
    }
}
