<?php

namespace App\Http\Controllers\Budget;

use App\Http\Controllers\Controller;
use App\Models\Budget\Appointment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(Request $request): View
    {
        $user  = auth('budget')->user();
        $year  = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        $appointments = $this->getMonthAppointments($user->id, $year, $month);

        return view('budget.calendar', compact('appointments', 'year', 'month'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'          => ['required', 'string', 'max:100'],
            'note'           => ['nullable', 'string', 'max:500'],
            'start_at'       => ['required', 'date'],
            'end_at'         => ['nullable', 'date', 'after_or_equal:start_at'],
            'remind_minutes' => ['nullable', 'integer', 'min:0', 'max:10080'],
        ]);

        $appointment = Appointment::create([
            ...$data,
            'user_id' => auth('budget')->id(),
        ]);

        return response()->json(['data' => $this->formatAppointment($appointment)], 201);
    }

    public function update(Request $request, Appointment $appointment): JsonResponse
    {
        abort_if($appointment->user_id !== auth('budget')->id(), 403);

        $data = $request->validate([
            'title'          => ['sometimes', 'string', 'max:100'],
            'note'           => ['nullable', 'string', 'max:500'],
            'start_at'       => ['sometimes', 'date'],
            'end_at'         => ['nullable', 'date', 'after_or_equal:start_at'],
            'remind_minutes' => ['nullable', 'integer', 'min:0', 'max:10080'],
        ]);

        $appointment->update($data);

        return response()->json(['data' => $this->formatAppointment($appointment->fresh())]);
    }

    public function destroy(Appointment $appointment): JsonResponse
    {
        abort_if($appointment->user_id !== auth('budget')->id(), 403);

        $appointment->delete();

        return response()->json(null, 204);
    }

    public function api(Request $request): JsonResponse
    {
        $user  = auth('budget')->user();
        $year  = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        $appointments = $this->getMonthAppointments($user->id, $year, $month);

        return response()->json(['data' => $appointments]);
    }

    public function available(Request $request): JsonResponse
    {
        $userId = auth('budget')->id();
        $data = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'include' => [
                'nullable',
                'integer',
                Rule::exists((new Appointment)->getTable(), 'id')
                    ->where(fn ($query) => $query->where('user_id', $userId)),
            ],
        ]);
        $start = Carbon::createFromFormat('Y-m-d', $data['date'])->startOfDay();
        $end = $start->copy()->addDay();

        $appointments = Appointment::with('transactions')
            ->where('user_id', $userId)
            ->where(function ($query) use ($data, $end, $start) {
                $query->where(function ($query) use ($end, $start) {
                    $query
                        ->where('start_at', '>=', $start)
                        ->where('start_at', '<', $end);
                });

                if (! empty($data['include'])) {
                    $query->orWhere('id', $data['include']);
                }
            })
            ->orderBy('start_at')
            ->get()
            ->map(fn (Appointment $appointment) => $this->formatAppointment($appointment))
            ->values();

        return response()->json(['data' => $appointments]);
    }

    private function getMonthAppointments(int $userId, int $year, int $month): array
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        return Appointment::with('transactions')
            ->where('user_id', $userId)
            ->whereBetween('start_at', [$start, $end])
            ->orderBy('start_at')
            ->get()
            ->map(fn ($a) => $this->formatAppointment($a))
            ->toArray();
    }

    private function formatAppointment(Appointment $a): array
    {
        $a->loadMissing('transactions');
        $linkedIncome = $a->transactions->where('type', 'income')->sum('amount');
        $linkedExpense = $a->transactions->where('type', 'expense')->sum('amount');

        return [
            'id'             => $a->id,
            'title'          => $a->title,
            'note'           => $a->note,
            'start_at'       => $a->start_at?->format('Y-m-d\TH:i'),
            'end_at'         => $a->end_at?->format('Y-m-d\TH:i'),
            'start_date'     => $a->start_at?->format('Y-m-d'),
            'start_time'     => $a->start_at?->format('H:i'),
            'end_time'       => $a->end_at?->format('H:i'),
            'remind_minutes' => $a->remind_minutes,
            'linked_transactions_count' => $a->transactions->count(),
            'linked_income' => (float) $linkedIncome,
            'linked_expense' => (float) $linkedExpense,
        ];
    }
}
