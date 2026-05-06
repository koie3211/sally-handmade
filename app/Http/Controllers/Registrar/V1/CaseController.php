<?php

namespace App\Http\Controllers\Registrar\V1;

use App\Enums\Registrar\CaseStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Registrar\V1\RegistrarCasePaymentRequest;
use App\Http\Requests\Registrar\V1\RegistrarCaseRequest;
use App\Http\Requests\Registrar\V1\RegistrarCaseStatusRequest;
use App\Http\Requests\Registrar\V1\RegistrarCaseStepRequest;
use App\Http\Resources\Registrar\RegistrarCaseResource;
use App\Models\Registrar\RegistrarCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CaseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $length = $request->integer('length', 25);
        $keyword = $request->query('keyword');

        $rows = RegistrarCase::query()
            ->with($this->relations())
            ->when($keyword, fn ($query) => $query->where(fn ($query) => $query
                ->where('customer_code', 'like', "%{$keyword}%")
                ->orWhere('customer_short_name', 'like', "%{$keyword}%")
                ->orWhere('tax_id_number', 'like', "%{$keyword}%")
                ->orWhere('contact_name', 'like', "%{$keyword}%")))
            ->when($request->query('status'), fn ($query, string $status) => $query->where('status', $status))
            ->when($request->query('accountant'), fn ($query, string $accountant) => $query->where('accountant', $accountant))
            ->latest('updated_at')
            ->paginate($length);

        return response()->json([
            'data' => [
                'data' => RegistrarCaseResource::collection($rows->getCollection())->resolve(),
                'count' => $rows->total(),
            ],
        ]);
    }

    public function store(RegistrarCaseRequest $request): JsonResponse
    {
        $registrarCase = DB::transaction(function () use ($request) {
            $data = $request->validated();

            $registrarCase = RegistrarCase::create($this->casePayload($data));
            $this->syncSteps($registrarCase, $data['steps'] ?? []);
            $registrarCase->payment()->create($data['payment'] ?? []);

            return $registrarCase;
        });

        return response()->json([
            'data' => new RegistrarCaseResource($registrarCase->load($this->relations())),
        ], 201);
    }

    public function show(RegistrarCase $registrarCase): JsonResponse
    {
        return response()->json([
            'data' => new RegistrarCaseResource($registrarCase->load($this->relations())),
        ]);
    }

    public function update(RegistrarCaseRequest $request, RegistrarCase $registrarCase): JsonResponse
    {
        DB::transaction(function () use ($request, $registrarCase) {
            $data = $request->validated();

            $registrarCase->update($this->casePayload($data));
            $this->syncSteps($registrarCase, $data['steps'] ?? []);
            $registrarCase->payment()->updateOrCreate([], $data['payment'] ?? []);
        });

        return response()->json([
            'data' => new RegistrarCaseResource($registrarCase->refresh()->load($this->relations())),
        ]);
    }

    public function destroy(RegistrarCase $registrarCase): JsonResponse
    {
        $registrarCase->delete();

        return response()->json(null, 204);
    }

    public function status(RegistrarCaseStatusRequest $request, RegistrarCase $registrarCase): JsonResponse
    {
        $registrarCase->update([
            'status' => $request->validated('status'),
        ]);

        return response()->json([
            'data' => new RegistrarCaseResource($registrarCase->refresh()->load($this->relations())),
        ]);
    }

    public function step(RegistrarCaseStepRequest $request, RegistrarCase $registrarCase, string $step): JsonResponse
    {
        abort_unless(array_key_exists($step, $this->stepMap()), 404, '流程不存在');

        $this->syncStep($registrarCase, $step, $request->validated());

        return response()->json([
            'data' => new RegistrarCaseResource($registrarCase->refresh()->load($this->relations())),
        ]);
    }

    public function payment(RegistrarCasePaymentRequest $request, RegistrarCase $registrarCase): JsonResponse
    {
        $registrarCase->payment()->updateOrCreate([], $request->validated());

        return response()->json([
            'data' => new RegistrarCaseResource($registrarCase->refresh()->load($this->relations())),
        ]);
    }

    private function casePayload(array $data): array
    {
        return Arr::only($data, [
            'accountant',
            'customer_code',
            'customer_short_name',
            'tax_id_number',
            'contact_name',
            'contact_phone',
            'service_items',
            'service_item_other',
            'status',
            'submission_agency',
            'uses_e_invoice',
            'e_invoice_note',
        ]) + [
            'status' => $data['status'] ?? CaseStatus::InProgress->value,
        ];
    }

    private function syncSteps(RegistrarCase $registrarCase, array $steps): void
    {
        foreach (array_keys($this->stepMap()) as $step) {
            $this->syncStep($registrarCase, $step, $steps[$step] ?? [
                'is_enabled' => false,
                'is_skipped' => false,
            ]);
        }
    }

    private function syncStep(RegistrarCase $registrarCase, string $step, array $payload): void
    {
        [$relation, $fields] = $this->stepMap()[$step];

        $registrarCase->{$relation}()->updateOrCreate(
            [],
            Arr::only($payload + ['is_skipped' => false], $fields)
        );
    }

    private function stepMap(): array
    {
        return [
            'pre_check' => ['preCheck', ['is_enabled', 'is_skipped', 'submitted_at', 'approved_at', 'capital_amount']],
            'business_registration' => ['businessRegistration', ['is_enabled', 'is_skipped', 'submitted_at', 'approved_at', 'corrected_at']],
            'certificate' => ['certificate', ['is_enabled', 'is_skipped', 'submitted_at', 'paid_at']],
            'tax_registration' => ['taxRegistration', ['is_enabled', 'is_skipped', 'submitted_at', 'signed_at', 'approved_at', 'opened_at', 'tax_officer_name', 'tax_officer_phone', 'invoice_purchase_certificate_received_at']],
            'permit' => ['permit', ['is_enabled', 'is_skipped', 'government_fee', 'service_fee', 'submitted_at', 'approved_at']],
            'tdcc_report' => ['tdccReport', ['is_enabled', 'is_skipped', 'reported_at']],
            'labor_health_insurance' => ['laborHealthInsurance', ['is_enabled', 'is_skipped', 'labor_submitted_at', 'health_submitted_at']],
            'import_export_registration' => ['importExportRegistration', ['is_enabled', 'is_skipped', 'submitted_at', 'approved_at']],
        ];
    }

    private function relations(): array
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
}
