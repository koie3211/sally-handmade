<?php

namespace App\Http\Controllers\Budget;

use App\Http\Controllers\Controller;
use App\Models\Budget\Appointment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    private function getMonthAppointments(int $userId, int $year, int $month): array
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        return Appointment::where('user_id', $userId)
            ->whereBetween('start_at', [$start, $end])
            ->orderBy('start_at')
            ->get()
            ->map(fn ($a) => $this->formatAppointment($a))
            ->toArray();
    }

    private function formatAppointment(Appointment $a): array
    {
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
        ];
    }
}
