<?php

namespace App\Http\Controllers\AdminHub\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminHub\V1\Admin\PermissionSortRequest;
use App\Http\Requests\AdminHub\V1\Admin\PermissionStoreRequest;
use App\Http\Requests\AdminHub\V1\Admin\PermissionUpdateRequest;
use App\Models\AdminHub\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class PermissionController extends Controller
{
    public function index(): JsonResponse
    {
        Gate::authorize('viewAny', Permission::class);

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
        Gate::authorize('create', Permission::class);

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
        Gate::authorize('view', $permission);

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
        Gate::authorize('update', $permission);

        $input = $request->validated();

        $permission->update($input);

        foreach ($input['action'] as $key => $value) {
            if ($value) {
                continue;
            }

            $permission->roles->each(function ($role) use ($key, $permission) {
                $roleAction = $role->pivot->action;
                unset($roleAction[$key]);

                $permission->roles()->updateExistingPivot($role->id, [
                    'action' => $roleAction,
                ]);
            });
        }

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
        Gate::authorize('delete', $permission);

        $permission->roles()->detach();

        $permission->delete();

        return response()->json(null, 204);
    }

    public function sort(PermissionSortRequest $request): JsonResponse
    {
        Gate::authorize('update', Permission::class);

        $input = $request->validated();

        foreach ($input['ids'] as $key => $id) {
            Permission::where('id', $id)->update([
                'sort' => $key,
            ]);
        }

        return response()->json(null, 204);
    }
}
