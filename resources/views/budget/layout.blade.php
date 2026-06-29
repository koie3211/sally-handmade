<!DOCTYPE html>
<html lang="zh-TW" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '荷包') · 家庭記帳</title>

    {{-- PWA --}}
    <link rel="manifest" href="/budget/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="荷包">
    <link rel="apple-touch-icon" href="/budget/icon-192.png">

    {{-- Tailwind CSS Play CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#4f46e5', light: '#818cf8' },
                    }
                }
            }
        }
    </script>

    {{-- 自訂 CSS --}}
    <style>
        .safe-area-bottom { padding-bottom: env(safe-area-inset-bottom); }
        .safe-area-top    { padding-top:    env(safe-area-inset-top);    }
        .nav-bar          { height: calc(4rem + env(safe-area-inset-bottom)); }
        .amount-input     { font-size: 2.5rem; font-weight: 700; letter-spacing: -0.02em; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0);    }
        }
        .slide-up { animation: slideUp 0.3s ease-out; }

        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        .fade-in { animation: fadeIn 0.2s ease-out; }

        @keyframes slideUpSheet {
            from { transform: translateY(100%); }
            to   { transform: translateY(0);    }
        }
        .sheet-slide-up { animation: slideUpSheet 0.35s cubic-bezier(0.32, 0.72, 0, 1); }
    </style>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>

    {{-- Alpine.js 全域工具 + 元件（須在 alpine:init 事件內定義） --}}
    <script>
        // AJAX 工具
        window.budgetUtils = {
            fetchJson(url, options = {}) {
                const token = document.querySelector('meta[name="csrf-token"]')?.content
                return fetch(url, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token ?? '',
                    },
                    ...options,
                }).then(async res => {
                    if (!res.ok) {
                        const err = await res.json().catch(() => ({}))
                        throw new Error(err.message ?? `HTTP ${res.status}`)
                    }
                    if (res.status === 204) return null
                    return res.json()
                })
            }
        }

        // Alpine 元件
        document.addEventListener('alpine:init', () => {
            Alpine.data('addTransaction', (categories) => ({
                open: false,
                loading: false,
                type: 'expense',
                amount: '',
                categoryId: null,
                note: '',
                date: new Date().toISOString().slice(0, 10),
                categories,

                get expenseCategories() {
                    return this.categories.filter(c => c.type === 'expense')
                },
                get incomeCategories() {
                    return this.categories.filter(c => c.type === 'income')
                },
                get filteredCategories() {
                    return this.type === 'expense' ? this.expenseCategories : this.incomeCategories
                },

                openSheet() {
                    this.open = true
                    this.reset()
                },

                reset() {
                    this.amount = ''
                    this.categoryId = this.filteredCategories[0]?.id ?? null
                    this.note = ''
                    this.date = new Date().toISOString().slice(0, 10)
                    this.loading = false
                },

                async submit() {
                    if (!this.amount || !this.categoryId) return
                    this.loading = true
                    try {
                        await window.budgetUtils.fetchJson('/transactions', {
                            method: 'POST',
                            body: JSON.stringify({
                                category_id: this.categoryId,
                                amount: this.amount,
                                type: this.type,
                                note: this.note,
                                transaction_date: this.date,
                            }),
                        })
                        this.open = false
                        window.location.reload()
                    } catch (e) {
                        alert(e.message)
                    } finally {
                        this.loading = false
                    }
                },
            }))
        })
    </script>

    {{-- Alpine.js CDN（defer，讓上方 alpine:init 先執行）--}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
</head>
<body class="h-full bg-slate-50 text-slate-800 antialiased">

<main class="min-h-full pb-20" id="app">
    @yield('content')
</main>

@auth('budget')
<nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 nav-bar z-40">
    <div class="flex items-start justify-around pt-2 px-2">
        <a href="{{ route('budget.dashboard') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-xl transition-colors
                  {{ request()->routeIs('budget.dashboard') ? 'text-indigo-600' : 'text-slate-400' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="text-xs font-medium">首頁</span>
        </a>

        <a href="{{ route('budget.history') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-xl transition-colors
                  {{ request()->routeIs('budget.history') ? 'text-indigo-600' : 'text-slate-400' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span class="text-xs font-medium">帳目</span>
        </a>

        <a href="{{ route('budget.analysis') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-xl transition-colors
                  {{ request()->routeIs('budget.analysis') ? 'text-indigo-600' : 'text-slate-400' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span class="text-xs font-medium">分析</span>
        </a>

        <a href="{{ route('budget.ai') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-xl transition-colors
                  {{ request()->routeIs('budget.ai') ? 'text-indigo-600' : 'text-slate-400' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
            <span class="text-xs font-medium">AI 建議</span>
        </a>

        <a href="{{ route('budget.settings') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-xl transition-colors
                  {{ request()->routeIs('budget.settings') ? 'text-indigo-600' : 'text-slate-400' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="text-xs font-medium">設定</span>
        </a>
    </div>
</nav>
@endauth

{{-- PWA Service Worker --}}
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/budget-sw.js').catch(() => {})
        })
    }
</script>

@stack('scripts')
</body>
</html>
