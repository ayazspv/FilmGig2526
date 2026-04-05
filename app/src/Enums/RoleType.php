<?php

namespace App\Enums;

enum RoleType: string
{
    case ADMIN = 'admin';
    case PRODUCTION_HOUSE = 'productionHouse';
    case FREELANCER = 'freelancer';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::PRODUCTION_HOUSE => 'Production Company',
            self::FREELANCER => 'Freelancer',
        };
    }

    public static function isValid(string $role): bool
    {
        return self::tryFrom($role) !== null;
    }
}