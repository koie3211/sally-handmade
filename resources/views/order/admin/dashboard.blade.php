@extends('order.admin.layout')

@section('title', '團購列表')

@section('content')
<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
    <div>
        <h1 class="text-2xl font-bold">團購列表</h1>
        <p class="mt-1 text-sm text-stone-500">共用團員 {{ $memberCount }} 人 · 菜單模板 {{ $menuCount }} 份</p>
    </div>
    <a href="{{ route('order.groups.create') }}" class="rounded-full bg-amber px-4 py-2 text-sm font-semibold hover:opacity-90">＋ 開新的一團</a>
</div>

@if ($groups->isEmpty())
    <div class="rounded-2xl bg-white p-8 text-center text-stone-500 ring-1 ring-black/10">
        還沒有開過團。先去準備<a href="{{ route('order.members.index') }}" class="text-amber underline">成員</a>與<a href="{{ route('order.menus.index') }}" class="text-amber underline">菜單</a>，再來開團。
    </div>
@else
    <div class="space-y-3">
        @foreach ($groups as $group)
            <a href="{{ route('order.groups.show', $group) }}" class="block rounded-2xl bg-white p-4 ring-1 ring-black/10 transition hover:ring-amber/50">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div>
                        <div class="font-semibold">{{ $group->shop_name ?: '未填店名' }}</div>
                        <div class="mt-1 text-xs text-stone-500">
                            短碼 {{ $group->code }} · 第 {{ $group->round }} 輪 · {{ $group->ordered_count }}/{{ $group->members_count }} 已選
                        </div>
                    </div>
                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $group->status === 'open' ? 'bg-emerald-50 text-emerald-700' : 'bg-stone-100 text-stone-500' }}">
                        {{ $group->status === 'open' ? '進行中' : '已關閉' }}
                    </span>
                </div>
            </a>
        @endforeach
    </div>
@endif
@endsection
