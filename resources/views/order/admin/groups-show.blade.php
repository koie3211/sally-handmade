@extends('order.admin.layout')

@section('title', $group->shop_name ?: '團購詳情')

@section('content')
<div class="mb-6">
    <a href="{{ route('order.dashboard') }}" class="text-sm text-stone-500 hover:underline">← 回列表</a>
    <h1 class="mt-2 text-2xl font-bold">{{ $group->shop_name ?: '未填店名' }}</h1>
    <p class="mt-1 text-sm text-stone-500">短碼 {{ $group->code }} · 第 {{ $group->round }} 輪 · {{ $group->status === 'open' ? '進行中' : '已關閉' }}</p>
</div>

<div class="mb-6 rounded-2xl bg-white p-4 ring-1 ring-black/10">
    <div class="mb-2 text-sm font-medium">公開連結（免登入）</div>
    <div class="flex flex-wrap gap-2">
        <input id="publicUrl" type="text" readonly value="{{ $publicUrl }}"
               class="min-w-[220px] flex-1 rounded-xl border border-stone-200 bg-stone-50 px-3 py-2 text-sm">
        <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('publicUrl').value).then(()=>alert('已複製'))"
                class="rounded-xl bg-amber px-4 py-2 text-sm font-semibold">複製</button>
        <a href="{{ $publicUrl }}" target="_blank" class="rounded-xl bg-stone-800 px-4 py-2 text-sm font-semibold text-white">開啟</a>
    </div>
</div>

@if ($group->image_url)
    <div class="mb-6 overflow-hidden rounded-2xl bg-white ring-1 ring-black/10">
        <img src="{{ $group->image_url }}" alt="菜單" class="max-h-72 w-full object-contain bg-stone-900">
    </div>
@endif

<div class="mb-6 rounded-2xl bg-white p-4 ring-1 ring-black/10">
    <h2 class="mb-3 font-semibold">目前選擇</h2>
    <ul class="divide-y divide-black/5 text-sm">
        @foreach ($group->members as $member)
            <li class="flex justify-between gap-3 py-2">
                <span class="font-medium">{{ $member->name }}</span>
                <span class="text-stone-500">
                    @if ($member->status === 'pass')
                        Pass
                    @elseif ($member->status === 'ordered')
                        {{ collect([$member->drink, $member->sugar, $member->ice])->filter()->implode('・') }}
                    @else
                        尚未選擇
                    @endif
                </span>
            </li>
        @endforeach
    </ul>
</div>

@if ($group->histories->isNotEmpty())
<div class="rounded-2xl bg-white p-4 ring-1 ring-black/10">
    <h2 class="mb-3 font-semibold">歷史結單</h2>
    <ul class="space-y-2 text-sm">
        @foreach ($group->histories as $history)
            <li class="rounded-xl bg-stone-50 px-3 py-2">
                {{ $history->finalized_at->format('n/j H:i') }} · {{ $history->total_cups }} 杯
                @if ($history->shop_name)
                    · {{ $history->shop_name }}
                @endif
            </li>
        @endforeach
    </ul>
</div>
@endif
@endsection
