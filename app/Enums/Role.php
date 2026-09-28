<?php

namespace App\Enums;

enum Role
{
    case ADMIN;
    case STAFF;
    case SEKRETARIS;

    public function status(): string
    {
        return match ($this) {
            self::ADMIN => 'admin',
            self::STAFF => 'staff',
            self::SEKRETARIS => 'sekretaris',
        };
    }
}
