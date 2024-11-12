<?php

namespace App\Http\Controllers\AdminHub\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminHub\V1\Admin\RoleSortRequest;
use App\Http\Requests\AdminHub\V1\Admin\RoleStoreRequest;
use App\Http\Requests\AdminHub\V1\Admin\RoleUpdateRequest;
use App\Models\AdminHub\Permission;
use App\Models\AdminHub\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        Gate::authorize('viewAny', Role::class);

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
        Gate::authorize('create', Role::class);

        return response()->json([
            'data' => [
                'actions' => Permission::orderBy('sort')->orderBy('id')->get(['id', 'name', 'action']),
            ],
        ]);
    }

    public function store(RoleStoreRequest $request): JsonResponse
    {
        Gate::authorize('create', Role::class);

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
        Gate::authorize('update', $role);

        return response()->json([
            'data' => [
                'actions' => Permission::orderBy('sort')->orderBy('id')->get(['id', 'name', 'action']),
            ],
        ]);
    }

    public function show(Role $role): JsonResponse
    {
        Gate::authorize('view', $role);

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
        Gate::authorize('update', $role);

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
        Gate::authorize('delete', $role);

        $role->permissions()->detach();

        $role->userGroups()->detach();

        $role->delete();

        return response()->json(null, 204);
    }

    public function sort(RoleSortRequest $request): JsonResponse
    {
        Gate::authorize('update', Role::class);

        $input = $request->validated();

        foreach ($input['ids'] as $key => $id) {
            Role::where('id', $id)->update([
                'sort' => $key,
            ]);
        }

        return response()->json(null, 204);
    }
}
