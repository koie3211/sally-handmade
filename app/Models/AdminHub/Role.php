<?php

namespace App\Models\AdminHub;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $table = 'admin_hub_roles';

    protected $fillable = [
        'name',
        'sort',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class)->using(PermissionRole::class);
    }
}
