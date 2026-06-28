const CACHE_NAME = 'budget-v1'

// 預快取的靜態資源（Vite build 後的路徑會由 layout 中的 @vite 產生，
// 此處快取 App Shell 所需的核心頁面）
const PRECACHE_URLS = [
    '/',
    '/login',
    '/offline',
]

// ── Install：預快取 App Shell ───────────────────────────
self.addEventListener('install', (event) => {
    self.skipWaiting()
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) =>
            cache.addAll(PRECACHE_URLS).catch(() => {
                // 忽略預快取失敗（未登入時 / 可能 404）
            })
        )
    )
})

// ── Activate：清除舊快取 ────────────────────────────────
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => key !== CACHE_NAME)
                    .map((key) => caches.delete(key))
            )
        ).then(() => self.clients.claim())
    )
})

// ── Fetch：Network First，失敗回傳快取 ─────────────────
self.addEventListener('fetch', (event) => {
    const { request } = event
    const url = new URL(request.url)

    // 只處理同源請求，且非 POST/DELETE/PUT
    if (url.origin !== self.location.origin) return
    if (request.method !== 'GET') return

    // API 請求（AJAX）不快取
    if (url.pathname.startsWith('/api/') || url.pathname.startsWith('/transactions')) return

    event.respondWith(
        fetch(request)
            .then((response) => {
                // 只快取成功的 HTML/CSS/JS 回應
                if (response.ok && (
                    response.headers.get('content-type')?.includes('text/html') ||
                    response.headers.get('content-type')?.includes('text/css') ||
                    response.headers.get('content-type')?.includes('javascript')
                )) {
                    const clone = response.clone()
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, clone))
                }
                return response
            })
            .catch(() =>
                caches.match(request).then((cached) => {
                    if (cached) return cached
                    // 完全離線且無快取：回傳簡易離線提示
                    if (request.headers.get('accept')?.includes('text/html')) {
                        return new Response(
                            '<html><body style="font-family:sans-serif;text-align:center;padding:3rem"><h1>📵 目前離線</h1><p>請連線後重新整理</p></body></html>',
                            { headers: { 'Content-Type': 'text/html; charset=utf-8' } }
                        )
                    }
                })
            )
    )
})
