{{-- 新增記帳 Bottom Sheet（由 x-data="addTransaction(...)" 控制）--}}
<template x-teleport="body">
    <div
        x-show="open"
        x-transition:enter="fade-in"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-black/40"
        @click.self="open = false"
    >
        <div
            x-show="open"
            x-transition:enter="sheet-slide-up"
            class="absolute bottom-0 left-0 right-0 rounded-t-3xl bg-white px-5 pt-4 shadow-2xl"
            style="padding-bottom: calc(1.5rem + env(safe-area-inset-bottom))"
        >
            <div class="mx-auto mb-4 h-1 w-10 rounded-full bg-slate-200"></div>

            <h2 class="mb-4 text-center text-base font-semibold text-slate-700">新增記帳</h2>

            {{-- 支出 / 收入 切換 --}}
            <div class="mb-4 flex rounded-xl bg-slate-100 p-1 gap-1">
                <button @click="type='expense'; categoryId=filteredCategories[0]?.id"
                        :class="type==='expense' ? 'bg-white text-rose-500 shadow-sm' : 'text-slate-500'"
                        class="flex-1 rounded-lg py-2 text-sm font-semibold transition">
                    支出
                </button>
                <button @click="type='income'; categoryId=filteredCategories[0]?.id"
                        :class="type==='income' ? 'bg-white text-emerald-600 shadow-sm' : 'text-slate-500'"
                        class="flex-1 rounded-lg py-2 text-sm font-semibold transition">
                    收入
                </button>
            </div>

            {{-- 金額輸入 --}}
            <div class="mb-4 rounded-2xl bg-slate-50 px-4 py-3">
                <label class="mb-1 block text-xs text-slate-400">金額</label>
                <div class="flex items-baseline gap-1">
                    <span class="text-xl text-slate-400">$</span>
                    <input
                        x-model="amount"
                        type="number"
                        inputmode="decimal"
                        min="0"
                        step="1"
                        placeholder="0"
                        class="amount-input w-full bg-transparent outline-none placeholder-slate-300"
                        :class="type==='expense' ? 'text-rose-500' : 'text-emerald-600'"
                    >
                </div>
            </div>

            {{-- 分類選擇 --}}
            <div class="mb-4">
                <p class="mb-2 text-xs text-slate-400">分類</p>
                <div class="grid grid-cols-4 gap-2">
                    <template x-for="cat in filteredCategories" :key="cat.id">
                        <button
                            @click="categoryId = cat.id"
                            :class="categoryId === cat.id ? 'ring-2 ring-indigo-500 bg-indigo-50' : 'bg-slate-50'"
                            class="flex flex-col items-center gap-1 rounded-xl p-2 transition"
                        >
                            <span class="text-xl" x-text="cat.icon"></span>
                            <span class="text-xs text-slate-600 truncate w-full text-center" x-text="cat.name"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- 日期 + 備註 --}}
            <div class="mb-5 grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-slate-50 px-3 py-2">
                    <label class="mb-0.5 block text-xs text-slate-400">日期</label>
                    <input type="date" x-model="date"
                           class="w-full bg-transparent text-sm font-medium text-slate-700 outline-none">
                </div>
                <div class="rounded-xl bg-slate-50 px-3 py-2">
                    <label class="mb-0.5 block text-xs text-slate-400">備註</label>
                    <input type="text" x-model="note" maxlength="50" placeholder="選填"
                           class="w-full bg-transparent text-sm text-slate-700 outline-none placeholder-slate-300">
                </div>
            </div>

            {{-- 送出 --}}
            <div class="flex gap-3">
                <button @click="open=false"
                        class="flex-1 rounded-xl border border-slate-200 py-3 text-sm font-semibold text-slate-500 transition hover:bg-slate-50">
                    取消
                </button>
                <button @click="submit()" :disabled="loading || !amount || !categoryId"
                        class="flex-1 rounded-xl bg-indigo-600 py-3 text-sm font-semibold text-white shadow-sm
                               transition disabled:opacity-50 active:scale-95 hover:bg-indigo-700">
                    <span x-show="!loading">新增</span>
                    <span x-show="loading">新增中…</span>
                </button>
            </div>
        </div>
    </div>
</template>
