{{-- ══════════════════════════════════════════════════════════════
     相機掃描 Overlay（獨立 teleport，避免與 Bottom Sheet 共用根元素）
     由 x-data="addTransaction(...)" 控制
     ══════════════════════════════════════════════════════════════ --}}
<template x-teleport="body">
    <div x-show="scannerOpen"
         class="fixed inset-0 z-[60] bg-black flex flex-col">

        {{-- 頂部控制 --}}
        <div class="flex items-center justify-between px-5 pb-3"
             style="padding-top: max(1.25rem, env(safe-area-inset-top))">
            <div>
                <p class="text-white font-semibold">掃描統一發票</p>
                <p class="text-slate-400 text-xs mt-0.5">對準發票左側較大的 QR Code</p>
            </div>
            <button @click="closeScanner()"
                    class="text-white p-2 rounded-full hover:bg-white/10 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- 相機畫面 --}}
        <div class="flex-1 relative overflow-hidden">
            <video id="invoice-scanner-video"
                   class="absolute inset-0 w-full h-full object-cover"
                   playsinline autoplay muted></video>

            {{-- 掃描框四角 --}}
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="relative w-64 h-64">
                    <span class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-indigo-400 rounded-tl-lg"></span>
                    <span class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-indigo-400 rounded-tr-lg"></span>
                    <span class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-indigo-400 rounded-bl-lg"></span>
                    <span class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-indigo-400 rounded-br-lg"></span>
                </div>
            </div>
        </div>

        {{-- 底部提示 --}}
        <div class="px-4 py-5 text-center"
             style="padding-bottom: calc(1.25rem + env(safe-area-inset-bottom))">
            <p class="text-white/60 text-sm">自動辨識後將填入金額與日期</p>
        </div>

        {{-- 隱藏 canvas（jsQR 解析用）--}}
        <canvas id="invoice-scanner-canvas" class="hidden"></canvas>
    </div>
</template>

{{-- ══════════════════════════════════════════════════════════════
     新增記帳 Bottom Sheet（獨立 teleport）
     由 x-data="addTransaction(...)" 控制
     ══════════════════════════════════════════════════════════════ --}}
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
            :style="{
                paddingBottom: 'calc(1.5rem + env(safe-area-inset-bottom))',
                transform: `translateY(${dragY}px)`,
                opacity: Math.max(0, 1 - dragY / 400),
                transition: dragging ? 'none' : 'transform 0.28s cubic-bezier(0.32,0.72,0,1), opacity 0.28s'
            }"
        >
            {{-- 拖曳把手 --}}
            <div class="mx-auto mb-4 h-1 w-10 rounded-full bg-slate-200 touch-none cursor-grab"
                 @touchstart="dragStart($event)"
                 @touchmove.prevent="dragMove($event)"
                 @touchend="dragEnd()"></div>

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

            {{-- 金額輸入 + 掃描按鈕 --}}
            <div class="mb-4 rounded-2xl bg-slate-50 px-4 py-3">
                <div class="flex items-center justify-between mb-1">
                    <label class="text-xs text-slate-400">金額</label>
                    <button @click="openScanner()"
                            class="flex items-center gap-1 rounded-lg bg-indigo-50 px-2.5 py-1
                                   text-xs font-semibold text-indigo-600 transition hover:bg-indigo-100">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        掃描發票
                    </button>
                </div>
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
