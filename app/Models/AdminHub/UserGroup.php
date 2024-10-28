<?php

namespace App\Models\AdminHub;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserGroup extends Model
{
    use HasFactory;

    protected $table = 'admin_hub_user_groups';

    protected $fillable = [
        'name',
        'level',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'admin_hub_role_user_group')->using(RoleUserGroup::class);
    }
}
