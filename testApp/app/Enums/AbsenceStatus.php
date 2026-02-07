<?php

namespace App\Enums;

enum AbsenceStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';

    public function label(): string {
        return match($this) {
            self::PENDING => 'Čeká na schválení',
            self::APPROVED => 'Schváleno',
            self::REJECTED => 'Zamítnuto',
            self::CANCELLED => 'Zrušeno',
        };
    }
}
