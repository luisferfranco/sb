<?php

namespace App\Enums;

enum EventType: string
{
    case GLOBAL = 'global';
    case LOCAL = 'local';

    public function label(): string
    {
        return match ($this) {
            self::GLOBAL => 'Global',
            self::LOCAL => 'Local',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::GLOBAL => 'success',
            self::LOCAL => 'warning',
        };
    }
}
