<?php

namespace App\Http\Requests\AdminHub\V1;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account' => ['required', 'string', 'max:16', 'unique:admin_hub_users,account'],
            'name' => ['required', 'string', 'max:16'],
            'email' => ['required', 'email:rfc,dns', 'max:100', 'unique:admin_hub_users,email'],
            'password' => ['required', 'string', 'confirmed'],
        ];
    }
}
