<?php

namespace App\Ai\Tools\Registrar;

use App\Ai\Tools\Registrar\Concerns\FormatsRegistrarCases;
use App\Models\Registrar\RegistrarCase;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

class ShowRegistrarCaseSummaryTool implements Tool
{
    use FormatsRegistrarCases;

    public function description(): string
    {
        return '依案件 ID 查詢單一工商登記案件摘要，包含目前流程、流程完成狀態、收款資料與必要聯絡資訊。';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'case_id' => $schema->integer()->required(),
        ];
    }

    public function handle(Request $request): string
    {
        $case = RegistrarCase::query()
            ->with($this->relations())
            ->find($request['case_id'] ?? null);

        if (! $case) {
            return json_encode([
                'error' => '找不到指定案件',
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return json_encode([
            'case' => $this->caseDetail($case),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
