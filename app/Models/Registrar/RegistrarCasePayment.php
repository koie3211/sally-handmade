<?php

namespace App\Models\Registrar;

use App\Enums\Registrar\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrarCasePayment extends Model
{
    protected $fillable = [
        'registrar_case_id',
        'deposit_amount',
        'deposit_received_at',
        'deposit_payment_method',
        'balance_amount',
        'balance_received_at',
        'balance_payment_method',
    ];

    protected function casts(): array
    {
        return [
            'deposit_received_at' => 'date',
            'deposit_payment_method' => PaymentMethod::class,
            'balance_received_at' => 'date',
            'balance_payment_method' => PaymentMethod::class,
        ];
    }

    public function registrarCase(): BelongsTo
    {
        return $this->belongsTo(RegistrarCase::class);
    }

    public function isComplete(): bool
    {
        return filled($this->deposit_amount)
            && filled($this->deposit_received_at)
            && filled($this->deposit_payment_method)
            && filled($this->balance_amount)
            && filled($this->balance_received_at)
            && filled($this->balance_payment_method);
    }
}
