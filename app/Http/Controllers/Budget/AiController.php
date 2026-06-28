<?php

namespace App\Http\Controllers\Budget;

use App\Ai\Agents\Budget\SpendingAdvisorAgent;
use App\Http\Controllers\Controller;
use App\Models\Budget\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AiController extends Controller
{
    public function index(): View
    {
        return view('budget.ai');
    }

    public function suggest(): JsonResponse
    {
        $user = auth('budget')->user();

        $transactions = Transaction::with('category')
            ->where('user_id', $user->id)
            ->where('transaction_date', '>=', now()->subDays(30)->toDateString())
            ->get();

        if ($transactions->isEmpty()) {
            return response()->json([
                'data' => [
                    'suggestions' => '目前尚無消費資料，請先新增一些記錄後再來查看 AI 建議！',
                ],
            ]);
        }

        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $totalIncome = $transactions->where('type', 'income')->sum('amount');

        $byCategory = $transactions
            ->where('type', 'expense')
            ->groupBy('category_id')
            ->map(fn ($g) => [
                'category' => $g->first()->category->name,
                'total' => $g->sum('amount'),
                'count' => $g->count(),
                'avg_per_transaction' => round($g->sum('amount') / $g->count(), 0),
            ])
            ->sortByDesc('total')
            ->values();

        $summary = "近 30 天消費摘要：\n";
        $summary .= "總支出：$" . number_format($totalExpense) . "\n";
        $summary .= "總收入：$" . number_format($totalIncome) . "\n";
        $summary .= "結餘：$" . number_format($totalIncome - $totalExpense) . "\n\n";
        $summary .= "各分類支出：\n";

        foreach ($byCategory as $item) {
            $summary .= "- {$item['category']}：共 {$item['count']} 筆，合計 $" . number_format($item['total']) . "（平均每筆 $" . number_format($item['avg_per_transaction']) . "）\n";
        }

        $response = (new SpendingAdvisorAgent)->prompt($summary);

        return response()->json([
            'data' => [
                'suggestions' => $response->text,
            ],
        ]);
    }
}
