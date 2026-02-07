<?php

namespace App\Enums;

enum GroupStatus: string
{
    case ACTIVE   = 'active';
    case INACTIVE = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE    => 'Activo',
            self::INACTIVE  => 'Inactivo',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE    => 'success',
            self::INACTIVE  => 'warning',
        };
    }
}
