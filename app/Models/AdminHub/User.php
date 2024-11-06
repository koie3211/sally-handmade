<?php

namespace App\Models\AdminHub;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasUlids;

    protected $table = 'admin_hub_users';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_group_id',
        'name',
        'avatar',
        'email',
        'account',
        'password',
        'status',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'status' => 'boolean',
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    public function userGroup(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class);
    }

    public function passwordLogs(): HasMany
    {
        return $this->hasMany(PasswordLog::class);
    }

    public function permissions(): array
    {
        $rolePermissions = $this->userGroup->roles
            ->map(fn ($role) => $role->permissions->pluck('pivot.action', 'resource'));

        $permissions = [];

        foreach ($rolePermissions as $permission) {
            foreach ($permission as $resource => $actions) {
                if (!isset($permissions[$resource])) {
                    $permissions[$resource] = $actions;
                } else {
                    foreach ($actions as $action => $value) {
                        if (!isset($permissions[$resource][$action])) {
                            $permissions[$resource][$action] = $value;
                        } else {
                            $permissions[$resource][$action] = $permissions[$resource][$action] || $value;
                        }
                    }
                }
            }
        }

        return $permissions;
    }
}
