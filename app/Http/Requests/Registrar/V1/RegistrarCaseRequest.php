<?php

namespace App\Http\Requests\Registrar\V1;

use App\Enums\Registrar\Accountant;
use App\Enums\Registrar\CaseStatus;
use App\Enums\Registrar\PaymentMethod;
use App\Enums\Registrar\ServiceItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistrarCaseRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'accountant' => ['required', Rule::enum(Accountant::class)],
            'customer_code' => ['required', 'string', 'max:64'],
            'customer_short_name' => ['required', 'string', 'max:128'],
            'tax_id_number' => ['nullable', 'string', 'max:16'],
            'contact_name' => ['nullable', 'string', 'max:64'],
            'contact_phone' => ['nullable', 'string', 'max:64'],
            'service_items' => ['required', 'array', 'min:1'],
            'service_items.*' => [Rule::enum(ServiceItem::class)],
            'service_item_other' => ['nullable', 'string', 'max:128'],
            'status' => ['nullable', Rule::enum(CaseStatus::class)],
            'submission_agency' => ['nullable', 'string', 'max:128'],
            'uses_e_invoice' => ['required', 'boolean'],
            'e_invoice_note' => ['nullable', 'string', 'max:1000'],

            'steps' => ['sometimes', 'array'],
            'steps.*.is_enabled' => ['required_with:steps.*', 'boolean'],
            'steps.*.is_skipped' => ['nullable', 'boolean'],
            'steps.*.submitted_at' => ['nullable', 'date'],
            'steps.*.approved_at' => ['nullable', 'date'],
            'steps.*.capital_amount' => ['nullable', 'integer', 'min:0'],
            'steps.*.corrected_at' => ['nullable', 'date'],
            'steps.*.paid_at' => ['nullable', 'date'],
            'steps.*.signed_at' => ['nullable', 'date'],
            'steps.*.opened_at' => ['nullable', 'date'],
            'steps.*.tax_officer_name' => ['nullable', 'string', 'max:64'],
            'steps.*.tax_officer_phone' => ['nullable', 'string', 'max:64'],
            'steps.*.invoice_purchase_certificate_received_at' => ['nullable', 'date'],
            'steps.*.government_fee' => ['nullable', 'integer', 'min:0'],
            'steps.*.service_fee' => ['nullable', 'integer', 'min:0'],
            'steps.*.reported_at' => ['nullable', 'date'],
            'steps.*.labor_submitted_at' => ['nullable', 'date'],
            'steps.*.health_submitted_at' => ['nullable', 'date'],

            'payment' => ['sometimes', 'array'],
            'payment.deposit_amount' => ['nullable', 'integer', 'min:0'],
            'payment.deposit_received_at' => ['nullable', 'date'],
            'payment.deposit_payment_method' => ['nullable', Rule::enum(PaymentMethod::class)],
            'payment.balance_amount' => ['nullable', 'integer', 'min:0'],
            'payment.balance_received_at' => ['nullable', 'date'],
            'payment.balance_payment_method' => ['nullable', Rule::enum(PaymentMethod::class)],
        ];
    }
}
