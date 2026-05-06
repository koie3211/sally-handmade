<?php

namespace App\Http\Requests\Registrar\V1;

use App\Enums\Registrar\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistrarCasePaymentRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'deposit_amount' => ['nullable', 'integer', 'min:0'],
            'deposit_received_at' => ['nullable', 'date'],
            'deposit_payment_method' => ['nullable', Rule::enum(PaymentMethod::class)],
            'balance_amount' => ['nullable', 'integer', 'min:0'],
            'balance_received_at' => ['nullable', 'date'],
            'balance_payment_method' => ['nullable', Rule::enum(PaymentMethod::class)],
        ];
    }
}
