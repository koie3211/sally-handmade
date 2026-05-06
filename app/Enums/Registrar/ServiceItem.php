<?php

namespace App\Enums\Registrar;

enum ServiceItem: string
{
    case Incorporation = 'incorporation';
    case CapitalIncrease = 'capital_increase';
    case BusinessScope = 'business_scope';
    case ShareTransfer = 'share_transfer';
    case ResponsiblePerson = 'responsible_person';
    case CharterAmendment = 'charter_amendment';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Incorporation => '設立',
            self::CapitalIncrease => '增資',
            self::BusinessScope => '所營',
            self::ShareTransfer => '轉讓',
            self::ResponsiblePerson => '負責人',
            self::CharterAmendment => '修章',
            self::Other => '其他',
        };
    }
}
