<?php

namespace App\Models\AdminHub;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $table = 'admin_hub_roles';

    protected $touches = ['permissions'];

    protected $fillable = [
        'name',
        'sort',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'admin_hub_permission_role')->using(PermissionRole::class)->withPivot('action');
    }

    public function userGroups(): BelongsToMany
    {
        return $this->belongsToMany(UserGroup::class, 'admin_hub_role_user_group')->using(RoleUserGroup::class);
    }
}
