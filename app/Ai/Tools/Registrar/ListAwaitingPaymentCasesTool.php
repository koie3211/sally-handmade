<?php

namespace App\Ai\Tools\Registrar;

use App\Ai\Tools\Registrar\Concerns\FormatsRegistrarCases;
use App\Enums\Registrar\CaseStatus;
use App\Models\Registrar\RegistrarCase;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

class ListAwaitingPaymentCasesTool implements Tool
{
    use FormatsRegistrarCases;

    public function description(): string
    {
        return '查詢目前狀態為待收款的工商登記案件，適合回答待收款清單、收款追蹤與結案前提醒。';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'limit' => $schema->integer()->min(1)->max(20),
        ];
    }

    public function handle(Request $request): string
    {
        $limit = $this->clampLimit($request['limit'] ?? null);

        $query = RegistrarCase::query()
            ->with($this->relations())
            ->where('status', CaseStatus::AwaitingPayment->value)
            ->latest('updated_at');

        $total = (clone $query)->count();

        return $this->casesResponse($query->limit($limit)->get(), $total);
    }
}
