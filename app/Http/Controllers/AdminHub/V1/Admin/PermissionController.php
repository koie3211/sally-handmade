<?php

namespace App\Http\Controllers\AdminHub\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminHub\V1\Admin\PermissionStoreRequest;
use App\Http\Requests\AdminHub\V1\Admin\PermissionUpdateRequest;
use App\Models\AdminHub\Permission;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Permission::query()
                ->orderBy('sort')->orderBy('id')
                ->get(['id', 'name']),
        ]);
    }

    public function store(PermissionStoreRequest $request): JsonResponse
    {
        $input = $request->validated();

        $permission = Permission::create($input);

        return response()->json([
            'id' => $permission->id,
            'name' => $permission->name,
        ], 201);
    }

    public function show(Permission $permission): JsonResponse
    {
        return response()->json([
            'id' => $permission->id,
            'name' => $permission->name,
            'resource' => $permission->resource,
            'action' => $permission->action,
        ]);
    }

    public function update(PermissionUpdateRequest $request, Permission $permission): JsonResponse
    {
        $input = $request->validated();

        $permission->update($input);

        // TODO: 處理角色權限

        return response()->json([
            'id' => $permission->id,
            'name' => $permission->name,
        ]);
    }

    public function destroy(Permission $permission): JsonResponse
    {
        // TODO: 處理角色權限

        $permission->delete();

        return response()->json(null, 204);
    }
}
