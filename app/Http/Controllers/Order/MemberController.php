<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreMemberRequest;
use App\Models\Order\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(): View
    {
        $members = Member::query()->orderBy('sort_order')->orderBy('id')->get();

        return view('order.admin.members', compact('members'));
    }

    public function store(StoreMemberRequest $request): RedirectResponse
    {
        $maxSort = (int) Member::query()->max('sort_order');

        Member::query()->create([
            'name' => $request->validated('name'),
            'sort_order' => $maxSort + 1,
        ]);

        return back()->with('success', '已新增團員');
    }

    public function destroy(Member $member): RedirectResponse
    {
        $member->delete();

        return back()->with('success', '已移除團員');
    }
}
