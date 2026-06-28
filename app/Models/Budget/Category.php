<?php

namespace App\Models\Budget;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'budget_categories';

    protected $fillable = [
        'user_id',
        'name',
        'icon',
        'color',
        'type',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * 取得當前使用者可用分類（系統預設 + 自訂）
     */
    public static function forUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return static::where(fn ($q) => $q->whereNull('user_id')->orWhere('user_id', $userId))
            ->orderBy('sort_order')
            ->get();
    }
}
