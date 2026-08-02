<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreGroupRequest;
use App\Models\Order\Group;
use App\Models\Order\GroupMember;
use App\Models\Order\Member;
use App\Models\Order\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function create(): View
    {
        $menus = Menu::query()->latest()->get();
        $memberCount = Member::query()->count();

        return view('order.admin.groups-create', compact('menus', 'memberCount'));
    }

    public function store(StoreGroupRequest $request): RedirectResponse
    {
        $members = Member::query()->orderBy('sort_order')->orderBy('id')->get();

        if ($members->isEmpty()) {
            return back()->withErrors(['members' => '請先到「成員管理」新增至少一位團員'])->withInput();
        }

        $menu = null;
        $shopName = $request->validated('shop_name');
        $imagePath = null;

        if ($request->filled('menu_id')) {
            $menu = Menu::query()->findOrFail($request->validated('menu_id'));
            $shopName = $shopName ?: $menu->shop_name;
            $imagePath = $menu->image_path;
        }

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('order/groups', 'public');
        }

        $group = DB::transaction(function () use ($request, $menu, $shopName, $imagePath, $members) {
            $group = Group::query()->create([
                'code' => Group::generateUniqueCode(),
                'menu_id' => $menu?->id,
                'shop_name' => $shopName,
                'image_path' => $imagePath,
                'status' => 'open',
                'round' => 1,
                'created_by' => auth('order')->id(),
            ]);

            foreach ($members as $index => $member) {
                GroupMember::query()->create([
                    'group_id' => $group->id,
                    'name' => $member->name,
                    'status' => 'unset',
                    'sort_order' => $index,
                ]);
            }

            return $group;
        });

        return redirect()
            ->route('order.groups.show', $group)
            ->with('success', '開團成功！把公開連結傳給大家吧');
    }

    public function show(Group $group): View
    {
        $group->load([
            'members',
            'histories' => fn ($q) => $q->latest('finalized_at')->limit(20),
        ]);

        return view('order.admin.groups-show', [
            'group' => $group,
            'publicUrl' => route('order.group.show', $group),
        ]);
    }
}
