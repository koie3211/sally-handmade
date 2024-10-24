<?php

namespace App\Models\AdminHub;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PermissionRole extends Pivot
{
    use HasFactory;

    protected $table = 'admin_hub_permission_role';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'action' => 'array',
        ];
    }
}
