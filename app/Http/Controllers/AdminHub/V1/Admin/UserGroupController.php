<?php

namespace App\Http\Controllers\AdminHub\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminHub\V1\Admin\UserGroupStoreRequest;
use App\Http\Requests\AdminHub\V1\Admin\UserGroupUpdateRequest;
use App\Models\AdminHub\Role;
use App\Models\AdminHub\UserGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserGroupController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $currUserGroup = $request->user()->userGroup;

        $length = $request->integer('length', 35);

        $keyword = $request->query('keyword');

        $rows = UserGroup::query()
            ->where(fn ($query) => $query->where('id', $currUserGroup->id)
                ->orWhere('level', '>', $currUserGroup->level))
            ->when($keyword, fn ($query) => $query->where('name', 'like', "%{$keyword}%"))
            ->orderByRaw("`id`='{$currUserGroup->id}' DESC")
            ->orderBy('sort')->orderBy('id')
            ->paginate($length);

        // TODO: 增加判斷參數
        return response()->json([
            'data' => [
                'data' => $rows->map(fn ($row) => [
                    'id' => $row->id,
                    'name' => $row->name,
                    'level' => $row->level,
                    'sort' => $row->sort,
                    'status' => $row->status,
                    'updated_at' => $row->updated_at->toDateTimeString(),
                ]),
                'count' => $rows->total(),
            ],
        ]);
    }

    public function create(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'roles' => Role::orderBy('sort')->orderBy('id')->get(['id as value', 'name as label']),
                'level' => $user->userGroup->level + 1,
            ],
        ]);
    }

    public function store(UserGroupStoreRequest $request): JsonResponse
    {
        $input = $request->safe();

        $user = $request->user();

        abort_if($input->level <= $user->userGroup->level, 400, '等級設定錯誤，請重新設定');

        $userGroup = UserGroup::create($input->except('roles'));

        $userGroup->roles()->attach($input->roles);

        return response()->json([
            'data' => [
                'id' => $userGroup->id,
                'name' => $userGroup->name,
                'level' => $userGroup->level,
                'sort' => $userGroup->sort,
                'status' => $userGroup->status,
                'updated_at' => $userGroup->updated_at->toDateTimeString(),
            ],
        ], 201);
    }

    public function edit(Request $request, UserGroup $userGroup): JsonResponse
    {
        $currUserGroup = $request->user()->userGroup;

        abort_if($userGroup->level <= $currUserGroup->level && $userGroup->id !== $currUserGroup->id, 403, '權限不足');

        return response()->json([
            'data' => [
                'roles' => Role::orderBy('sort')->orderBy('id')->get(['id as value', 'name as label']),
                'level' => $currUserGroup->level + 1,
            ],
        ]);
    }

    public function show(Request $request, UserGroup $userGroup): JsonResponse
    {
        $currUserGroup = $request->user()->userGroup;

        abort_if($userGroup->level <= $currUserGroup->level && $userGroup->id !== $currUserGroup->id, 403, '權限不足');

        return response()->json([
            'data' => [
                'id' => $userGroup->id,
                'name' => $userGroup->name,
                'level' => $userGroup->level,
                'sort' => $userGroup->sort,
                'status' => $userGroup->status,
                'roles' => $userGroup->roles->pluck('id'),
            ],
        ]);
    }

    public function update(UserGroupUpdateRequest $request, UserGroup $userGroup): JsonResponse
    {
        $currUserGroup = $request->user()->userGroup;

        abort_if($userGroup->level <= $currUserGroup->level && $userGroup->id !== $currUserGroup->id, 403, '權限不足');

        $input = $request->safe();

        $userGroup->update($input->except($userGroup->id === $currUserGroup->id ? ['roles', 'status'] : 'roles'));

        $userGroup->roles()->sync($input->roles);

        return response()->json([
            'data' => [
                'id' => $userGroup->id,
                'name' => $userGroup->name,
                'level' => $userGroup->level,
                'sort' => $userGroup->sort,
                'status' => $userGroup->status,
                'updated_at' => $userGroup->updated_at->toDateTimeString(),
            ],
        ]);
    }

    public function destroy(Request $request, UserGroup $userGroup): JsonResponse
    {
        $currUserGroup = $request->user()->userGroup;

        abort_if($userGroup->level <= $currUserGroup->level && $userGroup->id !== $currUserGroup->id, 403, '權限不足');

        abort_if($userGroup->is_default, 400, '禁止刪除預設群組');

        abort_if($userGroup->id === $currUserGroup->id, 400, '禁止刪除自己的群組');

        $userGroup->roles()->detach();

        $userGroup->users()->delete();

        $userGroup->delete();

        return response()->json(null, 204);
    }
}
