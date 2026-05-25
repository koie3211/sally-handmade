<?php

namespace App\Ai\Tools\Registrar;

use App\Ai\Tools\Registrar\Concerns\FormatsRegistrarCases;
use App\Enums\Registrar\Accountant;
use App\Enums\Registrar\CaseStatus;
use App\Models\Registrar\RegistrarCase;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

class ListCasesByAccountantTool implements Tool
{
    use FormatsRegistrarCases;

    public function description(): string
    {
        return '依承辦會計師查詢工商登記案件，可搭配案件狀態篩選。accountant 可用 ding、chen、mu，分別代表丁會、陳會、木會。';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'accountant' => $schema->string()->enum(Accountant::class)->required(),
            'status' => $schema->string()->enum([...array_column(CaseStatus::cases(), 'value'), null])->nullable()->required(),
            'limit' => $schema->integer()->min(1)->max(20)->nullable()->required(),
        ];
    }

    public function handle(Request $request): string
    {
        $accountant = $this->accountantValue($request['accountant'] ?? null);

        if (! $accountant) {
            return json_encode([
                'error' => '承辦會計師只能是 ding、chen、mu',
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $limit = $this->clampLimit($request['limit'] ?? null);
        $status = $this->statusValue($request['status'] ?? null);

        $query = RegistrarCase::query()
            ->with($this->relations())
            ->where('accountant', $accountant)
            ->when($status, fn ($query, string $status) => $query->where('status', $status))
            ->latest('updated_at');

        $total = (clone $query)->count();

        return $this->casesResponse($query->limit($limit)->get(), $total);
    }
}
