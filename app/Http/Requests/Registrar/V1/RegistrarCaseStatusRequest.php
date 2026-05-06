<?php

namespace App\Http\Requests\Registrar\V1;

use App\Enums\Registrar\CaseStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistrarCaseStatusRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(CaseStatus::class)],
        ];
    }
}
