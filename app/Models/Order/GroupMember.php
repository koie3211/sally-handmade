<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupMember extends Model
{
    protected $table = 'order_group_members';

    protected $fillable = [
        'group_id',
        'name',
        'status',
        'drink',
        'sugar',
        'ice',
        'sort_order',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function toClientArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'drink' => $this->drink,
            'sugar' => $this->sugar,
            'ice' => $this->ice,
        ];
    }
}
