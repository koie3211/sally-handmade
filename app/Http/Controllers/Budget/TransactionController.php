<?php

namespace App\Http\Controllers\Budget;

use App\Http\Controllers\Controller;
use App\Models\Budget\Category;
use App\Models\Budget\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth('budget')->user();
        $month = $request->input('month', now()->format('Y-m'));

        [$year, $mon] = explode('-', $month);

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

        return view('budget.history', compact(
            'transactions',
            'monthlyExpense',
            'monthlyIncome',
            'month',
            'categories',
        ));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'integer', 'exists:budget_categories,id'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999'],
            'type' => ['required', 'in:expense,income'],
            'note' => ['nullable', 'string', 'max:200'],
            'transaction_date' => ['required', 'date'],
        ]);

        $transaction = Transaction::create([
            ...$data,
            'user_id' => auth('budget')->id(),
        ]);

        $transaction->load('category');

        return response()->json([
            'data' => [
                'id' => $transaction->id,
                'amount' => $transaction->amount,
                'formatted_amount' => $transaction->formatted_amount,
                'type' => $transaction->type,
                'note' => $transaction->note,
                'transaction_date' => $transaction->transaction_date->format('Y-m-d'),
                'category' => [
                    'id' => $transaction->category->id,
                    'name' => $transaction->category->name,
                    'icon' => $transaction->category->icon,
                    'color' => $transaction->category->color,
                ],
            ],
        ], 201);
    }

    public function update(Request $request, Transaction $transaction): JsonResponse
    {
        abort_if($transaction->user_id !== auth('budget')->id(), 403);

        $data = $request->validate([
            'category_id' => ['sometimes', 'integer', 'exists:budget_categories,id'],
            'amount' => ['sometimes', 'numeric', 'min:0.01', 'max:9999999'],
            'type' => ['sometimes', 'in:expense,income'],
            'note' => ['nullable', 'string', 'max:200'],
            'transaction_date' => ['sometimes', 'date'],
        ]);

        $transaction->update($data);
        $transaction->load('category');

        return response()->json([
            'data' => [
                'id' => $transaction->id,
                'amount' => $transaction->amount,
                'formatted_amount' => $transaction->formatted_amount,
                'type' => $transaction->type,
                'note' => $transaction->note,
                'transaction_date' => $transaction->transaction_date->format('Y-m-d'),
                'category' => [
                    'id' => $transaction->category->id,
                    'name' => $transaction->category->name,
                    'icon' => $transaction->category->icon,
                    'color' => $transaction->category->color,
                ],
            ],
        ]);
    }

    public function destroy(Transaction $transaction): JsonResponse
    {
        abort_if($transaction->user_id !== auth('budget')->id(), 403);

        $transaction->delete();

        return response()->json(null, 204);
    }
}
