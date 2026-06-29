@extends('budget.layout')

@section('title', '設定')

@section('content')
<div>

    {{-- 頂部標題 --}}
    <div class="bg-white px-5 pt-12 pb-4 shadow-sm safe-area-top">
        <h1 class="text-xl font-bold text-slate-800">設定</h1>
    </div>

    <div class="px-4 py-4 space-y-4">

        {{-- 帳號資訊 --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100 text-2xl">👤</div>
                <div>
                    <p class="font-semibold text-slate-800">{{ auth('budget')->user()->name }}</p>
                    <p class="text-sm text-slate-400">{{ auth('budget')->user()->email }}</p>
                </div>
            </div>
        </div>

        {{-- 修改密碼 --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
            <h2 class="mb-4 text-sm font-semibold text-slate-600">修改密碼</h2>

            {{-- 成功訊息 --}}
            @if (session('success'))
                <div class="mb-4 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    ✓ {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('budget.settings.password') }}" class="space-y-3">
                @csrf
                @method('PUT')

                {{-- 目前密碼 --}}
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500">目前密碼</label>
                    <input type="password" name="current_password" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm
                                  outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20
                                  @error('current_password') border-rose-400 @enderror">
                    @error('current_password')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 新密碼 --}}
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500">新密碼（至少 8 字元）</label>
                    <input type="password" name="password" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm
                                  outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20
                                  @error('password') border-rose-400 @enderror">
                    @error('password')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 確認新密碼 --}}
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500">確認新密碼</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm
                                  outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20">
                </div>

                <button type="submit"
                        class="w-full rounded-xl bg-indigo-600 py-3 text-sm font-semibold text-white
                               shadow-sm transition hover:bg-indigo-700 active:scale-95 mt-2">
                    更新密碼
                </button>
            </form>
        </div>

        {{-- 登出 --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <form method="POST" action="{{ route('budget.logout') }}">
                @csrf
                <button type="submit"
                        class="w-full px-5 py-4 text-left text-sm font-medium text-rose-500
                               transition hover:bg-rose-50 flex items-center gap-3">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    登出
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
