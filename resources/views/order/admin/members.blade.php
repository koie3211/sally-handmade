@extends('order.admin.layout')

@section('title', '成員管理')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">成員管理</h1>
    <p class="mt-1 text-sm text-stone-500">共用名單。開團時會複製當下名單進該團，之後改這裡不影響舊團。</p>
</div>

<form method="POST" action="{{ route('order.members.store') }}" class="mb-6 flex flex-wrap gap-2 rounded-2xl bg-white p-4 ring-1 ring-black/10">
    @csrf
    <input type="text" name="name" value="{{ old('name') }}" required maxlength="64" placeholder="新增一位團員"
           class="min-w-[200px] flex-1 rounded-xl border border-stone-200 bg-stone-50 px-4 py-2.5 outline-none focus:border-amber">
    <button type="submit" class="rounded-xl bg-amber px-4 py-2.5 text-sm font-semibold">新增</button>
</form>

@if ($members->isEmpty())
    <p class="text-sm text-stone-500">尚未新增團員。</p>
@else
    <ul class="space-y-2">
        @foreach ($members as $member)
            <li class="flex items-center justify-between rounded-xl bg-white px-4 py-3 ring-1 ring-black/10">
                <span>{{ $member->name }}</span>
                <form method="POST" action="{{ route('order.members.destroy', $member) }}" onsubmit="return confirm('確定移除「{{ $member->name }}」？')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-rose-600 hover:underline">移除</button>
                </form>
            </li>
        @endforeach
    </ul>
@endif
@endsection
