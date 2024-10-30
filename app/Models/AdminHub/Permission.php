<?php

namespace App\Models\AdminHub;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $table = 'admin_hub_permissions';

    protected $fillable = [
        'name',
        'resource',
        'sort',
        'action',
    ];

    protected function casts(): array
    {
        return [
            'action' => 'array',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'admin_hub_permission_role')->using(PermissionRole::class)->withPivot('action');
    }
}
