<?php

namespace App\Enums;

enum GroupMemberStatus: string
{
    case APPROVED = 'approved';
    case PENDING  = 'pending';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::APPROVED  => 'Aprobado',
            self::PENDING   => 'Pendiente',
            self::REJECTED  => 'Rechazado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::APPROVED  => 'success',
            self::PENDING   => 'warning',
            self::REJECTED  => 'error',
        };
    }
}
