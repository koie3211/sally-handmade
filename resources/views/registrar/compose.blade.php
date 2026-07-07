<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>設立項目選配</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .badge-src {
            display: inline-block;
            background: #e5e7eb;
            color: #6b7280;
            font-size: 0.65rem;
            font-weight: 600;
            border-radius: 3px;
            padding: 1px 4px;
            line-height: 1.4;
        }
        .col-header {
            position: sticky;
            top: 0;
            z-index: 10;
            background: white;
            border-bottom: 1px solid #e5e7eb;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

{{-- 頂部導覽 --}}
<div class="bg-white border-b border-gray-200 px-4 py-3 flex items-center gap-4">
    <a href="/lookup" class="text-sm text-blue-600 hover:underline">← 代碼查詢</a>
    <span class="text-gray-300">|</span>
    <h1 class="text-base font-bold text-gray-800">設立項目選配</h1>
    <span class="text-xs text-gray-400">選取 1–4 個營業項目 → 展開對應小業別 → 挑出最終 1–4 項</span>
</div>

<div class="flex h-[calc(100vh-53px)] overflow-hidden" x-data="composeApp()" x-init="init()">

    {{-- ========== 欄 1：搜尋選取業別細類 ========== --}}
    <div class="flex flex-col w-80 shrink-0 bg-white border-r border-gray-200 overflow-hidden">
        <div class="col-header px-3 py-2.5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-gray-600 uppercase tracking-wide">① 選取營業項目細類</span>
                <span class="text-xs font-semibold px-1.5 py-0.5 rounded-full"
                      :class="selectedItems.length >= 4 ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700'"
                      x-text="selectedItems.length + ' / 4'"></span>
            </div>
            <input
                type="text"
                x-model="query"
                @input="search()"
                placeholder="搜尋代碼或名稱…"
                class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
            >
            <div class="flex flex-wrap gap-1 mt-2">
                <button @click="filterMajor = ''; search()"
                    :class="filterMajor === '' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'"
                    class="px-2 py-0.5 rounded-full text-xs font-medium transition">全</button>
                @foreach($majorGroups as $code => $name)
                <button @click="filterMajor = '{{ $code }}'; search()"
                    :class="filterMajor === '{{ $code }}' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'"
                    class="px-2 py-0.5 rounded-full text-xs font-medium transition">{{ $code }}</button>
                @endforeach
            </div>
        </div>

        {{-- 搜尋結果 --}}
        <div class="flex-1 overflow-y-auto divide-y divide-gray-100">
            <template x-for="item in searchResults" :key="item.code">
                <div class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 transition">
                    <div class="flex-1 min-w-0">
                        <div class="font-mono text-xs font-bold text-blue-700" x-text="item.code"></div>
                        <div class="text-xs text-gray-700 truncate" x-text="item.name"></div>
                        <div class="text-xs text-gray-400" x-text="item.classifications.length + ' 項分類'"></div>
                    </div>
                    <button
                        @click="addItem(item)"
                        :disabled="selectedItems.length >= 4 || isItemSelected(item.code)"
                        :class="isItemSelected(item.code)
                            ? 'text-green-600 bg-green-50 border-green-200 cursor-default'
                            : selectedItems.length >= 4
                                ? 'text-gray-300 border-gray-200 cursor-not-allowed'
                                : 'text-blue-600 border-blue-300 hover:bg-blue-50'"
                        class="shrink-0 text-xs border rounded px-1.5 py-0.5 transition"
                        x-text="isItemSelected(item.code) ? '✓' : '＋'"
                    ></button>
                </div>
            </template>
            <div x-show="searchResults.length === 0" class="px-3 py-6 text-center text-xs text-gray-400">
                找不到符合項目
            </div>
        </div>

        {{-- 已選業別籃 --}}
        <div class="border-t border-gray-200 bg-gray-50 px-3 py-2 space-y-1.5 max-h-48 overflow-y-auto">
            <div class="text-xs font-semibold text-gray-500 mb-1">已選取業別項目</div>
            <template x-if="selectedItems.length === 0">
                <div class="text-xs text-gray-400 py-2 text-center">尚未選取任何項目</div>
            </template>
            <template x-for="item in selectedItems" :key="item.code">
                <div class="flex items-center gap-2 bg-white rounded border border-blue-200 px-2 py-1">
                    <div class="flex-1 min-w-0">
                        <span class="font-mono text-xs font-bold text-blue-700" x-text="item.code"></span>
                        <span class="text-xs text-gray-600 ml-1" x-text="item.name"></span>
                    </div>
                    <button @click="removeItem(item.code)" class="text-gray-400 hover:text-red-500 text-sm leading-none shrink-0">×</button>
                </div>
            </template>
            <button
                x-show="selectedItems.length > 0"
                @click="selectedItems = []; selectedCls = []"
                class="w-full text-xs text-red-400 hover:text-red-600 pt-1">
                清空全部
            </button>
        </div>
    </div>

    {{-- ========== 欄 2：標準分類池 + 小業別 ========== --}}
    <div class="flex flex-col flex-1 bg-white border-r border-gray-200 overflow-hidden">
        <div class="col-header px-4 py-2.5">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-600 uppercase tracking-wide">② 對應分類 / 小業別</span>
                    <span class="text-xs text-gray-400 ml-2">有小業別者展開後選取，無則直接選取</span>
                </div>
                <span class="text-xs text-gray-500" x-text="classPool.length + ' 項分類'"></span>
            </div>
            <input
                type="text"
                x-model="poolQuery"
                placeholder="篩選代碼、名稱或小業別…"
                class="mt-2 w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
            >
        </div>

        {{-- 空狀態 --}}
        <div x-show="selectedItems.length === 0" class="flex-1 flex items-center justify-center text-gray-400 text-sm">
            <div class="text-center">
                <div class="text-3xl mb-2">←</div>
                <p>請先在左側選取業別項目</p>
            </div>
        </div>

        {{-- 分類清單 --}}
        <div x-show="selectedItems.length > 0" class="flex-1 overflow-y-auto" style="display:none">
            <template x-for="cls in filteredPool" :key="cls.code">
                <div class="border-b border-gray-100">

                    {{-- 分類列標頭 --}}
                    <div class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition"
                         :class="(SUB_CATEGORIES[cls.code] && SUB_CATEGORIES[cls.code].length) ? 'cursor-pointer' : ''"
                         @click="(SUB_CATEGORIES[cls.code] && SUB_CATEGORIES[cls.code].length) && togglePool(cls.code)">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-mono text-sm font-bold text-indigo-700" x-text="cls.code"></span>
                                <span class="text-sm text-gray-800" x-text="cls.name"></span>
                            </div>
                            <div class="flex gap-1 mt-0.5 flex-wrap items-center">
                                <template x-for="src in cls.sources" :key="src">
                                    <span class="badge-src" x-text="src"></span>
                                </template>
                                <span x-show="SUB_CATEGORIES[cls.code] && SUB_CATEGORIES[cls.code].length"
                                      class="text-xs text-indigo-500 font-medium ml-1"
                                      x-text="(SUB_CATEGORIES[cls.code] || []).length + ' 個小業別，點此展開'"></span>
                            </div>
                        </div>

                        {{-- 有小業別：展開按鈕 --}}
                        <template x-if="SUB_CATEGORIES[cls.code] && SUB_CATEGORIES[cls.code].length > 0">
                            <button
                                @click.stop="togglePool(cls.code)"
                                class="shrink-0 text-xs border border-indigo-200 text-indigo-600 hover:bg-indigo-50 rounded px-2 py-1 transition font-medium"
                                x-text="expandedPool[cls.code] ? '▲ 收合' : '▾ 小業別'"
                            ></button>
                        </template>

                        {{-- 無小業別：直接選取按鈕 --}}
                        <template x-if="!SUB_CATEGORIES[cls.code] || !SUB_CATEGORIES[cls.code].length">
                            <button
                                @click.stop="toggleDirectCls(cls)"
                                :disabled="!isSelected(cls.code) && selectedCls.length >= 4"
                                :class="isSelected(cls.code)
                                    ? 'bg-indigo-600 text-white border-indigo-600'
                                    : selectedCls.length >= 4
                                        ? 'text-gray-300 border-gray-200 cursor-not-allowed'
                                        : 'text-indigo-600 border-indigo-300 hover:bg-indigo-50'"
                                class="shrink-0 text-xs border rounded px-2 py-1 transition font-medium"
                                x-text="isSelected(cls.code) ? '✓ 已選' : '＋ 選取'"
                            ></button>
                        </template>
                    </div>

                    {{-- 小業別展開列 --}}
                    <div x-show="expandedPool[cls.code]"
                         class="bg-indigo-50 border-t border-indigo-100 divide-y divide-indigo-100"
                         style="display:none">
                        <template x-for="sub in (SUB_CATEGORIES[cls.code] || [])" :key="sub.code">
                            <div class="flex items-center gap-3 pl-8 pr-4 py-2 hover:bg-indigo-100 transition">
                                <div class="flex-1 min-w-0">
                                    <span class="font-mono text-xs font-bold text-indigo-600" x-text="sub.code"></span>
                                    <span class="text-xs text-gray-700 ml-1.5" x-text="sub.name"></span>
                                </div>
                                <button
                                    @click="toggleSubCat(cls, sub)"
                                    :disabled="!isSelected(sub.code) && selectedCls.length >= 4"
                                    :class="isSelected(sub.code)
                                        ? 'bg-indigo-600 text-white border-indigo-600'
                                        : selectedCls.length >= 4
                                            ? 'text-gray-300 border-gray-200 cursor-not-allowed'
                                            : 'text-indigo-600 border-indigo-300 hover:bg-indigo-50'"
                                    class="shrink-0 text-xs border rounded px-2 py-1 transition font-medium"
                                    x-text="isSelected(sub.code) ? '✓' : '＋'"
                                ></button>
                            </div>
                        </template>
                    </div>

                </div>
            </template>
            <div x-show="filteredPool.length === 0 && poolQuery" class="px-4 py-6 text-center text-xs text-gray-400">
                找不到符合的分類或小業別
            </div>
        </div>
    </div>

    {{-- ========== 欄 3：最終選取 ========== --}}
    <div class="flex flex-col w-80 shrink-0 bg-white overflow-hidden">
        <div class="col-header px-4 py-2.5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-gray-600 uppercase tracking-wide">③ 最終選取</span>
                <span class="text-xs font-semibold px-1.5 py-0.5 rounded-full"
                      :class="selectedCls.length >= 4 ? 'bg-green-100 text-green-700' : 'bg-indigo-100 text-indigo-700'"
                      x-text="selectedCls.length + ' / 4'"></span>
            </div>
            <div class="flex gap-2">
                <button
                    @click="copyAll()"
                    :disabled="selectedCls.length === 0"
                    class="flex-1 text-xs border border-gray-300 rounded-lg px-3 py-1.5 hover:bg-gray-50 transition disabled:opacity-40 disabled:cursor-not-allowed"
                >複製全部</button>
                <button
                    @click="selectedCls = []"
                    :disabled="selectedCls.length === 0"
                    class="text-xs border border-red-200 text-red-400 hover:text-red-600 rounded-lg px-3 py-1.5 transition disabled:opacity-40 disabled:cursor-not-allowed"
                >清空</button>
            </div>
            <div x-show="copied" x-cloak x-transition class="text-xs text-green-600 font-medium text-center mt-1">✓ 已複製至剪貼簿</div>
        </div>

        {{-- 已選清單 --}}
        <div class="flex-1 overflow-y-auto px-4 py-3 space-y-2">
            <template x-if="selectedCls.length === 0">
                <div class="text-center text-gray-400 text-sm py-8">
                    <div class="text-3xl mb-2">←</div>
                    <p class="text-xs">從中欄選取小業別或分類</p>
                </div>
            </template>
            <template x-for="(entry, idx) in selectedCls" :key="entry.code">
                <div class="rounded-lg border border-indigo-200 bg-indigo-50">
                    <div class="flex items-start gap-2 px-3 py-2">
                        <span class="text-xs font-bold text-indigo-400 mt-0.5 shrink-0" x-text="(idx + 1) + '.'"></span>
                        <div class="flex-1 min-w-0">
                            <div class="font-mono text-sm font-bold text-indigo-800" x-text="entry.code"></div>
                            <div class="text-xs text-gray-700 leading-snug" x-text="entry.name"></div>
                            {{-- 若為小業別，顯示父分類來源 --}}
                            <div x-show="entry.parentCode"
                                 class="text-xs text-gray-400 mt-0.5"
                                 x-text="entry.parentCode ? '← ' + entry.parentCode + ' ' + entry.parentName : ''"></div>
                        </div>
                        <button @click="removeCls(entry.code)" class="text-gray-400 hover:text-red-500 text-sm shrink-0 mt-0.5">×</button>
                    </div>
                </div>
            </template>
        </div>

        {{-- 輸出預覽 --}}
        <div x-show="selectedCls.length > 0" x-cloak class="border-t border-gray-200 px-4 py-3 bg-gray-50">
            <div class="text-xs font-semibold text-gray-500 mb-1.5">輸出預覽</div>
            <div class="font-mono text-xs text-gray-600 whitespace-pre-wrap break-all leading-5"
                 x-text="selectedCls.map((c, i) => (i+1) + '. ' + c.code + ' ' + c.name).join('\n')"></div>
        </div>
    </div>

</div>

<script>
const ALL_ITEMS = @json($items);
const SUB_CATEGORIES = @json($subCategories);

function composeApp() {
    return {
        query: '',
        filterMajor: '',
        poolQuery: '',
        searchResults: [],
        selectedItems: [],
        selectedCls: [],   // { code, name, parentCode|null, parentName|null }
        expandedPool: {},  // { [clsCode]: true/false }
        copied: false,
        _copyTimer: null,

        init() {
            this.search();
        },

        // 從所有已選業別項目匯聚去重的標準分類池
        get classPool() {
            const map = new Map();
            for (const item of this.selectedItems) {
                for (const cls of item.classifications) {
                    if (!map.has(cls.code)) {
                        map.set(cls.code, { ...cls, sources: [] });
                    }
                    map.get(cls.code).sources.push(item.code);
                }
            }
            return Array.from(map.values());
        },

        // 依搜尋字串篩選（含小業別名稱）
        get filteredPool() {
            const q = this.poolQuery.trim().toLowerCase();
            if (!q) return this.classPool;
            return this.classPool.filter(c => {
                if (c.code.toLowerCase().includes(q) || c.name.toLowerCase().includes(q)) return true;
                const subs = SUB_CATEGORIES[c.code] || [];
                return subs.some(s => s.code.toLowerCase().includes(q) || s.name.toLowerCase().includes(q));
            });
        },

        search() {
            const q = this.query.trim().toLowerCase();
            const major = this.filterMajor;
            this.searchResults = ALL_ITEMS.filter(item => {
                if (major && item.major_code !== major) return false;
                if (!q) return true;
                return item.code.toLowerCase().includes(q) || item.name.toLowerCase().includes(q);
            });
        },

        // 判斷某代碼是否已在最終選取中（可為小業別代碼或分類代碼）
        isSelected(code) {
            return this.selectedCls.some(c => c.code === code);
        },

        isItemSelected(code) {
            return this.selectedItems.some(i => i.code === code);
        },

        addItem(item) {
            if (this.selectedItems.length >= 4 || this.isItemSelected(item.code)) return;
            this.selectedItems.push(item);
        },

        removeItem(code) {
            this.selectedItems = this.selectedItems.filter(i => i.code !== code);
            // 移除因業別移除而不再有效的選取項目
            const remainingPoolCodes = new Set(this.classPool.map(c => c.code));
            this.selectedCls = this.selectedCls.filter(c => {
                const ref = c.parentCode ?? c.code;
                return remainingPoolCodes.has(ref);
            });
        },

        // 切換分類池的展開/收合（有小業別的分類列）
        togglePool(code) {
            this.expandedPool[code] = !this.expandedPool[code];
        },

        // 直接選取分類（無小業別者）
        toggleDirectCls(cls) {
            if (this.isSelected(cls.code)) {
                this.removeCls(cls.code);
            } else {
                if (this.selectedCls.length >= 4) return;
                this.selectedCls.push({ code: cls.code, name: cls.name, parentCode: null, parentName: null });
            }
        },

        // 選取小業別（有小業別的分類展開後）
        toggleSubCat(cls, sub) {
            if (this.isSelected(sub.code)) {
                this.removeCls(sub.code);
            } else {
                if (this.selectedCls.length >= 4) return;
                this.selectedCls.push({
                    code: sub.code,
                    name: sub.name,
                    parentCode: cls.code,
                    parentName: cls.name,
                });
            }
        },

        removeCls(code) {
            this.selectedCls = this.selectedCls.filter(c => c.code !== code);
        },

        copyAll() {
            if (this.selectedCls.length === 0) return;
            const text = this.selectedCls.map(c => c.code + '\t' + c.name).join('\n');
            this.doCopy(text);
        },

        doCopy(text) {
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
