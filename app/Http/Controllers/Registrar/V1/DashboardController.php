<?php

namespace App\Http\Controllers\Registrar\V1;

use App\Enums\Registrar\CaseStatus;
use App\Http\Controllers\Controller;
use App\Models\Registrar\RegistrarCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $statusCounts = RegistrarCase::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $recentlyUpdatedCases = RegistrarCase::query()
            ->latest('updated_at')
            ->limit(8)
            ->get(['id', 'customer_code', 'customer_short_name', 'status', 'updated_at']);

        $staleThreshold = Carbon::now()->subDays(14);

        return response()->json([
            'data' => [
                'total_cases' => RegistrarCase::count(),
                'in_progress_cases' => (int) ($statusCounts[CaseStatus::InProgress->value] ?? 0),
                'paused_cases' => (int) ($statusCounts[CaseStatus::Paused->value] ?? 0),
                'awaiting_payment_cases' => (int) ($statusCounts[CaseStatus::AwaitingPayment->value] ?? 0),
                'closed_cases' => (int) ($statusCounts[CaseStatus::Closed->value] ?? 0),
                'cancelled_cases' => (int) ($statusCounts[CaseStatus::Cancelled->value] ?? 0),
                'stale_cases' => RegistrarCase::where('status', CaseStatus::InProgress->value)
                    ->where('updated_at', '<', $staleThreshold)
                    ->count(),
                'recently_updated_cases' => $recentlyUpdatedCases->map(fn (RegistrarCase $case) => [
                    'id' => $case->id,
                    'customer_code' => $case->customer_code,
                    'customer_short_name' => $case->customer_short_name,
                    'status' => $case->status?->value ?? $case->status,
                    'status_label' => $case->status instanceof CaseStatus ? $case->status->label() : null,
                    'updated_at' => $case->updated_at?->toDateTimeString(),
                ]),
            ],
        ]);
    }
}
