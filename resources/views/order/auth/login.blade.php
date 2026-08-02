@extends('order.admin.layout')

@section('title', '登入')

@section('content')
<div class="flex min-h-[70vh] flex-col items-center justify-center">
    <div class="mb-8 text-center">
        <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-[#fff2e2] ring-1 ring-amber/30 text-xl font-bold text-amber">揪</div>
        <h1 class="text-2xl font-bold">勤美揪手搖</h1>
        <p class="mt-1 text-sm text-stone-500">團購管理後台</p>
    </div>

    <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/10">
        <form method="POST" action="{{ route('order.login.post') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="mb-1 block text-sm font-medium">電子郵件</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                       class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 outline-none focus:border-amber focus:ring-2 focus:ring-amber/20"
                       placeholder="you@example.com">
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-medium">密碼</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 outline-none focus:border-amber focus:ring-2 focus:ring-amber/20"
                       placeholder="••••••••">
            </div>

            <div class="flex items-center gap-2">
                <input id="remember" type="checkbox" name="remember" class="h-4 w-4 accent-amber">
                <label for="remember" class="text-sm text-stone-600">記住我</label>
            </div>

            <button type="submit" class="w-full rounded-xl bg-amber py-3 font-semibold text-ink hover:opacity-90">
                登入
            </button>
        </form>
    </div>
</div>
@endsection
