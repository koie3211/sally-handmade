<?php

namespace App\Models\Budget;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $table = 'budget_appointments';

    protected $fillable = [
        'user_id',
        'title',
        'note',
        'start_at',
        'end_at',
        'remind_minutes',
    ];

    protected function casts(): array
    {
        return [
            'start_at'     => 'datetime',
            'end_at'       => 'datetime',
            'reminded_at'  => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
