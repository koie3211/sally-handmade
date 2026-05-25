<?php

namespace App\Ai\Tools\Registrar;

use App\Ai\Tools\Registrar\Concerns\FormatsRegistrarCases;
use App\Enums\Registrar\Accountant;
use App\Enums\Registrar\CaseStatus;
use App\Models\Registrar\RegistrarCase;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

class ListRegistrarCasesTool implements Tool
{
    use FormatsRegistrarCases;

    public function description(): string
    {
        return '依關鍵字、案件狀態或承辦會計師查詢工商登記案件列表。只回傳精簡案件摘要，適合回答列表、搜尋與目前狀態問題。';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'keyword' => $schema->string()->nullable()->required(),
            'status' => $schema->string()->enum([...array_column(CaseStatus::cases(), 'value'), null])->nullable()->required(),
            'accountant' => $schema->string()->enum([...array_column(Accountant::cases(), 'value'), null])->nullable()->required(),
            'limit' => $schema->integer()->min(1)->max(20)->nullable()->required(),
        ];
    }

    public function handle(Request $request): string
    {
        $limit = $this->clampLimit($request['limit'] ?? null);
        $status = $this->statusValue($request['status'] ?? null);
        $accountant = $this->accountantValue($request['accountant'] ?? null);
        $keyword = trim((string) ($request['keyword'] ?? ''));

        $query = RegistrarCase::query()
            ->with($this->relations())
            ->when($keyword !== '', fn ($query) => $query->where(fn ($query) => $query
                ->where('customer_code', 'like', "%{$keyword}%")
                ->orWhere('customer_short_name', 'like', "%{$keyword}%")
                ->orWhere('tax_id_number', 'like', "%{$keyword}%")
                ->orWhere('contact_name', 'like', "%{$keyword}%")))
            ->when($status, fn ($query, string $status) => $query->where('status', $status))
            ->when($accountant, fn ($query, string $accountant) => $query->where('accountant', $accountant))
            ->latest('updated_at');

        $total = (clone $query)->count();

        return $this->casesResponse($query->limit($limit)->get(), $total);
    }
}
