<?php

namespace App\Models\Budget;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $table = 'budget_transactions';

    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'type',
        'note',
        'transaction_date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * 格式化金額顯示（含正負號）
     */
    public function getFormattedAmountAttribute(): string
    {
        $prefix = $this->type === 'expense' ? '-' : '+';
        return $prefix . number_format($this->amount);
    }
}
