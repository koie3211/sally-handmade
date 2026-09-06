<?php

namespace App\Models\Budget;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'budget_users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'default_transaction_type',
        'default_category_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $attributes = [
        'default_transaction_type' => 'expense',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'default_category_id' => 'integer',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function defaultCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'default_category_id');
    }

    /**
     * 安全後的記帳表單預設：分類必須可用且類型一致，否則退回該類型第一筆。
     *
     * @return array{type: string, category_id: int|null}
     */
    public function defaultBookkeeping(): array
    {
        $type = in_array($this->default_transaction_type, ['expense', 'income'], true)
            ? $this->default_transaction_type
            : 'expense';

        $ofType = Category::forUser($this->id)->where('type', $type);

        $preferred = $this->default_category_id
            ? $ofType->firstWhere('id', $this->default_category_id)
            : null;

        return [
            'type' => $type,
            'category_id' => $preferred?->id ?? $ofType->first()?->id,
        ];
    }
}
