<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>業別代碼查詢</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .cls-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 0.8rem;
            color: #1e40af;
            cursor: pointer;
            transition: background 0.15s;
        }
        .cls-tag:hover { background: #dbeafe; }
        .cls-tag .code { font-weight: 600; color: #1d4ed8; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<div class="max-w-5xl mx-auto px-4 py-8"
     x-data="lookupApp()"
     x-init="init()">

    {{-- 標題 --}}
    <div class="mb-6 flex items-start justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">業別代碼查詢</h1>
            <p class="text-sm text-gray-500 mt-1">輸入代碼或名稱，即可查看對應的中華民國行業標準分類（第八次修訂）</p>
        </div>
        <a href="/compose"
           class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg px-4 py-2 transition shadow-sm">
            ⚙ 進入選配工具
        </a>
    </div>

    {{-- 搜尋列 + 大類篩選 --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4 space-y-3">
        <div class="flex gap-2">
            <input
                type="text"
                x-model="query"
                @input="search()"
                placeholder="搜尋業別代碼或名稱，例如：JE01010 或 租賃"
                class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
            >
            <button
                @click="query = ''; search()"
                x-show="query.length > 0"
                x-cloak
                class="px-3 py-2 text-gray-400 hover:text-gray-600 text-sm"
            >✕ 清除</button>
        </div>

        {{-- 大類快速篩選 --}}
        <div class="flex flex-wrap gap-2">
            <button
                @click="filterMajor = ''; search()"
                :class="filterMajor === '' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                class="px-3 py-1 rounded-full text-xs font-medium transition"
            >全部</button>
            @foreach($majorGroups as $code => $name)
            <button
                @click="filterMajor = '{{ $code }}'; search()"
                :class="filterMajor === '{{ $code }}' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                class="px-3 py-1 rounded-full text-xs font-medium transition"
            >{{ $code }} {{ $name }}</button>
            @endforeach
        </div>
    </div>

    {{-- 結果統計 --}}
    <div class="text-xs text-gray-400 mb-3 px-1">
        共 <span x-text="results.length" class="font-semibold text-gray-600"></span> 筆結果
        <span x-show="copied" x-cloak x-transition class="ml-3 text-green-500 font-medium">✓ 已複製</span>
    </div>

    {{-- 結果列表 --}}
    <div class="space-y-2">
        <template x-for="item in results" :key="item.code">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                {{-- 標頭列 --}}
                <div class="flex items-center justify-between px-4 py-3 cursor-pointer hover:bg-gray-50 transition"
                     @click="item._open = !item._open">
                    <div class="flex items-center gap-3">
                        <span class="font-mono font-bold text-blue-700 text-sm w-20 shrink-0" x-text="item.code"></span>
                        <span class="font-medium text-gray-800 text-sm" x-text="item.name"></span>
                        <span class="hidden sm:inline text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full" x-text="item.major_code + ' ' + item.major_name"></span>
                    </div>
                    <div class="flex items-center gap-2 shrink-0 ml-2">
                        <span class="text-xs text-gray-400" x-text="item.classifications.length + ' 項分類'"></span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="item._open ? 'rotate-180' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                {{-- 展開的分類標籤 --}}
                <div x-show="item._open" class="border-t border-gray-100 px-4 py-3 bg-blue-50" style="display:none">
                    <div class="flex flex-wrap gap-2">
                        <template x-for="cls in item.classifications" :key="cls.code + cls.name">
                            <button
                                :class="['cls-tag', item._activeCls === cls.code ? 'ring-2 ring-blue-500 bg-blue-100' : '']"
                                @click.stop="toggleCls(item, cls)"
                                :title="'點擊複製並展開小業別：' + cls.code + ' ' + cls.name"
                            >
                                <span class="code" x-text="cls.code"></span>
                                <span x-text="cls.name"></span>
                                <span x-show="SUB_CATEGORIES[cls.code]" class="text-blue-400 text-xs">▾</span>
                            </button>
                        </template>
                    </div>

                    {{-- 小業別清單 --}}
                    <div x-show="item._activeCls && SUB_CATEGORIES[item._activeCls] && SUB_CATEGORIES[item._activeCls].length > 0"
                         class="mt-3 border-t border-blue-200 pt-3" style="display:none">
                        <div class="text-xs font-semibold text-blue-700 mb-2">
                            <span x-text="item._activeCls"></span>&nbsp;同業利潤小業別：
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="sub in (SUB_CATEGORIES[item._activeCls] || [])" :key="sub.code">
                                <button
                                    @click.stop="copyText(sub.code + '\t' + sub.name)"
                                    :title="'點擊複製：' + sub.code + ' ' + sub.name"
                                    class="inline-flex items-center gap-1 bg-white border border-blue-200 rounded px-2 py-0.5 text-xs text-blue-800 hover:bg-blue-50 transition"
                                >
                                    <span class="font-mono font-semibold text-blue-600" x-text="sub.code"></span>
                                    <span x-text="sub.name"></span>
                                </button>
                            </template>
                        </div>
                        <p class="text-xs text-gray-400 mt-1.5">點擊小業別可複製代碼與名稱</p>
                    </div>

                    <p class="text-xs text-gray-400 mt-2">點擊分類標籤可複製代碼；有 ▾ 符號者可展開同業利潤小業別</p>
                </div>
            </div>
        </template>

        {{-- 無結果 --}}
        <div x-show="results.length === 0" x-cloak class="text-center py-16 text-gray-400">
            <div class="text-4xl mb-2">🔍</div>
            <p>找不到符合的業別代碼</p>
        </div>
    </div>
</div>

<script>
const ALL_ITEMS = @json($items);
const SUB_CATEGORIES = @json($subCategories);

function lookupApp() {
    return {
        query: '',
        filterMajor: '',
        results: [],
        copied: false,
        _copyTimer: null,

        init() {
            ALL_ITEMS.forEach(item => { item._open = false; item._activeCls = null; });
            this.search();
        },

        search() {
            const q = this.query.trim().toLowerCase();
            const major = this.filterMajor;

            this.results = ALL_ITEMS.filter(item => {
                if (major && item.major_code !== major) return false;
                if (!q) return true;
                return item.code.toLowerCase().includes(q)
                    || item.name.toLowerCase().includes(q)
                    || item.classifications.some(c =>
                        c.code.toLowerCase().includes(q) || c.name.toLowerCase().includes(q)
                    );
            });

            // 關鍵字搜尋時自動展開有分類命中的項目
            if (q) {
                this.results.forEach(item => {
                    if (item.classifications.some(c =>
                        c.code.toLowerCase().includes(q) || c.name.toLowerCase().includes(q)
                    )) {
                        item._open = true;
                    }
                });
            }
        },

        toggleCls(item, cls) {
            this.copyText(cls.code + '\t' + cls.name);
            item._activeCls = (item._activeCls === cls.code) ? null : cls.code;
        },

        copyText(text) {
            navigator.clipboard.writeText(text).then(() => {
                this.copied = true;
                clearTimeout(this._copyTimer);
                this._copyTimer = setTimeout(() => { this.copied = false; }, 2000);
            });
        },
    };
}
</script>
</body>
</html>
