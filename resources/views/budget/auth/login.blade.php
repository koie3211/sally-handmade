@extends('budget.layout')

@section('title', '登入')

@section('content')
<div class="flex min-h-screen flex-col items-center justify-center px-6 py-12">

    {{-- Logo & 標題 --}}
    <div class="mb-8 text-center">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-600 text-3xl shadow-lg">
            👛
        </div>
        <h1 class="text-2xl font-bold text-slate-800">荷包</h1>
        <p class="mt-1 text-sm text-slate-500">家庭收支記帳</p>
    </div>

    {{-- 登入卡片 --}}
    <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <form method="POST" action="{{ route('budget.login.post') }}" class="space-y-4">
            @csrf

            {{-- 錯誤提示 --}}
            @if ($errors->any())
                <div class="rounded-xl bg-rose-50 p-3 text-sm text-rose-600">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Email --}}
            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-slate-700">
                    電子郵件
                </label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-base
                           outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20
                           @error('email') border-rose-400 @enderror"
                    placeholder="you@example.com"
                >
            </div>

            {{-- 密碼 --}}
            <div>
                <label for="password" class="mb-1 block text-sm font-medium text-slate-700">
                    密碼
                </label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-base
                           outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20"
                    placeholder="••••••••"
                >
            </div>

            {{-- 記住我 --}}
            <div class="flex items-center gap-2">
                <input
                    id="remember"
                    type="checkbox"
                    name="remember"
                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 accent-indigo-600"
                >
                <label for="remember" class="text-sm text-slate-600">
                    記住我（14 天免登入）
                </label>
            </div>

            {{-- 送出 --}}
            <button
                type="submit"
                class="w-full rounded-xl bg-indigo-600 py-3 text-base font-semibold text-white
                       shadow-sm transition hover:bg-indigo-700 active:scale-95"
            >
                登入
            </button>
        </form>
    </div>

    <p class="mt-6 text-xs text-slate-400">僅限家庭成員使用</p>
</div>
@endsection
