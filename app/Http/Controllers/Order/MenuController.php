<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreMenuRequest;
use App\Http\Requests\Order\UpdateMenuRequest;
use App\Models\Order\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        $menus = Menu::query()->latest()->get();

        return view('order.admin.menus', compact('menus'));
    }

    public function store(StoreMenuRequest $request): RedirectResponse
    {
        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('order/menus', 'order');
        }

        Menu::query()->create([
            'shop_name' => $request->validated('shop_name'),
            'image_path' => $path,
        ]);

        return back()->with('success', '已新增菜單');
    }

    public function update(UpdateMenuRequest $request, Menu $menu): RedirectResponse
    {
        $data = ['shop_name' => $request->validated('shop_name')];

        // Keep old image files so open groups that snapshot the same path still work.
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('order/menus', 'order');
        }

        $menu->update($data);

        return back()->with('success', '已更新菜單');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $menu->delete();

        return back()->with('success', '已刪除菜單');
    }
}
