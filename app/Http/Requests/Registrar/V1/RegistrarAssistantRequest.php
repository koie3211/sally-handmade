<?php

namespace App\Http\Requests\Registrar\V1;

use Illuminate\Foundation\Http\FormRequest;

class RegistrarAssistantRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'message' => '問題內容',
        ];
    }
}
