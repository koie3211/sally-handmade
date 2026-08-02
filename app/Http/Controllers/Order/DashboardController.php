<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Order\Group;
use App\Models\Order\Member;
use App\Models\Order\Menu;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $groups = Group::query()
            ->withCount([
                'members',
                'members as ordered_count' => fn ($q) => $q->where('status', 'ordered'),
            ])
            ->latest()
            ->get();

        return view('order.admin.dashboard', [
            'groups' => $groups,
            'memberCount' => Member::query()->count(),
            'menuCount' => Menu::query()->count(),
        ]);
    }
}
