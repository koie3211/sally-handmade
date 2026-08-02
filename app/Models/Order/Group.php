<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Group extends Model
{
    protected $table = 'order_groups';

    protected $fillable = [
        'code',
        'menu_id',
        'shop_name',
        'image_path',
        'status',
        'round',
        'finalized_at',
        'created_by',
    ];

    protected $appends = [
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'finalized_at' => 'datetime',
        ];
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (! $this->image_path) {
                return null;
            }

            return asset($this->image_path);
        });
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class)->orderBy('sort_order')->orderBy('id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(GroupHistory::class)->latest('finalized_at');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public static function generateUniqueCode(): string
    {
        do {
            $code = Str::lower(Str::random(8));
        } while (static::query()->where('code', $code)->exists());

        return $code;
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }
}
