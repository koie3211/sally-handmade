<?php

namespace App\Models\AdminHub;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $table = 'admin_hub_banners';

    protected $fillable = [
        'page',
        'name',
        'image',
        'status',
    ];
}
