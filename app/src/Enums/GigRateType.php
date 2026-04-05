<?php

namespace App\Enums;

enum GigRateType: string
{
    case HOURLY = 'hourly';
    case FIXED = 'fixed';

    public function label(): string
    {
        return match ($this) {
            self::HOURLY => 'Hourly',
            self::FIXED => 'Fixed',
        };
    }

    public function suffix(): string
    {
        return match ($this) {
            self::HOURLY => 'hr',
            self::FIXED => 'project',
        };
    }

    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    public static function isValid(string $rateType): bool
    {
        return self::tryFrom($rateType) !== null;
    }
}