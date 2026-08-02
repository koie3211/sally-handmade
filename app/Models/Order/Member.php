<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'order_members';

    protected $fillable = [
        'name',
        'sort_order',
    ];
}
