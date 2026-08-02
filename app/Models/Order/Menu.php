<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $table = 'order_menus';

    protected $fillable = [
        'shop_name',
        'image_path',
    ];

    protected $appends = [
        'image_url',
    ];

    protected function imageUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (! $this->image_path) {
                return null;
            }

            return asset($this->image_path);
        });
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }
}
