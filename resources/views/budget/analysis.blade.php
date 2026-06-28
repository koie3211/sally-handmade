@extends('budget.layout')

@section('title', '消費分析')

@section('content')
<div x-data="analysisPage()" x-init="loadData()">

    {{-- 頂部標題 --}}
    <div class="bg-white px-5 pt-12 pb-4 shadow-sm safe-area-top">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-slate-800">消費分析</h1>
            <div class="flex items-center gap-2 rounded-xl bg-slate-100 px-3 py-1.5">
                <input type="month" x-model="month" @change="loadData()"
                       class="bg-transparent text-sm font-semibold text-slate-700 outline-none">
            </div>
        </div>
    </div>

    {{-- 載入中 --}}
    <div x-show="loading" class="flex items-center justify-center py-20 text-slate-400">
        <svg class="h-8 w-8 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
    </div>

    <div x-show="!loading" class="px-4 py-4 space-y-4">

        {{-- 本月總覽卡片 --}}
        <div class="grid grid-cols-3 gap-3">
            <div class="rounded-2xl bg-white p-3 shadow-sm ring-1 ring-slate-100 text-center">
                <p class="text-xs text-slate-400">支出</p>
                <p class="mt-1 text-base font-bold text-rose-500" x-text="'$'+fmt(data.total_expense)"></p>
            </div>
            <div class="rounded-2xl bg-white p-3 shadow-sm ring-1 ring-slate-100 text-center">
                <p class="text-xs text-slate-400">收入</p>
                <p class="mt-1 text-base font-bold text-emerald-600" x-text="'$'+fmt(data.total_income)"></p>
            </div>
            <div class="rounded-2xl bg-white p-3 shadow-sm ring-1 ring-slate-100 text-center">
                <p class="text-xs text-slate-400">結餘</p>
                <p class="mt-1 text-base font-bold"
                   :class="data.net >= 0 ? 'text-emerald-600' : 'text-rose-500'"
                   x-text="(data.net >= 0 ? '+' : '') + '$' + fmt(Math.abs(data.net))"></p>
            </div>
        </div>

        {{-- 近 6 個月趨勢 --}}
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
            <h3 class="mb-3 text-sm font-semibold text-slate-600">近 6 個月趨勢</h3>
            <div class="h-48">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        {{-- 分類支出佔比 --}}
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
            <h3 class="mb-3 text-sm font-semibold text-slate-600">支出分類佔比</h3>
            <template x-if="data.expense_by_category && data.expense_by_category.length > 0">
                <div>
                    <div class="mx-auto h-44 w-44">
                        <canvas id="categoryChart"></canvas>
                    </div>
                    <div class="mt-4 space-y-2">
                        <template x-for="cat in data.expense_by_category" :key="cat.name">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-base" x-text="cat.icon"></span>
                                    <span class="text-sm text-slate-600" x-text="cat.name"></span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="h-1.5 w-20 overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full bg-indigo-500 transition-all duration-500"
                                             :style="`width:${data.total_expense > 0 ? (cat.total/data.total_expense*100).toFixed(0) : 0}%`">
                                        </div>
                                    </div>
                                    <span class="w-16 text-right text-sm font-semibold text-slate-700" x-text="'$'+fmt(cat.total)"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
            <template x-if="!data.expense_by_category || data.expense_by_category.length === 0">
                <p class="py-6 text-center text-sm text-slate-400">本月尚無支出記錄</p>
            </template>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function analysisPage() {
    return {
        loading: false,
        month: '{{ now()->format('Y-m') }}',
        data: {
            total_expense: 0,
            total_income: 0,
            net: 0,
            expense_by_category: [],
            trend: [],
        },
        trendChart: null,
        categoryChart: null,

        fmt(num) {
            return Number(num).toLocaleString('zh-TW')
        },

        async loadData() {
            this.loading = true
            try {
                const res = await window.budgetUtils.fetchJson(`/api/analysis/monthly?month=${this.month}`)
                this.data = res.data
                await this.$nextTick()
                this.renderCharts()
            } catch (e) {
                console.error(e)
            } finally {
                this.loading = false
            }
        },

        renderCharts() {
            // 趨勢圖
            const trendEl = document.getElementById('trendChart')
            if (trendEl) {
                if (this.trendChart) this.trendChart.destroy()
                this.trendChart = new Chart(trendEl, {
                    type: 'bar',
                    data: {
                        labels: this.data.trend.map(t => t.label),
                        datasets: [
                            {
                                label: '支出',
                                data: this.data.trend.map(t => t.expense),
                                backgroundColor: 'rgba(244,63,94,0.7)',
                                borderRadius: 6,
                            },
                            {
                                label: '收入',
                                data: this.data.trend.map(t => t.income),
                                backgroundColor: 'rgba(16,185,129,0.7)',
                                borderRadius: 6,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } },
                        scales: {
                            x: { grid: { display: false } },
                            y: { grid: { color: '#f1f5f9' }, ticks: { maxTicksLimit: 4 } },
                        },
                    },
                })
            }

            // 分類圓餅圖
            const catEl = document.getElementById('categoryChart')
            if (catEl && this.data.expense_by_category?.length > 0) {
                if (this.categoryChart) this.categoryChart.destroy()
                const colors = [
                    '#6366f1','#f43f5e','#f59e0b','#10b981',
                    '#3b82f6','#8b5cf6','#ec4899','#14b8a6',
                ]
                this.categoryChart = new Chart(catEl, {
                    type: 'doughnut',
                    data: {
                        labels: this.data.expense_by_category.map(c => c.name),
                        datasets: [{
                            data: this.data.expense_by_category.map(c => c.total),
                            backgroundColor: colors,
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: { legend: { display: false } },
                    },
                })
            }
        },
    }
}
</script>
@endpush
