<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登記工具</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
<div class="max-w-md w-full mx-auto px-4 py-12">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">登記工具</h1>
    <p class="text-sm text-gray-500 mb-8">公司、商業設立登記輔助工具</p>

    <div class="space-y-3">
        <a href="/lookup"
           class="flex items-start gap-4 bg-white border border-gray-200 rounded-xl px-5 py-4 shadow-sm hover:shadow-md hover:border-blue-300 transition group">
            <div class="mt-0.5 text-2xl">🔍</div>
            <div>
                <div class="font-semibold text-gray-800 group-hover:text-blue-700 transition">業別代碼查詢</div>
                <div class="text-xs text-gray-500 mt-0.5">查詢營業項目細類對應的中華民國行業標準分類（第八次修訂）及同業利潤小業別</div>
            </div>
        </a>

        <a href="/compose"
           class="flex items-start gap-4 bg-white border border-gray-200 rounded-xl px-5 py-4 shadow-sm hover:shadow-md hover:border-indigo-300 transition group">
            <div class="mt-0.5 text-2xl">⚙️</div>
            <div>
                <div class="font-semibold text-gray-800 group-hover:text-indigo-700 transition">設立項目選配</div>
                <div class="text-xs text-gray-500 mt-0.5">選取 1–4 個營業項目，系統自動匯聚對應標準分類，再挑選出最終 1–4 項</div>
            </div>
        </a>
    </div>
</div>
</body>
</html>
