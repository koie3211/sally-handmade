@extends('budget.layout')

@section('title', 'AI 消費建議')

@section('content')
<div x-data="aiPage()" x-init="loadSuggestions()">

    {{-- 頂部標題 --}}
    <div class="bg-white px-5 pt-12 pb-4 shadow-sm safe-area-top">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-800">AI 消費建議</h1>
                <p class="text-xs text-slate-400 mt-0.5">根據近 30 天消費分析</p>
            </div>
            <button @click="loadSuggestions()" :disabled="loading"
                    class="flex items-center gap-1.5 rounded-xl bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-600 transition
                           hover:bg-indigo-100 disabled:opacity-50">
                <svg class="h-3.5 w-3.5" :class="loading ? 'animate-spin' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                重新生成
            </button>
        </div>
    </div>

    <div class="px-4 py-4">

        {{-- 載入中 --}}
        <div x-show="loading" class="space-y-3">
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 animate-pulse rounded-full bg-indigo-100 flex items-center justify-center text-xl">🤔</div>
                    <div class="flex-1 space-y-2">
                        <div class="h-3 w-3/4 animate-pulse rounded-full bg-slate-200"></div>
                        <div class="h-3 w-1/2 animate-pulse rounded-full bg-slate-200"></div>
                    </div>
                </div>
                <div class="mt-4 space-y-2">
                    <div class="h-3 animate-pulse rounded-full bg-slate-100"></div>
                    <div class="h-3 w-5/6 animate-pulse rounded-full bg-slate-100"></div>
                    <div class="h-3 w-4/6 animate-pulse rounded-full bg-slate-100"></div>
                </div>
            </div>
            <p class="text-center text-xs text-slate-400">AI 正在分析您的消費模式…</p>
        </div>

        {{-- 錯誤 --}}
        <div x-show="error && !loading" class="rounded-2xl bg-rose-50 p-5 text-center">
            <p class="text-2xl">😓</p>
            <p class="mt-2 text-sm text-rose-600" x-text="error"></p>
            <button @click="loadSuggestions()" class="mt-3 rounded-xl bg-rose-500 px-4 py-2 text-sm font-semibold text-white">
                重試
            </button>
        </div>

        {{-- AI 建議結果 --}}
        <div x-show="!loading && !error && suggestions" class="space-y-4">

            {{-- AI 頭像卡 --}}
            <div class="rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 p-5 text-white shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-white/20 text-2xl">🤖</div>
                    <div>
                        <p class="font-semibold">Sally AI 理財顧問</p>
                        <p class="text-xs text-indigo-200">根據近 30 天的消費資料分析</p>
                    </div>
                </div>
            </div>

            {{-- 建議內容 --}}
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100 slide-up">
                <div class="prose prose-sm max-w-none text-slate-700 leading-relaxed whitespace-pre-line"
                     x-text="suggestions">
                </div>
            </div>

            <p class="text-center text-xs text-slate-400">
                建議由 AI 生成，僅供參考，請依實際情況判斷
            </p>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function aiPage() {
    return {
        loading: false,
        suggestions: null,
        error: null,

        async loadSuggestions() {
            this.loading = true
            this.error = null
            this.suggestions = null

            try {
                const res = await window.budgetUtils.fetchJson('/api/ai/suggest')
                this.suggestions = res.data.suggestions
            } catch (e) {
                this.error = e.message || '分析失敗，請稍後再試'
            } finally {
                this.loading = false
            }
        },
    }
}
</script>
@endpush
