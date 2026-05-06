<?php

namespace App\Enums\Registrar;

enum CaseStatus: string
{
    case Paused = 'paused';
    case InProgress = 'in_progress';
    case Cancelled = 'cancelled';
    case AwaitingPayment = 'awaiting_payment';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Paused => '暫緩',
            self::InProgress => '辦理中',
            self::Cancelled => '取消辦理',
            self::AwaitingPayment => '待收款',
            self::Closed => '結案',
        };
    }
}
