<?php

namespace App\Ai\Tools\Registrar\Concerns;

use App\Enums\Registrar\Accountant;
use App\Enums\Registrar\CaseStatus;
use App\Enums\Registrar\ServiceItem;
use App\Http\Resources\Registrar\RegistrarCaseResource;
use App\Models\Registrar\RegistrarCase;
use Illuminate\Database\Eloquent\Collection;

trait FormatsRegistrarCases
{
    /**
     * @return list<string>
     */
    protected function relations(): array
    {
        return [
            'preCheck',
            'businessRegistration',
            'certificate',
            'taxRegistration',
            'permit',
            'tdccReport',
            'laborHealthInsurance',
            'importExportRegistration',
            'payment',
        ];
    }

    protected function clampLimit(mixed $limit, int $default = 10, int $max = 20): int
    {
        $limit = filter_var($limit, FILTER_VALIDATE_INT);

        return max(1, min($max, $limit ?: $default));
    }

    /**
     * @param  Collection<int, RegistrarCase>  $cases
     */
    protected function casesResponse(Collection $cases, int $total): string
    {
        return json_encode([
            'count' => $total,
            'cases' => $cases->map(fn (RegistrarCase $case) => $this->caseSummary($case))->values(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    protected function caseSummary(RegistrarCase $case): array
    {
        $resource = (new RegistrarCaseResource($case))->resolve();

        return [
            'id' => $resource['id'],
            'customer_code' => $resource['customer_code'],
            'customer_short_name' => $resource['customer_short_name'],
            'accountant' => $resource['accountant_label'],
            'status' => $resource['status_label'],
            'service_items' => $resource['service_items_label'],
            'current_step' => $resource['current_step']['label'] ?? null,
            'workflow_ready_for_payment' => $resource['workflow_ready_for_payment'],
            'payment_ready_for_close' => $resource['payment_ready_for_close'],
            'updated_at' => $resource['updated_at'],
        ];
    }

    protected function caseDetail(RegistrarCase $case): array
    {
        $resource = (new RegistrarCaseResource($case))->resolve();

        return [
            ...$this->caseSummary($case),
            'tax_id_number' => $resource['tax_id_number'],
            'contact_name' => $resource['contact_name'],
            'contact_phone' => $resource['contact_phone'],
            'submission_agency' => $resource['submission_agency'],
            'uses_e_invoice' => $resource['uses_e_invoice'],
            'e_invoice_note' => $resource['e_invoice_note'],
            'steps' => $this->stepSummaries($resource['steps']),
            'payment' => $resource['payment'],
            'created_at' => $resource['created_at'],
        ];
    }

    protected function statusValue(mixed $status): ?string
    {
        return is_string($status) ? CaseStatus::tryFrom($status)?->value : null;
    }

    protected function accountantValue(mixed $accountant): ?string
    {
        return is_string($accountant) ? Accountant::tryFrom($accountant)?->value : null;
    }

    protected function serviceItemsLabel(?array $items): string
    {
        return collect($items ?? [])
            ->map(fn (string $item) => ServiceItem::tryFrom($item)?->label())
            ->filter()
            ->implode('、');
    }

    private function stepSummaries(array $steps): array
    {
        return collect($steps)
            ->map(fn (?array $step) => $step ? [
                'is_enabled' => $step['is_enabled'] ?? false,
                'is_skipped' => $step['is_skipped'] ?? false,
                'is_complete' => $step['is_complete'] ?? false,
            ] : null)
            ->all();
    }
}
