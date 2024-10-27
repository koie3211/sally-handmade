<?php

namespace App\Http\Controllers\AdminHub\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminHub\V1\Admin\RoleSortRequest;
use App\Http\Requests\AdminHub\V1\Admin\RoleStoreRequest;
use App\Http\Requests\AdminHub\V1\Admin\RoleUpdateRequest;
use App\Models\AdminHub\Permission;
use App\Models\AdminHub\Role;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Role::orderBy('sort')->orderBy('id')->get(['id', 'name', 'updated_at'])
                ->transform(fn ($role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'updated_at' => $role->updated_at->toDateTimeString(),
                ]),
        ]);
    }

    public function create(): JsonResponse
    {
        return response()->json([
            'data' => [
                'actions' => Permission::orderBy('sort')->orderBy('id')->get(['id', 'name', 'action']),
            ],
        ]);
    }

    public function store(RoleStoreRequest $request): JsonResponse
    {
        $input = $request->safe();

        $role = Role::create($input->only('name'));

        foreach ($input->permissions as $permissionId => $actions) {
            $role->permissions()->attach($permissionId, ['action' => $actions]);
        }

        return response()->json([
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'updated_at' => $role->updated_at->toDateTimeString(),
            ],
        ], 201);
    }

    public function edit(Role $role): JsonResponse
    {
        return response()->json([
            'data' => [
                'actions' => Permission::orderBy('sort')->orderBy('id')->get(['id', 'name', 'action']),
            ],
        ]);
    }

    public function show(Role $role): JsonResponse
    {
        return response()->json([
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('pivot.action', 'id'),
            ],
        ]);
    }

    public function update(RoleUpdateRequest $request, Role $role): JsonResponse
    {
        $input = $request->safe();

        $role->update($input->only('name'));

        $role->permissions()->detach();

        foreach ($input->permissions as $permissionId => $actions) {
            $role->permissions()->attach($permissionId, ['action' => $actions]);
        }

        return response()->json([
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'updated_at' => $role->updated_at->toDateTimeString(),
            ],
        ]);
    }

    public function destroy(Role $role): JsonResponse
    {
        // TODO: 處理群組權限

        $role->permissions()->detach();

        $role->delete();

        return response()->json(null, 204);
    }

    public function sort(RoleSortRequest $request): JsonResponse
    {
        $input = $request->validated();

        foreach ($input['ids'] as $key => $id) {
            Role::where('id', $id)->update([
                'sort' => $key,
            ]);
        }

        return response()->json(null, 204);
    }
}
