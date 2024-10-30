<?php

namespace App\Models\AdminHub;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordLog extends Model
{
    use HasFactory;

    protected $table = 'admin_hub_password_logs';

    protected $fillable = [
        'user_id',
        'password',
    ];

    public const UPDATED_AT = null;
}
