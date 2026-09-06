<?php

namespace App\Http\Controllers\Budget;

use App\Http\Controllers\Controller;
use App\Models\Budget\Appointment;
use App\Models\Budget\Category;
use App\Models\Budget\Transaction;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth('budget')->user();
        $current = $this->parseMonth($request->input('month'));
        $month = $current->format('Y-m');
        $prevMonth = $current->copy()->subMonth()->format('Y-m');
        $nextMonth = $current->copy()->addMonth()->format('Y-m');
        $monthLabel = $current->format('Y 年 n 月');
        $year = $current->year;
        $mon = $current->month;

        $transactions = Transaction::with('category')
            ->where('user_id', $user->id)
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $mon)
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get()
            ->groupBy(fn ($t) => $t->transaction_date->format('Y-m-d'));

        $monthlyExpense = $transactions->flatten()->where('type', 'expense')->sum('amount');
        $monthlyIncome = $transactions->flatten()->where('type', 'income')->sum('amount');

        $categories = Category::forUser($user->id);
        $defaults = $user->defaultBookkeeping();

        return view('budget.history', compact(
            'transactions',
            'monthlyExpense',
            'monthlyIncome',
            'month',
            'prevMonth',
            'nextMonth',
            'monthLabel',
            'categories',
            'defaults',
        ));
    }

    private function parseMonth(mixed $month): Carbon
    {
        if (is_string($month) && preg_match('/^\d{4}-\d{2}$/', $month)) {
            try {
                return Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            } catch (\Throwable) {
            }
        }

        return now()->startOfMonth();
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateTransaction($request);
        $userId = auth('budget')->id();

        $transaction = DB::transaction(function () use ($data, $userId) {
            return Transaction::create([
                ...$this->transactionAttributes($data),
                'user_id' => $userId,
                'appointment_id' => $this->resolveAppointmentId(
                    $data,
                    $userId,
                    $data['transaction_date'],
                ),
            ]);
        });

        return response()->json(['data' => $this->formatTransaction($transaction)], 201);
    }

    public function update(Request $request, Transaction $transaction): JsonResponse
    {
        abort_if($transaction->user_id !== auth('budget')->id(), 403);

        $data = $this->validateTransaction($request, updating: true, transaction: $transaction);
        $userId = auth('budget')->id();
        $transactionDate = $data['transaction_date']
            ?? $transaction->transaction_date->format('Y-m-d');

        DB::transaction(function () use ($data, $transaction, $transactionDate, $userId) {
            $attributes = $this->transactionAttributes($data);

            if (array_key_exists('appointment_action', $data)) {
                $attributes['appointment_id'] = $this->resolveAppointmentId(
                    $data,
                    $userId,
                    $transactionDate,
                );
            }

            $transaction->update($attributes);
        });

        return response()->json([
            'data' => $this->formatTransaction($transaction->fresh()),
        ]);
    }

    public function destroy(Transaction $transaction): JsonResponse
    {
        abort_if($transaction->user_id !== auth('budget')->id(), 403);

        $transaction->delete();

        return response()->json(null, 204);
    }

    private function validateTransaction(
        Request $request,
        bool $updating = false,
        ?Transaction $transaction = null,
    ): array {
        $userId = auth('budget')->id();
        $presence = $updating ? 'sometimes' : 'required';
        $categoryType = $request->input('type', $transaction?->type);
        $appointmentAction = $request->input('appointment_action');

        return $request->validate([
            'category_id' => [
                $presence,
                'integer',
                Rule::exists((new Category)->getTable(), 'id')
                    ->where(function ($query) use ($categoryType, $userId) {
                        $query
                            ->where('type', $categoryType)
                            ->where(function ($query) use ($userId) {
                                $query->whereNull('user_id')->orWhere('user_id', $userId);
                            });
                    }),
            ],
            'amount' => [$presence, 'numeric', 'min:0.01', 'max:9999999'],
            'type' => [$presence, 'in:expense,income'],
            'note' => ['nullable', 'string', 'max:200'],
            'transaction_date' => [$presence, 'date_format:Y-m-d'],
            'appointment_action' => [
                $updating ? 'sometimes' : 'nullable',
                Rule::in(['none', 'existing', 'create']),
            ],
            'appointment_id' => [
                'nullable',
                'integer',
                Rule::prohibitedIf($appointmentAction !== 'existing'),
                Rule::requiredIf($request->input('appointment_action') === 'existing'),
                Rule::exists((new Appointment)->getTable(), 'id')
                    ->where(fn ($query) => $query->where('user_id', $userId)),
            ],
            'appointment_title' => [
                'nullable',
                'string',
                'max:100',
                Rule::prohibitedIf($appointmentAction !== 'create'),
                Rule::requiredIf($request->input('appointment_action') === 'create'),
            ],
            'appointment_start_time' => [
                'nullable',
                'date_format:H:i',
                'after_or_equal:08:00',
                'before_or_equal:20:00',
                Rule::prohibitedIf($appointmentAction !== 'create'),
                Rule::requiredIf($request->input('appointment_action') === 'create'),
            ],
            'appointment_end_time' => [
                'nullable',
                'date_format:H:i',
                'after_or_equal:08:00',
                'before_or_equal:20:00',
                'after_or_equal:appointment_start_time',
                Rule::prohibitedIf($appointmentAction !== 'create'),
            ],
        ]);
    }

    private function transactionAttributes(array $data): array
    {
        return Arr::only($data, [
            'category_id',
            'amount',
            'type',
            'note',
            'transaction_date',
        ]);
    }

    private function resolveAppointmentId(
        array $data,
        int $userId,
        string $transactionDate,
    ): ?int {
        return match ($data['appointment_action'] ?? 'none') {
            'existing' => Appointment::where('user_id', $userId)
                ->lockForUpdate()
                ->findOrFail($data['appointment_id'])
                ->id,
            'create' => Appointment::create([
                'user_id' => $userId,
                'title' => $data['appointment_title'],
                'start_at' => "{$transactionDate}T{$data['appointment_start_time']}",
                'end_at' => isset($data['appointment_end_time'])
                    ? "{$transactionDate}T{$data['appointment_end_time']}"
                    : null,
            ])->id,
            'none' => null,
            default => null,
        };
    }

    private function formatTransaction(Transaction $transaction): array
    {
        $transaction->loadMissing(['category', 'appointment']);

        return [
            'id' => $transaction->id,
            'amount' => $transaction->amount,
            'formatted_amount' => $transaction->formatted_amount,
            'type' => $transaction->type,
            'note' => $transaction->note,
            'transaction_date' => $transaction->transaction_date->format('Y-m-d'),
            'appointment_id' => $transaction->appointment_id,
            'appointment' => $transaction->appointment ? [
                'id' => $transaction->appointment->id,
                'title' => $transaction->appointment->title,
                'start_at' => $transaction->appointment->start_at->format('Y-m-d\TH:i'),
            ] : null,
            'category' => [
                'id' => $transaction->category->id,
                'name' => $transaction->category->name,
                'icon' => $transaction->category->icon,
                'color' => $transaction->category->color,
            ],
        ];
    }
}
