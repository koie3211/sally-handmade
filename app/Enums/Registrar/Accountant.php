<?php

namespace App\Enums\Registrar;

enum Accountant: string
{
    case Ding = 'ding';
    case Chen = 'chen';
    case Mu = 'mu';

    public function label(): string
    {
        return match ($this) {
            self::Ding => '丁會',
            self::Chen => '陳會',
            self::Mu => '木會',
        };
    }
}
