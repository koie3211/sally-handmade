<!DOCTYPE html>
<html lang="zh-Hant" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '後台') · 勤美揪手搖</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        amber: { DEFAULT: '#c9863a', dim: '#d9ac74' },
                        ink: '#2b2318',
                        cream: '#f8f5ee',
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-full bg-cream text-ink antialiased">
@auth('order')
<header class="sticky top-0 z-20 border-b border-black/10 bg-cream/90 backdrop-blur">
    <div class="mx-auto flex max-w-4xl flex-wrap items-center justify-between gap-3 px-4 py-3">
        <a href="{{ route('order.dashboard') }}" class="font-semibold tracking-wide">勤美揪手搖 · 後台</a>
        <nav class="flex flex-wrap items-center gap-2 text-sm">
            <a href="{{ route('order.dashboard') }}" class="rounded-full px-3 py-1.5 hover:bg-black/5 {{ request()->routeIs('order.dashboard') ? 'bg-black/5 font-semibold' : '' }}">團購</a>
            <a href="{{ route('order.members.index') }}" class="rounded-full px-3 py-1.5 hover:bg-black/5 {{ request()->routeIs('order.members.*') ? 'bg-black/5 font-semibold' : '' }}">成員</a>
            <a href="{{ route('order.menus.index') }}" class="rounded-full px-3 py-1.5 hover:bg-black/5 {{ request()->routeIs('order.menus.*') ? 'bg-black/5 font-semibold' : '' }}">菜單</a>
            <a href="{{ route('order.groups.create') }}" class="rounded-full bg-amber px-3 py-1.5 font-semibold text-ink hover:opacity-90">開團</a>
            <form method="POST" action="{{ route('order.logout') }}">
                @csrf
                <button type="submit" class="rounded-full px-3 py-1.5 text-stone-500 hover:bg-black/5">登出</button>
            </form>
        </nav>
    </div>
</header>
@endauth

<main class="mx-auto max-w-4xl px-4 py-8">
    @if (session('success'))
        <div class="mb-4 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-800 ring-1 ring-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700 ring-1 ring-rose-200">
            {{ $errors->first() }}
        </div>
    @endif

    @yield('content')
</main>
</body>
</html>
