<?php

namespace App\Ai\Tools\Registrar;

use App\Ai\Tools\Registrar\Concerns\FormatsRegistrarCases;
use App\Enums\Registrar\CaseStatus;
use App\Models\Registrar\RegistrarCase;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Carbon;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

class ListStaleRegistrarCasesTool implements Tool
{
    use FormatsRegistrarCases;

    public function description(): string
    {
        return '查詢辦理中且超過指定天數未更新的工商登記案件。預設 14 天，適合回答卡關、久未更新或需要追蹤的案件。';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'days' => $schema->integer()->min(1)->max(365)->nullable()->required(),
            'limit' => $schema->integer()->min(1)->max(20)->nullable()->required(),
        ];
    }

    public function handle(Request $request): string
    {
        $days = $this->clampLimit($request['days'] ?? null, 14, 365);
        $limit = $this->clampLimit($request['limit'] ?? null);
        $threshold = Carbon::now()->subDays($days);

        $query = RegistrarCase::query()
            ->with($this->relations())
            ->where('status', CaseStatus::InProgress->value)
            ->where('updated_at', '<', $threshold)
            ->oldest('updated_at');

        $total = (clone $query)->count();

        return $this->casesResponse($query->limit($limit)->get(), $total);
    }
}
