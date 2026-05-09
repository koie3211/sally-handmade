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

    public function attributes(): array
    {
        return [
            'accountant' => '承辦會計師',
            'customer_code' => '客戶代碼',
            'customer_short_name' => '客戶簡稱',
            'tax_id_number' => '統一編號',
            'contact_name' => '聯絡人姓名',
            'contact_phone' => '聯絡人電話',
            'service_items' => '辦理項目',
            'service_items.*' => '辦理項目',
            'service_item_other' => '其他辦理項目',
            'status' => '案件狀況',
            'submission_agency' => '送件單位',
            'uses_e_invoice' => '使用光貿電子發票',
            'e_invoice_note' => '光貿電子發票備註',
            'steps' => '流程進度',
            'payment' => '收款與結案',
            ...$this->stepAttributes(),
            ...$this->paymentAttributes(),
        ];
    }

    private function stepAttributes(): array
    {
        $steps = [
            'pre_check' => [
                'label' => '預查流程',
                'fields' => [
                    'submitted_at' => '送件日',
                    'approved_at' => '核准日',
                    'capital_amount' => '資本額',
                ],
            ],
            'business_registration' => [
                'label' => '工商登記流程',
                'fields' => [
                    'submitted_at' => '送件日',
                    'approved_at' => '核准日',
                    'corrected_at' => '補正日',
                ],
            ],
            'certificate' => [
                'label' => '工商憑證流程',
                'fields' => [
                    'submitted_at' => '送件日',
                    'paid_at' => '繳費日',
                ],
            ],
            'tax_registration' => [
                'label' => '國稅局登記流程',
                'fields' => [
                    'submitted_at' => '送件日',
                    'signed_at' => '簽名日',
                    'approved_at' => '核准日',
                    'opened_at' => '開業日',
                    'tax_officer_name' => '稅務員姓名',
                    'tax_officer_phone' => '稅務員電話',
                    'invoice_purchase_certificate_received_at' => '領購票證日',
                ],
            ],
            'permit' => [
                'label' => '特許登記流程',
                'fields' => [
                    'government_fee' => '特許規費',
                    'service_fee' => '特許代辦費用',
                    'submitted_at' => '送件日',
                    'approved_at' => '核准日',
                ],
            ],
            'tdcc_report' => [
                'label' => '集保流程',
                'fields' => [
                    'reported_at' => '申報日期',
                ],
            ],
            'labor_health_insurance' => [
                'label' => '勞健保流程',
                'fields' => [
                    'labor_submitted_at' => '勞保送件日',
                    'health_submitted_at' => '健保送件日',
                ],
            ],
            'import_export_registration' => [
                'label' => '進出口廠商登記',
                'fields' => [
                    'submitted_at' => '送件日',
                    'approved_at' => '核准日',
                ],
            ],
        ];

        $attributes = [
            'steps.*.is_enabled' => '流程啟用狀態',
            'steps.*.is_skipped' => '流程跳過狀態',
        ];

        foreach ($steps as $stepKey => $step) {
            $attributes["steps.$stepKey"] = $step['label'];
            $attributes["steps.$stepKey.is_enabled"] = "{$step['label']}啟用狀態";
            $attributes["steps.$stepKey.is_skipped"] = "{$step['label']}跳過狀態";

            foreach ($step['fields'] as $fieldKey => $fieldLabel) {
                $attributes["steps.$stepKey.$fieldKey"] = "{$step['label']}{$fieldLabel}";
            }
        }

        return $attributes;
    }

    private function paymentAttributes(): array
    {
        return [
            'payment.deposit_amount' => '定金',
            'payment.deposit_received_at' => '定金收款日',
            'payment.deposit_payment_method' => '定金收款方式',
            'payment.balance_amount' => '尾款',
            'payment.balance_received_at' => '尾款收款日',
            'payment.balance_payment_method' => '尾款收款方式',
        ];
    }
}
