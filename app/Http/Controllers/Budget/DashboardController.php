<?php

namespace App\Http\Controllers\Budget;

use App\Http\Controllers\Controller;
use App\Models\Budget\Category;
use App\Models\Budget\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = auth('budget')->user();
        $today = now()->toDateString();

        $todayTransactions = Transaction::with('category')
            ->where('user_id', $user->id)
            ->where('transaction_date', $today)
            ->latest()
            ->get();

        $todayExpense = $todayTransactions->where('type', 'expense')->sum('amount');
        $todayIncome = $todayTransactions->where('type', 'income')->sum('amount');

        $recentTransactions = Transaction::with('category')
            ->where('user_id', $user->id)
            ->latest('transaction_date')
            ->latest()
            ->limit(10)
            ->get();

        $categories = Category::forUser($user->id);

        return view('budget.dashboard', compact(
            'todayExpense',
            'todayIncome',
            'recentTransactions',
            'categories',
        ));
    }
}
