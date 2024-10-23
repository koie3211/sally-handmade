<?php

namespace App\Models\AdminHub;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
            'password' => 'array',
        ];
    }
}
