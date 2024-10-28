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
        $length = $request->integer('length', 35);

        $keyword = $request->query('keyword');

        $rows = UserGroup::query()
            ->when($keyword, fn ($query) => $query->where('name', 'like', "%{$keyword}%"))
            ->paginate($length);

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

    public function create(): JsonResponse
    {
        return response()->json([
            'data' => [
                'roles' => Role::orderBy('sort')->orderBy('id')->get(['id as value', 'name as label']),
                'level' => 1, // TODO: 待改
            ],
        ]);
    }

    public function store(UserGroupStoreRequest $request): JsonResponse
    {
        $input = $request->safe();

        $userGroup = UserGroup::create($input->except('roles'));

        $userGroup->roles()->attach($input->roles);

        return response()->json([
            'data' => [
                'id' => $userGroup->id,
                'name' => $userGroup->name,
                'level' => $userGroup->level,
                'status' => $userGroup->status,
                'updated_at' => $userGroup->updated_at->toDateTimeString(),
            ],
        ], 201);
    }

    public function edit(UserGroup $userGroup): JsonResponse
    {
        return response()->json([
            'data' => [
                'roles' => Role::orderBy('sort')->orderBy('id')->get(['id as value', 'name as label']),
                'level' => 1, // TODO: 待改
            ],
        ]);
    }

    public function show(UserGroup $userGroup): JsonResponse
    {
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
        $input = $request->safe();

        $userGroup->update($input->except('roles'));

        $userGroup->roles()->sync($input->roles);

        return response()->json([
            'data' => [
                'id' => $userGroup->id,
                'name' => $userGroup->name,
                'level' => $userGroup->level,
                'status' => $userGroup->status,
                'updated_at' => $userGroup->updated_at->toDateTimeString(),
            ],
        ]);
    }

    public function destroy(UserGroup $userGroup): JsonResponse
    {
        // TODO: 處理帳戶

        $userGroup->roles()->detach();

        $userGroup->delete();

        return response()->json(null, 204);
    }
}
