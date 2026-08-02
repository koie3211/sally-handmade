@extends('order.admin.layout')

@section('title', '菜單管理')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">菜單管理</h1>
    <p class="mt-1 text-sm text-stone-500">店名與菜單照片可重用。開團時會 snapshot 到該團。</p>
</div>

<form method="POST" action="{{ route('order.menus.store') }}" enctype="multipart/form-data" class="mb-8 space-y-3 rounded-2xl bg-white p-4 ring-1 ring-black/10">
    @csrf
    <div>
        <label class="mb-1 block text-sm font-medium">飲料店名稱</label>
        <input type="text" name="shop_name" value="{{ old('shop_name') }}" required maxlength="120" placeholder="例如：Milksha 迷客夏"
               class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-2.5 outline-none focus:border-amber">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">菜單照片（可選）</label>
        <input type="file" name="image" accept="image/*" class="w-full text-sm">
    </div>
    <button type="submit" class="rounded-xl bg-amber px-4 py-2.5 text-sm font-semibold">新增菜單</button>
</form>

@if ($menus->isEmpty())
    <p class="text-sm text-stone-500">尚未新增菜單。</p>
@else
    <div class="space-y-4">
        @foreach ($menus as $menu)
            <div class="rounded-2xl bg-white p-4 ring-1 ring-black/10">
                <div class="mb-3 flex flex-wrap items-start justify-between gap-2">
                    <div>
                        <div class="font-semibold">{{ $menu->shop_name }}</div>
                        @if ($menu->image_url)
                            <img src="{{ $menu->image_url }}" alt="{{ $menu->shop_name }}" class="mt-2 max-h-40 rounded-lg ring-1 ring-black/10">
                        @else
                            <p class="mt-1 text-xs text-stone-400">尚無菜單照片</p>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('order.menus.destroy', $menu) }}" onsubmit="return confirm('確定刪除此菜單？')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-rose-600 hover:underline">刪除</button>
                    </form>
                </div>
                <form method="POST" action="{{ route('order.menus.update', $menu) }}" enctype="multipart/form-data" class="flex flex-wrap items-end gap-2 border-t border-black/5 pt-3">
                    @csrf
                    @method('PUT')
                    <div class="min-w-[180px] flex-1">
                        <label class="mb-1 block text-xs text-stone-500">更新店名</label>
                        <input type="text" name="shop_name" value="{{ $menu->shop_name }}" required class="w-full rounded-xl border border-stone-200 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs text-stone-500">換照片</label>
                        <input type="file" name="image" accept="image/*" class="text-xs">
                    </div>
                    <button type="submit" class="rounded-xl bg-stone-800 px-3 py-2 text-sm font-semibold text-white">更新</button>
                </form>
            </div>
        @endforeach
    </div>
@endif
@endsection
