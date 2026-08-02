<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\SaveGroupOrderRequest;
use App\Models\Order\Group;
use App\Models\Order\GroupHistory;
use App\Models\Order\GroupMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicGroupController extends Controller
{
    public function show(Group $group): View
    {
        $group->load(['members', 'histories']);

        return view('order.index', [
            'group' => $group,
            'members' => $group->members->map->toClientArray()->values()->all(),
            'histories' => $group->histories->map->toClientArray()->values()->all(),
            'saveUrl' => route('order.group.order.save', $group),
            'finalizeUrl' => route('order.group.finalize', $group),
            'restoreUrlTemplate' => route('order.group.restore', ['group' => $group, 'history' => '__ID__']),
            'stateUrl' => route('order.group.state', $group),
        ]);
    }

    public function state(Group $group): JsonResponse
    {
        $group->load(['members', 'histories']);

        return response()->json([
            'members' => $group->members->map->toClientArray()->values()->all(),
            'histories' => $group->histories->map->toClientArray()->values()->all(),
            'shopName' => $group->shop_name,
            'imageUrl' => $group->image_url,
            'round' => $group->round,
        ]);
    }

    public function saveOrder(SaveGroupOrderRequest $request, Group $group): JsonResponse
    {
        if (! $group->isOpen()) {
            return response()->json(['message' => '這一團已關閉，無法再改單'], 422);
        }

        $member = GroupMember::query()
            ->where('group_id', $group->id)
            ->whereKey($request->validated('member_id'))
            ->firstOrFail();

        if ($request->boolean('is_pass')) {
            $member->update([
                'status' => 'pass',
                'drink' => null,
                'sugar' => null,
                'ice' => null,
            ]);
        } elseif (filled($request->validated('drink'))) {
            $member->update([
                'status' => 'ordered',
                'drink' => $request->validated('drink'),
                'sugar' => $request->validated('sugar'),
                'ice' => $request->validated('ice'),
            ]);
        } else {
            $member->update([
                'status' => 'unset',
                'drink' => null,
                'sugar' => null,
                'ice' => null,
            ]);
        }

        $group->load('members');

        return response()->json([
            'message' => '已儲存',
            'member' => $member->fresh()->toClientArray(),
            'members' => $group->members->map->toClientArray()->values()->all(),
        ]);
    }

    public function finalize(Group $group): JsonResponse
    {
        if (! $group->isOpen()) {
            return response()->json(['message' => '這一團已關閉'], 422);
        }

        $ordered = $group->members()->where('status', 'ordered')->get();

        if ($ordered->isEmpty()) {
            return response()->json(['message' => '目前還沒有人選飲料，無法結單'], 422);
        }

        $history = DB::transaction(function () use ($group, $ordered) {
            $allMembers = $group->members()->orderBy('sort_order')->orderBy('id')->get();
            $groupsJson = $this->buildGroups($ordered);
            $snapshot = $allMembers->map->toClientArray()->values()->all();

            $history = GroupHistory::query()->create([
                'group_id' => $group->id,
                'shop_name' => $group->shop_name,
                'total_cups' => $ordered->count(),
                'groups_json' => $groupsJson,
                'snapshot_json' => $snapshot,
                'finalized_at' => now(),
            ]);

            $group->members()->update([
                'status' => 'unset',
                'drink' => null,
                'sugar' => null,
                'ice' => null,
            ]);

            $group->update([
                'round' => $group->round + 1,
                'finalized_at' => now(),
            ]);

            return $history;
        });

        $group->load(['members', 'histories']);

        return response()->json([
            'message' => '已結單，這次的明細已經存起來了',
            'history' => $history->toClientArray(),
            'members' => $group->members->map->toClientArray()->values()->all(),
            'histories' => $group->histories->map->toClientArray()->values()->all(),
            'round' => $group->fresh()->round,
        ]);
    }

    public function restore(Group $group, GroupHistory $history): JsonResponse
    {
        if ($history->group_id !== $group->id) {
            abort(404);
        }

        if (! $group->isOpen()) {
            return response()->json(['message' => '這一團已關閉，無法恢復'], 422);
        }

        DB::transaction(function () use ($group, $history) {
            $snapshotByName = collect($history->snapshot_json)->keyBy('name');

            foreach ($group->members as $member) {
                $snap = $snapshotByName->get($member->name);
                if (! $snap) {
                    continue;
                }

                $member->update([
                    'status' => $snap['status'] ?? 'unset',
                    'drink' => $snap['drink'] ?? null,
                    'sugar' => $snap['sugar'] ?? null,
                    'ice' => $snap['ice'] ?? null,
                ]);
            }

            $history->delete();
        });

        $group->load(['members', 'histories']);

        return response()->json([
            'message' => '已恢復這筆訂單，可以繼續修改',
            'members' => $group->members->map->toClientArray()->values()->all(),
            'histories' => $group->histories->map->toClientArray()->values()->all(),
        ]);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, GroupMember>  $orderedList
     * @return list<array{label: string, count: int, names: list<string>}>
     */
    private function buildGroups($orderedList): array
    {
        $groups = [];

        foreach ($orderedList as $person) {
            $key = implode('・', [
                $person->drink,
                $person->sugar ?: '（甜度未填）',
                $person->ice ?: '（冰塊未填）',
            ]);

            if (! isset($groups[$key])) {
                $groups[$key] = ['label' => $key, 'count' => 0, 'names' => []];
            }

            $groups[$key]['count']++;
            $groups[$key]['names'][] = $person->name;
        }

        $list = array_values($groups);
        usort($list, fn ($a, $b) => $b['count'] <=> $a['count']);

        return $list;
    }
}
