<?php

namespace App\Http\Controllers\Budget;

use App\Http\Controllers\Controller;
use App\Models\Budget\Transaction;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalysisController extends Controller
{
    public function index(): View
    {
        return view('budget.analysis');
    }

    public function monthly(Request $request): JsonResponse
    {
        $data = $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
        ]);

        $user = auth('budget')->user();
        $month = Carbon::createFromFormat('Y-m', $data['month'] ?? now()->format('Y-m'))->startOfMonth();

        $transactions = Transaction::with('category')
            ->where('user_id', $user->id)
            ->whereYear('transaction_date', $month->year)
            ->whereMonth('transaction_date', $month->month)
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $totalIncome = $transactions->where('type', 'income')->sum('amount');

        $expenseByCategory = $transactions
            ->where('type', 'expense')
            ->groupBy('category_id')
            ->map(fn ($group) => [
                'name' => $group->first()->category->name,
                'icon' => $group->first()->category->icon,
                'color' => $group->first()->category->color,
                'total' => $group->sum('amount'),
                'count' => $group->count(),
            ])
            ->sortByDesc('total')
            ->values();

        // 近 6 個月趨勢
        $trend = collect(range(5, 0))->map(function ($monthsAgo) use ($user, $month) {
            $date = $month->copy()->subMonths($monthsAgo);
            $rows = Transaction::where('user_id', $user->id)
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->get();

            return [
                'label' => $date->format('m月'),
                'expense' => $rows->where('type', 'expense')->sum('amount'),
                'income' => $rows->where('type', 'income')->sum('amount'),
            ];
        });

        return response()->json([
            'data' => [
                'total_expense' => $totalExpense,
                'total_income' => $totalIncome,
                'net' => $totalIncome - $totalExpense,
                'expense_by_category' => $expenseByCategory,
                'trend' => $trend,
                'transactions' => $transactions->map(fn (Transaction $transaction) => [
                    'id' => $transaction->id,
                    'transaction_date' => $transaction->transaction_date->format('Y-m-d'),
                    'type' => $transaction->type,
                    'amount' => (float) $transaction->amount,
                    'formatted_amount' => $transaction->formatted_amount,
                    'note' => $transaction->note,
                    'category' => [
                        'name' => $transaction->category->name,
                        'icon' => $transaction->category->icon,
                        'color' => $transaction->category->color,
                    ],
                ])->values(),
            ],
        ]);
    }
}
