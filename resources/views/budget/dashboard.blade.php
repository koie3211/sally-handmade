@extends('budget.layout')

@section('title', '今日消費')

@section('content')
<div x-data="addTransaction({{ $categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'icon' => $c->icon, 'color' => $c->color, 'type' => $c->type])->toJson() }})" x-init="openSheet()">

    {{-- 頂部：今日概覽 --}}
    <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 px-6 pb-8 pt-12 text-white safe-area-top">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-indigo-200">{{ now()->locale('zh_TW')->translatedFormat('n月j日，l') }}</p>
                <p class="mt-0.5 text-xs text-indigo-300">{{ auth('budget')->user()->name }}</p>
            </div>
            <a href="{{ route('budget.settings') }}" class="rounded-full p-2 text-indigo-200 hover:bg-indigo-700/50 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </a>
        </div>

        <div class="mt-6 text-center">
            <p class="text-sm text-indigo-200">今日支出</p>
            <p class="mt-1 text-5xl font-bold tracking-tight">
                ${{ number_format($todayExpense) }}
            </p>
            @if ($todayIncome > 0)
                <p class="mt-2 text-sm text-emerald-300">+ ${{ number_format($todayIncome) }} 收入</p>
            @endif
        </div>
    </div>

    {{-- 快速統計卡片 --}}
    <div class="mx-4 -mt-4 grid grid-cols-2 gap-3">
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
            <p class="text-xs text-slate-500">今日結餘</p>
            @php $net = $todayIncome - $todayExpense; @endphp
            <p class="mt-1 text-xl font-bold {{ $net >= 0 ? 'text-emerald-600' : 'text-rose-500' }}">
                {{ $net >= 0 ? '+' : '' }}${{ number_format($net) }}
            </p>
        </div>
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
            <p class="text-xs text-slate-500">今日筆數</p>
            <p class="mt-1 text-xl font-bold text-slate-700">
                {{ $recentTransactions->where('transaction_date', now()->toDateString())->count() }} 筆
            </p>
        </div>
    </div>

    {{-- 最近記錄 --}}
    <div class="mx-4 mt-6">
        <h2 class="mb-3 text-sm font-semibold text-slate-500 uppercase tracking-wide">最近記錄</h2>

        @if ($recentTransactions->isEmpty())
            <div class="rounded-2xl bg-white p-8 text-center text-slate-400 shadow-sm ring-1 ring-slate-100">
                <p class="text-3xl">📝</p>
                <p class="mt-2 text-sm">還沒有記錄，點下方 + 開始記帳</p>
            </div>
        @else
            <div class="space-y-2">
                @foreach ($recentTransactions as $transaction)
                    <div class="flex items-center gap-3 rounded-2xl bg-white px-4 py-3 shadow-sm ring-1 ring-slate-100 slide-up">
                        {{-- 分類圖示 --}}
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-slate-100 text-xl">
                            {{ $transaction->category->icon }}
                        </div>
                        {{-- 內容 --}}
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-slate-700">
                                {{ $transaction->category->name }}
                                @if ($transaction->note)
                                    <span class="font-normal text-slate-400">· {{ $transaction->note }}</span>
                                @endif
                            </p>
                            <p class="text-xs text-slate-400">
                                {{ $transaction->transaction_date->format('m/d') }}
                            </p>
                        </div>
                        {{-- 金額 --}}
                        <span class="text-base font-bold {{ $transaction->type === 'expense' ? 'text-rose-500' : 'text-emerald-600' }}">
                            {{ $transaction->formatted_amount }}
                        </span>
                    </div>
                @endforeach
            </div>

            @if ($recentTransactions->count() >= 10)
                <a href="{{ route('budget.history') }}"
                   class="mt-3 block text-center text-sm text-indigo-600 font-medium py-2">
                    查看全部記錄 →
                </a>
            @endif
        @endif
    </div>

    {{-- 浮動新增按鈕 --}}
    <button
        @click="openSheet()"
        class="fixed bottom-24 right-5 z-50 flex h-14 w-14 items-center justify-center
               rounded-full bg-indigo-600 text-white text-2xl shadow-lg shadow-indigo-500/40
               transition hover:bg-indigo-700 active:scale-90"
        style="bottom: calc(5rem + env(safe-area-inset-bottom))"
    >
        +
    </button>

    {{-- 新增記帳 Bottom Sheet --}}
    @include('budget.partials.add-transaction-sheet')

</div>
@endsection
