<?php

namespace App\Http\Resources\Registrar;

use App\Enums\Registrar\Accountant;
use App\Enums\Registrar\CaseStatus;
use App\Enums\Registrar\PaymentMethod;
use App\Enums\Registrar\ServiceItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class RegistrarCaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'accountant' => $this->accountant,
            'accountant_label' => $this->label(Accountant::class, $this->accountant),
            'customer_code' => $this->customer_code,
            'customer_short_name' => $this->customer_short_name,
            'tax_id_number' => $this->tax_id_number,
            'contact_name' => $this->contact_name,
            'contact_phone' => $this->contact_phone,
            'service_items' => $this->service_items ?? [],
            'service_item_labels' => collect($this->service_items ?? [])
                ->map(fn (string $item) => $this->label(ServiceItem::class, $item))
                ->values(),
            'service_items_label' => collect($this->service_items ?? [])
                ->map(fn (string $item) => $this->label(ServiceItem::class, $item))
                ->filter()
                ->implode('、'),
            'service_item_other' => $this->service_item_other,
            'status' => $this->status?->value ?? $this->status,
            'status_label' => $this->status instanceof CaseStatus ? $this->status->label() : null,
            'submission_agency' => $this->submission_agency,
            'uses_e_invoice' => (bool) $this->uses_e_invoice,
            'e_invoice_note' => $this->e_invoice_note,
            'current_step' => $this->currentStep(),
            'workflow_ready_for_payment' => $this->workflowReadyForPayment(),
            'payment_ready_for_close' => (bool) $this->payment?->isComplete(),
            'steps' => [
                'pre_check' => $this->stepData($this->preCheck),
                'business_registration' => $this->stepData($this->businessRegistration),
                'certificate' => $this->stepData($this->certificate),
                'tax_registration' => $this->stepData($this->taxRegistration),
                'permit' => $this->stepData($this->permit),
                'tdcc_report' => $this->stepData($this->tdccReport),
                'labor_health_insurance' => $this->stepData($this->laborHealthInsurance),
                'import_export_registration' => $this->stepData($this->importExportRegistration),
            ],
            'payment' => [
                'deposit_amount' => $this->payment?->deposit_amount,
                'deposit_received_at' => $this->date($this->payment?->deposit_received_at),
                'deposit_payment_method' => $this->payment?->deposit_payment_method?->value,
                'deposit_payment_method_label' => $this->payment?->deposit_payment_method instanceof PaymentMethod
                    ? $this->payment->deposit_payment_method->label()
                    : null,
                'balance_amount' => $this->payment?->balance_amount,
                'balance_received_at' => $this->date($this->payment?->balance_received_at),
                'balance_payment_method' => $this->payment?->balance_payment_method?->value,
                'balance_payment_method_label' => $this->payment?->balance_payment_method instanceof PaymentMethod
                    ? $this->payment->balance_payment_method->label()
                    : null,
            ],
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }

    private function stepData($step): ?array
    {
        if (! $step) {
            return null;
        }

        return collect($step->getAttributes())
            ->except(['id', 'registrar_case_id', 'created_at', 'updated_at'])
            ->map(fn ($value, string $key) => str_ends_with($key, '_at') ? $this->date($value) : $value)
            ->merge([
                'is_complete' => $step->isComplete(),
            ])
            ->all();
    }

    private function currentStep(): array
    {
        if ($this->status === CaseStatus::AwaitingPayment) {
            return ['key' => 'payment', 'label' => '待收款'];
        }

        if ($this->status === CaseStatus::Closed) {
            return ['key' => 'closed', 'label' => '結案'];
        }

        foreach ($this->workflowSteps() as $key => [$label, $step]) {
            if ($step?->is_enabled && ! $step->isComplete()) {
                return ['key' => $key, 'label' => $label];
            }
        }

        return ['key' => 'ready_for_payment', 'label' => '流程完成'];
    }

    private function workflowReadyForPayment(): bool
    {
        $enabledSteps = collect($this->workflowSteps())
            ->map(fn (array $item) => $item[1])
            ->filter(fn ($step) => $step?->is_enabled);

        return $enabledSteps->isNotEmpty()
            && $enabledSteps->every(fn ($step) => $step->isComplete());
    }

    private function workflowSteps(): array
    {
        return [
            'pre_check' => ['預查', $this->preCheck],
            'business_registration' => ['工商登記', $this->businessRegistration],
            'certificate' => ['工商憑證', $this->certificate],
            'tax_registration' => ['國稅局登記', $this->taxRegistration],
            'permit' => ['特許登記', $this->permit],
            'tdcc_report' => ['集保', $this->tdccReport],
            'labor_health_insurance' => ['勞健保', $this->laborHealthInsurance],
            'import_export_registration' => ['進出口廠商登記', $this->importExportRegistration],
        ];
    }

    private function date($value): ?string
    {
        if (! $value) {
            return null;
        }

        return $value instanceof Carbon
            ? $value->toDateString()
            : Carbon::parse($value)->toDateString();
    }

    private function label(string $enumClass, ?string $value): ?string
    {
        if (! $value || ! enum_exists($enumClass)) {
            return null;
        }

        $enum = $enumClass::tryFrom($value);

        return $enum?->label();
    }
}
