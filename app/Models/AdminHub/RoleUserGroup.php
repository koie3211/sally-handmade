<?php

namespace App\Models\AdminHub;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RoleUserGroup extends Pivot
{
    use HasFactory;

    protected $table = 'admin_hub_role_user_group';

    public $timestamps = false;
}
