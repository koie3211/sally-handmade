<?php

namespace App\Enums\Registrar;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Transfer = 'transfer';

    public function label(): string
    {
        return match ($this) {
            self::Cash => '現金',
            self::Transfer => '匯款',
        };
    }
}
