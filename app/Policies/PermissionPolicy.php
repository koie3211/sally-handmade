<?php

namespace App\Policies;

use App\Models\AdminHub\Permission;
use App\Models\AdminHub\User;

class PermissionPolicy
{
    public function __construct(
        private string $subject = 'permissions'
    ) {
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        $user->load('userGroup.roles.permissions');

        return $user->userGroup->roles
            ->map(fn ($role) => $role->permissions
                ->where('resource', $this->subject)->pluck('pivot.action', 'resource')
                ->every(fn ($actions) => isset($actions['read']) && $actions['read']))
            ->contains(true);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Permission $permission): bool
    {
        $user->load('userGroup.roles.permissions');

        return $user->userGroup->roles
            ->map(fn ($role) => $role->permissions
                ->where('resource', $this->subject)->pluck('pivot.action', 'resource')
                ->every(fn ($actions) => isset($actions['read']) && $actions['read']))
            ->contains(true);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        $user->load('userGroup.roles.permissions');

        return $user->userGroup->roles
            ->map(fn ($role) => $role->permissions
                ->where('resource', $this->subject)->pluck('pivot.action', 'resource')
                ->every(fn ($actions) => isset($actions['create']) && $actions['create']))
            ->contains(true);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Permission $permission): bool
    {
        $user->load('userGroup.roles.permissions');

        return $user->userGroup->roles
            ->map(fn ($role) => $role->permissions
                ->where('resource', $this->subject)->pluck('pivot.action', 'resource')
                ->every(fn ($actions) => isset($actions['update']) && $actions['update']))
            ->contains(true);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Permission $permission): bool
    {
        $user->load('userGroup.roles.permissions');

        return $user->userGroup->roles
            ->map(fn ($role) => $role->permissions
                ->where('resource', $this->subject)->pluck('pivot.action', 'resource')
                ->every(fn ($actions) => isset($actions['delete']) && $actions['delete']))
            ->contains(true);
    }
}
