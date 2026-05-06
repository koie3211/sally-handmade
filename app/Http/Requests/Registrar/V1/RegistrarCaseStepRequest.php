<?php

namespace App\Http\Requests\Registrar\V1;

use Illuminate\Foundation\Http\FormRequest;

class RegistrarCaseStepRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_enabled' => ['required', 'boolean'],
            'is_skipped' => ['nullable', 'boolean'],
            'submitted_at' => ['nullable', 'date'],
            'approved_at' => ['nullable', 'date'],
            'capital_amount' => ['nullable', 'integer', 'min:0'],
            'corrected_at' => ['nullable', 'date'],
            'paid_at' => ['nullable', 'date'],
            'signed_at' => ['nullable', 'date'],
            'opened_at' => ['nullable', 'date'],
            'tax_officer_name' => ['nullable', 'string', 'max:64'],
            'tax_officer_phone' => ['nullable', 'string', 'max:64'],
            'invoice_purchase_certificate_received_at' => ['nullable', 'date'],
            'government_fee' => ['nullable', 'integer', 'min:0'],
            'service_fee' => ['nullable', 'integer', 'min:0'],
            'reported_at' => ['nullable', 'date'],
            'labor_submitted_at' => ['nullable', 'date'],
            'health_submitted_at' => ['nullable', 'date'],
        ];
    }
}
