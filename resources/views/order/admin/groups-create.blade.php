@extends('order.admin.layout')

@section('title', '開團')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">開新的一團</h1>
    <p class="mt-1 text-sm text-stone-500">
        將複製目前共用名單（{{ $memberCount }} 人）進這一團，並產生公開短碼連結。
    </p>
</div>

@if ($memberCount === 0)
    <div class="rounded-2xl bg-amber/10 p-4 text-sm ring-1 ring-amber/30">
        還沒有團員。請先到<a href="{{ route('order.members.index') }}" class="font-semibold underline">成員管理</a>新增。
    </div>
@else
<form method="POST" action="{{ route('order.groups.store') }}" enctype="multipart/form-data" class="space-y-5 rounded-2xl bg-white p-5 ring-1 ring-black/10">
    @csrf

    <div>
        <label class="mb-2 block text-sm font-medium">選擇既有菜單（可選）</label>
        <select name="menu_id" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-2.5">
            <option value="">— 不選，改手動填 —</option>
            @foreach ($menus as $menu)
                <option value="{{ $menu->id }}" @selected(old('menu_id') == $menu->id)>{{ $menu->shop_name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium">飲料店名稱（可覆蓋菜單店名）</label>
        <input type="text" name="shop_name" value="{{ old('shop_name') }}" maxlength="120" placeholder="例如：Milksha 迷客夏"
               class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-2.5 outline-none focus:border-amber">
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium">本次菜單照片（可選，覆蓋菜單圖）</label>
        <input type="file" name="image" accept="image/*" class="w-full text-sm">
    </div>

    <button type="submit" class="w-full rounded-xl bg-amber py-3 font-semibold hover:opacity-90">確認開團</button>
</form>
@endif
@endsection
