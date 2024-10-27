<?php

namespace App\Http\Controllers\AdminHub\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminHub\V1\Admin\PermissionSortRequest;
use App\Http\Requests\AdminHub\V1\Admin\PermissionStoreRequest;
use App\Http\Requests\AdminHub\V1\Admin\PermissionUpdateRequest;
use App\Models\AdminHub\Permission;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Permission::orderBy('sort')->orderBy('id')->get(['id', 'name', 'action'])
                ->transform(fn ($permission) => [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'resource' => $permission->resource,
                    'can_create' => $permission->action['create'],
                    'can_read' => $permission->action['read'],
                    'can_update' => $permission->action['update'],
                    'can_delete' => $permission->action['delete'],
                ]),
        ]);
    }

    public function store(PermissionStoreRequest $request): JsonResponse
    {
        $input = $request->validated();

        $permission = Permission::create($input);

        return response()->json([
            'data' => [
                'id' => $permission->id,
                'name' => $permission->name,
                'resource' => $permission->resource,
                'can_create' => $permission->action['create'],
                'can_read' => $permission->action['read'],
                'can_update' => $permission->action['update'],
                'can_delete' => $permission->action['delete'],
            ],
        ], 201);
    }

    public function show(Permission $permission): JsonResponse
    {
        return response()->json([
            'data' => [
                'id' => $permission->id,
                'name' => $permission->name,
                'resource' => $permission->resource,
                'action' => $permission->action,
            ],
        ]);
    }

    public function update(PermissionUpdateRequest $request, Permission $permission): JsonResponse
    {
        $input = $request->validated();

        $permission->update($input);

        // TODO: 處理角色權限

        return response()->json([
            'data' => [
                'id' => $permission->id,
                'name' => $permission->name,
                'resource' => $permission->resource,
                'can_create' => $permission->action['create'],
                'can_read' => $permission->action['read'],
                'can_update' => $permission->action['update'],
                'can_delete' => $permission->action['delete'],
            ],
        ]);
    }

    public function destroy(Permission $permission): JsonResponse
    {
        // TODO: 處理角色權限

        $permission->delete();

        return response()->json(null, 204);
    }

    public function sort(PermissionSortRequest $request): JsonResponse
    {
        $input = $request->validated();

        foreach ($input['ids'] as $key => $id) {
            Permission::where('id', $id)->update([
                'sort' => $key,
            ]);
        }

        return response()->json(null, 204);
    }
}
