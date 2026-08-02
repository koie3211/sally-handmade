<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupHistory extends Model
{
    protected $table = 'order_group_histories';

    protected $fillable = [
        'group_id',
        'shop_name',
        'total_cups',
        'groups_json',
        'snapshot_json',
        'finalized_at',
    ];

    protected function casts(): array
    {
        return [
            'groups_json' => 'array',
            'snapshot_json' => 'array',
            'finalized_at' => 'datetime',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function toClientArray(): array
    {
        return [
            'id' => (string) $this->id,
            'dateStr' => $this->finalized_at->format('n/j'),
            'timeStr' => $this->finalized_at->format('H:i'),
            'shopName' => $this->shop_name,
            'total' => $this->total_cups,
            'groups' => $this->groups_json,
            'snapshot' => $this->snapshot_json,
        ];
    }
}
