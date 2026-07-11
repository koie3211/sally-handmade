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

    {{-- 操作提示 --}}
    <p class="px-5 pt-3 pb-0 text-xs text-slate-400">← 左滑編輯　右滑刪除 →</p>

    {{-- 交易清單 --}}
    <div class="px-4 py-3">
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
                        <div class="overflow-hidden rounded-2xl shadow-sm ring-1 ring-slate-100">
                            @foreach ($dayTransactions as $transaction)
                                @php
                                    $txData = [
                                        'id'               => $transaction->id,
                                        'type'             => $transaction->type,
                                        'amount'           => (string) $transaction->amount,
                                        'category_id'      => $transaction->category_id,
                                        'note'             => $transaction->note,
                                        'transaction_date' => $transaction->transaction_date->format('Y-m-d'),
                                    ];
                                @endphp

                                {{-- 滑動外容器 --}}
                                <div class="relative {{ !$loop->first ? 'border-t border-slate-50' : '' }}"
                                     style="min-height: 60px">

                                    {{-- 背景動作層 --}}
                                    <div class="absolute inset-0 flex overflow-hidden rounded-none">
                                        {{-- 右滑顯示：刪除（左側紅色）--}}
                                        <button
                                            class="flex w-20 flex-shrink-0 flex-col items-center justify-center gap-1 bg-rose-500 text-white"
                                            @click="confirmDeleteId = {{ $transaction->id }}; resetSwipe({{ $transaction->id }})">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            <span class="text-xs font-semibold">刪除</span>
                                        </button>

                                        <div class="flex-1"></div>

                                        {{-- 左滑顯示：編輯（右側藍色）--}}
                                        <button
                                            class="flex w-20 flex-shrink-0 flex-col items-center justify-center gap-1 bg-indigo-500 text-white"
                                            @click="openEditSheet({{ json_encode($txData) }}); resetSwipe({{ $transaction->id }})">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            <span class="text-xs font-semibold">編輯</span>
                                        </button>
                                    </div>

                                    {{-- 前景交易列（可滑動）--}}
                                    <div class="relative flex items-center gap-3 bg-white px-4 py-3 select-none"
                                         :style="{
                                             transform: `translateX(${swipeX({{ $transaction->id }})}px)`,
                                             transition: swipeDragging({{ $transaction->id }}) ? 'none' : 'transform 0.25s cubic-bezier(0.25,0.46,0.45,0.94)'
                                         }"
                                         @touchstart="swipeStart({{ $transaction->id }}, $event)"
                                         @touchmove.prevent="swipeMove({{ $transaction->id }}, $event)"
                                         @touchend="swipeEnd({{ $transaction->id }}, {{ json_encode($txData) }})"
                                         @click="resetAllSwipe()">

                                        {{-- 分類圖示 --}}
                                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-slate-100 text-xl">
                                            {{ $transaction->category->icon }}
                                        </div>

                                        {{-- 內容 --}}
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-medium text-slate-700">
                                                {{ $transaction->category->name }}
                                            </p>
                                            @if ($transaction->note)
                                                <p class="truncate text-xs text-slate-400">{{ $transaction->note }}</p>
                                            @endif
                                        </div>

                                        {{-- 金額 --}}
                                        <span class="text-base font-bold {{ $transaction->type === 'expense' ? 'text-rose-500' : 'text-emerald-600' }}">
                                            {{ $transaction->formatted_amount }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ── 刪除確認彈窗（共用）────────────────────── --}}
    <template x-teleport="body">
        <div x-show="confirmDeleteId !== null"
             x-transition:enter="fade-in"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[70] flex items-end justify-center bg-black/40 px-4"
             style="padding-bottom: calc(2rem + env(safe-area-inset-bottom))"
             @click.self="confirmDeleteId = null">

            <div x-show="confirmDeleteId !== null"
                 x-transition:enter="sheet-slide-up"
                 class="w-full max-w-sm rounded-2xl bg-white p-5 shadow-2xl">

                <div class="mb-4 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-rose-100 text-rose-500">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">確認刪除</p>
                        <p class="text-xs text-slate-400">刪除後無法復原</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button @click="confirmDeleteId = null"
                            class="flex-1 rounded-xl border border-slate-200 py-3 text-sm font-semibold text-slate-500 transition hover:bg-slate-50">
                        取消
                    </button>
                    <button @click="performDelete()"
                            :disabled="deleting"
                            class="flex-1 rounded-xl bg-rose-500 py-3 text-sm font-semibold text-white shadow-sm
                                   transition disabled:opacity-50 active:scale-95 hover:bg-rose-600">
                        <span x-show="!deleting">刪除</span>
                        <span x-show="deleting">刪除中…</span>
                    </button>
                </div>
            </div>
        </div>
    </template>

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

    {{-- 新增 / 編輯記帳 Bottom Sheet --}}
    @include('budget.partials.add-transaction-sheet')

</div>
@endsection
