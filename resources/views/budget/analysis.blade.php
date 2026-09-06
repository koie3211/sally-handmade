@extends('budget.layout')

@section('title', '消費分析')

@section('content')
<div x-data="analysisPage()" x-init="loadData()">

    {{-- 頂部標題 --}}
    <div class="bg-white px-5 pb-4 shadow-sm safe-area-top">
        <div class="flex items-center justify-between">
            <button @click="prevMonth()"
                    class="flex h-9 w-9 items-center justify-center rounded-full text-slate-400 hover:bg-slate-100 transition active:scale-90">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <div class="text-center">
                <h1 class="text-xl font-bold text-slate-800" x-text="monthLabel"></h1>
                <p class="mt-0.5 text-xs text-slate-400">消費分析</p>
            </div>

            <button @click="nextMonth()"
                    class="flex h-9 w-9 items-center justify-center rounded-full text-slate-400 hover:bg-slate-100 transition active:scale-90">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
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

        {{-- 每日收支月曆 --}}
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
            <div class="border-b border-slate-100 px-4 py-3">
                <h3 class="text-sm font-semibold text-slate-600">每日收支分布</h3>
            </div>

            <div class="grid grid-cols-7 border-b border-slate-100">
                <template x-for="(weekday, index) in ['日','一','二','三','四','五','六']" :key="weekday">
                    <div class="py-2 text-center text-xs font-semibold"
                         :class="index === 0 ? 'text-rose-400' : (index === 6 ? 'text-slate-400' : 'text-slate-500')"
                         x-text="weekday">
                    </div>
                </template>
            </div>

            <div class="grid grid-cols-7 divide-x divide-slate-100">
                <template x-for="(day, index) in calendarDays" :key="index">
                    <div class="min-h-[5rem] border-b border-slate-100 px-1 py-1 transition-colors"
                         :class="{
                             'pointer-events-none opacity-0': !day,
                             'cursor-pointer bg-indigo-50': day && selectedDate === day.date,
                             'cursor-pointer bg-white': day && selectedDate !== day.date,
                         }"
                         @click="day && selectDate(day.date)">
                        <template x-if="day">
                            <div>
                                <div class="flex justify-center">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full text-xs font-semibold"
                                          :class="day.isToday
                                              ? 'bg-indigo-600 text-white'
                                              : (day.weekday === 0 ? 'text-rose-400' : 'text-slate-600')"
                                          x-text="day.day">
                                    </span>
                                </div>
                                <div class="mt-1 space-y-0.5 text-center">
                                    <p x-show="day.income > 0"
                                       class="whitespace-nowrap text-[9px] font-semibold leading-tight tracking-tight text-emerald-600"
                                       x-text="'+' + fmt(day.income)">
                                    </p>
                                    <p x-show="day.expense > 0"
                                       class="whitespace-nowrap text-[9px] font-semibold leading-tight tracking-tight text-rose-500"
                                       x-text="'-' + fmt(day.expense)">
                                    </p>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            {{-- 選定日期明細 --}}
            <div x-show="selectedDate" class="border-t border-slate-100 bg-slate-50 px-4 py-4">
                <div class="mb-3 flex items-start justify-between gap-3">
                    <div>
                        <h4 class="text-sm font-semibold text-slate-700" x-text="selectedDateLabel"></h4>
                        <p class="mt-1 text-xs text-slate-400">
                            <span class="text-emerald-600" x-text="'收入 +' + fmt(selectedDayIncome)"></span>
                            <span class="mx-1">·</span>
                            <span class="text-rose-500" x-text="'支出 -' + fmt(selectedDayExpense)"></span>
                        </p>
                    </div>
                    <button @click="selectedDate = null" class="text-xs text-slate-400">關閉</button>
                </div>

                <template x-if="dayTransactions.length === 0">
                    <p class="rounded-xl bg-white px-4 py-5 text-center text-sm text-slate-400">
                        這天沒有收支記錄
                    </p>
                </template>

                <div class="space-y-2">
                    <template x-for="transaction in dayTransactions" :key="transaction.id">
                        <div class="flex items-center gap-3 rounded-xl bg-white px-3 py-2.5 shadow-sm ring-1 ring-slate-100">
                            <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-slate-100 text-lg"
                                 x-text="transaction.category.icon">
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-slate-700"
                                   x-text="transaction.category.name">
                                </p>
                                <p x-show="transaction.note"
                                   class="truncate text-xs text-slate-400"
                                   x-text="transaction.note">
                                </p>
                            </div>
                            <span class="flex-shrink-0 text-sm font-bold"
                                  :class="transaction.type === 'expense' ? 'text-rose-500' : 'text-emerald-600'"
                                  x-text="transaction.formatted_amount">
                            </span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- 近 6 個月趨勢 --}}
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
            <h3 class="mb-3 text-sm font-semibold text-slate-600">近 6 個月趨勢</h3>
            <div class="relative h-48 overflow-hidden">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        {{-- 分類支出佔比 --}}
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
            <h3 class="mb-3 text-sm font-semibold text-slate-600">支出分類佔比</h3>
            <template x-if="data.expense_by_category && data.expense_by_category.length > 0">
                <div>
                    <div class="relative mx-auto h-44 w-44 overflow-hidden">
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
    const today = new Date()

    return {
        loading: false,
        currentYear: today.getFullYear(),
        currentMonth: today.getMonth() + 1,
        calendarDays: [],
        selectedDate: null,
        data: {
            total_expense: 0,
            total_income: 0,
            net: 0,
            expense_by_category: [],
            trend: [],
            transactions: [],
        },
        trendChart: null,
        categoryChart: null,

        get monthLabel() {
            return `${this.currentYear} 年 ${this.currentMonth} 月`
        },

        get dayTransactions() {
            if (!this.selectedDate) return []
            return this.data.transactions.filter(transaction =>
                transaction.transaction_date === this.selectedDate
            )
        },

        get selectedDateLabel() {
            if (!this.selectedDate) return ''
            const date = new Date(this.selectedDate + 'T00:00:00')
            const weekdays = ['日', '一', '二', '三', '四', '五', '六']
            return `${date.getMonth() + 1} 月 ${date.getDate()} 日（${weekdays[date.getDay()]}）`
        },

        get selectedDayIncome() {
            return this.dayTransactions
                .filter(transaction => transaction.type === 'income')
                .reduce((total, transaction) => total + Number(transaction.amount), 0)
        },

        get selectedDayExpense() {
            return this.dayTransactions
                .filter(transaction => transaction.type === 'expense')
                .reduce((total, transaction) => total + Number(transaction.amount), 0)
        },

        fmt(num) {
            return Number(num).toLocaleString('zh-TW')
        },

        monthKey() {
            return `${this.currentYear}-${String(this.currentMonth).padStart(2, '0')}`
        },

        localDateKey(date) {
            return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`
        },

        async prevMonth() {
            if (this.currentMonth === 1) {
                this.currentYear--
                this.currentMonth = 12
            } else {
                this.currentMonth--
            }
            this.selectedDate = null
            await this.loadData()
        },

        async nextMonth() {
            if (this.currentMonth === 12) {
                this.currentYear++
                this.currentMonth = 1
            } else {
                this.currentMonth++
            }
            this.selectedDate = null
            await this.loadData()
        },

        selectDate(date) {
            this.selectedDate = this.selectedDate === date ? null : date
        },

        buildCalendar() {
            const firstDay = new Date(this.currentYear, this.currentMonth - 1, 1)
            const lastDay = new Date(this.currentYear, this.currentMonth, 0)
            const todayKey = this.localDateKey(new Date())
            const transactionsByDate = {}

            this.data.transactions.forEach(transaction => {
                const date = transaction.transaction_date
                if (!transactionsByDate[date]) transactionsByDate[date] = []
                transactionsByDate[date].push(transaction)
            })

            const days = Array(firstDay.getDay()).fill(null)
            for (let day = 1; day <= lastDay.getDate(); day++) {
                const date = `${this.currentYear}-${String(this.currentMonth).padStart(2, '0')}-${String(day).padStart(2, '0')}`
                const transactions = transactionsByDate[date] ?? []
                days.push({
                    day,
                    date,
                    weekday: new Date(this.currentYear, this.currentMonth - 1, day).getDay(),
                    isToday: date === todayKey,
                    income: transactions
                        .filter(transaction => transaction.type === 'income')
                        .reduce((total, transaction) => total + Number(transaction.amount), 0),
                    expense: transactions
                        .filter(transaction => transaction.type === 'expense')
                        .reduce((total, transaction) => total + Number(transaction.amount), 0),
                })
            }
            while (days.length % 7 !== 0) days.push(null)

            this.calendarDays = days
        },

        async loadData() {
            this.loading = true
            let loaded = false
            try {
                const res = await window.budgetUtils.fetchJson(`/api/analysis/monthly?month=${this.monthKey()}`)
                this.data = res.data
                this.buildCalendar()
                loaded = true
            } catch (e) {
                console.error(e)
            } finally {
                this.loading = false
            }

            if (loaded) {
                await this.$nextTick()
                this.renderCharts()
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
