@extends('budget.layout')

@section('title', '帳目記錄')

@section('content')
<div x-data="addTransaction({{ $categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'icon' => $c->icon, 'color' => $c->color, 'type' => $c->type])->toJson() }})">

    {{-- 頂部標題 --}}
    <div class="bg-white px-5 pt-12 pb-4 shadow-sm safe-area-top">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-slate-800">帳目記錄</h1>
            {{-- 月份切換 --}}
            <div class="flex items-center gap-2 rounded-xl bg-slate-100 px-3 py-1.5">
                <form method="GET" action="{{ route('budget.history') }}">
                    <input type="month" name="month" value="{{ $month }}"
                           class="bg-transparent text-sm font-semibold text-slate-700 outline-none"
                           onchange="this.form.submit()">
                </form>
            </div>
        </div>

        {{-- 本月統計 --}}
        <div class="mt-3 flex gap-4">
            <div class="flex-1 rounded-xl bg-rose-50 px-3 py-2">
                <p class="text-xs text-rose-400">本月支出</p>
                <p class="mt-0.5 text-lg font-bold text-rose-500">-${{ number_format($monthlyExpense) }}</p>
            </div>
            <div class="flex-1 rounded-xl bg-emerald-50 px-3 py-2">
                <p class="text-xs text-emerald-500">本月收入</p>
                <p class="mt-0.5 text-lg font-bold text-emerald-600">+${{ number_format($monthlyIncome) }}</p>
            </div>
            <div class="flex-1 rounded-xl bg-slate-100 px-3 py-2">
                @php $net = $monthlyIncome - $monthlyExpense; @endphp
                <p class="text-xs text-slate-500">結餘</p>
                <p class="mt-0.5 text-lg font-bold {{ $net >= 0 ? 'text-emerald-600' : 'text-rose-500' }}">
                    {{ $net >= 0 ? '+' : '' }}${{ number_format($net) }}
                </p>
            </div>
        </div>
    </div>

    {{-- 交易清單 --}}
    <div class="px-4 py-4">
        @if ($transactions->isEmpty())
            <div class="mt-8 text-center text-slate-400">
                <p class="text-3xl">🗓️</p>
                <p class="mt-2 text-sm">這個月還沒有記錄</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($transactions as $date => $dayTransactions)
                    <div>
                        {{-- 日期分隔 --}}
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-400">
                                {{ \Carbon\Carbon::parse($date)->locale('zh_TW')->translatedFormat('n月j日 (D)') }}
                            </span>
                            <span class="text-xs text-slate-400">
                                -${{ number_format($dayTransactions->where('type','expense')->sum('amount')) }}
                            </span>
                        </div>

                        {{-- 當日交易 --}}
                        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
                            @foreach ($dayTransactions as $transaction)
                                <div x-data="{ confirmDelete: false }" class="divide-y divide-slate-50">
                                    <div class="flex items-center gap-3 px-4 py-3">
                                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-slate-100 text-xl">
                                            {{ $transaction->category->icon }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-medium text-slate-700">
                                                {{ $transaction->category->name }}
                                            </p>
                                            @if ($transaction->note)
                                                <p class="truncate text-xs text-slate-400">{{ $transaction->note }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-base font-bold {{ $transaction->type === 'expense' ? 'text-rose-500' : 'text-emerald-600' }}">
                                                {{ $transaction->formatted_amount }}
                                            </span>
                                            <button @click="confirmDelete = !confirmDelete"
                                                    class="text-slate-300 transition hover:text-rose-400">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- 確認刪除（同一 x-data 作用域內）--}}
                                    <div x-show="confirmDelete"
                                         class="flex items-center justify-between bg-rose-50 px-4 py-2">
                                        <span class="text-xs text-rose-600">確定刪除這筆記錄？</span>
                                        <div class="flex gap-2">
                                            <button @click="confirmDelete = false"
                                                    class="text-xs text-slate-500 px-2 py-1">取消</button>
                                            <button
                                                @click="
                                                    window.budgetUtils.fetchJson(
                                                        '{{ route('budget.transactions.destroy', $transaction->id) }}',
                                                        { method: 'DELETE' }
                                                    ).then(() => location.reload())
                                                "
                                                class="rounded-lg bg-rose-500 px-3 py-1 text-xs font-semibold text-white">
                                                刪除
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- 浮動新增按鈕 --}}
    <button
        @click="openSheet()"
        class="fixed right-5 z-50 flex h-14 w-14 items-center justify-center
               rounded-full bg-indigo-600 text-white text-2xl shadow-lg shadow-indigo-500/40
               transition hover:bg-indigo-700 active:scale-90"
        style="bottom: calc(5rem + env(safe-area-inset-bottom))"
    >
        +
    </button>

    {{-- 新增記帳 Bottom Sheet（共用） --}}
    @include('budget.partials.add-transaction-sheet')

</div>
@endsection
